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
    use HasFactory, Notifiable, HasRoles, SoftDeletes
    {
        hasRole as traitHasRole;
        hasAnyRole as traitHasAnyRole;
        hasAllRoles as traitHasAllRoles;
        hasPermissionTo as traitHasPermissionTo;
    }

    /**
     * Döngüsel (recursive) shadowing kontrollerini engellemek için bayrak.
     */
    public $isShadowInstance = false;

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
    'created_by_id', // <--- YENİ EKLENDİ
    'updated_by_id', // <--- YENİ EKLENDİ
    'dogum_tarihi', // <--- YENİ EKLENDİ
    'rejected_at', // <--- YENİ EKLENDİ

    // === YENİ EKLENENLER (CRM) ===
    'is_personnel', // true: Personel, false: Müşteri Temsilcisi
    'customer_id', // Eğer müşteri temsilcisi ise bağlı olduğu firma ID
    'email_verified_at', // <--- YENİ EKLENDİ
    'require_password_change',
    'dismissed_password_alert',

    // === MAVİ YAKA ===
    'is_mavi_yaka',
    'tc_kimlik_no',
    'sicil_no',
    'hire_date',
    'termination_date',
    'can_intervene_quality',
    'toplam_puan',

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
            'dogum_tarihi' => 'date', // <--- YENİ EKLENDİ
            'is_mavi_yaka' => 'boolean',
            'hire_date' => 'date',
            'termination_date' => 'date',
            'require_password_change' => 'boolean',
            'dismissed_password_alert' => 'boolean',
            'can_intervene_quality' => 'boolean',
            'rejected_at' => 'datetime', // <--- YENİ EKLENDİ
        ];
    }

    /**
     * Check if the user is a blue collar worker (Mavi Yaka)
     */
    public function isMaviYaka(): bool
    {
        return (bool)$this->is_mavi_yaka;
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
    // === YENİ CRM İLİŞKİLERİ VE SCOPE'LAR (FİLTRELER) ===
    // =========================================================

    /**
     * İlişki: Kullanıcının bağlı olduğu Müşteri Firması (Eğer müşteri temsilcisi ise)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * İlişki: Kullanıcının bağlı olduğu TÜM Müşteri Firmaları (Many-to-Many)
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_user')->withPivot('is_active', 'unvan')->withTimestamps();
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
    // === MEVCUT İLİŞKİLERİN (AYNEN KORUNDU) ===
    // =========================================================

    public function bolum()
    {
        return $this->belongsTo(Bolum::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function iaas()
    {
        return $this->hasMany(Iaa::class , 'gonderen_user_id');
    }

    public function takimlar()
    {
        return $this->belongsToMany(Takim::class , 'takim_user', 'user_id', 'takim_id')
            ->withPivot('gorev_tanimi')
            ->withTimestamps();
    }

    public function lideriOlduguTakimlar()
    {
        return $this->hasMany(Takim::class , 'lider_user_id');
    }

    public function yonettigiSikayetKategorileri()
    {
        return $this->belongsToMany(
            SikayetKategori::class ,
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
        return $this->belongsToMany(Iaa::class , 'iaa_user', 'user_id', 'iaa_id')
            ->withPivot('rol', 'kazanilan_puan', 'durum')
            ->withTimestamps();
    }

    public function disiplinDosyalari()
    {
        return $this->hasMany(\App\Models\DisciplinaryCase::class , 'user_id');
    }

    public function raporladigiDisiplinDosyalari()
    {
        return $this->hasMany(\App\Models\DisciplinaryCase::class , 'reporter_id');
    }

    public function kullaniciIstekleri()
    {
        return $this->hasMany(KullaniciIstek::class, 'user_id')->latest();
    }

    public function girdigiSikayetler()
    {
        return $this->hasMany(MusteriSikayeti::class, 'olusturan_kurul_uyesi_id');
    }

    public function getAllowedBolumIds()
    {
        $user = $this->getEffectiveUser();

        // 1. Superadmin/Yonetim/Kurul/Müşteri ise tüm bölümleri görsün
        if ($user->traitHasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Müşteri', 'Müşteri Temsilcisi']))
        {
            return '*';
        }

        $bolumIds = [];

        // 2. Bölüm Kalite Yöneticisi
        if ($user->traitHasRole('Bölüm Kalite Yöneticisi'))
        {
            $yonetilenBolumler = $user->yonettigiSikayetKategorileri()
                ->pluck('bolum_id')
                ->unique()
                ->toArray();

            $bolumIds = array_merge($bolumIds, $yonetilenBolumler);
        }

        // 3. Bölüm Lideri
        if ($user->traitHasRole('Bölüm Lideri') && $user->bolum_id)
        {
            $bolumIds[] = $user->bolum_id;
        }

        // 4. Müşteri Şikayeti Çözüm Lideri
        if ($user->traitHasRole('Müşteri Şikayeti Çözüm Lideri'))
        {
            if ($user->bolum_id)
            {
                $bolumIds[] = $user->bolum_id;
            }
            
            // Lideri olduğu takımların varsayılan olarak atandığı kategorilerin bölüm ID'lerini de ekle
            $takimIds = $user->lideriOlduguTakimlar()->pluck('id')->toArray();
            if (!empty($takimIds)) {
                $kategoriBolumIds = \App\Models\SikayetKategori::whereIn('varsayilan_takim_id', $takimIds)
                                      ->whereNotNull('bolum_id')
                                      ->pluck('bolum_id')->toArray();
                $bolumIds = array_merge($bolumIds, $kategoriBolumIds);
            }
        }

        // 5. Direktör Yetkisi
        if ($user->traitHasRole('Direktör'))
        {
            $yonetilenBolumler = $user->yonetilenBolumler()->pluck('id')->toArray();
            $bolumIds = array_merge($bolumIds, $yonetilenBolumler);
        }

        // 6. Bölüm Lider Yardımcısı
        if ($user->isDepartmentDeputy() && $user->bolum_id)
        {
            $bolumIds[] = $user->bolum_id;
        }

        // 7. Müşteri Saha Temsilcisi
        if ($user->traitHasRole('Müşteri Saha Temsilcisi'))
        {
            $temsilciBolumleri = $user->musteriSahaTemsilcisiOlduguBolumler()->pluck('bolumler.id')->toArray();
            $bolumIds = array_merge($bolumIds, $temsilciBolumleri);
        }

        return array_unique($bolumIds);
    }

    // === SHADOWING (GÖZLEMCİ MODU) MANTIÄI ===

    public function observers()
    {
        return $this->belongsToMany(User::class , 'user_observers', 'target_user_id', 'observer_user_id')->withTimestamps();
    }

    public function observedUsers()
    {
        return $this->belongsToMany(User::class , 'user_observers', 'observer_user_id', 'target_user_id')->withTimestamps();
    }

    /**
     * Shadowing sırasında her yetki kontrolünde veritabanı sorgusunu önlemek için statik önbellek.
     */
    protected static $resolvedEffectiveUsers = [];

    /**
     * Kullanıcının şu an izlediği (shadow) kullanıcıyı döner.
     */
    public function getEffectiveUser()
    {
        $thisId = parent::getAttribute('id');

        // 1. Önce statik önbelleğe bak (Request boyunca bu spesifik kullanıcı için bir kere çözülsün)
        if (isset(static::$resolvedEffectiveUsers[$thisId]))
        {
            return static::$resolvedEffectiveUsers[$thisId];
        }

        // 2. Eğer bu instance zaten bir "gölge" instance ise direkt kendisini dön
        if ($this->isShadowInstance)
        {
            return $this;
        }

        // 3. Shadowing parametrelerini al
        // session() helper'ı request dışında hata vermesin diye guard ekleyelim
        $originalId = null;
        $targetId = null;
        try
        {
            $originalId = session('shadowing_original_user_id');
            $targetId = session('active_shadow_user_id');
        }
        catch (\Exception $e)
        {
        }

        // SADECE oturum sahibi olan kullanıcı nesnesi için shadowing uygula
        if ($originalId && $targetId && $thisId == $originalId)
        {

            // Veritabanından yetki kontrolü yap
            $isAuthorized = \Illuminate\Support\Facades\DB::table('user_observers')
                ->where('observer_user_id', $originalId)
                ->where('target_user_id', $targetId)
                ->exists();

            if ($isAuthorized)
            {
                // Hedef kullanıcıyı tüm yetki ve rolleriyle çek
                $targetUser = self::with(['roles', 'permissions'])->find($targetId);
                if ($targetUser)
                {
                    $targetUser->isShadowInstance = true;
                    static::$resolvedEffectiveUsers[$thisId] = $targetUser;
                    return $targetUser;
                }
            }
        }

        static::$resolvedEffectiveUsers[$thisId] = $this;
        return $this;
    }

    /**
     * Öznitelik erişimini shadowing durumunda hedef kullanıcıya yönlendirir.
     * Bu sayede $user->id, $user->bolum_id vb. çağrılar hedef kullanıcının verilerini döner.
     */
    public function getAttribute($key)
    {
        // Gölge nesneyse veya shadowing parametreleri yoksa parent'tan al
        if ($this->isShadowInstance || !session()->has('active_shadow_user_id'))
        {
            return parent::getAttribute($key);
        }

        // SADECE oturumu açan "asıl" kullanıcı nesnesi için proxy yap
        $originalId = session('shadowing_original_user_id');
        if ($originalId && parent::getAttribute('id') == $originalId)
        {
            $effectiveUser = $this->getEffectiveUser();
            if ($effectiveUser !== $this)
            {
                // Tüm öznitelik veya ilişki çağrılarını hedef kullanıcıdan döndür
                return $effectiveUser->parentGetAttribute($key);
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * Parent'ın getAttribute metoduna erişim (Döngü kırmak için)
     */
    public function parentGetAttribute($key)
    {
        return parent::getAttribute($key);
    }

    /**
     * Shadowing olsa bile orijinal kullanıcı verisine erişmek için.
     */
    public function getRealAttribute($key)
    {
        return parent::getAttribute($key);
    }

    public function isShadowing()
    {
        return session()->has('active_shadow_user_id');
    }

    // Role metodlarını override ediyoruz ki shadowing durumunda hedef kullanıcının yetkileri geçerli olsun.

    public function hasRole($roles, $guard = null): bool
    {
        $user = $this->getEffectiveUser();
        return ($user->id === $this->id) ? $this->traitHasRole($roles, $guard) : $user->traitHasRole($roles, $guard);
    }

    public function hasAnyRole(...$roles): bool
    {
        $user = $this->getEffectiveUser();
        return ($user->id === $this->id) ? $this->traitHasAnyRole(...$roles) : $user->traitHasAnyRole(...$roles);
    }

    public function hasAllRoles($roles, $guard = null): bool
    {
        $user = $this->getEffectiveUser();
        return ($user->id === $this->id) ? $this->traitHasAllRoles($roles, $guard) : $user->traitHasAllRoles($roles, $guard);
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $user = $this->getEffectiveUser();
        return ($user->id === $this->id) ? $this->traitHasPermissionTo($permission, $guardName) : $user->traitHasPermissionTo($permission, $guardName);
    }

    public function can($ability, $arguments = []): bool
    {
        $user = $this->getEffectiveUser();
        return ($user->id === $this->id) ? $this->parentCan($ability, $arguments) : $user->parentCan($ability, $arguments);
    }

    /**
     * Parent can() metoduna erişim için yardımcı metod.
     */
    protected function parentCan($ability, $arguments = [])
    {
        return parent::can($ability, $arguments);
    }

    /**
     * Kullanıcının giriş logları
     */
    public function loginActivities()
    {
        return $this->hasMany(\App\Models\LoginActivity::class)->latest();
    }

    /**
     * Şifre sıfırlama bildirimini gönderir.
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
        if (!$this->customer_id)
        {
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
        return $this->hasMany(Bolum::class , 'director_id');
    }

    public function musteriSahaTemsilcisiOlduguBolumler()
    {
        return $this->belongsToMany(Bolum::class, 'musteri_saha_temsilcisi_bolum', 'user_id', 'bolum_id')->withTimestamps();
    }

    /**
     * Role Intelligence: Kullanıcı Direktör mü?
     */
    public function isDirector(): bool
    {
        return $this->yonetilenBolumler()->exists();
    }

    /**
     * Role Intelligence: Kullanıcı Bölüm Lideri (Müdür) mü?
     */
    public function isDepartmentLeader(): bool
    {
        return $this->hasRole('Bölüm Lideri'); // Role ID 2
    }

    /**
     * Kullanıcının sorumlu olduğu bölümlerin ID'lerini döner
     */
    public function getResponsibleDepartments(): array
    {
        if ($this->hasRole(['Superadmin', 'Yonetim']))
        {
            return Bolum::pluck('id')->toArray();
        }

        $ids = [];

        // Direktör olarak sorumlu olduğu bölümler
        $ids = array_merge($ids, $this->yonetilenBolumler()->pluck('id')->toArray());

        // Kendi bölümü (Eğer liderse veya personelse)
        if ($this->bolum_id)
        {
            $ids[] = $this->bolum_id;
        }

        return array_unique($ids);
    }

    /**
     * Role Intelligence: Kullanıcının Müşteri Şikayeti modülüyle "Organik Bağı" var mı?
     * logic: Görevi, onay yetkisi, atanmış şikayeti veya şikayetle ilgili bölümleri olanlar.
     */
    public function hasSikayetOrganikBagi(): bool
    {
        $user = $this->getEffectiveUser();

        // 1. Üst Yönetim ve Kurul her zaman yetkilidir
        if ($user->hasAnyRole(['Superadmin', 'Yonetim', 'Yönetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            return true;
        }

        // 2. Operasyonel Bağ Kontrolü (Bölüm-Kategori eşleşmesi)
        if ($this->canSeeMusteriOperasyonlari()) {
            return true;
        }

        // 3. Görev ve Atama Bazlı Kontroller (Ziyaretler, Takımlar vb.)

        // a) Aktif bizzat atandığı veya personelinin atandığı bir ziyaret planı var mı?
        if ($this->hasZiyaretGorevi()) {
            return true;
        }

        // b) Bir şikayet çözüm takımında aktif görevli mi?
        $takimdaMi = \Illuminate\Support\Facades\DB::table('takim_user')
            ->join('takimlar', 'takim_user.takim_id', '=', 'takimlar.id')
            ->where('takim_user.user_id', $user->id)
            ->where('takimlar.tur', 'sikayet')
            ->exists();
        if ($takimdaMi) return true;

        // c) Hukuk Onayı bekleyen bir süreçte yetkili mi?
        if ($user->hasAnyRole(['Hukuk Admini', 'Hukuk Yöneticisi', 'Arabuluculuk Personel'])) {
            $hukukOnayiBekleyenVarMi = \App\Models\MusteriSikayeti::where('musteri_durum', 'Hukuk Onayı Bekliyor')->exists();
            if ($hukukOnayiBekleyenVarMi) return true;
        }

        return false;
    }

    /**
     * Kullanıcının veya (liderse) personelinin atanmış aktif bir ziyareti var mı?
     */
    public function hasZiyaretGorevi(): bool
    {
        $user = $this->getEffectiveUser();
        $ziyaretPlaniSorgu = \App\Models\IaaZiyaretPlani::whereNotIn('status', ['Tamamlandı', 'İptal Edildi']);
        
        // Bizzat kendisi
        if ((clone $ziyaretPlaniSorgu)->where('visitor_id', $user->id)->exists()) {
            return true;
        }

        // Personeli (Lider ve Direktörler için)
        if ($user->hasAnyRole(['Bölüm Lideri', 'Direktör'])) {
            $checkBolumIds = [];
            if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
                $checkBolumIds[] = $user->bolum_id;
            }
            if ($user->hasRole('Direktör')) {
                $checkBolumIds = array_merge($checkBolumIds, $user->yonetilenBolumler()->pluck('id')->toArray());
            }

            if (!empty($checkBolumIds)) {
                return (clone $ziyaretPlaniSorgu)->whereHas('visitor', function($v) use ($checkBolumIds) {
                    $v->whereIn('bolum_id', $checkBolumIds);
                })->exists();
            }
        }

        return false;
    }

    /**
     * Role Intelligence: Kullanıcı Bölüm Lider Yardımcısı mı?
     */
    public function isDepartmentDeputy(): bool
    {
        return $this->hasRole('Bölüm Lider Yardımcısı');
    }

    /**
     * Kullanıcının matris üzerinden verilmiş spesifik bir bölüm yetkisi olup olmadığını kontrol eder.
     * Hem rolü (Bölüm Lider Yardımcısı) hem de spesifik izni kontrol eder.
     */
    public function hasBolumAuthority($permission): bool
    {
        return $this->isDepartmentDeputy() && $this->hasPermissionTo($permission);
    }

    /**
     * Kullanıcı Müşteri Listesi, Şikayet Paneli vb. operasyonel linkleri görebilir mi?
     * (Bölüm-Kategori bağı şartına bağlıdır)
     */
    public function canSeeMusteriOperasyonlari(): bool
    {
        $user = $this->getEffectiveUser();

        // Admin ve operasyonel roller her zaman görür
        if ($user->hasAnyRole(['Superadmin', 'Yonetim', 'Yönetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Bölüm Kalite Yöneticisi', 'Müşteri Şikayeti Çözüm Lideri', 'Müşteri Saha Temsilcisi'])) {
            return true;
        }

        // Direktör: Sorumlu olduğu bölümlerden biri kategoriye bağlıysa
        if ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();
            return \App\Models\SikayetKategori::whereIn('bolum_id', $yonetilenBolumIds)->exists();
        }

        // Bölüm Lideri: Kendi bölümü şikayet kategorisine bağlıysa
        if ($user->hasRole('Bölüm Lideri')) {
            return $user->bolum_id && \App\Models\SikayetKategori::where('bolum_id', $user->bolum_id)->exists();
        }

        // Bölüm Lider Yardımcısı: Şikayet veya İade görme yetkisi varsa ve bölümü kategoriye bağlıysa
        if ($user->hasBolumAuthority('bolum.sikayet.gor') || $user->hasBolumAuthority('bolum.iade.gor')) {
            return $user->bolum_id && \App\Models\SikayetKategori::where('bolum_id', $user->bolum_id)->exists();
        }

        // Müşteri Saha Temsilcisi: Sorumlu olduğu bölümlerden biri kategoriye bağlıysa
        if ($user->hasRole('Müşteri Saha Temsilcisi')) {
            $temsilciBolumleri = $user->musteriSahaTemsilcisiOlduguBolumler()->pluck('bolumler.id')->toArray();
            return !empty($temsilciBolumleri) && \App\Models\SikayetKategori::whereIn('bolum_id', $temsilciBolumleri)->exists();
        }

        return false;
    }

    /**
     * Kullanıcının ünvanını veya ünvan yoksa sistemdeki rollerini döner.
     */
    public function getDisplayUnvanAttribute()
    {
        if ($this->unvan) {
            return $this->unvan;
        }

        // Atanan tüm rollerin isimlerini virgülle birleştir
        $roleNames = $this->roles->pluck('name');
        
        if ($roleNames->isNotEmpty()) {
            return $roleNames->implode(', ');
        }

        return 'Personel';
    }

    /**
     * Müşterinin bağlı olduğu firma isimlerini virgülle ayrılmış olarak döner.
     */
    public function getFirmaAdlariAttribute()
    {
        $firmNames = collect();
        if ($this->customer) {
            $firmNames->push($this->customer->name);
        }
        foreach ($this->customers as $cust) {
            $firmNames->push($cust->name);
        }
        return $firmNames->unique()->implode(', ');
    }


    /**
     * Get the user's profile photo URL.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path)
        {
            return asset('storage/' . $this->profile_photo_path);
        }

        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF";
    }

    /**
     * Determines if the user is allowed to view the /ziyaretler page and menu.
     */
    public function canViewZiyaretlerPage()
    {
        if ($this->hasRole(['Superadmin', 'Yonetim'])) {
            return true;
        }

        // 1. Üretim / Şikayet Bölümü Yöneticisi mi? (getAllowedBolumIds üzerinden)
        $allowedBolumIds = $this->getAllowedBolumIds();
        if ($allowedBolumIds === '*' || !empty($allowedBolumIds)) {
            return true;
        }

        // 2. Kendisi herhangi bir ziyarette görevli mi?
        $isVisitor = \App\Models\IaaZiyaretPlani::where('visitor_id', $this->id)
            ->orWhereJsonContains('visitors', (string)$this->id)
            ->orWhereJsonContains('visitors', $this->id)
            ->exists();
            
        if ($isVisitor) {
            return true;
        }

        // 3. Bölüm Lideri/Direktörü/Yardımcı Lideri olduğu personellerden herhangi biri görevli mi?
        $yonetilenBolumIds = [];
        if ($this->hasRole('Direktör')) {
            $yonetilenBolumIds = $this->yonetilenBolumler()->pluck('bolumler.id')->toArray();
        } elseif ($this->hasRole('Bölüm Lideri') && $this->bolum_id) {
            $yonetilenBolumIds[] = $this->bolum_id;
        } elseif ($this->hasRole('Bölüm Lider Yardımcısı') && $this->bolum_id && $this->hasPermissionTo('bolum.ziyaret.gor')) {
            $yonetilenBolumIds[] = $this->bolum_id;
        }

        if (!empty($yonetilenBolumIds)) {
            $personelIds = self::whereIn('bolum_id', $yonetilenBolumIds)->pluck('id')->toArray();
            if (!empty($personelIds)) {
                $hasPersonnelVisit = \App\Models\IaaZiyaretPlani::whereIn('visitor_id', $personelIds)
                    ->orWhere(function ($q) use ($personelIds) {
                        foreach ($personelIds as $pId) {
                            $q->orWhereJsonContains('visitors', (string)$pId)
                              ->orWhereJsonContains('visitors', $pId);
                        }
                    })->exists();
                
                if ($hasPersonnelVisit) {
                    return true;
                }
            }
        }

        return false;
    }
}
