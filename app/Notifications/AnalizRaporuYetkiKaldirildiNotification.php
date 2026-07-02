<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnalizRaporuYetkiKaldirildiNotification extends Notification
{
    use Queueable;

    public string $atayanAdi;

    public function __construct(string $atayanAdi)
    {
        $this->atayanAdi = $atayanAdi;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Müşteri Şikayet Analiz Raporu Yetkisi Kaldırıldı')
            ->greeting("Merhaba {$notifiable->name},")
            ->line("Sistem yöneticisi **{$this->atayanAdi}** tarafından Müşteri Şikayet Analiz Raporu'na olan erişim yetkiniz kaldırılmıştır.")
            ->line('Erişiminizle ilgili bir hata olduğunu düşünüyorsanız yöneticinize başvurabilirsiniz.')
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Şikayet Analiz Raporu erişim yetkiniz kaldırıldı.',
            'url' => url('/dashboard'),
            'icon' => 'x-circle',
            'color' => 'orange',
        ];
    }
}
