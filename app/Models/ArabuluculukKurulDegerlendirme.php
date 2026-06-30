<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArabuluculukKurulDegerlendirme extends Model
{
    // Migration'da tablo adını uzun yazmıştık, burada belirtiyoruz.
    protected $table = 'arabuluculuk_kurul_degerlendirmeleri';

    protected $guarded = [];

    // İlişkiler
    public function case()
    {
        return $this->belongsTo(ArabuluculukCase::class, 'case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}