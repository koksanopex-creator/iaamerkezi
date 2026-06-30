<?php

namespace App\Livewire\Admin;

use App\Models\ProfileComment;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ProfilYorumDenetimi extends Component
{
    use WithPagination;

    public $search = '';
    public $yazan_user_id = null;
    public $user_id = null; // Profil sahibi
    public $startDate = null;
    public $endDate = null;
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'yazan_user_id' => ['except' => null],
        'user_id' => ['except' => null],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteComment($id)
    {
        if (!auth()->user()->hasRole('Superadmin')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Bu işlem için yetkiniz yok.']);
            return;
        }

        $comment = ProfileComment::find($id);
        if ($comment) {
            // Alt yorumları da siliyoruz (DB tarafında cascade yoksa)
            $comment->cevaplar()->delete();
            $comment->delete();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Yorum ve varsa cevapları silindi.']);
        }
    }

    public function render()
    {
        $query = ProfileComment::query()
            ->with(['yazan', 'profilSahibi', 'parent'])
            ->latest();

        if ($this->search) {
            $query->where('yorum', 'like', '%' . $this->search . '%');
        }

        if ($this->yazan_user_id) {
            $query->where('yazan_user_id', $this->yazan_user_id);
        }

        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $yorumlar = $query->paginate($this->perPage);

        // İstatistikler
        $stats = [
            'total' => ProfileComment::count(),
            'today' => ProfileComment::whereDate('created_at', today())->count(),
            'active_commenters' => ProfileComment::distinct('yazan_user_id')->count(),
        ];

        return view('livewire.admin.profil-yorum-denetimi', [
            'yorumlar' => $yorumlar,
            'stats' => $stats,
            'users' => User::where('is_personnel', 1)->orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.app');
    }
}
