<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ProjeEkipDaveti extends Notification
{
    use Queueable;

    protected $iaa;
    protected $lider;

    public function __construct(Iaa $iaa, $lider)
    {
        $this->iaa = $iaa;
        $this->lider = $lider;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // Hem panel bildirimi hem mail
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Proje Ekibi Daveti: ' . $this->iaa->baslik)
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->lider->name . ' sizi "' . $this->iaa->baslik . '" projesinin çalışma ekibine davet etti.')
            ->action('Daveti İncele', route('dashboard')) // Dashboard'a yönlendiriyoruz, orada görecek
            ->line('Lütfen panelinizden daveti onaylayın veya reddedin.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->lider->name . ' sizi "' . \Illuminate\Support\Str::limit($this->iaa->baslik, 20) . '" projesine davet etti.',
            'action_url' => route('dashboard'), // Davetleri dashboard'da göstereceğiz
            'icon' => 'user-add',
            'color' => 'indigo',
            'iaa_id' => $this->iaa->id,
            'type' => 'proje_daveti' // Bunu dashboard'da yakalayacağız
        ];
    }
}