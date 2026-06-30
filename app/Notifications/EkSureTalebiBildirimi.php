<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EkSureTalebiBildirimi extends Notification
{
    use Queueable;

    public $iaa;
    public $mesaj;
    public $rol;

    /**
     * Create a new notification instance.
     */
    public function __construct($iaa, $mesaj, $rol = null)
    {
        $this->iaa = $iaa;
        $this->mesaj = $mesaj;
        $this->rol = $rol;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ek Süre Talebi Bildirimi - ' . $this->iaa->baslik)
            ->greeting('Merhaba ' . ($this->rol ? $this->rol . ', ' : ''))
            ->line($this->mesaj)
            ->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->mesaj,
            'link' => route('proje.workspace.show', $this->iaa->id),
            'iaa_id' => $this->iaa->id,
            'baslik' => $this->iaa->baslik,
            'tip' => 'ek_sure_talebi',
            'rol' => $this->rol
        ];
    }
}
