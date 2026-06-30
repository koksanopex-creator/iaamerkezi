<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToplantiAksiyon extends Model
{
    protected $table = 'toplanti_aksiyonlari';
    protected $fillable = ['toplanti_id', 'user_id', 'icerik', 'durum'];

    public function toplanti(): BelongsTo
    {
        return $this->belongsTo(DisiplinKuruluToplanti::class, 'toplanti_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
