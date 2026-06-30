<?php

namespace App\Livewire\Admin;

use App\Models\Bolum;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TakvimMapping extends Component
{
    public $takvimBusinessUnits = [];
    public $mappings = []; // bolum_id => takvim_business_unit_id

    public function mount()
    {
        $this->fetchTakvimBusinessUnits();
        $this->loadCurrentMappings();
    }

    public function fetchTakvimBusinessUnits()
    {
        try {
            $takvimUrl = config('services.takvim.url');
            $response = Http::get($takvimUrl . '/api/business-units');

            if ($response->successful()) {
                $this->takvimBusinessUnits = $response->json();
            } else {
                session()->flash('error', 'Takvim uygulamasından birimler çekilemedi.');
            }
        } catch (\Exception $e) {
            Log::error('Takvim Business Units fetch error: ' . $e->getMessage());
            session()->flash('error', 'Takvim bağlantı hatası.');
        }
    }

    public function loadCurrentMappings()
    {
        $bolumler = Bolum::all();
        foreach ($bolumler as $bolum) {
            $this->mappings[$bolum->id] = $bolum->takvim_business_unit_id ?: '';
        }
    }

    public function saveMappings()
    {
        try {
            foreach ($this->mappings as $bolumId => $buId) {
                Bolum::where('id', $bolumId)->update([
                    'takvim_business_unit_id' => $buId ?: null
                ]);
            }

            session()->flash('message', 'Eşleştirmeler başarıyla kaydedildi.');
        } catch (\Exception $e) {
            Log::error('Mapping save error: ' . $e->getMessage());
            session()->flash('error', 'Kaydedilirken bir hata oluştu.');
        }
    }

    public function render()
    {
        return view('livewire.admin.takvim-mapping', [
            'bolumler' => Bolum::orderBy('ad')->get()
        ])->layout('layouts.app');
    }
}
