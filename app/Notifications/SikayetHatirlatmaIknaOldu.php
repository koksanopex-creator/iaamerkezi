<?php

namespace App\Notifications;

use App\Models\SikayetHatirlatma;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SikayetHatirlatmaIknaOldu extends Notification implements ShouldQueue
{
    use Queueable;

    protected $hatirlatma;

    public function __construct(SikayetHatirlatma $hatirlatma)
    {
        $this->hatirlatma = $hatirlatma;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $sikayet = $this->hatirlatma->musteriSikayeti;

        return (new MailMessage)
            ->subject('Müşteri İkna Oldu: ' . $sikayet->musteri_sikayet_konusu)
            ->line($sikayet->musteri_adi . ' için gönderilen hatırlatma sonucunda müşteri ikna olduğunu belirtmiştir.')
            ->action('Detayları Görüntüle', route('admin.sikayet-hatirlatma.show', $this->hatirlatma->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'id' => $this->hatirlatma->id,
            'title' => '✅ Müşteri İkna Oldu',
            'message' => $this->hatirlatma->musteriSikayeti->musteri_adi . ' açıklamayı yeterli buldu.',
            'url' => route('admin.sikayet-hatirlatma.show', $this->hatirlatma->id),
            'type' => 'reminder_success',
        ];
    }
}
