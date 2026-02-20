<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrunVersiyonu extends Model
{
    protected $table = 'urun_versiyonlari';

    protected $fillable = ['bolum_id', 'ad', 'aktif_mi'];

    public function bolum()
    {
        return $this->belongsTo(Bolum::class, 'bolum_id');
    }
}
