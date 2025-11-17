<?php

namespace App\Notifications;

use App\Models\MusteriSikayeti; // <-- Modelimizi ekliyoruz
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SikayetTakimaAtandiBildirimi extends Notification
{
    use Queueable;

    protected $sikayet;

    /**
     * Create a new notification instance.
     * Bildirimi oluştururken $sikayet objesini almasını sağlıyoruz
     */
    public function __construct(MusteriSikayeti $sikayet)
    {
        $this->sikayet = $sikayet;
    }

    /**
     * Get the notification's delivery channels.
     * Bu bildirimin hangi kanallardan gideceğini belirler.
     * Biz sadece veritabanı istiyoruz.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     * Bu metot, 'notifications' tablosundaki 'data' sütununa ne yazılacağını belirler.
     */
    public function toDatabase(object $notifiable): array
    {
        // Rota adını web.php dosyanızdan aldım
        $link = route('admin.sikayetler.show', $this->sikayet->id);

        return [
            'message' => "#{$this->sikayet->id} numaralı şikayet takımınıza atandı.",
            'link' => $link, // route() helper'ı /iaa prefix'ini otomatik halledecektir.
            'sikayet_id' => $this->sikayet->id
        ];
    }
}