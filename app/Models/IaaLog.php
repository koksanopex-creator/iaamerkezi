<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IaaLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'iaa_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'iaa_id',
        'user_id',
        'eylem',
        'aciklama',
    ];

    /**
     * Log'un ait olduğu IAA (İyileştirmeye Açık Alan) kaydını getirir.
     */
    public function iaa()
    {
        return $this->belongsTo(Iaa::class);
    }

    /**
     * Log'u oluşturan kullanıcıyı getirir.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}