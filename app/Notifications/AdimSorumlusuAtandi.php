<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdimSorumlusuAtandi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $step;
    protected $sorumlu;
    protected $lider;

    public function __construct($iaa, $step, $sorumlu, $lider)
    {
        $this->iaa = $iaa;
        $this->step = $step;
        $this->sorumlu = $sorumlu;
        $this->lider = $lider;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $date = now()->format('d.m.Y H:i');
        
        return (new MailMessage)
            ->subject('Yeni Proje Adımı Ataması: ' . $this->iaa->baslik)
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line("'{$this->iaa->baslik}' projesinde yeni bir adım ataması yapıldı.")
            ->line("Adım: " . $this->step->name)
            ->line("Atayan: " . $this->lider->name)
            ->line("Atama Tarihi: " . $date)
            ->action('Projeye Git', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "{$this->lider->name}, '{$this->step->name}' adımını {$this->sorumlu->name}'a atadı.",
            'action_url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => 'user',
            'color' => 'indigo',
            'iaa_id' => $this->iaa->id
        ];
    }
}