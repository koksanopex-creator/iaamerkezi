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
        'notified_snapshot',
        'mail_sent',
        'mail_error'
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
     * ÖZELLİK: Eğer bağlı bir proje (IAA) varsa, ana durum 'İşlemde' olarak gösterilir, 
     * çünkü detaylı proje durumu zaten alt etiket olarak gösterilmektedir.
     */
    public function getMusteriDurumBadgeAttribute(): string
    {
        // 1. ÖNCE PROJE DURUMUNA BAK (EĞER PROJE VARSA ANA DURUMU PROJEDEN AL)
        if ($this->iaaProjesi) {
            $projeDurum = $this->iaaProjesi->durum;

            // Talep süreçleri
            if (str_contains(strtolower($projeDurum), 'talep')) {
                if (in_array(strtoupper($projeDurum), ['TALEP_OLARAK_KAPATILDI', 'TALEP_OLARAK_KAPATİLDİ'])) {
                    return '<span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-purple-50 text-purple-600 border border-purple-100 uppercase tracking-tight">🟣 Talep Olarak Kapatıldı</span>';
                }
                
                // Eğer özel bir onay/bekleme durumundaysa direkt projenin detaylı etiketini kullan
                if (str_contains(strtolower($projeDurum), 'bekliyor')) {
                    return $this->iaaProjesi->durum_etiketi;
                }

                return '<span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-purple-50 text-purple-700 border border-purple-100 animate-pulse uppercase tracking-tight">🟣 İşlemde (Müşteri Talebidir)</span>';
            }

            // Hatalı bildirim süreçleri
            if (str_contains(strtolower($projeDurum), 'hatali_bildirim')) {
                if ($projeDurum == 'hatali_bildirim_olarak_kapatildi') {
                    return '<span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-orange-50 text-orange-500 border border-orange-100 line-through uppercase tracking-tight">🟠 Hatalı Bildirim</span>';
                }

                if (str_contains(strtolower($projeDurum), 'bekliyor')) {
                    return $this->iaaProjesi->durum_etiketi;
                }

                return '<span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-orange-50 text-orange-600 border border-orange-100 animate-pulse uppercase tracking-tight">🟠 İşlemde (Hatalı Bildirim)</span>';
            }

            // Normal projeler için projenin kendi etiketini kullan
            return $this->iaaProjesi->durum_etiketi;
        }

        // 2. EĞER PROJE TALEP DEĞİLSE, NORMAL ŞİKAYET DURUMUNA BAK
        $class = '';
        $metin = $this->musteri_durum;

        switch ($this->musteri_durum) {
            case 'Yeni':
                $class = 'bg-blue-50 text-blue-600 border-blue-100';
                break;
            case 'İşlemde':
            case 'İnceleniyor':
            case 'Atandı':
            case 'Devam Ediyor':
                $class = 'bg-orange-50 text-orange-600 border-orange-100';
                $metin = 'İşlemde';
                break;
            case 'Bölüm Onayı Bekliyor':
                $class = 'bg-purple-50 text-purple-600 border-purple-100';
                $metin = 'Bölüm Kalite Yöneticisi Onayı Bekliyor';
                break;
            case 'Direktör Onayı Bekliyor':
                $class = 'bg-purple-50 text-purple-600 border-purple-100';
                break;
            case 'Yönetici Onayı Bekliyor':
                $class = 'bg-orange-50 text-orange-600 border-orange-100';
                $metin = 'Final Onay Bekliyor';
                break;
            case 'Çözümlendi':
            case 'Kapatıldı':
            case 'Tamamlandı':
                $class = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                break;
            case 'İptal Edildi':
            case 'Reddedildi':
            case 'Revize':
            case 'Tamamlanması Reddedildi':
                $class = 'bg-rose-50 text-rose-600 border-rose-100';
                break;
            default:
                $class = 'bg-slate-50 text-slate-600 border-slate-100';
        }

        // Bekleme süresini ekle (Onay bekleyen veya aktif süreçler için)
        $isWaiting = in_array($this->musteri_durum, ['Yeni', 'Atandı', 'İnceleniyor', 'Devam Ediyor', 'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor']);
        if ($isWaiting) {
            $tarih = $this->updated_at; 
            $gun = ceil(\Carbon\Carbon::parse($tarih)->diffInMinutes(now()) / (24 * 60));
            if ($gun < 1) $gun = 1;
            $metin .= " <span class='block text-[9px] opacity-80 font-bold mt-0.5 lowercase tracking-wider'>({$gun} gündür)</span>";
        }

        return "<span class=\"inline-flex flex-col items-center justify-center px-3 py-1.5 rounded-xl text-[10px] font-black border uppercase tracking-tight text-center leading-tight {$class}\">{$metin}</span>";
    }

    public function getMusteriDurumBadgeAnalizAttribute()
    {
        $html = $this->musteri_durum_badge;
        // Eğer etiket içinde (<span ... gündür) varsa onu kaldır
        $html = preg_replace("/\s*<span[^>]*>\(\d+\s*gündür\)<\/span>/i", "", $html);
        
        // CSS sınıflarını düzenle: whitespace-normal max-w-[120px] veya w-full ekle, flex-col kalabilir
        $html = str_replace('inline-flex', 'inline-flex w-full min-w-[120px] max-w-[160px] whitespace-normal', $html);

        return $html;
    }

    public function getMusteriSureBilgisiAttribute()
    {
        $isWaiting = in_array($this->musteri_durum, ['Yeni', 'Atandı', 'İnceleniyor', 'Devam Ediyor', 'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor']) || ($this->iaaProjesi && !in_array($this->iaaProjesi->durum, ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'İptal Edildi', 'Reddedildi']));
        $isCompleted = in_array($this->musteri_durum, ['Çözümlendi', 'Kapatıldı', 'Tamamlandı']) || ($this->iaaProjesi && in_array($this->iaaProjesi->durum, ['Tamamlandı', 'talep_olarak_kapatildi']));

        if ($isCompleted) {
            $baslangic = $this->created_at; 
            $bitis = $this->musteri_cozum_son_tarihi ?? $this->updated_at;
            $gun = ceil(\Carbon\Carbon::parse($baslangic)->diffInMinutes($bitis) / (24 * 60));
            if ($gun < 1) $gun = 1;
            return "<span class='text-emerald-600 font-bold text-[11px] bg-emerald-50 px-2 py-1.5 rounded-xl border border-emerald-100 block text-center whitespace-nowrap'>✓ {$gun} günde bitti</span>";
        }

        if ($isWaiting) {
            $tarih = $this->updated_at; 
            $gun = ceil(\Carbon\Carbon::parse($tarih)->diffInMinutes(now()) / (24 * 60));
            if ($gun < 1) $gun = 1;
            return "<span class='text-orange-600 font-bold text-[11px] bg-orange-50 px-2 py-1.5 rounded-xl border border-orange-100 block text-center whitespace-nowrap'>⏱ {$gun} gündür bekliyor</span>";
        }

        return "<span class='text-gray-400 font-medium text-[11px]'>-</span>";
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
            if (str_contains(strtolower($projeDurum), 'talep'))
                return 'purple';
            if (str_contains(strtolower($projeDurum), 'hatali_bildirim'))
                return 'orange';
            if ($projeDurum == 'Revize Ediliyor')
                return 'orange'; // İşlemde rengi
        }

        // 2. NORMAL DURUM
        return match ($this->musteri_durum) {
            'Yeni' => 'blue',
            'İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor' => 'orange',
            'Bölüm Onayı Bekliyor' => 'purple',
            'Direktör Onayı Bekliyor' => 'purple',
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
     * Şikayete atanan ana yetkilinin bu firma için tanımlanmış ünvanını döner.
     */
    public function getYetkiliUnvaniAttribute()
    {
        if (!$this->yetkili_user_id || !$this->customer_id) {
            return null;
        }

        $pivot = \Illuminate\Support\Facades\DB::table('customer_user')
            ->where('customer_id', $this->customer_id)
            ->where('user_id', $this->yetkili_user_id)
            ->first();

        return $pivot?->unvan ?? $this->yetkili_user?->unvan;
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
     * Şikayete ait hatırlatmalar.
     */
    public function hatirlatmalar()
    {
        return $this->hasMany(SikayetHatirlatma::class, 'musteri_sikayeti_id');
    }

    /**
     * Şikayetin aktif (açık) hatırlatması.
     */
    public function aktifHatirlatma()
    {
        return $this->hasOne(SikayetHatirlatma::class, 'musteri_sikayeti_id')
            ->whereIn('durum', ['bilgi_girisi_bekleniyor', 'bilgi_girildi'])
            ->latest();
    }

    /**
     * Şikayet için aktif bir hatırlatma süreci var mı?
     */
    public function getIsHatirlatmaBekliyorAttribute(): bool
    {
        return $this->aktifHatirlatma()->exists();
    }

    /**
     * Müşteri Dashboard için hatırlatma durumu ve cooldown verisi
     */
    public function getMusteriHatirlatmaDurumuAttribute()
    {
        // En son hatırlatmayı bul (tüm durumlar dahil)
        $sonHatirlatma = $this->hatirlatmalar()->latest()->first();
        
        // [YENİ] İlk aktifleşme süresi kontrolü (Eğer henüz hiç hatırlatma gönderilmemişse)
        if (!$sonHatirlatma) {
            $ilkAktifSaat = (float) \App\Models\Setting::get('hatirlatma_ilk_aktif_saat', 0);
            if ($ilkAktifSaat > 0) {
                $ilkAktifTarihi = $this->created_at->copy()->addMinutes($ilkAktifSaat * 60);
                if ($ilkAktifTarihi->isFuture()) {
                    $kalan = now()->diffForHumans($ilkAktifTarihi, true);
                    return [
                        'can_send' => false,
                        'message' => "Yeni Hatırlatma İçin: {$kalan}",
                        'id' => null
                    ];
                }
            }
        }

        // Mevcut hatirlatma varsa cooldown kontrolü
        if ($sonHatirlatma) {
            // Cooldown kontrolü (24 saat veya ayarlardaki süre)
            $cooldownSaat = (float) \App\Models\Setting::get('hatirlatma_cooldown_saat', 24);
            $sonrakiHak = \Carbon\Carbon::parse($sonHatirlatma->son_hatirlatma_tarihi)->addMinutes($cooldownSaat * 60);
            
            if ($sonrakiHak->isFuture()) {
                $kalan = now()->diffForHumans($sonrakiHak, true);
                return [
                    'can_send' => false,
                    'message' => "Yeni Hatırlatma İçin: {$kalan}",
                    'id' => $sonHatirlatma->id
                ];
            }
        } 
        
        return [
            'can_send' => true,
            'message' => 'HATIRLAT'
        ];
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
