<?php

namespace App\Livewire\Admin;

use App\Models\Bolum;
use App\Models\Customer;
use App\Models\MusteriSikayeti;
use Livewire\Component;
use Livewire\WithPagination;

class BolumSikayetListesi extends Component
{
    use WithPagination;

    public $bolumId;
    public $perPage = 10;
    
    // Filtreler
    public $status = '';
    public $customerId = '';
    public $startDate = '';
    public $endDate = '';
    public $search = '';

    protected $listeners = ['refreshSikayetList' => '$refresh'];

    public function mount($bolumId)
    {
        $this->bolumId = $bolumId;
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['status', 'customerId', 'startDate', 'endDate', 'search'])) {
            $this->perPage = 10; // Filtre değişince başa dön
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset(['status', 'customerId', 'startDate', 'endDate', 'search']);
        $this->perPage = 10;
        $this->resetPage();
    }

    public function render()
    {
        $bolum = Bolum::findOrFail($this->bolumId);
        
        $query = $bolum->sikayetler()
            ->with(['customer', 'iaaProjesi', 'cozumTakimi.users', 'sikayetKategori', 'sikayetAltKategori'])
            ->latest('musteri_sikayet_tarihi');

        if ($this->startDate) {
            $query->whereDate('musteri_sikayet_tarihi', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('musteri_sikayet_tarihi', '<=', $this->endDate);
        }
        if ($this->status) {
            $query->where('musteri_durum', $this->status);
        }
        if ($this->customerId) {
            $query->where('customer_id', $this->customerId);
        }

        if ($this->search) {
            $query->where('musteri_sikayet_konusu', 'like', '%' . $this->search . '%');
        }

        $sikayetler = $query->paginate($this->perPage);

        // Filtreler için veriler
        $relatedCustomerIds = $bolum->sikayetler()->select('customer_id')->distinct()->pluck('customer_id');
        $customers = Customer::whereIn('id', $relatedCustomerIds)->orderBy('name')->get();

        $statuses = [
            'Yeni', 'İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor', 
            'Çözümlendi', 'Kapatıldı', 'Tamamlandı', 'İptal Edildi', 
            'Reddedildi', 'Revize'
        ];

        return view('livewire.admin.bolum-sikayet-listesi', [
            'sikayetler' => $sikayetler,
            'customers' => $customers,
            'statuses' => $statuses,
            'bolum' => $bolum
        ]);
    }
}
