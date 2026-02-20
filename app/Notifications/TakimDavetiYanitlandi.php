<?php

namespace App\Notifications;

use App\Models\TakimDavetiyesi;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TakimDavetiYanitlandi extends Notification implements ShouldQueue
{
    use Queueable;

    protected $davetiye;
    protected $kabulEdenUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(TakimDavetiyesi $davetiye, User $kabulEdenUser)
    {
        $this->davetiye = $davetiye;
        $this->kabulEdenUser = $kabulEdenUser;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Hem veritabanına (zil) hem maile gönderir
        return ['database', 'mail'];
    }

    // === MAİL FORMATI ===
    public function toMail(object $notifiable): MailMessage
    {
        $durum = $this->davetiye->durum === 'kabul edildi' ? 'kabul etti' : 'reddetti';
        $renk = $this->davetiye->durum === 'kabul edildi' ? 'success' : 'error'; // Buton rengi

        return (new MailMessage)
            ->subject('Takım Davet Yanıtı: ' . $this->kabulEdenUser->name)
            ->greeting("Merhaba {$notifiable->name},")
            ->line("Personeliniz {$this->kabulEdenUser->name}, '{$this->davetiye->takim->ad}' takımı için gelen daveti {$durum}.")
            ->action('Takımı Görüntüle', route('takimlar.show', $this->davetiye->takim_id))
            ->level($renk); // Kabul ise yeşil, red ise kırmızı buton/çerçeve
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        // Duruma göre metin, ikon ve renk belirleyelim
        $isKabul = $this->davetiye->durum === 'kabul edildi';

        $durumText = $isKabul ? 'takım davetini kabul etti' : 'takım davetini reddetti';
        $icon = $isKabul ? 'check-circle' : 'x-circle';
        $color = $isKabul ? 'green' : 'red';

        return [
            // Standart Yapı (Diğer bildirimlerinizle uyumlu)
            'message' => "{$this->kabulEdenUser->name}, '{$this->davetiye->takim->ad}' {$durumText}.",

            // Frontendde linkin çalışması için genelde 'action_url' kullanıyoruz
            'action_url' => route('takimlar.show', $this->davetiye->takim_id),

            // Görsel Güzellikler
            'icon' => $icon,
            'color' => $color,
            'takim_id' => $this->davetiye->takim_id,
            'type' => 'davet_yaniti'
        ];
    }
}