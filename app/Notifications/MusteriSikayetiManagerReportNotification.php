<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MusteriSikayetiManagerReportNotification extends Notification
{
    use Queueable;

    public $kural;
    public $performansVerisi;

    /**
     * Create a new notification instance.
     */
    public function __construct($kural, $performansVerisi)
    {
        $this->kural = $kural;
        $this->performansVerisi = $performansVerisi;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        if ($this->kural->mail_aktif_et) {
            $channels[] = 'mail';
        }
        if ($this->kural->zili_aktif_et) {
            $channels[] = 'database';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
                    ->subject($this->kural->mail_konusu ?? 'Ekip Performans Raporu')
                    ->greeting('Merhaba ' . $notifiable->name . ',')
                    ->line($this->kural->mail_taslagi ?? 'Ekibinizin güncel performans raporu aşağıdadır:');

        foreach ($this->performansVerisi as $veri) {
            $mail->line("{$veri->name}: Toplam {$veri->toplam} Şikayet, Çözümlenen: {$veri->cozumlenen}, İptal/Reddedilen: {$veri->iptal_red}, Son 7 Gün: {$veri->son_7_gun}");
        }

        $mail->action('Raporu İncele', route('admin.sikayetler.kurulGirdileri'))
             ->line('Sistemimizi kullandığınız için teşekkürler!');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'chart-bar',
            'title' => 'Yönetici Raporu',
            'message' => $this->kural->bildirim_metni ?? 'Ekip performans raporunuz hazır.',
            'url' => route('admin.sikayetler.kurulGirdileri')
        ];
    }
}
