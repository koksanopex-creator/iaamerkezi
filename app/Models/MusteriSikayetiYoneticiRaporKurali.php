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
        'alicilar',
        'rapor_kapsami',
        'siklik',
        'periyot',
        'haftanin_gunleri',
        'ayin_gunleri',
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
        'alicilar' => 'array',
        'haftanin_gunleri' => 'array',
        'ayin_gunleri' => 'array',
        'mail_aktif_et' => 'boolean',
        'zili_aktif_et' => 'boolean',
        'son_calisma_tarihi' => 'datetime'
    ];
}
