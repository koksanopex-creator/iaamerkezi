<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\MusteriSikayeti;
use Carbon\Carbon;

class ZiyaretListesi extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $search = '';
    public $perPage = 15;

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        if (!Auth::user()->canViewZiyaretlerPage()) {
            abort(403, 'Bu sayfayı görüntüleme yetkiniz yok.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['startDate', 'endDate', 'search']);
        $this->resetPage();
    }

    public function getVisitsProperty()
    {
        $user = Auth::user();
        $allowedBolumIds = $user->getAllowedBolumIds();

        $query = \App\Models\IaaZiyaretPlani::with(['iaa.musteriSikayeti.customer', 'visitor']);

        if ($allowedBolumIds !== '*') {
            $query->where(function ($q) use ($allowedBolumIds, $user) {
                // 1. Üretim/Şikayet bölümü yöneticisi ise (kendi bölümlerinin şikayet projelerindeki ziyaretler)
                if (!empty($allowedBolumIds)) {
                    $q->orWhereHas('iaa', function ($sq) use ($allowedBolumIds) {
                        $sq->whereIn('bolum_id', $allowedBolumIds)
                            ->orWhereHas('musteriSikayeti.sikayetKategori', function ($ssq) use ($allowedBolumIds) {
                                $ssq->whereIn('bolum_id', $allowedBolumIds);
                            });
                    });
                }

                // 2. Kendi personelinin ziyaretleri (Sevkiyat Lideri vb. için)
                $yonetilenZiyaretciBolumIds = [];
                if ($user->hasRole('Direktör')) {
                    $yonetilenZiyaretciBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
                } elseif ($user->hasRole(['Bölüm Lideri', 'Müşteri Şikayeti Çözüm Lideri']) && $user->bolum_id) {
                    $yonetilenZiyaretciBolumIds[] = $user->bolum_id;
                } elseif ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id && $user->hasPermissionTo('bolum.ziyaret.gor')) {
                    $yonetilenZiyaretciBolumIds[] = $user->bolum_id;
                }

                if (!empty($yonetilenZiyaretciBolumIds)) {
                    $personelIds = \App\Models\User::whereIn('bolum_id', $yonetilenZiyaretciBolumIds)->pluck('id')->toArray();
                    if (!empty($personelIds)) {
                        $q->orWhereIn('visitor_id', $personelIds);
                        foreach ($personelIds as $pId) {
                            $q->orWhereJsonContains('visitors', (string)$pId)
                              ->orWhereJsonContains('visitors', $pId);
                        }
                    }
                }

                // 3. Kendisi ziyaretçi ise
                $q->orWhere('visitor_id', $user->id)
                  ->orWhereJsonContains('visitors', (string)$user->id)
                  ->orWhereJsonContains('visitors', $user->id);
            });
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('visit_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('visit_reason', 'like', '%' . $this->search . '%')
                  ->orWhere('visit_notes', 'like', '%' . $this->search . '%')
                  ->orWhereHas('visitor', function($vq) {
                      $vq->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('iaa.musteriSikayeti.customer', function($cq) {
                      $cq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $paginator = $query->latest('visit_date')->paginate($this->perPage);

        $data = $paginator->map(function($visit) {
            return [
                'id' => $visit->id,
                'visit_date' => $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d H:i:s') : null,
                'created_at' => $visit->created_at ? \Carbon\Carbon::parse($visit->created_at)->format('Y-m-d H:i:s') : null,
                'customer' => [
                    'name' => $visit->iaa->musteriSikayeti->customer->name ?? '',
                    'logo_path' => $visit->iaa->musteriSikayeti->customer->logo_path ?? null,
                ],
                'product' => ['name' => $visit->iaa->musteriSikayeti->musteri_urun_veya_hizmet ?? ''],
                'visit_reason' => $visit->visit_reason,
                'visit_notes' => $visit->visit_notes,
                'user' => ['name' => $visit->visitor->name ?? $visit->visitor_name],
                'status' => $visit->status,
                'estimated_return_date' => $visit->estimated_return_date ? \Carbon\Carbon::parse($visit->estimated_return_date)->format('Y-m-d H:i:s') : null,
                'return_date_revision_status' => $visit->return_date_revision_status,
                'updated_at' => $visit->updated_at ? \Carbon\Carbon::parse($visit->updated_at)->format('Y-m-d H:i:s') : null,
                'completed_at' => $visit->completed_at ? \Carbon\Carbon::parse($visit->completed_at)->format('Y-m-d H:i:s') : null,
                'cancelled_at' => $visit->cancelled_at ? \Carbon\Carbon::parse($visit->cancelled_at)->format('Y-m-d H:i:s') : null,
                'remote_id' => $visit->iaa->musteriSikayeti->id ?? null,
                'remote_url' => route('proje.workspace.show', $visit->iaa_id)
            ];
        })->toArray();

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage()
        ];
    }

    public function render()
    {
        $visitsData = $this->visits;
        
        // Enrich visits with IAA complaint subject/link
        $complaintIds = collect($visitsData['data'])->pluck('remote_id')->filter()->unique();
        $complaints = MusteriSikayeti::whereIn('id', $complaintIds)
            ->with(['iaaProjesi', 'bolum'])
            ->get()
            ->keyBy('id');

        return view('livewire.admin.ziyaret-listesi', [
            'visits' => $visitsData['data'],
            'pagination' => [
                'total' => $visitsData['total'],
                'current_page' => $visitsData['current_page'],
                'last_page' => $visitsData['last_page'],
                'per_page' => $visitsData['per_page'] ?? $this->perPage
            ],
            'complaints' => $complaints
        ])->layout('layouts.app');
    }
}
