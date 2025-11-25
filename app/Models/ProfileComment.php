<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileComment extends Model
{
    protected $fillable = ['user_id', 'yazan_user_id', 'yorum', 'parent_id'];

    // Yorumu yazan kişi
    public function yazan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'yazan_user_id');
    }

    // Profil sahibi
    public function profileUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Bu yorumun cevapları (Alt Yorumlar)
    public function cevaplar(): HasMany
    {
        return $this->hasMany(ProfileComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}