<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'dosyalar' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function case()
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }

    public function histories()
    {
        return $this->hasMany(DisciplinaryCommentHistory::class, 'comment_id')->orderBy('created_at', 'desc');
    }
}