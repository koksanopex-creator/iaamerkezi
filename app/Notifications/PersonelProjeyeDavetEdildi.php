<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Iaa;
use App\Models\User;

class PersonelProjeyeDavetEdildi extends Notification implements ShouldQueue
{
    use Queueable;

    protected $iaa;
    protected $personel;
    protected $tip; // 'davet' veya 'cikarildi'

    // Kurucu metoda $tip parametresi ekledik, varsayılan 'davet'
    public function __construct(Iaa $iaa, User $personel, $tip = 'davet')
    {
        $this->iaa = $iaa;
        $this->personel = $personel;
        $this->tip = $tip;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        // Mesajı tipe göre değiştiriyoruz
        $konu = $this->tip == 'davet' 
            ? 'Personelinize Proje Daveti Geldi' 
            : 'Personeliniz Proje Ekibinden Çıkarıldı';

        $mesaj = $this->tip == 'davet'
            ? "Bölümünüzden {$this->personel->name}, '{$this->iaa->baslik}' isimli projeye ekip üyesi olarak davet edildi."
            : "Bölümünüzden {$this->personel->name}, '{$this->iaa->baslik}' isimli projenin ekibinden ÇIKARILDI.";

        return (new MailMessage)
                    ->subject($konu)
                    ->greeting("Merhaba {$notifiable->name},")
                    ->line($mesaj)
                    ->action('Projeyi İncele', route('proje.workspace.show', $this->iaa->id));
    }

    public function toArray($notifiable)
    {
        $mesaj = $this->tip == 'davet'
            ? "Personeliniz {$this->personel->name}, '{$this->iaa->baslik}' projesine davet edildi."
            : "Personeliniz {$this->personel->name}, '{$this->iaa->baslik}' projesinden ÇIKARILDI.";

        $icon = $this->tip == 'davet' ? 'user-plus' : 'user-minus';
        $color = $this->tip == 'davet' ? 'blue' : 'red';

        return [
            'message' => $mesaj,
            'action_url' => route('proje.workspace.show', $this->iaa->id),
            'icon' => $icon,
            'color' => $color,
            'iaa_id' => $this->iaa->id
        ];
    }
}