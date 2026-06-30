<?php

namespace App\Livewire\Admin;

use App\Models\Bolum;
use App\Models\Iaa;
use App\Models\Takim;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class BolumIaaListesi extends Component
{
    use WithPagination;

    public $bolumId;
    public $perPage = 10;
    
    // Filtreler
    public $search = '';
    public $status = '';
    public $suggesterId = '';
    public $teamId = '';

    protected $listeners = ['refreshIaaList' => '$refresh'];

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
        if (in_array($propertyName, ['search', 'status', 'suggesterId', 'teamId'])) {
            $this->perPage = 10;
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'suggesterId', 'teamId']);
        $this->perPage = 10;
        $this->resetPage();
    }

    public function render()
    {
        $bolum = Bolum::findOrFail($this->bolumId);
        
        $query = Iaa::where('bolum_id', $this->bolumId)
            ->sadeceOneriler()
            ->with(['gonderen', 'atananTakim.lider'])
            ->latest();

        if ($this->search) {
            $query->where('baslik', 'like', '%' . $this->search . '%');
        }

        if ($this->status) {
            $query->where('durum', $this->status);
        }

        if ($this->suggesterId) {
            $query->where('gonderen_user_id', $this->suggesterId);
        }

        if ($this->teamId) {
            $query->where('atanan_takim_id', $this->teamId);
        }

        $iaaProjeleri = $query->paginate($this->perPage);

        // Filtreler için veriler
        $suggesterIds = Iaa::where('bolum_id', $this->bolumId)->sadeceOneriler()->distinct()->pluck('gonderen_user_id');
        $suggesters = User::whereIn('id', $suggesterIds)->orderBy('name')->get();
        
        $teamIds = Iaa::where('bolum_id', $this->bolumId)->sadeceOneriler()->distinct()->pluck('atanan_takim_id')->filter();
        $teams = Takim::whereIn('id', $teamIds)->orderBy('ad')->get();

        $statuses = [
            'Yeni', 'Atandı', 'Devam Ediyor', 'Tamamlandı', 'Reddedildi', 
            'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor'
        ];

        return view('livewire.admin.bolum-iaa-listesi', [
            'iaaProjeleri' => $iaaProjeleri,
            'suggesters' => $suggesters,
            'teams' => $teams,
            'statuses' => $statuses,
            'totalCount' => $query->count()
        ]);
    }
}
