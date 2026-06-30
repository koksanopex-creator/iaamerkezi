<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Takim;
use App\Models\User;

use Illuminate\Contracts\Queue\ShouldQueue;

class PersonelTakimBildirimi extends Notification implements ShouldQueue
{
    use Queueable;

    protected $personel;
    protected $takim;
    protected $lider; // İşlemi yapan kişi (Sinan)
    protected $tip; // 'davet', 'kabul', 'cikarildi'

    public function __construct(User $personel, Takim $takim, User $lider, $tip)
    {
        $this->personel = $personel;
        $this->takim = $takim;
        $this->lider = $lider;
        $this->tip = $tip;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $subject = '';
        $line = '';

        switch ($this->tip) {
            case 'davet':
                $subject = 'Personeliniz Takıma Davet Edildi';
                $line = "Personeliniz {$this->personel->name}, '{$this->takim->ad}' takımına {$this->lider->name} tarafından davet edildi.";
                break;
            case 'kabul':
                $subject = 'Personeliniz Takıma Katıldı';
                $line = "Personeliniz {$this->personel->name}, '{$this->takim->ad}' takımı için gelen daveti kabul etti.";
                break;
            case 'cikarildi':
                $subject = 'Personeliniz Takımdan Çıkarıldı';
                $line = "Personeliniz {$this->personel->name}, '{$this->takim->ad}' takımından {$this->lider->name} tarafından çıkarıldı.";
                break;
        }

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($line)
            ->action('Takımı Görüntüle', route('takimlar.show', $this->takim->id))
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray($notifiable)
    {
        $mesaj = '';
        $icon = 'info';
        $color = 'blue';

        switch ($this->tip) {
            case 'davet':
                $mesaj = 'Personeliniz olan ' . $this->personel->name . ', ' . $this->takim->ad . ' takımına ' . $this->lider->name . ' tarafından davet edildi.';
                $icon = 'user-plus';
                $color = 'blue';
                break;
            case 'kabul':
                $mesaj = 'Personeliniz olan ' . $this->personel->name . ', ' . $this->takim->ad . ' takımının davetini kabul etti.';
                $icon = 'check-circle';
                $color = 'green';
                break;
            case 'cikarildi':
                $mesaj = 'Personeliniz olan ' . $this->personel->name . ', ' . $this->takim->ad . ' takımından çıkarıldı.';
                $icon = 'user-remove';
                $color = 'red';
                break;
        }

        return [
            'message' => $mesaj,
            'action_url' => route('takimlar.show', $this->takim->id),
            'icon' => $icon,
            'color' => $color,
            'takim_id' => $this->takim->id,
            'personel_id' => $this->personel->id,
            'type' => 'personel_takim_islemi'
        ];
    }
}
