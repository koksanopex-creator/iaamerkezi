<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ObserverRemoved extends Notification
{
    use Queueable;

    protected $target;

    /**
     * Create a new notification instance.
     */
    public function __construct($target)
    {
        $this->target = $target;
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
        return (new MailMessage)
                    ->subject('Gözlemci Görevi Sona Erdi')
                    ->greeting('Merhaba ' . $notifiable->name . ',')
                    ->line($this->target->name . ' sizi "Bölüm Gözlemcisi" görevinden ayırdı.')
                    ->line('Artık bu yöneticinin hesabını izleme yetkiniz kalmamıştır.')
                    ->line('Teşekkür ederiz.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'observer_removed',
            'title' => 'Gözlemci Göreviniz Sona Erdi',
            'message' => $this->target->name . ' gözlemci yetkinizi geri aldı.',
            'url' => route('dashboard'),
            'icon' => '🚫'
        ];
    }
}
