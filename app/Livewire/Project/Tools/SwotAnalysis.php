<?php

namespace App\Livewire\Project\Tools;

use App\Models\IaaStepTool;
use Livewire\Component;

class SwotAnalysis extends Component
{
    public IaaStepTool $tool;
    public $canManage = false;

    // SWOT Verileri
    public $strengths = '';
    public $weaknesses = '';
    public $opportunities = '';
    public $threats = '';

    public function mount(IaaStepTool $tool, $canManage = false)
    {
        $this->tool = $tool;
        $this->canManage = $canManage;

        $data = $this->tool->data ?? [];
        $this->strengths = $data['strengths'] ?? '';
        $this->weaknesses = $data['weaknesses'] ?? '';
        $this->opportunities = $data['opportunities'] ?? '';
        $this->threats = $data['threats'] ?? '';
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
                'strengths' => $this->strengths,
                'weaknesses' => $this->weaknesses,
                'opportunities' => $this->opportunities,
                'threats' => $this->threats,
            ]
        ]);
        
        $this->dispatch('tool-saved');
    }

    public function render()
    {
        return view('livewire.project.tools.swot-analysis');
    }
}
