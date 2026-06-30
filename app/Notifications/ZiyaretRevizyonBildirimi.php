<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretRevizyonBildirimi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $actorName;
    protected $type; // 'Revize İsteniyor' or 'Reddedildi'
    protected $reason;

    public function __construct(Iaa $iaa, $actorName, $type, $reason)
    {
        $this->iaa = $iaa;
        $this->actorName = $actorName;
        $this->type = $type;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = $this->type === 'Revize İsteniyor' 
            ? "Müşteri Ziyareti Planı Revize Talebi" 
            : ($this->type === 'Dönüş Tarihi Revizyonu İptal Edildi' 
                ? "Dönüş Tarihi Revizyonu İptal Edildi" 
                : "Müşteri Ziyareti Planı Reddedildi");

        if ($this->type === 'Dönüş Tarihi Revizyonu İptal Edildi') {
            $message = "{$this->iaa->baslik} başlıklı Müşteri Şikayeti Projesinde {$this->actorName} tarafından yapılan dönüş tarihi revizyon talebi iptal edilmiştir.";
        } else {
            $message = "{$this->iaa->baslik} başlıklı Müşteri Şikayeti Projesinde müşteri ziyareti planınız {$this->actorName} tarafından " . strtolower($this->type) . ".";
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba " . $notifiable->name . ",")
            ->line($message)
            ->line("Açıklama: " . $this->reason)
            ->action('Detayları Gör', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar.');
    }

    public function toArray($notifiable): array
    {
        if ($this->type === 'Dönüş Tarihi Revizyonu İptal Edildi') {
            $title = 'Dönüş Tarihi Revizyonu İptal Edildi';
            $message = "{$this->iaa->baslik} başlıklı projede {$this->actorName} tarafından yapılan dönüş tarihi revizyon talebi iptal edilmiştir. Açıklama: " . $this->reason;
        } else {
            $title = $this->type === 'Revize İsteniyor' ? 'Ziyaret Revize Talebi' : 'Ziyaret Reddedildi';
            $message = "{$this->iaa->baslik} başlıklı projede ziyaret planınız {$this->actorName} tarafından " . strtolower($this->type) . ". Açıklama: " . $this->reason;
        }

        return [
            'iaa_id' => $this->iaa->id,
            'title' => $title,
            'message' => $message,
            'link' => route('proje.workspace.show', $this->iaa->id),
            'type' => 'ziyaret_revizyon'
        ];
    }
}
