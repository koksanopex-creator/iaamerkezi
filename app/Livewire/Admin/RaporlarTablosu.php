<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Iaa;
use App\Models\User;
use App\Models\Takim;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ProfesyonelIaalarExport;
use Carbon\Carbon;

class RaporlarTablosu extends Component
{
    use WithPagination;

    // --- FİLTRELER ---
    public $baslangicTarihi = null;
    public $bitisTarihi = null;
    public $search = '';
    public $durum = '';
    public $kullaniciTipi = '';

    // --- KPI VERİLERİ ---
    public $kpi = [];

    public function mount()
    {
        $this->hesaplaVeGonder();
    }

    public function updated($property)
    {
        if ($property !== 'page') {
            $this->resetPage();
        }
        $this->dispatch('refreshCharts', $this->hesaplaVeGonder());
    }

    public function resetFilters()
    {
        $this->reset(['search', 'durum', 'kullaniciTipi', 'baslangicTarihi', 'bitisTarihi']);
        $this->resetPage();
        $this->dispatch('refreshCharts', $this->hesaplaVeGonder());
    }

    private function baseQuery()
    {
        $query = Iaa::query()
            ->whereNotExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                         ->from('musteri_sikayetleri')
                         ->whereColumn('musteri_sikayetleri.iaa_id', 'iaas.id');
            })
            ->where('iaas.durum', '!=', 'talep_olarak_kapatildi');

        if ($this->baslangicTarihi) {
            $query->whereDate('iaas.created_at', '>=', $this->baslangicTarihi);
        }
        if ($this->bitisTarihi) {
            $query->whereDate('iaas.created_at', '<=', $this->bitisTarihi);
        }

        return $query;
    }

    public function hesaplaVeGonder()
    {
        $base = $this->baseQuery();

        // 1. KPI KARTLARI
        $this->kpi = [
            'toplam_oneri' => (clone $base)->count(),
            'onay_bekleyen_oneri' => (clone $base)->where('iaas.durum', 'Onay Bekliyor')->count(),
            'havuzdaki_oneri' => (clone $base)->where('iaas.durum', 'Havuzda')->count(),
            'atanmis_proje' => (clone $base)->whereIn('iaas.durum', ['Atandı', 'Revize Ediliyor', 'Yönetici Onayı Bekliyor'])->count(),
            'tamamlanan_proje' => (clone $base)->where('iaas.durum', 'Tamamlandı')->count(),
            'reddedilen_oneri' => (clone $base)->whereIn('iaas.durum', ['Reddedildi', 'Tamamlanması Reddedildi'])->count(),
            'kullanici_onerileri' => (clone $base)->whereNotNull('iaas.gonderen_user_id')->count(),
            'misafir_onerileri' => (clone $base)->whereNull('iaas.gonderen_user_id')->count(),
            'toplam_takim' => Takim::count(),
            'toplam_kullanici' => User::where('is_personnel', 1)->count(),
        ];

        // 2. TREND
        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartLabels[] = $date->translatedFormat('F Y');
            $count = (clone $base)
                ->whereYear('iaas.created_at', $date->year)
                ->whereMonth('iaas.created_at', $date->month)
                ->count();
            $chartData[] = $count;
        }

        // 3. ORAN
        $oranChartData = [$this->kpi['tamamlanan_proje'], $this->kpi['toplam_oneri'] - $this->kpi['tamamlanan_proje']];

        // 4. ÇOKLU ÜYELİK (Personel)
        $cokluUyelik = DB::table('takim_user')
            ->join('users', 'takim_user.user_id', '=', 'users.id')
            ->join('takimlar', 'takim_user.takim_id', '=', 'takimlar.id')
            ->where('users.is_personnel', 1)
            ->where('takimlar.tur', 'iaa')
            ->select('users.name', DB::raw('COUNT(takim_user.takim_id) as takim_sayisi'))
            ->groupBy('users.name')
            ->orderByDesc('takim_sayisi')
            ->limit(5)
            ->pluck('takim_sayisi', 'name');
        $cokluUyelikData = ['labels' => $cokluUyelik->keys(), 'data' => $cokluUyelik->values()];

        // 5. PUAN LİDERLİK
        $liderlikQuery = User::where('is_personnel', 1)
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'Superadmin'));

        if (!$this->baslangicTarihi && !$this->bitisTarihi) {
            $liderlik = $liderlikQuery->where('toplam_puan', '>', 0)->orderByDesc('toplam_puan')->take(5)->get()->map(fn($u) => ['name' => $u->name, 'puan' => $u->toplam_puan]);
        } else {
            $liderlik = $liderlikQuery->select('users.name')->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(puan), 0)')->from('iaas')->join('takim_user', 'iaas.atanan_takim_id', '=', 'takim_user.takim_id')->whereColumn('takim_user.user_id', 'users.id')->where('iaas.durum', 'Tamamlandı')->whereNotExists(fn($sq) => $sq->select(DB::raw(1))->from('musteri_sikayetleri')->whereColumn('musteri_sikayetleri.iaa_id', 'iaas.id'));
                if ($this->baslangicTarihi) $query->whereDate('iaas.created_at', '>=', $this->baslangicTarihi);
                if ($this->bitisTarihi) $query->whereDate('iaas.created_at', '<=', $this->bitisTarihi);
            }, 'donem_puani')->having('donem_puani', '>', 0)->orderByDesc('donem_puani')->take(5)->get()->map(fn($u) => ['name' => $u->name, 'puan' => $u->donem_puani]);
        }
        $puanChartData = ['labels' => $liderlik->pluck('name'), 'data' => $liderlik->pluck('puan')];

        // 6. HAVUZ PUAN
        $havuzPuan = (clone $base)->where('iaas.durum', 'Havuzda')->whereNotNull('iaas.puan')->orderByDesc('iaas.puan')->take(5)->pluck('iaas.puan', 'iaas.baslik');
        $havuzPuanData = ['labels' => $havuzPuan->keys(), 'data' => $havuzPuan->values()];

        // 7. TAMAMLANAN PUAN
        $tamamlananPuan = (clone $base)->where('iaas.durum', 'Tamamlandı')->whereNotNull('iaas.puan')->orderByDesc('iaas.puan')->take(5)->pluck('iaas.puan', 'iaas.baslik');
        $tamamlananPuanData = ['labels' => $tamamlananPuan->keys(), 'data' => $tamamlananPuan->values()];

        // 8. HIZLI PROJE
        $hizliProjeler = (clone $base)->where('iaas.durum', 'Tamamlandı')->whereNotNull('iaas.onaylanma_tarihi')->select('iaas.baslik', DB::raw('DATEDIFF(iaas.updated_at, iaas.onaylanma_tarihi) as sure_gun'))->orderBy('sure_gun', 'asc')->take(5)->pluck('sure_gun', 'iaas.baslik');
        $hizliProjeData = ['labels' => $hizliProjeler->keys(), 'data' => $hizliProjeler->values()];

        // 9. BÖLÜM DAĞILIMI
        $bolumQuery = Iaa::query()->whereNotExists(fn($sq) => $sq->select(DB::raw(1))->from('musteri_sikayetleri')->whereColumn('musteri_sikayetleri.iaa_id', 'iaas.id'))->where('iaas.durum', '!=', 'talep_olarak_kapatildi');
        if ($this->baslangicTarihi) $bolumQuery->whereDate('iaas.created_at', '>=', $this->baslangicTarihi);
        if ($this->bitisTarihi) $bolumQuery->whereDate('iaas.created_at', '<=', $this->bitisTarihi);
        $bolumDagilimi = $bolumQuery->join('bolumler', 'iaas.bolum_id', '=', 'bolumler.id')->select('bolumler.ad', DB::raw('count(*) as total'))->groupBy('bolumler.ad')->orderByDesc('total')->pluck('total', 'bolumler.ad');
        $bolumChartData = ['labels' => $bolumDagilimi->keys(), 'data' => $bolumDagilimi->values()];

        // 10. DURUM DAĞILIMI
        $durumDagilimi = (clone $base)->select('iaas.durum', DB::raw('count(*) as total'))->groupBy('iaas.durum')->pluck('total', 'iaas.durum');
        $renkHaritasi = ['Onay Bekliyor' => '#eab308', 'Havuzda' => '#6b7280', 'Atandı' => '#3b82f6', 'Tamamlandı' => '#22c55e', 'Reddedildi' => '#ef4444', 'Revize Ediliyor' => '#f97316', 'Yönetici Onayı Bekliyor' => '#06b6d4', 'Talep Alan' => '#0ea5e9', 'Tamamlanması Reddedildi' => '#be123c'];
        $durumLabels = $durumDagilimi->keys();
        $durumValues = $durumDagilimi->values();
        $durumColors = $durumLabels->map(fn($d) => $renkHaritasi[$d] ?? '#cbd5e1')->toArray();
        $durumChartData = ['labels' => $durumLabels, 'data' => $durumValues, 'colors' => $durumColors];

        // 11. GELİŞMİŞ RİSK ANALİZİ
        $rawRiskData = (clone $base)->whereNotNull('iaas.risk')->select('iaas.risk', DB::raw('count(*) as total'))->groupBy('iaas.risk')->pluck('total', 'iaas.risk');
        $riskDefinitions = [
            1 => ['label' => 'Düşük', 'color' => '#22c55e'],
            2 => ['label' => 'Düşük-Orta', 'color' => '#3b82f6'],
            3 => ['label' => 'Orta', 'color' => '#eab308'],
            4 => ['label' => 'Yüksek', 'color' => '#f97316'],
            5 => ['label' => 'Çok Yüksek', 'color' => '#ef4444'],
        ];
        $riskLabels = [];
        $riskData = [];
        $riskColors = [];
        foreach ($rawRiskData as $level => $count) {
            $def = $riskDefinitions[$level] ?? ['label' => "Seviye $level", 'color' => '#94a3b8'];
            $riskLabels[] = $def['label'];
            $riskData[] = $count;
            $riskColors[] = $def['color'];
        }
        $riskChartData = ['labels' => $riskLabels, 'data' => $riskData, 'colors' => $riskColors];

        return [
            'kpi' => $this->kpi,
            'trend' => ['labels' => $chartLabels, 'data' => $chartData],
            'oran' => $oranChartData,
            'cokluUyelik' => $cokluUyelikData,
            'puan' => $puanChartData,
            'havuzPuan' => $havuzPuanData,
            'tamamlananPuan' => $tamamlananPuanData,
            'hizliProje' => $hizliProjeData,
            'bolum' => $bolumChartData,
            'durum' => $durumChartData,
            'risk' => $riskChartData
        ];
    }

    public function render()
    {
        $chartData = $this->hesaplaVeGonder();
        $iaas = $this->getTableQuery()->latest('iaas.created_at')->paginate(10);
        $startNumber = ($iaas->currentPage() - 1) * $iaas->perPage();
        return view('livewire.admin.raporlar-tablosu', compact('iaas', 'startNumber', 'chartData'));
    }

    private function getTableQuery()
    {
        $query = $this->baseQuery()
            ->with(['gonderen.bolum', 'bolum', 'atananTakim.uyeler']);

        if ($this->search) $query->where('iaas.baslik', 'like', '%' . $this->search . '%');
        if ($this->durum) {
            if ($this->durum === 'Talep Alan') $query->where('iaas.durum', 'Havuzda')->has('talepEdenTakimlar');
            else $query->where('iaas.durum', $this->durum);
        }
        if ($this->kullaniciTipi) {
            if ($this->kullaniciTipi === 'kayitli') $query->whereNotNull('iaas.gonderen_user_id');
            if ($this->kullaniciTipi === 'misafir') $query->whereNull('iaas.gonderen_user_id');
        }
        return $query;
    }

    // --- YENİ EKLENEN: DİNAMİK DOSYA İSMİ OLUŞTURUCU ---
    private function getDynamicFileName($extension)
    {
        $name = 'IAA_Rapor';

        if ($this->baslangicTarihi && $this->bitisTarihi) {
            // Tarih aralığı varsa: IAA_Rapor_13.01.2026_30.01.2026.pdf
            $bas = Carbon::parse($this->baslangicTarihi)->format('d.m.Y');
            $bit = Carbon::parse($this->bitisTarihi)->format('d.m.Y');
            $name .= '_' . $bas . '_' . $bit;
        } elseif ($this->baslangicTarihi) {
            // Sadece başlangıç varsa (Opsiyonel)
            $bas = Carbon::parse($this->baslangicTarihi)->format('d.m.Y');
            $name .= '_' . $bas . '_Itibaren';
        }

        return $name . '.' . $extension;
    }

    public function downloadExcel()
    {
        $filters = ['baslangicTarihi' => $this->baslangicTarihi, 'bitisTarihi' => $this->bitisTarihi, 'search' => $this->search, 'durum' => $this->durum, 'kullaniciTipi' => $this->kullaniciTipi];
        // Dosya ismi dinamik fonksiyondan geliyor
        return Excel::download(new ProfesyonelIaalarExport($filters), $this->getDynamicFileName('xlsx'));
    }

    public function downloadPdf()
    {
        $iaas = $this->getTableQuery()->get();
        
        // Tarih bilgisini View'e gönderiyoruz
        $tarihBilgisi = "Tüm Zamanlar";
        if ($this->baslangicTarihi && $this->bitisTarihi) {
            $bas = Carbon::parse($this->baslangicTarihi)->format('d.m.Y');
            $bit = Carbon::parse($this->bitisTarihi)->format('d.m.Y');
            $tarihBilgisi = "$bas - $bit Tarihleri Arası";
        } elseif ($this->baslangicTarihi) {
            $tarihBilgisi = Carbon::parse($this->baslangicTarihi)->format('d.m.Y') . " Tarihinden İtibaren";
        }

        $pdf = Pdf::loadView('admin.raporlar.partials.rapor-pdf', compact('iaas', 'tarihBilgisi'));
        
        // Dosya ismi dinamik fonksiyondan geliyor
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $this->getDynamicFileName('pdf'));
    }

    public function getDurumBadgeClasses($durum)
    {
        return match($durum) {
            'Onay Bekliyor' => 'bg-yellow-100 text-yellow-800',
            'Havuzda' => 'bg-gray-200 text-gray-800',
            'Atandı' => 'bg-blue-100 text-blue-800',
            'Tamamlandı' => 'bg-green-100 text-green-800',
            'Reddedildi' => 'bg-red-100 text-red-800',
            'Revize Ediliyor' => 'bg-orange-100 text-orange-800',
            'Yönetici Onayı Bekliyor' => 'bg-cyan-100 text-cyan-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getRiskBadgeClasses($risk)
    {
        return match($risk) {
            1 => 'bg-green-100 text-green-800 border border-green-200',
            2 => 'bg-blue-100 text-blue-800 border border-blue-200',
            3 => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            4 => 'bg-orange-100 text-orange-800 border border-orange-200',
            5 => 'bg-red-100 text-red-800 border border-red-200',
            default => 'text-gray-400',
        };
    }

    public function getRiskLabel($risk)
    {
        return match($risk) {
            1 => 'Düşük',
            2 => 'Düşük-Orta',
            3 => 'Orta',
            4 => 'Yüksek',
            5 => 'Çok Yüksek',
            default => '-',
        };
    }
}