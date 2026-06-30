<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KullaniciIstek extends Model
{
    use HasFactory;

    protected $table = 'kullanici_istekleri';

    protected $fillable = [
        'user_id',
        'talep_turu',
        'eski_deger',
        'yeni_deger',
        'yeni_bolum_id',
        'durum',
        'admin_id',
        'admin_notu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function yeniBolum()
    {
        return $this->belongsTo(Bolum::class, 'yeni_bolum_id');
    }
}
