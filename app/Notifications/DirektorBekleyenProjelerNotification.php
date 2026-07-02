<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DirektorBekleyenProjelerNotification extends Notification
{
    use Queueable;

    protected $projectTitles;
    protected $count;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $projectTitles)
    {
        $this->projectTitles = $projectTitles;
        $this->count = count($projectTitles);
    }

    /**
     * Get the notification's delivery channels.
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
        $titlesStr = implode(', ', $this->projectTitles);
        $message = "Onayınızı bekleyen müşteri şikayeti projeleri: {$titlesStr} dir.";

        return (new MailMessage)
            ->subject('Onayınızı Bekleyen Projeler')
            ->greeting('Sayın ' . $notifiable->name . ',')
            ->line('Müşteri Şikayeti Projeleri Direktör Onay Süreci aktif edilmiştir.')
            ->line($message)
            ->action('Paneli Görüntüle', route('dashboard'))
            ->line('Bilginize sunarız.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $titlesStr = implode(', ', $this->projectTitles);
        $message = "**Direktör Onay Süreci**; Onayınızı bekleyen müşteri şikayeti projeleri: {$titlesStr} dir.";

        return [
            'message' => $message,
            'url' => route('dashboard'),
            'type' => 'direktor_bekleyen_projeler',
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
