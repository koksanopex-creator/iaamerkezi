<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Iaa; // Iaa modelini kullanacağımızı belirtiyoruz


class Takim extends Model
{
    use HasFactory;

    protected $table = 'takimlar';
    protected $fillable = ['ad', 'lider_user_id', 'amac', 'vizyon', 'misyon', 'kurallar', 'tur', 'toplam_puan'];

    /**
     * Takımın liderini (User) getirir.
     */
    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_user_id')->withTrashed();
    }

    /**
     * Takımın tüm üyelerini (User) getirir.
     */
    public function uyeler()
    {
        return $this->belongsToMany(User::class, 'takim_user', 'takim_id', 'user_id')
            ->withPivot('gorev_tanimi', 'katilma_sekli', 'created_at') // katilma_sekli ve created_at'i ekle
            ->withTimestamps();
    }

    /**
     * Alias for uyeler (Consistency)
     */
    public function users()
    {
        return $this->uyeler();
    }

    public function takimlar()
    {
        return $this->belongsToMany(Takim::class, 'takim_user', 'user_id', 'takim_id')
            ->withPivot('gorev_tanimi', 'katilma_sekli', 'created_at') // katilma_sekli ve created_at'i ekle
            ->withTimestamps();
    }

    /**
     * Bu takımın talep ettiği tüm İAA'ları döndürür.
     * (belongsToMany ilişkisi: Bir takım birden çok İAA talep edebilir)
     */

    public function talepEttigiIaalar()
    {
        return $this->belongsToMany(Iaa::class, 'iaa_talepleri', 'takim_id', 'iaa_id')
            ->withPivot('id', 'iaa_workflow_id', 'workflow_snapshot', 'start_date', 'due_date', 'status')
            ->withTimestamps();
    }

    /**
     * Bu takıma atanmış olan tüm projeleri (İAA'ları) döndürür.
     * (hasMany ilişkisi: Bir takımın birden çok projesi olabilir)
     */
    public function atananProjeler()
    {
        return $this->hasMany(Iaa::class, 'atanan_takim_id');
    }

    public function davetiyeler()
    {
        return $this->hasMany(TakimDavetiyesi::class);
    }


    public function atanmisProjeler()
    {
        // Bir takımın, "atanan_takim_id" alanı kendi ID'si olan İAA'larla ilişkisi
        return $this->hasMany(Iaa::class, 'atanan_takim_id');
    }

    /**
     * Bu 'sikayet' takımına atanmış olan tüm şikayetleri döndürür.
     */
    public function atananSikayetler()
    {
        // 'atanan_cozum_takimi_id' alanı üzerinden MusteriSikayeti modeline bağlanır
        return $this->hasMany(MusteriSikayeti::class, 'atanan_cozum_takimi_id');
    }

}