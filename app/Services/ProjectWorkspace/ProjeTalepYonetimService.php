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
                'durum' => 'Atandı',
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
                'durum' => 'Atandı',
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

            // Kapanış Tarihi Hesaplama
            $tamamlanmaTarihi = now();
            if ($iaa->talep_direktor_at) {
                $tamamlanmaTarihi = $iaa->talep_direktor_at;
            } else {
                $kaliteLog = \App\Models\IaaLog::where('iaa_id', $iaa->id)
                    ->where('eylem', 'Kalite Onayı (Talep)')
                    ->orderBy('created_at', 'desc')
                    ->first();
                if ($kaliteLog) {
                    $tamamlanmaTarihi = $kaliteLog->created_at;
                }
            }

            // 1. Durumları Güncelle
            $iaa->update([
                'durum' => 'talep_olarak_kapatildi',
                'tamamlanma_tarihi' => $tamamlanmaTarihi,
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
                    'musteri_onay_tarihi' => $tamamlanmaTarihi,
                    'kurul_onay_tarihi' => $tamamlanmaTarihi
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
            'durum' => 'Atandı', // Reddedildiğinde tekrar Atandı statüsüne döner
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

        // Bölüm Kalite Yöneticilerini Bul (Sadece bu kategoriden sorumlu olanlar)
        $kaliteYoneticileri = collect();
        if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayet_kategorisi_id) {
            $catId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
            $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                ->whereHas('yonettigiSikayetKategorileri', function($q) use ($catId) {
                    $q->where('sikayet_kategorileri.id', $catId);
                })->get();
        } elseif ($iaa->bolum_id) {
            // Eğer şikayete bağlı değilse (normal İAA), bölüme bağlı tüm yöneticilere gitsin
            $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                ->where('bolum_id', $iaa->bolum_id)
                ->get();
        }

        $superadmins = User::role('Superadmin')->get();
        $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
        $direktor = $bolum ? $bolum->director : null;

        $baslik = $iaa->baslik;

        // Bildirim Sınıfı: App\Notifications\TalepBildirimi (Yeni oluşturalım)

        switch ($type) {
            case 'markAsRequest':
                // Kalite Yöneticisine
                try {
                    Notification::send($kaliteYoneticileri, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} isimli proje {$lider->name} tarafından TALEP olarak işaretlenip onayınıza sunulmuştur.",
                        'Kalite Yöneticisi'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep bildirimi gönderilemedi (Kalite): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Bildirimi (Kalite)", $kaliteYoneticileri, $e->getMessage());
                }

                // Direktöre (Bilgi)
                if ($direktor) {
                    try {
                        $direktor->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "{$baslik} Başlıklı şikayet Bölüm Kalite Yöneticisi onayına 'Bu Bir Taleptir' istemi ile {$lider->name} tarafından sunulmuştur.",
                            'Direktör'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep bildirimi gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Bildirimi (Direktör)", $direktor, $e->getMessage());
                    }
                }
                // Şikayeti Açana
                if ($olusturan) {
                    try {
                        $olusturan->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz için {$lider->name} tarafından Talep onayı süreci başlatılmıştır.",
                            'Şikayet Sahibi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep bildirimi gönderilemedi (Sahip): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Bildirimi (Sahibi)", $olusturan, $e->getMessage());
                    }
                }
                // Müşteriye Bildir
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->yetkili_user) {
                    try {
                        $iaa->musteriSikayeti->yetkili_user->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz talep olarak değerlendirilip Kalite Yöneticisinin onayına sunulmuştur.",
                            'Müşteri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep bildirimi gönderilemedi (Müşteri): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Bildirimi (Müşteri)", $iaa->musteriSikayeti->yetkili_user, $e->getMessage());
                    }
                }
                // Superadmin'e (Sürekli Bildirim)
                try {
                    Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} için {$lider->name} tarafından TALEP süreci başlatılmıştır.",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep bildirimi gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Bildirimi (Admin)", $superadmins, $e->getMessage());
                }
                break;

            case 'rejectByQuality':
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "Proje Talep isteğiniz Kalite Yöneticisi tarafından REDDEDİLDİ.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep reddi bildirimi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Lider)", $lider, $e->getMessage());
                    }
                }
                // Şikayeti Açana
                if ($olusturan) {
                    try {
                        $olusturan->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "Talep süreci Kalite Yöneticisi tarafından reddedildi. Süreç proje olarak devam edecek.",
                            'Şikayet Sahibi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep reddi bildirimi gönderilemedi (Sahip): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Sahibi)", $olusturan, $e->getMessage());
                    }
                }
                // Superadmin'e
                try {
                    Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} talep isteği Kalite Yöneticisi tarafından REDDEDİLDİ.",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep reddi bildirimi gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Admin)", $superadmins, $e->getMessage());
                }
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
                    try {
                        $targetUser->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "{$baslik} nolu proje Kalite Yöneticisi onayıyla TALEP olarak işaretlenip son onayınıza sunulmuştur.",
                            'Direktör'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep onay bildirimi gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Onayı (Direktör)", $targetUser, $e->getMessage());
                    }
                }
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "Talep isteğiniz Kalite Yöneticisi tarafından onaylandı. Son onay için Direktöre iletildi.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep onay bildirimi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Onayı (Lider)", $lider, $e->getMessage());
                    }
                }
                // Müşteriye Bildir
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->yetkili_user) {
                    try {
                        $iaa->musteriSikayeti->yetkili_user->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz talep olarak değerlendirilip Direktör onayına sunulmuştur.",
                            'Müşteri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep onay bildirimi gönderilemedi (Müşteri): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Onayı (Müşteri)", $iaa->musteriSikayeti->yetkili_user, $e->getMessage());
                    }
                }
                // Superadmin'e
                try {
                    Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} talep isteği Kalite tarafından onaylandı ve Direktöre iletildi.",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep onay bildirimi gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Onayı (Admin)", $superadmins, $e->getMessage());
                }
                break;

            case 'approveByQuality_ToSuperadmin':
                // Superadmin'e (Zaten gidiyor ama mesajı netleştirelim)
                try {
                    Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} nolu proje Kalite Yöneticisi onayıyla TALEP olarak işaretlenip onayınıza sunulmuştur.",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep onay bildirimi gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Onayı (Admin)", $superadmins, $e->getMessage());
                }
                break;

            case 'rejectByDirector':
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "Talep isteğiniz Direktör tarafından REDDEDİLDİ.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep reddi bildirimi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Lider)", $lider, $e->getMessage());
                    }
                }
                // Kalite Yöneticisine
                try {
                    Notification::send($kaliteYoneticileri, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "Onayladığınız talep isteği Direktör tarafından REDDEDİLDİ.",
                        'Kalite Yöneticisi'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep reddi bildirimi gönderilemedi (Kalite): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Kalite)", $kaliteYoneticileri, $e->getMessage());
                }
                // Şikayeti Açana
                if ($olusturan) {
                    try {
                        $olusturan->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "Talep süreci Direktör tarafından reddedildi. Süreç devam ediyor.",
                            'Şikayet Sahibi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep reddi bildirimi gönderilemedi (Sahip): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Sahibi)", $olusturan, $e->getMessage());
                    }
                }
                // Superadmin'e
                try {
                    Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} talep isteği Direktör tarafından REDDEDİLDİ.",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep reddi bildirimi gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Admin)", $superadmins, $e->getMessage());
                }
                break;

            case 'approveByDirector_ToSuperadmin':
                // Superadmin'e (Zaten gidiyor)
                try {
                    Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "{$baslik} nolu proje Direktör onayıyla TALEP olarak işaretlenip son onayınıza sunulmuştur.",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep onay bildirimi gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Onayı (Admin)", $superadmins, $e->getMessage());
                }
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "Talep isteğiniz Direktör tarafından onaylandı. Son onay için Üst Yönetime iletildi.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep onay bildirimi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Onayı (Lider)", $lider, $e->getMessage());
                    }
                }
                break;

            case 'rejectBySuperadmin':
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "Talep isteğiniz Üst Yönetim (Superadmin) tarafından REDDEDİLDİ.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep reddi bildirimi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Lider)", $lider, $e->getMessage());
                    }
                }
                // Direktöre (varsa)
                if ($direktor) {
                    try {
                        $direktor->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "Onayladığınız talep isteği Üst Yönetim tarafından REDDEDİLDİ.",
                            'Direktör'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep reddi bildirimi gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Reddi (Direktör)", $direktor, $e->getMessage());
                    }
                }
                // Superadmin'e de bilgi (Kendi kendine işlem ama log gibi düşsün)
                /* Gerek yok zaten işlemi yapan o */
                break;

            case 'closeAsRequest':
                $successMsg = "{$baslik} dosyası TALEP (Request) olarak kabul edildi ve kapatıldı. İlgili puanlar iade alındı.";

                // Şikayeti Açana
                if ($olusturan) {
                    try {
                        $olusturan->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "{$baslik} Başlıklı şikayetiniz Müşteri Talebi olarak kabul edilmiştir. Şikayet puanınız geri alınmıştır.",
                            'Şikayet Sahibi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep kapanış bildirimi gönderilemedi (Sahip): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Kapanışı (Sahibi)", $olusturan, $e->getMessage());
                    }
                }
                
                // Müşteriye Bildir
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->yetkili_user) {
                    try {
                        $iaa->musteriSikayeti->yetkili_user->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz Müşteri Talebi olarak değerlendirilip kapatılmıştır.",
                            'Müşteri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep kapanış bildirimi gönderilemedi (Müşteri): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Kapanışı (Müşteri)", $iaa->musteriSikayeti->yetkili_user, $e->getMessage());
                    }
                }

                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            $successMsg,
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep kapanış bildirimi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Kapanışı (Lider)", $lider, $e->getMessage());
                    }
                }
                // Kalite Yöneticisine
                try {
                    Notification::send($kaliteYoneticileri, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        $successMsg,
                        'Kalite Yöneticisi'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep kapanış bildirimi gönderilemedi (Kalite): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Kapanışı (Kalite)", $kaliteYoneticileri, $e->getMessage());
                }
                // Direktöre
                if ($direktor) {
                    try {
                        $direktor->notify(new \App\Notifications\TalepBildirimi(
                            $iaa,
                            $successMsg,
                            'Direktör'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Talep kapanış bildirimi gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Kapanışı (Direktör)", $direktor, $e->getMessage());
                    }
                }
                // Superadmin'e (Diğer superadminlere de gitsin)
                try {
                    Notification::send($superadmins, new \App\Notifications\TalepBildirimi(
                        $iaa,
                        "TAMAMLANDI: {$successMsg}",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Talep kapanış bildirimi gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Talep Kapanışı (Admin)", $superadmins, $e->getMessage());
                }
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
            $iaa->update(['durum' => 'Atandı']);
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
            $iaa->update(['durum' => 'Atandı']);
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

        $iaa->update(['durum' => 'Atandı']);

        $this->notifyFaultyProcess($iaa, 'recallFaulty');

        return ['status' => 'success', 'message' => 'Hatalı bildirim talebiniz geri alındı, süreç devam ediyor.'];
    }

    /**
     * Hatalı Bildirim Onayını Geri Al (Kalite Yöneticisi)
     */
    public function recallFaultyByQuality($id)
    {
        $iaa = Iaa::findOrFail($id);
        $user = Auth::user();

        // Yetki kontrolü
        if (!$user->hasRole('Bölüm Kalite Yöneticisi') && !$user->hasRole('Superadmin')) {
            return ['status' => 'error', 'message' => 'Yetkisiz işlem.'];
        }

        // Sadece Direktör veya Superadmin onayındaysa geri alınabilir
        $beklemeDurumlari = [
            'hatali_bildirim_onayi_bekliyor_direktor',
            'hatali_bildirim_onayi_bekliyor_superadmin'
        ];

        if (!in_array($iaa->durum, $beklemeDurumlari)) {
            return ['status' => 'error', 'message' => 'Bu aşamada kalite onayını geri alma işlemi yapılamaz. Belki bir sonraki yetkili tarafından onaylanmış veya reddedilmiştir.'];
        }

        // Kendi onayladığı veya superadmin ise
        if ($iaa->hatali_bildirim_kalite_user_id != $user->id && !$user->hasRole('Superadmin')) {
            return ['status' => 'error', 'message' => 'Sadece kendi onayladığınız hatalı bildirim taleplerini geri alabilirsiniz.'];
        }

        // Durumu Kalite Onayına Bekliyor'a geri çek
        $iaa->update([
            'durum' => 'hatali_bildirim_onayi_bekliyor_kalite',
            // İsteğe bağlı olarak notu veya atama zamanını silebilir/güncelleyebiliriz
            // 'hatali_bildirim_kalite_notu' => null, 
        ]);

        // Log
        \App\Models\IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Kalite Onayı Geri Alındı (Hatalı Bildirim)',
            'aciklama' => "Kalite Yöneticisi, hatalı bildirim için verdiği onayı geri çekti."
        ]);

        // Bildirim göndermek isterseniz burada methoda case ekleyebilirsiniz 
        // $this->notifyFaultyProcess($iaa, 'recallFaultyByQuality');

        return ['status' => 'success', 'message' => 'Hatalı bildirim kalite onayı geri alındı, talep tekrar sizin ekranınıza düştü.'];
    }

    /**
     * Hatalı Bildirim Onayını Geri Al (Direktör)
     */
    public function recallFaultyByDirector($id)
    {
        $iaa = Iaa::findOrFail($id);
        $user = Auth::user();

        // Yetki kontrolü
        if (!$user->hasRole('Bölüm Direktörü') && !$user->hasRole('Superadmin')) {
            return ['status' => 'error', 'message' => 'Yetkisiz işlem.'];
        }

        if ($iaa->durum != 'hatali_bildirim_olarak_kapatildi') {
            return ['status' => 'error', 'message' => 'Sadece "Hatalı Bildirim Olarak Kapatıldı" statüsündeki kayıtlar geri alınabilir.'];
        }

        if ($iaa->hatali_bildirim_direktor_user_id != $user->id && !$user->hasRole('Superadmin')) {
            return ['status' => 'error', 'message' => 'Sadece kendi onayladığınız hatalı bildirim taleplerini geri alabilirsiniz.'];
        }

        // Puanları İade Et (GERİ AL)
        if ($iaa->musteriSikayeti) {
            $olusturan = $iaa->musteriSikayeti->olusturanKurulUyesi;
            $kazanilanPuan = $iaa->musteriSikayeti->kazanilan_puan;

            if ($olusturan && $kazanilanPuan > 0) {
                // Kapatılırken düşülen puanı GERİ YÜKLE
                $olusturan->increment('toplam_puan', $kazanilanPuan);

                \App\Models\MusteriLog::add(
                    $iaa->musteriSikayeti->customer_id,
                    'Hatalı Bildirim Geri Alma',
                    "Direktör kararı geri alındığı için {$olusturan->name} kullanıcısına {$kazanilanPuan} puan iade edildi."
                );
            }

            // Müşteri şikayeti durumunu geri aç
            $iaa->musteriSikayeti->update([
                'musteri_durum' => 'Açık',
                'kapama_nedeni' => null
            ]);
        }

        // Proje durumunu Direktör onayına geri çek
        $iaa->update([
            'durum' => 'hatali_bildirim_onayi_bekliyor_direktor',
            'hatali_bildirim_direktor_notu' => null,
            'hatali_bildirim_direktor_user_id' => null,
            'hatali_bildirim_direktor_at' => null
        ]);

        \App\Models\IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Direktör Onayı Geri Alındı (Hatalı Bildirim)',
            'aciklama' => "Direktör, hatalı bildirim için verdiği kapatma onayını geri çekti."
        ]);

        return ['status' => 'success', 'message' => 'Hatalı bildirim onayı başarıyla geri alındı, dosya yeniden açıldı.'];
    }

    /**
     * Hatalı Bildirim Onayını Geri Al (Superadmin)
     */
    public function recallFaultyBySuperadmin($id)
    {
        $iaa = Iaa::findOrFail($id);
        $user = Auth::user();

        // Yetki kontrolü
        if (!$user->hasRole('Superadmin')) {
            return ['status' => 'error', 'message' => 'Yetkisiz işlem.'];
        }

        if ($iaa->durum != 'hatali_bildirim_olarak_kapatildi') {
            return ['status' => 'error', 'message' => 'Sadece "Hatalı Bildirim Olarak Kapatıldı" statüsündeki kayıtlar geri alınabilir.'];
        }

        if ($iaa->hatali_bildirim_superadmin_user_id != $user->id && !$user->hasRole('Superadmin')) {
            return ['status' => 'error', 'message' => 'Sadece kendi onayladığınız hatalı bildirim taleplerini geri alabilirsiniz.'];
        }

        // Puanları İade Et (GERİ AL)
        if ($iaa->musteriSikayeti) {
            $olusturan = $iaa->musteriSikayeti->olusturanKurulUyesi;
            $kazanilanPuan = $iaa->musteriSikayeti->kazanilan_puan;

            if ($olusturan && $kazanilanPuan > 0) {
                // Kapatılırken düşülen puanı GERİ YÜKLE
                $olusturan->increment('toplam_puan', $kazanilanPuan);

                \App\Models\MusteriLog::add(
                    $iaa->musteriSikayeti->customer_id,
                    'Hatalı Bildirim Geri Alma',
                    "Üst Yönetim kararı geri alındığı için {$olusturan->name} kullanıcısına {$kazanilanPuan} puan iade edildi."
                );
            }

            // Müşteri şikayeti durumunu geri aç
            $iaa->musteriSikayeti->update([
                'musteri_durum' => 'Açık',
                'kapama_nedeni' => null
            ]);
        }

        // Proje durumunu Superadmin onayına geri çek
        $iaa->update([
            'durum' => 'hatali_bildirim_onayi_bekliyor_superadmin',
            'hatali_bildirim_superadmin_notu' => null,
            'hatali_bildirim_superadmin_user_id' => null,
            'hatali_bildirim_superadmin_at' => null
        ]);

        \App\Models\IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Yönetim Onayı Geri Alındı (Hatalı Bildirim)',
            'aciklama' => "Üst Yönetim, hatalı bildirim için verdiği kapatma onayını geri çekti."
        ]);

        return ['status' => 'success', 'message' => 'Hatalı bildirim onayı başarıyla geri alındı, dosya yeniden açıldı.'];
    }

    /**
     * Direktör Kararı (Hatalı Bildirim)
     */
    public function decideFaultyByDirector(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if ($request->action == 'reject') {
            $iaa->update(['durum' => 'Atandı']);
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
        // Kapanış Tarihi Hesaplama
        $tamamlanmaTarihi = now();
        if ($iaa->hatali_bildirim_direktor_at) {
            $tamamlanmaTarihi = $iaa->hatali_bildirim_direktor_at;
        } elseif ($iaa->hatali_bildirim_kalite_at) {
            $tamamlanmaTarihi = $iaa->hatali_bildirim_kalite_at;
        }

        // 1. Durumları Güncelle
        $iaa->update([
            'durum' => 'hatali_bildirim_olarak_kapatildi',
            'tamamlanma_tarihi' => $tamamlanmaTarihi
        ]);

        if ($iaa->musteriSikayeti) {
            $iaa->musteriSikayeti->update([
                'musteri_durum' => 'Kapatıldı',
                'kapama_nedeni' => 'Hatalı Bildirim: ' . $iaa->hatali_bildirim_gerekcesi,
                'musteri_onay_tarihi' => $tamamlanmaTarihi,
                'kurul_onay_tarihi' => $tamamlanmaTarihi
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

        // Bölüm Kalite Yöneticilerini Bul (Sadece bu kategoriden sorumlu olanlar)
        $kaliteYoneticileri = collect();
        if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayet_kategorisi_id) {
            $catId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
            $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                ->whereHas('yonettigiSikayetKategorileri', function($q) use ($catId) {
                    $q->where('sikayet_kategorileri.id', $catId);
                })->get();
        } elseif ($iaa->bolum_id) {
            // Eğer şikayete bağlı değilse (normal İAA), bölüme bağlı tüm yöneticilere gitsin
            $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                ->where('bolum_id', $iaa->bolum_id)
                ->get();
        }

        $superadmins = User::role('Superadmin')->get();
        $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
        $direktor = $bolum ? $bolum->director : null;

        $baslik = $iaa->baslik;

        switch ($type) {
            case 'markAsFaulty':
                // Kalite Yöneticisine
                try {
                    Notification::send($kaliteYoneticileri, new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "{$baslik} isimli şikayet {$lider->name} tarafından hatalı olarak işaretlenip onayınıza sunulmuştur.",
                        'Kalite Yöneticisi'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Hatalı bildirim gönderilemedi (Kalite): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim (Kalite)", $kaliteYoneticileri, $e->getMessage());
                }
                // Direktöre
                if ($direktor) {
                    try {
                        $direktor->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "{$baslik} Başlıklı şikayet Bölüm kalite yöneticisi onayına hatalı bildirim olarak sunulmuştur.",
                            'Direktör'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim (Direktör)", $direktor, $e->getMessage());
                    }
                }
                // Şikayeti Açana
                if ($olusturan) {
                    try {
                        $olusturan->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz {$lider->name} tarafından Hatalı Bildirim olarak işaretlendi. Onay bekleniyor.",
                            'Şikayet Sahibi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim gönderilemedi (Sahip): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim (Sahibi)", $olusturan, $e->getMessage());
                    }
                }
                // Müşteriye Bildir
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->yetkili_user) {
                    try {
                        $iaa->musteriSikayeti->yetkili_user->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz hatalı bildirim olarak değerlendirilip Kalite Yöneticisinin onayına sunulmuştur.",
                            'Müşteri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim gönderilemedi (Müşteri): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim (Müşteri)", $iaa->musteriSikayeti->yetkili_user, $e->getMessage());
                    }
                }
                break;

            case 'rejectByQuality':
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "Hatalı bildirim talebiniz Kalite Yöneticisi tarafından REDDEDİLDİ.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim reddi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Reddi (Lider)", $lider, $e->getMessage());
                    }
                }
                // Şikayeti Açana
                if ($olusturan) {
                    try {
                        $olusturan->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz için yapılan hatalı bildirim talebi REDDEDİLDİ. Süreç devam ediyor.",
                            'Şikayet Sahibi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim reddi gönderilemedi (Sahip): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Reddi (Sahibi)", $olusturan, $e->getMessage());
                    }
                }
                break;

            case 'approveByQuality_ToDirector':
                // Direktöre
                if ($targetUser) {
                    try {
                        $targetUser->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "{$baslik} nolu şikayet Kalite Yöneticisi onayıyla Hatalı Bildirim olarak işaretlenip onayınıza sunulmuştur.",
                            'Direktör'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim onay gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Onayı (Direktör)", $targetUser, $e->getMessage());
                    }
                }
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "Hatalı bildirim talebiniz Kalite Yöneticisi tarafından onaylandı. Son onay için Direktöre iletildi.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim onay gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Onayı (Lider)", $lider, $e->getMessage());
                    }
                }
                // Müşteriye Bildir
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->yetkili_user) {
                    try {
                        $iaa->musteriSikayeti->yetkili_user->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz hatalı bildirim olarak değerlendirilip Direktör onayına sunulmuştur.",
                            'Müşteri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim onay gönderilemedi (Müşteri): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Onayı (Müşteri)", $iaa->musteriSikayeti->yetkili_user, $e->getMessage());
                    }
                }
                break;

            case 'approveByQuality_ToSuperadmin':
                // Superadmin'lere
                try {
                    Notification::send($superadmins, new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "{$baslik} nolu şikayet Kalite Yöneticisi onayıyla Hatalı Bildirim olarak işaretlenip onayınıza sunulmuştur.",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Hatalı bildirim onay gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Onayı (Admin)", $superadmins, $e->getMessage());
                }
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "Hatalı bildirim talebiniz Kalite Yöneticisi tarafından onaylandı. Son onay için Üst Yönetime iletildi.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim onay gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Onayı (Lider)", $lider, $e->getMessage());
                    }
                }
                break;

            case 'rejectByDirector':
            case 'rejectBySuperadmin':
                $rolAd = ($type == 'rejectByDirector') ? 'Direktör' : 'Superadmin';
                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "Hatalı bildirim talebiniz {$rolAd} tarafından REDDEDİLDİ.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim reddi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Reddi (Lider)", $lider, $e->getMessage());
                    }
                }
                // Kalite Yöneticisine (Bilgi)
                try {
                    Notification::send($kaliteYoneticileri, new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "Onayladığınız hatalı bildirim {$rolAd} tarafından REDDEDİLDİ.",
                        'Kalite Yöneticisi'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Hatalı bildirim reddi gönderilemedi (Kalite): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Reddi (Kalite)", $kaliteYoneticileri, $e->getMessage());
                }
                break;

            case 'recallFaulty':
                $recallMsg = "{$baslik} şikayeti için yapılan Hatalı Bildirim talebi {$lider->name} tarafından geri çekildi.";
                // Kalite Yöneticisine
                try {
                    Notification::send($kaliteYoneticileri, new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        $recallMsg,
                        'Kalite Yöneticisi'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Hatalı bildirim geri çekme gönderilemedi (Kalite): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Geri Çekme (Kalite)", $kaliteYoneticileri, $e->getMessage());
                }
                // Direktöre
                if ($direktor) {
                    try {
                        $direktor->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            $recallMsg,
                            'Direktör'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim geri çekme gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Geri Çekme (Direktör)", $direktor, $e->getMessage());
                    }
                }
                break;

            case 'closeAsFaulty':
                // Herkese Bildir
                $successMsg = "{$baslik} dosyası Hatalı Bildirim olarak kesinleşti ve kapatıldı. Puanlar iade edildi.";

                // Şikayeti Açana
                if ($olusturan) {
                    try {
                        $olusturan->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "{$baslik} Başlıklı şikayetiniz Hatalı Bildirim olarak karara bağlanmıştır. Puanlarınız geri alınmıştır.",
                            'Şikayet Sahibi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim kapanış gönderilemedi (Sahip): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Kapanışı (Sahibi)", $olusturan, $e->getMessage());
                    }
                }
                
                // Müşteriye Bildir
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->yetkili_user) {
                    try {
                        $iaa->musteriSikayeti->yetkili_user->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            "{$baslik} başlıklı şikayetiniz hatalı bildirim olarak değerlendirilip kapatılmıştır.",
                            'Müşteri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim kapanış gönderilemedi (Müşteri): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Kapanışı (Müşteri)", $iaa->musteriSikayeti->yetkili_user, $e->getMessage());
                    }
                }

                // Lidere
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            $successMsg,
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim kapanış gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Kapanışı (Lider)", $lider, $e->getMessage());
                    }
                }
                // Kalite Yöneticisine
                try {
                    Notification::send($kaliteYoneticileri, new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        $successMsg,
                        'Kalite Yöneticisi'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Hatalı bildirim kapanış gönderilemedi (Kalite): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Kapanışı (Kalite)", $kaliteYoneticileri, $e->getMessage());
                }
                // Direktöre
                if ($direktor) {
                    try {
                        $direktor->notify(new \App\Notifications\HataliBildirimBildirimi(
                            $iaa,
                            $successMsg,
                            'Direktör'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Hatalı bildirim kapanış gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Kapanışı (Direktör)", $direktor, $e->getMessage());
                    }
                }
                // Superadmin'e
                try {
                    Notification::send($superadmins, new \App\Notifications\HataliBildirimBildirimi(
                        $iaa,
                        "TAMAMLANDI: {$successMsg}",
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Hatalı bildirim kapanış gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Hatalı Bildirim Kapanışı (Admin)", $superadmins, $e->getMessage());
                }
                break;
        }
    }

    // --- EK SÜRE TALEBİ YÖNETİMİ ---

    public function requestExtension(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if (!$iaa->musteriSikayeti) {
            throw new \Exception('Bu proje bir müşteri şikayetine bağlı değil.');
        }

        $request->validate([
            'gun_sayisi' => 'required|integer|min:1|max:30',
            'aciklama' => 'required|string|max:500'
        ]);

        $gunSayisi = $request->gun_sayisi;
        $aciklamaText = $request->aciklama;

        // Ek süre bilgileri musteri_sikayetleri tablosuna eklenmekte
        $iaa->musteriSikayeti->update([
            'musteri_ek_sure_talep_durumu' => 'Talep Edildi',
            'ek_sure_talep_aciklamasi' => "{$gunSayisi} Gün talep edildi. Gerekçe: {$aciklamaText}",
        ]);

        // Log Kaydı
        \App\Models\IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Ek Süre Talebi',
            'aciklama' => "Proje lideri {$gunSayisi} gün ek süre talep etti. Gerekçe: {$aciklamaText}"
        ]);

        $this->notifyExtensionProcess($iaa, 'requestExtension');
    }

    public function decideExtensionByDirector(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if (!$iaa->musteriSikayeti) {
            throw new \Exception('Bu proje bir müşteri şikayetine bağlı değil.');
        }

        if ($request->action === 'reject') {
            $redNedeni = $request->input('ek_sure_red_nedeni', 'Belirtilmedi.');

            $iaa->musteriSikayeti->update([
                'musteri_ek_sure_talep_durumu' => 'Reddedildi',
                'ek_sure_red_nedeni' => $redNedeni
            ]);

            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Ek Süre Reddi (Direktör)',
                'aciklama' => "Ek süre talebi Direktör tarafından REDDEDİLDİ. Gerekçe: {$redNedeni}"
            ]);

            $this->notifyExtensionProcess($iaa, 'rejectByDirector');

            return ['status' => 'error', 'message' => 'Ek süre talebi reddedildi.'];
        }

        if ($request->action === 'approve') {
            // Açıklamadan gün sayısını çıkaralım (örn: "5 Gün talep edildi...")
            $aciklama = $iaa->musteriSikayeti->ek_sure_talep_aciklamasi;
            preg_match('/(\d+)\s+Gün/i', $aciklama, $matches);
            $gunSayisi = isset($matches[1]) ? (int) $matches[1] : 0;

            if ($gunSayisi > 0) {
                // Mevcut tarihe gün ekle
                $mevcutTarih = $iaa->musteriSikayeti->musteri_cozum_son_tarihi
                    ? \Carbon\Carbon::parse($iaa->musteriSikayeti->musteri_cozum_son_tarihi)
                    : now();

                $yeniTarih = $mevcutTarih->addDays($gunSayisi);

                $iaa->musteriSikayeti->update([
                    'musteri_ek_sure_talep_durumu' => 'Onaylandı',
                    'musteri_cozum_son_tarihi' => $yeniTarih
                ]);

                // Ayrıca proje atamasının da bitiş tarihini güncelliyoruz
                $assignment = \App\Models\IaaTalep::where('iaa_id', $iaa->id)->latest()->first();
                if ($assignment) {
                    $assignment->update([
                        'due_date' => $yeniTarih
                    ]);
                }

                \App\Models\IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Ek Süre Onayı (Direktör)',
                    'aciklama' => "Ek süre talebi Direktör tarafından ONAYLANDI. Son tarih {$yeniTarih->format('d.m.Y H:i')} olarak güncellendi."
                ]);

                $this->notifyExtensionProcess($iaa, 'approveByDirector');

                return ['status' => 'success', 'message' => 'Ek süre talebi onaylandı ve son çözüm tarihi güncellendi.'];
            }
            return ['status' => 'error', 'message' => 'Gün sayısı tespit edilemedi.'];
        }
    }

    public function decideExtensionBySuperadmin(Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if (!$iaa->musteriSikayeti) {
            throw new \Exception('Bu proje bir müşteri şikayetine bağlı değil.');
        }

        if ($request->action === 'reject') {
            $redNedeni = $request->input('ek_sure_red_nedeni', 'Belirtilmedi.');

            $iaa->musteriSikayeti->update([
                'musteri_ek_sure_talep_durumu' => 'Reddedildi',
                'ek_sure_red_nedeni' => $redNedeni
            ]);

            \App\Models\IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Ek Süre Reddi (Superadmin)',
                'aciklama' => "Ek süre talebi Superadmin tarafından REDDEDİLDİ. Gerekçe: {$redNedeni}"
            ]);

            $this->notifyExtensionProcess($iaa, 'rejectBySuperadmin');

            return ['status' => 'error', 'message' => 'Ek süre talebi reddedildi.'];
        }

        if ($request->action === 'approve') {
            // Açıklamadan gün sayısını çıkaralım (örn: "5 Gün talep edildi...")
            $aciklama = $iaa->musteriSikayeti->ek_sure_talep_aciklamasi;
            preg_match('/(\d+)\s+Gün/i', $aciklama, $matches);
            $gunSayisi = isset($matches[1]) ? (int) $matches[1] : 0;

            if ($gunSayisi > 0) {
                // Mevcut tarihe gün ekle
                $mevcutTarih = $iaa->musteriSikayeti->musteri_cozum_son_tarihi
                    ? \Carbon\Carbon::parse($iaa->musteriSikayeti->musteri_cozum_son_tarihi)
                    : now();

                $yeniTarih = $mevcutTarih->addDays($gunSayisi);

                $iaa->musteriSikayeti->update([
                    'musteri_ek_sure_talep_durumu' => 'Onaylandı',
                    'musteri_cozum_son_tarihi' => $yeniTarih
                ]);

                // Ayrıca proje atamasının da bitiş tarihini güncelliyoruz
                $assignment = \App\Models\IaaTalep::where('iaa_id', $iaa->id)->latest()->first();
                if ($assignment) {
                    $assignment->update([
                        'due_date' => $yeniTarih
                    ]);
                }

                \App\Models\IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Ek Süre Onayı (Superadmin)',
                    'aciklama' => "Ek süre talebi Superadmin tarafından ONAYLANDI. Son tarih {$yeniTarih->format('d.m.Y H:i')} olarak güncellendi."
                ]);

                $this->notifyExtensionProcess($iaa, 'approveBySuperadmin');

                return ['status' => 'success', 'message' => 'Ek süre talebi onaylandı ve son çözüm tarihi güncellendi.'];
            }
            return ['status' => 'error', 'message' => 'Gün sayısı tespit edilemedi.'];
        }
    }

    private function notifyExtensionProcess(Iaa $iaa, $type)
    {
        $lider = $iaa->atananTakim ? $iaa->atananTakim->lider : null;
        $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum ?? null;
        $direktor = $bolum ? $bolum->director : null;
        $superadmins = User::role('Superadmin')->get();

        // System Settings
        $direktorOnayiSetting = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->value('value');
        $direktorOnayiAktif = filter_var($direktorOnayiSetting, FILTER_VALIDATE_BOOLEAN);

        $kategori = $iaa->musteriSikayeti->sikayetKategori ?? null;
        $kaliteYoneticileri = [];
        if ($kategori) {
            $kaliteYoneticileri = User::role('Bölüm Kalite Yöneticisi')
                ->whereHas('yonettigiSikayetKategorileri', function ($q) use ($kategori) {
                    $q->where('sikayet_kategori_id', $kategori->id);
                })
                ->get();
        }

        $baslik = $iaa->baslik;
        $bolumAd = $bolum ? $bolum->name : 'Belirsiz';
        $liderName = $lider ? $lider->name : 'Belirsiz';

        switch ($type) {
            case 'requestExtension':
                // 1. Lidere Bilgi
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\EkSureTalebiBildirimi(
                            $iaa,
                            "{$baslik} projesi için Ek Süre Talebiniz yöneticiye başarıyla iletilmiştir.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Ek süre talebi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Talebi (Lider)", $lider, $e->getMessage());
                    }
                }

                // 2. Kalite Yöneticisine Bilgi
                $kaliteMsg = "{$baslik} başlıklı şikayet size bağlı {$bolumAd} bölümün Müşteri Şikayeti Çözüm takımı lideri {$liderName} tarafından Ek Süre Talebi ile yöneticiye gönderilmiştir.";
                try {
                    Notification::send($kaliteYoneticileri, new \App\Notifications\EkSureTalebiBildirimi(
                        $iaa,
                        $kaliteMsg,
                        'Kalite Yöneticisi'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Ek süre talebi gönderilemedi (Kalite): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Talebi (Kalite)", $kaliteYoneticileri, $e->getMessage());
                }

                // 3. Direktöre Bilgi veya Onay Talebi (Ayar durumuna göre)
                if ($direktor) {
                    try {
                        if ($direktorOnayiAktif) {
                            $direktor->notify(new \App\Notifications\EkSureTalebiBildirimi(
                                $iaa,
                                "{$baslik} projesi için Takım Lideri {$liderName} ek süre talep etmiştir. Onayınız bekleniyor.",
                                'Direktör'
                            ));
                        } else {
                            // Eğer ayar kapalıysa, Direktör onay vermek zorunda değil ancak bilgilendirme almalı
                            $direktor->notify(new \App\Notifications\EkSureTalebiBildirimi(
                                $iaa,
                                "{$baslik} projesi için Takım Lideri {$liderName} ek süre talep etmiştir. Onay süreci yönetici (Superadmin) tarafından yürütülmektedir.",
                                'Direktör'
                            ));
                        }
                    } catch (\Exception $e) {
                        \Log::error('Ek süre talebi gönderilemedi (Direktör): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Talebi (Direktör)", $direktor, $e->getMessage());
                    }
                }

                // 4. Superadmine Bilgi/Onay Talebi (Her zaman bildir)
                try {
                    Notification::send($superadmins, new \App\Notifications\EkSureTalebiBildirimi(
                        $iaa,
                        "{$baslik} projesi için Takım Lideri {$liderName} ek süre talep etmiştir." . ($direktorOnayiAktif && $direktor ? ' (Direktör onayı da bekleniyor)' : ' Onayınız bekleniyor.'),
                        'Superadmin'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Ek süre talebi gönderilemedi (Admin): ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Talebi (Admin)", $superadmins, $e->getMessage());
                }
                break;
            case 'approveByDirector':
            case 'approveBySuperadmin':
                $rolAd = ($type == 'approveByDirector') ? 'Direktör' : 'Superadmin';
                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\EkSureTalebiBildirimi(
                            $iaa,
                            "{$baslik} projesi için yaptığınız Ek Süre Talebi {$rolAd} tarafından ONAYLANDI. Son teslim tarihiniz uzatıldı.",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Ek süre onay gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Onayı (Lider)", $lider, $e->getMessage());
                    }
                }
                if ($kaliteYoneticileri && count($kaliteYoneticileri) > 0) {
                    try {
                        Notification::send($kaliteYoneticileri, new \App\Notifications\EkSureTalebiBildirimi(
                            $iaa,
                            "{$baslik} projesi için Ek Süre Talebi {$rolAd} tarafından ONAYLANDI. Son teslim tarihi uzatıldı.",
                            'Kalite Yöneticisi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Ek süre onay gönderilemedi (Kalite): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Onayı (Kalite)", $kaliteYoneticileri, $e->getMessage());
                    }
                }
                if ($type == 'approveByDirector') {
                    try {
                        Notification::send($superadmins, new \App\Notifications\EkSureTalebiBildirimi(
                            $iaa,
                            "{$baslik} projesi için ek süre talebi Direktör tarafından onaylanmıştır.",
                            'Superadmin'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Ek süre onay gönderilemedi (Admin): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Onayı (Admin)", $superadmins, $e->getMessage());
                    }
                }
                break;
            case 'rejectByDirector':
            case 'rejectBySuperadmin':
                $rolAd = ($type == 'rejectByDirector') ? 'Direktör' : 'Superadmin';
                $redNedeniText = $iaa->musteriSikayeti->ek_sure_red_nedeni ?? 'Gerekçe belirtilmedi.';

                if ($lider) {
                    try {
                        $lider->notify(new \App\Notifications\EkSureTalebiBildirimi(
                            $iaa,
                            "{$baslik} projesi için yaptığınız Ek Süre Talebi {$rolAd} tarafından REDDEDİLDİ. Gerekçe: {$redNedeniText}",
                            'Takım Lideri'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Ek süre reddi gönderilemedi (Lider): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Reddi (Lider)", $lider, $e->getMessage());
                    }
                }
                if ($kaliteYoneticileri && count($kaliteYoneticileri) > 0) {
                    try {
                        Notification::send($kaliteYoneticileri, new \App\Notifications\EkSureTalebiBildirimi(
                            $iaa,
                            "{$baslik} projesi için Ek Süre Talebi {$rolAd} tarafından REDDEDİLDİ.",
                            'Kalite Yöneticisi'
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Ek süre reddi gönderilemedi (Kalite): ' . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure($iaa, "Ek Süre Reddi (Kalite)", $kaliteYoneticileri, $e->getMessage());
                    }
                }
                break;
        }
    }
}