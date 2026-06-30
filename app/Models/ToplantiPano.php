<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToplantiPano extends Model
{
    protected $table = 'toplanti_pano';
    protected $fillable = ['toplanti_id', 'icerik'];

    public function toplanti(): BelongsTo
    {
        return $this->belongsTo(DisiplinKuruluToplanti::class, 'toplanti_id');
    }
}
