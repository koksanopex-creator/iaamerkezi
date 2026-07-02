<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApplicationRejectedNotification extends Notification
{
    use Queueable;

    public string $rejectionReason;

    public function __construct(string $rejectionReason)
    {
        $this->rejectionReason = $rejectionReason;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = rtrim(env('CENTRAL_SSO_URL', 'http://localhost:8001'), '/');

        return (new MailMessage)
            ->subject('İAA Uygulamasına Giriş Başvurunuz Reddedildi')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line('İAA uygulamasına yapmış olduğunuz kayıt/giriş başvurusu yöneticiler tarafından reddedilmiştir.')
            ->line('**Reddetme Sebebi:** ' . $this->rejectionReason)
            ->line('Detaylı bilgiyi İAA bekleme ekranından görebilir, dilerseniz başvurunuzu güncelleyerek tekrar onay talebinde bulunabilirsiniz.')
            ->action('Sisteme Giriş Yap', route('home'))
            ->salutation('Bilginize sunarız.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'İAA Uygulamasına giriş başvurunuz reddedildi. Sebep: ' . $this->rejectionReason,
            'type' => 'sso_application_rejected',
            'url' => route('home'),
        ];
    }
}
