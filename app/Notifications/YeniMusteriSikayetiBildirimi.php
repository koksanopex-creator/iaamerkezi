<?php

namespace App\Notifications;

use App\Models\MusteriSikayeti; //
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YeniMusteriSikayetiBildirimi extends Notification
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
        if ($notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable) {
            return ['mail'];
        }
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isDirector = method_exists($notifiable, 'hasRole') ? $notifiable->hasRole('Direktör') : false;
        $isCustomerRep = method_exists($notifiable, 'hasRole') ? $notifiable->hasRole('Müşteri Temsilcisi') : false;

        $name = match (true) {
            isset($notifiable->name) => $notifiable->name,
            $notifiable instanceof \Illuminate\Notifications\AnonymousNotifiable => 'Yetkili',
            default => 'Yetkili'
        };
        $bolumAd = $this->sikayet->sikayetKategori && $this->sikayet->sikayetKategori->bolum ? $this->sikayet->sikayetKategori->bolum->ad : 'Genel';
        $olusturan = $this->sikayet->olusturanKurulUyesi ? $this->sikayet->olusturanKurulUyesi->name : 'Sistem';

        $responseTime = \App\Models\Setting::where('key', 'sikayet_response_time_hours')->first()?->value ?? '72';

        $subject = 'Yeni Şikayet Bildirimi: #' . $this->sikayet->id;
        $introLine = "Bölümünüzü veya sorumluluk alanınızı ilgilendiren yeni bir şikayet sisteme girildi.";

        if ($isDirector) {
            $introLine = "Direktörlüğüne bağlı {$bolumAd} bölümüne \"{$this->sikayet->musteri_sikayet_konusu}\" başlıklı şikayet {$olusturan} tarafından girilmiştir.";
        } elseif ($isCustomerRep) {
            $subject = 'Şikayetiniz Kayıt Altına Alınmıştır: #' . $this->sikayet->id;
            $introLine = "Sayın {$name}, \"{$this->sikayet->musteri_sikayet_konusu}\" başlıklı şikayetiniz başarıyla kayıt altına alınmıştır. Şikayetiniz {$responseTime} saat içerisinde uygun bir çözüm takımına atanacak ve süreç başlatılacaktır. Süreci aşağıdaki bağlantıdan takip edebilirsiniz.";
        }

        $actionUrl = $isCustomerRep 
            ? route('iaa.sikayetler.show', $this->sikayet->id)
            : route('admin.sikayetler.show', $this->sikayet->id);

        return (new MailMessage)
            ->subject($subject)
            ->greeting($isCustomerRep ? "Merhaba," : "Merhaba {$name},")
            ->line($introLine)
            ->line("Müşteri: " . $this->sikayet->musteri_adi)
            ->line("Konu: " . $this->sikayet->musteri_sikayet_konusu)
            ->action('Şikayeti İncele', $actionUrl)
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $isCustomerRep = method_exists($notifiable, 'hasRole') ? $notifiable->hasRole('Müşteri Temsilcisi') : false;
        $link = $isCustomerRep 
            ? route('iaa.sikayetler.show', $this->sikayet->id)
            : route('admin.sikayetler.show', $this->sikayet->id);

        // Verileri hazırlayalım
        $bolumAd = $this->sikayet->sikayetKategori && $this->sikayet->sikayetKategori->bolum ? $this->sikayet->sikayetKategori->bolum->ad : 'Genel';

        // Yeni Format: **Yeni Şikayet**; #50 (**Başlık**) başlıklı şikayet sisteme girilmiştir.
        $message = "**Yeni Şikayet**; #{$this->sikayet->id} (**{$this->sikayet->musteri_sikayet_konusu}**) başlıklı şikayet sisteme girilmiştir.";

        // Rol Bazlı Özelleştirme
        if (method_exists($notifiable, 'hasRole')) {
            if ($notifiable->hasRole(['Bölüm Lideri', 'Bölüm Kalite Yöneticisi'])) {
                // Kullanıcı talebi: "** Yeni Şikayet ** #82 (**.........**) başlıklı bölümünüze ait şikayet sisteme girilmiştir."
                $message = "** Yeni Şikayet ** #{$this->sikayet->id} (**{$this->sikayet->musteri_sikayet_konusu}**) başlıklı bölümünüze ait şikayet sisteme girilmiştir.";
            } elseif ($notifiable->hasRole('Direktör')) {
                $olusturan = $this->sikayet->olusturanKurulUyesi ? $this->sikayet->olusturanKurulUyesi->name : 'Sistem';
                $message = "**Yeni Şikayet**; Direktörlüğünüze bağlı **{$bolumAd}** bölümüne **{$this->sikayet->musteri_sikayet_konusu}** başlıklı şikayet **{$olusturan}** tarafından girilmiştir.";
            } elseif ($isCustomerRep) {
                $message = "**Şikayet Kaydı**; Firmanız adına **{$this->sikayet->musteri_sikayet_konusu}** başlıklı şikayet kaydı oluşturulmuştur.";
            }
        }

        return [
            'message' => $message,
            'link' => $link,
            'sikayet_id' => $this->sikayet->id
        ];
    }
}