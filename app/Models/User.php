<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // <-- BU SATIRI EKLE
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Takim; // Takim modelini kullanacağımızı belirtiyoruz




class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles; // <-- HasRoles'u BURAYA EKLE

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // YENİ VE DOĞRU HALİ
    protected $fillable = [
        'name',
        'email',
        'password',
        'bolum_id',
        'onaylandi_mi',
        'telefon',             // <-- YENİ EKLENDİ
        'profile_photo_path',  // <-- YENİ EKLENDİ
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Kullanıcının ait olduğu bölümü getirir.
     */
    public function bolum()
    {
        return $this->belongsTo(Bolum::class);
    }

    /**
     * Kullanıcının gönderdiği tüm İAA'ları getirir.
     */
    public function iaas()
    {
        return $this->hasMany(Iaa::class, 'gonderen_user_id');
    }

    /**
     * Kullanıcının üye olduğu tüm takımları getirir.
     */
    public function takimlar()
    {
        return $this->belongsToMany(Takim::class, 'takim_user', 'user_id', 'takim_id')
                    ->withPivot('gorev_tanimi')
                    ->withTimestamps();
    }

    /**
     * Bu kullanıcının lideri olduğu tüm takımları döndürür.
     * (hasMany ilişkisi: Bir kullanıcının birden çok takımı olabilir)
     */
    public function lideriOlduguTakimlar()
    {
        return $this->hasMany(Takim::class, 'lider_user_id');
    }

    /**
     * Bu kullanıcının YÖNETİCİSİ (Bölüm Kalite Yöneticisi) olduğu Şikayet Kategorileri.
     * (Ara Onay yetkisi için kullanılır)
     */
    public function yonettigiSikayetKategorileri()
    {
        return $this->belongsToMany(
            SikayetKategori::class, 
            'bolum_kalite_yoneticileri', // Ara tablo adı
            'user_id', 
            'sikayet_kategori_id'
        );
    }

    // Profilime yapılan yorumlar
    public function profilYorumlari()
    {
        return $this->hasMany(ProfileComment::class)->orderBy('created_at', 'desc');
    }

    // Kullanıcının dahil olduğu proje bazlı görevler
    public function gorevliOlduguProjeler()
    {
        return $this->belongsToMany(Iaa::class, 'iaa_user', 'user_id', 'iaa_id')
                    ->withPivot('rol', 'kazanilan_puan', 'durum')
                    ->withTimestamps();
    }
}