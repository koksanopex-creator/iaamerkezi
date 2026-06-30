<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArabuluculukLog extends Model
{
    protected $table = 'arabuluculuk_logs';
    protected $guarded = [];

    // İlişkiler
    public function case() {
        return $this->belongsTo(ArabuluculukCase::class, 'case_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}