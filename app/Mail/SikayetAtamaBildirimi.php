<?php

namespace App\Mail;

use App\Models\MusteriSikayeti;
use App\Models\Takim; // Çözüm ekibi modeli (Sizdeki adı farklı olabilir)
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SikayetAtamaBildirimi extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public MusteriSikayeti $sikayet;
    public Takim $team; // Atanan takım
    public ?User $atananKullanici; // Atanan spesifik kullanıcı (opsiyonel)
    public string $detayLinki;

    /**
     * Create a new message instance.
     * @param MusteriSikayeti $sikayet
     * @param Takim $team Atanan çözüm ekibi
     * @param User|null $atananKullanici (Eğer spesifik bir kişiye atanıyorsa)
     */
    public function __construct(MusteriSikayeti $sikayet, Takim $team, ?User $atananKullanici = null)
    {
        $this->sikayet = $sikayet;
        $this->team = $team;
        $this->atananKullanici = $atananKullanici;

        // === GÜNCELLENMİŞ URL MANTIĞI ===
        // Sizin sikayetler-tablosu.blade.php dosyanızdaki mantığa göre:
        // Eğer şikayetin bir iaa_id'si varsa, proje linkine yönlendir.
        if ($sikayet->iaa_id) {
            $this->detayLinki = URL::route('proje.workspace.show', $sikayet->iaa_id);
        } else {
            // Eğer (bir hata sonucu) iaa_id oluşmamışsa, şikayet detayına gitsin.
            $this->detayLinki = URL::route('admin.sikayetler.show', $sikayet->id); 
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Şikayet Ataması] ' . $this->sikayet->musteri_sikayet_konusu,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sikayet-atama-bildirimi', // Bu, bir sonraki adımda oluşturacağımız view
            with: [
                'sikayet' => $this->sikayet,
                'team' => $this->team,
                'atananKullanici' => $this->atananKullanici,
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