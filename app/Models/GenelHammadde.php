<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenelHammadde extends Model
{
    protected $table = 'genel_hammaddeler';

    protected $fillable = ['bolum_id', 'ad', 'aktif_mi'];

    public function bolum()
    {
        return $this->belongsTo(Bolum::class, 'bolum_id');
    }
}
