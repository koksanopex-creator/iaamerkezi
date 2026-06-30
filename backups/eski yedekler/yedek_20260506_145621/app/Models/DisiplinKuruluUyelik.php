<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisiplinKuruluUyelik extends Model
{
    protected $table = 'disiplin_kurulu_uyelik';

    protected $fillable = [
        'user_id', 'rol', 'katilim_tarihi', 'ayrilma_tarihi',
        'ekleyen_user_id', 'cikaran_user_id', 'notlar', 'aktif',
    ];

    protected $casts = [
        'katilim_tarihi'  => 'date',
        'ayrilma_tarihi'  => 'date',
        'aktif'           => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ekleyen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ekleyen_user_id');
    }

    public function cikaran(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cikaran_user_id');
    }
}
