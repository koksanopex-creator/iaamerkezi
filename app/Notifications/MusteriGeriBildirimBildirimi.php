<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MusteriGeriBildirimBildirimi extends Notification implements ShouldQueue
{
    use Queueable;

    public $sikayet;
    public $feedback;
    public $note;

    public function __construct($sikayet, $feedback, $note)
    {
        $this->sikayet = $sikayet;
        $this->feedback = $feedback;
        $this->note = $note;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $color = match($this->feedback) {
            'Onaylandı' => 'success',
            'Reddedildi' => 'error',
            default => 'warning' // Revizyon
        };

        return (new MailMessage)
                    ->subject('Müşteri Geri Bildirimi: #' . $this->sikayet->id . ' - ' . $this->feedback)
                    ->greeting('Merhaba ' . $notifiable->name . ',')
                    ->line('Müşteri, #' . $this->sikayet->id . ' nolu şikayetin çözümünü değerlendirdi.')
                    ->line('Karar: ' . $this->feedback)
                    ->line('Müşteri Notu: ' . ($this->note ?? 'Yok'))
                    ->action('Şikayeti Görüntüle', route('admin.sikayetler.show', $this->sikayet->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Müşteri #{$this->sikayet->id} nolu şikayet için geri bildirimde bulundu: {$this->feedback}",
            'url' => route('admin.sikayetler.show', $this->sikayet->id),
            'icon' => 'star',
            'color' => match($this->feedback) {
                'Onaylandı' => 'green',
                'Reddedildi' => 'red',
                default => 'yellow'
            },
            'sikayet_id' => $this->sikayet->id
        ];
    }
}