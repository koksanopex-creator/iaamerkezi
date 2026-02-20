<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TakimKatilmaIstegi extends Notification
{
    use Queueable;

    protected $davet;
    protected $gonderenUser;
    protected $takim;

    public function __construct($davet)
    {
        $this->davet = $davet;
        $this->gonderenUser = $davet->davetEden; 
        $this->takim = $davet->takim;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            // MESAJ: "Serkan, 'A Takımı'na katılmak istiyor."
            'message' => $this->gonderenUser->name . ', "' . $this->takim->ad . '" takımına katılmak istiyor.',
            
            // DÜZELTME: Lider bu isteği "Katılma İsteklerim" sayfasında görür.
            // Rota listenizde bu sayfa: 'takimlar.isteklerim' (/katilma-isteklerim) olarak görünüyor.
            // Veya direkt takımın yönetim sayfasına da atabilirsiniz: route('takimlar.show', $this->takim->id)
            'action_url' => route('takimlar.isteklerim'), 
            
            'icon' => 'user-add', 
            'color' => 'orange', // Uyarı rengi
            'davet_id' => $this->davet->id,
            'type' => 'takim_istegi'
        ];
    }
}