<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PersonelTutanakOlusturduBildirimi extends Notification
{
    use Queueable;

    public $case;

    public function __construct($case)
    {
        $this->case = $case;
    }

    public function via($notifiable): array
    {
        return ['database']; // Sadece zile düşsün (İstersen mail de ekleyebilirsin)
    }

    public function toArray($notifiable): array
    {
        // ÖZEL MESAJ FORMATI:
        // "Personeliniz Serkan Akardeniz, Furkan Özbiçer hakkında bir tutanak tuttu."
        
        $reporterName = $this->case->reporter->name; // Serkan
        $targetName = $this->case->user->name;       // Furkan

        return [
            'message' => "Personeliniz {$reporterName}, {$targetName} hakkında bir tutanak tuttu. İncelemek ister misiniz?",
            'url' => route('admin.disiplin.show', $this->case->id),
            'icon' => 'user-group', // Grup ikonu (Personelim anlamında)
            'color' => 'yellow',    // Uyarı rengi (Turuncu/Sarı)
            'case_id' => $this->case->id
        ];
    }
}