<?php

namespace App\Mail;

use App\Models\MusteriSikayeti;
use App\Models\Takim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SikayetAtamaBilgilendirmesi extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public MusteriSikayeti $sikayet;
    public Takim $team;
    public string $detayLinki;

    /**
     * Create a new message instance.
     */
    public function __construct(MusteriSikayeti $sikayet, Takim $team)
    {
        $this->sikayet = $sikayet;
        $this->team = $team;

        // === GÜNCELLENMİŞ URL MANTIĞI ===
        if ($sikayet->iaa_id) {
            $this->detayLinki = URL::route('proje.workspace.show', $sikayet->iaa_id);
        } else {
            $this->detayLinki = URL::route('admin.sikayetler.show', $sikayet->id); 
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Bilgi] Şikayet Ataması Yapıldı: ' . $this->sikayet->musteri_sikayet_konusu,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Bir sonraki adımda bu view'ı oluşturacağız
        return new Content(
            markdown: 'emails.sikayet-atama-bilgilendirmesi',
            with: [
                'sikayet' => $this->sikayet,
                'team' => $this->team,
                'detayLinki' => $this->detayLinki,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}