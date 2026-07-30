<?php

namespace App\Livewire\Project\Tools;

use App\Models\IaaStepTool;
use Livewire\Component;

class ParetoAnalysis extends Component
{
    public IaaStepTool $tool;
    public $canManage = false;

    // items format: [['category' => 'Hata A', 'frequency' => 10], ...]
    public $items = [];

    public function mount(IaaStepTool $tool, $canManage = false)
    {
        $this->tool = $tool;
        $this->canManage = $canManage;

        $data = $this->tool->data ?? [];
        $this->items = $data['items'] ?? [];

        if (empty($this->items)) {
            $this->items = [
                ['category' => '', 'frequency' => '']
            ];
        }
    }

    public function addItem()
    {
        if (!$this->canManage) return;
        
        $this->items[] = ['category' => '', 'frequency' => ''];
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

        // Sayısal değerleri cast et
        foreach ($this->items as &$item) {
            $item['frequency'] = $item['frequency'] !== '' ? (float)$item['frequency'] : '';
        }

        $this->tool->update([
            'data' => [
                'items' => $this->items
            ]
        ]);
        
        $this->dispatch('tool-saved');
        
        // Hesaplamaları yapıp event ile gönder
        $sortedItems = collect($this->items)
            ->filter(fn($i) => !empty($i['category']) && is_numeric($i['frequency']))
            ->sortByDesc('frequency')
            ->values()
            ->toArray();
            
        $totalFrequency = array_sum(array_column($sortedItems, 'frequency'));
        $cumulativeItems = [];
        $cumulative = 0;
        
        foreach ($sortedItems as $item) {
            $cumulative += $item['frequency'];
            $cumulativePercentage = $totalFrequency > 0 ? round(($cumulative / $totalFrequency) * 100, 1) : 0;
            
            $cumulativeItems[] = [
                'category' => $item['category'],
                'frequency' => $item['frequency'],
                'cumulative_percentage' => $cumulativePercentage
            ];
        }

        $this->dispatch('pareto-data-updated-' . $this->tool->id, $cumulativeItems);
    }

    public function render()
    {
        $sortedItems = collect($this->items)
            ->filter(fn($i) => !empty($i['category']) && is_numeric($i['frequency']))
            ->sortByDesc('frequency')
            ->values()
            ->toArray();
            
        $totalFrequency = array_sum(array_column($sortedItems, 'frequency'));
        $cumulativeItems = [];
        $cumulative = 0;
        
        foreach ($sortedItems as $item) {
            $cumulative += $item['frequency'];
            $cumulativePercentage = $totalFrequency > 0 ? round(($cumulative / $totalFrequency) * 100, 1) : 0;
            
            $cumulativeItems[] = [
                'category' => $item['category'],
                'frequency' => $item['frequency'],
                'cumulative_percentage' => $cumulativePercentage
            ];
        }

        return view('livewire.project.tools.pareto-analysis', [
            'analyzedData' => $cumulativeItems,
            'totalFrequency' => $totalFrequency
        ]);
    }
}
