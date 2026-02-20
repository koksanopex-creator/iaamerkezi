<?php

namespace App\Livewire\Project;

use Livewire\Component;
use App\Models\Iaa;
use App\Models\IaaProgressUpdate;
use App\Models\IaaTalep;
use App\Models\IaaWorkflowStep;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class ActiveStep extends Component
{
    use WithFileUploads;

    public $iaa;
    public $assignment;
    public $currentStep;
    public ?IaaProgressUpdate $progressUpdateModel = null;

    public $formData = [];
    public $toolsData = [
        'five_whys' => ['why1' => '', 'why2' => '', 'why3' => '', 'why4' => '', 'why5' => ''],
        'fishbone' => ['problem' => '', 'insan' => '', 'yontem' => '', 'makine' => '', 'malzeme' => '', 'olcum' => '', 'cevre' => ''],
        'pareto' => [
            'header1' => 'Problem',
            'header2' => 'Sıklık',
            'rows' => [['problem' => '', 'frequency' => '']],
        ],
        // === GÜNCELLENMİŞ YAPI ===
        // Config (title, axis labels) ve data (rows) bir arada tutulacak
        'bar_chart_data' => [], // Örn: [ $index => ['title' => '..', 'axis_x_label' => '..', 'axis_y_label' => '..', 'rows' => [...]] ]
        'line_chart_data' => [],
        // ========================
    ];

    public $newUploads = [];
    public array $initialChartData = []; // Blade için ilk veriler

    public function mount($iaa, $assignment, $currentStep, $progressUpdate)
    {
        $this->iaa = $iaa;
        $this->assignment = $assignment;
        $this->currentStep = $currentStep;
        $this->progressUpdateModel = $progressUpdate;

        // Varsayılan yapıları ve $newUploads'ı widget index'lerine göre başlat
        if (isset($this->currentStep->widgets) && is_array($this->currentStep->widgets)) {
            foreach ($this->currentStep->widgets as $index => $widget) {
                if (!isset($widget['type'])) continue;
                $config = $widget['config'] ?? []; // Widget tanımındaki config

                if ($widget['type'] === 'file_upload') {
                    $this->newUploads[$index] = [];
                } elseif ($widget['type'] === 'bar_chart') {
                    // Varsayılan config değerlerini widget tanımından al
                    $this->toolsData['bar_chart_data'][$index] = [
                        'title' => $config['title'] ?? 'Sütun Grafiği',
                        'axis_x_label' => $config['axis_x_label'] ?? 'Kategoriler',
                        'axis_y_label' => $config['axis_y_label'] ?? 'Değerler',
                        'rows' => [['label' => '', 'value' => '']]
                    ];
                } elseif ($widget['type'] === 'line_chart') {
                    $this->toolsData['line_chart_data'][$index] = [
                        'title' => $config['title'] ?? 'Çizgi Grafiği',
                        'axis_x_label' => $config['axis_x_label'] ?? 'Kategoriler',
                        'axis_y_label' => $config['axis_y_label'] ?? 'Değerler',
                        'rows' => [['label' => '', 'value' => '']]
                    ];
                }
            }
        }

        // Kayıtlı veriyi yükle (varsayılanların üzerine yazacak)
        if ($this->progressUpdateModel && !empty($this->progressUpdateModel->content)) {
            $contentData = json_decode($this->progressUpdateModel->content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->formData = $contentData['form_data'] ?? [];
                if (!empty($contentData['tools'])) {
                    $savedTools = $contentData['tools'];

                    if (!empty($savedTools['five_whys'])) { $this->toolsData['five_whys'] = array_merge($this->toolsData['five_whys'], $savedTools['five_whys']); }
                    if (!empty($savedTools['fishbone'])) { $this->toolsData['fishbone'] = array_merge($this->toolsData['fishbone'], $savedTools['fishbone']); }
                    if (!empty($savedTools['pareto'])) {
                        if (isset($savedTools['pareto']['header1'])) { $this->toolsData['pareto']['header1'] = $savedTools['pareto']['header1']; }
                        if (isset($savedTools['pareto']['header2'])) { $this->toolsData['pareto']['header2'] = $savedTools['pareto']['header2']; }
                        if (isset($savedTools['pareto']['rows']) && is_array($savedTools['pareto']['rows'])) { $this->toolsData['pareto']['rows'] = $savedTools['pareto']['rows']; }
                    }
                    // === YENİ GRAFİKLERİ YÜKLE (Config dahil) ===
                    if (!empty($savedTools['bar_chart_data'])) {
                         foreach($savedTools['bar_chart_data'] as $idx => $data) {
                             if (isset($this->toolsData['bar_chart_data'][$idx])) {
                                 // Kayıtlı config ve rows ile varsayılanı tamamen değiştir
                                $this->toolsData['bar_chart_data'][$idx] = array_merge($this->toolsData['bar_chart_data'][$idx], $data);
                            }
                         }
                    }
                    if (!empty($savedTools['line_chart_data'])) {
                         foreach($savedTools['line_chart_data'] as $idx => $data) {
                            if (isset($this->toolsData['line_chart_data'][$idx])) {
                                $this->toolsData['line_chart_data'][$idx] = array_merge($this->toolsData['line_chart_data'][$idx], $data);
                            }
                         }
                    }
                    // ===========================================
                }
            } else {
                 Log::error('IaaProgressUpdate content JSON decode hatası: ' . json_last_error_msg(), ['id' => $this->progressUpdateModel->id]);
            }
        }

        // === İlk grafik verilerini hesapla (Blade için) ===
        $this->prepareInitialChartData();
    }

    // İlk grafik verilerini hesaplayıp $initialChartData'ya atar
    private function prepareInitialChartData()
    {
        $this->initialChartData = [];
        if (isset($this->currentStep->widgets) && is_array($this->currentStep->widgets)) {
             foreach ($this->currentStep->widgets as $index => $widget) {
                if (!isset($widget['type'])) continue;
                $componentId = $this->getId();
                $chartKey = $componentId . '-' . $index;

                if ($widget['type'] === 'pareto') {
                    $this->initialChartData['pareto'][$componentId] = $this->calculateParetoData()->toArray();
                } elseif ($widget['type'] === 'bar_chart') {
                    $this->initialChartData['bar_chart'][$chartKey] = $this->calculateGenericChartData('bar_chart_data', $index)->toArray();
                } elseif ($widget['type'] === 'line_chart') {
                    $this->initialChartData['line_chart'][$chartKey] = $this->calculateGenericChartData('line_chart_data', $index)->toArray();
                }
            }
        }
    }


    // --- PARETO METODLARI ---
    public function addParetoRow() { $this->toolsData['pareto']['rows'][] = ['problem' => '', 'frequency' => '']; }
    public function removeParetoRow($index) {
        if (isset($this->toolsData['pareto']['rows'][$index]) && count($this->toolsData['pareto']['rows']) > 1) { // Son satır silinmesin
            unset($this->toolsData['pareto']['rows'][$index]);
            $this->toolsData['pareto']['rows'] = array_values($this->toolsData['pareto']['rows']);
        }
    }
    private function calculateParetoData(): Collection {
         $rows = $this->toolsData['pareto']['rows'] ?? [];
        $data = collect($rows)
            ->filter(fn($row) => !empty($row['problem']) && isset($row['frequency']) && is_numeric($row['frequency']) && $row['frequency'] > 0)
            ->sortByDesc('frequency')
            ->values();
        $total = $data->sum('frequency'); $cumulative = 0;
        return $data->map(function ($item) use ($total, &$cumulative) {
            $cumulative += (float)$item['frequency'];
            $item['cumulative_sum'] = $cumulative;
            $item['cumulative_percentage'] = $total > 0 ? round(($cumulative / $total) * 100, 2) : 0;
            return $item;
        });
    }
    public function generateChartData() { // Sadece PARETO
         try {
            $processedData = $this->calculateParetoData();
            $chartData = [
                'labels' => $processedData->pluck('problem')->toArray(),
                'frequencies' => $processedData->pluck('frequency')->toArray(),
                'percentages' => $processedData->pluck('cumulative_percentage')->toArray(),
                'header2' => $this->toolsData['pareto']['header2'] ?? 'Sıklık',
            ];
            $this->dispatch('updateParetoChart-'.$this->getId(), data: $chartData);
        } catch (\Exception $e) { Log::error('Pareto grafik hatası: ' . $e->getMessage()); $this->dispatch('show-error', 'Grafik hatası.'); }
    }
    // --- PARETO BİTİŞ ---

    // === GENEL GRAFİK METODLARI ===
    private function calculateGenericChartData(string $toolKey, int $widgetIndex): Collection {
         $rows = $this->toolsData[$toolKey][$widgetIndex]['rows'] ?? [];
         return collect($rows)
             ->filter(fn($row) => isset($row['label']) && $row['label'] !== '' && isset($row['value']) && is_numeric($row['value']))
             ->values();
    }

    // --- Sütun Grafiği ---
    public function addBarChartRow($widgetIndex) {
         if (isset($this->toolsData['bar_chart_data'][$widgetIndex])) {
            $this->toolsData['bar_chart_data'][$widgetIndex]['rows'][] = ['label' => '', 'value' => ''];
        }
    }
    public function removeBarChartRow($widgetIndex, $rowIndex) {
        if (isset($this->toolsData['bar_chart_data'][$widgetIndex]['rows'][$rowIndex]) && count($this->toolsData['bar_chart_data'][$widgetIndex]['rows']) > 1) {
            unset($this->toolsData['bar_chart_data'][$widgetIndex]['rows'][$rowIndex]);
            $this->toolsData['bar_chart_data'][$widgetIndex]['rows'] = array_values($this->toolsData['bar_chart_data'][$widgetIndex]['rows']);
        }
    }
    // Sadece Bar Chart butonu için
    public function generateBarChartData($widgetIndex) {
        try {
            $processedData = $this->calculateGenericChartData('bar_chart_data', $widgetIndex);
            // Config verisini $toolsData'dan al
            $config = $this->toolsData['bar_chart_data'][$widgetIndex] ?? [];
            $chartData = [
                'labels' => $processedData->pluck('label')->toArray(),
                'values' => $processedData->pluck('value')->toArray(),
                'axis_x' => $config['axis_x_label'] ?? 'Kategoriler',
                'axis_y' => $config['axis_y_label'] ?? 'Değerler',
                'title' => $config['title'] ?? 'Sütun Grafiği',
            ];
            $this->dispatch('updateBarChart-'.$this->getId().'-'.$widgetIndex, data: $chartData);
        } catch (\Exception $e) { Log::error('Sütun grafik hatası: ' . $e->getMessage()); $this->dispatch('show-error', 'Grafik hatası.'); }
    }

    // --- Çizgi Grafiği ---
    public function addLineChartRow($widgetIndex) {
        if (isset($this->toolsData['line_chart_data'][$widgetIndex])) {
            $this->toolsData['line_chart_data'][$widgetIndex]['rows'][] = ['label' => '', 'value' => ''];
        }
     }
    public function removeLineChartRow($widgetIndex, $rowIndex) {
        if (isset($this->toolsData['line_chart_data'][$widgetIndex]['rows'][$rowIndex]) && count($this->toolsData['line_chart_data'][$widgetIndex]['rows']) > 1) {
            unset($this->toolsData['line_chart_data'][$widgetIndex]['rows'][$rowIndex]);
            $this->toolsData['line_chart_data'][$widgetIndex]['rows'] = array_values($this->toolsData['line_chart_data'][$widgetIndex]['rows']);
        }
    }
     // Sadece Line Chart butonu için
     public function generateLineChartData($widgetIndex) {
        try {
            $processedData = $this->calculateGenericChartData('line_chart_data', $widgetIndex);
            $config = $this->toolsData['line_chart_data'][$widgetIndex] ?? [];
            $chartData = [
                'labels' => $processedData->pluck('label')->toArray(),
                'values' => $processedData->pluck('value')->toArray(),
                'axis_x' => $config['axis_x_label'] ?? 'Kategoriler',
                'axis_y' => $config['axis_y_label'] ?? 'Değerler',
                'title' => $config['title'] ?? 'Çizgi Grafiği',
            ];
            $this->dispatch('updateLineChart-'.$this->getId().'-'.$widgetIndex, data: $chartData);
        } catch (\Exception $e) { Log::error('Çizgi grafik hatası: ' . $e->getMessage()); $this->dispatch('show-error', 'Grafik hatası.'); }
    }
    // =============================


    public function removeNewUpload($widgetIndex, $fileKey) { /* ... */
        if (isset($this->newUploads[$widgetIndex][$fileKey])) { array_splice($this->newUploads[$widgetIndex], $fileKey, 1); }
    }
    public function markFileForDeletion($widgetIndex, $filePath) { /* ... */
        if (isset($this->formData[$widgetIndex]['files'])) { $this->formData[$widgetIndex]['files'] = array_values(array_filter( $this->formData[$widgetIndex]['files'], fn($file) => $file !== $filePath )); }
    }
    private function storeUploadedFile($file): ?string { /* ... */
         if (!$file || !$file->isValid()) { Log::warning('Geçersiz dosya yüklendi.', ['file' => $file?->getClientOriginalName() ?? 'N/A']); return null; }
         if (!method_exists($file, 'getClientOriginalExtension')) { Log::warning('Dosya uzantısı alınamadı.', ['file' => $file?->getClientOriginalName() ?? 'N/A']); return null; }
         $timestamp = now()->format('Ymd_His'); $randomStr = Str::random(5); $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
         $safeOriginalName = Str::slug($originalName); $extension = strtolower($file->getClientOriginalExtension()); $newName = "{$timestamp}_{$safeOriginalName}_{$randomStr}.{$extension}";
         $iaaId = ($this->iaa instanceof Iaa) ? $this->iaa->id : ($this->iaa['id'] ?? 'unknown_iaa');
         $stepId = ($this->currentStep instanceof IaaWorkflowStep) ? $this->currentStep->id : ($this->currentStep['id'] ?? 'unknown_step');
         $path = "project_files/{$iaaId}/step_{$stepId}";
         try { return $file->storeAs($path, $newName, 'public'); } catch (\Exception $e) { Log::error("Dosya yüklenemedi: " . $e->getMessage(), ['path' => $path, 'newName' => $newName]); return null; }
    }


    public function save()
    {
        try {
            DB::transaction(function () {
                $widgetTypesInThisStep = isset($this->currentStep->widgets) ? collect($this->currentStep->widgets)->pluck('type') : collect();
                $processedFormData = $this->formData;
                $widgetConfigs = $this->currentStep->widgets ?? [];

                // --- Dosya Yükleme ---
                foreach ($widgetConfigs as $index => $widget) {
                    if (isset($widget['type']) && $widget['type'] === 'file_upload' && isset($this->newUploads[$index])) {
                        $existingFilePaths = $processedFormData[$index]['files'] ?? [];
                        $newFilePaths = [];
                        $filesToUpload = $this->newUploads[$index];
                        if (is_array($filesToUpload)) {
                            foreach ($filesToUpload as $file) {
                                $storedPath = $this->storeUploadedFile($file);
                                if ($storedPath) {
                                    $newFilePaths[] = $storedPath;
                                } else {
                                    Log::warning('Dosya yükleme başarısız oldu, atlanıyor.', ['file' => $file?->getClientOriginalName() ?? 'N/A']);
                                }
                            }
                        }
                        $processedFormData[$index]['files'] = array_merge($existingFilePaths, $newFilePaths);
                        $this->newUploads[$index] = [];
                    }
                }
                // --- Dosya Yükleme Sonu ---

                // --- Araç Verilerini Kaydet ---
                $toolsToSave = [];
                if ($widgetTypesInThisStep->contains('five_whys')) { $toolsToSave['five_whys'] = $this->toolsData['five_whys'] ?? null; }
                if ($widgetTypesInThisStep->contains('fishbone')) { $toolsToSave['fishbone'] = $this->toolsData['fishbone'] ?? null; }
                if ($widgetTypesInThisStep->contains('pareto')) { $toolsToSave['pareto'] = $this->toolsData['pareto'] ?? null; }
                if ($widgetTypesInThisStep->contains('bar_chart')) { $toolsToSave['bar_chart_data'] = $this->toolsData['bar_chart_data'] ?? null; }
                if ($widgetTypesInThisStep->contains('line_chart')) { $toolsToSave['line_chart_data'] = $this->toolsData['line_chart_data'] ?? null; }
                // --- Araç Verileri Sonu ---

                $contentToSave = json_encode(['form_data' => $processedFormData, 'tools' => $toolsToSave]);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('JSON encode hatası: ' . json_last_error_msg());
                }

                IaaProgressUpdate::updateOrCreate(
                    ['iaa_talep_id' => $this->assignment->id, 'iaa_workflow_step_id' => $this->currentStep->id],
                    ['user_id' => Auth::id(), 'content' => $contentToSave, 'completed_at' => now()]
                );

                // --- PROJE DURUMU KONTROLÜ (GÜNCELLENDİ) ---
                $assignmentModel = IaaTalep::find($this->assignment->id);
                $iaaModel = ($this->iaa instanceof Iaa) ? $this->iaa : Iaa::find($this->iaa['id'] ?? null);

                if ($assignmentModel && $assignmentModel->workflow && $iaaModel) {
                    $totalSteps = $assignmentModel->workflow->steps()->count();
                    $completedSteps = IaaProgressUpdate::where('iaa_talep_id', $this->assignment->id)->whereNotNull('completed_at')->count();

                    if ($completedSteps >= $totalSteps) {
                        // =================================================================
                        // DİKKAT: BURADAKİ STATÜ GÜNCELLEMESİNİ İPTAL ETTİK
                        // Böylece proje "Devam Ediyor" statüsünde kalacak ve
                        // "Projeyi Tamamla / İade Gir" butonu görünecek.
                        // =================================================================
                        
                        // $iaaModel->update(['durum' => 'Bölüm Onayı Bekliyor']);  <-- BU SATIR KAPATILDI
                        // $assignmentModel->update(['status' => 'Bölüm Onayında']); <-- BU SATIR KAPATILDI

                        // İsteğe bağlı: Sadece bildirim gönderebiliriz
                        // ... (Bildirim kodu buraya gelebilir) ...
                    }
                }
            }); // Transaction sonu (}); burası hatasız olmalı)

            session()->flash('success', '"' . ($this->currentStep->name ?? 'Adım') . '" başarıyla tamamlandı!');
            
            $iaaId = ($this->iaa instanceof Iaa) ? $this->iaa->id : ($this->iaa['id'] ?? null);

            if ($iaaId) {
                // Scroll işlemi için tamamlanan ID'yi gönderiyoruz
                return redirect()->route('proje.workspace.show', $iaaId)
                    ->with('scroll_to_step', $this->currentStep->id);
            } else {
                Log::error('Yönlendirme için iaaId bulunamadı.');
                return redirect()->route('home');
            }

        } catch (\Exception $e) {
            Log::error('Adım kaydedilirken hata oluştu: ' . $e->getMessage(), [
                'iaa_id' => ($this->iaa instanceof Iaa) ? $this->iaa->id : ($this->iaa['id'] ?? null),
                'assignment_id' => $this->assignment->id ?? null,
                'step_id' => $this->currentStep->id ?? null,
                'user_id' => Auth::id()
            ]);
            session()->flash('error', 'Adım kaydedilirken bir hata oluştu. Lütfen tekrar deneyin.');
            return null;
        }
    }

    public function cancel() { /* ... (önceki gibi) ... */
        $iaaId = ($this->iaa instanceof Iaa) ? $this->iaa->id : ($this->iaa['id'] ?? null);
        if ($iaaId) { return redirect()->route('proje.workspace.show', $iaaId); }
        else { Log::warning('Cancel işleminde yönlendirme için iaaId bulunamadı.'); return redirect()->route('home'); }
    }
    

    public function render()
    {
        return view('livewire.project.active-step', [
            'initialChartData' => $this->initialChartData,
            
            // === İŞTE ÇÖZÜM BURASI ===
            // PHP'deki '$progressUpdateModel'i, Blade'e '$progressUpdate' adıyla gönderiyoruz.
            'progressUpdate' => $this->progressUpdateModel, 
            // ==========================
            
            'paretoProcessedData' => $this->calculateParetoData()
        ]);
    }
}
