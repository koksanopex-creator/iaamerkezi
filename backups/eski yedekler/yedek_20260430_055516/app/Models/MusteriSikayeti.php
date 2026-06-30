<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // HasMany'i import et
use App\Models\Iaa;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class MusteriSikayeti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'musteri_sikayetleri';

    protected $fillable = [
        'iaa_id', // <-- YENİ EKLENDİ
        // === YENİ EKLENEN (CRM) ===
        'customer_id', // Hangi Firmadan?
        'yetkili_user_id',
        // ===========================
        'musteri_adi',
        'musteri_iletisim',
        'konum_tipi',
        'musteri_sikayet_konusu',
        'musteri_sikayet_detayi',
        'musteri_urun_veya_hizmet',
        'musteri_barkod_resim_yolu',
        'musteri_sikayet_tarihi',
        'musteri_durum',
        'musteri_oncelik',
        'atanan_cozum_takimi_id',
        'musteri_cozum_notlari',
        'musteri_onay_tarihi',
        'kurul_onay_tarihi',
        'olusturan_kurul_uyesi_id',
        'sikayet_kategorisi_id',
        'sikayet_alt_kategori_id',
        'sikayet_alt_kategori_diger',
        'musteri_cozum_son_tarihi',
        'musteri_ek_sure_talep_durumu',
        'musteri_puan',
        'kazanilan_puan',
        'ek_sure_talep_aciklamasi',
        'ek_sure_red_nedeni',
        'etki_puani',
        'karmasiklik_puani',
        'takip_token',
        'guest_password_hash',
        'musteri_feedback',
        'musteri_feedback_note',
        'edit_locked_at',
        'musteri_bildirim_yapan_id',
        'musteri_bildirim_tarihi',
        'lot_no',
        'machine_id',
        'genel_hammadde_id',
        'urun_versiyonu_id',
        'notified_snapshot'
    ];

    // === BU BLOĞU EKLE ===
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'musteri_sikayet_tarihi' => 'date',
        'musteri_onay_tarihi' => 'datetime',
        'kurul_onay_tarihi' => 'datetime',
        'musteri_cozum_son_tarihi' => 'datetime',
        'edit_locked_at' => 'datetime',
        'musteri_bildirim_tarihi' => 'datetime',
    ];

    // =========================================================
    // === YENİ İLİŞKİ (CRM) ===
    // =========================================================

    /**
     * Şikayetin bağlı olduğu Müşteri Firması (Opsiyonel)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    // === YENİ İLİŞKİYİ EKLE ===
    /**
     * Bu şikayetin dönüştürüldüğü IAA projesini getirir.
     */
    public function iaaProjesi()
    {
        return $this->belongsTo(Iaa::class, 'iaa_id');
    }
    // === İLİŞKİ SONU ===

    public function cozumTakimi()
    {
        // Takim modelinin tam yolunu belirtmek daha güvenli olabilir
        return $this->belongsTo(\App\Models\Takim::class, 'atanan_cozum_takimi_id');
    }

    public function olusturanKurulUyesi()
    {
        return $this->belongsTo(User::class, 'olusturan_kurul_uyesi_id');
    }

    /**
     * Bir şikayetin birden çok dosyası olabilir (hasMany relationship).
     */
    public function dosyalar()
    {
        // 'musteri_sikayeti_id' yabancı anahtarı üzerinden ilişki kurar.
        return $this->hasMany(MusteriSikayetiDosyasi::class, 'musteri_sikayeti_id');
    }

    /**
     * Bir şikayetin ait olduğu kategori (belongsTo relationship).
     */
    public function sikayetKategori()
    {
        // 'sikayet_kategorisi_id' yabancı anahtarı üzerinden ilişki kurar.
        return $this->belongsTo(SikayetKategori::class, 'sikayet_kategorisi_id');
    }

    /**
     * Şikayetin bağlı olduğu Bölüm (Kategori üzerinden)
     */
    public function bolum()
    {
        return $this->hasOneThrough(
            Bolum::class, 
            SikayetKategori::class, 
            'id', // SikayetKategori.id
            'id', // Bolum.id
            'sikayet_kategorisi_id', // MusteriSikayeti.sikayet_kategorisi_id
            'bolum_id' // SikayetKategori.bolum_id
        );
    }

    /**
     * Şikayetin seçilen alt kategorisi
     */
    public function sikayetAltKategori()
    {
        return $this->belongsTo(SikayetAltKategori::class, 'sikayet_alt_kategori_id');
    }

    /**
     * Şikayete ait işlem logları.
     */
    public function loglar(): HasMany
    {
        return $this->hasMany(MusteriSikayetiLog::class, 'musteri_sikayeti_id')->latest(); // En yeniden eskiye
    }

    /**
     * Öncelik durumu için Tailwind CSS sınıfını döndürür.
     * Veritabanındaki 'musteri_oncelik' değerini okur ve CSS sınıfı üretir.
     */
    public function getOncelikBadgeClassAttribute(): string
    {
        switch ($this->musteri_oncelik) {
            case 'Acil':
                return 'bg-red-100 text-red-800';
            case 'Yüksek':
                return 'bg-orange-100 text-orange-800'; // Yüksek için Turuncu
            case 'Normal':
                return 'bg-blue-100 text-blue-800';   // Normal için Mavi
            case 'Düşük':
                return 'bg-gray-100 text-gray-800';   // Düşük için Gri
            default:
                return 'bg-blue-100 text-blue-800';
        }
    }

    /**
     * Durum etiketini HTML olarak döndürür.
     * ÖZELLİK: Eğer bağlı proje "Talep" modundaysa, şikayet durumunu ezer ve Talep durumunu gösterir.
     */
    public function getMusteriDurumBadgeAttribute(): string
    {
        // 1. ÖNCE PROJE DURUMUNA BAK (ÜSTÜNLÜK PROJEDE)
        if ($this->iaaProjesi) {
            $projeDurum = $this->iaaProjesi->durum;

            if ($projeDurum == 'talep_olarak_kapatildi') {
                return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-300 decoration-slice">⚪ Talep Olarak Kapatıldı</span>';
            }
            if ($projeDurum == 'talep_onayi_bekliyor_kalite') {
                return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200 animate-pulse">🟣 Talep Onayı (Kalite)</span>';
            }
            if ($projeDurum == 'talep_onayi_bekliyor_superadmin') {
                return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 animate-pulse">🔵 Talep Onayı (Yönetim)</span>';
            }
            if ($projeDurum == 'Revize Ediliyor') {
                return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border bg-orange-100 text-orange-800 border-orange-200">İşlemde</span>';
            }
        }

        // 2. EĞER PROJE TALEP DEĞİLSE, NORMAL ŞİKAYET DURUMUNA BAK
        $class = '';
        $metin = $this->musteri_durum;

        switch ($this->musteri_durum) {
            case 'Yeni':
                $class = 'bg-blue-100 text-blue-800 border-blue-200';
                break;
            case 'İşlemde':
            case 'İnceleniyor':
            case 'Atandı':
            case 'Devam Ediyor':
                $class = 'bg-orange-100 text-orange-800 border-orange-200';
                $metin = 'İşlemde';
                break;
            case 'Bölüm Onayı Bekliyor': // Mevcut
                $class = 'bg-purple-100 text-purple-800 border-purple-200';
                break;
            case 'Direktör Onayı Bekliyor': // EKLENDİ
                $class = 'bg-pink-100 text-pink-800 border-pink-200';
                break;
            case 'Yönetici Onayı Bekliyor': // Mevcut
                $class = 'bg-orange-100 text-orange-800 border-orange-200';
                break;
            case 'Çözümlendi':
            case 'Kapatıldı':
            case 'Tamamlandı':
                $class = 'bg-green-100 text-green-800 border-green-200';
                break;
            case 'İptal Edildi':
            case 'Reddedildi':
            case 'Revize':
            case 'Tamamlanması Reddedildi':
                $class = 'bg-red-100 text-red-800 border-red-200';
                break;
            default:
                $class = 'bg-gray-100 text-gray-800 border-gray-200';
        }

        return "<span class=\"inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {$class}\">{$metin}</span>";
    }

    /**
     * Duruma göre renk kodunu döndürür.
     * ÖZELLİK: Proje Talep ise rengi ona göre ayarlar.
     */
    public function getDurumRengiAttribute()
    {
        // 1. PROJE KONTROLÜ
        if ($this->iaaProjesi) {
            $projeDurum = $this->iaaProjesi->durum;
            if ($projeDurum == 'talep_olarak_kapatildi')
                return 'gray';
            if ($projeDurum == 'talep_onayi_bekliyor_kalite')
                return 'purple';
            if ($projeDurum == 'talep_onayi_bekliyor_superadmin')
                return 'indigo';
            if ($projeDurum == 'Revize Ediliyor')
                return 'orange'; // İşlemde rengi
        }

        // 2. NORMAL DURUM
        return match ($this->musteri_durum) {
            'Yeni' => 'blue',
            'İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor' => 'orange',
            'Bölüm Onayı Bekliyor' => 'purple',
            'Direktör Onayı Bekliyor' => 'pink', // EKLENDİ
            'Yönetici Onayı Bekliyor' => 'orange',
            'Çözümlendi', 'Kapatıldı', 'Tamamlandı' => 'green',
            'İptal Edildi', 'Reddedildi', 'Revize', 'Tamamlanması Reddedildi' => 'red',
            default => 'gray'
        };
    }



    /**
     * Hatanın Çözümü 1: Şikayete bağlı PROJE üzerinden TÜM yorumları getirir.
     * Bu, withCount(['projeYorumlari']) için gereklidir.
     */
    public function projeYorumlari()
    {
        // hasManyThrough: Bu model (Şikayet), Iaa modeli üzerinden ProjeYorumu'na bağlanır.
        return $this->hasManyThrough(
            ProjeYorumu::class, // Hedef model (Yorumlar)
            Iaa::class,         // Ara model (Projeler)
            'id',               // Iaa tablosundaki 'id' (iaa.id)
            'iaa_id',           // ProjeYorumu tablosundaki 'iaa_id' (proje_yorumlari.iaa_id)
            'iaa_id',           // MusteriSikayeti tablosundaki 'iaa_id' (musteri_sikayetleri.iaa_id)
            'id'                // Iaa tablosundaki 'id' (iaa.id)
        );
    }

    /**
     * Hatanın Çözümü 2: Şikayete bağlı PROJE üzerinden SADECE MÜŞTERİ yorumlarını getirir.
     * Bu, withCount(['musteriProjeYorumlari']) için gereklidir.
     */
    public function musteriProjeYorumlari()
    {
        // Yukarıdaki 'projeYorumlari' ilişkisini kullanır ve onu Müşteri için filtreler
        return $this->projeYorumlari()
            ->whereNull('proje_yorumlari.user_id')
            ->whereNotNull('proje_yorumlari.musteri_sikayeti_id');
    }

    public function yetkili_user()
    {
        // Yetkili kişi "users" tablosunda olduğu için User modeline bağlanmalı
        return $this->belongsTo(\App\Models\User::class, 'yetkili_user_id');
    }

    /**
     * Bu şikayet bir İAA (İyileştirme) projesine dönüştürüldüyse, o projeyi getirir.
     */
    public function iaa()
    {
        // Şikayet tablosundaki 'iaa_id', Iaa tablosundaki 'id'ye gider.
        return $this->belongsTo(Iaa::class, 'iaa_id');
    }

    // Bu dosyaya şu ilişkiyi ekleyin:
    public function iadeler()
    {
        return $this->hasMany(SikayetIadesi::class, 'musteri_sikayeti_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function genelHammadde()
    {
        return $this->belongsTo(GenelHammadde::class, 'genel_hammadde_id');
    }

    public function urunVersiyonu()
    {
        return $this->belongsTo(UrunVersiyonu::class, 'urun_versiyonu_id');
    }

    /**
     * Şikayete ait birden fazla teknik detay (Lot, Makine vb.) olabilir.
     */
    public function teknikDetaylar()
    {
        return $this->hasMany(SikayetTeknikDetay::class, 'musteri_sikayeti_id');
    }

    /**
     * Bu şikayete ait diğer ilgili (ek) yetkililer.
     */
    public function ekYetkililer()
    {
        return $this->belongsToMany(User::class, 'sikayet_ek_yetkililer', 'musteri_sikayeti_id', 'user_id')->withTimestamps();
    }

    /**
     * Bu şikayete ait misafir giriş şifreleri (çoklu alıcı desteği).
     */
    public function guestPasswords()
    {
        return $this->hasMany(SikayetGuestPassword::class, 'musteri_sikayeti_id');
    }

    /**
     * Model booted event.
     */
    protected static function booted()
    {
        static::creating(function ($sikayet) {
            if (!$sikayet->takip_token) {
                // Ensure unique token
                do {
                    $token = Str::random(12);
                } while (static::where('takip_token', $token)->exists());
                
                $sikayet->takip_token = $token;
            }
        });
    }
}
