<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\MusteriSikayeti;

class SikayetTakipBilgilendirmeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sikayet;
    public $plainPassword; // Müşteriye göstereceğimiz ham şifre
    public $isReset;       // İlk bildirim mi, şifre sıfırlama mı?

    public function __construct(MusteriSikayeti $sikayet, $plainPassword, $isReset = false)
    {
        $this->sikayet = $sikayet;
        $this->plainPassword = $plainPassword;
        $this->isReset = $isReset;
    }

    public function build()
    {
        $subject = $this->isReset 
            ? 'Şikayet Takip Şifreniz Yenilendi - #' . $this->sikayet->id 
            : 'Şikayetiniz İşleme Alındı - Takip Bilgileri #' . $this->sikayet->id;

        return $this->subject($subject)->view('emails.sikayet-takip-bilgilendirme');
    }
}