<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\IaaStepAssignment;
use App\Models\MusteriSikayetiLog;
use App\Notifications\ProjeDurumuDegisti;
use App\Notifications\ProjeStakeholderBilgilendirme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;

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
                'onaya_gonderilme_tarihi' => now(), // Yeni tarih mantığı
                'tamamlanma_tarihi' => now(),
                'durum_degistirme_tarihi' => now(),
                'tamamlayan_lider_id' => optional($iaa->atananTakim)->lider_user_id, // Lideri sabitle
            ]);

            // === YENİ: TARİHSEL PUAN BÜTÜNLÜĞÜ (Takım Üyelerini Dondur) ===
            if ($iaa->atananTakim) {
                $uyeler = $iaa->atananTakim->uyeler()->pluck('user_id')->toArray();
                if (!empty($uyeler)) {
                    $syncData = [];
                    foreach ($uyeler as $userId) {
                        $syncData[$userId] = [
                            'rol' => 'Takım Üyesi',
                            'kazanilan_puan' => $iaa->puan ?? 0,
                            'durum' => 'onaylandi'
                        ];
                    }
                    $iaa->projeEkibi()->syncWithoutDetaching($syncData);
                }
            }
            // =========================================================


            // 3. Log
            IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Bölüm Onayına Gönderildi (İadeli)',
                'aciklama' => Auth::user()->name . " projeyi tamamladı ve iade bilgileriyle onaya sundu."
            ]);

            // Takvim Ziyaretini Kilitle
            $this->lockVisitInTakvim($iaa->id, true);

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

        $isComplaint = $iaa->musteriSikayeti !== null;

        $yeniDurum = $isComplaint ? 'Bölüm Onayı Bekliyor' : 'Yönetici Onayı Bekliyor';
        $logEylemi = $isComplaint ? 'Bölüm Onayına Gönderildi' : 'Yönetici Onayına Gönderildi';
        $notifyRole = $isComplaint ? 'Bölüm Kalite Yöneticisi' : 'Superadmin';

        $bildirimMesaji = $isComplaint
            ? Auth::user()->name . " projeyi tamamladı."
            : $iaa->baslik . " başlıklı İAA projesi " . Auth::user()->name . " tarafından onayınıza sunulmuştur.";

        $iaa->update([
            'durum' => $yeniDurum,
            'basvuran_notu' => 'İadesiz kapanış yapıldı.',
            'onaya_gonderilme_tarihi' => now(), // Yeni tarih mantığı
            'tamamlanma_tarihi' => now(),
            'durum_degistirme_tarihi' => now(),
            'tamamlayan_lider_id' => optional($iaa->atananTakim)->lider_user_id, // Lideri sabitle
        ]);

        // === YENİ: TARİHSEL PUAN BÜTÜNLÜĞÜ (Takım Üyelerini Dondur) ===
        if ($iaa->atananTakim) {
            $uyeler = $iaa->atananTakim->uyeler()->pluck('user_id')->toArray();
            if (!empty($uyeler)) {
                $syncData = [];
                foreach ($uyeler as $userId) {
                    $syncData[$userId] = [
                        'rol' => 'Takım Üyesi',
                        'kazanilan_puan' => $iaa->puan ?? 0,
                        'durum' => 'onaylandi'
                    ];
                }
                $iaa->projeEkibi()->syncWithoutDetaching($syncData);
            }
        }
        // =========================================================


        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => $logEylemi,
            'aciklama' => Auth::user()->name . " projeyi tamamladı ve $notifyRole onayına sundu."
        ]);

        // Takvim Ziyaretini Kilitle
        $this->lockVisitInTakvim($iaa->id, true);

        if ($isComplaint) {
            MusteriSikayetiLog::create([
                'musteri_sikayeti_id' => $iaa->musteriSikayeti->id,
                'user_id' => Auth::id(),
                'eylem' => 'Çözüm Önerisi Sunuldu',
                'aciklama' => 'Proje tamamlandı, onay süreci başlatıldı.'
            ]);
        }

        $this->notifyNextApprover($iaa, $notifyRole, $bildirimMesaji);
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
            'tamamlanma_tarihi' => null,
            'tamamlayan_lider_id' => null, // Onay geri çekildiğinde tamamlayan bilgisini temizle
            'durum_degistirme_tarihi' => now(),
        ]);


        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Başvuru Geri Çekildi',
            'aciklama' => Auth::user()->name . " onaya gönderilen projeyi geri çekti."
        ]);

        // Takvim Ziyaret kilidini aç
        $this->lockVisitInTakvim($iaa->id, false);

        // Bildirimleri temizle (Database Notification)
        // İlgili IAA'ya ait okunmamış bildirimleri bul ve sil
        // Bu logic biraz karmaşık olabilir, basitçe geçiyorum şimdilik
    }

    private function lockVisitInTakvim($iaaId, $lock = true)
    {
        try {
            // Takvim API URL'si .env'den veya config'den alınmalı
            $baseUrl = rtrim(config('services.takvim.url', 'http://localhost:8001'), '/');
            
            Http::withHeaders([
                'Accept' => 'application/json',
                'X-App-Internal' => 'iaa'
            ])->post($baseUrl . '/api/visit/toggle-lock', [
                'remote_id' => $iaaId,
                'lock' => $lock
            ]);
        } catch (\Exception $e) {
            \Log::error("Takvim visit lock toggle failed for IAA $iaaId: " . $e->getMessage());
        }
    }

    private function notifyNextApprover(Iaa $iaa, $roleName, $customMessage = null)
    {
        $tamamlayanKisi = Auth::user()->name;

        if ($roleName === 'Bölüm Kalite Yöneticisi') {
            // Bölüm bilgisini belirle (Proje veya Şikayet üzerinden)
            $bolum = $iaa->bolum ?? ($iaa->musteriSikayeti ? $iaa->musteriSikayeti->bolum : null);
            $bolumId = $bolum ? $bolum->id : null;
            $bolumAdi = $bolum ? $bolum->ad : 'Belirsiz';

            // Kalite Yöneticilerini Bul (Onay verecek kişiler)
            $kaliteYoneticileri = collect();
            if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayet_kategorisi_id) {
                $kategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                    ->where(function ($q) use ($bolumId, $kategoriId) {
                        $q->where('bolum_id', $bolumId)
                            ->orWhereHas('yonettigiSikayetKategorileri', function ($sq) use ($kategoriId) {
                                $sq->where('sikayet_kategori_id', $kategoriId);
                            });
                    })->get();
            } else if ($bolumId) {
                $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')->where('bolum_id', $bolumId)->get();
            }

            $kyIsimleri = $kaliteYoneticileri->pluck('name')->implode(', ');
            if (empty($kyIsimleri)) {
                $kyIsimleri = 'Bölüm Kalite Yöneticisi';
            }

            // 1. Kalite Yöneticisine (Onay Bekliyor Bildirimi)
            $message = $customMessage ?? ($tamamlayanKisi . " projeyi tamamladı.");
            if ($kaliteYoneticileri->isNotEmpty()) {
                Notification::send($kaliteYoneticileri, new ProjeDurumuDegisti(
                    $iaa,
                    'ONAYINIZI BEKLİYOR.',
                    $message
                ));
            }

            // 2. Direktöre (Şenol Kanat vb.)
            if ($bolum && $bolum->director_id) {
                $direktor = \App\Models\User::find($bolum->director_id);
                if ($direktor) {
                    $dirMessage = "Direktörlüğünüze bağlı {$bolumAdi} bölümüne ait {$iaa->baslik} başlıklı şikayet {$tamamlayanKisi} tarafından {$kyIsimleri}'ın onayına gönderilmiştir";
                    $direktor->notify(new ProjeStakeholderBilgilendirme($iaa, $dirMessage, 'info'));
                }
            }

            // 3. Bölüm Liderine (Emrah Al vb.)
            if ($bolumId) {
                $bolumLiderleri = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $bolumId)->get();
                if ($bolumLiderleri->isNotEmpty()) {
                    $blMessage = "Bölümünüze bağlı {$iaa->baslik} başlıklı şikayet {$tamamlayanKisi} tarafından {$kyIsimleri}'ın onayına gönderilmiştir";
                    Notification::send($bolumLiderleri, new ProjeStakeholderBilgilendirme($iaa, $blMessage, 'info'));
                }
            }

            // 4. Müşteri Temsilcilerine
            if ($iaa->musteriSikayeti) {
                $sikayet = $iaa->musteriSikayeti;
                $temsilciler = collect();

                if ($sikayet->yetkili_user_id) {
                    $temsilciler->push($sikayet->yetkili_user);
                }
                $temsilciler = $temsilciler->merge($sikayet->ekYetkililer);

                if ($sikayet->customer) {
                    $firmaTemsilcileri = $sikayet->customer->users()->role('Müşteri Temsilcisi')->get();
                    $temsilciler = $temsilciler->merge($firmaTemsilcileri);
                }

                $finalTemsilciler = $temsilciler->unique('id')->reject(fn($u) => $u->id == Auth::id());

                if ($finalTemsilciler->isNotEmpty()) {
                    $msMsg = "{$iaa->baslik} başlıklı Şikayetiniz {$bolumAdi} bölümü kalite yöneticisi onayına sunulmuştur.";
                    Notification::send($finalTemsilciler, new ProjeStakeholderBilgilendirme($iaa, $msMsg, 'info'));
                }
            }
        } else {
            // Diğer Roller İçin (Mevcut Mantık - Genelde Superadmin için)
            $query = \App\Models\User::role($roleName);
            $yoneticiler = $query->get();
            $message = $customMessage ?? ($tamamlayanKisi . " projeyi tamamladı.");

            if ($yoneticiler->isNotEmpty()) {
                Notification::send($yoneticiler, new ProjeDurumuDegisti(
                    $iaa,
                    'ONAYINIZI BEKLİYOR.',
                    $message
                ));
            }
        }
    }
}
