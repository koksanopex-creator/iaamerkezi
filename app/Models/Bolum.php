<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bolum extends Model
{
    use HasFactory, SoftDeletes;

    // =================================================================
    // ÇÖZÜM: MODELE HANGİ TABLOYU KULLANACAĞINI SÖYLÜYORUZ
    // =================================================================
    protected $table = 'bolumler'; // <-- BU SATIRI EKLE

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ad',
        'is_active',
    ];
}