<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KullaniciIstegiSonucNotification extends Notification
{
    public $istek;

    /**
     * Create a new notification instance.
     */
    public function __construct($istek)
    {
        $this->istek = $istek;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $turText = $this->istek->talep_turu == 'isim_degisikligi' ? 'İsim Değişikliği' : 'Bölüm Değişikliği';
        $durumText = $this->istek->durum == 'onaylandi' ? 'onaylandı' : 'reddedildi';
        
        $message = "{$turText} talebiniz {$durumText}.";

        return [
            'type' => 'kullanici_istegi_sonucu',
            'istek_id' => $this->istek->id,
            'message' => $message,
            'action_url' => route('profile.edit', ['tab' => 'settings'])
        ];
    }
}
