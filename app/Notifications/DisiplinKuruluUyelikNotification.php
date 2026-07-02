<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Queue\ShouldQueue;

class DisiplinKuruluUyelikNotification extends Notification
{
    use Queueable;

    public string $islem;      // 'eklendi' | 'cikarildi'
    public string $rolAdi;     // 'Disiplin Kurulu Başkanı' | 'Disiplin Kurulu Üyesi'
    public string $atayanAdi;  // Kararı alan kişinin adı
    public ?string $notlar;

    public function __construct(string $islem, string $rolAdi, string $atayanAdi, ?string $notlar = null)
    {
        $this->islem    = $islem;
        $this->rolAdi   = $rolAdi;
        $this->atayanAdi = $atayanAdi;
        $this->notlar   = $notlar;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->islem === 'eklendi') {
            $subject = "Disiplin Kuruluna Atandınız: {$this->rolAdi}";
            $line1   = "**{$this->atayanAdi}** tarafından **{$this->rolAdi}** olarak Disiplin Kuruluna atandınız.";
            $line2   = "Kurula katılım tarihiniz bugün itibarıyla geçerli olup yeni yetkileriniz aktif edilmiştir.";
        } elseif ($this->islem === 'eklendi_diger') {
            $subject = "Kurula Yeni Üye Katıldı";
            $line1   = "**{$this->atayanAdi}** tarafından Disiplin Kuruluna yeni bir üye atandı.";
            $line2   = $this->notlar; // "X kurula yeni üye olarak katıldı."
        } elseif ($this->islem === 'cikarildi_diger') {
            $subject = "Disiplin Kurulu Üyesi Ayrıldı";
            $line1   = "Disiplin Kurulu üyelerinden biri kuruldan ayrıldı.";
            $line2   = $this->notlar; // "X üyeliği Y tarafından sonlandırıldı."
        } else {
            $subject = "Disiplin Kurulu Üyeliğiniz Sonlandırıldı";
            $line1   = "**{$this->atayanAdi}** tarafından Disiplin Kurulu üyeliğiniz (**{$this->rolAdi}**) sona erdirilmiştir.";
            $line2   = "Bu rolle bağlantılı yetkileriniz kaldırılmıştır.";
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($line1)
            ->line($line2);

        if ($this->notlar && !in_array($this->islem, ['eklendi_diger', 'cikarildi_diger'])) {
            $mail->line("**Not:** {$this->notlar}");
        }

        return $mail
            ->action('Disiplin Kurulu Portalına Git', route('admin.disiplin.kurul.index'))
            ->line('Saygılarımızla, Köksan İAA Sistemi');
    }

    public function toArray(object $notifiable): array
    {
        if ($this->islem === 'eklendi') {
            $message = "{$this->rolAdi} olarak Disiplin Kuruluna atandınız. Karar: {$this->atayanAdi}";
            $icon    = 'badge-check';
            $color   = 'green';
        } elseif ($this->islem === 'eklendi_diger') {
            $message = $this->notlar;
            $icon    = 'user-plus';
            $color   = 'blue';
        } elseif ($this->islem === 'cikarildi_diger') {
            $message = $this->notlar;
            $icon    = 'user-remove'; // veya 'x-circle'
            $color   = 'orange';
        } else {
            $message = "Disiplin Kurulu üyeliğiniz ({$this->rolAdi}) {$this->atayanAdi} tarafından sonlandırıldı.";
            $icon    = 'x-circle';
            $color   = 'red';
        }

        return [
            'message' => $message,
            'url'     => route('admin.disiplin.kurul.index'),
            'icon'    => $icon,
            'color'   => $color,
        ];
    }
}
