<?php

namespace App\Mail;

use App\Models\MusteriSikayeti;
use App\Models\Takim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SikayetAtandiMusteriBildirimi extends Mailable
{
    use Queueable, SerializesModels;

    public $sikayet;
    public $takim;
    public $takipLinki;

    public function __construct(MusteriSikayeti $sikayet, Takim $takim)
    {
        $this->sikayet = $sikayet;
        $this->takim = $takim;
        // Müşterinin şifresiz takip linkini oluştur
        $this->takipLinki = route('public.sikayet.show', $sikayet->takip_token); 
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[#" . $this->sikayet->id . "] Şikayetiniz Çözüm Ekibimize Atandı"
        );
    }

    public function content(): Content
    {
        // Basit bir e-posta görünümü için markdown kullanacağız
        // Bu dosya: resources/views/emails/sikayet/atama_musteri.blade.php
        return new Content(
            markdown: 'emails.sikayet.atama_musteri', 
        );
    }
}