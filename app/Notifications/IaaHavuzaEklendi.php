<?php

namespace App\Notifications;

use App\Models\Iaa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class IaaHavuzaEklendi extends Notification
{
    use Queueable;

    protected $iaa;

    /**
     * Create a new notification instance.
     */
    public function __construct(Iaa $iaa)
    {
        $this->iaa = $iaa;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        // Rota adını web.php dosyanızdan aldım
        $link = route('iaa.show', $this->iaa->id);

        return [
            // İstediğiniz mesaj
            'message' => "{$this->iaa->puan} puana sahip '{$this->iaa->baslik}' başlıklı İAA havuza eklendi.",
            'link' => $link,
            'url' => $link, // JS tarafindaki oncelik icin
            'action_url' => $link,
            'iaa_id' => $this->iaa->id
        ];
    }
}