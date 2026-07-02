<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Queue\ShouldQueue; // KUYRUK İÇİN

class TakimaDavetEdildi extends Notification
{
    use Queueable;

    protected $davet;
    protected $takim;
    protected $davetEden;

    public function __construct($davet)
    {
        $this->davet = $davet;
        $this->takim = $davet->takim;
        $this->davetEden = $davet->davetEden;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Takım Daveti: ' . $this->takim->ad)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($this->davetEden->name . ' sizi "' . $this->takim->ad . '" takımına davet etti.')
            ->action('Davetleri Görüntüle', route('takimlar.davetlerim'))
            ->line('Lütfen sisteme giriş yaparak daveti onaylayınız veya reddediniz.')
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray($notifiable)
    {
        return [
            // MESAJ: "Sinan sizi 'Puan Avcıları' takımına davet etti."
            'message' => $this->davetEden->name . ' sizi "' . $this->takim->ad . '" takımına davet etti.',

            // ROTA: Kullanıcı daveti kabul etmek için kendi davetlerim sayfasına gider.
            'action_url' => route('takimlar.davetlerim'),

            'icon' => 'mail', // Davet ikonu
            'color' => 'blue',
            'davet_id' => $this->davet->id,
            'type' => 'takim_daveti'
        ];
    }
}