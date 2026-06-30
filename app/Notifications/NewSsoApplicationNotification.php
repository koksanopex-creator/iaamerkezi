<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewSsoApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $newUser;
    public string $title;
    public string $messageContent;

    public function __construct(User $newUser, string $title = 'Sisteme Yeni Başvuru Yapıldı', string $messageContent = 'Sisteme yeni başvuru yapıldı lütfen onaylayın.')
    {
        $this->newUser = $newUser;
        $this->title = $title;
        $this->messageContent = $messageContent;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.users.onay_bekleyenler');

        return (new MailMessage)
            ->subject($this->title)
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->messageContent)
            ->line('**Başvuru Yapan:** ' . $this->newUser->name)
            ->line('**E-posta:** ' . $this->newUser->email)
            ->line('**Bölüm:** ' . ($this->newUser->bolum ? $this->newUser->bolum->ad : 'Belirtilmedi'))
            ->line('**Başvuru Tarihi:** ' . now()->format('d.m.Y H:i'))
            ->action('Başvuruyu Onayla', $url)
            ->salutation('Bilginize sunarız.');
    }

    public function toArray(object $notifiable): array
    {
        $url = route('admin.users.onay_bekleyenler');

        return [
            'message' => $this->messageContent,
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'type' => 'new_sso_application',
            'url' => $url,
        ];
    }
}
