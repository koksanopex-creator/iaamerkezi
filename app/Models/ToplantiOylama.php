<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToplantiOylama extends Model
{
    protected $table = 'toplanti_oylamalari';
    protected $fillable = ['toplanti_id', 'baslatan_id', 'konu', 'aktif'];

    public function toplanti(): BelongsTo
    {
        return $this->belongsTo(DisiplinKuruluToplanti::class, 'toplanti_id');
    }

    public function baslatan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'baslatan_id');
    }

    public function oylar(): HasMany
    {
        return $this->hasMany(ToplantiOy::class, 'oylama_id');
    }
}
