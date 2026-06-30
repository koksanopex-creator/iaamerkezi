<?php

namespace App\Livewire\Admin;

use App\Models\IaaZiyaretPlani;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithPagination;

class ZiyaretPlanlarim extends Component
{
    use WithPagination;

    public $activeTab = 'my_visits'; // my_visits, pending_approval

    protected $queryString = [
        'activeTab' => ['except' => 'my_visits'],
    ];

    public function mount()
    {
        // Yetki kontrolü yapsak iyi olur ama genelde herkes erişebilmeli
        if (!Auth::user()->hasRole(['Superadmin', 'Yönetim', 'Direktör'])) {
            $this->activeTab = 'my_visits';
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $query = IaaZiyaretPlani::with(['iaa.musteriSikayeti.customer', 'iaa.bolum'])->latest();

        // Admin/Director see pending approvals, others only their own
        if ($this->activeTab === 'pending_approval') {
            $query->whereIn('status', ['Beklemede', 'Direktör Onayı Bekliyor', 'Yönetim Onayı Bekliyor', 'Revizyon Bekliyor']);
        } else {
            // My visits
            // Eger visitor_id Takvim'deki ile eşleşiyorsa. Fakat IAA'da visitor_id email veya ID olarak tutuluyor olabilir.
            // Biz şimdilik Takvim user_id'sini eşleştiremiyorsak created_by (iaa) gibi bir şey kullanmalıyız.
            // Şimdilik sadece user'in adını içeren veya visitor_id? Takvim entegrasyonuna bakarak Iaa'nın oluşturucusuna da bakabiliriz.
            $query->where(function($q) use ($user) {
                $q->where('visitor_id', $user->id)
                  ->orWhere('visitor_name', 'like', '%' . $user->name . '%')
                  ->orWhere('planner_id', $user->id)
                  ->orWhereHas('iaa', function ($iaaQuery) use ($user) {
                      $iaaQuery->where('iaas.gonderen_user_id', $user->id);
                  });
            });
        }

        $visits = $query->paginate(15);

        return view('livewire.admin.ziyaret-planlarim', [
            'visits' => $visits
        ])->layout('layouts.app');
    }
}
