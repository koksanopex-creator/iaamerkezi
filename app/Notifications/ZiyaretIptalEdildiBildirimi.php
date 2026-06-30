<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretIptalEdildiBildirimi extends Notification implements ShouldQueue
{
    use Queueable;

    protected $iaa;
    protected $actorName;
    protected $reason;

    public function __construct(Iaa $iaa, $actorName, $reason)
    {
        $this->iaa = $iaa;
        $this->actorName = $actorName;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Müşteri Ziyareti Planı İptal Edildi')
            ->greeting("Merhaba " . $notifiable->name . ",")
            ->line("{$this->iaa->baslik} başlıklı projede onaylanmış olan müşteri ziyareti planı {$this->actorName} tarafından iptal edilmiştir.")
            ->line("İptal Gerekçesi: " . $this->reason)
            ->action('Projeyi Gör', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar.');
    }

    public function toArray($notifiable): array
    {
        return [
            'iaa_id' => $this->iaa->id,
            'title' => 'Ziyaret İptal Edildi',
            'message' => "{$this->iaa->baslik} başlıklı projede onaylı ziyaret planı {$this->actorName} tarafından iptal edildi. Gerekçe: " . $this->reason,
            'link' => route('proje.workspace.show', $this->iaa->id),
            'type' => 'ziyaret_iptal'
        ];
    }
}
