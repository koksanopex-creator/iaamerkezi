<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Şikayet modelini kullanacağımız için buraya ekledik
use App\Models\MusteriSikayeti; 

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    // Mass assignment koruması için doldurulabilir alanlar
    protected $fillable = [
        'name',           // Firma Adı
        'logo_path',      // Logo
        'tax_number',     // Vergi No
        'tax_office',     // Vergi Dairesi
        'address',        // Adres
        'phone',          // Santral Telefonu
        'email',          // Genel E-posta
        'location_type',  // Yurt İçi / Yurt Dışı
        'is_active',      // Aktif/Pasif Durumu
        'passive_reason', // Pasife alınma sebebi (BUNU LİSTEYE EKLEDİK)
    ];

    /**
     * Bu firmaya bağlı yetkili kişiler (Müşteri Temsilcileri)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'customer_id');
    }

    /**
     * DÜZELTME BURADA YAPILDI:
     * Veritabanına göre şikayetler doğrudan 'customer_id' ile firmaya bağlı.
     * Bu yüzden 'hasManyThrough' yerine 'hasMany' kullanıyoruz.
     */
    public function sikayetler()
    {
        return $this->hasMany(MusteriSikayeti::class, 'customer_id');
    }
    
    // --- ESKİ KODLARIN BOZULMAMASI İÇİN ALIASLAR ---
    
    public function representatives() { return $this->users(); }
    public function complaints() { return $this->sikayetler(); }
}