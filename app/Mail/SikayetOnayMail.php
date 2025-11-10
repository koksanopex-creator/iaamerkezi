<?php

namespace App\Mail;

use App\Models\MusteriSikayeti;
use App\Models\Setting; // Ayarları çekmek için ekledik
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL; // Takip linki oluşturmak için

class SikayetOnayMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // E-posta şablonunda kullanılacak public değişkenler
    public MusteriSikayeti $sikayet;
    public string $plainPassword;
    public string $takipLinki;
    public ?string $yanitSuresi; // Ayarlardan gelecek (nullable olabilir)

    /**
     * Create a new message instance.
     *
     * @param MusteriSikayeti $sikayet     Oluşturulan şikayet kaydı
     * @param string          $plainPassword Gönderilecek hash'lenmemiş şifre
     */
    public function __construct(MusteriSikayeti $sikayet, string $plainPassword)
    {
        $this->sikayet = $sikayet;
        $this->plainPassword = $plainPassword;

        // Takip linkini oluştur (route() helper'ı ile)
        $this->takipLinki = URL::route('public.sikayet.show', ['token' => $sikayet->takip_token]);

        // Ayarlardan yanıt süresini al (varsayılan 72)
        $this->yanitSuresi = Setting::where('key', 'sikayet_response_time_hours')->value('value') ?? '72';
    }

    /**
     * Get the message envelope.
     * E-postanın konusunu ve gönderen/alan bilgilerini tanımlar.
     */
    public function envelope(): Envelope
    {
        // E-posta konusunu ayarlardan al (varsayılan bir başlık belirle)
        $subject = Setting::where('key', 'sikayet_onay_email_subject')->value('value') ?? 'Şikayetiniz Alınmıştır - Takip Bilgileriniz';

        return new Envelope(
            subject: $subject,
            // İstersen gönderen adresini de buradan ayarlayabilirsin:
            // from: new Address('noreply@koksan.com', config('app.name')),
        );
    }

    /**
     * Get the message content definition.
     * E-postanın içeriğini (hangi view'ı kullanacağını) tanımlar.
     */
    public function content(): Content
    {
        // Ayarlardan e-posta içeriği şablonunu al
        $bodyTemplate = Setting::where('key', 'sikayet_onay_email_body')->value('value') ?? "Sayın {musteri_adi},\n\nŞikayetiniz alınmıştır ({sikayet_konusu}).\n\nŞikayetiniz en geç {yanit_suresi} saat içinde değerlendirilecek ve tarafınıza geri dönüş yapılacaktır.\n\nTakip bilgileriniz aşağıdadır:\nTakip Linki: {takip_linki}\nŞifreniz: {sifre}\n\nTeşekkür ederiz.";

        // Şablondaki değişkenleri değiştir
        $body = str_replace(
            ['{musteri_adi}', '{sikayet_konusu}', '{takip_linki}', '{sifre}', '{yanit_suresi}'],
            [
                $this->sikayet->musteri_adi,
                $this->sikayet->musteri_sikayet_konusu,
                $this->takipLinki,
                $this->plainPassword,
                $this->yanitSuresi
            ],
            $bodyTemplate
        );

        // Markdown view'ını ve view'a gönderilecek veriyi tanımla
        return new Content(
            markdown: 'emails.sikayet-onay', // Bu view'ı sonra oluşturacağız
            with: [
                'bodyContent' => $body, // İşlenmiş metni view'a gönder
                'takipLinki' => $this->takipLinki, // Buton için linki ayrıca gönderelim
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
        // Şimdilik ek dosya göndermiyoruz
        return [];
    }
}