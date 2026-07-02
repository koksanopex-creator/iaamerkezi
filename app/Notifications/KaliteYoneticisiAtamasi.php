<?php

namespace App\Notifications;

use App\Models\SikayetKategori;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KaliteYoneticisiAtamasi extends Notification
{
    use Queueable;

    protected $kategori;
    protected $type; // 'assigned' or 'unassigned'

    /**
     * Create a new notification instance.
     */
    public function __construct(SikayetKategori $kategori, string $type = 'assigned')
    {
        $this->kategori = $kategori;
        $this->type = $type;
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
    public function viaQueued(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->type === 'assigned' 
            ? 'Yeni Kalite Yönetimi Ataması: ' . $this->kategori->ad 
            : 'Kalite Yönetimi Görevi Sonlandırıldı: ' . $this->kategori->ad;

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Sayın ' . $notifiable->name . ',')
            ->line($this->type === 'assigned' 
                ? "Sistem üzerinden **{$this->kategori->ad}** kategorisindeki müşteri şikayetleri için 'Bölüm Kalite Yöneticisi' olarak görevlendirildiniz."
                : "Sistem üzerinden **{$this->kategori->ad}** kategorisindeki 'Bölüm Kalite Yöneticisi' göreviniz sonlandırılmıştır.")
            ->line('Artık bu kategorideki şikayetlerin onay süreçleri ve bildirimleri sizinle ilgili olacaktır.')
            ->action('Sisteme Git', url('/'))
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
            'type' => $this->type,
            'kategori_id' => $this->kategori->id,
            'kategori_ad' => $this->kategori->ad,
            'message' => $this->type === 'assigned' 
                ? "{$this->kategori->ad} kategorisi için Bölüm Kalite Yöneticisi olarak atandınız."
                : "{$this->kategori->ad} kategorisi Bölüm Kalite Yöneticisi göreviniz sona erdi.",
        ];
    }
}
