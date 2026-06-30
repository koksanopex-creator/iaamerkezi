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
    public $note;

    public function __construct($case, $baskan, $note = null)
    {
        $this->case = $case;
        $this->baskan = $baskan;
        $this->note = $note;
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

        $mail = (new MailMessage)
            ->subject('Disiplin Kurulu Oylaması Başlatıldı — Dosya #' . $this->case->id)
            ->greeting("Merhaba {$notifiable->name},")
            ->line("Disiplin Kurulu Başkanı **{$this->baskan->name}**, #{$this->case->id} numaralı dosya için oylamayı başlattı.")
            ->line("**Personel:** " . $this->case->user->name)
            ->line("**Toplantı Tarihi:** " . $toplantiTarihi);

        if ($this->note) {
            $mail->line('---')
                 ->line("**Başkanın Notu:**")
                 ->line("_{$this->note}_")
                 ->line('---');
        }

        return $mail->action('Disiplin Kurulu Odasına Git', $url)
            ->line('Saygılarımızla, Köksan İyileştirmeye Açık Alan');
    }

    public function toArray(object $notifiable): array
    {
        $message = 'Kurul Başkanı ' . $this->baskan->name . ' oylamayı başlattı. Dosya: ' . $this->case->user->name;
        if ($this->note) {
            $message .= ' — Not: ' . \Str::limit($this->note, 50);
        }

        return [
            'message' => $message,
            'url' => route('admin.disiplin.show', $this->case->id) . '?tab=kurul',
            'icon' => 'cursor-click',
            'color' => 'indigo',
            'case_id' => $this->case->id,
            'note' => $this->note
        ];
    }
}
