<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisiplinOylamaBaslatildi extends Notification
{
    use Queueable;

    public $case;
    public $baskan;

    public function __construct($case, $baskan)
    {
        $this->case = $case;
        $this->baskan = $baskan;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.disiplin.show', $this->case->id) . '?tab=kurul';
        $toplantiTarihi = $this->case->toplanti_tarihi
            ? $this->case->toplanti_tarihi->format('d.m.Y H:i')
            : 'Belirtilmedi';

        return (new MailMessage)
            ->subject('Disiplin Kurulu Oylaması Başlatıldı — Dosya #' . $this->case->id)
            ->greeting("Merhaba {$notifiable->name},")
            ->line("Disiplin Kurulu Başkanı **{$this->baskan->name}**, #{$this->case->id} numaralı dosya için oylamayı başlattı.")
            ->line("**Personel:** " . $this->case->user->name)
            ->line("**Toplantı Tarihi:** " . $toplantiTarihi)
            ->line("Lütfen dosyayı inceleyerek oyunuzu sisteme giriniz.")
            ->action('Disiplin Kurulu Odasına Git', $url)
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Kurul Başkanı ' . $this->baskan->name . ' oylamayı başlattı. Dosya: ' . $this->case->user->name . ' — Oy kullanabilirsiniz.',
            'url' => route('admin.disiplin.show', $this->case->id) . '?tab=kurul',
            'icon' => 'cursor-click',
            'color' => 'indigo',
            'case_id' => $this->case->id
        ];
    }
}
