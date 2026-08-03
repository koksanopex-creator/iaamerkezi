<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaporKurali extends Model
{
    protected $table = 'rapor_kurallari';
    protected $guarded = [];

    protected $casts = [
        'alicilar' => 'array',       // JSON veriyi otomatik diziye çevirir
        'icerik_ayarlari' => 'array',
        'gunler' => 'array', 
        'disiplin_suc_kategorileri' => 'array',
        'aktif' => 'boolean',
        'son_gonderim_tarihi' => 'datetime',
    ];
}