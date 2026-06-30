<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SikayetAcildiMusteriBildirimi extends Mailable
{
    use Queueable, SerializesModels;

    public $sikayet;
    public $customSubject;
    public $customBody;
    public $trackingUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(\App\Models\MusteriSikayeti $sikayet)
    {
        $this->sikayet = $sikayet;
        $this->trackingUrl = route('public.sikayet.show', $sikayet->takip_token);

        // Fetch settings or use defaults
        $settings = \App\Models\Setting::whereIn('key', ['sikayet_onay_email_subject', 'sikayet_onay_email_body'])->get()->keyBy('key');

        $this->customSubject = $settings->get('sikayet_onay_email_subject')?->value ?? 'Şikayetiniz Tarafımıza Ulaştı - ' . env('APP_NAME');

        $defaultBody = "Sayın {musteri_adi},\n\nŞikayetiniz alınmıştır. Takip bilgileriniz aşağıdadır:\nTakip Linki: {takip_linki}\n\nTeşekkür ederiz.";
        $rawBody = $settings->get('sikayet_onay_email_body')?->value ?? $defaultBody;

        // Replace placeholders
        $this->customBody = str_replace(
            ['{musteri_adi}', '{takip_linki}', '{sifre}'],
            [$sikayet->musteri_adi, $this->trackingUrl, ''], // Şifre burada yok
            $rawBody
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->customSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sikayet.acildi',
            with: [
                'sikayet' => $this->sikayet,
                'customBody' => $this->customBody,
                'trackingUrl' => $this->trackingUrl,
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
