<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YeniDisiplinYorumu extends Notification
{
    use Queueable;

    protected $case;
    protected $commenterName;

    public function __construct($case, $commenterName)
    {
        $this->case = $case;
        $this->commenterName = $commenterName;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        // 1. Link Mantığı (Role Göre Rota Belirleme)
        $url = '';
        if ($notifiable->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Bölüm Lideri'])) {
            // Yöneticiler için Admin rotası
            $url = route('admin.disiplin.show', $this->case->id);
        } else {
            // Personel (Cihangir) için Standart rota
            // DİKKAT: 'disiplin.show' rotasının web.php'de tanımlı olduğundan emin ol.
            // Eğer yoksa aşağıda web.php'ye ekleyeceğiz.
            $url = route('disiplin.show', $this->case->id);
        }

        // 2. Mesaj Mantığı (Daha Açıklayıcı)
        // Örn: "Hukuk Admini, Cihangir Kaplan'ın (#2) tutanağına yeni bir yorum yaptı."
        $message = $this->commenterName . ', ' . $this->case->user->name . "\'ın (#" . $this->case->id . ") tutanağına yeni bir yorum yaptı.";

        return [
            'message' => $message,
            'url' => $url,
            'icon' => 'chat-alt-2', // İkonu biraz daha modern yapalım
            'color' => 'indigo'
        ];
    }
}