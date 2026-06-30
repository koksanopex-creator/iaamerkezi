<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SikayetHatirlatma extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sikayet_hatirlatmalari';

    protected $fillable = [
        'musteri_sikayeti_id',
        'gonderen_user_id',
        'durum',
        'hatirlatma_sayisi',
        'son_hatirlatma_tarihi',
        'sonraki_hak_tarihi',
    ];

    protected $casts = [
        'son_hatirlatma_tarihi' => 'datetime',
        'sonraki_hak_tarihi' => 'datetime',
    ];

    /**
     * Hatırlatmanın bağlı olduğu şikayet
     */
    public function musteriSikayeti(): BelongsTo
    {
        return $this->belongsTo(MusteriSikayeti::class, 'musteri_sikayeti_id');
    }

    /**
     * Hatırlatmayı gönderen müşteri temsilcisi
     */
    public function gonderen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gonderen_user_id');
    }

    /**
     * Hatırlatma altındaki tartışma yorumları
     */
    public function yorumlar(): HasMany
    {
        return $this->hasMany(SikayetHatirlatmaYorumu::class, 'sikayet_hatirlatma_id');
    }

    /**
     * Bu hatırlatma ile bildirim gönderilen kişiler
     */
    public function bildirilenler(): HasMany
    {
        return $this->hasMany(SikayetHatirlatmaBildirilen::class, 'sikayet_hatirlatma_id');
    }
}
