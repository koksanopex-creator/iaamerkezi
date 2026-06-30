<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class DisiplinItirazHakkiDogdu extends Notification
{
    use Queueable;

    public $case;
    public $type; // 'personel', 'lider', 'reporter'

    public function __construct($case, string $type)
    {
        $this->case = $case;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // tckimlik@koksan.com ise mail göndermiyoruz
        if ($notifiable->email && !Str::contains($notifiable->email, 'tckimlik@koksan.com')) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Disiplin Dosyası İtiraz Hakkı Bilgilendirmesi';
        $greeting = "Merhaba {$notifiable->name},";
        $url = route($this->type === 'personel' ? 'disiplin.show' : 'admin.disiplin.show', $this->case->id);
        
        $message = new MailMessage();
        $message->subject($subject)->greeting($greeting);

        if ($this->type === 'personel') {
            $message->line("#{$this->case->id} numaralı disiplin dosyanızda verilen ceza onaylanmıştır.")
                    ->line("Bu karara karşı **3 iş günü** içinde itiraz etme hakkınız bulunmaktadır.")
                    ->action('İtiraz Etmek İçin Tıklayın', $url);
        } elseif ($this->type === 'lider') {
            $message->line("Bölümünüz personeli **{$this->case->user->name}**'in #{$this->case->id} numaralı dosyasından aldığı ceza onaylanmıştır.")
                    ->line("Personeliniz adına **3 iş günü** içinde itiraz hakkını kullanabilirsiniz.")
                    ->action('İtirazı Personel Adına Yapmak İçin Tıklayın', $url);
        } else { // reporter
            $message->line("Tutanağını tuttuğunuz **{$this->case->user->name}** isimli personelin #{$this->case->id} numaralı dosyasından aldığı ceza onaylanmıştır.")
                    ->line("Personelin bu karara karşı **3 iş günü** içinde itiraz hakkı bulunmaktadır.")
                    ->action('Dosyayı Görüntüle', $url);
        }

        return $message->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        $url = route($this->type === 'personel' ? 'disiplin.show' : 'admin.disiplin.show', $this->case->id);
        
        if ($this->type === 'personel') {
            $msg = "Cezanızın onaylandığı #{$this->case->id} disiplin dosyanıza 3 gün içinde itiraz hakkınız vardır. İtiraz etmek için tıklayın.";
        } elseif ($this->type === 'lider') {
            $msg = "Personeliniz olan {$this->case->user->name}'in dosyasından aldığı cezaya 3 gün içinde itiraz hakkı vardır. İtirazı personeliniz adına yapmak için tıklayınız.";
        } else {
            $msg = "Tutanağını tuttuğunuz {$this->case->user->name} isimli personelin #{$this->case->id} dosyasından aldığı cezaya 3 gün içinde itiraz hakkı vardır.";
        }

        return [
            'message' => $msg,
            'url' => $url,
            'icon' => 'gavel',
            'color' => 'amber',
            'case_id' => $this->case->id
        ];
    }
}
