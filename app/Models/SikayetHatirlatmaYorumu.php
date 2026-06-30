<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SikayetHatirlatmaYorumu extends Model
{
    use HasFactory;

    protected $table = 'sikayet_hatirlatma_yorumlari';

    protected $fillable = [
        'sikayet_hatirlatma_id',
        'hatirlatma_numarasi',
        'user_id',
        'yorum',
    ];

    /**
     * Bağlı olduğu hatırlatma
     */
    public function hatirlatma(): BelongsTo
    {
        return $this->belongsTo(SikayetHatirlatma::class, 'sikayet_hatirlatma_id');
    }

    /**
     * Yorumu yazan kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
