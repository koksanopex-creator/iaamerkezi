<?php

namespace App\Notifications;

use App\Models\TakimDavetiyesi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TakimIstegiKabulEdildi extends Notification
{
    use Queueable;

    protected $davetiye;

    /**
     * Create a new notification instance.
     */
    public function __construct(TakimDavetiyesi $davetiye)
    {
        $this->davetiye = $davetiye;
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
        return (new MailMessage)
            ->subject('Takım Katılım İsteğiniz Kabul Edildi')
            ->greeting("Merhaba {$notifiable->name},")
            ->line("'{$this->davetiye->takim->ad}' takımı için gönderdiğiniz katılma isteği kabul edildi.")
            ->line("Artık bu takımın bir üyesisiniz.")
            ->action('Takımı Görüntüle', route('takimlar.show', $this->davetiye->takim_id))
            ->line('İyi çalışmalar dileriz!');
    }

    /**
     * Get the array representation of the notification (Database/Bell).
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "'{$this->davetiye->takim->ad}' takımı katılım isteğiniz kabul edildi. Artık bu takımın üyesisiniz.",
            'action_url' => route('takimlar.show', $this->davetiye->takim_id),
            'icon' => 'user-check',
            'color' => 'green',
            'takim_id' => $this->davetiye->takim_id,
            'type' => 'takim_istegi_kabul'
        ];
    }
}
