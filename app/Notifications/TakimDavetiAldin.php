<?php

namespace App\Notifications;

use App\Models\Takim; // <-- Bu kez Takim modelini kullanıyoruz
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TakimDavetiAldin extends Notification
{
    use Queueable;

    protected $takim;
    protected $davetEden;

    /**
     * Create a new notification instance.
     */
    public function __construct(Takim $takim, $davetEden)
    {
        $this->takim = $takim;
        $this->davetEden = $davetEden;
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
        $link = route('takimlar.davetlerim');

        return [
            'message' => "{$this->davetEden->name}, sizi '{$this->takim->ad}' takımına davet etti.",
            'link' => $link, // route() helper'ı /iaa prefix'ini otomatik halledecektir.
            'takim_id' => $this->takim->id
        ];
    }
}