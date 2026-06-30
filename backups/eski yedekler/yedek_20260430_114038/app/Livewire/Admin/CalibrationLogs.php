<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DataCalibrationLog;

class CalibrationLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $type = ''; // 'veri' or 'puan'
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = DataCalibrationLog::query()
            ->with('causer')
            ->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('model', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('field', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        $logs = $query->paginate($this->perPage);

        return view('livewire.admin.calibration-logs', [
            'logs' => $logs
        ])->layout('layouts.app');
    }
}
