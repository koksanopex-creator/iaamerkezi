<?php

namespace App\Livewire\Project\Tools;

use App\Models\IaaStepTool;
use Livewire\Component;

class FiveWhysAnalysis extends Component
{
    public IaaStepTool $tool;
    public $canManage = false;

    public $problemStatement = '';
    public $whys = ['', '', '', '', ''];
    public $rootCause = '';

    public function mount(IaaStepTool $tool, $canManage = false)
    {
        $this->tool = $tool;
        $this->canManage = $canManage;

        $data = $this->tool->data ?? [];
        $this->problemStatement = $data['problemStatement'] ?? '';
        $this->whys = $data['whys'] ?? ['', '', '', '', ''];
        
        // Ensure it's always an array of 5
        while (count($this->whys) < 5) {
            $this->whys[] = '';
        }
        
        $this->rootCause = $data['rootCause'] ?? '';
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
                'problemStatement' => $this->problemStatement,
                'whys' => $this->whys,
                'rootCause' => $this->rootCause
            ]
        ]);
        
        $this->dispatch('tool-saved');
    }

    public function render()
    {
        return view('livewire.project.tools.five-whys-analysis');
    }
}
