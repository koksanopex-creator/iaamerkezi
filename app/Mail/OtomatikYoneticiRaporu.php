<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtomatikYoneticiRaporu extends Mailable
{
    use Queueable, SerializesModels;

    public $raporData;
    public $raporBasligi;
    public $tarih;

    /**
     * Create a new message instance.
     */
    public function __construct($data, $baslik)
    {
        $this->raporData = $data;
        $this->raporBasligi = $baslik;

        // Sunucunun dil paketine güvenmeyip manuel çeviriyoruz
        $simdi = now();
        
        $aylar = [
            'January' => 'Ocak', 'February' => 'Şubat', 'March' => 'Mart',
            'April' => 'Nisan', 'May' => 'Mayıs', 'June' => 'Haziran',
            'July' => 'Temmuz', 'August' => 'Ağustos', 'September' => 'Eylül',
            'October' => 'Ekim', 'November' => 'Kasım', 'December' => 'Aralık'
        ];
        
        $gunler = [
            'Monday' => 'Pazartesi', 'Tuesday' => 'Salı', 'Wednesday' => 'Çarşamba',
            'Thursday' => 'Perşembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi', 'Sunday' => 'Pazar'
        ];

        $ay = $aylar[$simdi->format('F')];
        $gun = $gunler[$simdi->format('l')];
        
        // Çıktı Örneği: 19 Ocak 2026 Pazartesi
        $this->tarih = $simdi->format('d') . ' ' . $ay . ' ' . $simdi->format('Y') . ' ' . $gun;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("\u{1F4CA} " . $this->raporBasligi . ' - ' . $this->tarih)
                    ->view('emails.raporlar.otomatik-ozet');
    }
}