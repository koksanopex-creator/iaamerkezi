<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SikayetTeknikDetay extends Model
{
    use HasFactory;

    protected $table = 'sikayet_teknik_detaylari';

    protected $fillable = [
        'musteri_sikayeti_id',
        'lot_no',
        'machine_id',
        'genel_hammadde_id',
        'urun_versiyonu_id'
    ];

    public function musteriSikayeti()
    {
        return $this->belongsTo(MusteriSikayeti::class, 'musteri_sikayeti_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function genelHammadde()
    {
        return $this->belongsTo(GenelHammadde::class, 'genel_hammadde_id');
    }

    public function urunVersiyonu()
    {
        return $this->belongsTo(UrunVersiyonu::class, 'urun_versiyonu_id');
    }
}
