<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SikayetKategori;
use App\Models\Machine;
use App\Models\BolumKategorisi;
use App\Models\User;
use App\Models\Iaa;
use App\Models\MusteriSikayeti;
use App\Models\GenelHammadde;
use App\Models\UrunVersiyonu;
use App\Models\IaaWorkflow;

class Bolum extends Model
{
    use HasFactory, SoftDeletes;

    // =================================================================
    // ÇÖZÜM: MODELE HANGİ TABLOYU KULLANACAĞINI SÖYLÜYORUZ
    // =================================================================
    protected $table = 'bolumler'; // <-- BU SATIRI EKLE

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'ad',
    'is_active',
    'bolum_kategori_id',
    'logo_yolu',
    'has_machines',
    'director_id',
    'sikayet_workflow_id',
    'takvim_business_unit_id',
    ];

    /**
     * Bu bölüme bağlı şikayet kategorileri.
     */
    public function sikayetKategorileri()
    {
        return $this->hasMany(SikayetKategori::class , 'bolum_id');
    }

    /**
     * Bu bölüme bağlı makineler.
     */
    public function machines()
    {
        return $this->hasMany(Machine::class , 'bolum_id');
    }

    public function kategori()
    {
        return $this->belongsTo(BolumKategorisi::class , 'bolum_kategori_id');
    }

    /**
     * Bu bölüme bağlı kullanıcılar.
     */
    public function users()
    {
        return $this->hasMany(User::class , 'bolum_id');
    }

    /**
     * Bu bölüme bağlı İAA projeleri.
     */
    public function iaas()
    {
        return $this->hasMany(Iaa::class , 'bolum_id');
    }

    public function sikayetler()
    {
        return $this->hasManyThrough(MusteriSikayeti::class , SikayetKategori::class , 'bolum_id', 'sikayet_kategorisi_id');
    }

    public function genelHammaddeler()
    {
        return $this->hasMany(GenelHammadde::class , 'bolum_id');
    }

    public function urunVersiyonlari()
    {
        return $this->hasMany(UrunVersiyonu::class , 'bolum_id');
    }

    /**
     * Bölümün bağlı olduğu Direktör
     */
    public function director()
    {
        return $this->belongsTo(User::class , 'director_id');
    }

    /**
     * Bölümün Müşteri Şikayeti iş akışı şablonu
     */
    public function sikayetWorkflow()
    {
        return $this->belongsTo(IaaWorkflow::class , 'sikayet_workflow_id');
    }

    /**
     * Bu bölüme bağlı disiplin dosyaları (Kullanıcılar üzerinden)
     */
    public function disciplinaryCases()
    {
        return $this->hasManyThrough(DisciplinaryCase::class , User::class , 'bolum_id', 'user_id');
    }
}