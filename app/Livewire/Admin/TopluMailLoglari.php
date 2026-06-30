<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BulkMailLog;

class TopluMailLoglari extends Component
{
    use WithPagination;

    public $selectedLog = null;

    public function mount()
    {
        $user = auth()->user();
        if (!$user->hasRole('Superadmin') && !$user->hasRole('Yonetim') && !$user->hasPermissionTo('toplu_mail_gonder')) {
            abort(403);
        }
    }

    public function viewDetails($logId)
    {
        $this->selectedLog = BulkMailLog::with(['recipients.user.customer', 'sender'])->findOrFail($logId);
        $this->dispatch('open-log-modal');
    }

    public function render()
    {
        $logs = BulkMailLog::with('sender')->orderByDesc('created_at')->paginate(15);

        return view('livewire.admin.toplu-mail-loglari', [
            'logs' => $logs
        ])->layout('layouts.app', ['header' => 'Toplu Mail Logları']);
    }
}
