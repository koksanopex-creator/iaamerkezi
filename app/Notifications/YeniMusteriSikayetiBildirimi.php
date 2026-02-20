<?php

namespace App\Notifications;

use App\Models\MusteriSikayeti; //
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YeniMusteriSikayetiBildirimi extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sikayet;

    /**
     * Create a new notification instance.
     */
    public function __construct(MusteriSikayeti $sikayet)
    {
        $this->sikayet = $sikayet;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; // Sadece veritabanına kaydet
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isDirector = $notifiable->hasRole('Direktör');
        $bolumAd = $this->sikayet->sikayetKategori && $this->sikayet->sikayetKategori->bolum ? $this->sikayet->sikayetKategori->bolum->ad : 'Genel';
        $olusturan = $this->sikayet->olusturanKurulUyesi ? $this->sikayet->olusturanKurulUyesi->name : 'Sistem';

        $subject = 'Yeni Şikayet Bildirimi: #' . $this->sikayet->id;
        $introLine = "Bölümünüzü veya sorumluluk alanınızı ilgilendiren yeni bir şikayet sisteme girildi.";

        if ($isDirector) {
            $introLine = "Direktörlüğüne bağlı {$bolumAd} bölümüne \"{$this->sikayet->musteri_sikayet_konusu}\" başlıklı şikayet {$olusturan} tarafından girilmiştir.";
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($introLine)
            ->line("Müşteri: " . $this->sikayet->musteri_adi)
            ->line("Konu: " . $this->sikayet->musteri_sikayet_konusu)
            ->action('Şikayeti İncele', route('admin.sikayetler.show', $this->sikayet->id))
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $link = route('admin.sikayetler.show', $this->sikayet->id);

        // Verileri hazırlayalım
        $bolumAd = $this->sikayet->sikayetKategori && $this->sikayet->sikayetKategori->bolum ? $this->sikayet->sikayetKategori->bolum->ad : 'Genel';

        // Yeni Format: **Yeni Şikayet**; #50 (**Başlık**) başlıklı şikayet sisteme girilmiştir.
        $message = "**Yeni Şikayet**; #{$this->sikayet->id} (**{$this->sikayet->musteri_sikayet_konusu}**) başlıklı şikayet sisteme girilmiştir.";

        // Eğer direktörse mevcut özel formatı koru ama iyileştir
        if ($notifiable->hasRole('Direktör')) {
            $olusturan = $this->sikayet->olusturanKurulUyesi ? $this->sikayet->olusturanKurulUyesi->name : 'Sistem';
            $message = "**Yeni Şikayet**; Direktörlüğünüze bağlı **{$bolumAd}** bölümüne **{$this->sikayet->musteri_sikayet_konusu}** başlıklı şikayet **{$olusturan}** tarafından girilmiştir.";
        }

        return [
            'message' => $message,
            'link' => $link,
            'sikayet_id' => $this->sikayet->id
        ];
    }
}