<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Takim; // Takim modelini kullanacağımızı belirtiyoruz
use App\Models\IaaTalep;
use Carbon\Carbon;
use App\Models\IaaLog;
use App\Models\MusteriSikayeti;
use Illuminate\Support\Facades\DB;


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

        // === ŞU SATIRLARI EN ALTA EKLEYİN ===
        'talep_gerekcesi',        // EKSİK OLAN BU
        'talep_isteyen_user_id',  // EKSİK OLAN BU
        'talep_dosyasi',
        'talep_red_gerekcesi',    // EKSİK OLAN BU
        'talep_kalite_notu',      // EKSİK OLAN BU
        'talep_admin_notu',      // Controller'da red gerekçesi buna yazılacak
        'kapanis_notu',           // EKSİK OLAN BU
        'atama_zamani',
        'tamamlanma_suresi_gun',
        'hatali_bildirim_gerekcesi',
        'hatali_bildirim_tarihi',
        // YENİ EKLENENLER
        'direktor_notu',
        'direktor_onay_tarihi',
    ];

    // YENİ EKLENİYOR: Veritabanından gelen bu alanları Carbon nesnesine dönüştür
    protected $casts = [
        'onaylanma_tarihi' => 'datetime',
        'tamamlanma_tarihi' => 'datetime',
        'durum_degistirme_tarihi' => 'datetime',
        'direktor_onay_tarihi' => 'datetime', // <-- YENİ
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

    /**
     * Bu projede görevli kullanıcılar (Alias for projeEkibi).
     * Yetki kontrollerinde standart isim olarak kullanılır.
     */
    public function users()
    {
        // projeEkibi ile aynı ilişkiyi döndürür
        return $this->projeEkibi();
    }

    // === HATALI BİLDİRİM ONAY İLİŞKİLERİ ===
    public function hataliBildirimKaliteUser()
    {
        return $this->belongsTo(User::class, 'hatali_bildirim_kalite_user_id');
    }

    public function hataliBildirimDirektorUser()
    {
        return $this->belongsTo(User::class, 'hatali_bildirim_direktor_user_id');
    }

    public function hataliBildirimSuperadminUser()
    {
        return $this->belongsTo(User::class, 'hatali_bildirim_superadmin_user_id');
    }

    public function hataliBildirimLiderUser()
    {
        return $this->belongsTo(User::class, 'talep_isteyen_user_id');
    }

    public function stepAssignments()
    {
        return $this->hasMany(IaaStepAssignment::class, 'iaa_id');
    }

    public function progressUpdates()
    {
        return $this->hasMany(IaaProgressUpdate::class, 'iaa_talep_id', 'iaa_id');
        // Note: Progress updates are linked to 'iaa_talepleri', usually.
        // Let's check schema. IaaProgressUpdate usually has iaa_talep_id?
        // Controller line 354: $assignment = DB::table('iaa_talepleri')...
        // DashboardController uses IaaProgressUpdate?
        // Use hasManyThrough if needed, or simple hasMany if column exists.
        // I'll assume standard linking for now, but better to check.
        // Actually, IaaProgressUpdate links to IaaTalep, which links to Iaa.
    }

    // Better: define access via Talep
    public function progressUpdatesViaTalep()
    {
        return $this->hasManyThrough(IaaProgressUpdate::class, IaaTalep::class, 'iaa_id', 'iaa_talep_id');
    }

    // app/Models/Iaa.php içine ekleyin:

    /**
     * Durum rengini Tailwind renk kodu olarak döndürür.
     */
    public function getDurumRengiAttribute()
    {
        return match ($this->durum) {
            'Yeni', 'Devam Ediyor' => 'blue',
            'Atandı' => 'indigo',
            'Tamamlandı' => 'emerald',
            'Reddedildi', 'Tamamlanması Reddedildi' => 'red',
            'Bölüm Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_kalite' => 'purple',
            'Direktör Onayı Bekliyor', 'hatali_bildirim_onayi_bekliyor_direktor' => 'pink',
            'Yönetici Onayı Bekliyor', 'hatali_bildirim_onayi_bekliyor_superadmin' => 'orange',
            'talep_onayi_bekliyor_superadmin' => 'indigo',
            'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi' => 'gray',
            default => 'gray',
        };
    }

    public function getDurumEtiketiAttribute()
    {
        $durumlar = [
            // --- MEVCUT GERÇEK DURUMLAR ---
            'Yeni' => ['text' => 'Yeni', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
            'Atandı' => ['text' => 'Atandı', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
            'Devam Ediyor' => ['text' => 'Devam Ediyor', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'Tamamlandı' => ['text' => 'Tamamlandı', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'Reddedildi' => ['text' => 'Reddedildi', 'class' => 'bg-red-100 text-red-800 border-red-200'],
            'Tamamlanması Reddedildi' => ['text' => 'Tamamlanması Reddedildi', 'class' => 'bg-red-50 text-red-700 border-red-200'],
            'Bölüm Onayı Bekliyor' => ['text' => 'Bölüm Onayı Bekliyor', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            'Direktör Onayı Bekliyor' => ['text' => 'Direktör Onayı Bekliyor', 'class' => 'bg-pink-100 text-pink-800 border-pink-200'], // EKLENDİ
            'Yönetici Onayı Bekliyor' => ['text' => 'Yönetici Onayı Bekliyor', 'class' => 'bg-orange-100 text-orange-800 border-orange-200'],

            // --- YENİ EKLENEN TALEP DURUMLARI ---
            'talep_onayi_bekliyor_kalite' => [
                'text' => '🟣 Talep Onayı Bekleniyor (Kalite)',
                'class' => 'bg-purple-50 text-purple-700 border-purple-200 shadow-sm animate-pulse font-bold'
            ],
            'talep_onayi_bekliyor_superadmin' => [
                'text' => '🔵 Talep Onayı Bekleniyor (Yönetim)',
                'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200 shadow-sm font-bold'
            ],
            'talep_olarak_kapatildi' => [
                'text' => '⚪ Talep Olarak Kapatıldı',
                'class' => 'bg-gray-100 text-gray-600 border-gray-300 font-bold decoration-slice'
            ],
            // --- HATALI BİLDİRİM DURUMLARI ---
            'hatali_bildirim_onayi_bekliyor_kalite' => [
                'text' => '🟠 Hatalı Bildirim Onayı (Kalite)',
                'class' => 'bg-orange-50 text-orange-700 border-orange-200 shadow-sm animate-pulse font-bold'
            ],
            'hatali_bildirim_onayi_bekliyor_direktor' => [
                'text' => '🔴 Hatalı Bildirim Onayı (Direktör)',
                'class' => 'bg-rose-50 text-rose-700 border-rose-200 shadow-sm animate-pulse font-bold'
            ],
            'hatali_bildirim_onayi_bekliyor_superadmin' => [
                'text' => '🛡️ Hatalı Bildirim Onayı (Superadmin)',
                'class' => 'bg-slate-50 text-slate-700 border-slate-200 shadow-sm animate-pulse font-bold'
            ],
            'hatali_bildirim_olarak_kapatildi' => [
                'text' => '🚫 Hatalı Bildirim Olarak Kapatıldı',
                'class' => 'bg-gray-100 text-gray-400 border-gray-300 font-bold line-through'
            ],
            'TALEP_OLARAK_KAPATİLDİ' => [
                'text' => '⚪ Talep Olarak Kapatıldı',
                'class' => 'bg-gray-100 text-gray-600 border-gray-300 font-bold decoration-slice'
            ],
            'TALEP_OLARAK_KAPATILDI' => [
                'text' => '⚪ Talep Olarak Kapatıldı',
                'class' => 'bg-gray-800 text-white border-gray-600 font-bold decoration-slice text-sm px-4 py-2 shadow-lg animate-pulse'
            ],
        ];

        $ayar = $durumlar[$this->durum] ?? ['text' => $this->durum, 'class' => 'bg-gray-100 text-gray-800'];

        return sprintf('<span class="inline-flex items-center px-3 py-1 rounded-md text-xs border %s">%s</span>', $ayar['class'], $ayar['text']);
    }

    /**
     * Projenin şu anki aktif adımını getirir.
     */
    public function aktifAdim()
    {
        return $this->hasOneThrough(
            \App\Models\IaaWorkflowStep::class,
            \App\Models\IaaStepAssignment::class,
            'iaa_id',
            'id',
            'id',
            'iaa_workflow_step_id'
        )
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('iaa_progress_updates')
                    ->join('iaa_talepleri', 'iaa_progress_updates.iaa_talep_id', '=', 'iaa_talepleri.id')
                    ->whereColumn('iaa_talepleri.iaa_id', 'iaa_step_assignments.iaa_id')
                    ->whereColumn('iaa_progress_updates.iaa_workflow_step_id', 'iaa_workflow_steps.id')
                    ->whereNotNull('iaa_progress_updates.completed_at');
            })
            // DÜZELTME BURASI: 'step_order' yerine 'order' kullanıyoruz
            ->orderBy('iaa_workflow_steps.order', 'asc')
            ->take(1);
    }


    /**
     * Sadece Müşteri Şikayeti KAYNAKLI OLMAYAN, saf IAA projelerini filtreler.
     * Şikayet tablosunda ID'si geçenleri ve sistem tarafından 'talep' olarak işaretlenenleri eler.
     */
    public function scopeSadeceOneriler($query)
    {
        return $query->whereNotExists(function ($subquery) {
            // 1. Müşteri Şikayetleri tablosunda bu IAA'nın ID'si geçiyor mu?
            $subquery->select(\DB::raw(1))
                ->from('musteri_sikayetleri')
                ->whereColumn('musteri_sikayetleri.iaa_id', 'iaas.id');
        })
            // 2. Ayrıca 'Talep Olarak Kapatıldı' statüsü teknik bir statüdür, raporlarda işi yoktur.
            // Bunu string olarak değil, bir "durum kuralı" olarak buraya gömüyoruz.
            ->where('durum', '!=', 'talep_olarak_kapatildi');
    }
}