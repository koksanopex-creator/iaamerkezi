<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\IaaStepAssignment;
use App\Models\MusteriSikayetiLog;
use App\Notifications\ProjeDurumuDegisti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ProjeTamamlamaService
{
    /**
     * İadeli Tamamlama (SENARYO 1)
     */
    public function completeWithReturn(Request $request, $id)
    {
        $iaa = Iaa::with('musteriSikayeti')->findOrFail($id);

        $request->validate([
            'iade_tarihi' => 'required|date',
            'urun_turu' => 'required|string',
            'iade_sebebi' => 'required|string',
            'miktar' => 'required|numeric|min:0',
            'birim' => 'required|string',
            'toplam_parti_miktari' => 'nullable|numeric|min:0',
            'aciklama' => 'nullable|string',
            'dosya' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        DB::transaction(function () use ($request, $iaa) {
            // 1. İade Kaydını Oluştur veya Güncelle
            if ($iaa->musteriSikayeti) {

                $dosyaYolu = null;
                if ($request->hasFile('dosya')) {
                    $dosyaYolu = $request->file('dosya')->store('iade_dosyalari', 'public');
                }

                // Mevcut kaydı bul veya yeni oluştur
                $iade = \App\Models\SikayetIadesi::where('musteri_sikayeti_id', $iaa->musteriSikayeti->id)->first();

                if ($iade) {
                    $iade->update([
                        'user_id' => Auth::id(),
                        'iade_tarihi' => $request->iade_tarihi,
                        'urun_turu' => $request->urun_turu,
                        'iade_sebebi' => $request->iade_sebebi,
                        'miktar' => $request->miktar,
                        'birim' => $request->birim,
                        'toplam_parti_miktari' => $request->toplam_parti_miktari,
                        'aciklama' => $request->aciklama,
                        'dosya_yolu' => $dosyaYolu ?? $iade->dosya_yolu // Yeni dosya varsa güncelle, yoksa eskisi kalsın
                    ]);
                } else {
                    \App\Models\SikayetIadesi::create([
                        'musteri_sikayeti_id' => $iaa->musteriSikayeti->id,
                        'user_id' => Auth::id(),
                        'iade_tarihi' => $request->iade_tarihi,
                        'urun_turu' => $request->urun_turu,
                        'iade_sebebi' => $request->iade_sebebi,
                        'miktar' => $request->miktar,
                        'birim' => $request->birim,
                        'toplam_parti_miktari' => $request->toplam_parti_miktari,
                        'aciklama' => $request->aciklama,
                        'dosya_yolu' => $dosyaYolu
                    ]);
                }
            }

            // 2. Projeyi Güncelle
            $iaa->update([
                'durum' => 'Bölüm Onayı Bekliyor',
                'basvuran_notu' => $request->aciklama, // Açıklamayı nota da ekleyelim
                'tamamlanma_tarihi' => now(),
            ]);

            // 3. Log
            IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Bölüm Onayına Gönderildi (İadeli)',
                'aciklama' => Auth::user()->name . " projeyi tamamladı ve iade bilgileriyle onaya sundu."
            ]);

            // 4. Müşteri Şikayeti Log
            if ($iaa->musteriSikayeti) {
                MusteriSikayetiLog::create([
                    'musteri_sikayeti_id' => $iaa->musteriSikayeti->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Çözüm Önerisi Sunuldu',
                    'aciklama' => 'Proje tamamlandı, iadeli kapanış onayı başlatıldı.'
                ]);
            }
        });

        // Bildirim
        $this->notifyNextApprover($iaa, 'Bölüm Kalite Yöneticisi');
    }

    /**
     * İadesiz Tamamlama (SENARYO 2)
     */
    public function completeWithoutReturn(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);
        // Validasyon kaldırıldı (Form'da input yok)
        // $request->validate(['not' => 'required|string|min:10']); 

        $iaa->update([
            'durum' => 'Bölüm Onayı Bekliyor',
            'basvuran_notu' => 'İadesiz kapanış yapıldı.',
            'tamamlanma_tarihi' => now()
        ]);

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Bölüm Onayına Gönderildi',
            'aciklama' => Auth::user()->name . " projeyi iadesiz olarak tamamladı ve onaya sundu."
        ]);

        if ($iaa->musteriSikayeti) {
            MusteriSikayetiLog::create([
                'musteri_sikayeti_id' => $iaa->musteriSikayeti->id,
                'user_id' => Auth::id(),
                'eylem' => 'Çözüm Önerisi Sunuldu',
                'aciklama' => 'Proje tamamlandı, onay süreci başlatıldı.'
            ]);
        }

        $this->notifyNextApprover($iaa, 'Bölüm Kalite Yöneticisi');
    }

    /**
     * Başvuruyu Geri Çekme
     */
    public function recallSubmission(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if ($iaa->durum !== 'Bölüm Onayı Bekliyor') {
            abort(403, 'Sadece bölüm onayı bekleyen projeler geri çekilebilir.');
        }

        if ($iaa->atananTakim && $iaa->atananTakim->lider_user_id != Auth::id() && !Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Sadece proje lideri geri çekebilir.');
        }

        $iaa->update([
            'durum' => 'Atandı', // Veya 'Devam Ediyor'
            'tamamlanma_tarihi' => null
        ]);

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Başvuru Geri Çekildi',
            'aciklama' => Auth::user()->name . " onaya gönderilen projeyi geri çekti."
        ]);

        // Bildirimleri temizle (Database Notification)
        // İlgili IAA'ya ait okunmamış bildirimleri bul ve sil
        // Bu logic biraz karmaşık olabilir, basitçe geçiyorum şimdilik
    }

    private function notifyNextApprover(Iaa $iaa, $roleName)
    {
        // Bildirim mantığı - Bölüm Kalite Yöneticisi bul
        // User::role... vs
        // Notification::send...
        // Bu kısım çok tekrar ediyor, ayrı bir NotificationService veya Trait kullanılabilir.
        // Şimdilik basit tutuyoruz.

        $yoneticiler = \App\Models\User::role($roleName)->get();
        if ($yoneticiler->isNotEmpty()) {
            Notification::send($yoneticiler, new ProjeDurumuDegisti(
                $iaa,
                'ONAYINIZI BEKLİYOR.',
                Auth::user()->name . " projeyi tamamladı."
            ));
        }
    }
}
