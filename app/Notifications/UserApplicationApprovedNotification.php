<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApplicationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('dashboard');

        return (new MailMessage)
            ->subject('İAA Uygulamasına Girişiniz Onaylandı')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line('İAA uygulamasına yapmış olduğunuz kayıt/giriş başvurunuz onaylanmıştır.')
            ->line('Artık sisteme giriş yapabilir ve size atanan yetkiler çerçevesinde uygulamayı kullanabilirsiniz.')
            ->action('Sisteme Giriş Yap', $url)
            ->salutation('İyi çalışmalar dileriz.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'İAA Uygulamasına girişiniz onaylandı.',
            'type' => 'sso_application_approved',
            'url' => route('dashboard'),
        ];
    }
}
