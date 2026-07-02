<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinSorumluYetkiDegisikligi extends Notification
{
    use Queueable;

    protected $islemYapan;
    protected $hedefKullanici;
    protected $durum; // true: verildi, false: alındı
    protected $tip; // 'target': personele giden, 'manager': müdüre giden

    public function __construct($islemYapan, $hedefKullanici, $durum, $tip = 'target')
    {
        $this->islemYapan = $islemYapan;
        $this->hedefKullanici = $hedefKullanici;
        $this->durum = $durum;
        $this->tip = $tip;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusText = $this->durum ? 'verildi' : 'geri alındı';
        
        if ($this->tip === 'manager') {
            $subject = "Yardımcınız Yetki Değişikliği Yaptı: {$this->hedefKullanici->name}";
            $line = "Yardımcınız {$this->islemYapan->name}, {$this->hedefKullanici->name} isimli personele disiplin tutanağı tutma yetkisi {$statusText}.";
        } else {
            $subject = "Disiplin Yetkisi {$statusText}";
            $line = "{$this->islemYapan->name} tarafından disiplin tutanağı tutma yetkiniz {$statusText}.";
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Merhaba {$notifiable->name},")
            ->line($line);

        if ($this->durum && $this->tip === 'target') {
            $mail->action('Tutanak Oluştur', route('admin.disiplin.create'));
        }

        return $mail->line('İyi çalışmalar dileriz.');
    }

    public function toArray($notifiable): array
    {
        $statusText = $this->durum ? 'verildi' : 'geri alındı';
        
        if ($this->tip === 'manager') {
            $msg = "Yardımcınız {$this->islemYapan->name}, {$this->hedefKullanici->name} personeline tutanak yetkisi {$statusText}.";
        } else {
            $msg = "{$this->islemYapan->name} tarafından tutanak yetkiniz {$statusText}.";
        }

        return [
            'message' => $msg,
            'url' => $this->durum ? route('admin.disiplin.create') : route('admin.disiplin.index'),
            'icon' => $this->durum ? 'shield-check' : 'shield-exclamation',
            'color' => $this->durum ? 'green' : 'red'
        ];
    }
}
