<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;
use App\Models\Takim;
use App\Models\User;

class IaaProjesineTalepGeldi extends Notification
{
    use Queueable;

    protected $iaa;
    protected $takim;
    protected $lider;
    protected $tip; // 'superadmin', 'bolum_lideri', 'direktor'

    public function __construct(Iaa $iaa, Takim $takim, User $lider, string $tip)
    {
        $this->iaa = $iaa;
        $this->takim = $takim;
        $this->lider = $lider;
        $this->tip = $tip;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'İAA Projesine Yeni Talep: ' . $this->iaa->baslik;
        $greeting = 'Sayın ' . $notifiable->name . ',';
        
        $message = match($this->tip) {
            'superadmin' => "Havuzda bulunan \"{$this->iaa->baslik}\" başlıklı İAA projesine, {$this->lider->name} liderliğindeki \"{$this->takim->ad}\" takımı talep göndermiştir.",
            'bolum_lideri' => "Bölümünüz personeli olan {$this->lider->name} (veya üyesi olduğu) \"{$this->takim->ad}\" takımı, \"{$this->iaa->baslik}\" başlıklı İAA projesine talip olmuştur.",
            'direktor' => "Sorumluluğunuzdaki bir bölümün personeli tarafından temsil edilen \"{$this->takim->ad}\" takımı, \"{$this->iaa->baslik}\" başlıklı İAA projesine talip olmuştur.",
            default => "Yeni bir İAA talebi yapıldı."
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->action('Talepleri Görüntüle', route('admin.iaa-yonetim.talepler', $this->iaa->id))
            ->line('İyi çalışmalar dileriz.');
    }

    public function toDatabase(object $notifiable): array
    {
        $message = match($this->tip) {
            'superadmin' => "\"{$this->iaa->baslik}\" projesine \"{$this->takim->ad}\" takımı talep gönderdi.",
            'bolum_lideri' => "Personeliniz {$this->lider->name}, \"{$this->takim->ad}\" takımı ile bir İAA'ya talip oldu.",
            'direktor' => "Bölümünüzden bir takım \"{$this->iaa->baslik}\" projesine talip oldu.",
            default => "Yeni İAA talebi."
        };

        return [
            'message' => $message,
            'action_url' => route('admin.iaa-yonetim.talepler', $this->iaa->id),
            'icon' => 'shopping-cart',
            'color' => 'blue',
            'type' => 'iaa_talep'
        ];
    }
}
