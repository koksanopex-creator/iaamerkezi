<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryVote extends Model
{
    use HasFactory;

    // Veritabanına yazılmasına izin verilen sütunlar
    protected $fillable = [
        'case_id',
        'user_id',
        'oy_yonu',
        'yorum',
    ];

    // --- İLİŞKİLER ---

    // Oy veren kullanıcı
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Hangi dava dosyasına oy verildiği
    public function disciplinaryCase()
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }
}