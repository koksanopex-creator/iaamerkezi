<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Notification;

trait NotifiesManager
{
    /**
     * İşlem yapılan kullanıcıya bildirim gönderirken, 
     * o kullanıcının Bölüm Liderine de (CC) gönderir.
     * * @param User $targetUser İşlem yapılan personel (Örn: Furkan)
     * @param mixed $notificationInstance Gönderilecek Bildirim Sınıfı
     */
    public function notifyUserAndManager(User $targetUser, $notificationInstance)
    {
        // 1. Asıl personele gönder
        $targetUser->notify($notificationInstance);

        // 2. Personelin bölümü var mı kontrol et
        if ($targetUser->bolum_id) {
            
            // 3. O bölümün "Bölüm Lideri" rolündeki yöneticilerini bul
            $managers = User::role('Bölüm Lideri')
                        ->where('bolum_id', $targetUser->bolum_id)
                        ->where('id', '!=', $targetUser->id) // Müdür kendine işlem yapıyorsa tekrar gitmesin
                        ->get();

            // 4. Müdürlere de gönder
            if ($managers->isNotEmpty()) {
                Notification::send($managers, $notificationInstance);
            }
        }
    }
}