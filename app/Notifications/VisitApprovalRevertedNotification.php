<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class VisitApprovalRevertedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $iaa;
    public $reverterName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Iaa $iaa, $reverterName)
    {
        $this->iaa = $iaa;
        $this->reverterName = $reverterName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Sadece zil bildirimi (Mail yok)
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'iaa_id' => $this->iaa->id,
            'title' => 'Karar Geri Alındı',
            'message' => "{$this->reverterName} tarafından Ziyaret Planı onayı iptal edildi ve bir önceki aşamaya çekildi.",
            'icon' => 'heroicon-o-arrow-uturn-left',
            'color' => 'orange'
        ];
    }
}
