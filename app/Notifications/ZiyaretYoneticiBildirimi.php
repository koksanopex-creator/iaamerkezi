<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class ZiyaretYoneticiBildirimi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $visitorName;
    protected $status; // 'Onaylandı', 'Reddedildi', 'Revize İsteniyor'
    protected $reason;

    public function __construct(Iaa $iaa, $visitorName, $status, $reason = null)
    {
        $this->iaa = $iaa;
        $this->visitorName = $visitorName;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = match($this->status) {
            'Onaylandı' => 'Personelinizin Müşteri Ziyareti Onaylandı',
            'Revize İsteniyor' => 'Personelinizin Ziyaret Planında Revize İstendi',
            'Reddedildi' => 'Personelinizin Ziyaret Planı Reddedildi',
            default => 'Personelinizin Ziyareti Hakkında'
        };

        $message = "Bölüm personellerinizden {$this->visitorName}, \"{$this->iaa->baslik}\" başlıklı Müşteri Şikayeti Projesi kapsamında müşteri ziyareti gerçekleştirecektir ve bu ziyaret planı {$this->status}.";
        
        if ($this->status === 'Onaylandı') {
            $message = "Bölüm personellerinizden {$this->visitorName}, \"{$this->iaa->baslik}\" başlıklı Müşteri Şikayeti Projesi kapsamında bir müşteri ziyareti gerçekleştirecektir. Ziyaret planı onaylanmıştır.";
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($message);

        if ($this->reason) {
            $mail->line('Açıklama/Gerekçe: ' . $this->reason);
        }

        return $mail->action('Projeyi Görüntüle', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'visit_manager_notification',
            'title' => 'Personel Ziyaret Durumu: ' . $this->status,
            'message' => "Personeliniz {$this->visitorName}'in dahil olduğu ziyaret planı {$this->status}.",
            'iaa_id' => $this->iaa->id,
            'url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => match($this->status) {
                'Onaylandı' => 'check-circle',
                'Revize İsteniyor' => 'refresh-ccw',
                'Reddedildi' => 'x-circle',
                default => 'info'
            },
            'color' => match($this->status) {
                'Onaylandı' => 'emerald',
                'Revize İsteniyor' => 'amber',
                'Reddedildi' => 'red',
                default => 'blue'
            }
        ];
    }
}
