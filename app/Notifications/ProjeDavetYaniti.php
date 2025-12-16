<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;
use App\Models\User;

class ProjeDavetYaniti extends Notification implements ShouldQueue
{
    use Queueable;

    protected $iaa;
    protected $user;
    protected $yanit; // 'kabul' veya 'red'

    public function __construct(Iaa $iaa, User $user, $yanit)
    {
        $this->iaa = $iaa;
        $this->user = $user;
        $this->yanit = $yanit;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        // Alıcı, projenin lideri mi?
        // (Not: $this->iaa->atananTakim ilişkisi dolu gelmeli, yoksa hata alabiliriz. 
        // Genelde Notification serialize edilirken id tutar, tekrar çeker.)
        
        $isLider = false;
        if ($this->iaa->atananTakim && $this->iaa->atananTakim->lider_user_id == $notifiable->id) {
            $isLider = true;
        }

        $durumText = ($this->yanit == 'kabul') ? 'KABUL ETTİ' : 'REDDETTİ';

        if ($isLider) {
            // ERHAN CESUR (LİDER) İÇİN MESAJ
            $konu = 'Proje Davet Yanıtı: ' . $this->user->name;
            $mesaj = "{$this->user->name}, '{$this->iaa->baslik}' projesi için gönderdiğiniz daveti {$durumText}.";
        } else {
            // EMRAH AL (MÜDÜR) İÇİN MESAJ
            $konu = 'Personel Proje Durumu: ' . $this->user->name;
            $mesaj = "Personeliniz {$this->user->name}, '{$this->iaa->baslik}' projesine katılım davetini {$durumText}.";
        }

        return (new MailMessage)
            ->subject($konu)
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($mesaj)
            ->action('Projeye Git', route('proje.workspace.show', $this->iaa->id));
    }

    public function toArray($notifiable)
    {
        // Alıcı Lider mi kontrolü (Veritabanı bildirimi için)
        $isLider = false;
        if ($this->iaa->atananTakim && $this->iaa->atananTakim->lider_user_id == $notifiable->id) {
            $isLider = true;
        }

        $durumText = ($this->yanit == 'kabul') ? 'KABUL ETTİ' : 'REDDETTİ';
        $renk = ($this->yanit == 'kabul') ? 'green' : 'red';
        $icon = ($this->yanit == 'kabul') ? 'check-circle' : 'x-circle';

        if ($isLider) {
            // LİDER GÖRÜNÜMÜ
            $message = "{$this->user->name}, '{$this->iaa->baslik}' proje davetinizi {$durumText}.";
        } else {
            // MÜDÜR GÖRÜNÜMÜ
            $message = "Personeliniz {$this->user->name}, '{$this->iaa->baslik}' proje davetini {$durumText}.";
        }

        return [
            'message' => $message,
            'action_url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => $icon,
            'color' => $renk,
            'user_id' => $this->user->id
        ];
    }
}