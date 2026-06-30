<?php

namespace App\Services;

use App\Models\User;
use App\Models\Iaa;
use App\Models\MusteriSikayeti;
use App\Models\DisciplinaryCase;
use App\Models\Bolum;
use App\Models\Takim;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Services\Dashboard\KullaniciPuanService;
use Carbon\Carbon;

class PuanAnalizService
{
    /**
     * Raporlara dahil edilecek geçerli kullanıcı ID'lerini döner.
     * (Superadmin, Yonetim, Dış Avukat ve Müşteriler hariç tutulur)
     */
    public function getEligibleUserIds()
    {
        return User::where('is_personnel', true)
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['Superadmin', 'Yonetim', 'Dış Avukat']);
            })
            ->pluck('id')
            ->toArray();
    }

    /**
     * KPI istatistiklerini hesaplar (Sonuçlar tamsayıdır).
     */
    public function getKpiStats($bolumIds = '*', $startDate = null, $endDate = null)
    {
        $eligibleUserIds = $this->getEligibleUserIds();
        
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate) : Carbon::now();
        
        // Önceki dönem tarihleri (Aynı süre kadar geriye git)
        $diffInDays = $start->diffInDays($end);
        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($diffInDays);

        // Mevcut Dönem
        $currentGains = $this->calculateTotalGains($bolumIds, $start, $end, $eligibleUserIds);
        $currentLosses = $this->calculateTotalLosses($bolumIds, $start, $end, $eligibleUserIds);
        $currentNet = $currentGains - $currentLosses;
        $activeUserCount = User::whereIn('id', $eligibleUserIds)
            ->when($bolumIds !== '*', fn($q) => $q->whereIn('bolum_id', (array)$bolumIds))
            ->where('onaylandi_mi', true)->count();

        // Önceki Dönem (Kıyaslama için)
        $prevGains = $this->calculateTotalGains($bolumIds, $prevStart, $prevEnd, $eligibleUserIds);
        $prevLosses = $this->calculateTotalLosses($bolumIds, $prevStart, $prevEnd, $eligibleUserIds);
        $prevNet = $prevGains - $prevLosses;

        return [
            'gains' => [
                'value' => round($currentGains),
                'change' => $this->calculateChange($currentGains, $prevGains)
            ],
            'losses' => [
                'value' => round($currentLosses),
                'change' => $this->calculateChange($currentLosses, $prevLosses, true) // Ters mantık (Loss azalması iyidir)
            ],
            'net' => [
                'value' => round($currentNet),
                'change' => $this->calculateChange($currentNet, $prevNet)
            ],
            'active_users' => [
                'value' => $activeUserCount,
                'avg_net' => $activeUserCount > 0 ? round($currentNet / $activeUserCount, 1) : 0
            ]
        ];
    }

    /**
     * Puan trendini hesaplar.
     */
    public function getTrendStats($bolumIds = '*', $startDate = null, $endDate = null)
    {
        $eligibleUserIds = $this->getEligibleUserIds();
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->subYear();
        $end = $endDate ? Carbon::parse($endDate) : Carbon::now();

        $stats = ['labels' => [], 'datasets' => [
            ['label' => 'Kazanılan', 'data' => [], 'borderColor' => '#10b981', 'backgroundColor' => 'rgba(16, 185, 129, 0.1)', 'fill' => true],
            ['label' => 'Kesinti', 'data' => [], 'borderColor' => '#ef4444', 'backgroundColor' => 'rgba(239, 68, 68, 0.1)', 'fill' => true]
        ]];

        $current = clone $start;
        while ($current <= $end) {
            $stats['labels'][] = $current->format('M Y');
            $pStart = $current->copy()->startOfMonth();
            $pEnd = $current->copy()->endOfMonth();

            $stats['datasets'][0]['data'][] = round($this->calculateTotalGains($bolumIds, $pStart, $pEnd, $eligibleUserIds));
            $stats['datasets'][1]['data'][] = round($this->calculateTotalLosses($bolumIds, $pStart, $pEnd, $eligibleUserIds));
            $current->addMonth();
        }

        return $stats;
    }

    /**
     * Puan kaynaklarının dağılımını hesaplar (Disiplin dahil).
     */
    public function getCategoryStats($bolumIds = '*', $startDate = null, $endDate = null)
    {
        $eligibleUserIds = $this->getEligibleUserIds();
        $iaaOneriPuani = Setting::where('key', 'iaa_oneri_puani')->value('value') ?? 0;
        $sikayetGirisPuani = Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0;

        // İAA Projeleri
        $iaaQ = Iaa::where('durum', 'Tamamlandı')->where('puan', '>', 0)->whereNotNull('oneri');
        $this->applyBolumFilter($iaaQ, $bolumIds, 'iaas');
        $this->applyDateFilter($iaaQ, $startDate, $endDate, DB::raw('COALESCE(onaya_gonderilme_tarihi, onaylanma_tarihi, created_at)'));
        $iaaPoints = round($iaaQ->sum('puan'));

        // Şikayet Çözümleri
        $sikayetCozumQ = Iaa::where('durum', 'Tamamlandı')->where('puan', '>', 0)->where(fn($q) => $q->whereNull('oneri')->orWhere('oneri', ''));
        $this->applyBolumFilter($sikayetCozumQ, $bolumIds, 'iaas');
        $this->applyDateFilter($sikayetCozumQ, $startDate, $endDate, DB::raw('COALESCE(onaya_gonderilme_tarihi, onaylanma_tarihi, created_at)'));
        $sikayetCozumPoints = round($sikayetCozumQ->sum('puan'));

        // Öneri Girişleri
        $oneriGirisQ = Iaa::whereNotIn('durum', ['Taslak', 'Onay Bekliyor', 'Reddedildi'])->whereIn('gonderen_user_id', $eligibleUserIds);
        $this->applyBolumFilter($oneriGirisQ, $bolumIds, 'iaas', 'gonderen_user_id');
        $this->applyDateFilter($oneriGirisQ, $startDate, $endDate, 'created_at');
        $oneriGirisPoints = round($oneriGirisQ->count() * $iaaOneriPuani);

        // Şikayet Girişleri
        $sikayetGirisQ = MusteriSikayeti::where('musteri_durum', '!=', 'Talep')->whereIn('olusturan_kurul_uyesi_id', $eligibleUserIds);
        $this->applyBolumFilter($sikayetGirisQ, $bolumIds, 'musteri_sikayetleri', 'olusturan_kurul_uyesi_id');
        $this->applyDateFilter($sikayetGirisQ, $startDate, $endDate, 'created_at');
        $sikayetGirisPoints = round($sikayetGirisQ->count() * $sikayetGirisPuani);

        // Disiplin Kesintileri (Pozitif değer olarak gösterilecek)
        $losses = round($this->calculateTotalLosses($bolumIds, $startDate, $endDate, $eligibleUserIds));

        return [
            'labels' => ['İAA Projeleri', 'Şikayet Çözümleri', 'Öneri Girişleri', 'Şikayet Girişleri', 'Disiplin Kesintisi'],
            'data' => [$iaaPoints, $sikayetCozumPoints, $oneriGirisPoints, $sikayetGirisPoints, $losses],
            'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#6366f1', '#ef4444']
        ];
    }

    /**
     * Takım sıralamasını getirir.
     */
    public function getTeamStats($bolumIds = '*', $startDate = null, $endDate = null)
    {
        $eligibleUserIds = $this->getEligibleUserIds();
        $puanService = app(KullaniciPuanService::class);

        $teams = Takim::with(['users'])
            ->when($bolumIds !== '*', function($q) use ($bolumIds) {
                $q->whereHas('users', function($uq) use ($bolumIds) {
                    $uq->whereIn('bolum_id', (array)$bolumIds);
                });
            })
            ->get();

        foreach ($teams as $team) {
            $teamNet = 0;
            $prevNet = 0;
            // TODO: Daha performanslı bir hesaplama için puan servisinin toplu desteği gerekebilir.
            $teamUsers = $team->users->filter(fn($u) => in_array($u->id, $eligibleUserIds));
            
            foreach ($teamUsers as $user) {
                /** @var User $user */
                $teamNet += $puanService->calculateScoreInRange($user, $startDate, $endDate);
                
                // Değişim hesaplamak için (Önceki ay/dönem)
                $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
                $end = $endDate ? Carbon::parse($endDate) : Carbon::now();
                $diff = $start->diffInDays($end);
                $prevNet += $puanService->calculateScoreInRange($user, $start->copy()->subDays($diff), $start->copy()->subDay());
            }
            $team->net_puan = round($teamNet);
            $team->degisim = $this->calculateChange($teamNet, $prevNet);
        }

        return $teams->sortByDesc('net_puan')->values();
    }

    /**
     * Bölüm karşılaştırmasını getirir (Sadece puanı olanlar).
     */
    public function getDepartmentStats($bolumIds = '*', $startDate = null, $endDate = null)
    {
        $eligibleUserIds = $this->getEligibleUserIds();
        
        $bolumler = Bolum::when($bolumIds !== '*', fn($q) => $q->whereIn('id', (array)$bolumIds))->get();
        $result = [];

        foreach ($bolumler as $bolum) {
            $gains = $this->calculateTotalGains($bolum->id, $startDate, $endDate, $eligibleUserIds);
            $losses = $this->calculateTotalLosses($bolum->id, $startDate, $endDate, $eligibleUserIds);
            $net = round($gains - $losses);

            if ($net > 0) {
                $result[] = ['name' => $bolum->ad, 'puan' => $net];
            }
        }

        usort($result, fn($a, $b) => $b['puan'] <=> $a['puan']);
        return $result;
    }

    public function getTopPerformers($bolumIds = '*', $limit = 50, $startDate = null, $endDate = null)
    {
        $eligibleUserIds = $this->getEligibleUserIds();
        $query = User::whereIn('id', $eligibleUserIds)->where('onaylandi_mi', true);
        if ($bolumIds !== '*') $query->whereIn('bolum_id', (array)$bolumIds);

        $users = $query->with('bolum')->get();
        $puanService = app(KullaniciPuanService::class);

        foreach ($users as $user) {
            /** @var User $user */
            $user->period_puan = round($puanService->calculateScoreInRange($user, $startDate, $endDate));
        }

        return $users->sortByDesc('period_puan')->values();
    }

    private function calculateTotalGains($bolumIds, $start, $end, $eligibleIds)
    {
        $iaaOneriPuani = Setting::where('key', 'iaa_oneri_puani')->value('value') ?? 0;
        $sikayetGirisPuani = Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0;
        
        $total = 0;
        $q = Iaa::where('durum', 'Tamamlandı')->where('puan', '>', 0);
        $this->applyBolumFilter($q, $bolumIds, 'iaas');
        $this->applyDateFilter($q, $start, $end, DB::raw('COALESCE(onaya_gonderilme_tarihi, onaylanma_tarihi, created_at)'));
        $total += $q->sum('puan');

        $q2 = Iaa::whereNotIn('durum', ['Taslak', 'Onay Bekliyor', 'Reddedildi'])->whereIn('gonderen_user_id', $eligibleIds);
        $this->applyBolumFilter($q2, $bolumIds, 'iaas', 'gonderen_user_id');
        $this->applyDateFilter($q2, $start, $end, 'created_at');
        $total += $q2->count() * $iaaOneriPuani;

        $q3 = MusteriSikayeti::where('musteri_durum', '!=', 'Talep')->whereIn('olusturan_kurul_uyesi_id', $eligibleIds);
        $this->applyBolumFilter($q3, $bolumIds, 'musteri_sikayetleri', 'olusturan_kurul_uyesi_id');
        $this->applyDateFilter($q3, $start, $end, 'created_at');
        $total += $q3->count() * $sikayetGirisPuani;

        return $total;
    }

    private function calculateTotalLosses($bolumIds, $start, $end, $eligibleIds)
    {
        $q = DisciplinaryCase::where('durum', 'Karar Verildi')->whereIn('user_id', $eligibleIds)
            ->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)');
        if ($bolumIds !== '*') {
            $q->whereHas('user', fn($u) => $u->whereIn('bolum_id', (array)$bolumIds));
        }
        $this->applyDateFilter($q, $start, $end, 'updated_at');
        return $q->sum('hesaplanan_puan');
    }

    private function calculateChange($current, $prev, $inverse = false)
    {
        if ($prev == 0) return $current > 0 ? ($inverse ? -100 : 100) : 0;
        $pct = (($current - $prev) / abs($prev)) * 100;
        return round($pct, 1);
    }

    private function applyBolumFilter($query, $bolumIds, $tableName, $userIdColumn = null)
    {
        if ($bolumIds === '*') return;
        $ids = (array)$bolumIds;

        if ($tableName === 'iaas') {
            if ($userIdColumn === 'gonderen_user_id') {
                $query->whereHas('gonderen', fn($q) => $q->whereIn('bolum_id', $ids));
            } else {
                $query->whereHas('atananTakim', fn($q) => $q->whereIn('bolum_id', $ids));
            }
        } elseif ($tableName === 'musteri_sikayetleri') {
            if ($userIdColumn === 'olusturan_kurul_uyesi_id') {
                $query->whereHas('olusturanKurulUyesi', fn($q) => $q->whereIn('bolum_id', $ids));
            } else {
                $query->whereIn('sikayet_kategorisi_id', function($q) use ($ids) {
                    $q->select('id')->from('sikayet_kategorileri')->whereIn('bolum_id', $ids);
                });
            }
        }
    }

    private function applyDateFilter($query, $startDate, $endDate, $column = 'updated_at')
    {
        if ($startDate) $query->whereDate($column, '>=', $startDate);
        if ($endDate) $query->whereDate($column, '<=', $endDate);
    }
}
