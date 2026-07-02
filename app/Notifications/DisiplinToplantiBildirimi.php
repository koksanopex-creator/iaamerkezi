<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinToplantiBildirimi extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $toplanti;
    protected $tur;
    protected $mesaj;

    public function __construct($toplanti, $tur, $mesaj)
    {
        $this->toplanti = $toplanti;
        $this->tur = $tur;
        $this->mesaj = $mesaj;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Disiplin Kurulu Toplantısı: ' . ($this->tur === 'baslatıldı' ? 'Başladı' : 'Sonlandırıldı');
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting('Sayın ' . $notifiable->name . ',')
            ->line('"' . $this->toplanti->baslik . '" başlıklı disiplin kurulu toplantısı ile ilgili yeni bir gelişme var.')
            ->line($this->mesaj)
            ->action('Toplantıya Git', route('admin.disiplin.kurul.toplanti.show', $this->toplanti->id))
            ->line('Katılımınız ve katkılarınız için teşekkür ederiz.');
    }

    public function toArray(object $notifiable): array
    {
        $targetUrl = route('admin.disiplin.kurul.toplanti.show', $this->toplanti->id);
        
        return [
            'type'        => 'kurul_toplantisi',
            'category'    => 'disiplin',
            'toplanti_id' => $this->toplanti->id,
            'baslik'      => 'Disiplin Kurulu Toplantısı: ' . $this->toplanti->baslik,
            'tur'         => $this->tur,
            'mesaj'       => $this->mesaj ?? ($this->tur === 'tamamlandı' ? 'Toplantı sonlandırıldı.' : 'Toplantı başladı.'),
            'message'     => $this->mesaj ?? ($this->tur === 'tamamlandı' ? 'Toplantı sonlandırıldı.' : 'Toplantı başladı.'),
            'link'        => $targetUrl,
            'url'         => $targetUrl,
            'action_url'  => $targetUrl,
            'ikon'        => $this->tur === 'tamamlandı' ? 'check-circle' : 'calendar-clock',
            'label'       => 'KURUL TOPLANTISI',
            'subject'     => $this->toplanti->baslik
        ];
    }
}
