<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SikayetIadesi extends Model
{
    protected $table = 'sikayet_iadeleri';
    protected $fillable = [
        'musteri_sikayeti_id',
        'user_id',
        'iade_tarihi',          // <--- BUNU EKLEYİN
        'urun_turu',
        'toplam_parti_miktari', // <--- BUNU MUTLAKA EKLEYİN (EKSİK OLAN BU) 
        'miktar',
        'birim',
        'iade_sebebi',
        'dosya_yolu',
        'aciklama'
    ];

    protected $casts = [
        'iade_tarihi' => 'date', // Tarih formatı için önemli
    ];

    public function musteriSikayeti()
    {
        return $this->belongsTo(MusteriSikayeti::class, 'musteri_sikayeti_id');
    }

    // ALIAS: Controller'da 'sikayet' olarak çağrılmış, hata vermemesi için ekliyoruz.
    public function sikayet()
    {
        return $this->belongsTo(MusteriSikayeti::class, 'musteri_sikayeti_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}