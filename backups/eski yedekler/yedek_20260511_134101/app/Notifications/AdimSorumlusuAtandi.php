<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

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
        return ['database'];
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