<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Şifre Sıfırlama İsteği')
            ->greeting('Merhaba!')
            ->line('Hesabınız için bir şifre sıfırlama talebi aldığınız için bu e-postayı alıyorsunuz.')
            ->action('Şifreyi Sıfırla', $url)
            ->line('Bu şifre sıfırlama bağlantısının süresi ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' dakika içinde dolacaktır.')
            ->line('Şifre sıfırlama talebinde bulunmadıysanız, herhangi bir işlem yapmanıza gerek yoktur.')
            ->salutation("Saygılarımızla,\n" . config('app.name'));
    }
}
