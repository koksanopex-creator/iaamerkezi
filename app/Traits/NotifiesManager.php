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
        try {
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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('NotifiesManager trait bildirimi gönderilemedi: ' . $e->getMessage());
            
            // Model bilgisini bildirim instance'ından çekmeye çalış (eğer varsa)
            $model = property_exists($notificationInstance, 'iaa') ? $notificationInstance->iaa : (property_exists($notificationInstance, 'sikayet') ? $notificationInstance->sikayet : null);
            
            \App\Helpers\MailLogHelper::logFailure(
                $model,
                'Yönetici/Kullanıcı Bildirimi (NotifiesManager)',
                collect([$targetUser])->merge($managers ?? collect()),
                $e->getMessage(),
                get_class($notificationInstance),
                [
                    'recipient_ids' => collect([$targetUser->id])->merge($managers ? $managers->pluck('id') : collect())->unique()->toArray(),
                    // Params için instance içindeki verileri serialize etmek zor olabilir, 
                    // Ancak çoğu bildirimimiz model bazlı.
                    'params' => property_exists($notificationInstance, 'iaa') ? ['iaa' => $notificationInstance->iaa] : (property_exists($notificationInstance, 'sikayet') ? ['sikayet' => $notificationInstance->sikayet] : [])
                ],
                $targetUser->bolum_id
            );
        }
    }

    /**
     * Sadece Bölüm Liderine bildirim gönderir.
     */
    public function notifyDepartmentLeader(User $targetUser, $notificationInstance)
    {
        try {
            if ($targetUser->bolum_id) {
                $managers = User::role('Bölüm Lideri')
                    ->where('bolum_id', $targetUser->bolum_id)
                    ->where('id', '!=', Auth::id()) // İşlemi yapan zaten müdürse ona gitmesin
                    ->get();

                if ($managers->isNotEmpty()) {
                    Notification::send($managers, $notificationInstance);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('NotifiesManager department leader bildirimi gönderilemedi: ' . $e->getMessage());
            
            $model = property_exists($notificationInstance, 'iaa') ? $notificationInstance->iaa : (property_exists($notificationInstance, 'sikayet') ? $notificationInstance->sikayet : null);

            \App\Helpers\MailLogHelper::logFailure(
                $model,
                'Bölüm Lideri Bildirimi (NotifiesManager)',
                $managers ?? collect(),
                $e->getMessage(),
                get_class($notificationInstance),
                [
                    'recipient_ids' => ($managers ?? collect())->pluck('id')->toArray(),
                    'params' => property_exists($notificationInstance, 'iaa') ? ['iaa' => $notificationInstance->iaa] : (property_exists($notificationInstance, 'sikayet') ? ['sikayet' => $notificationInstance->sikayet] : [])
                ],
                $targetUser->bolum_id
            );
        }
    }
}