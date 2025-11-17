<?php

namespace App\Notifications;

use App\Models\TakimDavetiyesi; //
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TakimDavetiYanitlandi extends Notification
{
    use Queueable;

    protected $davetiye;
    protected $yanitlayanUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(TakimDavetiyesi $davetiye, User $yanitlayanUser)
    {
        $this->davetiye = $davetiye;
        $this->yanitlayanUser = $yanitlayanUser;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        // Rota adını web.php dosyanızdan aldım
        $link = route('takimlar.show', $this->davetiye->takim_id);
        
        // "kabul edildi" veya "reddedildi" durumuna göre metni ayarla
        $durumText = $this->davetiye->durum === 'kabul edildi' ? 'takımınıza katıldı' : 'takım davetinizi reddetti';
        $takimAdi = $this->davetiye->takim->ad;

        return [
            // İstediğiniz mesaj
            'message' => "{$this->yanitlayanUser->name}, '{$takimAdi}' {$durumText}.",
            'link' => $link,
            'takim_id' => $this->davetiye->takim_id
        ];
    }
}