<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bolum_id',
        'name',
        'installation_date',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Makinenin bağlı olduğu bölüm.
     */
    public function bolum()
    {
        return $this->belongsTo(Bolum::class, 'bolum_id');
    }

    /**
     * Makineyi oluşturan kullanıcı.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Makineyi son güncelleyen kullanıcı.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
