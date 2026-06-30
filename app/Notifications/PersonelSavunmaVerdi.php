<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PersonelSavunmaVerdi extends Notification
{
    use Queueable;

    public $case;
    public $isUpdate;

    public function __construct($case, $isUpdate = false)
    {
        $this->case = $case;
        $this->isUpdate = $isUpdate;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.disiplin.show', $this->case->id);
        $subject = $this->isUpdate ? 'Disiplin Savunması Güncellendi' : 'Yeni Disiplin Savunması Yazıldı';
        $message = '';

        if ($notifiable->hasRole(['Hukuk Yöneticisi', 'Hukuk Admini'])) {
            $message = "Değerlendirmenizi bekleyen disiplin tutanak dosyası var. {$this->case->user->name} isimli personele " .
                ($this->case->reporter->name ?? 'amir') . " tarafından yazılan tutanağa " .
                ($this->isUpdate ? "savunma güncellenmiştir." : "savunma yazılmıştır.") .
                " Lütfen değerlendirmenizi yapınız.";
        } elseif ($notifiable->hasRole('Superadmin')) {
            $message = "Bilgilendirme: {$this->case->user->name} isimli personele yazılan tutanağa " .
                ($this->isUpdate ? "savunma güncellenmiştir." : "savunma yazılmıştır.");
        } elseif ($notifiable->hasRole('Direktör')) {
            $message = "Bölümünüz personeli {$this->case->user->name}, tutanağına " .
                ($this->isUpdate ? "savunmasını düzenledi." : "savunma yazdı.");
        } else {
            // Bölüm Lideri
            $message = "Personeliniz {$this->case->user->name}, {$this->case->id} nolu tutanağına " .
                ($this->isUpdate ? "savunmasında düzenleme yaptı." : "savunma yazdı.");
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($message)
            ->action('Dosyayı İncele', $url)
            ->line('Lütfen sistemi kontrol ederek gerekli değerlendirmeyi yapınız.')
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        $message = '';
        if ($notifiable->hasRole(['Hukuk Yöneticisi', 'Hukuk Admini'])) {
            $message = "Değerlendirmenizi bekleyen tutanak: {$this->case->user->name} savunma yazdı/güncelledi.";
        } elseif ($notifiable->hasRole('Superadmin')) {
            $message = "Bilgi: {$this->case->user->name} savunma yazdı/güncelledi.";
        } elseif ($notifiable->hasRole('Direktör')) {
            $message = "Bölüm personeli {$this->case->user->name} savunma " . ($this->isUpdate ? "güncelledi." : "yazdı.");
        } else {
            $message = "Personeliniz {$this->case->user->name} " . ($this->isUpdate ? "savunmasında düzenleme yaptı." : "savunma yazdı.");
        }

        return [
            'message' => $message,
            'url' => route('admin.disiplin.show', $this->case->id),
            'icon' => 'document-text',
            'color' => 'blue',
            'case_id' => $this->case->id
        ];
    }
}
