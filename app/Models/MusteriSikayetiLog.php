<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusteriSikayetiLog extends Model
{
    use HasFactory;

    protected $table = 'musteri_sikayeti_loglari';

    protected $fillable = [
        'musteri_sikayeti_id',
        'user_id',
        'eylem',
        'aciklama',
    ];

    /**
     * Logu oluşturan kullanıcı.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Logun ait olduğu şikayet.
     */
    public function sikayet()
    {
        return $this->belongsTo(MusteriSikayeti::class, 'musteri_sikayeti_id');
    }
}