<?php

namespace App\Notifications;

use App\Models\Iaa;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YeniIaaOnerisi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $sender;

    /**
     * Create a new notification instance.
     *
     * @param Iaa $iaa
     * @param User|string|null $sender (User model for registered, string 'Misafir' for guest)
     */
    public function __construct(Iaa $iaa, $sender = 'Misafir')
    {
        $this->iaa = $iaa;
        $this->sender = $sender;
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
        $link = route('iaa.show', $this->iaa->id);
        
        $senderName = is_string($this->sender) ? $this->sender : ($this->sender->name ?? 'Bilinmeyen');

        return [
            'message' => "'{$senderName}' tarafından yeni bir İAA önerisi gönderildi: '{$this->iaa->baslik}'",
            'link' => $link,
            'url' => $link,
            'action_url' => $link,
            'iaa_id' => $this->iaa->id,
            'type' => 'yeni_iaa_onerisi'
        ];
    }
}
