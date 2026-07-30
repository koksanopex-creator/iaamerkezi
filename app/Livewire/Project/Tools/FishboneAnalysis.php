<?php

namespace App\Livewire\Project\Tools;

use App\Models\IaaStepTool;
use Livewire\Component;

class FishboneAnalysis extends Component
{
    public IaaStepTool $tool;
    public $canManage = false;

    public $problem_statement = '';
    
    // ['insan' => ['Neden 1', 'Neden 2'], 'makine' => [], ...]
    public $categories = [
        'insan' => [],
        'makine' => [],
        'malzeme' => [],
        'metot' => [],
        'olcum' => [],
        'cevre' => []
    ];

    public $newItems = [
        'insan' => '',
        'makine' => '',
        'malzeme' => '',
        'metot' => '',
        'olcum' => '',
        'cevre' => ''
    ];

    public function mount(IaaStepTool $tool, $canManage = false)
    {
        $this->tool = $tool;
        $this->canManage = $canManage;

        $data = $this->tool->data ?? [];
        $this->problem_statement = $data['problem_statement'] ?? '';
        
        foreach (array_keys($this->categories) as $key) {
            $this->categories[$key] = $data['categories'][$key] ?? [];
        }
    }

    public function addItem($category)
    {
        if (!$this->canManage || empty(trim($this->newItems[$category] ?? ''))) return;
        
        $this->categories[$category][] = trim($this->newItems[$category]);
        $this->newItems[$category] = '';
        $this->saveData();
    }

    public function removeItem($category, $index)
    {
        if (!$this->canManage) return;
        
        unset($this->categories[$category][$index]);
        $this->categories[$category] = array_values($this->categories[$category]);
        $this->saveData();
    }

    public function updatedProblemStatement()
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
                'problem_statement' => $this->problem_statement,
                'categories' => $this->categories
            ]
        ]);
        
        $this->dispatch('tool-saved');
    }

    public function render()
    {
        return view('livewire.project.tools.fishbone-analysis');
    }
}
