<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;
use App\Models\IaaWorkflowStep;
use App\Models\User;

use Illuminate\Contracts\Queue\ShouldQueue;

class WidgetUserSelectedNotification extends Notification
{
    use Queueable;

    public $iaa;
    public $step;
    public $selectedUser;
    public $sender;
    public $draft;
    public $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Iaa $iaa, IaaWorkflowStep $step, User $selectedUser, User $sender, $draft, $notes)
    {
        $this->iaa = $iaa;
        $this->step = $step;
        $this->selectedUser = $selectedUser;
        $this->sender = $sender;
        $this->draft = $draft;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Proje Sorumluluğu: {$this->iaa->baslik} - {$this->step->name}")
            ->greeting("Merhaba {$notifiable->name},")
            ->line($this->draft);

        if (!empty($this->notes)) {
            $mail->line('**Ek Notlar:**')
                 ->line($this->notes);
        }

        $mail->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
             ->line("Bu bildirim {$this->sender->name} tarafından gönderilmiştir.");

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = "'{$this->iaa->baslik}' projesi '{$this->step->name}' adımında '{$this->selectedUser->name}' için sorumluluk tanımlaması.";
        
        return [
            'icon' => 'fas fa-user-check',
            'renk' => 'primary',
            'message' => $message,
            'url' => route('proje.workspace.show', $this->iaa->id)
        ];
    }
}
