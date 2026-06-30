<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisiplinKuruluToplanti extends Model
{
    protected $table = 'disiplin_kurulu_toplanti';

    protected $fillable = [
        'baslik', 'aciklama', 'icerik', 'tur', 'baslangic_tarihi', 'bitis_tarihi',
        'yer', 'ajanda', 'toplanti_notu', 'durum',
        'olusturan_user_id', 'disciplinary_case_id',
        'baslatilma_at', 'bitirilme_at', 'planlanan_sure_dk',
        'erteleme_sebebi', 'iptal_sebebi', 'hatirlatma_dk', 'hatirlatma_gonderildi',
        'active_widgets', 'toplanti_karari', 'karar_dosya_yolu'
    ];

    protected $casts = [
        'baslangic_tarihi' => 'datetime',
        'bitis_tarihi'     => 'datetime',
        'baslatilma_at'    => 'datetime',
        'bitirilme_at'     => 'datetime',
        'active_widgets'   => 'json',
    ];

    public function olusturan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'olusturan_user_id');
    }

    public function disiplinDosyasi(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'disciplinary_case_id');
    }

    public function katilimcilar(): HasMany
    {
        return $this->hasMany(DisiplinKuruluToplantiKatilimci::class, 'toplanti_id');
    }

    public function aksiyonlar(): HasMany
    {
        return $this->hasMany(ToplantiAksiyon::class, 'toplanti_id');
    }

    public function oylamalar(): HasMany
    {
        return $this->hasMany(ToplantiOylama::class, 'toplanti_id');
    }

    public function pano(): HasMany
    {
        return $this->hasMany(ToplantiPano::class, 'toplanti_id');
    }

    /**
     * Kullanıcının toplantıyı yönetme yetkisi var mı?
     */
    public function kararMaddeleri()
    {
        return $this->hasMany(ToplantiKararMadde::class, 'toplanti_id');
    }

    public function canUserManage($user)
    {
        if (!$user) return false;

        // 1. Toplantıyı oluşturan
        if ($this->olusturan_user_id == $user->id) return true;

        // 2. Hukuk Admini veya Kurul Başkanı rolüne sahip olanlar
        if ($user->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı'])) return true;

        // 3. Toplantı sırasında moderatör yetkisi verilenler
        return $this->katilimcilar()->where('user_id', $user->id)->where('is_moderator', true)->exists();
    }
}
