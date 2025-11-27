<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Iaa; 
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SikayetGorevlerim extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();

        // 1. Kalıcı Takım Üyelikleri (Müşteri Şikayeti Takımları)
        // Mevcut kodundaki o kısmı burası karşılıyor.
        $takimIdleri = $user->takimlar()
                            ->where('tur', 'sikayet')
                            ->pluck('takimlar.id');

        // 2. Squad (Geçici) Üyelikleri (Sadece onayladıkları)
        // Cihangir'in projeyi görmesini sağlayan "Sihirli Dokunuş" burası.
        $squadProjeIdleri = $user->gorevliOlduguProjeler()
                                 ->wherePivot('durum', 'onaylandi')
                                 ->pluck('iaas.id');

        // 3. SORGULAMA
        $query = Iaa::with([
            'musteriSikayeti.sikayetKategori', // Kategori adını almak için
            'atananTakim', 
            'logs' => function($q) { // Son işlemi yapanı bulmak için logları çekiyoruz
                $q->with('user')->latest()->take(1);
            }
        ])
        // Sadece bir Müşteri Şikayetine bağlı projeleri getir
        ->has('musteriSikayeti') 
        // KAPSAM: (Takımımdakiler) VEYA (Squad'ındakiler)
        ->where(function($q) use ($takimIdleri, $squadProjeIdleri) {
            $q->whereIn('atanan_takim_id', $takimIdleri)
              ->orWhereIn('id', $squadProjeIdleri);
        })
        ->whereNotIn('durum', ['Tamamlandı', 'Reddedildi', 'Havuzda', 'Onay Bekliyor']);

    $projeler = $query->latest('updated_at')->paginate(10);

        return view('livewire.sikayet-gorevlerim', [
            'projeler' => $projeler
        ]);
    }
}