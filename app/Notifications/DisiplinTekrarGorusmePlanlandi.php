<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class DisiplinTekrarGorusmePlanlandi extends Notification
{
    use Queueable;

    public $case;
    public $user;

    public function __construct($case, $user)
    {
        $this->case = $case;
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.disiplin.show', $this->case->id) . '?tab=kurul';
        
        $roleName = $this->user->roles->first() ? $this->user->roles->first()->name : 'Yetkili';
        $toplantiTarihi = $this->case->toplanti_tarihi
            ? Carbon::parse($this->case->toplanti_tarihi)->format('d.m.Y H:i')
            : 'Belirtilmedi';
        
        $gorusmeSayisi = ($this->case->rediscussion_count + 1);
        $ihlal = $this->case->behavior ? $this->case->behavior->tanim : 'Belirtilmemiş';

        $message = "{$this->case->user->name} isimli personelin {$this->case->id} id numaralı {$ihlal} ihlali disiplin dosyası {$gorusmeSayisi}. kez görüşülmek üzere {$toplantiTarihi} tarihine {$roleName} {$this->user->name} tarafından planlanmıştır.";

        return (new MailMessage)
            ->subject("Disiplin Dosyası {$gorusmeSayisi}. Kez Görüşülecek — Dosya #{$this->case->id}")
            ->greeting("Merhaba {$notifiable->name},")
            ->line($message)
            ->action('Disiplin Kurulu Odasına Git', $url)
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        $roleName = $this->user->roles->first() ? $this->user->roles->first()->name : 'Yetkili';
        $toplantiTarihi = $this->case->toplanti_tarihi
            ? Carbon::parse($this->case->toplanti_tarihi)->format('d.m.Y H:i')
            : 'Belirtilmedi';
            
        $gorusmeSayisi = ($this->case->rediscussion_count + 1);
        $ihlal = $this->case->behavior ? $this->case->behavior->tanim : 'Belirtilmemiş';
        
        $message = "{$this->case->user->name} isimli personelin {$this->case->id} id numaralı {$ihlal} ihlali disiplin dosyası {$gorusmeSayisi}. kez görüşülmek üzere {$toplantiTarihi} tarihine {$roleName} {$this->user->name} tarafından planlanmıştır.";

        return [
            'message' => $message,
            'url' => route('admin.disiplin.show', $this->case->id) . '?tab=kurul',
            'icon' => 'calendar',
            'color' => 'amber',
            'case_id' => $this->case->id
        ];
    }
}
