<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;

class DonusTarihiRevizyonTalebiBildirimi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $visitorName;
    protected $reason;
    protected $roleType; // 'approver', 'planner', 'manager', 'requester'
    protected $approverName;

    public function __construct(Iaa $iaa, $visitorName, $reason, $roleType, $approverName = null)
    {
        $this->iaa = $iaa;
        $this->visitorName = $visitorName;
        $this->reason = $reason;
        $this->roleType = $roleType;
        $this->approverName = $approverName;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = "Dönüş Tarihi Revizyon Talebi - " . $this->iaa->baslik;

        $message = $this->getMessage();

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba " . $notifiable->name . ",")
            ->line($message)
            ->action('Detayları Gör', route('proje.workspace.show', $this->iaa->id))
            ->line('İyi çalışmalar.');
    }

    public function toArray($notifiable): array
    {
        return [
            'iaa_id' => $this->iaa->id,
            'title' => 'Dönüş Tarihi Revizyon Talebi',
            'message' => $this->getMessage(),
            'link' => route('proje.workspace.show', $this->iaa->id),
            'type' => 'ziyaret_revizyon',
            'icon' => 'clock',
            'color' => 'orange'
        ];
    }

    protected function getMessage()
    {
        if ($this->roleType === 'approver') {
            return "{$this->iaa->baslik} Başlıklı Şikayet için 'Ziyarete giden/gidecek personel' {$this->visitorName} tarafından ziyaret planında dönüş tarihi için revizyon talep edildi ve onayınıza sunuldu. Açıklama: \"{$this->reason}\"";
        } elseif ($this->roleType === 'requester') {
            return "{$this->iaa->baslik} başlıklı proje için dönüş tarihi revizyon talebiniz {$this->approverName}'ye iletilmiştir.";
        } elseif ($this->roleType === 'planner') {
            return "{$this->iaa->baslik} başlıklı projede oluşturduğunuz ziyaret planı için 'Ziyarete giden/gidecek personel' {$this->visitorName} tarafından dönüş tarihi revizyonu istenmiştir. Açıklama: \"{$this->reason}\"";
        } else {
            return "{$this->iaa->baslik} başlıklı projede ziyaret planı için 'Ziyarete giden/gidecek personel' {$this->visitorName} tarafından dönüş tarihi revizyonu istenmiştir. Açıklama: \"{$this->reason}\"";
        }
    }
}
