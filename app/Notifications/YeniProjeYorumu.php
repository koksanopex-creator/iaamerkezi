<?php

namespace App\Notifications;

use App\Models\ProjeYorumu; //
use App\Models\IaaWorkflowStep; //
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class YeniProjeYorumu extends Notification
{
    use Queueable;

    protected $yorum;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProjeYorumu $yorum)
    {
        $this->yorum = $yorum;
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
        // Yorumun hangi adıma ait olduğunu buluyoruz
        $step = IaaWorkflowStep::find($this->yorum->iaa_workflow_step_id);
        $stepName = $step ? $step->name : 'bir adıma';

        // Rota adını web.php dosyanızdan aldım
        // Linki projenin çalışma alanına ve ilgili adımın yorumlarına (anchor link) yönlendirelim
        $link = route('proje.workspace.show', $this->yorum->iaa_id) . '#step-comments-' . $this->yorum->iaa_workflow_step_id;

        return [
            // İstediğiniz mesaj
            'message' => "{$this->yorum->yapan_kisi_adi}, '{$stepName}' adımına yeni bir yorum yaptı.",
            'link' => $link,
            'iaa_id' => $this->yorum->iaa_id
        ];
    }
}