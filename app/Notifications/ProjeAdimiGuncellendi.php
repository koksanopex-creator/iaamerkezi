<?php

namespace App\Notifications;

use App\Models\Iaa;
use App\Models\IaaWorkflowStep; //
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjeAdimiGuncellendi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $step;
    protected $guncelleyenUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(Iaa $iaa, IaaWorkflowStep $step, User $guncelleyenUser)
    {
        $this->iaa = $iaa;
        $this->step = $step;
        $this->guncelleyenUser = $guncelleyenUser;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        // Rota adını web.php dosyanızdan aldım
        // Linki projenin çalışma alanına ve ilgili adıma (anchor link) yönlendirelim
        $link = route('proje.workspace.show', $this->iaa->id) . '#step-' . $this->step->id;

        return [
            // İstediğiniz mesaj
            'message' => "{$this->guncelleyenUser->name}, '{$this->iaa->baslik}' projesinin '{$this->step->name}' adımını güncelledi.",
            'link' => $link,
            'iaa_id' => $this->iaa->id
        ];
    }
}