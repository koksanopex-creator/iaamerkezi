<?php

namespace App\Notifications;

use App\Models\Iaa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjeStakeholderBilgilendirme extends Notification implements ShouldQueue
{
    use Queueable;

    protected $iaa;
    protected $message;
    protected $type; // 'info', 'success', 'warning'

    /**
     * Create a new notification instance.
     */
    public function __construct(Iaa $iaa, string $message, string $type = 'info')
    {
        $this->iaa = $iaa;
        $this->message = $message;
        $this->type = $type;
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
            ->subject('Proje Bilgilendirmesi: #' . $this->iaa->id)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($this->message)
            ->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $icon = 'info-circle';
        $color = 'blue';

        if ($this->type === 'success') {
            $icon = 'check-circle';
            $color = 'green';
        } elseif ($this->type === 'warning') {
            $icon = 'exclamation-circle';
            $color = 'orange';
        }

        return [
            'message' => $this->message,
            'link' => route('proje.workspace.show', $this->iaa->id),
            'iaa_id' => $this->iaa->id,
            'icon' => $icon,
            'color' => $color
        ];
    }
}
