<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\IaaZiyaretPlani;

class VisitStatusChanged extends Notification
{
    use Queueable;

    public $ziyaretPlani;
    public $action; // 'approved', 'rejected', 'revision'
    public $message;

    public function __construct(IaaZiyaretPlani $ziyaretPlani, $action, $message = null)
    {
        $this->ziyaretPlani = $ziyaretPlani;
        $this->action = $action;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Şimdilik sadece veritabanı bildirimi (Temel yapı)
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Ziyaret Planı Durumu Güncellendi';
        $statusText = '';

        if ($this->action === 'approved') {
            $statusText = 'Onaylandı';
        } elseif ($this->action === 'rejected') {
            $statusText = 'Reddedildi';
        } elseif ($this->action === 'revision') {
            $statusText = 'Revizyon İstendi';
        }

        return (new MailMessage)
                    ->subject($subject . ': ' . $statusText)
                    ->greeting('Merhaba ' . $notifiable->name . ',')
                    ->line('Ziyaret planınızın durumu güncellendi: ' . $statusText)
                    ->line($this->message ? 'Mesaj/Gerekçe: ' . $this->message : '')
                    ->action('Görüntüle', route('proje.workspace.show', $this->ziyaretPlani->iaa_id))
                    ->line('İyi çalışmalar dileriz.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ziyaret_' . $this->action,
            'title' => 'Ziyaret Planı ' . ucfirst($this->action),
            'message' => 'Ziyaret planı durumu güncellendi.',
            'detail' => $this->message,
            'iaa_id' => $this->ziyaretPlani->iaa_id,
        ];
    }
}
