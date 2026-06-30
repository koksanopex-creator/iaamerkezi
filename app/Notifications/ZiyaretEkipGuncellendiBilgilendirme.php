<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretEkipGuncellendiBilgilendirme extends Notification
{
    use Queueable;

    protected $iaa;
    protected $addedNamesStr;

    public function __construct(Iaa $iaa, $addedNamesStr)
    {
        $this->iaa = $iaa;
        $this->addedNamesStr = $addedNamesStr;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    protected function getMessage()
    {
        return "{$this->iaa->baslik} başlıklı müşteri şikayeti için planlanan ziyaretinize {$this->addedNamesStr} adlı kişi(ler) katılım sağlamak üzere eklenmiştir.";
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ziyaret Ekibi Güncellendi')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->getMessage())
            ->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'visit_team_updated_info',
            'title' => 'Ziyaret Ekibine Yeni Kişi Eklendi',
            'message' => $this->getMessage(),
            'iaa_id' => $this->iaa->id,
            'url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => 'users',
            'color' => 'blue'
        ];
    }
}
