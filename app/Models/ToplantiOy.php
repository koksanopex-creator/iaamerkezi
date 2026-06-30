<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToplantiOy extends Model
{
    protected $table = 'toplanti_oylari';
    protected $fillable = ['oylama_id', 'user_id', 'oy'];

    public function oylama(): BelongsTo
    {
        return $this->belongsTo(ToplantiOylama::class, 'oylama_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
