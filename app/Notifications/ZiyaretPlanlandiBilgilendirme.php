<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretPlanlandiBilgilendirme extends Notification
{
    use Queueable;

    protected $iaa;
    protected $type; // 'leader', 'quality', 'visitor'

    public function __construct(Iaa $iaa, $type)
    {
        $this->iaa = $iaa;
        $this->type = $type;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    protected function getMessage()
    {
        return match($this->type) {
            'leader' => "{$this->iaa->baslik} başlıklı bölümünüze ait müşteri şikayeti için bir müşteri ziyareti planlanmıştır. Detaylar için tıklayınız",
            'quality' => "{$this->iaa->baslik} başlıklı sorumlu olduğunuz bölüme ait müşteri şikayeti için bir müşteri ziyareti planlanmıştır. Detaylar için tıklayınız",
            'visitor' => "{$this->iaa->baslik} başlıklı müşteri şikayeti için bir ziyaret gerçekleştirmeniz gerekmektedir. Detaylar için tıklayınız",
            default => "{$this->iaa->baslik} projesi için müşteri ziyareti planlanmıştır."
        };
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Müşteri Ziyareti Planlandı')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->getMessage())
            ->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'visit_planned_info',
            'title' => 'Müşteri Ziyareti Planlandı',
            'message' => $this->getMessage(),
            'iaa_id' => $this->iaa->id,
            'url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => 'calendar-days',
            'color' => 'blue'
        ];
    }
}
