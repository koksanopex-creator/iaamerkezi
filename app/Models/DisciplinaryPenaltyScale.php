<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplinaryPenaltyScale extends Model
{
    protected $fillable = ['min_puan', 'max_puan', 'ceza_adi', 'renk', 'karar_metni'];
}
