<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KullaniciIstegiNotification extends Notification
{
    use Queueable;

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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $turText = $this->istek->talep_turu == 'isim_degisikligi' ? 'İsim Değişikliği' : 'Bölüm Değişikliği';
        $userName = $this->istek->user->name ?? 'Bir kullanıcı';
        
        return [
            'type' => 'kullanici_istegi',
            'istek_id' => $this->istek->id,
            'message' => "{$userName}, yeni bir {$turText} talebinde bulundu.",
            'action_url' => route('admin.istekler.index')
        ];
    }
}
