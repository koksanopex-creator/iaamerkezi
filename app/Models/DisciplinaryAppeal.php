<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryAppeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'disciplinary_case_id',
        'user_id',
        'reason',
        'on_behalf',
        'on_behalf_of_user_id',
    ];

    protected $casts = [
        'on_behalf' => 'boolean',
    ];

    public function case()
    {
        return $this->belongsTo(DisciplinaryCase::class, 'disciplinary_case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Personel adına itiraz yapıldıysa, o personeli döner.
     */
    public function onBehalfOf()
    {
        return $this->belongsTo(User::class, 'on_behalf_of_user_id');
    }
}
