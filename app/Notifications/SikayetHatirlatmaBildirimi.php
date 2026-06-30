<?php

namespace App\Notifications;

use App\Models\SikayetHatirlatma;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SikayetHatirlatmaBildirimi extends Notification implements ShouldQueue
{
    use Queueable;

    protected $hatirlatma;
    protected $rol;

    public function __construct(SikayetHatirlatma $hatirlatma, string $rol)
    {
        $this->hatirlatma = $hatirlatma;
        $this->rol = $rol;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $sikayet = $this->hatirlatma->musteriSikayeti;
        $user = $this->hatirlatma->gonderen;

        // Gönderen detayını hazırla
        $firma = $sikayet->customer->name ?? 'Müşteri';
        $context = "{$firma} Firması {$user->display_unvan} {$user->name}";

        $message = "{$context}, \"{$sikayet->musteri_sikayet_konusu}\" başlıklı şikayet için bir hatırlatma gönderdi.";

        return (new MailMessage)
            ->subject('Müşteri Hatırlatması: ' . $sikayet->musteri_sikayet_konusu)
            ->line($message)
            ->action('Detayları Görüntüle', route('admin.sikayet-hatirlatma.show', $this->hatirlatma->id))
            ->line('Lütfen süreci inceleyip gerekli bilgilendirmeyi yapınız.');
    }

    public function toArray($notifiable): array
    {
        $sikayet = $this->hatirlatma->musteriSikayeti;
        $user = $this->hatirlatma->gonderen;
        $firma = $sikayet->customer->name ?? 'Müşteri';
        $context = "{$firma} Firması {$user->display_unvan} {$user->name}";

        return [
            'id' => $this->hatirlatma->id,
            'title' => '🔔 Müşteri Hatırlatması',
            'message' => "{$context}, \"{$sikayet->musteri_sikayet_konusu}\" başlıklı şikayet için bir hatırlatma gönderdi.",
            'url' => route('admin.sikayet-hatirlatma.show', $this->hatirlatma->id),
            'type' => 'reminder',
        ];
    }
}
