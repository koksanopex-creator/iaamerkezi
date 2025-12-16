<?php

namespace App\Notifications;

use App\Models\MusteriSikayeti; //
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YeniMusteriSikayetiBildirimi extends Notification
{
    use Queueable;

    protected $sikayet;

    /**
     * Create a new notification instance.
     */
    public function __construct(MusteriSikayeti $sikayet)
    {
        $this->sikayet = $sikayet;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; // Sadece veritabanına kaydet
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Yeni Şikayet Bildirimi: #' . $this->sikayet->id)
                    ->greeting("Merhaba {$notifiable->name},")
                    ->line("Bölümünüzü veya sorumluluk alanınızı ilgilendiren yeni bir şikayet sisteme girildi.")
                    ->line("Müşteri: " . $this->sikayet->musteri_adi)
                    ->line("Konu: " . $this->sikayet->musteri_sikayet_konusu)
                    ->action('Şikayeti İncele', route('admin.sikayetler.show', $this->sikayet->id));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        // Rota adını web.php dosyanızdan aldım
        $link = route('admin.sikayetler.show', $this->sikayet->id);

        return [
            // İstediğiniz mesaj
            'message' => "Yeni bir müşteri şikayeti eklendi: #{$this->sikayet->id} ({$this->sikayet->musteri_sikayet_konusu})",
            // Link, /iaa alt klasörüne otomatik uyum sağlar
            'link' => $link, 
            'sikayet_id' => $this->sikayet->id
        ];
    }
}