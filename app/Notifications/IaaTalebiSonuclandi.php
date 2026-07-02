<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;
use App\Models\Takim;

class IaaTalebiSonuclandi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $takim;
    protected $sonuc; // 'onaylandi', 'reddedildi'

    public function __construct(Iaa $iaa, Takim $takim, string $sonuc)
    {
        $this->iaa = $iaa;
        $this->takim = $takim;
        $this->sonuc = $sonuc;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $durumMetni = $this->sonuc === 'onaylandi' ? 'ONAYLANDI' : 'REDDEDİLDİ';
        $subject = "İAA Proje Talebi Sonucu: {$durumMetni}";
        
        $message = "{$this->takim->ad} takımı olarak talip olduğunuz \"{$this->iaa->baslik}\" başlıklı İAA projesi talebiniz yönetici tarafından " . ($this->sonuc === 'onaylandi' ? 'onaylanmıştır. Çalışmalara başlayabilirsiniz.' : 'reddedilmiştir.');

        $actionUrl = $this->sonuc === 'onaylandi' 
            ? route('proje.workspace.show', $this->iaa->id)
            : route('iaa.havuz');

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->action($this->sonuc === 'onaylandi' ? 'Projeye Git' : 'Havuza Git', $actionUrl)
            ->line('İyi çalışmalar dileriz.');
    }

    public function toDatabase(object $notifiable): array
    {
        $statusText = $this->sonuc === 'onaylandi' ? 'onaylandı' : 'reddedildi';
        $message = "\"{$this->iaa->baslik}\" projesi talebiniz {$statusText}.";

        $actionUrl = $this->sonuc === 'onaylandi' 
            ? route('proje.workspace.show', $this->iaa->id)
            : route('iaa.havuz');

        return [
            'message' => $message,
            'action_url' => $actionUrl,
            'icon' => $this->sonuc === 'onaylandi' ? 'check-circle' : 'times-circle',
            'color' => $this->sonuc === 'onaylandi' ? 'green' : 'red',
            'type' => 'iaa_talep_sonuc'
        ];
    }
}
