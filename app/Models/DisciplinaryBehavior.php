<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryBehavior extends Model
{
    use HasFactory;

    // BU SATIRI EKLE
    protected $fillable = ['category_id', 'tanim', 'yasal_dayanak', 'aktif_mi'];

    public function category()
    {
        return $this->belongsTo(DisciplinaryCategory::class, 'category_id');
    }
}