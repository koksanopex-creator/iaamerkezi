<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // <-- Import Eklendi
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Takim;
use App\Models\SikayetKategori;

class User extends Authenticatable implements MustVerifyEmail // <-- Interface Implemente Edildi
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

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
        'last_seen_at',

        // === YENİ EKLENENLER (CRM) ===
        'is_personnel', // true: Personel, false: Müşteri Temsilcisi
        'customer_id',  // Eğer müşteri temsilcisi ise bağlı olduğu firma ID
        'email_verified_at', // <--- YENİ EKLENDİ
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
            'last_seen_at' => 'datetime', // <--- KRİTİK EKLEME BURADA
        ];
    }

    /**
     * Kullanıcının son 5 dakika içinde aktif olup olmadığını kontrol eder.
     */
    public function isOnline()
    {
        // last_seen_at verisi varsa ve şu anki zamandan farkı 5 dakikadan azsa true döner
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
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

        // 2. Bölüm Kalite Yöneticisi (Serkan Tölek - SQL HATASI BURADAYDI)
        if ($this->hasRole('Bölüm Kalite Yöneticisi')) {
            // HATA: bolum_kalite_yoneticileri tablosunda 'bolum_id' yok.
            // DOĞRUSU: İlişki üzerinden gidip kategorinin bağlı olduğu bölümü almalıyız.

            $yonetilenBolumler = $this->yonettigiSikayetKategorileri()
                ->pluck('bolum_id') // Kategoriler tablosundaki bolum_id'yi alır
                ->unique()          // Aynı bölümden birden fazla kategori varsa tekilleştirir
                ->toArray();

            $bolumIds = array_merge($bolumIds, $yonetilenBolumler);
        }

        // 3. Bölüm Lideri (Serkan Atak)
        if ($this->hasRole('Bölüm Lideri') && $this->bolum_id) {
            $bolumIds[] = $this->bolum_id;
        }

        // 4. Müşteri Şikayeti Çözüm Lideri (Hasan Ekinci)
        if ($this->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            if ($this->bolum_id) {
                $bolumIds[] = $this->bolum_id;
            }
        }

        // 5. Direktör Yetkisi
        if ($this->hasRole('Direktör')) {
            // Direktörlerin yönettiği bölümlerin ID'lerini al
            $yonetilenBolumler = $this->yonetilenBolumler()->pluck('id')->toArray();
            $bolumIds = array_merge($bolumIds, $yonetilenBolumler);
        }

        return array_unique($bolumIds);
    }

    /**
     * Kullanıcının giriş logları
     */
    public function loginActivities()
    {
        return $this->hasMany(\App\Models\LoginActivity::class)->latest();
    }

    /**
     * Şifre sıfırlama bildirimini gönderir.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /**
     * E-posta doğrulama bildirimini gönderir.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }

    /**
     * Kullanıcıyı oluşturan kişiyi (MusteriLog üzerinden) bulmaya çalışır.
     * Bu özellik özellikli "Müşteri Temsilcisi" kullanıcıları için çalışır.
     */
    public function getCreatorAttribute()
    {
        if (!$this->customer_id) {
            return null;
        }

        // MusteriLog tablosunda bu kişi için "Yetkili Ekleme" kaydı var mı?
        // Açıklama alanı: "X, yeni yetkili ekledi: {USER_NAME}"
        $log = \App\Models\MusteriLog::where('customer_id', $this->customer_id)
            ->where('islem_turu', 'Yetkili Ekleme')
            ->where('aciklama', 'LIKE', '%' . $this->name . '%') // İsme göre eşleştirme (riskli ama mevcut yapıda tek yol)
            ->with('user') // Logu oluşturan user
            ->first();

        return $log ? $log->user : null;
    }

    /**
     * Kullanıcının Direktör olarak yönettiği Bölümler
     */
    public function yonetilenBolumler()
    {
        return $this->hasMany(Bolum::class, 'director_id');
    }
}