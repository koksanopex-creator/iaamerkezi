<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SikayetIadesi;
use App\Models\SikayetKategori;
use App\Models\Bolum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SikayetIadesiExport;
use Barryvdh\DomPDF\Facade\Pdf;

class SikayetIadesiRaporu extends Component
{
    use WithPagination;

    // Filtreler
    public $startDate;
    public $endDate;
    public $selectedBolumId = '';
    public $selectedReason = '';
    public $search = '';
    public $selectedUnit = '';
    public $perPage = 5;
    public $showAll = false;

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'selectedBolumId' => ['except' => ''],
        'selectedReason' => ['except' => ''],
        'selectedUnit' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        // Varsayılan olarak Tüm Zamanlar (Boş)
        $this->startDate = null;
        $this->endDate = null;
    }

    public function setFilterPeriod($period)
    {
        switch ($period) {
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'quarter':
                // Kullanıcının isteği: Nisan'daysak Nisan-Mayıs-Haziran (3 aylık standart çeyrekler)
                $this->startDate = Carbon::now()->startOfQuarter()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'all':
                $this->startDate = null;
                $this->endDate = null;
                break;
        }
        $this->resetPage();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['startDate', 'endDate', 'selectedBolumId', 'selectedReason', 'selectedUnit', 'search'])) {
            $this->resetPage();
        }
    }

    public function setFilterReason($reason)
    {
        $this->selectedReason = ($this->selectedReason === $reason) ? '' : $reason;
        $this->resetPage();
    }

    public function setFilterUnit($unit)
    {
        $this->selectedUnit = ($this->selectedUnit === $unit) ? '' : $unit;
        $this->resetPage();
    }

    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
        $this->perPage = $this->showAll ? 1000 : 5;
    }

    public function exportExcel()
    {
        $iadeler = $this->getBaseQuery()->orderBy('iade_tarihi', 'desc')->get();
        return Excel::download(new SikayetIadesiExport($iadeler, $this->startDate, $this->endDate), 'sikayet-iadeleri-raporu.xlsx');
    }

    public function exportPdf()
    {
        $iadeler = $this->getBaseQuery()->orderBy('iade_tarihi', 'desc')->get();
        
        $tarihBilgisi = \Carbon\Carbon::parse($this->startDate)->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($this->endDate)->format('d.m.Y');
        
        $pdf = Pdf::loadView('pdf.sikayet-iadesi', [
            'iadeler' => $iadeler,
            'tarihBilgisi' => $tarihBilgisi
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'sikayet-iadeleri-raporu.pdf');
    }

    public function clearFilters()
    {
        $this->reset(['startDate', 'endDate', 'selectedBolumId', 'selectedReason', 'selectedUnit', 'search']);
        $this->mount();
        $this->resetPage();
    }

    /**
     * Yetki kısıtlamalı ana sorguyu döner
     */
    private function getBaseQuery()
    {
        $user = Auth::user();
        $query = SikayetIadesi::query()
            ->with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.customer', 'user']);

        // 1. Organik Bağ ve Rol Bazlı Filtreleme
        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
            $query->whereHas('musteriSikayeti', function($q) {
                $q->where('konum_tipi', 'Yurt İçi');
            });
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $query->whereHas('musteriSikayeti', function($q) {
                $q->where('konum_tipi', 'Yurt Dışı');
            });
        } elseif (!$user->hasAnyRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
            $allowedBolumIds = $user->getAllowedBolumIds();
            
            $query->whereHas('musteriSikayeti.sikayetKategori', function($q) use ($allowedBolumIds, $user) {
                if ($allowedBolumIds === '*') {
                    // Tüm bölümler (Superadmin/Yonetim/Kurul için geçerli, ama yukarıda eledik)
                } else {
                    $q->whereIn('bolum_id', $allowedBolumIds);
                }
            });
        }

        // 2. Kullanıcı Filtreleri
        if ($this->startDate) {
            $query->whereDate('iade_tarihi', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('iade_tarihi', '<=', $this->endDate);
        }
        if ($this->selectedBolumId) {
            $query->whereHas('musteriSikayeti.sikayetKategori', function($q) {
                $q->where('bolum_id', $this->selectedBolumId);
            });
        }
        if ($this->selectedReason) {
            $query->where('iade_sebebi', $this->selectedReason);
        }

        if ($this->selectedUnit) {
            $query->where('birim', $this->selectedUnit);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('iade_sebebi', 'like', '%' . $this->search . '%')
                  ->orWhere('aciklama', 'like', '%' . $this->search . '%')
                  ->orWhereHas('musteriSikayeti', function($sq) {
                      $sq->where('musteri_adi', 'like', '%' . $this->search . '%')
                        ->orWhere('musteri_sikayet_konusu', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query;
    }

    public function render()
    {
        // Bölgesel ayarları Türkçe'ye zorla
        \Carbon\Carbon::setLocale('tr');
        setlocale(LC_TIME, 'tr_TR.utf8', 'tr_TR', 'turkish');

        $iadeler = $this->getBaseQuery()->latest('iade_tarihi')->paginate(15);
        
        // KPI Hesaplamaları
        $kpiQuery = $this->getBaseQuery();
        $totalIadeCount = (clone $kpiQuery)->count();
        
        // Birim bazlı toplam miktarlar
        $unitTotals = (clone $kpiQuery)
            ->select('birim', DB::raw('SUM(miktar) as total_amount'))
            ->groupBy('birim')
            ->get();

        // En çok iade sebebi
        $topReason = (clone $kpiQuery)
            ->select('iade_sebebi', DB::raw('count(*) as count'))
            ->groupBy('iade_sebebi')
            ->orderByDesc('count')
            ->first();

        // Grafik Verileri (Sebep Dağılımı)
        $reasonDistribution = (clone $kpiQuery)
            ->select('iade_sebebi', DB::raw('count(*) as count'))
            ->groupBy('iade_sebebi')
            ->get();

        // Grafik Verileri (Bölüm Dağılımı)
        $deptDistribution = (clone $kpiQuery)
            ->join('musteri_sikayetleri', 'sikayet_iadeleri.musteri_sikayeti_id', '=', 'musteri_sikayetleri.id')
            ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
            ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
            ->select('bolumler.ad as bolum_adi', DB::raw('count(*) as count'))
            ->groupBy('bolumler.ad')
            ->get();

        // === 3. YENİ GRAFİK: TREND ANALİZİ (AYLIK) ===
        $trendData = (clone $kpiQuery)
            ->select(DB::raw('DATE_FORMAT(iade_tarihi, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Tarih aralığını doldur (Boş ayları 0 yap)
        $trendDates = [];
        $trendCounts = [];
        
        // Eğer tarih seçili değilse son 6 ayı göster, seçiliyse o aralığı
        $startRange = $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subMonths(5);
        
        // ÖNEMLİ: Eğer bitiş tarihi seçilmemişse (Tüm Zamanlar), BUGÜNÜ (Nisan) son sınır kabul et.
        // Böylece Mayıs/Haziran gibi gelecek aylar grafikte boş yere görünmez.
        $endRange = $this->endDate ? Carbon::parse($this->endDate) : Carbon::now();

        $curr = (clone $startRange)->startOfMonth();
        $limit = (clone $endRange)->startOfMonth();
        
        // Sonsuz döngü koruması (maks 24 ay)
        $safetyCounter = 0;
        while ($curr <= $limit && $safetyCounter < 24) {
            $formattedMonth = $curr->format('Y-m');
            $trendDates[] = $curr->translatedFormat('M Y');
            $found = $trendData->firstWhere('month', $formattedMonth);
            $trendCounts[] = $found ? $found->count : 0;
            $curr->addMonth();
            $safetyCounter++;
        }

        // === 4. YENİ GRAFİK: ŞİKAYET / İADE ORANI ===
        // Yetki bazlı toplam şikayet sorgusu
        $complaintQuery = \App\Models\MusteriSikayeti::query();
        
        if ($this->startDate) {
            $complaintQuery->whereDate('created_at', '>=', Carbon::parse($this->startDate)->startOfDay());
        }
        if ($this->endDate) {
            $complaintQuery->whereDate('created_at', '<=', Carbon::parse($this->endDate)->endOfDay());
        }
        
        $user = Auth::user();
        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
            $complaintQuery->where('konum_tipi', 'Yurt İçi');
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $complaintQuery->where('konum_tipi', 'Yurt Dışı');
        } elseif (!$user->hasAnyRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
            $allowedBolumIds = $user->getAllowedBolumIds();
            if ($allowedBolumIds !== '*') {
                $complaintQuery->whereHas('sikayetKategori', function($q) use ($allowedBolumIds) {
                    $q->whereIn('bolum_id', $allowedBolumIds);
                });
            }
        }
        
        if ($this->selectedBolumId) {
            $complaintQuery->whereHas('sikayetKategori', function($q) {
                $q->where('bolum_id', $this->selectedBolumId);
            });
        }

        $totalComplaints = $complaintQuery->count();

        // Numerator: Seçilen tarihlerde AÇILAN ve (herhangi bir zamanda) iadesi olan şikayetler
        $complaintsWithReturns = (clone $complaintQuery)->has('iadeler')->count();

        $complaintsNoReturns = max(0, $totalComplaints - $complaintsWithReturns);
        
        $ratioData = [$complaintsWithReturns, $complaintsNoReturns];
        $returnRate = $totalComplaints > 0 ? round(($complaintsWithReturns / $totalComplaints) * 100, 1) : 0;

        // === 5. YENİ GRAFİK: BÖLÜM & SEBEP PARETO (Horizontal Stacked Bar) ===
        $paretoDataRaw = (clone $kpiQuery)
            ->join('musteri_sikayetleri', 'sikayet_iadeleri.musteri_sikayeti_id', '=', 'musteri_sikayetleri.id')
            ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
            ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
            ->select('bolumler.ad as bolum_adi', 'sikayet_iadeleri.iade_sebebi', DB::raw('count(*) as count'))
            ->groupBy('bolumler.ad', 'sikayet_iadeleri.iade_sebebi')
            ->get();

        $reasonsList = $paretoDataRaw->pluck('iade_sebebi')->unique()->values();
        $departmentsList = $paretoDataRaw->pluck('bolum_adi')->unique()->values();

        // Bölümlere Göre Sebep Dağılımı (Metin/Mini-Bar Kartları İçin)
        $deptReasonStats = [];
        foreach($departmentsList as $dept) {
            $reasonsInDept = $paretoDataRaw->where('bolum_adi', $dept)->sortByDesc('count')->values();
            $totalInDept = $reasonsInDept->sum('count');
            
            $deptReasonStats[$dept] = [
                'total' => $totalInDept,
                'reasons' => $reasonsInDept->map(function($r) use ($totalInDept) {
                    return [
                        'sebep' => $r->iade_sebebi,
                        'count' => $r->count,
                        'percent' => $totalInDept > 0 ? round(($r->count / $totalInDept) * 100) : 0
                    ];
                })->toArray()
            ];
        }

        // Bölümleri en çok iade alandan en aza doğru sırala
        uasort($deptReasonStats, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        // Filtre seçenekleri (Yetki Bazlı Kısıtlandı)
        $allowedIds = Auth::user()->getAllowedBolumIds();
        $bolumQuery = Bolum::whereIn('id', SikayetKategori::pluck('bolum_id')->unique());
        
        if ($allowedIds !== '*') {
            $bolumQuery->whereIn('id', $allowedIds);
        }
        
        $bolumler = $bolumQuery->orderBy('ad')->get();
        $reasons = SikayetIadesi::select('iade_sebebi')->distinct()->whereNotNull('iade_sebebi')->pluck('iade_sebebi');

        // Grafikleri güncellemek için event fırlat
        $this->dispatch('contentChanged', [
            'reasons' => $reasonDistribution,
            'depts' => $deptDistribution,
            'trendDates' => $trendDates,
            'trendCounts' => $trendCounts,
            'ratioData' => $ratioData,
            'returnRate' => $returnRate
        ]);

        return view('livewire.sikayet-iadesi-raporu', [
            'iadeler' => $iadeler,
            'totalIadeCount' => $totalIadeCount,
            'unitTotals' => $unitTotals,
            'topReason' => $topReason,
            'reasonDistribution' => $reasonDistribution,
            'deptDistribution' => $deptDistribution,
            'trendDates' => $trendDates,
            'trendCounts' => $trendCounts,
            'ratioData' => $ratioData,
            'returnRate' => $returnRate,
            'deptReasonStats' => $deptReasonStats,
            'bolumler' => $bolumler,
            'reasons' => $reasons,
        ]);
    }
}
