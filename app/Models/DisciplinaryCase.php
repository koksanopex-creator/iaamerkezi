<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryCase extends Model
{
    use HasFactory, SoftDeletes;

    // $guarded = []; yerine $fillable kullanıyoruz. Bu daha güvenli ve nettir.
    protected $fillable = [
        'user_id',
        'reporter_id',
        'behavior_id',
        'impact_id',
        'scope_id',
        'olay_tarihi',
        'olay_aciklamasi',
        'kanit_dosyalari',
        'tekrar_sayisi',
        'hesaplanan_puan',
        'sistem_oneri_ceza',
        'final_karar',
        'karar_tarihi',
        'durum',
        // --- SONRADAN EKLENEN SÜTUNLAR ---
        'savunma_aciklamasi',
        'savunma_dosyalari',
        'savunma_tarihi',
        'yonetici_notu',
        'karar_dosyasi',
        'toplanti_tarihi',
    ];

    protected $casts = [
        'olay_tarihi' => 'datetime',
        'savunma_tarihi' => 'datetime',
        'karar_tarihi' => 'datetime',
        'kanit_dosyalari' => 'array', 
        'savunma_dosyalari' => 'array', 
        'toplanti_tarihi' => 'datetime',
    ];

    // --- İLİŞKİLER ---

    // Suçu İşleyen Personel
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Bildiren Amir
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    // İhlal Edilen Madde (Suç)
    public function behavior()
    {
        return $this->belongsTo(DisciplinaryBehavior::class, 'behavior_id');
    }

    // Etki / Şiddet
    public function impact()
    {
        return $this->belongsTo(DisciplinaryImpact::class, 'impact_id');
    }

    // Kapsam
    public function scope()
    {
        return $this->belongsTo(DisciplinaryScope::class, 'scope_id');
    }

    // Kategoriye Erişim (Behavior üzerinden)
    public function category() 
    { 
        return $this->hasOneThrough(
            DisciplinaryCategory::class, 
            DisciplinaryBehavior::class, 
            'id', // Behavior tablosundaki primary key
            'id', // Category tablosundaki primary key
            'behavior_id', // Case tablosundaki foreign key
            'category_id' // Behavior tablosundaki foreign key
        ); 
    }

    // Kurul Oyları
    public function oylar()
    {
        return $this->hasMany(DisciplinaryVote::class, 'case_id');
    }

    public function comments()
    {
        return $this->hasMany(DisciplinaryComment::class, 'case_id')->orderBy('created_at', 'desc');
    }
}