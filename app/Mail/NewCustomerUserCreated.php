<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCustomerUserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword; // Şifrenin açık hali sadece mail gönderimi süresince burada duracak

    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hoşgeldiniz - Sisteme Giriş Bilgileriniz',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-customer-user',
        );
    }
}