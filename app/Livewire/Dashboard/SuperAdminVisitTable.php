<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\IaaZiyaretPlani;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\VisitNotificationTrait;

class SuperAdminVisitTable extends Component
{
    use VisitNotificationTrait;
    public $startDate;
    public $endDate;
    public $status = 'all';
    public $bolumIds = [];
    public $iaaIds = [];
    public $hideHeader = false;
    public $showAll = false;
    public $initialLimit = 5;
    
    // Approval Form
    public $showApproveModal = false;
    public $selectedVisitId;
    public $estimatedReturnDate;
    
    // Rejection Form
    public $showRejectModal = false;
    public $rejectionReason;
    public $actionType; // 'reject' or 'revision'

    protected $queryString = ['startDate', 'endDate', 'status'];

    protected $listeners = ['refreshVisitTable' => '$refresh'];

    public function mount($bolumIds = [], $hideHeader = false, $iaaIds = [])
    {
        $this->bolumIds = $bolumIds;
        $this->iaaIds = $iaaIds;
        $this->hideHeader = $hideHeader;
        $this->startDate = null;
        $this->endDate = null;
    }

    public function render()
    {
        $query = IaaZiyaretPlani::with(['iaa.musteriSikayeti.customer', 'planner', 'visitor', 'iaa.atananTakim', 'approver']);
            
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('visit_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }


        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if (!empty($this->bolumIds)) {
            $query->whereHas('iaa', function ($q) {
                $q->whereIn('bolum_id', $this->bolumIds)
                    ->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) {
                        $sq->whereIn('bolum_id', $this->bolumIds);
                    });
            });
        }

        if (!empty($this->iaaIds)) {
            $query->whereIn('iaa_id', $this->iaaIds);
        }

        $allVisits = $query->latest('visit_date')->get();
        $totalCount = $allVisits->count();
        $visits = $this->showAll ? $allVisits : $allVisits->take($this->initialLimit);

        return view('livewire.dashboard.super-admin-visit-table', [
            'visits' => $visits,
            'totalCount' => $totalCount,
            'hasMore' => $totalCount > $this->initialLimit,
        ]);
    }

    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
    }

    public function resetFilters()
    {
        $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        $this->status = 'all';
        $this->showAll = false;
    }

    // --- Onay / Red / Revizyon / Geri Al Metodları (VisitApprovalWidget'tan taşındı) ---

    public function openApproveModal($visitId)
    {
        $this->selectedVisitId = $visitId;
        $this->showApproveModal = true;
        $this->estimatedReturnDate = now()->addDays(7)->format('Y-m-d');
    }

    public function approveVisit()
    {
        $this->validate([
            'estimatedReturnDate' => 'required|date|after_or_equal:today',
        ]);

        $ziyaretPlani = IaaZiyaretPlani::find($this->selectedVisitId);
        if ($ziyaretPlani) {
            $ziyaretPlani->status = 'Onaylandı';
            $ziyaretPlani->estimated_return_date = $this->estimatedReturnDate;
            $ziyaretPlani->approved_by = Auth::id();
            $ziyaretPlani->save();

            \App\Models\IaaLog::create([
                'iaa_id' => $ziyaretPlani->iaa_id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı Onaylandı',
                'aciklama' => Auth::user()->name . " tarafından tablo üzerinden ziyaret planı onaylandı. Tahmini Dönüş: " . Carbon::parse($this->estimatedReturnDate)->format('d.m.Y')
            ]);

            if ($ziyaretPlani->iaa) {
                $this->dispatchVisitWorkflowNotifications($ziyaretPlani->iaa, 'Onaylandı', Auth::user()->name);
            }

            $this->showApproveModal = false;
            $this->dispatch('refreshVisitTable');
            session()->flash('success', 'Ziyaret planı onaylandı.');
        }
    }

    public function openRejectModal($visitId, $type = 'reject')
    {
        $this->selectedVisitId = $visitId;
        $this->actionType = $type;
        $this->showRejectModal = true;
        $this->rejectionReason = '';
    }

    public function processRejectOrRevision()
    {
        $this->validate([
            'rejectionReason' => 'required|min:5',
        ]);

        $ziyaretPlani = IaaZiyaretPlani::find($this->selectedVisitId);
        if ($ziyaretPlani) {
            $status = ($this->actionType === 'revision') ? 'Revize İsteniyor' : 'Reddedildi';
            $ziyaretPlani->status = $status;
            
            $user = Auth::user();
            if ($user->hasRole('Direktör')) {
                $ziyaretPlani->rejection_reason_director = $this->rejectionReason;
            } elseif ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                $ziyaretPlani->rejection_reason_quality = $this->rejectionReason;
            } elseif ($user->hasRole('Superadmin')) {
                $ziyaretPlani->rejection_reason_superadmin = $this->rejectionReason;
            } else {
                $ziyaretPlani->reject_reason = $this->rejectionReason;
            }
            
            $ziyaretPlani->save();

            \App\Models\IaaLog::create([
                'iaa_id' => $ziyaretPlani->iaa_id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı ' . $status,
                'aciklama' => Auth::user()->name . " tarafından tablo üzerinden ziyaret planına $status verildi. Sebep: " . $this->rejectionReason
            ]);

            // Bildirimler
            if ($ziyaretPlani->iaa) {
                $this->dispatchVisitWorkflowNotifications($ziyaretPlani->iaa, $status, Auth::user()->name, $this->rejectionReason);
            }

            $this->showRejectModal = false;
            $this->dispatch('refreshVisitTable');
            session()->flash('success', "Ziyaret planı $status durumuna getirildi.");
        }
    }

    public function undoAction($visitId)
    {
        $ziyaretPlani = IaaZiyaretPlani::find($visitId);
        if ($ziyaretPlani) {
            $oldStatus = $ziyaretPlani->status;
            $ziyaretPlani->status = 'Beklemede';
            $ziyaretPlani->approved_by = null;
            $ziyaretPlani->estimated_return_date = null;
            
            // Sebepleri temizle
            $ziyaretPlani->rejection_reason_director = null;
            $ziyaretPlani->rejection_reason_quality = null;
            $ziyaretPlani->rejection_reason_superadmin = null;
            $ziyaretPlani->reject_reason = null;
            
            $ziyaretPlani->save();

            \App\Models\IaaLog::create([
                'iaa_id' => $ziyaretPlani->iaa_id,
                'user_id' => Auth::id(),
                'eylem' => 'İşlem Geri Alındı',
                'aciklama' => Auth::user()->name . " tarafından $oldStatus işlemi geri alındı."
            ]);

            $this->dispatch('refreshVisitTable');
            session()->flash('success', 'İşlem geri alındı.');
        }
    }
}
