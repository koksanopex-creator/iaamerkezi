<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes; // SoftDeletes'i import ettin ama 'use' etmeyi unutmuşsun, onu da ekledim.
use App\Models\Takim;
use App\Models\SikayetKategori; // Bunu ekledim

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

    // App\Models\User.php içine ekle:

    /**
     * Kullanıcının yetkili olduğu (görebileceği) Bölüm ID'lerini getirir.
     */

    public function getAllowedBolumIds()
    {
        // 1. Superadmin/Yonetim/Kurul ise tüm bölümleri görsün
        if ($this->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu'])) {
            return '*'; 
        }

        $bolumIds = [];

        // 2. Bölüm Kalite Yöneticisi (Serkan Tölek)
        if ($this->hasRole('Bölüm Kalite Yöneticisi')) {
             $yonetilenBolumler = \Illuminate\Support\Facades\DB::table('bolum_kalite_yoneticileri')
                ->where('user_id', $this->id)
                // Pivot tablonda bolum_id varsa bunu kullan, yoksa kategori->bolum ilişkisine gitmelisin.
                // Şimdilik pivotta bolum_id olduğunu varsayıyoruz (eski koda göre).
                ->pluck('bolum_id') 
                ->toArray();
             $bolumIds = array_merge($bolumIds, $yonetilenBolumler);
        }

        // 3. Bölüm Lideri (Serkan Atak)
        if ($this->hasRole('Bölüm Lideri') && $this->bolum_id) {
            $bolumIds[] = $this->bolum_id;
        }

        // 4. Müşteri Şikayeti Çözüm Lideri (Hasan Ekinci)
        if ($this->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            // HATA VEREN KOD KALDIRILDI ($takim->bolum_id yok).
            // Çözüm: Liderin kendi bölümünü ekliyoruz.
            if ($this->bolum_id) {
                $bolumIds[] = $this->bolum_id;
            }
        }

        return array_unique($bolumIds);
    }
}