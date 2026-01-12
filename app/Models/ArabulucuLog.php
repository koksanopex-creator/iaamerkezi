<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArabulucuLog extends Model
{
    protected $table = 'arabulucu_logs';
    protected $fillable = ['user_id', 'arabulucu_id', 'islem_turu', 'detay', 'ip_adres'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function arabulucu()
    {
        // softDelete olduğu için silinenleri de getirsin
        return $this->belongsTo(Arabulucu::class, 'arabulucu_id')->withTrashed();
    }
}