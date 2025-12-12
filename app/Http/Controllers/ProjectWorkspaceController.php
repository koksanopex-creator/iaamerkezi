<?php

namespace App\Http\Controllers;

use App\Models\Iaa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\IaaProgressUpdate;
use App\Models\IaaWorkflowStep;
use App\Models\IaaLog;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjeAdimiGuncellendi;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SikayetTakipBilgilendirmeMail;
use App\Models\MusteriSikayetiLog;


class ProjectWorkspaceController extends Controller
{
    /**
     * Proje çalışma alanını gösterir.
     */
    public function show($id) // ID olarak alıyoruz, model binding yerine manuel çekeceğiz ki ilişkileri yönetelim
    {
        // İlişkileri yüklüyoruz (Squad için projeEkibi şart)
        $iaa = Iaa::with(['musteriSikayeti', 'atananTakim', 'projeEkibi', 'talepEdenTakimlar.lider', 'talepEdenTakimlar.uyeler'])->findOrFail($id);
        
        $isYetkiliKullanici = false;
        $isYetkiliMisafir = false;

        // 1. GİRİŞ YAPMIŞ KULLANICI KONTROLÜ (HİBRİT YAPI)
        if (Auth::check()) {
            $isYetkiliKullanici = $this->checkAuthorization($iaa);
        }

        // 2. MİSAFİR (MÜŞTERİ) KONTROLÜ (Token ve Session ile)
        $sikayet = $iaa->musteriSikayeti;
        
        if ($sikayet) {
            $sessionKey = 'sikayet_logged_in_' . $sikayet->takip_token;
            if (Session::has($sessionKey)) {
                $isYetkiliMisafir = true;
            }
        }

        // 3. NİHAİ GÜVENLİK DUVARI
        if (!$isYetkiliKullanici && !$isYetkiliMisafir) {
            
            // Eğer müşteri ise ve token varsa giriş sayfasına yönlendir
            if ($sikayet && $sikayet->takip_token) {
                return redirect()->route('public.sikayet.show', ['token' => $sikayet->takip_token])
                    ->with('error', 'Proje detaylarını görmek için lütfen şifrenizle giriş yapın.');
            }
            
            // Eğer kullanıcı giriş yapmamışsa login'e at
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            
            // Giriş yapmış ama yetkisi yoksa 403 ver
            abort(403, 'Bu projeye erişim yetkiniz bulunmamaktadır. (Squad veya Takım listesinde değilsiniz.)');
        }

        // --- VERİLERİN HAZIRLANMASI ---
        
        $takim = $iaa->atananTakim;
        
        $assignment = DB::table('iaa_talepleri')
                        ->where('iaa_id', $iaa->id)
                        ->first(); 

        // Atama yoksa hata ver
        if (!$assignment) {
            if($sikayet) {
                 return redirect()->route('public.sikayet.show', ['token' => $sikayet->takip_token])
                    ->with('error', 'Proje ataması bulunamadı.');
            }
            return redirect()->route('dashboard')->with('error', 'Proje ataması bulunamadı.');
        }

        $workflow = \App\Models\IaaWorkflow::with('steps')->find($assignment->iaa_workflow_id);
        $steps = $workflow->steps;

        $allProgressUpdates = IaaProgressUpdate::where('iaa_talep_id', $assignment->id)->get();

        $completedStepIds = $allProgressUpdates->whereNotNull('completed_at')
                                                ->pluck('iaa_workflow_step_id') 
                                                ->toArray();

        // Adım atamalarını çek
        $stepAssignments = DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa->id)
            ->get()
            ->keyBy('iaa_workflow_step_id'); // Step ID'ye göre erişmek için

        $progressUpdates = $allProgressUpdates->keyBy('iaa_workflow_step_id');
        
        // Takım üyesi mi? (Görselde 'Düzenle' butonlarını göstermek için)
        // Misafirler üye değildir. Yetkili kullanıcı ise kontrol edilir.
        $isTeamMember = $isYetkiliKullanici; 

        $totalStepsCount = $steps->count();
        $completedStepsCount = count($completedStepIds);
        $progressPercentage = $totalStepsCount > 0 ? ($completedStepsCount / $totalStepsCount) * 100 : 0;

        $statusDate = null;
        $logAction = null;

        switch ($iaa->durum) {
            case 'Revize Ediliyor':
                $logAction = 'Revizyon Talep Edildi';
                break;
            case 'Tamamlanması Reddedildi':
                $logAction = 'Tamamlanmış Projenin Reddi';
                break;
        }

        if ($logAction) {
            $latestLog = IaaLog::where('iaa_id', $iaa->id)->where('eylem', $logAction)->latest('created_at')->first();
            if ($latestLog) {
                $statusDate = $latestLog->created_at;
            }
        } elseif ($iaa->durum === 'Tamamlandı') {
            $statusDate = $iaa->onaylanma_tarihi;
        }

        $tumProjeLoglari = IaaLog::where('iaa_id', $iaa->id)
            ->whereIn('eylem', [
                'Proje Adımı Tamamlandı', 
                'Proje Adımı Yeniden Açıldı',
                'Revizyon Talep Edildi', 
                'Proje Onaylandı',
                'Onay Geri Alındı',
            ])
            ->with('user') 
            ->latest()    
            ->get();
                            
        $sonOnLoglar = $tumProjeLoglari->take(5);

        return view('proje-calisma-alani.show', compact(
            'iaa', 'takim', 'assignment', 'workflow', 'steps',
            'completedStepIds', 'progressUpdates', 'totalStepsCount', 'completedStepsCount',
            'progressPercentage', 'isTeamMember', 'statusDate', 'tumProjeLoglari', 'sonOnLoglar', 'stepAssignments'
        ));
    }

    /**
     * Adım Kaydetme İşlemi
     */
    public function storeStep(Request $request, $assignment_id, $step_id)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:20',
        ]);

        $assignment = DB::table('iaa_talepleri')->find($assignment_id);
        $step = IaaWorkflowStep::find($step_id);

        // İlgili projeyi bul
        $iaa = Iaa::find($assignment->iaa_id);

        // === HİBRİT YETKİ KONTROLÜ ===
        if (!$this->checkAuthorization($iaa)) {
            abort(403, 'Bu projeye müdahale etme yetkiniz yok.');
        }
        // ==============================

        // === SORUMLULUK KONTROLÜ (YENİ) ===
        // 1. Adım ataması var mı bak
        $assignmentRecord = DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa->id)
            ->where('iaa_workflow_step_id', $step->id)
            ->first();

        // 2. Eğer atama varsa ve işlem yapan kişi:
        //    - Atanan kişi değilse
        //    - VE Lider değilse (Lider her şeyi yapar)
        //    - VE Admin değilse
        if ($assignmentRecord) {
            $liderId = $iaa->atananTakim->lider_user_id ?? 0;
            $userId = Auth::id();

            if ($assignmentRecord->user_id != $userId && $userId != $liderId && !Auth::user()->hasRole('Superadmin')) {
                $sorumluUser = \App\Models\User::find($assignmentRecord->user_id);
                abort(403, "Bu adım '{$sorumluUser->name}' kullanıcısına atanmıştır. Sadece sorumlu kişi veya lider tamamlayabilir.");
            }
        }
        // ==================================

        IaaProgressUpdate::updateOrCreate(
            [
                'iaa_talep_id' => $assignment->id,
                'iaa_workflow_step_id' => $step->id,
            ],
            [
                'user_id' => Auth::id(),
                'content' => $validated['content'],
                'completed_at' => now(),
            ]
        );
        
        // Loglama
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Proje Adımı Tamamlandı',
            'aciklama' => Auth::user()->name . " adlı kullanıcı, '" . $step->name . "' adımını kaydetti ve tamamladı."
        ]);

        // Bildirim (Takım üyelerine)
        try {
            $takim = \App\Models\Takim::find($assignment->takim_id);
            if ($takim) {
                $guncelleyenKullanici = Auth::user();
                $bildirimAlacaklar = $takim->users->where('id', '!=', $guncelleyenKullanici->id);

                if ($bildirimAlacaklar->isNotEmpty()) {
                    Notification::send($bildirimAlacaklar, new ProjeAdimiGuncellendi($iaa, $step, $guncelleyenKullanici));
                }
            }
        } catch (\Exception $e) {
            // Bildirim hatası süreci durdurmasın
        }

        return redirect()->route('proje.workspace.show', $iaa->id)
                         ->with('success', '"' . $step->name . '" adımı başarıyla tamamlandı!');
    }

    /**
     * Adımı Yeniden Açma İşlemi
     */
    public function reopenStep(IaaProgressUpdate $progress_update)
    {
        $assignment = DB::table('iaa_talepleri')->find($progress_update->iaa_talep_id);
        $iaa = Iaa::find($assignment->iaa_id);

        // === HİBRİT YETKİ KONTROLÜ ===
        if (!$this->checkAuthorization($iaa)) {
            abort(403, 'Bu adımı düzenleme yetkiniz yok.');
        }
        // ==============================

        // === 2. YENİ EKLENEN KISIM: DURUM KİLİDİ ===
        // Eğer proje onay sürecindeyse veya bitmişse, adım açılamaz.
        $kilitliDurumlar = [
            'Bölüm Onayı Bekliyor', 
            'Yönetici Onayı Bekliyor', 
            'Tamamlandı'
        ];

        if (in_array($iaa->durum, $kilitliDurumlar)) {
            return back()->with('error', 'Proje onay aşamasında veya tamamlandığı için değişiklik yapılamaz. Önce revizyon talep edilmelidir.');
        }
        // === KİLİT SONU ===

        $progress_update->update(['completed_at' => null]);
        DB::table('iaa_talepleri')->where('id', $assignment->id)->update(['status' => 'Devam Ediyor']);

        // Loglama
        $progress_update->load('step'); 
        $stepName = $progress_update->step ? $progress_update->step->name : 'Bilinmeyen Adım';

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Proje Adımı Yeniden Açıldı',
            'aciklama' => Auth::user()->name . " adlı kullanıcı, '" . $stepName . "' adımını yeniden düzenlemek için açtı."
        ]);

        return redirect()->route('proje.workspace.show', $iaa->id)
                         ->with('success', 'Adım yeniden düzenlemeye açıldı.');
    }

    /**
     * Yanlışlıkla açılan adımı tekrar kapatır (DB Update Yöntemi).
     */
    public function cancelReopenStep($id)
    {
        // 1. Adımın kime ait olduğunu bul (Güvenlik için)
        $update = \Illuminate\Support\Facades\DB::table('iaa_progress_updates')->where('id', $id)->first();
        
        if (!$update) {
            return back()->with('error', 'Kayıt bulunamadı.');
        }

        // 2. Hangi projeye ait olduğunu bul
        $assignment = \Illuminate\Support\Facades\DB::table('iaa_talepleri')->where('id', $update->iaa_talep_id)->first();
        
        // 3. ZORLA KAPAT (Veritabanına direkt yazıyoruz)
        \Illuminate\Support\Facades\DB::table('iaa_progress_updates')
            ->where('id', $id)
            ->update([
                'completed_at' => now(), // Şu anki zamanı bas
                'updated_at' => now()
            ]);

        // 4. Proje sayfasına geri dön
        return redirect()->route('proje.workspace.show', $assignment->iaa_id)->with('info', 'İşlem iptal edildi, adım kapatıldı.');

    }

    /**
     * ============================================================
     * MERKEZİ YETKİ KONTROLÜ (HİBRİT MANTIK)
     * ============================================================
     */
    private function checkAuthorization(Iaa $iaa)
    {
        $user = Auth::user();
        
        // 1. Superadmin veya Yönetim Joker
        // Yönetim rolü de Superadmin gibi her yere girebilsin (izleme amaçlı)
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            return true;
        }

        // 2. Müşteri Şikayeti Kaynaklı Proje (SQUAD MANTIĞI)
        if ($iaa->musteriSikayeti) {
            
            // A) Takım Lideri mi? (Ana lider her zaman yetkilidir)
            if ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $user->id) {
                return true;
            }

            // === DEĞİŞEN KISIM BURASI ===
            // B) Squad Üyesi mi? (Lider eklemiş VE Kullanıcı KABUL ETMİŞ mi?)
            // contains() yerine veritabanı sorgusu ile pivot tablodaki 'durum'u kontrol ediyoruz.
            if ($iaa->projeEkibi()->where('user_id', $user->id)->wherePivot('durum', 'onaylandi')->exists()) {
                return true;
            }
            // ============================

            // C) Bölüm Kalite Yöneticisi mi? (Kategori Sorumlusu)
            if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                $sikayetKategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                if ($sikayetKategoriId && $user->yonettigiSikayetKategorileri->contains($sikayetKategoriId)) {
                    return true;
                }
            }

            return false; // Hiçbiri değilse giremez
        }

        // 3. Standart İAA Projesi (TAKIM HAVUZU MANTIĞI)
        else {
            // Takımın herhangi bir üyesi girebilir
            if ($user->takimlar->contains($iaa->atanan_takim_id)) {
                return true;
            }
        }

        return false;
    }

    public function assignUserToStep(Request $request, $iaa_id, $step_id)
    {
        $iaa = Iaa::with('atananTakim', 'projeEkibi')->findOrFail($iaa_id);
        $step = IaaWorkflowStep::findOrFail($step_id);
        $liderId = $iaa->atananTakim->lider_user_id;
        $currentUser = Auth::user();

        // Yetki: Sadece Lider veya Admin atama yapabilir
        if ($currentUser->id != $liderId && !$currentUser->hasRole('Superadmin')) {
            abort(403, 'Atama yapma yetkiniz yok.');
        }

        $targetUserId = $request->input('user_id');
        
        // Atamayı kaydet veya güncelle (updateOrCreate)
        // DB::table kullanarak model oluşturmadan hızlıca yapıyoruz
        if ($targetUserId) {
            DB::table('iaa_step_assignments')->updateOrInsert(
                ['iaa_id' => $iaa->id, 'iaa_workflow_step_id' => $step->id],
                [
                    'user_id' => $targetUserId,
                    'assigned_by' => $currentUser->id,
                    'updated_at' => now(),
                    'created_at' => now() // updateOrInsert created_at'i otomatik doldurmaz, ilk kayıtta lazım
                ]
            );
            
            $sorumlu = \App\Models\User::find($targetUserId);
            $mesaj = "Adım sorumlusu '{$sorumlu->name}' olarak güncellendi.";

            // BİLDİRİM GÖNDER (Tüm Ekibe)
            // Lider hariç herkese gitsin
            $ekip = $iaa->projeEkibi->merge([$iaa->atananTakim->lider]); // Lideri de listeye al (eğer admin atadıysa)
            $notifyList = $ekip->filter(fn($u) => $u->id != $currentUser->id);
            
            if($notifyList->isNotEmpty()){
                 \Illuminate\Support\Facades\Notification::send($notifyList, new \App\Notifications\AdimSorumlusuAtandi($iaa, $step, $sorumlu, $currentUser));
            }

        } else {
            // Eğer user_id boş geldiyse atamayı kaldır (Opsiyonel)
             DB::table('iaa_step_assignments')
                ->where('iaa_id', $iaa->id)
                ->where('iaa_workflow_step_id', $step->id)
                ->delete();
             $mesaj = "Adım ataması kaldırıldı, herkes işlem yapabilir.";
        }

        return back()->with('success', $mesaj);
    }

    /**
     * Müşteriye manuel bildirim gönderir, token ve şifre oluşturur.
     */
    public function notifyCustomer(Request $request, $id)
    {
        // 1. Yetki Kontrolü
        if (!Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi'])) {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $iaa = Iaa::findOrFail($id);
        $sikayet = $iaa->musteriSikayeti;

        if (!$sikayet) {
            return back()->with('error', 'Bu projeye bağlı bir müşteri şikayeti bulunamadı.');
        }
        
        if (empty($sikayet->musteri_iletisim)) {
            return back()->with('error', 'Müşteri e-posta adresi kayıtlı değil.');
        }

        // 2. Token ve Şifre Üretimi
        if (!$sikayet->takip_token) {
            $sikayet->takip_token = Str::random(12);
        }

        // Şifreyi üret
        $plainPassword = Str::random(8); 
        // Hashleyerek kaydet
        $sikayet->guest_password_hash = Hash::make($plainPassword);

        // 3. Bildirim Bilgilerini Kaydet
        $sikayet->musteri_bildirim_yapan_id = Auth::id();
        $sikayet->musteri_bildirim_tarihi = now();
        $sikayet->save();

        // 4. Log Tut
        MusteriSikayetiLog::create([
            'musteri_sikayeti_id' => $sikayet->id,
            'user_id' => Auth::id(),
            'eylem' => 'Müşteri Bilgilendirildi',
            'aciklama' => Auth::user()->name . " tarafından müşteriye takip linki gönderildi."
        ]);

        // 5. Mail Gönder
        try {
            Mail::to($sikayet->musteri_iletisim)->send(new SikayetTakipBilgilendirmeMail($sikayet, $plainPassword, false));
        } catch (\Exception $e) {
            // Mail gitmese bile işlem başarılı sayılsın ama uyarı verelim
            return back()->with('success', 'Müşteri bilgileri oluşturuldu ancak mail gönderilemedi. Şifre: ' . $plainPassword)->with('generated_password', $plainPassword);
        }

        // 6. Başarı ve Şifreyi Ekranda Göster
        return back()->with('success', 'Müşteriye bildirim gönderildi.')->with('generated_password', $plainPassword);
    }

    /**
     * Şifreyi sıfırlar ve tekrar gönderir.
     */
    public function resetCustomerPassword($id)
    {
        if (!Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi'])) {
            abort(403);
        }

        $iaa = Iaa::findOrFail($id);
        $sikayet = $iaa->musteriSikayeti;

        if (!$sikayet) return back()->with('error', 'Şikayet bulunamadı.');

        // Yeni Şifre
        $newPlainPassword = Str::random(8);
        $sikayet->guest_password_hash = Hash::make($newPlainPassword);
        $sikayet->save();

        // Log
        MusteriSikayetiLog::create([
            'musteri_sikayeti_id' => $sikayet->id,
            'user_id' => Auth::id(),
            'eylem' => 'Müşteri Şifresi Sıfırlandı',
            'aciklama' => Auth::user()->name . " tarafından müşteri şifresi yenilendi."
        ]);

        // Mail
        try {
            Mail::to($sikayet->musteri_iletisim)->send(new SikayetTakipBilgilendirmeMail($sikayet, $newPlainPassword, true));
        } catch (\Exception $e) {}

        return back()->with('success', 'Şifre sıfırlandı.')->with('generated_password', $newPlainPassword);
    }
}