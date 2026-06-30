<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SahaTemsilcisiYetkiGuncellendiBildirimi extends Notification
{
    use Queueable;

    protected $guncelleyenKisiAdi;

    /**
     * Create a new notification instance.
     */
    public function __construct($guncelleyenKisiAdi = null)
    {
        $this->guncelleyenKisiAdi = $guncelleyenKisiAdi;
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

    protected function getMessage()
    {
        $kisiText = $this->guncelleyenKisiAdi ? ($this->guncelleyenKisiAdi . ' tarafından ') : '';
        return "Müşteri Saha Temsilcisi yetkileriniz {$kisiText}güncellenmiştir. Sorumlu olduğunuz bölgeler/bölümler yeniden düzenlenmiştir.";
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Saha Temsilcisi Yetkileriniz Güncellendi')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->getMessage())
            ->line('Sisteme giriş yaparak güncel ziyaret planlarınızı ve sorumlu olduğunuz şikayetleri kontrol edebilirsiniz.')
            ->action('Sisteme Giriş Yap', route('dashboard'))
            ->line('İyi çalışmalar dileriz.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'saha_temsilcisi_yetki_guncelleme',
            'title' => 'Yetkileriniz Güncellendi',
            'message' => $this->getMessage(),
            'url' => route('dashboard'),
            'icon' => 'shield-check',
            'color' => 'indigo'
        ];
    }
}
