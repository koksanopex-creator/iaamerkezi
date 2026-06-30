<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DisiplinToplantiMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $toplanti;
    public $tur;
    public $mesaj;

    public function __construct($toplanti, $tur, $mesaj)
    {
        $this->toplanti = $toplanti;
        $this->tur = $tur;
        $this->mesaj = $mesaj;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Disiplin Kurulu Toplantı Bilgilendirmesi: ' . $this->toplanti->baslik,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.disiplin.toplanti_bilgilendirme',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
