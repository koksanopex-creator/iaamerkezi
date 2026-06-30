<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SikayetHatirlaticiKurali extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sikayet_hatirlatici_kurallari';

    protected $fillable = [
        'ad',
        'aktif',
        'proje_durumlari',
        'siklik',
        'haftanin_gunleri',
        'saat',
        'bildirim_rolleri',
        'sikayeti_girene_bildir',
        'musteriye_bildir',
        'ek_kullanici_ids',
        'mail_konusu',
        'mail_taslagi',
        'bildirim_metni',
        'son_calisma_tarihi'
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'proje_durumlari' => 'array',
        'haftanin_gunleri' => 'array',
        'bildirim_rolleri' => 'array',
        'ek_kullanici_ids' => 'array',
        'sikayeti_girene_bildir' => 'boolean',
        'musteriye_bildir' => 'boolean',
        'son_calisma_tarihi' => 'datetime'
    ];

    /**
     * Kurala dahil edilen ek kullanıcılar
     */
    public function ekKullanicilar()
    {
        return User::whereIn('id', $this->ek_kullanici_ids ?? [])->get();
    }
}
