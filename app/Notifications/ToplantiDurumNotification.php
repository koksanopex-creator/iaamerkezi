<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DisiplinKuruluToplanti;

class ToplantiDurumNotification extends Notification
{
    use Queueable;

    public function __construct(
        public DisiplinKuruluToplanti $toplanti, 
        public string $durum, 
        public string $sebep
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $baslik = $this->durum === 'ertelendi' ? "Toplantı Ertelendi" : "Toplantı İptal Edildi";
        $renk = $this->durum === 'ertelendi' ? "amber" : "rose";

        return (new MailMessage)
            ->subject("{$baslik}: {$this->toplanti->baslik}")
            ->greeting("Merhaba {$notifiable->name},")
            ->line("**{$this->toplanti->baslik}** konulu toplantı {$this->durum} durumuna alınmıştır.")
            ->line("**Sebep:** {$this->sebep}")
            ->when($this->durum === 'ertelendi', function($mail) {
                return $mail->line("**Yeni Tarih:** " . $this->toplanti->baslangic_tarihi->format('d.m.Y H:i'));
            })
            ->action('Detayları Görüntüle', route('admin.disiplin.kurul.toplanti.show', $this->toplanti->id));
    }

    public function toArray(object $notifiable): array
    {
        $msg = $this->durum === 'ertelendi' 
            ? "Toplantı ertelendi: " . $this->toplanti->baslik 
            : "Toplantı iptal edildi: " . $this->toplanti->baslik;

        $actionUrl = route('admin.disiplin.kurul.toplanti.show', $this->toplanti->id);

        return [
            'type'       => 'toplanti_durum',
            'category'   => 'disiplin',
            'label'      => 'KURUL TOPLANTISI',
            'message'    => $msg,
            'url'        => $actionUrl,
            'action_url' => $actionUrl,
            'icon'       => $this->durum === 'ertelendi' ? 'clock' : 'x-circle',
            'color'      => $this->durum === 'ertelendi' ? 'amber' : 'rose'
        ];
    }
}
