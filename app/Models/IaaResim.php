<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- BU SATIRI EKLE


class IaaResim extends Model
{
    use HasFactory, SoftDeletes; // <-- SoftDeletes'i BURAYA EKLE

    protected $table = 'iaa_resimler';
    protected $fillable = ['iaa_id', 'dosya_yolu'];

    public function iaa()
    {
        return $this->belongsTo(Iaa::class);
    }
}