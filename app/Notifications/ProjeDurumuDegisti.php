<?php

namespace App\Notifications;

use App\Models\Iaa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjeDurumuDegisti extends Notification
{
    use Queueable;

    protected $iaa;
    protected $durumText; // Örn: "onayınızı beklemektedir."
    protected $bilgi;     // Örn: "Bölüm yöneticisi Serkan Tölek tarafından onaylanmıştır."

    /**
     * Create a new notification instance.
     */
    public function __construct(Iaa $iaa, string $durumText, ?string $bilgi = null)
    {
        $this->iaa = $iaa;
        $this->durumText = $durumText;
        $this->bilgi = $bilgi;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $url = route('proje.workspace.show', $this->iaa->id);

        if (str_contains($this->durumText, 'onayınızı beklemektedir')) {
            $url .= '#yonetici-onay-alani';
        }

        $line = "'{$this->iaa->baslik}' başlıklı proje {$this->durumText}";
        if ($this->bilgi) {
            $line .= " ({$this->bilgi})";
        }

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Proje Durum Güncellemesi: ' . $this->iaa->baslik)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($line)
            ->action('Projeyi İncele', $url)
            ->line('Detayları görüntülemek için yukarıdaki butona tıklayabilirsiniz.')
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        // 1. LİNK DÜZELTMESİ:
        $url = route('proje.workspace.show', $this->iaa->id);

        if (str_contains($this->durumText, 'onayınızı beklemektedir')) {
            $url .= '#yonetici-onay-alani';
        }

        // 2. MESAJ DÜZELTMESİ:
        // "Neden:" kelimesini kaldırdık. Parantez içinde şık bir bilgi notu ekledik.
        $message = "'{$this->iaa->baslik}' başlıklı proje {$this->durumText}";

        if ($this->bilgi) {
            $message .= " ({$this->bilgi})";
        }

        // 3. RENK VE İKON SEÇİMİ:
        // Duruma göre ikon ve renk belirleyelim
        $icon = 'info-circle'; // Varsayılan
        $color = 'blue';

        if (str_contains($this->durumText, 'onayland') || str_contains($this->durumText, 'onayınızı')) {
            $icon = 'check-circle';
            $color = 'green';
        } elseif (str_contains($this->durumText, 'redde') || str_contains($this->durumText, 'reviz')) {
            $icon = 'exclamation-circle';
            $color = 'red';
        }

        return [
            'message' => $message,
            'action_url' => $url, // Blade için
            'link' => $url,       // JavaScript için (ESKİ YAPIYI BOZMAMAK ADINA EKLENDİ)
            'icon' => $icon,
            'color' => $color,
            'iaa_id' => $this->iaa->id
        ];
    }
}