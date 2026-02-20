<?php

namespace App\Notifications;

use App\Models\MusteriSikayeti; // <-- Modelimizi ekliyoruz
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SikayetTakimaAtandiBildirimi extends Notification
{
    use Queueable;

    protected $sikayet;
    protected $type; // lider, direktor, kalite, bolum_lideri, olusturan

    /**
     * Create a new notification instance.
     */
    public function __construct(MusteriSikayeti $sikayet, string $type = 'lider')
    {
        $this->sikayet = $sikayet;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->toDatabase($notifiable);

        return (new MailMessage)
            ->subject('Şikayet Ataması: #' . $this->sikayet->id)
            ->greeting("Merhaba {$notifiable->name},")
            ->line(strip_tags(str_replace(['**', '\"'], '', $data['message'])))
            ->action('Şikayeti İncele', $data['link'])
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $link = route('admin.sikayetler.show', $this->sikayet->id);
        $bolumAd = $this->sikayet->sikayetKategori && $this->sikayet->sikayetKategori->bolum ? $this->sikayet->sikayetKategori->bolum->ad : 'Genel';
        $baslik = $this->sikayet->musteri_sikayet_konusu;
        $takimAd = $this->sikayet->cozumTakimi ? $this->sikayet->cozumTakimi->ad : 'Çözüm Takımı';

        $message = "#{$this->sikayet->id} Nolu **{$baslik}** başlıklı şikayet takımınıza atandı.";

        switch ($this->type) {
            case 'direktor':
                $message = "#{$this->sikayet->id} Nolu **{$baslik}** başlıklı şikayet direktörlüğünüze bağlı **{$bolumAd}** bölümün Müşteri şikayet çözüm takımına atandı.";
                break;
            case 'kalite':
                $message = "#{$this->sikayet->id} Nolu **{$baslik}** başlıklı şikayet size bağlı **{$bolumAd}** bölümünün Müşteri şikayet çözüm takımına atandı.";
                break;
            case 'bolum_lideri':
                $message = "#{$this->sikayet->id} Nolu **{$baslik}** başlıklı bölümünüze ait şikayet çözüm için **\"{$takimAd}\"**'na atandı.";
                break;
            case 'olusturan':
                $message = "#{$this->sikayet->id} Nolu **{$baslik}** başlıklı şikayet **{$bolumAd}** bölümünün Müşteri şikayet çözüm takımına atandı.";
                break;
        }

        return [
            'message' => $message,
            'link' => $link,
            'sikayet_id' => $this->sikayet->id
        ];
    }
}