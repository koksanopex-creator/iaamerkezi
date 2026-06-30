<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisiplinKuruluToplantiKatilimci extends Model
{
    protected $table = 'disiplin_kurulu_toplanti_katilimci';

    protected $fillable = [
        'toplanti_id', 'user_id', 'dis_katilimci_adi', 'dis_katilimci_email',
        'rol', 'katilim_durumu', 'katilmama_nedeni', 'davet_gonderildi_at',
    ];

    protected $casts = [
        'davet_gonderildi_at' => 'datetime',
    ];

    public function toplanti(): BelongsTo
    {
        return $this->belongsTo(DisiplinKuruluToplanti::class, 'toplanti_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
