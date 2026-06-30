<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $rawPassword;

    public function __construct(User $user, string $rawPassword)
    {
        $this->user = $user;
        $this->rawPassword = $rawPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Köksan Portal\'a Hoşgeldiniz!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.users.welcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
