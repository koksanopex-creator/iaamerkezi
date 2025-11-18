<?php

namespace App\Mail;

use App\Models\ProjeYorumu;
use App\Models\Iaa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class YeniYorumBildirimiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $yorum;
    public $proje;
    public $projeLinki;

    /**
     * Create a new message instance.
     */
    public function __construct(ProjeYorumu $yorum, Iaa $proje)
    {
        $this->yorum = $yorum;
        $this->proje = $proje;
        // Proje çalışma alanının linki
        $this->projeLinki = route('proje.workspace.show', $proje->id); 
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Proje #" . $this->proje->id . "] Yeni Bir Yorum Eklendi: " . $this->proje->baslik
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Bu dosyayı bir sonraki adımda oluşturacağız
        return new Content(
            markdown: 'emails.proje.yeni_yorum',
        );
    }
}