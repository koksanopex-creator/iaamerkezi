<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Takim; // Takim modelini kullanacağımızı belirtiyoruz
use App\Models\IaaTalep;
use Carbon\Carbon;
use App\Models\IaaLog;
use App\Models\MusteriSikayeti;


class Iaa extends Model
{
    use HasFactory;

    protected $table = 'iaas'; // Tablo adını belirtiyoruz

    protected $fillable = [
        // Kullanıcıların doğrudan doldurduğu alanlar
        'baslik',
        'mevcut_durum',
        'oneri',
        'bolum_id',
        'gonderen_user_id',
        'oneren_kazanc_miktar',
        'oneren_kazanc_birim',
        'oneren_butce_miktar',
        'oneren_butce_birim',

        // Misafir formu için eklenenler
        'guest_name',
        'guest_email',
        'ilgili_alan',

        // Yönetici tarafından veya sistem tarafından güncellenen ve İZİN VERİLMESİ GEREKEN alanlar
        'durum',
        'risk',
        'kazanc_miktar',
        'kazanc_birim',
        'butce_miktar',
        'butce_birim',
        'puan',
        'oncelik',
        'yonetici_notu',
        'onaylayan_user_id',
        'onaylanma_tarihi',
        'tamamlanma_tarihi',
        'atanan_takim_id', // BU İKİSİNİ EKLEMEK SORUNU ÇÖZECEK
        'durum_degistirme_tarihi',
    ];

    // YENİ EKLENİYOR: Veritabanından gelen bu alanları Carbon nesnesine dönüştür
    protected $casts = [
        'onaylanma_tarihi' => 'datetime',
        'tamamlanma_tarihi' => 'datetime',
        'durum_degistirme_tarihi' => 'datetime', // <-- Yeni eklediğimiz tarih
    ];

    /**
     * İAA'yı gönderen kullanıcı.
     */
    public function gonderen()
    {
        return $this->belongsTo(User::class, 'gonderen_user_id');
    }

    /**
     * İAA'yı onaylayan veya reddeden yönetici.
     */
    public function onaylayan()
    {
        return $this->belongsTo(User::class, 'onaylayan_user_id');
    }

    /**
     * İAA'nın ait olduğu bölüm.
     */
    public function bolum()
    {
        return $this->belongsTo(Bolum::class);
    }

    /**
     * İAA'ya ait resimleri getirir.
     */
    public function resimler()
    {
        return $this->hasMany(IaaResim::class);
    }

    /**
     * İAA'nın talep edildiği veya atandığı kullanıcı.
     */
    /**public function atanan()
    {
        return $this->belongsTo(User::class, 'atandi_user_id');
    } */

    /**
     * Bu İAA'nın atandığı takımı döndürür.
     * (belongsTo ilişkisi: Bir İAA bir takıma aittir)
     */
    public function atananTakim()
    {
        return $this->belongsTo(Takim::class, 'atanan_takim_id');
    }

    /**
     * === YENİ İLİŞKİ ===
     * Bu IAA projesinin, hangi müşteri şikayetinden dönüştüğünü getirir.
     * (Bir Proje, bir şikayete aittir - hasOne ters ilişki)
     */
    public function musteriSikayeti()
    {
        // musteri_sikayetleri tablosundaki 'iaa_id' sütunu üzerinden
        return $this->hasOne(MusteriSikayeti::class, 'iaa_id');
    }

    /**
     * Bu İAA'ya talepte bulunan tüm takımları döndürür.
     * (belongsToMany ilişkisi: Bir İAA'ya birden çok takım talep edebilir)
     */
    public function talepEdenTakimlar()
    {
        return $this->belongsToMany(Takim::class, 'iaa_talepleri', 'iaa_id', 'takim_id')
                    ->withPivot('id', 'iaa_workflow_id', 'start_date', 'due_date', 'status')
                    ->withTimestamps();
    }

    /**
     * Bu İAA'nın atandığı iş akışını (workflow) döndürür.
     * Bu ilişki, iaa_talepleri tablosu üzerinden çalışır.
     */
    public function workflow()
    {
        // Bir İAA'nın bir tane talep kaydı ve o talep kaydının da bir tane iş akışı vardır.
        // Bu yüzden "hasOneThrough" ilişkisini kullanıyoruz.
        return $this->hasOneThrough(
            \App\Models\IaaWorkflow::class,      // Ulaşmak istediğimiz en son model
            \App\Models\IaaTalep::class,         // Ara tabloyu temsil eden model
            'iaa_id',                            // IaaTalep modelindeki Iaa anahtarı
            'id',                                // IaaWorkflow modelindeki kendi anahtarı
            'id',                                // Iaa modelindeki kendi anahtarı
            'iaa_workflow_id'                   // IaaTalep modelindeki IaaWorkflow anahtarı
        );
    }

    // YENİ EKLENECEK İLİŞKİ
    public function iaaTalebi()
    {
        // Bir IAA'nın sadece bir tane aktif talebi/atama kaydı olur.
        return $this->hasOne(IaaTalep::class);
    }

    // Projenin kaç günde tamamlandığını hesaplayan akıllı özellik (accessor)
    public function getCompletionDurationInDaysAttribute()
    {
        if ($this->iaaTalebi && $this->iaaTalebi->start_date && $this->onaylanma_tarihi) {
            try {
                $baslangic = new \DateTime($this->iaaTalebi->start_date);
                $bitis = new \DateTime($this->onaylanma_tarihi);
                $interval = $baslangic->diff($bitis);
                
                // DateInterval nesnesinin 'days' özelliği her zaman pozitif toplam gün sayısını verir.
                $gunFarki = $interval->days;

                return $gunFarki == 0 ? 'Aynı gün' : $gunFarki . ' gün';

            } catch (\Exception $e) {
                // Tarih formatı bozuksa veya bir hata oluşursa null döndür.
                return null; 
            }
        }
        
        return null;
    }

    public function logs()
    {
        // 'iaa_id' foreign key'i üzerinden iaa_logs tablosuna bağlanır
        return $this->hasMany(IaaLog::class, 'iaa_id');
    }

    /**
     * Bu projeye ait TÜM yorumları getirir.
     */
    public function yorumlar()
    {
        return $this->hasMany(ProjeYorumu::class, 'iaa_id');
    }

    /**
     * Bu projeye SADECE MÜŞTERİ tarafından yapılan yorumları getirir.
     */
    public function musteriYorumlari()
    {
        // Yorumu misafir yapmışsa user_id'si 'null' olur.
        return $this->hasMany(ProjeYorumu::class, 'iaa_id')
                    ->whereNull('user_id')
                    ->whereNotNull('musteri_sikayeti_id');
    }

    // Bu projeye özel atanmış SQUAD ekibi (Sadece şikayet kaynaklı projeler için kritik)
    public function projeEkibi()
    {
        return $this->belongsToMany(User::class, 'iaa_user', 'iaa_id', 'user_id')
                    ->withPivot('rol', 'kazanilan_puan', 'durum')
                    ->withTimestamps();
    }

}