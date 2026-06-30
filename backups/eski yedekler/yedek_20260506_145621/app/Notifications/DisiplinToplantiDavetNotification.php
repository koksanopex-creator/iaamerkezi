<?php

namespace App\Notifications;

use App\Models\DisiplinKuruluToplanti;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        $personelAdi = $this->toplanti->disiplinDosyasi->user->name ?? 'İlgili Personel';

        return (new MailMessage)
            ->subject("Disiplin Kurulu Toplantı Daveti: {$this->toplanti->baslik}")
            ->greeting("Merhaba {$name},")
            ->line("Disiplin sürecinden gelen **{$personelAdi}** dosyası, disiplin kurulunda **{$tarih}** tarihinde görüşülmek üzere bir toplantı oluşturulmuştur.")
            ->line("**Yer:** " . ($this->toplanti->yer ?: 'Belirtilmedi'))
            ->line("**Tür:** {$this->toplanti->tur}")
            ->action('Toplantı Detayına Git', route('admin.disiplin.kurul.toplanti.show', $this->toplanti))
            ->line('Saygılarımızla, Köksan İAA Sistemi');
    }

    public function toArray(object $notifiable): array
    {
        $tarih = $this->toplanti->baslangic_tarihi
            ? $this->toplanti->baslangic_tarihi->format('d.m.Y H:i')
            : 'Belirtilmedi';

        $actionUrl = route('admin.disiplin.kurul.toplanti.show', $this->toplanti);
        $personelAdi = $this->toplanti->disiplinDosyasi->user->name ?? 'İlgili Personel';

        return [
            'type'       => 'disiplin_davet',
            'category'   => 'disiplin',
            'label'      => 'KURUL TOPLANTISI',
            'message'    => "Disiplin sürecinden gelen {$personelAdi} dosyası disiplin kurulunda {$tarih} tarihinde görüşülmek üzere toplantı oluşturulmuştur.",
            'url'        => $actionUrl,

            'action_url' => $actionUrl,
            'icon'       => 'calendar',
            'color'      => 'violet',
        ];
    }
}
