<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryScope extends Model
{
    use HasFactory;

    // BU SATIRI EKLE
    protected $fillable = ['tanim', 'puan'];
}