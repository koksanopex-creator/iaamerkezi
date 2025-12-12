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

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // DÜZELTME 1: Zil bildirimi çalışması için 'database' EKLENDİ.
        // Eğer mail de gitmesini istiyorsan: return ['database', 'mail']; yapabilirsin.
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Yeni Disiplin Tutanağı Bildirimi')
                    ->line('Hakkınızda veya sorumluluk alanınızda yeni bir disiplin süreci başlatılmıştır.')
                    ->action('Dosyayı Görüntüle', route('disiplin.show', $this->case->id))
                    ->line('Lütfen sistemi kontrol ediniz.');
    }

    /**
     * Get the array representation of the notification.
     * VERİTABANI (ZİL) BİLDİRİMİ İÇİN BURASI ÇALIŞIR
     */
    public function toArray(object $notifiable): array
    {
        // 1. LINK AYRIMI (Mantık Güncellendi)
        $url = '';

        // EĞER bildirim giden kişi (notifiable), tutanak yiyen kişiyle (case->user_id) AYNIYSA:
        // Bu kişi personel ekranına gitmeli.
        if ($notifiable->id == $this->case->user_id) {
            $url = route('disiplin.show', $this->case->id);
            $message = 'Hakkınızda yeni bir disiplin tutanağı oluşturuldu. Savunma bekleniyor.';
        } 
        // DEĞİLSE (Demek ki yönetici veya amir):
        // Yönetici ekranına gitmeli.
        else {
            $url = route('admin.disiplin.show', $this->case->id);
            $message = $this->case->user->name . ' hakkında yeni bir disiplin tutanağı oluşturuldu.';
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