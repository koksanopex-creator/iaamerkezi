<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinKararGeriAlindi extends Notification
{
    use Queueable;

    public $case;
    public $reverter;

    public function __construct($case, $reverter)
    {
        $this->case = $case;
        $this->reverter = $reverter;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Disiplin Kararı Geri Alındı';
        $line1 = "#{$this->case->id} numaralı disiplin dosyası hakkında verilen karar geri alınmıştır.";
        $line2 = "Dosya tekrar 'Kurulda' durumuna getirilmiş ve oylama süreci yeniden başlatılmıştır.";
        
        $url = route('admin.disiplin.show', $this->case->id);
        $action = 'Dosyayı İncele';

        if ($notifiable->hasRole('Personel')) {
            $url = route('disiplin.show', $this->case->id);
            $action = 'Dosya Detayı';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($line1)
            ->line($line2)
            ->action($action, $url)
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "#{$this->case->id} nolu disiplin kararı geri alındı. Dosya tekrar kurul aşamasında.",
            'url' => $notifiable->hasRole('Personel') ? route('disiplin.show', $this->case->id) : route('admin.disiplin.show', $this->case->id),
            'icon' => 'arrow-u-turn-left',
            'color' => 'amber',
            'case_id' => $this->case->id
        ];
    }
}
