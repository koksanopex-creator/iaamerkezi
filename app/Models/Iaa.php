<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Takim; // Takim modelini kullanacağımızı belirtiyoruz
use Carbon\Carbon;
use App\Models\IaaLog;
use App\Models\IaaTalep;
use App\Models\IaaWorkflow;
use App\Models\IaaWorkflowStep;
use App\Models\IaaProgressUpdate;
use App\Models\IaaStepAssignment;
use App\Models\MusteriSikayeti;
use App\Models\Setting;
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
    'onaya_gonderilme_tarihi', // KULLANICININ YENİ EKLEDİĞİ TARİH
    'atama_zamani',
    'tamamlanma_suresi_gun',
    'hatali_bildirim_gerekcesi',
    'hatali_bildirim_tarihi',
    'hatali_bildirim_kalite_user_id',
    'hatali_bildirim_kalite_notu',
    'hatali_bildirim_kalite_at',
    'hatali_bildirim_direktor_user_id',
    'hatali_bildirim_direktor_notu',
    'hatali_bildirim_direktor_at',
    'hatali_bildirim_superadmin_user_id',
    'hatali_bildirim_superadmin_notu',
    'hatali_bildirim_superadmin_at',
    // YENİ EKLENENLER
    'direktor_notu',
    'direktor_onay_tarihi',
    'visit_planned',
    'tamamlayan_lider_id',
    'atamadaki_lider_id',
    'yil_baz',
    ];

    // YENİ EKLENİYOR: Veritabanından gelen bu alanları Carbon nesnesine dönüştür
    protected $casts = [
    'onaya_gonderilme_tarihi' => 'datetime', // <-- YENİ EKLENEN TARİH
    'onaylanma_tarihi' => 'datetime',
    'tamamlanma_tarihi' => 'datetime',
    'durum_degistirme_tarihi' => 'datetime',
    'direktor_onay_tarihi' => 'datetime', // <-- YENİ
    'yil_baz' => 'integer',
    ];

    /**
     * İAA'yı gönderen kullanıcı.
     */
    public function gonderen()
    {
        return $this->belongsTo(User::class, 'gonderen_user_id')->withTrashed();
    }

    /**
     * İAA'yı onaylayan veya reddeden yönetici.
     */
    public function onaylayan()
    {
        return $this->belongsTo(User::class, 'onaylayan_user_id')->withTrashed();
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
     * Projeyi tamamlayan lider (Dondurulmuş veri).
     */
    public function tamamlayanLider()
    {
        return $this->belongsTo(User::class, 'tamamlayan_lider_id')->withTrashed();
    }

    /**
     * Projenin atandığı andaki lider (Dondurulmuş veri).
     */
    public function atamadakiLider()
    {
        return $this->belongsTo(User::class, 'atamadaki_lider_id')->withTrashed();
    }

    /**
     * === YENİ İLİŞKİ ===
     * Bu IAA projesinin, hangi müşteri şikayetinden dönüştüğünü getirir.
     * (Bir Proje, bir şikayete aittir - hasOne ters ilişki)
     */
    public function musteriSikayeti()
    {
        // musteri_sikayetleri tablosundaki 'iaa_id' sütunu üzerinden
        return $this->hasOne(MusteriSikayeti::class, 'iaa_id')->withTrashed();
    }

    /**
     * Bu IAA projesi için planlanan EN SON veya TEK Ziyaret (Geriye Dönük Uyumluluk İçin)
     */
    public function ziyaretPlani()
    {
        return $this->hasOne(IaaZiyaretPlani::class, 'iaa_id')->latestOfMany();
    }

    /**
     * Bu IAA projesi için planlanan TÜM Ziyaretler
     */
    public function ziyaretPlanlari()
    {
        return $this->hasMany(IaaZiyaretPlani::class, 'iaa_id');
    }

    /**
     * Bu İAA'ya talepte bulunan tüm takımları döndürür.
     * (belongsToMany ilişkisi: Bir İAA'ya birden çok takım talep edebilir)
     */
    public function talepEdenTakimlar()
    {
        return $this->belongsToMany(Takim::class, 'iaa_talepleri', 'iaa_id', 'takim_id')
            ->withPivot('id', 'iaa_workflow_id', 'workflow_snapshot', 'start_date', 'due_date', 'status')
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
            IaaWorkflow::class,      // Ulaşmak istediğimiz en son model
            IaaTalep::class,         // Ara tabloyu temsil eden model
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

    /**
     * Projenin gerçek tamamlanma/kapanış tarihini döndürür.
     * (Loglardan veya veritabanı alanlarından akıllıca hesaplar)
     */
    public function getRealCompletionDateAttribute()
    {
        // 1. Durum kapalı değilse tarih yoktur
        $terminalStates = ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi'];
        if (!in_array($this->durum, $terminalStates))
        {
            return null;
        }

        // YENİ: Direktör Onayı ayarını kontrol et
        $direktorOnayiAktif = Setting::where('key', 'sikayet_direktor_onayi_aktif')->value('value') == '1';

        // 2. Loglardan en doğru tarihi bulmaya çalış
        // Eğer direktör onayı kapalıysa ve Bölüm Onayı logu varsa, onu tercih et (Kullanıcı talebi)
        if (!$direktorOnayiAktif) {
            $bolumLog = IaaLog::where('iaa_id', $this->id)
                ->where('eylem', 'Bölüm Onayı Verildi')
                ->latest()
                ->first();
            
            if ($bolumLog) {
                return $bolumLog->created_at;
            }
        }

        $onayLog = IaaLog::where('iaa_id', $this->id)
            ->whereIn('eylem', ['Direktör Onayı Verildi', 'Bölüm Onayı Verildi', 'Proje Onaylandı', 'Hatalı Bildirim Onaylandı'])
            ->latest()
            ->first();

        if ($onayLog)
        {
            return $onayLog->created_at;
        }

        // 3. Fallback: Model üzerindeki tarih alanları
        return $this->tamamlanma_tarihi
            ?? $this->onaylanma_tarihi
            ?? $this->hatali_bildirim_superadmin_at
            ?? $this->updated_at;
    }

    // Projenin kaç günde tamamlandığını/kapatıldığını hesaplayan akıllı özellik (accessor)
    public function getCompletionDurationInDaysAttribute()
    {
        // Başlangıç tarihi olarak takıma atanma tarihini alıyoruz
        $baslangicTarihi = $this->iaaTalebi->start_date ?? $this->created_at;
        $bitisTarihi = $this->real_completion_date;

        if ($baslangicTarihi && $bitisTarihi)
        {
            try
            {
                $baslangic = Carbon::parse($baslangicTarihi);
                $bitis = Carbon::parse($bitisTarihi);

                $gunFarki = ceil($baslangic->diffInDays($bitis));
                return $gunFarki <= 1 ? 'Aynı gün' : $gunFarki . ' günde';

            }
            catch (\Exception $e)
            {
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
            ->withTrashed()
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
        return $this->belongsTo(User::class, 'hatali_bildirim_kalite_user_id')->withTrashed();
    }

    public function hataliBildirimDirektorUser()
    {
        return $this->belongsTo(User::class, 'hatali_bildirim_direktor_user_id')->withTrashed();
    }

    public function hataliBildirimSuperadminUser()
    {
        return $this->belongsTo(User::class, 'hatali_bildirim_superadmin_user_id')->withTrashed();
    }

    public function hataliBildirimLiderUser()
    {
        return $this->belongsTo(User::class, 'talep_isteyen_user_id')->withTrashed();
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
        return match ($this->durum)
        {
            'Yeni', 'Devam Ediyor' => 'blue',
            'Atandı' => 'indigo',
            'Tamamlandı' => 'emerald',
            'Reddedildi', 'Tamamlanması Reddedildi' => 'red',
            // REVİZE SÜRECİ -> SARI (Amber)
            'Revize Ediliyor' => 'amber',
            // ONAY SÜREÇLERİ -> MOR (Purple)
            'Onay Bekliyor',
            'Bölüm Onayı Bekliyor',
            'talep_onayi_bekliyor_kalite',
            'talep_onayi_bekliyor_direktor',
            'talep_onayi_bekliyor_superadmin',
            'talep_olarak_kapatildi',
            'TALEP_OLARAK_KAPATİLDİ',
            'TALEP_OLARAK_KAPATILDI',
            'Direktör Onayı Bekliyor' => 'purple',
            // HATALI BİLDİRİM SÜREÇLERİ -> TURUNCU (Orange)
            'hatali_bildirim_onayi_bekliyor_kalite',
            'hatali_bildirim_onayi_bekliyor_direktor',
            'hatali_bildirim_onayi_bekliyor_superadmin',
            'hatali_bildirim_olarak_kapatildi',
            'Yönetici Onayı Bekliyor' => 'orange',
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
            'Bölüm Onayı Bekliyor' => ['text' => 'Bölüm Kalite Yöneticisi Onayı Bekliyor', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            'Direktör Onayı Bekliyor' => ['text' => 'Direktör Onayı Bekliyor', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            'Yönetici Onayı Bekliyor' => ['text' => 'Final Onay Bekliyor', 'class' => 'bg-orange-100 text-orange-800 border-orange-200'],
            // --- REVİZE VE ONAY DURUMLARI ---
            'Revize Ediliyor' => ['text' => 'Revize Ediliyor', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            'Onay Bekliyor' => ['text' => 'Bölüm Kalite Yöneticisi Onayı Bekliyor', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],

            // --- YENİ EKLENEN TALEP DURUMLARI ---
            'talep_onayi_bekliyor_kalite' => [
                'text' => '🟣 Müşterinin Talebidir, Kalite Onayı Bekliyor',
                'class' => 'bg-purple-50 text-purple-700 border-purple-200 shadow-sm animate-pulse font-bold'
            ],
            'talep_onayi_bekliyor_direktor' => [
                'text' => '🟣 Müşterinin Talebidir, Direktör Onayı Bekliyor',
                'class' => 'bg-purple-50 text-purple-700 border-purple-200 shadow-sm animate-pulse font-bold'
            ],
            'talep_onayi_bekliyor_superadmin' => [
                'text' => '🟣 Müşterinin Talebidir, Yönetim Onayı Bekliyor',
                'class' => 'bg-purple-50 text-purple-700 border-purple-200 shadow-sm animate-pulse font-bold'
            ],
            'hatali_bildirim_onayi_bekliyor_kalite' => [
                'text' => '🟠 Hatalı Bildirimdir, Kalite Onayı Bekliyor',
                'class' => 'bg-orange-50 text-orange-700 border-orange-200 shadow-sm animate-pulse font-bold'
            ],
            'hatali_bildirim_onayi_bekliyor_direktor' => [
                'text' => '🟠 Hatalı Bildirimdir, Direktör Onayı Bekliyor',
                'class' => 'bg-orange-50 text-orange-700 border-orange-200 shadow-sm animate-pulse font-bold'
            ],
            'hatali_bildirim_onayi_bekliyor_superadmin' => [
                'text' => '🟠 Hatalı Bildirimdir, Yönetim Onayı Bekliyor',
                'class' => 'bg-orange-50 text-orange-700 border-orange-200 shadow-sm animate-pulse font-bold'
            ],
            'hatali_bildirim_olarak_kapatildi' => [
                'text' => '🟠 Hatalı Bildirim Olarak Kapatıldı',
                'class' => 'bg-orange-100 text-orange-600 border-orange-200 font-bold line-through'
            ],
            'talep_olarak_kapatildi' => [
                'text' => '🟣 Müşterinin Talebidir Olarak Kapatıldı',
                'class' => 'bg-purple-100 text-purple-700 border-purple-200 font-bold'
            ],
            'TALEP_OLARAK_KAPATİLDİ' => [
                'text' => '🟣 Müşterinin Talebidir Olarak Kapatıldı',
                'class' => 'bg-purple-100 text-purple-700 border-purple-200 font-bold'
            ],
            'TALEP_OLARAK_KAPATILDI' => [
                'text' => '🟣 Müşterinin Talebidir Olarak Kapatıldı',
                'class' => 'bg-purple-100 text-purple-700 border-purple-200 font-bold'
            ],
        ];

        // --- SAF İAA vs. ŞİKAYET AYRIMI ---
        if ($this->durum === 'Onay Bekliyor') {
            $safIaa = !empty($this->oneri);
            $durumlar['Onay Bekliyor'] = $safIaa
                ? ['text' => '🟡 Öneri Onayı Bekliyor', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200']
                : ['text' => 'Bölüm Kalite Yöneticisi Onayı Bekliyor', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'];
        }

        $ayar = $durumlar[$this->durum] ?? ['text' => $this->durum, 'class' => 'bg-gray-100 text-gray-800'];
        $text = $ayar['text'];

        // Bekleme süresini ekle (Onay bekleyen veya aktif süreçler için)
        $isAnimated = in_array($this->durum, ['Yeni', 'Atandı', 'Devam Ediyor', 'Revize Ediliyor']) || str_contains(strtolower($this->durum), 'bekliyor');
        
        if ($isAnimated) {
            $tarih = $this->onaya_gonderilme_tarihi ?? $this->durum_degistirme_tarihi ?? $this->created_at;
            if ($tarih) {
                $gun = ceil(\Carbon\Carbon::parse($tarih)->diffInMinutes(now()) / (24 * 60));
                if ($gun < 1) $gun = 1;
                $text .= "<span class='block text-[9px] opacity-80 font-bold mt-0.5 lowercase tracking-wider'>({$gun} gündür)</span>";
            }
        }

        // Tamamlanma süresini ekle
        $isCompleted = in_array($this->durum, ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']);
        if ($isCompleted) {
            $baslangic = $this->created_at;
            $bitis = $this->tamamlanma_tarihi ?? $this->updated_at;
            $gun = ceil(\Carbon\Carbon::parse($baslangic)->diffInMinutes($bitis) / (24 * 60));
            if ($gun < 1) $gun = 1;
            $text .= "<span class='block text-[9px] opacity-80 font-bold mt-0.5 lowercase tracking-wider'>({$gun} günde)</span>";
        }

        return sprintf('<span class="inline-flex flex-col items-center justify-center px-2 py-1.5 rounded-xl text-[10px] font-black border uppercase tracking-tight text-center leading-tight w-32 whitespace-normal break-words %s">%s</span>', $ayar['class'], $text);
    }

    /**
     * Projenin şu anki aktif adımını getirir.
     * SNAPSHOT DESTEĞİ: Eğer dondurulmuş (snapshot) adımlar varsa oradan hesaplar.
     */
    public function getAktifAdimAttribute()
    {
        // 1. Snapshot Kontrolü
        $assignment = $this->iaaTalebi;
        if ($assignment && !empty($assignment->workflow_snapshot))
        {
            $steps = collect($assignment->workflow_snapshot);

            // Tamamlanmış adım ID'lerini al
            $completedStepIds = IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
                ->whereNotNull('completed_at')
                ->pluck('iaa_workflow_step_id')
                ->toArray();

            // Tamamlanmamış ilk adımı bul
            $nextStepData = $steps->reject(function ($step) use ($completedStepIds)
            {
                // HATA ÖNLEYİCİ: Eğer $step bir dizi değilse veya 'id' anahtarı yoksa bu adımı geç
                if (!is_array($step) || !isset($step['id']))
                {
                    return true;
                }
                return in_array($step['id'], $completedStepIds);
            })->sortBy('order')->first();

            if ($nextStepData)
            {
                $step = new IaaWorkflowStep();
                $step->forceFill($nextStepData);
                return $step;
            }
            return null;
        }

        // 2. Klasik Dinamik İlişki (Snapshot yoksa)
        return $this->aktifAdimRelationship()->first();
    }

    /**
     * Eski aktifAdim() metodunun ilişki versiyonu (Internal use only)
     */
    public function aktifAdimRelationship()
    {
        return $this->hasOneThrough(
            IaaWorkflowStep::class,
            IaaStepAssignment::class,
            'iaa_id',
            'id',
            'id',
            'iaa_workflow_step_id'
        )
            ->whereNotExists(function ($query)
            {
                $query->select(DB::raw(1))
                    ->from('iaa_progress_updates')
                    ->join('iaa_talepleri', 'iaa_progress_updates.iaa_talep_id', '=', 'iaa_talepleri.id')
                    ->whereColumn('iaa_talepleri.iaa_id', 'iaa_step_assignments.iaa_id')
                    ->whereColumn('iaa_progress_updates.iaa_workflow_step_id', 'iaa_workflow_steps.id')
                    ->whereNotNull('iaa_progress_updates.completed_at');
            })
            ->orderBy('iaa_workflow_steps.order', 'asc');
    }

    // Geriye dönük uyumluluk için aktifAdim() metodunu her zaman ilişki döndürecek şekilde güncelliyoruz
    public function aktifAdim()
    {
        return $this->aktifAdimRelationship();
    }


    /**
     * Sadece Müşteri Şikayeti KAYNAKLI OLMAYAN, saf IAA projelerini filtreler.
     * Şikayet tablosunda ID'si geçenleri VE "Müşteri şikayetinden..." diye oluşturulmuş olanları eler.
     * Bu sayede şikayeti silinmiş ama kendisi havada kalmış çöp Iaa'ların da sızması engellenir.
     */
    public function scopeSadeceOneriler($query)
    {
        return $query->whereNotExists(function ($subquery)
        {
            // Mevcut aktif müşteri şikayeti var mı kontrolü
            $subquery->select(DB::raw(1))
                ->from('musteri_sikayetleri')
                ->whereColumn('musteri_sikayetleri.iaa_id', 'iaas.id');
        })
            // Çöp olarak kalan (şikayeti silinmiş ama veritabanında duran) şikayet kökenli Iaa'ları ele
            ->where('oneri', 'not like', 'Müşteri şikayetinden%')
            // 'Talep Olarak Kapatıldı' statüsü raporlarda/listelerde görünmemeli
            ->where('durum', '!=', 'talep_olarak_kapatildi');
    }

    /**
     * Projenin ilerleme verisini (Adım sayıları, yüzde ve kapanış durumu) döndürür.
     */
    public function getIlerlemeVerisiAttribute()
    {
        $assignment = $this->iaaTalebi;

        if (!$assignment)
        {
            return [
                'tamamlanan' => 0,
                'toplam' => 0,
                'yuzde' => 0,
                'kapanis_bekleniyor' => false
            ];
        }

        if (!empty($assignment->workflow_snapshot)) {
            $totalSteps = is_array($assignment->workflow_snapshot) ? count($assignment->workflow_snapshot) : 0;
        } else {
            // Snapshot boş ise mevcut workflow adımlarını kullan (eski projeler için geriye dönük uyumluluk)
            $workflow = $assignment->workflow;
            $totalSteps = $workflow ? $workflow->steps()->count() : 0;
        }

        // Tamamlanmış adım sayısını bul
        $completedSteps = IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
            ->whereNotNull('completed_at')
            ->count();

        $percentage = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;

        // Kaç gündür devam ediyor?
        $baslangicTarihi = $assignment->start_date ?? $this->created_at;
        $gecenGun = ceil(Carbon::parse($baslangicTarihi)->diffInDays(now()));
        if ($gecenGun <= 0)
            $gecenGun = 1;

        // Kapanış Bekleniyor mu? 
        // Kural: Tüm adımlar bittiyse ama durum hala terminal (Tamamlandı, Kapatıldı vs.) bir durumda değilse.
        $terminalStates = ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi'];
        // Onay bekleyen durumlar (Bu durumlarda 'Kapanış Bekleniyor' uyarısı verilmemeli)
        $approvalStates = ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'];

        $kapanisBekleniyor = ($completedSteps === $totalSteps && $totalSteps > 0 && !in_array($this->durum, $terminalStates) && !in_array($this->durum, $approvalStates));


        return [
            'tamamlanan' => $completedSteps,
            'toplam' => $totalSteps,
            'yuzde' => $percentage,
            'gecen_gun' => $gecenGun,
            'baslangic_tarihi' => $baslangicTarihi,
            'kapanis_bekleniyor' => $kapanisBekleniyor
        ];
    }

    /**
     * Projenin şu anki aktif aşamasını veya beklediği onay durumunu metin olarak döndürür.
     */
    public function getAktifAsamaMetniAttribute()
    {
        // 1. ÖNCE ONAY DURUMLARINA BAK (Eğer bir onay bekliyorsa, aktif adım yerine bu gösterilmeli)
        $approvalStates = [
            'Bölüm Onayı Bekliyor',
            'Direktör Onayı Bekliyor',
            'Yönetici Onayı Bekliyor',
            'talep_onayi_bekliyor_kalite',
            'talep_onayi_bekliyor_direktor',
            'talep_onayi_bekliyor_superadmin',
            'hatali_bildirim_onayi_bekliyor_kalite',
            'hatali_bildirim_onayi_bekliyor_direktor',
            'hatali_bildirim_onayi_bekliyor_superadmin'
        ];

        if (in_array($this->durum, $approvalStates)) {
            return match ($this->durum) {
                'Bölüm Onayı Bekliyor' => 'Bölüm Kalite Yöneticisi Onayı Bekleniyor',
                'Direktör Onayı Bekliyor' => 'Direktör Onayı Bekleniyor',
                'Yönetici Onayı Bekliyor' => 'Yönetici Onayı (Final) Bekleniyor',
                'talep_onayi_bekliyor_kalite' => 'Müşteri Talebi: Kalite Onayı Bekleniyor',
                'talep_onayi_bekliyor_direktor' => 'Müşteri Talebi: Direktör Onayı Bekleniyor',
                'talep_onayi_bekliyor_superadmin' => 'Müşteri Talebi: Yönetim Onayı Bekleniyor',
                'hatali_bildirim_onayi_bekliyor_kalite' => 'Hatalı Bildirim: Kalite Onayı Bekleniyor',
                'hatali_bildirim_onayi_bekliyor_direktor' => 'Hatalı Bildirim: Direktör Onayı Bekleniyor',
                'hatali_bildirim_onayi_bekliyor_superadmin' => 'Hatalı Bildirim: Yönetim Onayı Bekleniyor',
                default => 'Onay Süreci Bekleniyor'
            };
        }

        // 2. ONAY SÜRECİNDE DEĞİLSE AKTİF İŞ ADIMINA BAK
        $adim = $this->aktif_adim;
        if ($adim)
        {
            return $adim->name;
        }

        $veriler = $this->ilerleme_verisi;
        if ($veriler['kapanis_bekleniyor'])
        {
            return 'Kapanış İşlemleri Bekleniyor';
        }

        $terminalStates = ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi'];
        if (in_array($this->durum, $terminalStates))
        {
            return 'Tamamlandı / Kapalı';
        }

        return 'İşlem Sırası Bekleniyor';
    }
    /**
     * Projenin mevcut durumunda ne kadar süredir beklediğini metin olarak döner.
     */
    public function getBeklemeSuresiMetniAttribute()
    {
        $durum = $this->durum;
        $tarih = null;
        $prefix = "";

        // Terminal (Bitiş) durumlarında bekleme süresi göstermiyoruz
        $terminalStates = ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi'];
        if (in_array($durum, $terminalStates))
        {
            return null;
        }

        switch ($durum)
        {
            case 'Yeni':
                $tarih = $this->created_at;
                $prefix = "Yeni Şikayet: ";
                break;
            case 'Atandı':
            case 'Devam Ediyor':
                $ilerleme = $this->ilerleme_verisi;
                if ($ilerleme['kapanis_bekleniyor'])
                {
                    $tarih = $this->durum_degistirme_tarihi ?? $this->updated_at;
                    $prefix = "Kapanış Hazır: ";
                }
                else
                {
                    $tarih = $this->atama_zamani ?? $this->created_at;
                    $prefix = "İşlemde: ";
                }
                break;
            case 'Bölüm Onayı Bekliyor':
                $tarih = $this->onaya_gonderilme_tarihi ?? $this->durum_degistirme_tarihi ?? $this->updated_at;
                $prefix = "Bölüm Kalite Yöneticisi Onayında: ";
                break;
            case 'Direktör Onayı Bekliyor':
                $tarih = $this->durum_degistirme_tarihi ?? $this->onaya_gonderilme_tarihi ?? $this->updated_at;
                $prefix = "Direktör Onayında: ";
                break;
            case 'Yönetici Onayı Bekliyor':
                $tarih = $this->durum_degistirme_tarihi ?? $this->updated_at;
                $prefix = "Final Onayında: ";
                break;
            case 'Revize Ediliyor':
                $tarih = $this->durum_degistirme_tarihi ?? $this->updated_at;
                $prefix = "Revizede: ";
                break;
            case 'Tamamlanması Reddedildi':
                $tarih = $this->durum_degistirme_tarihi ?? $this->updated_at;
                $prefix = "Reddedildi: ";
                break;
            default:
                $tarih = $this->durum_degistirme_tarihi ?? $this->updated_at;
                $prefix = "Bekliyor: ";
        }

        if ($tarih)
        {
            $tarihObj = Carbon::parse($tarih);
            $gun = ceil($tarihObj->diffInDays(now()));
            // Kullanıcı talebi: Her zaman en az 1 gün göster
            if ($gun <= 0)
                $gun = 1;

            return $prefix . $gun . " Gündür";
        }

        return null;
    }
}