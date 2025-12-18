<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes; // SoftDeletes'i import ettin ama 'use' etmeyi unutmuşsun, onu da ekledim.
use App\Models\Takim;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes; // <-- SoftDeletes EKLENDİ

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'unvan', // <--- YENİ EKLENDİ
        'email',
        'password',
        'bolum_id',
        'onaylandi_mi',
        'telefon',
        'profile_photo_path',
        
        // === YENİ EKLENENLER (CRM) ===
        'is_personnel', // true: Personel, false: Müşteri Temsilcisi
        'customer_id',  // Eğer müşteri temsilcisi ise bağlı olduğu firma ID
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
            'is_personnel' => 'boolean', // <-- Cast eklendi
        ];
    }

    // =========================================================
    // === YENİ CRM İLİŞKİLERİ VE SCOPE'LAR (FİLTRELER) ===
    // =========================================================

    /**
     * İlişki: Kullanıcının bağlı olduğu Müşteri Firması (Eğer müşteri temsilcisi ise)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * SCOPE: Sadece şirket personelini getirir.
     * Kullanımı: User::personel()->get();
     */
    public function scopePersonel($query)
    {
        return $query->where('is_personnel', true);
    }

    /**
     * SCOPE: Sadece dış müşteri temsilcilerini getirir.
     * Kullanımı: User::musteriler()->get();
     */
    public function scopeMusteriler($query)
    {
        return $query->where('is_personnel', false);
    }

    // =========================================================
    // === MEVCUT İLİŞKİLERİN (AYNEN KORUNDU) ===
    // =========================================================

    public function bolum()
    {
        return $this->belongsTo(Bolum::class);
    }

    public function iaas()
    {
        return $this->hasMany(Iaa::class, 'gonderen_user_id');
    }

    public function takimlar()
    {
        return $this->belongsToMany(Takim::class, 'takim_user', 'user_id', 'takim_id')
                    ->withPivot('gorev_tanimi')
                    ->withTimestamps();
    }

    public function lideriOlduguTakimlar()
    {
        return $this->hasMany(Takim::class, 'lider_user_id');
    }

    public function yonettigiSikayetKategorileri()
    {
        return $this->belongsToMany(
            SikayetKategori::class, 
            'bolum_kalite_yoneticileri', 
            'user_id', 
            'sikayet_kategori_id'
        );
    }

    public function profilYorumlari()
    {
        return $this->hasMany(ProfileComment::class)->orderBy('created_at', 'desc');
    }

    public function gorevliOlduguProjeler()
    {
        return $this->belongsToMany(Iaa::class, 'iaa_user', 'user_id', 'iaa_id')
                    ->withPivot('rol', 'kazanilan_puan', 'durum')
                    ->withTimestamps();
    }

    public function disiplinDosyalari()
    {
        return $this->hasMany(\App\Models\DisciplinaryCase::class, 'user_id');
    }

    public function raporladigiDisiplinDosyalari()
    {
        return $this->hasMany(\App\Models\DisciplinaryCase::class, 'reporter_id');
    }
}