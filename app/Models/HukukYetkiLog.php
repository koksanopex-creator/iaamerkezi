<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HukukYetkiLog extends Model
{
    protected $table = 'hukuk_yetki_loglari';

    protected $fillable = [
        'admin_id',
        'user_id',
        'type',
        'target_name',
        'action',
        'details',
        'ip_address'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
