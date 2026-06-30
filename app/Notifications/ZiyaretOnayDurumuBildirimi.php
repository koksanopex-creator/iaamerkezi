<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretOnayDurumuBildirimi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $approverName;
    protected $status; // 'onaylanmıştır', 'revize istenmiştir', 'reddedilmiştir'
    protected $reason;

    public function __construct(Iaa $iaa, $approverName, $status, $reason = null)
    {
        $this->iaa = $iaa;
        $this->approverName = $approverName;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusText = match($this->status) {
            'Onaylandı' => 'onaylanmıştır',
            'Revizyon Bekliyor' => 'için revizyon istenmiştir',
            'Reddedildi' => 'reddedilmiştir',
            default => 'güncellenmiştir'
        };

        $message = '"' . $this->iaa->baslik . '" başlıklı Müşteri Şikayeti Projesinde müşteri ziyareti planınız ' . $this->approverName . ' tarafından ' . $statusText . '.';

        $mail = (new MailMessage)
            ->subject('Ziyaret Planı Onay Durumu: ' . $this->status)
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($message);

        if ($this->reason) {
            $mail->line('Gerekçe/Not: ' . $this->reason);
        }

        return $mail->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable): array
    {
        $statusText = match($this->status) {
            'Onaylandı' => 'onaylanmıştır',
            'Revizyon Bekliyor' => 'revize istenmiştir',
            'Reddedildi' => 'reddedilmiştir',
            default => 'güncellenmiştir'
        };

        return [
            'type' => 'visit_status_updated',
            'title' => 'Ziyaret Planı Durumu: ' . $this->status,
            'message' => "{$this->iaa->baslik} başlıklı Müşteri Şikayeti Projesinde müşteri ziyareti planınız {$this->approverName} tarafından {$statusText}.",
            'iaa_id' => $this->iaa->id,
            'url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => match($this->status) {
                'Onaylandı' => 'check-circle',
                'Revizyon Bekliyor' => 'refresh-ccw',
                'Reddedildi' => 'x-circle',
                default => 'info'
            },
            'color' => match($this->status) {
                'Onaylandı' => 'emerald',
                'Revizyon Bekliyor' => 'amber',
                'Reddedildi' => 'red',
                default => 'blue'
            }
        ];
    }
}
