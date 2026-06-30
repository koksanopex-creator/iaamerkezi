<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinTutanagiOlusturuldu extends Notification
{
    use Queueable;

    public $case;

    /**
     * Create a new notification instance.
     */
    public function __construct($case)
    {
        $this->case = $case;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = '';
        $message = '';
        $subject = 'Yeni Disiplin Tutanağı Bildirimi';

        if ($notifiable->id == $this->case->user_id) {
            $url = route('disiplin.show', $this->case->id);
            $message = 'Hakkınızda yeni bir disiplin tutanağı oluşturuldu. Savunma vermeniz beklenmektedir.';
        } else {
            $url = route('admin.disiplin.show', $this->case->id);
            // Organizasyonel Bağ Kontrolleri
            $isActualDirector = ($this->case->user->bolum && $this->case->user->bolum->director_id == $notifiable->id);
            $isActualLeader = ($notifiable->hasRole('Bölüm Lideri') && $notifiable->bolum_id == $this->case->user->bolum_id);

            if ($isActualDirector) {
                $message = "Bölümünüz personeli {$this->case->user->name} hakkında yeni bir disiplin tutanağı oluşturuldu. (Bilgilendirme)";
            } elseif ($isActualLeader) {
                $message = "Personeliniz {$this->case->user->name} hakkında yeni bir disiplin tutanağı oluşturuldu.";
            } elseif ($notifiable->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])) {
                $message = "{$this->case->user->name} hakkında yeni bir disiplin tutanağı oluşturuldu. (Bilgilendirme)";
            } else {
                $message = "{$this->case->user->name} hakkında yeni bir disiplin tutanağı oluşturuldu.";
            }
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($message)
            ->action('Tutanağı Görüntüle', $url)
            ->line('Lütfen sistemi kontrol ediniz.')
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        $url = '';
        $message = '';

        if ($notifiable->id == $this->case->user_id) {
            $url = route('disiplin.show', $this->case->id);
            $message = 'Hakkınızda yeni bir disiplin tutanağı oluşturuldu. Savunma bekleniyor.';
        } else {
            $url = route('admin.disiplin.show', $this->case->id);
            // Organizasyonel Bağ Kontrolleri
            $isActualDirector = ($this->case->user->bolum && $this->case->user->bolum->director_id == $notifiable->id);
            $isActualLeader = ($notifiable->hasRole('Bölüm Lideri') && $notifiable->bolum_id == $this->case->user->bolum_id);

            if ($isActualDirector) {
                $message = "Bölümünüz personeli {$this->case->user->name} hakkında yeni tutanak (Bilgilendirme)";
            } elseif ($isActualLeader) {
                $message = "Personeliniz {$this->case->user->name} hakkında yeni bir disiplin tutanağı oluşturuldu.";
            } elseif ($notifiable->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])) {
                $message = "{$this->case->user->name} hakkında yeni tutanak (Bilgilendirme)";
            } else {
                $message = "{$this->case->user->name} hakkında yeni disiplin tutanağı.";
            }
        }

        return [
            'message' => $message,
            'url' => $url,
            'icon' => 'exclamation-circle',
            'color' => 'red',
            'case_id' => $this->case->id
        ];
    }
}