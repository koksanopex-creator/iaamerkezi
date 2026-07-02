<?php

namespace App\Notifications;

use App\Models\DisiplinKuruluToplanti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class DisiplinToplantiRevizyonNotification extends Notification
{
    use Queueable;

    public DisiplinKuruluToplanti $toplanti;
    public array $oldData;
    public array $newData;
    public string $guncelleyenAdi;

    public function __construct(DisiplinKuruluToplanti $toplanti, array $oldData, array $newData, string $guncelleyenAdi)
    {
        $this->toplanti      = $toplanti;
        $this->oldData       = $oldData;
        $this->newData       = $newData;
        $this->guncelleyenAdi = $guncelleyenAdi;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->name ?? 'Katılımcı';

        return (new MailMessage)
            ->from(config('mail.from.address'), 'Köksan Disiplin Kurulu Sistemi')
            ->subject("Toplantı Revizyonu: {$this->toplanti->baslik}")
            ->markdown('emails.disiplin.toplanti-revizyon', [
                'name' => $name,
                'toplanti' => $this->toplanti,
                'oldData' => $this->oldData,
                'newData' => $this->newData,
                'guncelleyenAdi' => $this->guncelleyenAdi,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $actionUrl = route('admin.disiplin.kurul.toplanti.show', $this->toplanti);
        
        return [
            'type'       => 'disiplin_revizyon',
            'category'   => 'disiplin',
            'label'      => 'TOPLANTI REVİZYONU',
            'message'    => "Disiplin kurulu toplantısı güncellendi: {$this->toplanti->baslik}",
            'url'        => $actionUrl,
            'action_url' => $actionUrl,
            'icon'       => 'refresh',
            'color'      => 'indigo',
        ];
    }
}
