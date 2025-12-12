<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SorumluAtandiBildirimi extends Notification
{
    use Queueable;

    protected $liderAdi;

    public function __construct($liderAdi)
    {
        $this->liderAdi = $liderAdi;
    }

    public function via($notifiable): array
    {
        return ['database']; // Zile düşsün
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "{$this->liderAdi} tarafından Disiplin Tutanak Sorumlusu olarak yetkilendirildiniz.",
            'url' => route('admin.disiplin.create'), // Tıklayınca oluşturma sayfasına gitsin
            'icon' => 'shield-check', // İkon
            'color' => 'green'
        ];
    }
}