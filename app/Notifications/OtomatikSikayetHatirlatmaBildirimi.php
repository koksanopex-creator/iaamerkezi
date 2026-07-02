<?php

namespace App\Notifications;

use App\Models\MusteriSikayeti;
use App\Models\SikayetHatirlaticiKurali;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtomatikSikayetHatirlatmaBildirimi extends Notification
{
    use Queueable;

    protected $sikayet;
    protected $kural;

    public function __construct(MusteriSikayeti $sikayet, SikayetHatirlaticiKurali $kural)
    {
        $this->sikayet = $sikayet;
        $this->kural = $kural;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Değişkenleri Değiştir
        $replace = [
            '{sikayet_konusu}' => $this->sikayet->musteri_sikayet_konusu,
            '{musteri_adi}' => $this->sikayet->musteri_adi,
            '{firma_adi}' => $this->sikayet->customer->name ?? 'N/A',
            '{tarih}' => now()->format('d.m.Y H:i'),
            '{sikayet_durumu}' => $this->sikayet->musteri_durum,
        ];

        $konu = $this->kural->mail_konusu ?: 'Otomatik Hatırlatma: {sikayet_konusu}';
        $govde = $this->kural->mail_taslagi ?: '{sikayet_konusu} konulu şikayet için çözüm süreci devam etmektedir. Lütfen güncel durumu kontrol ediniz.';

        $konu = str_replace(array_keys($replace), array_values($replace), $konu);
        $govde = str_replace(array_keys($replace), array_values($replace), $govde);

        return (new MailMessage)
            ->subject($konu)
            ->line($govde)
            ->action('Şikayeti Görüntüle', route('proje.workspace.show', $this->sikayet->iaa_id ?: 0))
            ->line('Bu bildirim otomatik hatırlatma kuralı çerçevesinde gönderilmiştir.');
    }

    public function toArray($notifiable): array
    {
        $replace = [
            '{sikayet_konusu}' => $this->sikayet->musteri_sikayet_konusu,
            '{musteri_adi}' => $this->sikayet->musteri_adi,
            '{firma_adi}' => $this->sikayet->customer->name ?? 'N/A',
            '{tarih}' => now()->format('d.m.Y H:i'),
            '{sikayet_durumu}' => $this->sikayet->musteri_durum,
        ];

        $metin = $this->kural->bildirim_metni ?: '{musteri_adi} şikayeti için hatırlatma.';
        $metin = str_replace(array_keys($replace), array_values($replace), $metin);

        return [
            'id' => $this->sikayet->id,
            'title' => '🕒 Otomatik Hatırlatma',
            'message' => $metin,
            'url' => route('proje.workspace.show', $this->sikayet->iaa_id ?: 0),
            'type' => 'automated_reminder',
        ];
    }
}
