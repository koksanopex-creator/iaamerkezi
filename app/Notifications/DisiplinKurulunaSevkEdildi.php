<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinKurulunaSevkEdildi extends Notification
{
    use Queueable;

    public $case;

    /**
     * Create a new notification instance.
     */
    public function __construct($case)
    {
        $this->case = $case;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $toplantiTarihi = $this->case->toplanti_tarihi ? $this->case->toplanti_tarihi->format('d.m.Y H:i') : 'Belirtilmedi';
        $url = route('admin.disiplin.show', $this->case->id);

        return (new MailMessage)
            ->subject('Disiplin Kurulu Toplantı Bildirimi')
            ->greeting("Merhaba {$notifiable->name},")
            ->line("Bir disiplin dosyası Disiplin Kurulu'na sevk edildi ve değerlendirmeniz beklenmektedir.")
            ->line("Personel: " . $this->case->user->name)
            ->line("Toplantı Tarihi: " . $toplantiTarihi)
            ->line("Lütfen toplantı saatinde sistem üzerinden dosyayı inceleyip kararınızı veriniz.")
            ->action('Dosyayı İncele', $url)
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $toplantiTarihi = $this->case->toplanti_tarihi ? $this->case->toplanti_tarihi->format('d.m.Y H:i') : 'Belirtilmedi';

        return [
            'message' => 'Disiplin kurulu toplantınız var. Dosya: ' . $this->case->user->name . '. Toplantı: ' . $toplantiTarihi,
            'url' => route('admin.disiplin.show', $this->case->id),
            'icon' => 'users',
            'color' => 'indigo',
            'case_id' => $this->case->id
        ];
    }
}
