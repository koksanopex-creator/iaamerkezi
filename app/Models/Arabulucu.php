<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Arabulucu extends Model
{
    use SoftDeletes;
    
    protected $table = 'arabulucular';

    // $guarded = [] yerine $fillable kullanarak hangi alanların kaydedileceğini 
    // net bir şekilde belirtmek daha profesyonel ve güvenlidir.
    protected $fillable = [
        'name', 
        'sicil_no', 
        'sehir', 
        'email', 
        'telefon', 
        'adres',
        'is_active',   // YENİ: Aktif/Pasif durumu
        'created_by'   // YENİ: Kaydeden kullanıcı ID'si
    ];

    // is_active sütununun veritabanından çekilirken otomatik olarak 
    // "true" veya "false" (boolean) tipine dönüştürülmesini sağlar.
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // --- İLİŞKİLER ---

    // 1. Arabulucunun Dosyaları
    public function cases() {
        return $this->hasMany(ArabuluculukCase::class, 'arabulucu_id');
    }

    // 2. YENİ: Arabulucuyu Sisteme Ekleyen Kullanıcı
    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}