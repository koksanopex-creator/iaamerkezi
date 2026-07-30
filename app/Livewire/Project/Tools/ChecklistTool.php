<?php

namespace App\Livewire\Project\Tools;

use App\Models\IaaStepTool;
use Livewire\Component;

class ChecklistTool extends Component
{
    public IaaStepTool $tool;
    public $canManage = false;

    // Checklist öğeleri dizisi: [['text' => 'Madde 1', 'checked' => false], ...]
    public $items = [];

    public function mount(IaaStepTool $tool, $canManage = false)
    {
        $this->tool = $tool;
        $this->canManage = $canManage;

        $data = $this->tool->data ?? [];
        $this->items = $data['items'] ?? [];

        if (empty($this->items)) {
            $this->items = [
                ['text' => '', 'checked' => false]
            ];
        }
    }

    public function addItem()
    {
        if (!$this->canManage) return;
        
        $this->items[] = ['text' => '', 'checked' => false];
        $this->saveData();
    }

    public function removeItem($index)
    {
        if (!$this->canManage || count($this->items) <= 1) return;
        
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->saveData();
    }

    public function toggleItem($index)
    {
        if (!$this->canManage) return;
        
        $this->items[$index]['checked'] = !($this->items[$index]['checked'] ?? false);
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
        return view('livewire.project.tools.checklist-tool');
    }
}
