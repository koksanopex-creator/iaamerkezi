<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SikayetAltKategori extends Model
{
    protected $table = 'sikayet_alt_kategorileri';

    protected $fillable = [
        'sikayet_kategori_id',
        'ad'
    ];

    public function anaKategori()
    {
        return $this->belongsTo(SikayetKategori::class, 'sikayet_kategori_id');
    }
}