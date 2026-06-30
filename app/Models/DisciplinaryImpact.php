<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryImpact extends Model
{
    use SoftDeletes;
    use HasFactory;

    // BU SATIRI EKLE
    protected $fillable = ['tanim', 'puan'];
}