<?php

namespace App\Notifications;

use App\Models\Iaa;
use App\Models\IaaWorkflowStep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjeAdimiTamamlandiMusteri extends Notification implements ShouldQueue
{
    use Queueable;

    protected $iaa;
    protected $step;
    protected $completedCount;
    protected $totalCount;

    /**
     * Create a new notification instance.
     */
    public function __construct(Iaa $iaa, IaaWorkflowStep $step, int $completedCount, int $totalCount)
    {
        $this->iaa = $iaa;
        $this->step = $step;
        $this->completedCount = $completedCount;
        $this->totalCount = $totalCount;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $baslik = $this->iaa->baslik;
        $stepName = $this->step->name;
        $progress = "{$this->completedCount}/{$this->totalCount}";
        
        return (new MailMessage)
            ->subject("Proje Adımı Tamamlandı: #{$this->iaa->id}")
            ->greeting("Merhaba {$notifiable->name},")
            ->line("#{$this->iaa->id} Nolu **\"{$baslik}\"** başlıklı şikayetinizin **{$stepName}** adımı tamamlanmıştır.")
            ->line("Mevcut süreç ilerlemesi: **{$progress}**")
            ->action('Proje Alanını Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $baslik = $this->iaa->baslik;
        $stepName = $this->step->name;
        $progress = "{$this->completedCount}/{$this->totalCount}";

        return [
            'message' => "#{$this->iaa->id} Nolu **\"{$baslik}\"** başlıklı şikayetinizin **{$stepName}** adımı tamamlanmıştır. (Süreç: {$progress})",
            'link' => route('proje.workspace.show', $this->iaa->id),
            'iaa_id' => $this->iaa->id,
            'step_id' => $this->step->id
        ];
    }
}
