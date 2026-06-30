<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TalepBildirimi extends Notification
{
    use Queueable;

    private $iaa;
    private $message;
    private $role;

    /**
     * Create a new notification instance.
     */
    public function __construct($iaa, $message, $role)
    {
        $this->iaa = $iaa;
        $this->message = $message;
        $this->role = $role;
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
        $isCustomerRep = method_exists($notifiable, 'hasRole') ? $notifiable->hasRole('Müşteri Temsilcisi') : false;
        
        $actionUrl = route('iaa.show', $this->iaa->id);
        if (empty($this->iaa->oneri) && $this->iaa->musteriSikayeti) {
            $actionUrl = $isCustomerRep 
                ? route('iaa.sikayetler.show', $this->iaa->musteriSikayeti->id)
                : route('admin.sikayetler.show', $this->iaa->musteriSikayeti->id);
        }

        return (new MailMessage)
            ->subject('Proje Talep Bildirimi: ' . $this->iaa->baslik)
            ->greeting('Sayın ' . $notifiable->name . ',')
            ->line($this->message)
            ->action('Projeyi Görüntüle', $actionUrl)
            ->line('Bilginize sunarız.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
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
            'iaa_id' => $this->iaa->id,
            'message' => $this->message,
            'role' => $this->role,
            'type' => 'talep_bildirimi',
            'url' => $actionUrl, // Frontend için gerekli
            'action_url' => $actionUrl // Yedek olarak
        ];
    }
}
