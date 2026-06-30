<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinTumOylarTamamlandi extends Notification
{
    use Queueable;

    public $case;

    public function __construct($case)
    {
        $this->case = $case;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.disiplin.show', $this->case->id) . '?tab=kurul';

        return (new MailMessage)
            ->subject('Tüm Oylar Kullanıldı — ' . $this->case->user->name)
            ->greeting("Merhaba {$notifiable->name},")
            ->line("{$this->case->user->name} isimli personelin #{$this->case->id} ID'li disiplin dosyası için **tüm kurul üyeleri oylarını kullanmıştır.**")
            ->line("Lütfen dosyayı inceleyerek nihai kararınızı verin ve dosyayı kapatın.")
            ->action('Kurul Odasına Git', $url)
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->case->user->name} (#{$this->case->id}) dosyası için tüm oylar kullanıldı. Lütfen dosyayı kapatın.",
            'url' => route('admin.disiplin.show', $this->case->id) . '?tab=kurul',
            'icon' => 'flag',
            'color' => 'indigo',
            'case_id' => $this->case->id
        ];
    }
}
