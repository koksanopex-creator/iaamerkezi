<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BolumKategorisi extends Model
{
    use HasFactory;

    protected $table = 'bolum_kategorileri';

    protected $fillable = ['ad'];

    public function bolumler()
    {
        return $this->hasMany(Bolum::class , 'bolum_kategori_id');
    }
}
