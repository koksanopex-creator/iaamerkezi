<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SikayetGuestPassword extends Model
{
    use HasFactory;

    protected $table = 'sikayet_guest_passwords';

    protected $fillable = [
        'musteri_sikayeti_id',
        'email',
        'recipient_name',
        'recipient_type',
        'password_hash',
        'sent_by',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Bu şifre kaydının bağlı olduğu müşteri şikayeti.
     */
    public function musteriSikayeti()
    {
        return $this->belongsTo(MusteriSikayeti::class, 'musteri_sikayeti_id');
    }

    /**
     * Şifreyi gönderen kullanıcı.
     */
    public function sentByUser()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
