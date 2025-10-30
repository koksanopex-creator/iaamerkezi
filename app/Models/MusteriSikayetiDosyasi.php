<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusteriSikayetiDosyasi extends Model
{
    use HasFactory;

    protected $table = 'musteri_sikayeti_dosyalari';

    protected $fillable = [
        'musteri_sikayeti_id',
        'dosya_yolu',
        'orijinal_adi',
        'mime_tipi',
    ];

    /**
     * Bu dosyanın ait olduğu şikayeti döndürür.
     */
    public function sikayet()
    {
        return $this->belongsTo(MusteriSikayeti::class, 'musteri_sikayeti_id');
    }
}
