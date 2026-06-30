<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinOyKullanildi extends Notification
{
    use Queueable;

    public $case;
    public $voter;

    public function __construct($case, $voter)
    {
        $this->case = $case;
        $this->voter = $voter;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.disiplin.show', $this->case->id) . '?tab=kurul';

        return (new MailMessage)
            ->subject('Yeni Oy Kullanıldı — ' . $this->case->user->name)
            ->greeting("Merhaba {$notifiable->name},")
            ->line("{$this->case->user->name} isimli personelin #{$this->case->id} ID'li disiplin dosyası için **{$this->voter->name}** oyunu kullanmıştır.")
            ->action('Dosyayı İncele', $url)
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->case->user->name} (#{$this->case->id}) dosyası için {$this->voter->name} oyunu kullanmıştır.",
            'url' => route('admin.disiplin.show', $this->case->id) . '?tab=kurul',
            'icon' => 'check-circle',
            'color' => 'emerald',
            'case_id' => $this->case->id
        ];
    }
}
