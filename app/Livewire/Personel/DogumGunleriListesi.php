<?php

namespace App\Livewire\Personel;

use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;

class DogumGunleriListesi extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $type = 'today'; // Varsayılan artık 'today'

    public function mount()
    {
        // Yetki Kontrolü
        $user = auth()->user();
        if (!$user->is_personnel || $user->hasRole(['Müşteri Temsilcisi', 'Müşteri'])) {
            abort(403, 'Bu sayfayı görüntüleme yetkiniz yok.');
        }
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $today = now()->startOfDay();
        
        $query = User::where('is_personnel', true)
            ->whereNotNull('dogum_tarihi')
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['Müşteri Temsilcisi', 'Müşteri']);
            });

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Performans için tümünü çekmek yerine filtrelemeyi optimize etmeliyiz.
        // Ancak doğum günü filtresi (ay/gün) SQL'de karmaşık olabildiği için koleksiyon üzerinden devam edip sayfalamayı manuel yapacağız.
        $allUsers = $query->get();

        $birthdays = $allUsers->map(function($user) use ($today) {
            $bday = $user->dogum_tarihi->copy()->year($today->year);
            
            // Sıralama ve kalan gün için en yakın doğum gününü bul
            $upcomingBday = $bday->copy();
            if ($upcomingBday->isBefore($today)) $upcomingBday->addYear();
            
            $pastBday = $bday->copy();
            if ($pastBday->isAfter($today)) $pastBday->subYear();

            $user->upcoming_bday = $upcomingBday;
            $user->past_bday = $pastBday;
            $user->is_today = $bday->isToday();
            
            return $user;
        });

        // Ayarları al
        $upcomingRange = (int)(\App\Models\Setting::where('key', 'birthday_upcoming_days')->first()?->value ?? 7);
        $pastRange = (int)(\App\Models\Setting::where('key', 'birthday_past_days')->first()?->value ?? 7); // Listede biraz daha uzun olabilir ama ayar 7 ise 7 yapalım

        if ($this->type == 'today') {
            $birthdays = $birthdays->filter(function($u) {
                return $u->is_today;
            })->sortBy('name');
        } elseif ($this->type == 'upcoming') {
            // Sadece önümüzdeki X gün
            $nextWeek = $today->copy()->addDays($upcomingRange);
            $birthdays = $birthdays->filter(function($u) use ($today, $nextWeek) {
                return $u->upcoming_bday->isAfter($today) && $u->upcoming_bday->isBefore($nextWeek->copy()->addDay());
            })->sortBy(function($u) { return $u->upcoming_bday->timestamp; });
        } elseif ($this->type == 'past') {
            // Sadece geçen X gün
            $lastWeek = $today->copy()->subDays($pastRange);
            $birthdays = $birthdays->filter(function($u) use ($today, $lastWeek) {
                return $u->past_bday->isBefore($today) && $u->past_bday->isAfter($lastWeek->copy()->subDay());
            })->sortByDesc(function($u) { return $u->past_bday->timestamp; });
        } else {
            // Hepsi: En yakın olanlar önce (Yaklaşanlar + Geçmiştekiler şeklinde sıralı)
            $birthdays = $birthdays->sortBy(function($u) use ($today) {
                $days = $today->diffInDays($u->upcoming_bday, false);
                return $days;
            });
        }

        // Manuel Sayfalama
        $perPage = 20;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $pagedBirthdays = new \Illuminate\Pagination\LengthAwarePaginator(
            $birthdays->forPage($currentPage, $perPage)->values(),
            $birthdays->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Support\Facades\Request::url(), 'query' => \Illuminate\Support\Facades\Request::query()]
        );

        return view('livewire.personel.dogum-gunleri-listesi', [
            'birthdays' => $pagedBirthdays
        ])->layout('layouts.app');
    }
}
