<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToplantiKararMadde extends Model
{
    protected $table = 'toplanti_karar_maddeleri';

    protected $fillable = [
        'toplanti_id',
        'madde_metni',
        'sorumlu_user_id',
        'durum'
    ];

    public function toplanti()
    {
        return $this->belongsTo(DisiplinKuruluToplanti::class, 'toplanti_id');
    }

    public function sorumlu()
    {
        return $this->belongsTo(User::class, 'sorumlu_user_id');
    }
}
