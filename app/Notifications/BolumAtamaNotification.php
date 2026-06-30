<?php

namespace App\Notifications;

use App\Models\Bolum;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BolumAtamaNotification extends Notification
{
    use Queueable;

    protected Bolum $bolum;
    protected string $type; // direktor_atandi, lider_atandi, direktor_bagli_lider_atandi
    protected ?User $assignedUser; // Atanan Lider (Eğer direktöre bilgi gidiyorsa)

    /**
     * Create a new notification instance.
     */
    public function __construct(Bolum $bolum, string $type, ?User $assignedUser = null)
    {
        $this->bolum = $bolum;
        $this->type = $type;
        $this->assignedUser = $assignedUser;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->toDatabase($notifiable);
        
        return (new MailMessage)
            ->subject('Bölüm Atama Bildirimi')
            ->greeting("Merhaba {$notifiable->name},")
            ->line($data['message'])
            ->action('Bölüm Panelini Görüntüle', route('admin.bolumler.dashboard', $this->bolum->id))
            ->salutation('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $message = '';
        $icon = 'users';
        $color = 'indigo';

        switch ($this->type) {
            case 'direktor_atandi':
                $message = "**{$this->bolum->ad}** bölümüne direktör olarak atandınız.";
                $icon = 'user-tie'; // FontAwesome benzeri ikon isimleri kullanıyorsa projeniz
                $color = 'indigo';
                break;

            case 'lider_atandi':
                $message = "**{$this->bolum->ad}** bölümüne bölüm lideri olarak atandınız.";
                $icon = 'user-star';
                $color = 'purple';
                break;

            case 'direktor_bagli_lider_atandi':
                $liderName = $this->assignedUser ? $this->assignedUser->name : 'Bir personel';
                $message = "Size bağlı **{$this->bolum->ad}** bölümüne bölüm lideri olarak **{$liderName}** atanmıştır.";
                $icon = 'user-plus';
                $color = 'amber';
                break;
        }

        return [
            'message' => $message,
            'url' => route('admin.bolumler.dashboard', $this->bolum->id),
            'icon' => $icon,
            'color' => $color,
            'bolum_id' => $this->bolum->id,
            'type' => $this->type
        ];
    }
}
