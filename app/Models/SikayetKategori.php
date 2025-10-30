<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SikayetKategori extends Model
{
    use HasFactory;

    protected $table = 'sikayet_kategorileri';

    protected $fillable = [
        'ad',
        'varsayilan_takim_id',
    ];

    // Bir kategorinin varsayılan takımıyla olan ilişkisi
    public function varsayilanTakim()
    {
        return $this->belongsTo(Takim::class, 'varsayilan_takim_id');
    }

    /**
     * Bu kategoriye ait müşteri şikayetleri (hasMany ilişkisi)
     */
    public function sikayetler()
    {
        // 'sikayet_kategorisi_id' foreign key'i üzerinden MusteriSikayeti modeline bağlanır
        return $this->hasMany(MusteriSikayeti::class, 'sikayet_kategorisi_id');
    }
}