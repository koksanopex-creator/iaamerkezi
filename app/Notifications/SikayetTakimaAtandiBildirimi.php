<?php

namespace App\Notifications;

use App\Models\MusteriSikayeti; // <-- Modelimizi ekliyoruz
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SikayetTakimaAtandiBildirimi extends Notification implements ShouldQueue
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
        $mail = (new MailMessage)
            ->subject('Şikayet Ataması: #' . $this->sikayet->id)
            ->greeting("Merhaba {$notifiable->name},")
            ->line(strip_tags(str_replace(['**', '\"'], '', $data['message'])))
            ->action('Şikayeti İncele', $data['link']);

        // Müşteri tarafına özel şifre notu ekle
        if (in_array($this->type, ['musteri', 'ek_ilgili'])) {
            $mail->line('Sisteme mevcut giriş bilgileriniz (şifreniz) ile erişim sağlayabilirsiniz.')
                 ->line('Şifrenizi hatırlamıyorsanız giriş sayfasından "Şifremi Unuttum" adımını takip edebilirsiniz.');
        }

        $mail->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $link = route('admin.sikayetler.show', $this->sikayet->id);
        
        // Null-safe relations
        $kategori = $this->sikayet->sikayetKategori;
        $bolum = $kategori ? $kategori->bolum : null;
        $bolumAd = $bolum ? $bolum->ad : 'Genel';
        
        $takim = $this->sikayet->cozumTakimi;
        $takimAd = $takim ? $takim->ad : 'Çözüm Takımı';
        
        $baslik = $this->sikayet->musteri_sikayet_konusu ?? 'Şikayet';

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
            case 'musteri':
            case 'ek_ilgili':
                $message = "#{$this->sikayet->id} Nolu **\"{$baslik}\"** başlıklı şikayetiniz çözüm için **\"{$takimAd}\"**'na atanmış ve çalışma başlatılmıştır.";
                if ($this->sikayet->iaa_id) {
                    $link = route('proje.workspace.show', $this->sikayet->iaa_id);
                }
                break;
        }

        return [
            'message' => $message,
            'link' => $link,
            'sikayet_id' => $this->sikayet->id
        ];
    }
}