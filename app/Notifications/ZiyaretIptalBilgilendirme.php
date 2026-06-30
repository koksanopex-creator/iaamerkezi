<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretIptalBilgilendirme extends Notification
{
    use Queueable;

    protected $iaa;
    protected $type; // 'visitor_cancelled'
    protected $revizyonNotu;

    public function __construct(Iaa $iaa, $type = 'visitor_cancelled', $revizyonNotu = null)
    {
        $this->iaa = $iaa;
        $this->type = $type;
        $this->revizyonNotu = $revizyonNotu;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    protected function getMessage()
    {
        $message = "{$this->iaa->baslik} başlıklı müşteri şikayeti için daha önce planlanan ziyaretinize katılımınız iptal edilmiştir.";
        if ($this->revizyonNotu) {
            $message .= " Revizyon Sonrası Not: \"{$this->revizyonNotu}\"";
        }
        return $message;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Müşteri Ziyareti İptal Edildi')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->getMessage())
            ->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'visit_cancelled_info',
            'title' => 'Ziyaret İptal Edildi',
            'message' => $this->getMessage(),
            'iaa_id' => $this->iaa->id,
            'url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => 'x-circle',
            'color' => 'red'
        ];
    }
}
