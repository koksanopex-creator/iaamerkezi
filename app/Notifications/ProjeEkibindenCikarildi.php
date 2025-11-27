<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;
use App\Models\User;

class ProjeEkibindenCikarildi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $lider;

    public function __construct(Iaa $iaa, User $lider)
    {
        $this->iaa = $iaa;
        $this->lider = $lider;
    }

    public function via($notifiable)
    {
        return ['database']; // Sadece panel bildirimi (İstersen 'mail' de ekleyebilirsin)
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->lider->name . ', sizi "' . \Illuminate\Support\Str::limit($this->iaa->baslik, 25) . '" projesinin ekibinden çıkardı.',
            'action_url' => route('dashboard'), // Artık projeye giremeyeceği için Dashboard'a yönlendiriyoruz
            'icon' => 'user-remove', // Özel ikon (blade tarafında tanımlıysa) veya 'x-circle'
            'color' => 'red', // Kırmızı uyarı rengi
            'iaa_id' => $this->iaa->id
        ];
    }
}