<?php

namespace App\Notifications;

use App\Models\DisiplinKuruluToplanti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Queue\ShouldQueue;

class DisiplinToplantiDavetNotification extends Notification
{
    use Queueable;

    public DisiplinKuruluToplanti $toplanti;
    public string $davetEdenAdi;

    public function __construct(DisiplinKuruluToplanti $toplanti, string $davetEdenAdi)
    {
        $this->toplanti    = $toplanti;
        $this->davetEdenAdi = $davetEdenAdi;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tarih = $this->toplanti->baslangic_tarihi
            ? $this->toplanti->baslangic_tarihi->format('d.m.Y H:i')
            : 'Belirtilmedi';

        $name = $notifiable->name ?? 'Katılımcı';
        $casesCount = $this->toplanti->disiplinDosyalari->count();
        $personelAdi = $casesCount === 1 
            ? ($this->toplanti->disiplinDosyalari->first()->user->name ?? 'İlgili Personel')
            : ($casesCount . ' Adet Disiplin Dosyası');

        $mail = (new MailMessage)
            ->from(config('mail.from.address'), 'Köksan Disiplin Kurulu Sistemi')
            ->subject("Disiplin Kurulu Toplantı Daveti: {$this->toplanti->baslik}")
            ->greeting("Merhaba {$name},")
            ->line("Disiplin kurulu tarafından düzenlenen **{$this->toplanti->baslik}** konulu toplantıya davet edildiniz.");

        if ($casesCount > 0) {
            $mail->line("Bu toplantı, **{$personelAdi}** ile ilgilidir.");
        }

        return $mail->line("**Tarih:** {$tarih}")
            ->line("**Yer:** " . ($this->toplanti->yer ?: 'Belirtilmedi'))
            ->line("**Tür:** {$this->toplanti->tur}")
            ->action('Toplantı Detayına Git', route('admin.disiplin.kurul.toplanti.show', $this->toplanti))
            ->line('Saygılarımızla, Köksan Disiplin Kurulu Sistemi');
    }

    public function toArray(object $notifiable): array
    {
        $tarih = $this->toplanti->baslangic_tarihi
            ? $this->toplanti->baslangic_tarihi->format('d.m.Y H:i')
            : 'Belirtilmedi';

        $actionUrl = route('admin.disiplin.kurul.toplanti.show', $this->toplanti);
        $casesCount = $this->toplanti->disiplinDosyalari->count();
        $personelAdi = $casesCount === 1 
            ? ($this->toplanti->disiplinDosyalari->first()->user->name ?? 'İlgili Personel')
            : ($casesCount . ' Adet Disiplin Dosyası');
        
        $msg = "Disiplin kurulu toplantısı planlandı: {$this->toplanti->baslik} ({$tarih})";
        if ($casesCount > 0) {
            $msg = "Disiplin sürecinden gelen {$personelAdi} için toplantı oluşturulmuştur. ({$tarih})";
        }

        return [
            'type'       => 'disiplin_davet',
            'category'   => 'disiplin',
            'label'      => 'KURUL TOPLANTISI',
            'message'    => $msg,
            'url'        => $actionUrl,

            'action_url' => $actionUrl,
            'icon'       => 'calendar',
            'color'      => 'violet',
        ];
    }
}
