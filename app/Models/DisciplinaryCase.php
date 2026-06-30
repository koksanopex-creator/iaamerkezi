<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryCase extends Model
{
    use HasFactory, SoftDeletes;

    // $guarded = []; yerine $fillable kullanıyoruz. Bu daha güvenli ve nettir.
    protected $fillable = [
        'user_id',
        'reporter_id',
        'behavior_id',
        'impact_id',
        'scope_id',
        'olay_tarihi',
        'olay_aciklamasi',
        'kanit_dosyalari',
        'tekrar_sayisi',
        'hesaplanan_puan',
        'sistem_oneri_ceza',
        'final_karar',
        'karar_tarihi',
        'durum',
        // --- SONRADAN EKLENEN SÜTUNLAR ---
        'savunma_aciklamasi',
        'savunma_dosyalari',
        'savunma_tarihi',
        'yonetici_notu',
        'karar_dosyasi',
        'toplanti_tarihi',
        'oylama_aktif',
        'oylama_notu',
        'rediscussion_count',
        'rediscussion_reason',
        'oylama_baslatan_id',
        'oylama_baslatildi_at',
        'oylama_bitti_at',
        'is_appealed',
        'manual_penalty_name',
        'manual_penalty_by',
        'settings_snapshot',
    ];

    protected $casts = [
        'olay_tarihi' => 'datetime',
        'savunma_tarihi' => 'datetime',
        'karar_tarihi' => 'datetime',
        'kanit_dosyalari' => 'array',
        'savunma_dosyalari' => 'array',
        'karar_dosyasi' => 'array',
        'toplanti_tarihi' => 'datetime',
        'oylama_aktif' => 'boolean',
        'rediscussion_count' => 'integer',
        'oylama_baslatildi_at' => 'datetime',
        'oylama_bitti_at' => 'datetime',
        'is_appealed' => 'boolean',
        'settings_snapshot' => 'array',
    ];

        protected static function booted()
    {
        static::saving(function ($case) {
            $snapshot = $case->settings_snapshot ?? [];
            
            // Eğer behavior, impact veya scope güncelleniyorsa, snapshot'ı tazeleyelim.
            // Yeni dosya oluşturulurken de bu çalışacak.
            if ($case->isDirty('behavior_id') || $case->isDirty('impact_id') || $case->isDirty('scope_id') || empty($snapshot)) {
                $case->loadMissing(['behavior.category', 'impact', 'scope']);
                
                $snapshot['behavior_tanim'] = $case->behavior->tanim ?? null;
                $snapshot['category_ad'] = optional($case->behavior)->category->ad ?? null;
                $snapshot['impact_tanim'] = $case->impact->tanim ?? null;
                $snapshot['scope_tanim'] = $case->scope->tanim ?? null;
                
                $case->settings_snapshot = $snapshot;
            }
        });
    }

    // --- AKILLI ÇAĞIRICILAR (ACCESSORS) ---
    public function getBehaviorTextAttribute()
    {
        return $this->settings_snapshot['behavior_tanim'] ?? optional($this->behavior)->tanim ?? 'Silinmiş Kayıt';
    }

    public function getCategoryTextAttribute()
    {
        return $this->settings_snapshot['category_ad'] ?? optional(optional($this->behavior)->category)->ad ?? 'Silinmiş Kayıt';
    }

    public function getImpactTextAttribute()
    {
        return $this->settings_snapshot['impact_tanim'] ?? optional($this->impact)->tanim ?? '-';
    }

    public function getScopeTextAttribute()
    {
        return $this->settings_snapshot['scope_tanim'] ?? optional($this->scope)->tanim ?? '-';
    }

    // --- İLİŞKİLER ---

    // Suçu İşleyen Personel
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    // Bildiren Amir
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id')->withTrashed();
    }

    // İhlal Edilen Madde (Suç)
    public function behavior()
    {
        return $this->belongsTo(DisciplinaryBehavior::class, 'behavior_id')->withTrashed();
    }

    public function logs()
    {
        return $this->hasMany(DisciplinaryLog::class);
    }

    // Etki / Şiddet
    public function impact()
    {
        return $this->belongsTo(DisciplinaryImpact::class, 'impact_id')->withTrashed();
    }

    // Kapsam
    public function scope()
    {
        return $this->belongsTo(DisciplinaryScope::class, 'scope_id')->withTrashed();
    }

    // Kategoriye Erişim (Behavior üzerinden)
    public function category()
    {
        return $this->hasOneThrough(
            DisciplinaryCategory::class,
            DisciplinaryBehavior::class,
            'id', // Behavior tablosundaki primary key
            'id', // Category tablosundaki primary key
            'behavior_id', // Case tablosundaki foreign key
            'category_id' // Behavior tablosundaki foreign key
        ); 
    }

    // Kurul Oyları
    public function oylar()
    {
        return $this->hasMany(DisciplinaryVote::class, 'case_id');
    }

    public function comments()
    {
        return $this->hasMany(DisciplinaryComment::class, 'case_id')->orderBy('created_at', 'desc');
    }

    public function toplantilar()
    {
        return $this->belongsToMany(DisiplinKuruluToplanti::class, 'disiplin_case_toplanti', 'case_id', 'toplanti_id');
    }

    public function appeals()
    {
        return $this->hasMany(DisciplinaryAppeal::class, 'disciplinary_case_id');
    }

    public function adjuster()
    {
        return $this->belongsTo(User::class, 'manual_penalty_by')->withTrashed();
    }

    // --- İTİRAZ MANTIĞI ---

    public function getAppealDeadlineAttribute()
    {
        if (!$this->oylama_bitti_at) return null;

        $date = $this->oylama_bitti_at->copy()->addDay()->startOfDay();
        $daysCounted = 0;

        while ($daysCounted < 3) {
            // Pazar günlerini atlıyoruz
            if ($date->dayOfWeek !== \Carbon\Carbon::SUNDAY) {
                $daysCounted++;
            }
            if ($daysCounted < 3) {
                $date->addDay();
            }
        }

        return $date->endOfDay();
    }

    public function getIsAppealWindowOpenAttribute()
    {
        if ($this->durum !== 'Karar Verildi' || $this->is_appealed) return false;
        if ($this->final_karar === 'Savunma Kabul Edildi (Ceza Yok)') return false;
        
        $deadline = $this->appeal_deadline;
        if (!$deadline) return false;

        return now()->lessThanOrEqualTo($deadline);
    }
    /**
     * Matris Puanını ve Önerilen Cezayı Hesapla
     */
    public function calculateMatrixScore()
    {
        $impact = $this->impact ? $this->impact->puan : 0;
        $scope = $this->scope ? $this->scope->puan : 0;

        // Tekrar Sayısı (Aynı suçtan kesinleşmiş cezalar)
        $gecmisSayisi = self::where('user_id', $this->user_id)
            ->where('behavior_id', $this->behavior_id)
            ->where('durum', 'Karar Verildi')
            ->where('id', '!=', $this->id) // Mevcut dosyayı sayma
            ->count();

        $tekrar = $gecmisSayisi + 1;

        // Katsayıyı Bul
        $katsayiKaydi = DisciplinaryMultiplier::where('tekrar_sayisi', $tekrar)->first();
        if (!$katsayiKaydi) {
            $katsayiKaydi = DisciplinaryMultiplier::orderBy('tekrar_sayisi', 'desc')->first();
        }
        $katsayi = $katsayiKaydi ? $katsayiKaydi->katsayi : 1.0;

        // Formül: (Etki x Kapsam) * Katsayı
        $baseScore = $impact * $scope;
        $totalScore = $baseScore * $katsayi;

        // Ceza Skalasından Öneriyi Bul
        $skala = DisciplinaryPenaltyScale::where('min_puan', '<=', $totalScore)
            ->where('max_puan', '>=', $totalScore)
            ->first();

        $oneri = $skala ? $skala->ceza_adi : 'Kurul Değerlendirmesi';

        return [
            'tekrar' => $tekrar,
            'katsayi' => $katsayi,
            'toplam_puan' => $totalScore,
            'oneri_ceza' => $oneri
        ];
    }
}