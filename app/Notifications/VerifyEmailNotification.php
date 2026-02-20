<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class VerifyEmailNotification extends VerifyEmail
{
    /**
     * Get the verify email notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('E-Posta Adresini Doğrula')
            ->greeting('Merhaba!')
            ->line('E-posta adresinizi doğrulamak için lütfen aşağıdaki butona tıklayın.')
            ->action('E-Posta Adresini Doğrula', $url)
            ->line('Eğer hesap oluşturmadıysanız, herhangi bir işlem yapmanıza gerek yoktur.')
            ->salutation("Saygılarımızla,\n" . config('app.name'));
    }
}
