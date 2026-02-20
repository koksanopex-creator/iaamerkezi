<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\User;
use App\Notifications\ProjeDurumuDegisti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ProjeTalepYonetimService
{
    /**
     * Projeyi Talebe Çevirme
     */
    public function markAsRequest(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        $iaa->update([
            'durum' => 'talep_onayi_bekliyor_kalite',
            'talep_gerekcesi' => $request->gerekce,
            'talep_isteyen_user_id' => Auth::id()
        ]);

        // Log Kaydı
        \App\Models\IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Talep Başlatıldı',
            'aciklama' => "Proje için TALEP süreci başlatıldı. Gerekçe: {$request->gerekce}"
        ]);

        // Log Kaydı
        \App\Models\IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Talep Başlatıldı',
            'aciklama' => "Proje için TALEP süreci başlatıldı. Gerekçe: {$request->gerekce}"
        ]);

        $this->notifyRequestProcess($iaa, 'markAsRequest');
    }

    /**
     * Kalite Yöneticisi Kararı
     */
    public function decideRequestByQuality(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasRole('Bölüm Kalite Yöneticisi') && !$user->hasRole('Superadmin')) {
            abort(403, 'Yetkisiz işlem.');
        }

        if ($request->action == 'reject') {
            $request->validate(['not' => 'required|string']);

            $iaa->update([
                'durum' => 'calisiliyor',
                'talep_kalite_notu' => $request->not
            ]);

            // Log Kaydı
            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Kalite Reddi (Talep)',
                'aciklama' => "Talep isteği Kalite Yöneticisi tarafından reddedildi. Not: {$request->not}"
            ]);

            $this->notifyRequestProcess($iaa, 'rejectByQuality');

            return ['status' => 'error', 'message' => 'Reddedildi, süreç şikayet olarak devam edecek.'];
        }

        if ($request->action == 'approve') {
            $request->validate(['not' => 'nullable|string']);

            // Direktör Onayı Gerekli mi?
            $direktorOnayiAktif = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->value('value');
            $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
            $direktor = $bolum ? $bolum->director : null;

            if ($direktorOnayiAktif && $direktor) {
                // Direktöre Gönder
                $iaa->update([
                    'durum' => 'talep_onayi_bekliyor_direktor', // Bu durumu modele eklemek gerekecek
                    'talep_kalite_notu' => $request->not,
                ]);

                // Log Kaydı
                \App\Models\IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Kalite Onayı (Talep)',
                    'aciklama' => 'Talep isteği Kalite Yöneticisi tarafından onaylandı ve Direktör onayına sunuldu.'
                ]);

                // Log Kaydı (Direktöre Giden Yol)
                \App\Models\IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Kalite Onayı (Talep)',
                    'aciklama' => 'Talep isteği Kalite Yöneticisi tarafından onaylandı ve Direktör onayına sunuldu.'
                ]);

                $this->notifyRequestProcess($iaa, 'approveByQuality_ToDirector', $direktor);

                return ['status' => 'success', 'message' => 'Onaylandı, Bölüm Direktörüne iletildi.'];
            }

            // Direktör Yoksa -> Superadmin'e Gönder
            $iaa->update([
                'durum' => 'talep_onayi_bekliyor_superadmin',
                'talep_kalite_notu' => $request->not
            ]);

            // Log Kaydı
            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Kalite Onayı (Talep)',
                'aciklama' => 'Talep isteği Kalite Yöneticisi tarafından onaylandı ve Superadmin onayına sunuldu.'
            ]);

            $this->notifyRequestProcess($iaa, 'approveByQuality_ToSuperadmin');

            return ['status' => 'success', 'message' => 'Onaylandı, Superadmin\'e iletildi.'];
        }
    }

    /**
     * Direktör Kararı (Talep) - YENİ
     */
    public function decideRequestByDirector(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if ($request->action == 'reject') {
            $request->validate(['not' => 'required|string']);

            $iaa->update([
                'durum' => 'calisiliyor',
            ]);
            // Notu nereye kaydedeceğiz? Modelde talep_direktor_notu var.
            // Fakat red durumunda loglamak daha doğru olabilir veya update ile.
            // Migration ile talep_direktor_notu ekledik.
            $iaa->update(['talep_direktor_notu' => $request->not]);

            // Log Kaydı
            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Direktör Reddi (Talep)',
                'aciklama' => "Talep isteği Direktör tarafından reddedildi. Not: {$request->not}"
            ]);

            // Log Kaydı
            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Direktör Reddi (Talep)',
                'aciklama' => "Talep isteği Direktör tarafından reddedildi. Not: {$request->not}"
            ]);

            $this->notifyRequestProcess($iaa, 'rejectByDirector');

            return ['status' => 'error', 'message' => 'Talep reddedildi, süreç "Devam Ediyor" statüsüne alındı.'];
        }

        if ($request->action == 'approve') {
            $iaa->update([
                'durum' => 'talep_onayi_bekliyor_superadmin',
                'talep_direktor_notu' => $request->not,
                'talep_direktor_user_id' => Auth::id(),
                'talep_direktor_at' => now()
            ]);

            // Log Kaydı
            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Direktör Onayı (Talep)',
                'aciklama' => "Talep isteği Direktör tarafından onaylandı ve Superadmin'e iletildi. Not: {$request->not}"
            ]);

            // Log Kaydı
            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Direktör Onayı (Talep)',
                'aciklama' => "Talep isteği Direktör tarafından onaylandı ve Superadmin'e iletildi. Not: {$request->not}"
            ]);

            $this->notifyRequestProcess($iaa, 'approveByDirector_ToSuperadmin');

            return ['status' => 'success', 'message' => 'Onaylandı, Üst Yönetime (Superadmin) iletildi.'];
        }
    }

    /**
     * Superadmin Kararı
     */
    public function decideRequestBySuperadmin(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if ($request->action == 'approve') {

            // 1. Durumları Güncelle
            $iaa->update([
                'durum' => 'talep_olarak_kapatildi',
                'tamamlanma_tarihi' => now(),
                'proje_puani' => 0,
                'kapanis_notu' => 'Talep olarak kapatıldı, puan verilmedi.',
                'talep_admin_notu' => $request->not // Onay notunu da kaydedelim
            ]);

            // Log Kaydı
            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Talep Kapanışı',
                'aciklama' => "Dosya TALEP olarak kabul edildi ve kapatıldı. Puanlar iade edildi. Yönetim Notu: {$request->not}"
            ]);

            // Ekip puanlarını sıfırla
            foreach ($iaa->projeEkibi as $uye) {
                $iaa->projeEkibi()->updateExistingPivot($uye->id, ['kazanilan_puan' => 0]);
            }

            // Müşteri Şikayetini kapat ve Puan İadesi Yap
            if ($iaa->musteriSikayeti) {
                $iaa->musteriSikayeti->update([
                    'musteri_durum' => 'Kapatıldı',
                    'musteri_cozum_notlari' => 'Bu kayıt bir iyileştirme projesi değil, müşteri talebi olarak değerlendirilmiş ve kapatılmıştır.',
                    'musteri_onay_tarihi' => now(),
                    'kurul_onay_tarihi' => now()
                ]);

                // PUAN İADESİ (Sadece oluşturandan)
                $olusturan = $iaa->musteriSikayeti->olusturanKurulUyesi;
                $kazanilanPuan = $iaa->musteriSikayeti->kazanilan_puan;

                if ($olusturan && $kazanilanPuan > 0) {
                    $olusturan->decrement('toplam_puan', $kazanilanPuan);
                    \App\Models\MusteriLog::add(
                        $iaa->musteriSikayeti->customer_id,
                        'Talep Dönüşümü Puan İadesi',
                        "Projenin 'Talep' olarak kapatılması nedeniyle {$olusturan->name} kullanıcısından {$kazanilanPuan} puan geri alındı."
                    );
                }
            }

            // Log Kaydı
            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Talep Kapanışı',
                'aciklama' => "Dosya TALEP olarak kabul edildi ve kapatıldı. Puanlar iade edildi. Yönetim Notu: {$request->not}"
            ]);

            $this->notifyRequestProcess($iaa, 'closeAsRequest');

            return ['status' => 'success', 'message' => 'Dosya talep olarak kapatıldı. Puan verilmedi ve varsa iade alındı.', 'redirect' => 'dashboard'];
        }

        // RED DURUMU
        $iaa->update([
            'durum' => 'calisiliyor', // Atandı yerine calisiliyor daha mantıklı, kaldığı yerden devam.
            'talep_admin_notu' => $request->not
        ]);

        // Log Kaydı
        \App\Models\IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Yönetim Reddi (Talep)',
            'aciklama' => "Talep isteği Superadmin tarafından reddedildi. Not: {$request->not}"
        ]);

        // Log Kaydı
        \App\Models\IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Yönetim Reddi (Talep)',
            'aciklama' => "Talep isteği Superadmin tarafından reddedildi. Not: {$request->not}"
        ]);

        $this->notifyRequestProcess($iaa, 'rejectBySuperadmin');

        return ['status' => 'info', 'message' => 'Talep reddedildi, süreç "Devam Ediyor" statüsüne alındı.'];
    }

    /**
     * Merkezi Talep Bildirim Yönetimi
     */
    private function notifyRequestProcess(Iaa $iaa, $type, $targetUser = null)
    {
        $lider = $iaa->atananTakim ? $iaa->atananTakim->lider : null;
        $olusturan = $iaa->musteriSikayeti ? $iaa->musteriSikayeti->olusturanKurulUyesi : null;
        $kaliteYoneticileri = User::role('Bölüm Kalite Yöneticisi')->get();
        $superadmins = User::role('Superadmin')->get();
        $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
        $direktor = $bolum ? $bolum->director : null;

        $baslik = $iaa->baslik;

        // Bildirim Sınıfı: App\Notifications\TalepBildirimi (Yeni oluşturalım)

        switch ($type) {
            case 'markAsRequest':
                // Kalite Yöneticisine
                Notification::send($kaliteYoneticileri, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "{$baslik} isimli proje {$lider->name} tarafından TALEP olarak işaretlenip onayınıza sunulmuştur.",
                    'Kalite Yöneticisi'
                ));
                // Direktöre (Bilgi)
                if ($direktor) {
                    $direktor->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} Başlıklı şikayet Bölüm Kalite Yöneticisi onayına 'Bu Bir Taleptir' istemi ile {$lider->name} tarafından sunulmuştur.",
                        'Direktör'
                    ));
                }
                // Şikayeti Açana
                if ($olusturan) {
                    $olusturan->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} başlıklı şikayetiniz için {$lider->name} tarafından Talep onayı süreci başlatılmıştır.",
                        'Şikayet Sahibi'
                    ));
                }
                // Superadmin'e (Sürekli Bildirim)
                Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "{$baslik} için {$lider->name} tarafından TALEP süreci başlatılmıştır.",
                    'Superadmin'
                ));
                break;

            case 'rejectByQuality':
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Proje Talep isteğiniz Kalite Yöneticisi tarafından REDDEDİLDİ.",
                        'Takım Lideri'
                    ));
                }
                // Şikayeti Açana
                if ($olusturan) {
                    $olusturan->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Talep süreci Kalite Yöneticisi tarafından reddedildi. Süreç proje olarak devam edecek.",
                        'Şikayet Sahibi'
                    ));
                }
                // Superadmin'e
                Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "{$baslik} talep isteği Kalite Yöneticisi tarafından REDDEDİLDİ.",
                    'Superadmin'
                ));
                break;

            case 'approveByQuality_ToDirector':
                // IaaLog Kaydı
                \App\Models\IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => Auth::id(), // Kalite Yöneticisi
                    'eylem' => 'Kalite Onayı (Talep)',
                    'aciklama' => 'Talep isteği Kalite Yöneticisi tarafından onaylandı ve Direktöre iletildi.'
                ]);

                // Direktöre
                if ($targetUser) {
                    $targetUser->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} nolu proje Kalite Yöneticisi onayıyla TALEP olarak işaretlenip son onayınıza sunulmuştur.",
                        'Direktör'
                    ));
                }
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Talep isteğiniz Kalite Yöneticisi tarafından onaylandı. Son onay için Direktöre iletildi.",
                        'Takım Lideri'
                    ));
                }
                // Superadmin'e
                Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "{$baslik} talep isteği Kalite tarafından onaylandı ve Direktöre iletildi.",
                    'Superadmin'
                ));
                break;

            case 'approveByQuality_ToSuperadmin':
                // Superadmin'e (Zaten gidiyor ama mesajı netleştirelim)
                Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "{$baslik} nolu proje Kalite Yöneticisi onayıyla TALEP olarak işaretlenip onayınıza sunulmuştur.",
                    'Superadmin'
                ));
                break;

            case 'rejectByDirector':
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Talep isteğiniz Direktör tarafından REDDEDİLDİ.",
                        'Takım Lideri'
                    ));
                }
                // Kalite Yöneticisine
                Notification::send($kaliteYoneticileri, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "Onayladığınız talep isteği Direktör tarafından REDDEDİLDİ.",
                    'Kalite Yöneticisi'
                ));
                // Şikayeti Açana
                if ($olusturan) {
                    $olusturan->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Talep süreci Direktör tarafından reddedildi. Süreç devam ediyor.",
                        'Şikayet Sahibi'
                    ));
                }
                // Superadmin'e
                Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "{$baslik} talep isteği Direktör tarafından REDDEDİLDİ.",
                    'Superadmin'
                ));
                break;

            case 'approveByDirector_ToSuperadmin':
                // Superadmin'e (Zaten gidiyor)
                Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "{$baslik} nolu proje Direktör onayıyla TALEP olarak işaretlenip son onayınıza sunulmuştur.",
                    'Superadmin'
                ));
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Talep isteğiniz Direktör tarafından onaylandı. Son onay için Üst Yönetime iletildi.",
                        'Takım Lideri'
                    ));
                }
                break;

            case 'rejectBySuperadmin':
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Talep isteğiniz Üst Yönetim (Superadmin) tarafından REDDEDİLDİ.",
                        'Takım Lideri'
                    ));
                }
                // Direktöre (varsa)
                if ($direktor) {
                    $direktor->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Onayladığınız talep isteği Üst Yönetim tarafından REDDEDİLDİ.",
                        'Direktör'
                    ));
                }
                // Superadmin'e de bilgi (Kendi kendine işlem ama log gibi düşsün)
                /* Gerek yok zaten işlemi yapan o */
                break;

            case 'closeAsRequest':
                $successMsg = "{$baslik} dosyası TALEP (Request) olarak kabul edildi ve kapatıldı. İlgili puanlar iade alındı.";

                // Şikayeti Açana
                if ($olusturan) {
                    $olusturan->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} Başlıklı şikayetiniz Müşteri Talebi olarak kabul edilmiştir. Şikayet puanınız geri alınmıştır.",
                        'Şikayet Sahibi'
                    ));
                }
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        $successMsg,
                        'Takım Lideri'
                    ));
                }
                // Kalite Yöneticisine
                Notification::send($kaliteYoneticileri, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    $successMsg,
                    'Kalite Yöneticisi'
                ));
                // Direktöre
                if ($direktor) {
                    $direktor->notify(new \App\Notifications\TalepBildirimi(
                        $iaa,
                        $successMsg,
                        'Direktör'
                    ));
                }
                // Superadmin'e (Diğer superadminlere de gitsin)
                Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                    $iaa,
                    "TAMAMLANDI: {$successMsg}",
                    'Superadmin'
                ));
                break;
        }
    }

    // --- HATALI BİLDİRİM YÖNETİMİ ---


    /**
     * Hatalı Bildirim İşaretleme (Lider Tarafından)
     */
    public function markAsFaulty(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        $iaa->update([
            'durum' => 'hatali_bildirim_onayi_bekliyor_kalite',
            'hatali_bildirim_gerekcesi' => $request->gerekce,
            'hatali_bildirim_tarihi' => now(),
            'talep_isteyen_user_id' => Auth::id()
        ]);

        $this->notifyFaultyProcess($iaa, 'markAsFaulty');
    }

    /**
     * Kalite Yöneticisi Kararı (Hatalı Bildirim)
     */
    public function decideFaultyByQuality(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if ($request->action == 'reject') {
            $iaa->update(['durum' => 'Devam Ediyor']);
            $this->notifyFaultyProcess($iaa, 'rejectByQuality');
            return ['status' => 'error', 'message' => 'Hatalı bildirim reddedildi, süreç devam ediyor.'];
        }

        if ($request->action == 'approve') {
            // Direktör Onayı Gerekli mi?
            $direktorOnayiAktif = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->value('value');
            $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
            $direktor = $bolum ? $bolum->director : null;

            if ($direktorOnayiAktif && $direktor) {
                // Direktöre Gönder
                $iaa->update([
                    'durum' => 'hatali_bildirim_onayi_bekliyor_direktor',
                    'hatali_bildirim_kalite_notu' => $request->not,
                    'hatali_bildirim_kalite_user_id' => Auth::id(),
                    'hatali_bildirim_kalite_at' => now()
                ]);
                $this->notifyFaultyProcess($iaa, 'approveByQuality_ToDirector', $direktor);
                return ['status' => 'success', 'message' => 'Onaylandı, Bölüm Direktörüne iletildi.'];
            }

            // Direktör Yoksa veya Ayar Kapalıysa -> Superadmin'e Gönder
            $iaa->update([
                'durum' => 'hatali_bildirim_onayi_bekliyor_superadmin',
                'hatali_bildirim_kalite_notu' => $request->not,
                'hatali_bildirim_kalite_user_id' => Auth::id(),
                'hatali_bildirim_kalite_at' => now()
            ]);
            $this->notifyFaultyProcess($iaa, 'approveByQuality_ToSuperadmin');
            return ['status' => 'success', 'message' => 'Onaylandı, Üst Yönetim (Superadmin) onayına sunuldu.'];
        }
    }

    /**
     * Superadmin Kararı (Hatalı Bildirim)
     */
    public function decideFaultyBySuperadmin(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if ($request->action == 'reject') {
            $iaa->update(['durum' => 'Devam Ediyor']);
            $this->notifyFaultyProcess($iaa, 'rejectBySuperadmin');
            return ['status' => 'error', 'message' => 'Hatalı bildirim Superadmin tarafından reddedildi, süreç devam ediyor.'];
        }

        if ($request->action == 'approve') {
            $iaa->update([
                'hatali_bildirim_superadmin_notu' => $request->not,
                'hatali_bildirim_superadmin_user_id' => Auth::id(),
                'hatali_bildirim_superadmin_at' => now()
            ]);
            $this->closeAsFaulty($iaa);
            return ['status' => 'success', 'message' => 'Hatalı bildirim onaylandı ve dosya kapatıldı.'];
        }
    }

    /**
     * Hatalı Bildirim Talebini Geri Al (Team Leader)
     */
    public function recallFaulty($id)
    {
        $iaa = Iaa::findOrFail($id);

        // Sadece Lider geri alabilir (Bunu Controller'da da kontrol edeceğiz ama burada durum kontrolü de yapalım)
        $beklemeDurumlari = [
            'hatali_bildirim_onayi_bekliyor_kalite',
            'hatali_bildirim_onayi_bekliyor_direktor',
            'hatali_bildirim_onayi_bekliyor_superadmin'
        ];

        if (!in_array($iaa->durum, $beklemeDurumlari)) {
            return ['status' => 'error', 'message' => 'Bu aşamada geri alma işlemi yapılamaz.'];
        }

        $iaa->update(['durum' => 'Devam Ediyor']);

        $this->notifyFaultyProcess($iaa, 'recallFaulty');

        return ['status' => 'success', 'message' => 'Hatalı bildirim talebiniz geri alındı, süreç devam ediyor.'];
    }

    /**
     * Direktör Kararı (Hatalı Bildirim)
     */
    public function decideFaultyByDirector(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if ($request->action == 'reject') {
            $iaa->update(['durum' => 'Devam Ediyor']);
            $this->notifyFaultyProcess($iaa, 'rejectByDirector');
            return ['status' => 'error', 'message' => 'Hatalı bildirim reddedildi, süreç devam ediyor.'];
        }

        if ($request->action == 'approve') {
            $iaa->update([
                'hatali_bildirim_direktor_notu' => $request->not,
                'hatali_bildirim_direktor_user_id' => Auth::id(),
                'hatali_bildirim_direktor_at' => now()
            ]);
            $this->closeAsFaulty($iaa);
            return ['status' => 'success', 'message' => 'Hatalı bildirim onaylandı ve dosya kapatıldı.'];
        }
    }

    /**
     * Yardımcı Metod: Hatalı Bildirim Olarak Kapat ve Puan Sil
     */
    private function closeAsFaulty(Iaa $iaa)
    {
        // 1. Durumları Güncelle
        $iaa->update(['durum' => 'hatali_bildirim_olarak_kapatildi']);

        if ($iaa->musteriSikayeti) {
            $iaa->musteriSikayeti->update([
                'musteri_durum' => 'Kapatıldı',
                'kapama_nedeni' => 'Hatalı Bildirim: ' . $iaa->hatali_bildirim_gerekcesi
            ]);

            // 2. Puan İadesi (SADECE OLUŞTURAN KİŞİDEN)
            $olusturan = $iaa->musteriSikayeti->olusturanKurulUyesi;
            $kazanilanPuan = $iaa->musteriSikayeti->kazanilan_puan;

            if ($olusturan && $kazanilanPuan > 0) {
                $olusturan->decrement('toplam_puan', $kazanilanPuan);
                // Log atalım ki puanın neden düştüğü belli olsun
                \App\Models\MusteriLog::add(
                    $iaa->musteriSikayeti->customer_id,
                    'Hatalı Bildirim Puan İadesi',
                    "Hatalı bildirim nedeniyle {$olusturan->name} kullanıcısından {$kazanilanPuan} puan geri alındı."
                );
            }
        }

        // 3. Bildirimler
        $this->notifyFaultyProcess($iaa, 'closeAsFaulty');
    }

    /**
     * Merkezi Bildirim Yönetimi
     */
    private function notifyFaultyProcess(Iaa $iaa, $type, $targetUser = null)
    {
        $lider = $iaa->atananTakim ? $iaa->atananTakim->lider : null;
        $olusturan = $iaa->musteriSikayeti ? $iaa->musteriSikayeti->olusturanKurulUyesi : null;
        $kaliteYoneticileri = User::role('Bölüm Kalite Yöneticisi')->get();
        $superadmins = User::role('Superadmin')->get();
        $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
        $direktor = $bolum ? $bolum->director : null;

        $baslik = $iaa->baslik;

        switch ($type) {
            case 'markAsFaulty':
                // Kalite Yöneticisine
                Notification::send($kaliteYoneticileri, new \App\Notifications\HataliBildirimBildirimi(
                    $iaa,
                    "{$baslik} isimli şikayet {$lider->name} tarafından hatalı olarak işaretlenip onayınıza sunulmuştur.",
                    'Kalite Yöneticisi'
                ));
                // Direktöre
                if ($direktor) {
                    $direktor->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "{$baslik} Başlıklı şikayet Bölüm kalite yöneticisi onayına hatalı bildirim olarak sunulmuştur.",
                        'Direktör'
                    ));
                }
                // Şikayeti Açana
                if ($olusturan) {
                    $olusturan->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "{$baslik} başlıklı şikayetiniz {$lider->name} tarafından Hatalı Bildirim olarak işaretlendi. Onay bekleniyor.",
                        'Şikayet Sahibi'
                    ));
                }
                break;

            case 'rejectByQuality':
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "Hatalı bildirim talebiniz Kalite Yöneticisi tarafından REDDEDİLDİ.",
                        'Takım Lideri'
                    ));
                }
                // Şikayeti Açana
                if ($olusturan) {
                    $olusturan->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "{$baslik} başlıklı şikayetiniz için yapılan hatalı bildirim talebi REDDEDİLDİ. Süreç devam ediyor.",
                        'Şikayet Sahibi'
                    ));
                }
                break;

            case 'approveByQuality_ToDirector':
                // Direktöre
                if ($targetUser) {
                    $targetUser->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "{$baslik} nolu şikayet Kalite Yöneticisi onayıyla Hatalı Bildirim olarak işaretlenip onayınıza sunulmuştur.",
                        'Direktör'
                    ));
                }
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "Hatalı bildirim talebiniz Kalite Yöneticisi tarafından onaylandı. Son onay için Direktöre iletildi.",
                        'Takım Lideri'
                    ));
                }
                break;

            case 'approveByQuality_ToSuperadmin':
                // Superadmin'lere
                Notification::send($superadmins, new \App\Notifications\HataliBildirimBildirimi(
                    $iaa,
                    "{$baslik} nolu şikayet Kalite Yöneticisi onayıyla Hatalı Bildirim olarak işaretlenip onayınıza sunulmuştur.",
                    'Superadmin'
                ));
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "Hatalı bildirim talebiniz Kalite Yöneticisi tarafından onaylandı. Son onay için Üst Yönetime iletildi.",
                        'Takım Lideri'
                    ));
                }
                break;

            case 'rejectByDirector':
            case 'rejectBySuperadmin':
                $rolAd = ($type == 'rejectByDirector') ? 'Direktör' : 'Superadmin';
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "Hatalı bildirim talebiniz {$rolAd} tarafından REDDEDİLDİ.",
                        'Takım Lideri'
                    ));
                }
                // Kalite Yöneticisine (Bilgi)
                Notification::send($kaliteYoneticileri, new \App\Notifications\HataliBildirimBildirimi(
                    $iaa,
                    "Onayladığınız hatalı bildirim {$rolAd} tarafından REDDEDİLDİ.",
                    'Kalite Yöneticisi'
                ));
                break;

            case 'recallFaulty':
                $recallMsg = "{$baslik} şikayeti için yapılan Hatalı Bildirim talebi {$lider->name} tarafından geri çekildi.";
                // Kalite Yöneticisine
                Notification::send($kaliteYoneticileri, new \App\Notifications\HataliBildirimBildirimi(
                    $iaa,
                    $recallMsg,
                    'Kalite Yöneticisi'
                ));
                // Direktöre
                if ($direktor) {
                    $direktor->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        $recallMsg,
                        'Direktör'
                    ));
                }
                break;

            case 'closeAsFaulty':
                // Herkese Bildir
                $successMsg = "{$baslik} dosyası Hatalı Bildirim olarak kesinleşti ve kapatıldı. Puanlar iade edildi.";

                // Şikayeti Açana
                if ($olusturan) {
                    $olusturan->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "{$baslik} Başlıklı şikayetiniz Hatalı Bildirim olarak karara bağlanmıştır. Puanlarınız geri alınmıştır.",
                        'Şikayet Sahibi'
                    ));
                }
                // Lidere
                if ($lider) {
                    $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        $successMsg,
                        'Takım Lideri'
                    ));
                }
                // Kalite Yöneticisine
                Notification::send($kaliteYoneticileri, new \App\Notifications\HataliBildirimBildirimi(
                    $iaa,
                    $successMsg,
                    'Kalite Yöneticisi'
                ));
                // Direktöre
                if ($direktor) {
                    $direktor->notify(new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        $successMsg,
                        'Direktör'
                    ));
                }
                // Superadmin'e
                Notification::send($superadmins, new \App\Notifications\HataliBildirimBildirimi(
                    $iaa,
                    "TAMAMLANDI: {$successMsg}",
                    'Superadmin'
                ));
                break;
        }
    }
}