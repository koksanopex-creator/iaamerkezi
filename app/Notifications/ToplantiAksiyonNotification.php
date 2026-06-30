<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ToplantiAksiyon;

class ToplantiAksiyonNotification extends Notification
{
    use Queueable;

    public function __construct(public ToplantiAksiyon $aksiyon, public string $atayanAdi) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Yeni Toplantı Aksiyonu Atandı")
            ->greeting("Merhaba {$notifiable->name},")
            ->line("**{$this->atayanAdi}** tarafından bir disiplin kurulu toplantısında size yeni bir görev atandı.")
            ->line("**Görev:** {$this->aksiyon->icerik}")
            ->line("**Toplantı:** {$this->aksiyon->toplanti->baslik}")
            ->action('Toplantı Detayına Git', route('admin.disiplin.kurul.toplanti.show', $this->aksiyon->toplanti_id))
            ->line('Görevlerinizi zamanında tamamlamanızı rica ederiz.');
    }

    public function toArray(object $notifiable): array
    {
        $actionUrl = route('admin.disiplin.kurul.toplanti.show', $this->aksiyon->toplanti_id);

        return [
            'type'       => 'toplanti_aksiyon',
            'category'   => 'disiplin',
            'label'      => 'KURUL TOPLANTISI',
            'message'    => "Toplantıda size yeni bir aksiyon atandı: " . substr($this->aksiyon->icerik, 0, 50) . "...",
            'url'        => $actionUrl,
            'action_url' => $actionUrl,
            'icon'       => 'clipboard-list',
            'color'      => 'violet'
        ];
    }
}
