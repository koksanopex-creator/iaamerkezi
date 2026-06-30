<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QualityInterventionAuthorityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $isGranted;

    /**
     * Create a new notification instance.
     */
    public function __construct(bool $isGranted)
    {
        $this->isGranted = $isGranted;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->isGranted 
            ? 'Kalite Yönetimi Müdahale Yetkisi Verildi' 
            : 'Kalite Yönetimi Müdahale Yetkisi Kaldırıldı';

        $message = $this->isGranted 
            ? "Sistem yöneticisi tarafından size müşteri şikayeti projelerinde 'Müdahale Yetkisi' tanımlanmıştır. Artık sorumlu olduğunuz kategorilerdeki projelerde tam yetkiyle (adım tamamlama, kullanıcı atama vb.) işlem yapabilirsiniz."
            : "Sistem yöneticisi tarafından müşteri şikayeti projelerindeki 'Müdahale Yetkiniz' kaldırılmıştır.";

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Sayın ' . $notifiable->name . ',')
            ->line($message)
            ->action('Projeleri Görüntüle', url('/admin/iaa'))
            ->line('İyi çalışmalar dileriz.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'is_granted' => $this->isGranted,
            'message' => $this->isGranted 
                ? "Müşteri şikayeti projelerinde müdahale yetkisi tanımlandı."
                : "Müşteri şikayeti projelerinde müdahale yetkiniz kaldırıldı.",
        ];
    }
}
