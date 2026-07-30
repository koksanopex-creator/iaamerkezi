<?php

namespace App\Livewire\Project\Tools;

use App\Models\IaaStepTool;
use Livewire\Component;

class FourMReport extends Component
{
    public IaaStepTool $tool;
    public $canManage = false;

    // items format: ['man' => '...', 'machine' => '...', 'material' => '...', 'method' => '...']
    public $items = [
        'man' => '',
        'machine' => '',
        'material' => '',
        'method' => ''
    ];

    public function mount(IaaStepTool $tool, $canManage = false)
    {
        $this->tool = $tool;
        $this->canManage = $canManage;

        $data = $this->tool->data ?? [];
        $this->items = [
            'man' => $data['items']['man'] ?? '',
            'machine' => $data['items']['machine'] ?? '',
            'material' => $data['items']['material'] ?? '',
            'method' => $data['items']['method'] ?? ''
        ];
    }

    public function updated($propertyName)
    {
        if ($this->canManage) {
            $this->saveData();
        }
    }

    public function saveData()
    {
        if (!$this->canManage) return;

        $this->tool->update([
            'data' => [
                'items' => $this->items
            ]
        ]);
        
        $this->dispatch('tool-saved');
    }

    public function render()
    {
        return view('livewire.project.tools.four-m-report');
    }
}
