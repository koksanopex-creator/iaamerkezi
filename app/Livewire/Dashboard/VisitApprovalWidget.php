<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\IaaZiyaretPlani;
use App\Models\IaaLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VisitApprovalWidget extends Component
{
    public $bolumId;
    public $pendingVisits = [];
    
    // Approval Form
    public $showApproveModal = false;
    public $selectedVisitId;
    public $estimatedReturnDate;
    
    // Rejection Form
    public $showRejectModal = false;
    public $rejectionReason;
    public $actionType; // 'reject' or 'revision'

    protected $listeners = ['refreshVisitWidget' => '$refresh'];

    public function mount($bolumId, $initialVisits = [])
    {
        $this->bolumId = $bolumId;
        // initialVisits bir collection ise toArray() veyaそのまま kullanılabilir
        $this->loadVisits();
    }

    public function loadVisits()
    {
        // Re-load to ensure fresh data
        // Beklemede, Onaylandı, Reddedildi ve Revize İsteniyor durumlarını getiriyoruz.
        $this->pendingVisits = IaaZiyaretPlani::whereIn('status', ['Beklemede', 'Onaylandı', 'Reddedildi', 'Revize İsteniyor'])
            ->whereHas('iaa', function ($q) {
                $q->where('bolum_id', $this->bolumId)
                    ->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) {
                        $sq->where('bolum_id', $this->bolumId);
                    });
            })
            ->with(['iaa.musteriSikayeti.customer', 'planner', 'visitor', 'approver'])
            ->latest()
            ->take(15) // Dashboard kalabalık olmasın diye limitliyoruz
            ->get();
    }

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

            IaaLog::create([
                'iaa_id' => $ziyaretPlani->iaa_id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı Onaylandı',
                'aciklama' => Auth::user()->name . " tarafından dashboard üzerinden ziyaret planı onaylandı. Tahmini Dönüş: " . Carbon::parse($this->estimatedReturnDate)->format('d.m.Y')
            ]);

            // Bildirim gönderimi (Opsiyonel: PlanVisit.php'deki gibi eklenebilir)
            
            $this->showApproveModal = false;
            $this->loadVisits();
            $this->dispatch('refreshVisitWidget');
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

            IaaLog::create([
                'iaa_id' => $ziyaretPlani->iaa_id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı ' . $status,
                'aciklama' => Auth::user()->name . " tarafından dashboard üzerinden ziyaret planına $status verildi. Sebep: " . $this->rejectionReason
            ]);

            // [YENİ] BİLDİRİMLER (Lider, Kalite, Planlayan)
            try {
                $iaa = $ziyaretPlani->iaa;
                $notifications = [
                    $iaa->atananTakim->lider ?? null, 
                    $iaa->atananTakim->bolumKaliteYoneticisi ?? null, 
                    $ziyaretPlani->planner
                ];
                $notificationClass = \App\Notifications\ZiyaretRevizyonBildirimi::class;
                foreach (array_filter($notifications) as $recipient) {
                    $recipient->notify(new $notificationClass($iaa, Auth::user()->name, $status, $this->rejectionReason));
                }
            } catch (\Exception $e) {
                Log::error('Visit Revision/Reject notification failed (Widget): ' . $e->getMessage());
            }

            $this->showRejectModal = false;
            $this->loadVisits();
            $this->dispatch('refreshVisitWidget');
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

            IaaLog::create([
                'iaa_id' => $ziyaretPlani->iaa_id,
                'user_id' => Auth::id(),
                'eylem' => 'İşlem Geri Alındı',
                'aciklama' => Auth::user()->name . " tarafından $oldStatus işlemi geri alındı."
            ]);

            $this->loadVisits();
            $this->dispatch('refreshVisitWidget');
            session()->flash('success', 'İşlem geri alındı.');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.visit-approval-widget');
    }
}
