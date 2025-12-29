<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // HasMany'i import et
use App\Models\Iaa; // <-- BU SATIRI en üste (use'ların yanına) EKLE

class MusteriSikayeti extends Model
{
    use HasFactory;

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
        'etki_puani',
        'karmasiklik_puani',
        'takip_token',
        'guest_password_hash',
        'musteri_feedback',
        'musteri_feedback_note',
        'edit_locked_at',
        'musteri_bildirim_yapan_id',
        'musteri_bildirim_tarihi'
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
     * Durum için Tailwind CSS badge HTML'ini döndürür.
     * Veritabanındaki 'musteri_durum' değerini okur ve HTML üretir.
     */
    public function getMusteriDurumBadgeAttribute(): string
    {
        $class = '';
        switch ($this->musteri_durum) {
            case 'Yeni':
                $class = 'bg-yellow-100 text-yellow-800';
                break;
            case 'İşlemde':
                $class = 'bg-blue-100 text-blue-800';
                break;
            case 'Çözümlendi':
            case 'Kapatıldı':
                $class = 'bg-green-100 text-green-800';
                break;
            case 'Yeniden Açıldı': // Senaryonuz için eklendi
            case 'Revize':       // Senaryonuz için eklendi
                $class = 'bg-red-100 text-red-800';
                break;
            default:
                $class = 'bg-gray-100 text-gray-800';
        }
        
        // Doğrudan HTML olarak hazır bir badge (etiket) döndürüyoruz
        return "<span class=\"inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {$class}\">{$this->musteri_durum}</span>";
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

    

}
