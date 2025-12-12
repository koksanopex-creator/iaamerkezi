<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PersonelSavunmaVerdi extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $case;
    public function __construct($case)
    {
        $this->case = $case;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->case->user->name . ' disiplin savunmasını sisteme girdi. Karar bekleniyor.',
            'action_url' => route('admin.disiplin.show', $this->case->id), // Admin Linki
            'icon' => 'document-text',
            'color' => 'blue',
            'case_id' => $this->case->id
        ];
    }
}
