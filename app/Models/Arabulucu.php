<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Arabulucu extends Model
{
    use SoftDeletes;
    
    protected $table = 'arabulucular';
    protected $guarded = [];

    // İlişkiler
    public function cases() {
        return $this->hasMany(ArabuluculukCase::class, 'arabulucu_id');
    }
}