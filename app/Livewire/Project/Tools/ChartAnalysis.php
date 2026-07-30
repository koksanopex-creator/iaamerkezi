<?php

namespace App\Livewire\Project\Tools;

use App\Models\IaaStepTool;
use Livewire\Component;

class ChartAnalysis extends Component
{
    public IaaStepTool $tool;
    public $canManage = false;

    public $chartType = 'bar'; // bar, line, pie
    public $chartTitle = '';
    public $unit = '';
    public $xAxisTitle = '';
    
    public $labels = [''];
    public $series = []; // [['name' => 'Series 1', 'data' => [10, 20]], ...]

    public function mount(IaaStepTool $tool, $canManage = false)
    {
        $this->tool = $tool;
        $this->canManage = $canManage;

        $data = $this->tool->data ?? [];
        $this->chartType = $data['chartType'] ?? 'bar';
        $this->chartTitle = $data['chartTitle'] ?? '';
        $this->unit = $data['unit'] ?? '';
        $this->xAxisTitle = $data['xAxisTitle'] ?? '';
        
        $this->labels = $data['labels'] ?? ['Kategori 1', 'Kategori 2', 'Kategori 3'];
        
        if (isset($data['series'])) {
            $this->series = $data['series'];
        } elseif (isset($data['values'])) {
            $this->series = [
                ['name' => 'Değer', 'data' => $data['values']]
            ];
        } else {
            $this->series = [
                ['name' => 'Seri 1', 'data' => [10, 20, 15]]
            ];
        }

        if (empty($this->labels)) {
            $this->labels = [''];
            $this->series = [
                ['name' => 'Seri 1', 'data' => ['']]
            ];
        }
    }

    public function addDataRow()
    {
        if (!$this->canManage) return;
        
        $this->labels[] = '';
        foreach ($this->series as $i => $s) {
            $this->series[$i]['data'][] = '';
        }
        $this->saveData();
    }

    public function removeDataRow($index)
    {
        if (!$this->canManage || count($this->labels) <= 1) return;
        
        unset($this->labels[$index]);
        $this->labels = array_values($this->labels);
        
        foreach ($this->series as $i => $s) {
            unset($this->series[$i]['data'][$index]);
            $this->series[$i]['data'] = array_values($this->series[$i]['data']);
        }
        
        $this->saveData();
    }

    public function addSeries()
    {
        if (!$this->canManage) return;
        
        $newSeriesName = 'Seri ' . (count($this->series) + 1);
        $newData = array_fill(0, count($this->labels), '');
        
        $this->series[] = [
            'name' => $newSeriesName,
            'data' => $newData
        ];
        
        $this->saveData();
    }
    
    public function removeSeries($index)
    {
        if (!$this->canManage || count($this->series) <= 1) return;
        
        unset($this->series[$index]);
        $this->series = array_values($this->series);
        
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

        $sanitizedSeries = [];
        foreach ($this->series as $s) {
            $numericData = array_map(function($val) {
                return is_numeric($val) ? (float)$val : 0;
            }, $s['data'] ?? []);
            
            $sanitizedSeries[] = [
                'name' => $s['name'] ?: 'İsimsiz',
                'data' => $numericData
            ];
        }

        $this->tool->update([
            'data' => [
                'chartType' => $this->chartType,
                'chartTitle' => $this->chartTitle,
                'unit' => $this->unit,
                'xAxisTitle' => $this->xAxisTitle,
                'labels' => $this->labels,
                'series' => $sanitizedSeries
            ]
        ]);
        
        $this->dispatch('tool-saved');
        
        $this->dispatch('update-chart-' . $this->tool->id, [
            'type' => $this->chartType,
            'unit' => $this->unit,
            'xAxisTitle' => $this->xAxisTitle,
            'labels' => $this->labels,
            'series' => $sanitizedSeries
        ]);
    }

    public function render()
    {
        return view('livewire.project.tools.chart-analysis');
    }
}
