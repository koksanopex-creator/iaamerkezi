<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

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

            // 3. O bölümün "Bölüm Lideri" VE "Bildirim Yetkili Yardımcılarını" bul
            $managers = User::where('bolum_id', $targetUser->bolum_id)
                ->where('id', '!=', $targetUser->id)
                ->where(function($q) {
                    $q->role('Bölüm Lideri')
                      ->orWhere(function($sq) {
                          $sq->role('Bölüm Lider Yardımcısı')
                            ->permission('bolum.personel.bildirim');
                      });
                })
                ->get();

            // 4. Müdür ve Yetkili Yardımcılara gönder
            if ($managers->isNotEmpty()) {
                Notification::send($managers, $notificationInstance);
            }
        }
    }

    /**
     * Sadece Bölüm Liderine bildirim gönderir.
     */
    public function notifyDepartmentLeader(User $targetUser, $notificationInstance)
    {
        if ($targetUser->bolum_id) {
            $managers = User::role('Bölüm Lideri')
                ->where('bolum_id', $targetUser->bolum_id)
                ->where('id', '!=', Auth::id()) // İşlemi yapan zaten müdürse ona gitmesin
                ->get();

            if ($managers->isNotEmpty()) {
                Notification::send($managers, $notificationInstance);
            }
        }
    }
}