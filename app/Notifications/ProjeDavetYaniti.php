<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;
use App\Models\User;

class ProjeDavetYaniti extends Notification
{
    use Queueable;

    protected $iaa;
    protected $user;
    protected $yanit; // 'kabul' veya 'red'

    public function __construct(Iaa $iaa, User $user, $yanit)
    {
        $this->iaa = $iaa;
        $this->user = $user;
        $this->yanit = $yanit;
    }

    public function via($notifiable)
    {
        return ['database']; // Sadece panel bildirimi
    }

    public function toArray($notifiable)
    {
        $durumText = ($this->yanit == 'kabul') ? 'KABUL ETTİ' : 'REDDETTİ';
        $renk = ($this->yanit == 'kabul') ? 'green' : 'red';
        $icon = ($this->yanit == 'kabul') ? 'check-circle' : 'x-circle';

        return [
            'message' => "{$this->user->name}, '{$this->iaa->baslik}' proje davetinizi {$durumText}.",
            // Reddedilse bile lider projeye gidip modalı açıp görebilir, o yüzden link proje linki
            'action_url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => $icon,
            'color' => $renk,
            'user_id' => $this->user->id
        ];
    }
}