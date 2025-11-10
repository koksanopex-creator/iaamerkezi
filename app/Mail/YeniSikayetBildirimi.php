<?php

namespace App\Mail;

use App\Models\MusteriSikayeti;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class YeniSikayetBildirimi extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public MusteriSikayeti $sikayet;
    public string $detayLinki;

    /**
     * Create a new message instance.
     */
    public function __construct(MusteriSikayeti $sikayet)
    {
        $this->sikayet = $sikayet;
        // Yönetim panelindeki detay sayfasına link (route adını düzeltmeniz gerekebilir)
        $this->detayLinki = URL::route('admin.sikayetler.show', $this->sikayet->id); 
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Yeni Şikayet] ' . $this->sikayet->musteri_sikayet_konusu,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.yeni-sikayet-bildirimi',
            with: [
                'sikayet' => $this->sikayet,
                'detayLinki' => $this->detayLinki,
            ],
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