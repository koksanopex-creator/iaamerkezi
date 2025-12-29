<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class MusteriLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Hızlı Kayıt Fonksiyonu (Customer ID opsiyonel, genel işlemse null olabilir)
    public static function add($customerId, $islem, $aciklama = null)
    {
        self::create([
            'customer_id' => $customerId,
            'user_id' => Auth::check() ? Auth::id() : null, // Login değilse null olabilir
            'islem_turu' => $islem,
            'aciklama' => $aciklama,
            'ip_adresi' => Request::ip(),
        ]);
    }
}