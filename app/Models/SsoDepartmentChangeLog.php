<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsoDepartmentChangeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'old_bolum_id',
        'new_bolum_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldBolum()
    {
        return $this->belongsTo(Bolum::class, 'old_bolum_id');
    }

    public function newBolum()
    {
        return $this->belongsTo(Bolum::class, 'new_bolum_id');
    }
}
