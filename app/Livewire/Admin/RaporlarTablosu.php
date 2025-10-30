<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Iaa;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RaporlarTablosu extends Component
{
    use WithPagination;

    public $search = '';
    public $durum = '';
    public $kullaniciTipi = '';
    public $baslangicTarihi = '';
    public $bitisTarihi = '';
    public $initialChartData;


    /**
     * Bileşen ilk yüklendiğinde grafiği doldurmak için bu metod çalışır.
     */
    public function mount()
    {
        $this->initialChartData = $this->dispatchChartData();
    }

    // Filtrelerden herhangi biri değiştiğinde bu metodlar çalışır.
    public function updated($property)
    {
        if ($property !== 'page') {
            $this->resetPage();
        }
        $this->dispatchChartData();
    }

    public function resetFilters()
    {
        $this->reset();
        $this->dispatchChartData();
    }
    
    // Grafik verilerini hesaplar ve tarayıcıya bir event gönderir.

    // LÜTFEN MEVCUT dispatchChartData FONKSİYONUNU SİLİP BUNU YAPIŞTIRIN
    
    // Grafik verilerini hesaplar ve tarayıcıya bir event gönderir.
    public function dispatchChartData()
    {
        // 1. Kullanıcının seçtiği tüm filtreleri uygulayacağımız temel bir sorgu oluşturuyoruz.
        $baseQuery = Iaa::query()
            ->when($this->search, fn($q) => $q->where('baslik', 'like', '%' . $this->search . '%'))
            ->when($this->durum, function ($q) {
                if ($this->durum === 'Talep Alan') {
                    return $q->where('durum', 'Havuzda')->has('talepEdenTakimlar');
                }
                return $q->where('durum', $this->durum);
            })
            ->when($this->kullaniciTipi, function ($q) {
                if ($this->kullaniciTipi === 'kayitli') return $q->whereNotNull('gonderen_user_id');
                if ($this->kullaniciTipi === 'misafir') return $q->whereNull('gonderen_user_id');
            })
            ->when($this->baslangicTarihi, fn($q) => $q->whereDate('created_at', '>=', $this->baslangicTarihi))
            ->when($this->bitisTarihi, fn($q) => $q->whereDate('created_at', '<=', $this->bitisTarihi));

        $chartLabels = [];
        $chartData = [];

        // 2. Son 12 ay için bir döngü başlatıyoruz.
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            // Etiket olarak ay ve yılı ekliyoruz (örn: "Ekim 2025")
            $chartLabels[] = $date->translatedFormat('F Y');

            // 3. Her ay için, filtrelenmiş temel sorguyu klonlayıp o aya özel sayım yapıyoruz.
            // Bu yöntem, groupBy'dan daha basit ve güvenilirdir.
            $count = (clone $baseQuery)
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
            
            $chartData[] = $count;
        }
        
        // 4. Veriyi paketleyip tarayıcıya gönderiyoruz.
        $label = ($this->durum ? "'{$this->durum}' Durumundaki" : 'Tüm') . ' Önerilerin Aylık Trendi';
        $payload = ['labels' => $chartLabels, 'data' => $chartData, 'label' => $label];
        $this->dispatch('updateChart', $payload);

        return $payload;
    }


    public function getDurumBadgeClasses($durum)
    {
        return [
            'Onay Bekliyor' => 'bg-yellow-100 text-yellow-800',
            'Havuzda' => 'bg-gray-200 text-gray-800',
            'Talep Alan' => 'bg-sky-100 text-sky-800',
            'Atandı' => 'bg-blue-100 text-blue-800',
            'Revize Ediliyor' => 'bg-orange-100 text-orange-800',
            'Yönetici Onayı Bekliyor' => 'bg-cyan-100 text-cyan-800',
            'Tamamlandı' => 'bg-green-100 text-green-800',
            'Reddedildi' => 'bg-red-100 text-red-800',
            'Tamamlanması Reddedildi' => 'bg-red-200 text-red-900',
        ][$durum] ?? 'bg-gray-100 text-gray-800';
    }
    

    public function render()
    {
        $query = Iaa::query()->with(['gonderen.bolum', 'bolum', 'atananTakim.uyeler'])->latest();

        $query->when($this->search, fn($q) => $q->where('baslik', 'like', '%' . $this->search . '%'));
        
        $query->when($this->durum, function ($q) {
            if ($this->durum === 'Talep Alan') {
                return $q->where('durum', 'Havuzda')->has('talepEdenTakimlar');
            }
            return $q->where('durum', $this->durum);
        });

        $query->when($this->kullaniciTipi, function ($q) {
            if ($this->kullaniciTipi === 'kayitli') return $q->whereNotNull('gonderen_user_id');
            if ($this->kullaniciTipi === 'misafir') return $q->whereNull('gonderen_user_id');
        });

        $query->when($this->baslangicTarihi, fn($q) => $q->whereDate('created_at', '>=', $this->baslangicTarihi));
        $query->when($this->bitisTarihi, fn($q) => $q->whereDate('created_at', '<=', $this->bitisTarihi));

        $iaas = $query->paginate(10);
        $startNumber = ($iaas->currentPage() - 1) * $iaas->perPage();

        return view('livewire.admin.raporlar-tablosu', compact('iaas', 'startNumber'));
    }
}

