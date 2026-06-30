<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DisiplinKuruluToplanti;

use Illuminate\Contracts\Queue\ShouldQueue;

class DisiplinKuruluToplantiNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $islem; // 'planlandi' | 'iptal' | 'guncellendi' | 'baslatildi'
    public DisiplinKuruluToplanti $toplanti;
    public string $yapanAdi;

    public function __construct(string $islem, DisiplinKuruluToplanti $toplanti, string $yapanAdi)
    {
        $this->islem    = $islem;
        $this->toplanti = $toplanti;
        $this->yapanAdi = $yapanAdi;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = "";
        $line1   = "";
        
        if ($this->islem === 'planlandi') {
            $subject = "Yeni Disiplin Kurulu Toplantısı Planlandı";
            $line1   = "**{$this->yapanAdi}** tarafından yeni bir kurul toplantısı planlandı.";
        } elseif ($this->islem === 'baslatildi') {
            $subject = "Disiplin Kurulu Toplantısı Başlatıldı";
            $line1   = "**{$this->yapanAdi}** tarafından toplantı başlatıldı. Şu an kurul odasında görüşme devam ediyor.";
        } elseif ($this->islem === 'iptal') {
            $subject = "Disiplin Kurulu Toplantısı İptal Edildi";
            $line1   = "**{$this->yapanAdi}** tarafından planlanan toplantı iptal edildi.";
        } else {
            $subject = "Disiplin Kurulu Toplantısı Güncellendi";
            $line1   = "Toplantı bilgilerinde değişiklik yapıldı.";
        }

        return (new MailMessage)
            ->from(config('mail.from.address'), 'Köksan Disiplin Kurulu Sistemi')
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($line1)
            ->line("---")
            ->line("**Toplantı Başlığı:** {$this->toplanti->baslik}")
            ->line("**Tarih:** " . ($this->toplanti->baslangic_tarihi ? $this->toplanti->baslangic_tarihi->format('d.m.Y H:i') : 'Belirtilmemiş'))
            ->line("**Yer:** " . ($this->toplanti->yer ?: '-'))
            ->action('Detayları Görüntüle', route('admin.disiplin.kurul.toplanti.show', $this->toplanti))
            ->line('Saygılarımızla, Köksan Disiplin Kurulu Sistemi');
    }

    public function toArray(object $notifiable): array
    {
        $message = "";
        $icon    = "";
        $color   = "";

        if ($this->islem === 'planlandi') {
            $message = "Yeni toplantı planlandı: {$this->toplanti->baslik}";
            $icon    = 'calendar-plus';
            $color   = 'green';
        } elseif ($this->islem === 'baslatildi') {
            $message = "Toplantı başlatıldı: {$this->toplanti->baslik}";
            $icon    = 'play-circle';
            $color   = 'indigo';
        } elseif ($this->islem === 'iptal') {
            $message = "Toplantı iptal edildi: {$this->toplanti->baslik}";
            $icon    = 'calendar-x';
            $color   = 'red';
        } else {
            $message = "Toplantı güncellendi: {$this->toplanti->baslik}";
            $icon    = 'calendar-edit';
            $color   = 'blue';
        }

        return [
            'message' => $message,
            'url'     => route('admin.disiplin.kurul.toplanti.show', $this->toplanti),
            'icon'    => $icon,
            'color'   => $color,
        ];
    }
}
