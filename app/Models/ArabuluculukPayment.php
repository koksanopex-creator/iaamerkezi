<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArabuluculukPayment extends Model
{
    protected $table = 'arabuluculuk_payments';
    
    // Bu satır sayesinde 'banka_adi', 'iban' vs. tek tek yazmaya gerek kalmadan kaydedilir.
    protected $guarded = [];

    protected $casts = [
        'odeme_tarihi' => 'date',
        'tutar' => 'decimal:2',
        'finance_onay_at' => 'datetime',
        
        // --- BURASI ÇOK ÖNEMLİ ---
        // Bunu eklemezsek Blade dosyasında ->format('d.m.Y') kodu hata verir.
        'son_odeme_tarihi' => 'date', 
    ];

    // İlişkiler
    public function case() {
        return $this->belongsTo(ArabuluculukCase::class, 'case_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function approver() {
        return $this->belongsTo(User::class, 'finance_onay_by');
    }
}