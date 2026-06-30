<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MusteriSikayetiYoneticiRaporKurali extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'musteri_sikayeti_yonetici_rapor_kurallari';

    protected $fillable = [
        'ad',
        'aktif',
        'siklik',
        'haftanin_gunleri',
        'saat',
        'mail_aktif_et',
        'zili_aktif_et',
        'mail_konusu',
        'mail_taslagi',
        'bildirim_metni',
        'son_calisma_tarihi'
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'haftanin_gunleri' => 'array',
        'mail_aktif_et' => 'boolean',
        'zili_aktif_et' => 'boolean',
        'son_calisma_tarihi' => 'datetime'
    ];
}
