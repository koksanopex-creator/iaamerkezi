<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArabuluculukKurul extends Model
{
    protected $table = 'arabuluculuk_kurul_degerlendirmeleri';
    protected $guarded = [];

    // İlişki: Hangi dosyaya ait?
    public function case() {
        return $this->belongsTo(ArabuluculukCase::class, 'case_id');
    }

    // İlişki: Kim yazdı?
    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}