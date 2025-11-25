<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    // created_at otomatik yönetilsin, updated_at olmasın
    public $timestamps = false; 
    
    protected $fillable = ['user_id', 'ip_address', 'user_agent', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
