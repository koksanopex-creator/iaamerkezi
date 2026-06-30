<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsoActionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'action_by',
        'details'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
