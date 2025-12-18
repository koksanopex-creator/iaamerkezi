<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    // Mass assignment koruması için doldurulabilir alanlar
    protected $fillable = [
        'name',           // Firma Adı
        'logo_path', // <--- YENİ EKLENDİ
        'tax_number',     // Vergi No
        'tax_office',     // Vergi Dairesi
        'address',        // Adres
        'phone',          // Santral Telefonu
        'email',          // Genel E-posta
        'location_type',  // Yurt İçi / Yurt Dışı
        'is_active',      // Aktif/Pasif Durumu
    ];

    /**
     * Bu firmaya bağlı yetkili kişiler (Müşteri Temsilcileri)
     * Bir firmanın birden fazla yetkilisi olabilir (User tablosunda)
     */
    public function representatives()
    {
        return $this->hasMany(User::class, 'customer_id');
    }

    /**
     * Bu firmaya ait şikayetler
     */
    public function complaints()
    {
        return $this->hasMany(MusteriSikayeti::class, 'customer_id');
    }
}