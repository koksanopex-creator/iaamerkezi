<?php

namespace App\Livewire; // 'Admin' namespace'i kaldırıldı

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Iaa; // Projeleri (şikayetler) çekmek için
use Livewire\WithPagination;
use Livewire\Attributes\Layout; // Layout'u belirtmek için

// Bu component'in ana layout dosyasını (app.blade.php) kullanmasını sağlar
#[Layout('layouts.app')]
class SikayetGorevlerim extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();

        // 1. Kullanıcının üye olduğu 'sikayet' takımlarının ID'lerini al
        $sikayetTakimIds = $user->takimlar()
                                ->where('tur', 'sikayet')
                                // === DÜZELTME BURADA ===
                             // 'id' yerine 'takimlar.id' yazarak hangi id olduğunu belirtiyoruz
                             ->pluck('takimlar.id');
                             // === DÜZELTME SONU ===

        // 2. Bu takımlara atanmış ve 'Atandı' (yani işlemde) olan projeleri (iaas) al
        $query = Iaa::whereIn('atanan_takim_id', $sikayetTakimIds)
                     ->where('durum', 'Atandı'); // Sadece devam edenler
        
        $projeler = $query->with('musteriSikayeti') // Orijinal şikayet detayları için
                          ->latest('onaylanma_tarihi')
                          ->paginate(10);

        return view('livewire.sikayet-gorevlerim', [
            'projeler' => $projeler
        ]);
    }
}