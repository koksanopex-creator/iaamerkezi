<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Takim;
use App\Models\User;

use Illuminate\Contracts\Queue\ShouldQueue;

class TakimdanCikarildi extends Notification implements ShouldQueue
{
    use Queueable;

    protected $takim;
    protected $lider;

    public function __construct(Takim $takim, User $lider)
    {
        $this->takim = $takim;
        $this->lider = $lider;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Takımdan Çıkarıldınız: ' . $this->takim->ad)
            ->greeting("Merhaba {$notifiable->name},")
            ->line("Üzgünüz, '{$this->takim->ad}' takımından takım lideri {$this->lider->name} tarafından çıkarıldınız.")
            ->action('Takımları Görüntüle', route('takimlar.index'))
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->lider->name . ', sizi "' . $this->takim->ad . '" takımından çıkardı.',
            'action_url' => route('takimlar.index'),
            'icon' => 'user-remove',
            'color' => 'red',
            'takim_id' => $this->takim->id
        ];
    }
}
