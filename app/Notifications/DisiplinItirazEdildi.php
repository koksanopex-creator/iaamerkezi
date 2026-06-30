<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class DisiplinItirazEdildi extends Notification
{
    use Queueable;

    public $case;
    public $appeal;

    public function __construct($case, $appeal)
    {
        $this->case = $case;
        $this->appeal = $appeal;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($notifiable->email && !Str::contains($notifiable->email, 'tckimlik@koksan.com')) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Disiplin Kararına İtiraz Edildi')
            ->greeting("Merhaba {$notifiable->name},")
            ->line("**{$this->case->user->name}** hakkındaki #{$this->case->id} numaralı disiplin dosyası kararına itiraz edilmiştir.")
            ->line("İtiraz Eden: **{$this->appeal->user->name}**")
            ->line("İtiraz Gerekçesi: \"{$this->appeal->reason}\"")
            ->action('Dosyayı İncele', route('admin.disiplin.show', $this->case->id))
            ->line('Hukuk birimi tarafından yeni bir kurul toplantısı planlanacaktır.')
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "{$this->case->user->name} hakkındaki disiplin kararına itiraz edildi. Yeni bir değerlendirme yapılması gerekmektedir.",
            'url' => route('admin.disiplin.show', $this->case->id),
            'icon' => 'reply-all',
            'color' => 'blue',
            'case_id' => $this->case->id
        ];
    }
}
