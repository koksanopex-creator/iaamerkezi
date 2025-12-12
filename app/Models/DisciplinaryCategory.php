<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryCategory extends Model
{
    use HasFactory;
    
    // BU SATIRI EKLE
    protected $fillable = ['ad']; 

    public function behaviors()
    {
        return $this->hasMany(DisciplinaryBehavior::class, 'category_id');
    }
}