<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RolAtandiNotification extends Notification
{
    use Queueable;

    public string $rolAdi;
    public bool $eklendi; // true = atandı, false = kaldırıldı
    public string $atayanAdi;

    public function __construct(string $rolAdi, bool $eklendi, string $atayanAdi)
    {
        $this->rolAdi = $rolAdi;
        $this->eklendi = $eklendi;
        $this->atayanAdi = $atayanAdi;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->eklendi) {
            $subject = "Sisteme Yeni Göreviniz Atandı: {$this->rolAdi}";
            $line1 = "Sistem yöneticisi **{$this->atayanAdi}** tarafından **{$this->rolAdi}** rolüne atandınız.";
            $line2 = "Bu rolle birlikte sisteme yeni yetkiler kazandınız. Değişiklikler anlık olarak geçerli oldu.";
        } else {
            $subject = "Rol Değişikliği Bildirimi: {$this->rolAdi}";
            $line1 = "Sistem yöneticisi **{$this->atayanAdi}** tarafından **{$this->rolAdi}** rolünüz kaldırıldı.";
            $line2 = "Bu role bağlı yetkileriniz artık geçerli değil. Sorularınız için yöneticinize danışabilirsiniz.";
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($line1)
            ->line($line2)
            ->action('Sisteme Git', url('/'))
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        if ($this->eklendi) {
            $message = "{$this->rolAdi} rolüne atandınız. Yeni yetkileriniz aktif edildi.";
            $icon = 'badge-check';
            $color = 'green';
        } else {
            $message = "{$this->rolAdi} rolünüz kaldırıldı.";
            $icon = 'x-circle';
            $color = 'orange';
        }

        return [
            'message' => $message,
            'url' => url('/dashboard'),
            'icon' => $icon,
            'color' => $color,
        ];
    }
}
