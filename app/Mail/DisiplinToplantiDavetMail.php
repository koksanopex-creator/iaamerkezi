<?php

namespace App\Mail;

use App\Models\DisiplinKuruluToplanti;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisiplinToplantiDavetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $toplanti;

    public function __construct(DisiplinKuruluToplanti $toplanti)
    {
        $this->toplanti = $toplanti;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), 'Köksan Disiplin Kurulu Sistemi')
                    ->subject('Disiplin Kurulu Toplantı Daveti: ' . $this->toplanti->baslik)
                    ->markdown('emails.disiplin.toplanti-davet');
    }
}
