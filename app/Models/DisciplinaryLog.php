<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplinaryLog extends Model
{
    protected $fillable = [
        'disciplinary_case_id',
        'user_id',
        'eylem',
        'aciklama',
        'eski_metin'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disciplinaryCase()
    {
        return $this->belongsTo(DisciplinaryCase::class);
    }
}
