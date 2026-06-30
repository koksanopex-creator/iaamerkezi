<?php

namespace App\Notifications;

use App\Models\SikayetHatirlatmaYorumu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SikayetHatirlatmaYaniti extends Notification implements ShouldQueue
{
    use Queueable;

    protected $yorum;

    public function __construct(SikayetHatirlatmaYorumu $yorum)
    {
        $this->yorum = $yorum;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $hatirlatma = $this->yorum->hatirlatma;
        $sikayet = $hatirlatma->musteriSikayeti;
        $user = $this->yorum->user;

        // Gönderen detayını hazırla (Unvan ve Bölüm/Firma)
        $context = "";
        if (!$user->is_personnel) {
            $firma = $sikayet->customer->name ?? 'Müşteri';
            $context = "{$firma} Firması {$user->display_unvan} {$user->name}";
        } else {
            $bolumAd = $user->bolum->ad ?? 'Genel';
            $context = "{$bolumAd} Bölümü {$user->display_unvan} {$user->name}";
        }

        $message = "{$context}, \"{$sikayet->musteri_sikayet_konusu}\" başlıklı şikayetin Hatırlatma kısmına bir açıklama ekledi.";

        return (new MailMessage)
            ->subject('Hatırlatma Yanıtı: ' . $sikayet->musteri_sikayet_konusu)
            ->line($message)
            ->line('Açıklama: ' . $this->yorum->yorum)
            ->action('Detayları Görüntüle', route('admin.sikayet-hatirlatma.show', $hatirlatma->id));
    }

    public function toArray($notifiable): array
    {
        $hatirlatma = $this->yorum->hatirlatma;
        $sikayet = $hatirlatma->musteriSikayeti;
        $user = $this->yorum->user;

        // Gönderen detayını hazırla
        $context = "";
        if (!$user->is_personnel) {
            $firma = $sikayet->customer->name ?? 'Müşteri';
            $context = "{$firma} Firması {$user->display_unvan} {$user->name}";
        } else {
            $bolumAd = $user->bolum->ad ?? 'Genel';
            $context = "{$bolumAd} Bölümü {$user->display_unvan} {$user->name}";
        }

        return [
            'id' => $this->yorum->sikayet_hatirlatma_id,
            'title' => '💬 Hatırlatma Yanıtı',
            'message' => "{$context}, \"{$sikayet->musteri_sikayet_konusu}\" başlıklı şikayetin Hatırlatma kısmına bir açıklama ekledi.",
            'url' => route('admin.sikayet-hatirlatma.show', $this->yorum->sikayet_hatirlatma_id),
            'type' => 'reminder_reply',
        ];
    }
}
