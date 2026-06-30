<?php

namespace App\Livewire\Admin\Sikayetler;

use Livewire\Component;
use App\Models\MusteriSikayeti;
use Illuminate\Support\Facades\Auth;

class MusteriDigerSikayetlerModal extends Component
{
    public $customerId;
    public $currentSikayetId;
    public $isOpen = false;
    
    protected $listeners = ['openDigerSikayetlerModal' => 'openModal'];

    public function openModal($customerId, $currentSikayetId = null)
    {
        $this->customerId = $customerId;
        $this->currentSikayetId = $currentSikayetId;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $user = Auth::user();
        
        $totalCount = 0;
        $filteredCount = 0;
        $sikayetler = collect();
        $mesaj = '';

        if ($this->customerId && $this->isOpen) {
            // Include related models for display
            $baseQuery = MusteriSikayeti::where('customer_id', $this->customerId)
                            ->with(['sikayetKategori', 'cozumTakimi', 'iaaProjesi']);
                            
            $totalCount = (clone $baseQuery)->count();

            // Superadmin, Yonetim, Müşteri Şikayeti Kurulu vb. roles or Müşteri sees all
            if ($user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri', 'Müşteri Temsilcisi'])) {
                $sikayetler = $baseQuery->latest()->get();
                $filteredCount = $sikayetler->count();
                
                if ($user->hasRole(['Müşteri', 'Müşteri Temsilcisi'])) {
                    $mesaj = "Toplam {$totalCount} şikayetiniz bulunmaktadır.";
                } else {
                    $mesaj = "Toplam {$totalCount} şikayetin tümünü görüyorsunuz.";
                }
            } else {
                $allowedBolumIds = $user->getAllowedBolumIds();
                
                if ($allowedBolumIds === '*') {
                    $sikayetler = $baseQuery->latest()->get();
                    $filteredCount = $sikayetler->count();
                    $mesaj = "Toplam {$totalCount} şikayetin tümünü görüyorsunuz.";
                } else {
                    $sikayetler = $baseQuery->whereHas('sikayetKategori', function($q) use ($allowedBolumIds) {
                        $q->whereIn('bolum_id', $allowedBolumIds);
                    })->latest()->get();
                    
                    $filteredCount = $sikayetler->count();
                    
                    if ($user->hasRole('Direktör')) {
                        $mesaj = "Toplam {$totalCount} şikayetten sorumluluğunuzdaki bölümlere ait {$filteredCount} şikayeti görüyorsunuz.";
                    } elseif ($user->hasRole('Bölüm Lideri') || $user->isDepartmentDeputy()) {
                        $mesaj = "Toplam {$totalCount} şikayetten bölümünüze ait {$filteredCount} şikayeti görüyorsunuz.";
                    } elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
                        $mesaj = "Toplam {$totalCount} şikayetten sizi ilgilendiren {$filteredCount} şikayeti görüyorsunuz.";
                    } else {
                        $mesaj = "Toplam {$totalCount} şikayetten yetkili olduğunuz {$filteredCount} şikayeti görüyorsunuz.";
                    }
                }
            }
        }

        return view('livewire.admin.sikayetler.musteri-diger-sikayetler-modal', [
            'sikayetler' => $sikayetler,
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
            'mesaj' => $mesaj
        ]);
    }
}
