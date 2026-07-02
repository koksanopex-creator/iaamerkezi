<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserAddedNotification extends Notification
{
    use Queueable;

    public User $newUser;
    public string $message;
    public string $title;
    protected bool $sendMail;

    public function __construct(User $newUser, string $message, string $title = 'Yeni Kullanıcı Eklendi', bool $sendMail = true)
    {
        $this->newUser = $newUser;
        $this->message = $message;
        $this->title = $title;
        $this->sendMail = $sendMail;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($this->sendMail) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->newUser->bolum_id
            ? route('admin.bolumler.dashboard', $this->newUser->bolum_id)
            : route('admin.users.index');

        return (new MailMessage)
            ->subject($this->title)
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->message)
            ->line('**Kullanıcı Adı:** ' . $this->newUser->name)
            ->line('**E-posta:** ' . $this->newUser->email)
            ->line('**Bölüm:** ' . ($this->newUser->bolum ? $this->newUser->bolum->ad : 'Belirtilmedi'))
            ->action('Bölüm Panosuna Git', $url)
            ->salutation('Bilginize sunarız.');
    }

    public function toArray(object $notifiable): array
    {
        $url = $this->newUser->bolum_id
            ? route('admin.bolumler.dashboard', $this->newUser->bolum_id)
            : route('admin.users.index');

        return [
            'message' => $this->message,
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'type' => 'new_user_added',
            'url' => $url,
        ];
    }
}
