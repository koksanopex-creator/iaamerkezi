<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretciAtandiBildirimi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $assignerName;

    public function __construct(Iaa $iaa, $assignerName)
    {
        $this->iaa = $iaa;
        $this->assignerName = $assignerName;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Proje Ziyaretçisi Olarak Atandınız')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->assignerName . ' tarafından "' . $this->iaa->baslik . '" başlıklı projede ziyaretçi (müfettiş/incelemeci) olarak atandınız.')
            ->line('Proje çalışma alanına giderek ziyaret planını inceleyebilir, bulgu ve sonuçları girebilirsiniz.')
            ->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'visitor_assigned',
            'title' => 'Ziyaretçi Olarak Atandınız',
            'message' => "{$this->assignerName} tarafından bir projede ziyaretçi olarak görevlendirildiniz.",
            'iaa_id' => $this->iaa->id,
            'url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => 'user-check',
            'color' => 'blue'
        ];
    }
}
