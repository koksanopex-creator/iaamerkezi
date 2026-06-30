<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HataliBildirimBildirimi extends Notification
{
    use Queueable;

    private $iaa;
    private $mesaj;
    private $rol;

    /**
     * Create a new notification instance.
     */
    public function __construct($iaa, $mesaj, $rol)
    {
        $this->iaa = $iaa;
        $this->mesaj = $mesaj;
        $this->rol = $rol;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sikayetBaslik = $this->iaa->baslik ?? 'Belirtilmemiş';
        $sikayetId = $this->iaa->musteriSikayeti ? $this->iaa->musteriSikayeti->id : '-';

        $isCustomerRep = method_exists($notifiable, 'hasRole') ? $notifiable->hasRole('Müşteri Temsilcisi') : false;
        
        $actionUrl = route('iaa.show', $this->iaa->id);
        if (empty($this->iaa->oneri) && $this->iaa->musteriSikayeti) {
            $actionUrl = $isCustomerRep 
                ? route('iaa.sikayetler.show', $this->iaa->musteriSikayeti->id)
                : route('admin.sikayetler.show', $this->iaa->musteriSikayeti->id);
        }

        return (new MailMessage)
            ->subject('Hatalı Bildirim Süreci: #' . $sikayetId)
            ->greeting('Sayın ' . $notifiable->name . ',')
            ->line($this->mesaj)
            ->line('Şikayet ID: #' . $sikayetId)
            ->line('Şikayet Başlığı: ' . $sikayetBaslik)
            ->action('Detayları Görüntüle', $actionUrl)
            ->line('İyi çalışmalar dileriz.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $isCustomerRep = method_exists($notifiable, 'hasRole') ? $notifiable->hasRole('Müşteri Temsilcisi') : false;
        
        $actionUrl = route('iaa.show', $this->iaa->id);
        if (empty($this->iaa->oneri) && $this->iaa->musteriSikayeti) {
            $actionUrl = $isCustomerRep 
                ? route('iaa.sikayetler.show', $this->iaa->musteriSikayeti->id)
                : route('admin.sikayetler.show', $this->iaa->musteriSikayeti->id);
        }

        return [
            'type' => 'hatali_bildirim', // Özel ikon/renk için
            'title' => 'Hatalı Bildirim Güncellemesi',
            'message' => $this->mesaj,
            'iaa_id' => $this->iaa->id,
            'url' => $actionUrl,
        ];
    }
}
