<?php

namespace App\Traits;

use App\Models\Setting;
use App\Models\User;
use App\Models\SikayetKategori;
use App\Models\MusteriSikayeti;
use App\Notifications\YeniMusteriSikayetiBildirimi;
use Illuminate\Support\Facades\Notification;

trait ComplaintNotificationTrait
{
    /**
     * Şikayet eklendiğinde ilgili kişilere (ve Bölüm Liderine) haber verir.
     */
    public function sendNewComplaintNotification(MusteriSikayeti $sikayet)
    {
        // 1. Alıcı Listesini ve Snapshot Verisini Hazırla
        $settings = Setting::all()->keyBy('key');
        $recipients = collect();
        $snapshot = [];

        // Yardımcı fonksiyon: Snapshot'a kullanıcı ekle
        $addToSnapshot = function ($user, $roleLabel) use (&$snapshot) {
            $snapshot[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->telefon ?? $user->phone ?? null,
                'photo' => $user->profile_photo_path,
                'role_label' => $roleLabel,
                'notified_at' => now()->toDateTimeString(),
            ];
        };

        // 2. Rollerden Gelenler (Superadmin vb.)
        $roleIdsValue = $settings->get('sikayet_notify_role_ids')?->value;
        if (!empty($roleIdsValue)) {
            $roleIds = explode(',', $roleIdsValue);
            $usersFromRoles = User::whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('id', $roleIds);
            })->get();
            foreach ($usersFromRoles as $u) {
                $recipients->push($u);
                $addToSnapshot($u, 'Sistem Yöneticisi');
            }
        }

        // 3. Özel Seçilmiş Kullanıcılar
        $userIdsValue = $settings->get('sikayet_notify_user_ids')?->value;
        if (!empty($userIdsValue)) {
            $userIds = explode(',', $userIdsValue);
            $usersFromIds = User::whereIn('id', $userIds)->get();
            foreach ($usersFromIds as $u) {
                $recipients->push($u);
                $addToSnapshot($u, 'Bilgilendirilen');
            }
        }

        // 4. KATEGORİ -> BÖLÜM -> BÖLÜM LİDERİ, KALİTE, DİREKTÖR
        if ($sikayet->sikayet_kategorisi_id) {
            $kategori = SikayetKategori::find($sikayet->sikayet_kategorisi_id);
            
            if ($kategori && $kategori->bolum_id) {
                // Bölüm Liderlerini Bul
                $bolumLiderleri = User::role('Bölüm Lideri')
                                      ->where('bolum_id', $kategori->bolum_id)
                                      ->get();
                foreach ($bolumLiderleri as $u) {
                    $recipients->push($u);
                    $addToSnapshot($u, 'Bölüm Lideri');
                }

                // Bölüm Lider Yardımcılarını (Matristen yetki alanları) Bul
                $bolumYardimcilari = User::permission('bolum.sikayet.bildirim')
                                      ->where('bolum_id', $kategori->bolum_id)
                                      ->get();
                foreach ($bolumYardimcilari as $u) {
                    if (!$recipients->contains('id', $u->id)) {
                        $recipients->push($u);
                        $addToSnapshot($u, 'Bölüm Lider Yardımcısı (Bildirim Yetkili)');
                    }
                }

                // Direktörü Bul
                $bolum = $kategori->bolum;
                if ($bolum && $bolum->director_id) {
                    $direktor = User::find($bolum->director_id);
                    if ($direktor) {
                        $recipients->push($direktor);
                        $addToSnapshot($direktor, 'Bölüm Direktörü');
                    }
                }

                // Kalite Yöneticilerini Bul
                $kaliteYoneticileri = User::whereHas('yonettigiSikayetKategorileri', function($q) use ($sikayet) {
                    $q->where('sikayet_kategori_id', $sikayet->sikayet_kategorisi_id);
                })->get();
                foreach ($kaliteYoneticileri as $u) {
                    $recipients->push($u);
                    $addToSnapshot($u, 'Bölüm Kalite Yöneticisi');
                }
            }
        }

        // 5. Müşteri Şikayeti Kurulu Üyeleri
        $kurulUyeleri = User::role('Müşteri Şikayeti Kurulu')->get();
        foreach ($kurulUyeleri as $u) {
            $recipients->push($u);
            $addToSnapshot($u, 'Müşteri Şikayeti Kurulu Üyesi');
        }

        // 5.5. Müşteri Saha Temsilcileri
        if ($sikayet->sikayet_kategorisi_id) {
            $kategori = SikayetKategori::find($sikayet->sikayet_kategorisi_id);
            if ($kategori && $kategori->bolum_id) {
                $sahaTemsilcileri = User::role('Müşteri Saha Temsilcisi')->get();
                foreach ($sahaTemsilcileri as $u) {
                    if (in_array($kategori->bolum_id, $u->getAllowedBolumIds())) {
                        if (!$recipients->contains('id', $u->id)) {
                            $recipients->push($u);
                        }
                        $addToSnapshot($u, 'Müşteri Saha Temsilcisi');
                    }
                }
            }
        }

        // 6. Müşteri Temsilcileri (Bu firmaya bağlı TÜM aktif yetkililer)
        if ($sikayet->customer_id && $sikayet->customer) {
            $musteriTemsilcileri = $sikayet->customer->users()
                ->wherePivot('is_active', true)
                ->get();

            foreach ($musteriTemsilcileri as $u) {
                if (!$recipients->contains('id', $u->id)) {
                    $recipients->push($u);
                }
                $addToSnapshot($u, 'Müşteri Temsilcisi');
            }
        }

        // 7. Spesifik Atanmış Ek Yetkililer (Pivot Tablo)
        foreach ($sikayet->ekYetkililer as $u) {
            if (!$recipients->contains('id', $u->id)) {
                $recipients->push($u);
            }
            $addToSnapshot($u, 'Ek İlgili');
        }

        // Snapshot'ı Tekilleştir (Kullanıcı ID bazlı en son rolü koruyarak)
        $uniqueSnapshot = collect($snapshot)->keyBy('user_id')->values()->toArray();

        // Veritabanına kaydet
        $sikayet->update([
            'notified_snapshot' => json_encode($uniqueSnapshot)
        ]);

        // Bildirimi Göndereni Çıkar (Kendisine bildirim gitmesin)
        $finalRecipients = $recipients->unique('id')->reject(function ($user) use ($sikayet) {
            $currentUserId = auth()->id();
            return $user->id == $currentUserId || $user->id == $sikayet->olusturan_kurul_uyesi_id;
        });

        if ($finalRecipients->isNotEmpty()) {
            try {
                Notification::send($finalRecipients, new YeniMusteriSikayetiBildirimi($sikayet));
            } catch (\Exception $e) {
                \Log::error('Yeni şikayet toplu bildirimi gönderilemedi: ' . $e->getMessage());
                \App\Helpers\MailLogHelper::logFailure(
                    $sikayet, 
                    "Yeni Şikayet Bildirimi (Toplu)", 
                    $finalRecipients, 
                    $e->getMessage(),
                    \App\Notifications\YeniMusteriSikayetiBildirimi::class,
                    [
                        'recipient_ids' => $finalRecipients->pluck('id')->toArray(),
                        'params' => ['sikayet' => $sikayet]
                    ],
                    $sikayet->sikayetKategori->bolum_id ?? null
                );
            }
        }

        // Manuel E-postalar (Sadece Mail)
        $manualEmailsValue = $settings->get('sikayet_notify_manual_emails')?->value;
        if (!empty($manualEmailsValue)) {
            $manualEmails = explode(',', $manualEmailsValue);
            foreach ($manualEmails as $email) {
                $email = trim($email);
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        Notification::route('mail', $email)
                            ->notify(new YeniMusteriSikayetiBildirimi($sikayet));
                    } catch (\Exception $e) {
                        \Log::error("Yeni şikayet manuel bildirim gönderilemedi ({$email}): " . $e->getMessage());
                        \App\Helpers\MailLogHelper::logFailure(
                            $sikayet, 
                            "Yeni Şikayet Bildirimi (Manuel)", 
                            $email, 
                            $e->getMessage(),
                            \App\Notifications\YeniMusteriSikayetiBildirimi::class,
                            [
                                'recipient_ids' => [], // Route notification olduğu için ID yok
                                'params' => ['sikayet' => $sikayet]
                            ],
                            $sikayet->sikayetKategori->bolum_id ?? null
                        );
                    }
                }
            }
        }
    }
}