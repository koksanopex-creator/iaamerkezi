<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HukukYetkiGuncellendiNotification extends Notification
{
    use Queueable;

    public string $adminName;
    public string $affectedUserName;
    public string $type; // 'role', 'permission'
    public string $targetName; // 'Hukuk Yöneticisi', 'Disiplin Ayarları' vb.
    public string $action; // 'atandi', 'kaldirildi', 'verildi', 'alindi'
    public bool $isForAdmin; // Diğer adminlere giden bildirim mi?

    public function __construct(string $adminName, string $affectedUserName, string $type, string $targetName, string $action, bool $isForAdmin = false)
    {
        $this->adminName = $adminName;
        $this->affectedUserName = $affectedUserName;
        $this->type = $type;
        $this->targetName = $targetName;
        $this->action = $action;
        $this->isForAdmin = $isForAdmin;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = "Hukuk Yetki Matrisi Güncellemesi";
        
        if ($this->isForAdmin) {
            $subject = "Yönetici Bilgilendirme: Yetki Değişikliği";
            $message = "**{$this->adminName}**, **{$this->affectedUserName}** isimli kullanıcının **{$this->targetName}** yetkisini/rolünü **{$this->action}**.";
        } else {
            $message = "**{$this->adminName}** tarafından size **{$this->targetName}** yetkisi/rolü **{$this->action}**.";
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba " . $notifiable->name . ",")
            ->line($message)
            ->line("Bu değişiklik sistem üzerinde anlık olarak tanımlanmıştır.")
            ->action('Matrisi Görüntüle', url('/admin/disiplin/hukuk-yetki-matrisi'))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toArray(object $notifiable): array
    {
        if ($this->isForAdmin) {
            $message = "{$this->adminName}, {$this->affectedUserName} kullanıcısının {$this->targetName} yetkisini {$this->action}.";
            $icon = 'shield-exclamation';
            $color = 'indigo';
        } else {
            $message = "Size {$this->targetName} yetkisi {$this->action}.";
            $icon = ($this->action === 'atandi' || $this->action === 'verildi') ? 'shield-check' : 'shield-off';
            $color = ($this->action === 'atandi' || $this->action === 'verildi') ? 'emerald' : 'rose';
        }

        return [
            'message' => $message,
            'url' => url('/admin/disiplin/hukuk-yetki-matrisi'),
            'icon' => $icon,
            'color' => $color,
        ];
    }
}
