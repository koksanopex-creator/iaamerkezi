<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IadeTanimi extends Model
{
    protected $table = 'iade_tanimlari';
    protected $fillable = ['bolum_id', 'tip', 'deger', 'aktif'];

    public function bolum() {
        return $this->belongsTo(Bolum::class);
    }
}