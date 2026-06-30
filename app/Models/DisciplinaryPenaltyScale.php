<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryPenaltyScale extends Model
{
    use SoftDeletes;
    protected $fillable = ['min_puan', 'max_puan', 'ceza_adi', 'renk', 'karar_metni'];
}
