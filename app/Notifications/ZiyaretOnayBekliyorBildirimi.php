<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretOnayBekliyorBildirimi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $senderName;

    public function __construct(Iaa $iaa, $senderName)
    {
        $this->iaa = $iaa;
        $this->senderName = $senderName;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ziyaret Planı Onayınızı Bekliyor')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->iaa->baslik . ' başlıklı Müşteri Şikayeti Projesinde müşteri ziyareti planlanmıştır.')
            ->line('Ziyaret detaylarını görmek ve onay veya red vermek için aşağıdaki butonu kullanabilirsiniz.')
            ->action('Ziyareti İncele ve Onayla', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'visit_approval_pending',
            'title' => 'Ziyaret Onayı Bekleniyor',
            'message' => "{$this->iaa->baslik} başlıklı Müşteri Şikayeti Projesinde müşteri ziyareti planlanmıştır. Ziyaret detaylarını görmek ve onay veya red vermek için tıklayınız.",
            'iaa_id' => $this->iaa->id,
            'url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => 'clipboard-check',
            'color' => 'indigo'
        ];
    }
}
