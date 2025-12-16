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
        // 1. Ayarları ve Alıcı Listesini Hazırla
        $settings = Setting::all()->keyBy('key');
        $recipients = collect();

        // 2. Rollerden Gelenler (Superadmin, Çözüm Lideri vb.)
        $roleIdsValue = $settings->get('sikayet_notify_role_ids')?->value;
        if (!empty($roleIdsValue)) {
            $roleIds = explode(',', $roleIdsValue);
            $usersFromRoles = User::whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('id', $roleIds);
            })->get();
            $recipients = $recipients->merge($usersFromRoles);
        }

        // 3. Özel Seçilmiş Kullanıcılar
        $userIdsValue = $settings->get('sikayet_notify_user_ids')?->value;
        if (!empty($userIdsValue)) {
            $userIds = explode(',', $userIdsValue);
            $usersFromIds = User::whereIn('id', $userIds)->get();
            $recipients = $recipients->merge($usersFromIds);
        }

        // ==========================================================
        // 4. KRİTİK KISIM: KATEGORİ -> BÖLÜM -> BÖLÜM LİDERİ
        // ==========================================================
        if ($sikayet->sikayet_kategorisi_id) {
            $kategori = SikayetKategori::find($sikayet->sikayet_kategorisi_id);
            
            if ($kategori && $kategori->bolum_id) {
                // Bölüm Liderlerini Bul
                $bolumLiderleri = User::role('Bölüm Lideri')
                                      ->where('bolum_id', $kategori->bolum_id)
                                      ->get();
                
                $recipients = $recipients->merge($bolumLiderleri);
            }
        }
        // ==========================================================

        // Tekilleştir ve Gönder (Zil + Mail)
        $finalRecipients = $recipients->unique('id');

        if ($finalRecipients->isNotEmpty()) {
            Notification::send($finalRecipients, new YeniMusteriSikayetiBildirimi($sikayet));
        }

        // 5. Manuel E-postalar (Sadece Mail)
        $manualEmailsValue = $settings->get('sikayet_notify_manual_emails')?->value;
        if (!empty($manualEmailsValue)) {
            $manualEmails = explode(',', $manualEmailsValue);
            foreach ($manualEmails as $email) {
                if(filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    Notification::route('mail', trim($email))
                        ->notify(new YeniMusteriSikayetiBildirimi($sikayet));
                }
            }
        }
    }
}