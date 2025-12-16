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
        // === YENİ EKLENENLER ===
        'bolum_id', // <-- BU EKLENDİ
        'diger_secenegi_goster', 
        'diger_aciklama_basligi',
    ];

    // === YENİ EKLENEN İLİŞKİ ===
    public function bolum()
    {
        return $this->belongsTo(Bolum::class, 'bolum_id');
    }

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
        return $this->hasMany(MusteriSikayeti::class, 'sikayet_kategorisi_id');
    }

    // === YENİ EKLENEN İLİŞKİ ===
    /**
     * Bu kategoriye ait alt kategoriler
     */
    public function altKategoriler()
    {
        return $this->hasMany(SikayetAltKategori::class, 'sikayet_kategori_id');
    }
}