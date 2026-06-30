<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SikayetHatirlatmaBildirilen extends Model
{
    use HasFactory;

    protected $table = 'sikayet_hatirlatma_bildirilenler';

    protected $fillable = [
        'sikayet_hatirlatma_id',
        'user_id',
        'bildirim_rolu',
    ];

    /**
     * Bağlı olduğu hatırlatma
     */
    public function hatirlatma(): BelongsTo
    {
        return $this->belongsTo(SikayetHatirlatma::class, 'sikayet_hatirlatma_id');
    }

    /**
     * Bildirim gönderilen kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
