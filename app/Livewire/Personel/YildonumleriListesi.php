<?php

namespace App\Livewire\Personel;

use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;

class YildonumleriListesi extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $type = 'today';

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
            ->whereNotNull('hire_date')
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['Müşteri Temsilcisi', 'Müşteri']);
            });

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $allUsers = $query->get();

        $anniversaries = $allUsers->map(function($user) use ($today) {
            $anniv = $user->hire_date->copy()->year($today->year);
            $years = $today->year - $user->hire_date->year;
            
            $upcomingAnniv = $anniv->copy();
            $uYears = $years;
            if ($upcomingAnniv->isBefore($today)) {
                $upcomingAnniv->addYear();
                $uYears++;
            }
            
            $pastAnniv = $anniv->copy();
            $pYears = $years;
            if ($pastAnniv->isAfter($today)) {
                $pastAnniv->subYear();
                $pYears--;
            }

            $user->upcoming_anniv = $upcomingAnniv;
            $user->upcoming_years = $uYears;
            $user->past_anniv = $pastAnniv;
            $user->past_years = $pYears;
            $user->current_anniv_years = $years;
            $user->is_today = $anniv->isToday();
            
            return $user;
        })->filter(fn($u) => ($u->is_today ? $u->current_anniv_years : $u->upcoming_years) > 0);

        // Ayarları al (Giriş menzilleri doğum günüyle ortak kullanılabilir veya yeni ayarlar eklenebilir)
        $upcomingRange = (int)(\App\Models\Setting::where('key', 'birthday_upcoming_days')->first()?->value ?? 7);
        $pastRange = (int)(\App\Models\Setting::where('key', 'birthday_past_days')->first()?->value ?? 7);

        if ($this->type == 'today') {
            $anniversaries = $anniversaries->filter(function($u) {
                return $u->is_today;
            })->sortBy('name');
        } elseif ($this->type == 'upcoming') {
            $nextWeek = $today->copy()->addDays($upcomingRange);
            $anniversaries = $anniversaries->filter(function($u) use ($today, $nextWeek) {
                return $u->upcoming_anniv->isAfter($today) && $u->upcoming_anniv->isBefore($nextWeek->copy()->addDay());
            })->sortBy(function($u) { return $u->upcoming_anniv->timestamp; });
        } elseif ($this->type == 'past') {
            $lastWeek = $today->copy()->subDays($pastRange);
            $anniversaries = $anniversaries->filter(function($u) use ($today, $lastWeek) {
                return $u->past_anniv->isBefore($today) && $u->past_anniv->isAfter($lastWeek->copy()->subDay());
            })->sortByDesc(function($u) { return $u->past_anniv->timestamp; });
        } else {
            $anniversaries = $anniversaries->sortBy(function($u) use ($today) {
                $days = $today->diffInDays($u->upcoming_anniv, false);
                return $days;
            });
        }

        // Manuel Sayfalama
        $perPage = 20;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $pagedAnniversaries = new \Illuminate\Pagination\LengthAwarePaginator(
            $anniversaries->forPage($currentPage, $perPage)->values(),
            $anniversaries->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Support\Facades\Request::url(), 'query' => \Illuminate\Support\Facades\Request::query()]
        );

        return view('livewire.personel.yildonumleri-listesi', [
            'anniversaries' => $pagedAnniversaries
        ])->layout('layouts.app');
    }
}
