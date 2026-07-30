<?php

namespace App\Livewire\Project\Tools;

use App\Models\IaaStepTool;
use Livewire\Component;

class ActionListTool extends Component
{
    public IaaStepTool $tool;
    public $canManage = false;

    // Aksiyon öğeleri dizisi: [['action' => 'İş', 'owner' => 'Kişi', 'target_date' => '2023-12-31', 'status' => 'pending'], ...]
    public $items = [];

    public function mount(IaaStepTool $tool, $canManage = false)
    {
        $this->tool = $tool;
        $this->canManage = $canManage;

        $data = $this->tool->data ?? [];
        $this->items = $data['items'] ?? [];

        if (empty($this->items)) {
            $this->items = [
                ['action' => '', 'owner' => '', 'target_date' => '', 'status' => 'pending']
            ];
        }
    }

    public function addItem()
    {
        if (!$this->canManage) return;
        
        $this->items[] = ['action' => '', 'owner' => '', 'target_date' => '', 'status' => 'pending'];
        $this->saveData();
    }

    public function removeItem($index)
    {
        if (!$this->canManage || count($this->items) <= 1) return;
        
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->saveData();
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
        return view('livewire.project.tools.action-list-tool');
    }
}
