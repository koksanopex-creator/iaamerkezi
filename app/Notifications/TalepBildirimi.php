<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TalepBildirimi extends Notification
{
    use Queueable;

    private $iaa;
    private $message;
    private $role;

    /**
     * Create a new notification instance.
     */
    public function __construct($iaa, $message, $role)
    {
        $this->iaa = $iaa;
        $this->message = $message;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Proje Talep Bildirimi: ' . $this->iaa->baslik)
            ->greeting('Sayın ' . $notifiable->name . ',')
            ->line($this->message)
            ->action('Projeyi Görüntüle', route('iaa.show', $this->iaa->id))
            ->line('Bilginize sunarız.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'iaa_id' => $this->iaa->id,
            'message' => $this->message,
            'role' => $this->role,
            'type' => 'talep_bildirimi',
            'url' => route('iaa.show', $this->iaa->id), // Frontend için gerekli
            'action_url' => route('iaa.show', $this->iaa->id) // Yedek olarak
        ];
    }
}
