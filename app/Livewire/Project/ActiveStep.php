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

    protected $listeners = ['visit-synced' => '$refresh'];

    public $formData = [];
    public $toolsData = [
        'five_whys' => ['why1' => '', 'why2' => '', 'why3' => '', 'why4' => '', 'why5' => ''],
        'fishbone' => ['problem' => '', 'insan' => '', 'yontem' => '', 'makine' => '', 'malzeme' => '', 'olcum' => '', 'cevre' => ''],
        'pareto' => [
            'header1' => 'Problem',
            'header2' => 'Sıklık',
            'rows' => [['problem' => '', 'frequency' => '']],
        ],
        'bar_chart_data' => [],
        'line_chart_data' => [],
        // === YENİ ANALİZ ARAÇLARI ===
        'swot' => ['strengths' => '', 'weaknesses' => '', 'opportunities' => '', 'threats' => ''],
        'action_list' => [], // ['items' => [['id' => uniqid(), 'text' => '', 'is_completed' => false]]]
        'task_list' => [],   // ['tasks' => [['id' => uniqid(), 'description' => '', 'assigned_user_id' => null]]]
        'prioritization_matrix' => [], // ['items' => [['id' => uniqid(), 'action' => '', 'effort' => 'low', 'impact' => 'high']]]
        '4m_report' => [], // [$index => ['man' => '', 'machine' => '', 'material' => '', 'method' => '']]
        // ============================
    ];

    public $newUploads = [];
    // Before/After ve özel resim yüklemeleri için yeni yapı
    public $newImageUploads = [];
    public array $initialChartData = []; // Blade için ilk veriler

    // === Bildirim Modalı Özellikleri ===
    public $isNotificationModalOpen = false;
    public $notificationDraft = '';
    public $notificationNotes = '';
    public $notifyingWidgetIndex = null;
    // ===================================

    public function mount($iaa, $assignment, $currentStep, $progressUpdate)
    {
        $this->iaa = $iaa;
        // Modelleri array'e çeviriyoruz çünkü snapshot olanlar Livewire re-hydration'da kaybolabiliyor
        // stdClass durumunda toArray() metodunun olmamasını kontrol ediyoruz
        $this->assignment = is_object($assignment) ? (method_exists($assignment, 'toArray') ? $assignment->toArray() : (array)$assignment) : $assignment;
        $this->currentStep = is_object($currentStep) ? (method_exists($currentStep, 'toArray') ? $currentStep->toArray() : (array)$currentStep) : $currentStep;
        $this->progressUpdateModel = $progressUpdate;

        // Varsayılan yapıları ve $newUploads'ı widget index'lerine göre başlat
        if (isset($this->currentStep['widgets']) && is_array($this->currentStep['widgets'])) {
            foreach ($this->currentStep['widgets'] as $index => $widget) {
                if (!isset($widget['type']))
                    continue;
                $config = $widget['config'] ?? []; // Widget tanımındaki config

                if ($widget['type'] === 'file_upload') {
                    $this->newUploads[$index] = [];
                } elseif ($widget['type'] === 'bar_chart') {
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
                } elseif ($widget['type'] === 'checklist') {
                    // Checklist maddelerini config'den parse et
                    $items = !empty($config['items']) ? array_filter(array_map('trim', explode("\n", $config['items']))) : [];
                    $this->formData[$index] = ['checklist' => array_fill(0, count($items), false)];
                } elseif ($widget['type'] === 'before_after') {
                    $this->formData[$index] = ['before_text' => '', 'after_text' => '', 'before_image_path' => null, 'after_image_path' => null, 'before_images' => [], 'after_images' => []];
                    $this->newImageUploads[$index] = ['before' => [], 'after' => []];
                } elseif ($widget['type'] === 'image_upload') {
                    $this->formData[$index] = ['files' => []]; // Çoklu desteklerse diye array
                    $this->newImageUploads[$index] = ['files' => []];
                } elseif ($widget['type'] === 'risk_matrix') {
                    $this->formData[$index] = ['risk_row' => '', 'risk_col' => '', 'risk_notes' => ''];
                } elseif ($widget['type'] === 'action_list') {
                    $this->toolsData['action_list'][$index] = ['items' => []];
                } elseif ($widget['type'] === 'task_list') {
                    $this->toolsData['task_list'][$index] = ['tasks' => []];
                } elseif ($widget['type'] === 'prioritization_matrix') {
                    $this->toolsData['prioritization_matrix'][$index] = ['items' => []];
                } elseif ($widget['type'] === '4m_report') {
                    $this->toolsData['4m_report'][$index] = ['man' => '', 'machine' => '', 'material' => '', 'method' => ''];
                }
            }
        }

        // Kayıtlı veriyi yükle (varsayılanların üzerine yazacak)
        if ($this->progressUpdateModel && !empty($this->progressUpdateModel->content)) {
            $contentData = json_decode($this->progressUpdateModel->content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->formData = $contentData['form_data'] ?? [];

                // --- ESKİ user_id VERİLERİNİ user_ids ARRAY'İNE DÖNÜŞTÜRME ---
                if (isset($this->currentStep['widgets']) && is_array($this->currentStep['widgets'])) {
                    foreach ($this->currentStep['widgets'] as $index => $widget) {
                        if (isset($widget['type']) && $widget['type'] === 'user_select') {
                            if (isset($this->formData[$index]['user_id']) && !isset($this->formData[$index]['user_ids'])) {
                                $this->formData[$index]['user_ids'] = [$this->formData[$index]['user_id']];
                            }
                        }
                    }
                }
                // -------------------------------------------------------------

                if (!empty($contentData['tools'])) {
                    $savedTools = $contentData['tools'];

                    if (!empty($savedTools['five_whys'])) {
                        $this->toolsData['five_whys'] = array_merge($this->toolsData['five_whys'], $savedTools['five_whys']);
                    }
                    if (!empty($savedTools['fishbone'])) {
                        $this->toolsData['fishbone'] = array_merge($this->toolsData['fishbone'], $savedTools['fishbone']);
                    }
                    if (!empty($savedTools['pareto'])) {
                        if (isset($savedTools['pareto']['header1'])) {
                            $this->toolsData['pareto']['header1'] = $savedTools['pareto']['header1'];
                        }
                        if (isset($savedTools['pareto']['header2'])) {
                            $this->toolsData['pareto']['header2'] = $savedTools['pareto']['header2'];
                        }
                        if (isset($savedTools['pareto']['rows']) && is_array($savedTools['pareto']['rows'])) {
                            $this->toolsData['pareto']['rows'] = $savedTools['pareto']['rows'];
                        }
                    }
                    if (!empty($savedTools['bar_chart_data'])) {
                        foreach ($savedTools['bar_chart_data'] as $idx => $data) {
                            if (isset($this->toolsData['bar_chart_data'][$idx])) {
                                $this->toolsData['bar_chart_data'][$idx] = array_merge($this->toolsData['bar_chart_data'][$idx], $data);
                            }
                        }
                    }
                    if (!empty($savedTools['line_chart_data'])) {
                        foreach ($savedTools['line_chart_data'] as $idx => $data) {
                            if (isset($this->toolsData['line_chart_data'][$idx])) {
                                $this->toolsData['line_chart_data'][$idx] = array_merge($this->toolsData['line_chart_data'][$idx], $data);
                            }
                        }
                    }
                    // === YENİ ANALİZ ARAÇLARINI YÜKLE ===
                    if (!empty($savedTools['swot'])) {
                        $this->toolsData['swot'] = array_merge($this->toolsData['swot'], $savedTools['swot']);
                    }
                    if (!empty($savedTools['action_list'])) {
                        $this->toolsData['action_list'] = $savedTools['action_list'];
                    }
                    if (!empty($savedTools['task_list'])) {
                        $this->toolsData['task_list'] = $savedTools['task_list'];
                    }
                    if (!empty($savedTools['prioritization_matrix'])) {
                        $this->toolsData['prioritization_matrix'] = $savedTools['prioritization_matrix'];
                    }
                    if (!empty($savedTools['4m_report'])) {
                        $this->toolsData['4m_report'] = $savedTools['4m_report'];
                    }
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
        if (isset($this->currentStep['widgets']) && is_array($this->currentStep['widgets'])) {
            foreach ($this->currentStep['widgets'] as $index => $widget) {
                if (!isset($widget['type']))
                    continue;
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

    public function saveDraft()
    {
        try {
            // Temizlik: user_ids içinden çıkarılan kişileri notified_users içinden de çıkaralım ki yetkileri düşsün!
            if (is_array($this->formData)) {
                foreach ($this->formData as $idx => $data) {
                    if (isset($data['user_ids']) && isset($data['notified_users'])) {
                        $currentUserIds = $data['user_ids'];
                        $this->formData[$idx]['notified_users'] = array_values(array_intersect($data['notified_users'], $currentUserIds));
                    }
                }
            }

            DB::transaction(function () {
                $widgetTypesInThisStep = isset($this->currentStep['widgets']) ? collect($this->currentStep['widgets'])->pluck('type') : collect();
                $processedFormData = is_array($this->formData) ? $this->formData : [];
                
                $toolsToSave = [];
                if ($widgetTypesInThisStep->contains('five_whys')) {
                    $toolsToSave['five_whys'] = $this->toolsData['five_whys'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('fishbone')) {
                    $toolsToSave['fishbone'] = $this->toolsData['fishbone'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('pareto')) {
                    $toolsToSave['pareto'] = $this->toolsData['pareto'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('bar_chart')) {
                    $toolsToSave['bar_chart_data'] = $this->toolsData['bar_chart_data'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('line_chart')) {
                    $toolsToSave['line_chart_data'] = $this->toolsData['line_chart_data'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('swot')) {
                    $toolsToSave['swot'] = $this->toolsData['swot'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('action_list')) {
                    $toolsToSave['action_list'] = $this->toolsData['action_list'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('task_list')) {
                    $toolsToSave['task_list'] = $this->toolsData['task_list'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('prioritization_matrix')) {
                    $toolsToSave['prioritization_matrix'] = $this->toolsData['prioritization_matrix'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('4m_report')) {
                    $toolsToSave['4m_report'] = $this->toolsData['4m_report'] ?? null;
                }

                $contentToSave = json_encode(['form_data' => $processedFormData, 'tools' => $toolsToSave]);
                
                $existing = IaaProgressUpdate::where('iaa_talep_id', $this->assignment['id'])
                    ->where('iaa_workflow_step_id', $this->currentStep['id'])
                    ->first();

                IaaProgressUpdate::updateOrCreate(
                    ['iaa_talep_id' => $this->assignment['id'], 'iaa_workflow_step_id' => $this->currentStep['id']],
                    ['user_id' => Auth::id(), 'content' => $contentToSave, 'completed_at' => $existing ? $existing->completed_at : null]
                );
            });
            
            $this->progressUpdateModel = IaaProgressUpdate::where('iaa_talep_id', $this->assignment['id'])
                    ->where('iaa_workflow_step_id', $this->currentStep['id'])
                    ->first();
        } catch (\Exception $e) {
            Log::error('Draft Save Hatası: ' . $e->getMessage());
        }
    }

    public function autosaveUserSelect($userIds = [], $index = null)
    {
        if ($index !== null) {
            $this->formData[$index]['user_ids'] = $userIds;
        }
        $this->saveDraft();
    }

    private function redirectWithMessage($iaaId, $status, $message)
    {
        return redirect()->route('proje.workspace.show', $iaaId)->with($status, $message);
    }


    // --- PARETO METODLARI ---
    public function addParetoRow()
    {
        $this->toolsData['pareto']['rows'][] = ['problem' => '', 'frequency' => ''];
    }
    public function removeParetoRow($index)
    {
        if (isset($this->toolsData['pareto']['rows'][$index]) && count($this->toolsData['pareto']['rows']) > 1) { // Son satır silinmesin
            unset($this->toolsData['pareto']['rows'][$index]);
            $this->toolsData['pareto']['rows'] = array_values($this->toolsData['pareto']['rows']);
        }
    }
    private function calculateParetoData(): Collection
    {
        $rows = $this->toolsData['pareto']['rows'] ?? [];
        $data = collect($rows)
            ->filter(fn($row) => !empty($row['problem']) && isset($row['frequency']) && is_numeric($row['frequency']) && $row['frequency'] > 0)
            ->sortByDesc('frequency')
            ->values();
        $total = $data->sum('frequency');
        $cumulative = 0;
        return $data->map(function ($item) use ($total, &$cumulative) {
            $cumulative += (float) $item['frequency'];
            $item['cumulative_sum'] = $cumulative;
            $item['cumulative_percentage'] = $total > 0 ? round(($cumulative / $total) * 100, 2) : 0;
            return $item;
        });
    }
    public function generateChartData()
    { // Sadece PARETO
        try {
            $processedData = $this->calculateParetoData();
            $chartData = [
                'labels' => $processedData->pluck('problem')->toArray(),
                'frequencies' => $processedData->pluck('frequency')->toArray(),
                'percentages' => $processedData->pluck('cumulative_percentage')->toArray(),
                'header2' => $this->toolsData['pareto']['header2'] ?? 'Sıklık',
            ];
            $this->dispatch('updateParetoChart-' . $this->getId(), $chartData);
        } catch (\Exception $e) {
            Log::error('Pareto grafik hatası: ' . $e->getMessage());
            $this->dispatch('show-error', 'Grafik hatası.');
        }
    }
    // --- PARETO BİTİŞ ---

    // === GENEL GRAFİK METODLARI ===
    private function calculateGenericChartData(string $toolKey, int $widgetIndex): Collection
    {
        $rows = $this->toolsData[$toolKey][$widgetIndex]['rows'] ?? [];
        return collect($rows)
            ->filter(fn($row) => isset($row['label']) && $row['label'] !== '' && isset($row['value']) && is_numeric($row['value']))
            ->values();
    }

    // --- Sütun Grafiği ---
    public function addBarChartRow($widgetIndex)
    {
        if (isset($this->toolsData['bar_chart_data'][$widgetIndex])) {
            $this->toolsData['bar_chart_data'][$widgetIndex]['rows'][] = ['label' => '', 'value' => ''];
        }
    }
    public function removeBarChartRow($widgetIndex, $rowIndex)
    {
        if (isset($this->toolsData['bar_chart_data'][$widgetIndex]['rows'][$rowIndex]) && count($this->toolsData['bar_chart_data'][$widgetIndex]['rows']) > 1) {
            unset($this->toolsData['bar_chart_data'][$widgetIndex]['rows'][$rowIndex]);
            $this->toolsData['bar_chart_data'][$widgetIndex]['rows'] = array_values($this->toolsData['bar_chart_data'][$widgetIndex]['rows']);
        }
    }
    // Sadece Bar Chart butonu için
    public function generateBarChartData($widgetIndex)
    {
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
            $this->dispatch('updateBarChart-' . $this->getId() . '-' . $widgetIndex, $chartData);
        } catch (\Exception $e) {
            Log::error('Sütun grafik hatası: ' . $e->getMessage());
            $this->dispatch('show-error', 'Grafik hatası.');
        }
    }

    // --- Çizgi Grafiği ---
    public function addLineChartRow($widgetIndex)
    {
        if (isset($this->toolsData['line_chart_data'][$widgetIndex])) {
            $this->toolsData['line_chart_data'][$widgetIndex]['rows'][] = ['label' => '', 'value' => ''];
        }
    }
    public function removeLineChartRow($widgetIndex, $rowIndex)
    {
        if (isset($this->toolsData['line_chart_data'][$widgetIndex]['rows'][$rowIndex]) && count($this->toolsData['line_chart_data'][$widgetIndex]['rows']) > 1) {
            unset($this->toolsData['line_chart_data'][$widgetIndex]['rows'][$rowIndex]);
            $this->toolsData['line_chart_data'][$widgetIndex]['rows'] = array_values($this->toolsData['line_chart_data'][$widgetIndex]['rows']);
        }
    }
    // Sadece Line Chart butonu için
    public function generateLineChartData($widgetIndex)
    {
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
            $this->dispatch('updateLineChart-' . $this->getId() . '-' . $widgetIndex, $chartData);
        } catch (\Exception $e) {
            Log::error('Çizgi grafik hatası: ' . $e->getMessage());
            $this->dispatch('show-error', 'Grafik hatası.');
        }
    }
    // =============================

    // === YENİ LİSTE METODLARI ===
    // Action List
    public function addActionListRow($widgetIndex)
    {
        $this->toolsData['action_list'][$widgetIndex]['items'][] = ['id' => uniqid(), 'text' => '', 'is_completed' => false];
    }
    public function removeActionListRow($widgetIndex, $itemIndex)
    {
        unset($this->toolsData['action_list'][$widgetIndex]['items'][$itemIndex]);
        $this->toolsData['action_list'][$widgetIndex]['items'] = array_values($this->toolsData['action_list'][$widgetIndex]['items']);
    }

    // Task List
    public function addTaskListRow($widgetIndex)
    {
        $this->toolsData['task_list'][$widgetIndex]['tasks'][] = ['id' => uniqid(), 'description' => '', 'assigned_user_id' => null];
    }
    public function removeTaskListRow($widgetIndex, $taskIndex)
    {
        unset($this->toolsData['task_list'][$widgetIndex]['tasks'][$taskIndex]);
        $this->toolsData['task_list'][$widgetIndex]['tasks'] = array_values($this->toolsData['task_list'][$widgetIndex]['tasks']);
    }

    // Prioritization Matrix
    public function addPrioritizationMatrixRow($widgetIndex)
    {
        $this->toolsData['prioritization_matrix'][$widgetIndex]['items'][] = ['id' => uniqid(), 'action' => '', 'effort' => 'düşük', 'impact' => 'düşük'];
    }
    public function removePrioritizationMatrixRow($widgetIndex, $itemIndex)
    {
        unset($this->toolsData['prioritization_matrix'][$widgetIndex]['items'][$itemIndex]);
        $this->toolsData['prioritization_matrix'][$widgetIndex]['items'] = array_values($this->toolsData['prioritization_matrix'][$widgetIndex]['items']);
    }
    // =============================

    public function removeNewUpload($widgetIndex, $fileKey)
    { /* ... */
        if (isset($this->newUploads[$widgetIndex][$fileKey])) {
            array_splice($this->newUploads[$widgetIndex], $fileKey, 1);
        }
    }
    public function markFileForDeletion($widgetIndex, $filePath)
    { /* ... */
        if (isset($this->formData[$widgetIndex]['files'])) {
            $this->formData[$widgetIndex]['files'] = array_values(array_filter($this->formData[$widgetIndex]['files'], fn($file) => $file !== $filePath));
        }
    }

    public function removePreviewImage($widgetIndex, $type, $index = null)
    {
        if ($type === 'before') {
            if ($index !== null && is_array($this->newImageUploads[$widgetIndex]['before'])) {
                array_splice($this->newImageUploads[$widgetIndex]['before'], $index, 1);
            } else {
                $this->newImageUploads[$widgetIndex]['before'] = [];
            }
        } elseif ($type === 'after') {
            if ($index !== null && is_array($this->newImageUploads[$widgetIndex]['after'])) {
                array_splice($this->newImageUploads[$widgetIndex]['after'], $index, 1);
            } else {
                $this->newImageUploads[$widgetIndex]['after'] = [];
            }
        } elseif ($type === 'files' && $index !== null) {
            array_splice($this->newImageUploads[$widgetIndex]['files'], $index, 1);
        }
    }

    public function removeExistingImage($widgetIndex, $type, $filePath = null)
    {
        if ($type === 'before') {
            if ($filePath !== null && isset($this->formData[$widgetIndex]['before_images'])) {
                $this->formData[$widgetIndex]['before_images'] = array_values(array_filter(
                    $this->formData[$widgetIndex]['before_images'],
                    fn($file) => $file !== $filePath
                ));
            }
            if ($filePath === null || $this->formData[$widgetIndex]['before_image_path'] === $filePath) {
                $this->formData[$widgetIndex]['before_image_path'] = null;
            }
        } elseif ($type === 'after') {
            if ($filePath !== null && isset($this->formData[$widgetIndex]['after_images'])) {
                $this->formData[$widgetIndex]['after_images'] = array_values(array_filter(
                    $this->formData[$widgetIndex]['after_images'],
                    fn($file) => $file !== $filePath
                ));
            }
            if ($filePath === null || $this->formData[$widgetIndex]['after_image_path'] === $filePath) {
                $this->formData[$widgetIndex]['after_image_path'] = null;
            }
        } elseif ($type === 'files' && $filePath !== null) {
            if (isset($this->formData[$widgetIndex]['files'])) {
                $this->formData[$widgetIndex]['files'] = array_values(array_filter(
                    $this->formData[$widgetIndex]['files'],
                    fn($file) => $file !== $filePath
                ));
            }
        }
    }

    private function storeUploadedFile($file): ?string
    { /* ... */
        if (!$file || !$file->isValid()) {
            Log::warning('Geçersiz dosya yüklendi.', ['file' => $file?->getClientOriginalName() ?? 'N/A']);
            return null;
        }
        if (!method_exists($file, 'getClientOriginalExtension')) {
            Log::warning('Dosya uzantısı alınamadı.', ['file' => $file?->getClientOriginalName() ?? 'N/A']);
            return null;
        }
        
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        
        // GÜVENLİK: Sadece İzin Verilen Uzantılar ve MIME Tipleri (Whitelist Yaklaşımı)
        $allowedExtensions = [
            // Resimler
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            // Belgeler
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf',
            // Medya
            'mp4', 'mov', 'avi', 'mp3', 'wav',
            // Arşivler
            'zip', 'rar', '7z'
        ];

        $allowedMimePrefixes = [
            'image/',
            'video/',
            'audio/',
            'text/'
        ];

        $allowedExactMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // docx
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // pptx
            'application/rtf',
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/csv'
        ];

        $isMimeAllowed = false;
        foreach ($allowedMimePrefixes as $prefix) {
            if (str_starts_with($mimeType, $prefix)) {
                $isMimeAllowed = true;
                break;
            }
        }
        if (!$isMimeAllowed && in_array($mimeType, $allowedExactMimes)) {
            $isMimeAllowed = true;
        }

        if (!in_array($extension, $allowedExtensions) || !$isMimeAllowed) {
            Log::warning('Güvenlik İhlali: Yasaklı dosya türü yüklenmek istendi.', [
                'file' => $file->getClientOriginalName(),
                'mime' => $mimeType,
                'extension' => $extension
            ]);
            // Kullanıcıya uyarı ver
            $this->dispatch('sweetalert', [
                'type' => 'error',
                'title' => 'Güvenlik İhlali',
                'text' => 'Yüklemeye çalıştığınız dosyanın içeriği güvenlik politikalarımıza aykırıdır (Çalıştırılabilir dosya tespiti).'
            ]);
            return null;
        }
        $iaaId = is_object($this->iaa) ? $this->iaa->id : (is_array($this->iaa) ? ($this->iaa['id'] ?? 'unknown_iaa') : $this->iaa);
        $stepId = is_object($this->currentStep) ? $this->currentStep->id : ($this->currentStep['id'] ?? 'unknown_step');
        
        $timestamp = now()->format('Ymd-His');
        $unique2char = strtolower(\Illuminate\Support\Str::random(2));
        $newName = "{$stepId}_{$timestamp}-{$unique2char}.{$extension}";
        
        $path = "proje/{$iaaId}";
        try {
            return $file->storeAs($path, $newName, 'public');
        } catch (\Exception $e) {
            Log::error("Dosya yüklenemedi: " . $e->getMessage(), ['path' => $path, 'newName' => $newName]);
            return null;
        }
    }


    public function save()
    {
        $iaaId = is_object($this->iaa) ? $this->iaa->id : (is_array($this->iaa) ? ($this->iaa['id'] ?? null) : $this->iaa);
        
        // Ziyaret Planı Validasyonu Kullanıcı İsteği İle Kapatıldı
        // (Ziyaret tamamlanmasa da adım kaydedilebilecek)

        try {
            DB::transaction(function () {
                $widgetTypesInThisStep = isset($this->currentStep['widgets']) ? collect($this->currentStep['widgets'])->pluck('type') : collect();
                $processedFormData = is_array($this->formData) ? $this->formData : [];
                $widgetConfigs = $this->currentStep['widgets'] ?? [];

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

                    // --- Yeni Özel Resim Yüklemeleri (Before/After & Image Upload) ---
                    if (isset($widget['type']) && $widget['type'] === 'before_after') {
                        if (!isset($processedFormData[$index]['before_images']) || !is_array($processedFormData[$index]['before_images'])) {
                            $processedFormData[$index]['before_images'] = [];
                        }
                        $existingBeforePaths = $processedFormData[$index]['before_images'];
                        $newBeforePaths = [];
                        
                        $beforeUploads = $this->newImageUploads[$index]['before'] ?? [];
                        if (!empty($beforeUploads)) {
                            if (!is_array($beforeUploads)) { $beforeUploads = [$beforeUploads]; }
                            // Maksimum 4 resim sınırı (Önce)
                            $remainingBeforeSlots = max(0, 4 - count($existingBeforePaths));
                            $beforeUploads = array_slice($beforeUploads, 0, $remainingBeforeSlots);

                            foreach ($beforeUploads as $file) {
                                $storedPath = $this->storeUploadedFile($file);
                                if ($storedPath) {
                                    $newBeforePaths[] = $storedPath;
                                }
                            }
                        }
                        $processedFormData[$index]['before_images'] = array_merge($existingBeforePaths, $newBeforePaths);

                        if (!isset($processedFormData[$index]['after_images']) || !is_array($processedFormData[$index]['after_images'])) {
                            $processedFormData[$index]['after_images'] = [];
                        }
                        $existingAfterPaths = $processedFormData[$index]['after_images'];
                        $newAfterPaths = [];
                        
                        $afterUploads = $this->newImageUploads[$index]['after'] ?? [];
                        if (!empty($afterUploads)) {
                            if (!is_array($afterUploads)) { $afterUploads = [$afterUploads]; }
                            // Maksimum 4 resim sınırı (Sonra)
                            $remainingAfterSlots = max(0, 4 - count($existingAfterPaths));
                            $afterUploads = array_slice($afterUploads, 0, $remainingAfterSlots);

                            foreach ($afterUploads as $file) {
                                $storedPath = $this->storeUploadedFile($file);
                                if ($storedPath) {
                                    $newAfterPaths[] = $storedPath;
                                }
                            }
                        }
                        $processedFormData[$index]['after_images'] = array_merge($existingAfterPaths, $newAfterPaths);

                        // Resetliyoruz
                        $this->newImageUploads[$index]['before'] = [];
                        $this->newImageUploads[$index]['after'] = [];
                    }

                    if (isset($widget['type']) && $widget['type'] === 'image_upload' && isset($this->newImageUploads[$index]['files'])) {
                        if (!isset($processedFormData[$index]['files']) || !is_array($processedFormData[$index]['files'])) {
                            $processedFormData[$index]['files'] = [];
                        }
                        $existingPaths = $processedFormData[$index]['files'];
                        $newPaths = [];
                        if (isset($this->newImageUploads[$index]['files']) && is_array($this->newImageUploads[$index]['files'])) {
                            foreach ($this->newImageUploads[$index]['files'] as $file) {
                                $storedPath = $this->storeUploadedFile($file);
                                if ($storedPath) {
                                    $newPaths[] = $storedPath;
                                }
                            }
                        }
                        $processedFormData[$index]['files'] = array_merge($existingPaths, $newPaths);
                        $this->newImageUploads[$index]['files'] = [];
                    }
                }
                // --- Dosya Yükleme Sonu ---

                // --- Araç Verilerini Kaydet ---
                $toolsToSave = [];
                if ($widgetTypesInThisStep->contains('five_whys')) {
                    $toolsToSave['five_whys'] = $this->toolsData['five_whys'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('fishbone')) {
                    $toolsToSave['fishbone'] = $this->toolsData['fishbone'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('pareto')) {
                    $toolsToSave['pareto'] = $this->toolsData['pareto'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('bar_chart')) {
                    $toolsToSave['bar_chart_data'] = $this->toolsData['bar_chart_data'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('line_chart')) {
                    $toolsToSave['line_chart_data'] = $this->toolsData['line_chart_data'] ?? null;
                }
                // === YENİ ANALİZ ARAÇLARI ===
                if ($widgetTypesInThisStep->contains('swot')) {
                    $toolsToSave['swot'] = $this->toolsData['swot'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('action_list')) {
                    $toolsToSave['action_list'] = $this->toolsData['action_list'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('task_list')) {
                    $toolsToSave['task_list'] = $this->toolsData['task_list'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('prioritization_matrix')) {
                    $toolsToSave['prioritization_matrix'] = $this->toolsData['prioritization_matrix'] ?? null;
                }
                if ($widgetTypesInThisStep->contains('4m_report')) {
                    $toolsToSave['4m_report'] = $this->toolsData['4m_report'] ?? null;
                }
                // checklist, before_after, risk_matrix, image_upload formData üzerinden kaydedilir (processedFormData)
                // --- Araç Verileri Sonu ---

                $contentToSave = json_encode(['form_data' => $processedFormData, 'tools' => $toolsToSave]);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('JSON encode hatası: ' . json_last_error_msg());
                }

                // Bildirim Gönderilecek mi kontrolü (Daha önce tamamlanmamışsa gönderilecek)
                $isFirstCompletion = !IaaProgressUpdate::where('iaa_talep_id', $this->assignment['id'])
                    ->where('iaa_workflow_step_id', $this->currentStep['id'])
                    ->whereNotNull('completed_at')
                    ->exists();

                IaaProgressUpdate::updateOrCreate(
                    ['iaa_talep_id' => $this->assignment['id'], 'iaa_workflow_step_id' => $this->currentStep['id']],
                    ['user_id' => Auth::id(), 'content' => $contentToSave, 'completed_at' => now()]
                );

                // --- PROJE DURUMU KONTROLÜ VE BİLDİRİM ---
                $assignmentModel = IaaTalep::find($this->assignment['id']);
                $iaaModel = is_object($this->iaa) ? $this->iaa : Iaa::find(is_array($this->iaa) ? ($this->iaa['id'] ?? null) : $this->iaa);
                $stepModel = IaaWorkflowStep::find($this->currentStep['id']);

                if ($assignmentModel && $iaaModel && $stepModel) {
                    
                    // Bildirim Gönderimi (Sadece ilk kez tamamlandığında)
                    if ($isFirstCompletion) {
                        try {
                            $stepService = app(\App\Services\ProjectWorkspace\ProjeAdimIslemleriService::class);
                            $stepService->notifyStakeholdersAboutProgress($iaaModel, $stepModel, $assignmentModel);
                        } catch (\Exception $e) {
                            Log::error('Livewire Adım Bildirimi Hatası: ' . $e->getMessage());
                            \App\Helpers\MailLogHelper::logFailure(
                                $iaaModel,
                                '"' . $iaaModel->baslik . '" projesinde "' . $stepModel->name . '" adımı tamamlama bildirimi gönderilemedi',
                                collect(),
                                $e->getMessage(),
                                null,
                                null,
                                $iaaModel->bolum_id
                            );
                        }
                    }

                    $totalSteps = 0;
                    if (!empty($assignmentModel->workflow_snapshot)) {
                        $snapshotData = $assignmentModel->workflow_snapshot;
                        if (is_string($snapshotData)) {
                            $snapshotData = json_decode($snapshotData, true);
                        }
                        $totalSteps = is_array($snapshotData) ? count($snapshotData) : 0;
                    } elseif ($assignmentModel->workflow) {
                        $totalSteps = $assignmentModel->workflow->steps()->count();
                    }

                    $completedSteps = IaaProgressUpdate::where('iaa_talep_id', $this->assignment['id'])->whereNotNull('completed_at')->pluck('iaa_workflow_step_id')->unique()->count();

                    if ($totalSteps > 0 && $completedSteps >= $totalSteps) {
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

            session()->flash('success', '"' . ($this->currentStep['name'] ?? 'Adım') . '" başarıyla tamamlandı!');

            $iaaId = is_object($this->iaa) ? $this->iaa->id : (is_array($this->iaa) ? ($this->iaa['id'] ?? null) : $this->iaa);

            if ($iaaId) {
                // Scroll işlemi için tamamlanan ID'yi gönderiyoruz
                return redirect()->route('proje.workspace.show', $iaaId)
                    ->with('scroll_to_step', $this->currentStep['id']);
            } else {
                Log::error('Yönlendirme için iaaId bulunamadı.');
                return redirect()->route('home');
            }

        } catch (\Exception $e) {
            Log::error('Adım kaydedilirken hata oluştu: ' . $e->getMessage(), [
                'iaa_id' => ($this->iaa instanceof Iaa) ? $this->iaa->id : ($this->iaa['id'] ?? null),
                'assignment_id' => $this->assignment['id'] ?? null,
                'step_id' => $this->currentStep['id'] ?? null,
                'user_id' => Auth::id()
            ]);
            session()->flash('error', 'Adım kaydedilirken bir hata oluştu. Lütfen tekrar deneyin.');
            return null;
        }
    }

    public function cancel()
    { /* ... (önceki gibi) ... */
        $iaaId = is_object($this->iaa) ? $this->iaa->id : (is_array($this->iaa) ? ($this->iaa['id'] ?? null) : $this->iaa);
        if ($iaaId) {
            return redirect()->route('proje.workspace.show', $iaaId);
        } else {
            Log::warning('Cancel işleminde yönlendirme için iaaId bulunamadı.');
            return redirect()->route('home');
        }
    }

    // === Bildirim Gönderme Metodları ===
    public function openNotificationModal($widgetIndex)
    {
        $this->notifyingWidgetIndex = $widgetIndex;
        $this->notificationNotes = '';
        
        $stepName = $this->currentStep['name'] ?? 'Bilinmeyen Adım';
        $iaaBaslik = is_object($this->iaa) ? $this->iaa->baslik : (is_array($this->iaa) ? ($this->iaa['baslik'] ?? '') : '');

        $this->notificationDraft = "Merhaba,\n\n'{$iaaBaslik}' projesindeki '{$stepName}' adımı için tarafınıza sorumluluk tanımlanmıştır.\nLütfen sistemi kontrol ediniz.";
        
        $this->isNotificationModalOpen = true;
    }

    public function closeNotificationModal()
    {
        $this->isNotificationModalOpen = false;
        $this->notifyingWidgetIndex = null;
        $this->notificationNotes = '';
    }

    public function sendUserSelectNotification()
    {
        $index = $this->notifyingWidgetIndex;
        if ($index === null) return;

        $selectedIds = $this->formData[$index]['user_ids'] ?? [];
        if (empty($selectedIds)) {
            session()->flash('error', 'Lütfen en az bir kullanıcı seçin.');
            return;
        }

        $usersToNotify = User::whereIn('id', $selectedIds)->get();
        if ($usersToNotify->isEmpty()) return;

        $notes = $this->notificationNotes;
        $draft = $this->notificationDraft;
        $currentUser = Auth::user();
        
        // iaModel'i bul
        $iaaModel = is_object($this->iaa) ? $this->iaa : Iaa::find(is_array($this->iaa) ? ($this->iaa['id'] ?? null) : $this->iaa);
        $stepModel = IaaWorkflowStep::find($this->currentStep['id']);

        if (!$iaaModel || !$stepModel) {
            session()->flash('error', 'Proje veya adım bilgisi eksik.');
            return;
        }

        defer(function () use ($usersToNotify, $iaaModel, $stepModel, $draft, $notes, $currentUser) {
            foreach ($usersToNotify as $user) {
                // Sorumluya ve bölüm liderine bildirim at (AdimSorumlusuAtandi benzeri ama yeni oluşturacağımız WidgetUserSelectedNotification ile)
                // Yöneticileri bul:
                $managers = collect();
                if ($user->bolum_id) {
                    $bolumLiderleri = User::role('Bölüm Lideri')->where('bolum_id', $user->bolum_id)->get();
                    $bolumLiderYardimcilari = User::role('Bölüm Lider Yardımcısı')->where('bolum_id', $user->bolum_id)->get();
                    $direktorler = User::role('Direktör')->where('bolum_id', $user->bolum_id)->get();
                    $managers = $managers->merge($bolumLiderleri)->merge($bolumLiderYardimcilari)->merge($direktorler);
                }
                
                $allRecipients = $managers->push($user)->unique('id');

                foreach ($allRecipients as $recipient) {
                    try {
                        \Illuminate\Support\Facades\Notification::send($recipient, new \App\Notifications\WidgetUserSelectedNotification(
                            $iaaModel, 
                            $stepModel, 
                            $user, // Seçilen kişi (Kimin seçildiğini belirtmek için)
                            $currentUser, // Gönderen
                            $draft,
                            $notes
                        ));
                    } catch (\Exception $e) {
                        Log::error('Widget Kullanıcı Bildirimi gönderilemedi: ' . $e->getMessage());
                    }
                }
            }
        });

        // Durumu güncelle
        if (!isset($this->formData[$index]['notified_users'])) {
            $this->formData[$index]['notified_users'] = [];
        }
        $this->formData[$index]['notified_users'] = array_unique(array_merge($this->formData[$index]['notified_users'], $selectedIds));

        if (!empty($notes)) {
            $this->formData[$index]['last_notification_note'] = $notes;
        }
        
        $this->saveDraft();

        $this->closeNotificationModal();
        session()->flash('success', 'Bildirimler arka planda gönderilmek üzere sıraya alındı ve güncel seçimler kaydedildi.');
    }
    // ===================================


    public function hasPendingVisit()
    {
        $iaaId = is_object($this->iaa) ? $this->iaa->id : (is_array($this->iaa) ? ($this->iaa['id'] ?? null) : $this->iaa);
        return \App\Models\IaaZiyaretPlani::where('iaa_id', $iaaId)
            ->where('iaa_workflow_step_id', $this->currentStep['id'])
            ->whereNotIn('status', ['Tamamlandı', 'İptal Edildi'])
            ->exists();
    }

    public function render()
    {
        $users = User::where('onaylandi_mi', true)
            ->where('is_personnel', true)
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['Superadmin', 'Yonetim']);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.project.active-step', [
            'initialChartData' => $this->initialChartData,
            'users' => $users,
            // === İŞTE ÇÖZÜM BURASI ===
            // PHP'deki '$progressUpdateModel'i, Blade'e '$progressUpdate' adıyla gönderiyoruz.
            'progressUpdate' => $this->progressUpdateModel,
            // ==========================

            'paretoProcessedData' => $this->calculateParetoData()
        ]);
    }
}
