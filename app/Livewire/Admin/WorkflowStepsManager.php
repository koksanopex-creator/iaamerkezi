<?php

// app/Livewire/Admin/WorkflowStepsManager.php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\IaaWorkflow;
use App\Models\IaaWorkflowStep;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkflowStepsManager extends Component
{
    public IaaWorkflow $workflow;
    public $steps;

    // Yeni adım için form alanları
    public $name;
    public $description;
    public $default_duration_days = 5;

    // Widget Yapılandırması
    public $widgets = [];
    public $availableWidgets = [ // Kullanılabilir widget türleri
        'textbox' => ['label' => 'Metin Alanı', 'options' => ['title' => 'Başlık', 'required' => 'Zorunlu mu? (checkbox)']],
        'five_whys' => ['label' => '5 Neden Analizi', 'options' => []],
        'fishbone' => ['label' => 'Balık Kılçığı', 'options' => []],
        'pareto' => ['label' => 'Pareto Analizi', 'options' => []],
        'user_select' => ['label' => 'Kullanıcı Seçimi (Sorumluluk Atar)', 'options' => ['title' => 'Başlık']],
        'user_select_info' => ['label' => 'Kullanıcı Seçimi (Sadece Bilgi Amaçlı)', 'options' => ['title' => 'Başlık']],
        'date_picker' => ['label' => 'Tarih Seçici', 'options' => ['title' => 'Başlık']],
        'file_upload' => ['label' => 'Dosya Yükleme', 'options' => ['title' => 'Başlık', 'multiple' => 'Çoklu Dosya? (checkbox)']],
        'info_text' => ['label' => 'Bilgi Metni', 'options' => ['title' => 'Başlık', 'content' => 'İçerik (textarea)']],
        // === GRAFİK TÜRLERİ ===
        'bar_chart' => ['label' => 'Sütun Grafiği (Dinamik)', 'options' => ['title' => 'Grafik Başlığı', 'axis_x_label' => 'X Ekseni Başlığı', 'axis_y_label' => 'Y Ekseni Başlığı']],
        'line_chart' => ['label' => 'Çizgi Grafiği (Dinamik)', 'options' => ['title' => 'Grafik Başlığı', 'axis_x_label' => 'X Ekseni Başlığı', 'axis_y_label' => 'Y Ekseni Başlığı']],
        // === YENİ ANALİZ ARAÇLARI ===
        'swot' => ['label' => 'SWOT Analizi', 'options' => ['title' => 'Başlık']],
        'checklist' => ['label' => 'Kontrol Listesi', 'options' => ['title' => 'Başlık', 'items' => 'Maddeler (her satır bir madde) (textarea)']],
        'before_after' => ['label' => 'Önce/Sonra Karşılaştırma', 'options' => ['title' => 'Başlık']],
        'risk_matrix' => ['label' => 'Risk Matrisi', 'options' => ['title' => 'Başlık', 'size' => 'Matris Boyutu (3 veya 5)']],
        'image_upload' => ['label' => 'Açıklamalı Resim Yükleme', 'options' => ['title' => 'Başlık', 'description' => 'Açıklama', 'multiple' => 'Çoklu Resim? (checkbox)']],
        'action_list' => ['label' => 'Aksiyon Listesi', 'options' => ['title' => 'Başlık']],
        'task_list' => ['label' => 'Görev Listesi (Kişi Atamalı)', 'options' => ['title' => 'Başlık']],
        'prioritization_matrix' => ['label' => 'Önceliklendirme Matrisi (Efor/Etki)', 'options' => ['title' => 'Başlık']],
        '4m_report' => ['label' => '4M Gelişim Raporu (İnsan, Makine, Malzeme, Metot)', 'options' => ['title' => 'Rapor Başlığı']],
        // ===========================
    ];
    public $selectedWidgetType = 'textbox';

    // Düzenleme için özellikler
    public $editingStepId = null;
    public $editingName;
    public $editingDescription;
    public $editingDuration;


    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_duration_days' => 'required|integer|min:1',
            'widgets' => 'nullable|array',
            'widgets.*.type' => ['required', Rule::in(array_keys($this->availableWidgets))],
            'widgets.*.config' => 'nullable|array',
            'widgets.*.config.title' => 'nullable|string|max:255',
            'widgets.*.config.required' => 'nullable|boolean', // Textbox için
            'widgets.*.config.multiple' => 'nullable|boolean', // FileUpload için
            'widgets.*.config.content' => 'nullable|string', // InfoText için
            // === YENİ GRAFİK VALİDASYONLARI ===
            'widgets.*.config.axis_x_label' => 'nullable|string|max:100',
            'widgets.*.config.axis_y_label' => 'nullable|string|max:100',
            // === YENİ ANALİZ ARAÇLARI VALİDASYONLARI ===
            'widgets.*.config.items' => 'nullable|string', // Checklist maddeleri
            'widgets.*.config.size' => 'nullable|string|in:3,5', // Risk Matrisi boyutu
            'widgets.*.config.description' => 'nullable|string', // Açıklamalı Resim Yükleme
            'widgets.*.config.4m_report_title' => 'nullable|string|max:255', // 4M Raporu başlığı için opsiyonel
            // ==================================
        ];
    }

    protected function editingRules()
    {
        // rules() ile aynı validasyonları kullanabiliriz, sadece input adları farklı
        $rules = $this->rules();
        $rules['editingName'] = $rules['name'];
        $rules['editingDescription'] = $rules['description'];
        $rules['editingDuration'] = $rules['default_duration_days'];
        unset($rules['name'], $rules['description'], $rules['default_duration_days']);
        return $rules;
    }


    public function mount(IaaWorkflow $workflow)
    {
        $this->workflow = $workflow;
        $this->loadSteps();
    }

    public function loadSteps()
    {
        $this->steps = $this->workflow->steps()->orderBy('order')->get();
    }

    public function addStep()
    {
        $validated = $this->validate($this->rules());
        $lastOrder = $this->workflow->steps()->max('order') ?? 0;

        $this->workflow->steps()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'default_duration_days' => $validated['default_duration_days'],
            'widgets' => $this->prepareWidgetsForSave($validated['widgets'] ?? []), // Config'i temizle ve boolean'ları düzelt
            'order' => $lastOrder + 1,
        ]);

        session()->flash('success', 'Yeni adım başarıyla eklendi.');
        $this->resetForm();
        $this->loadSteps();
    }

    public function updateStepOrder($list)
    {
        foreach ($list as $item) {
            IaaWorkflowStep::find($item['value'])->update(['order' => $item['order']]);
        }
        $this->loadSteps();
        session()->flash('success', 'Adım sırası güncellendi.');
    }

    public function editStep($stepId)
    {
        $step = IaaWorkflowStep::find($stepId);
        if ($step) {
            $this->editingStepId = $step->id;
            $this->editingName = $step->name;
            $this->editingDescription = $step->description;
            $this->editingDuration = $step->default_duration_days;
            // Boolean'ları checkbox'lar için string'e çevirerek yükle
            $this->widgets = $this->prepareWidgetsForEdit($step->widgets ?? []);
        }
    }

    public function updateStep()
    {
        $validated = $this->validate($this->editingRules());
        $step = IaaWorkflowStep::find($this->editingStepId);
        if ($step) {
            $step->update([
                'name' => $validated['editingName'],
                'description' => $validated['editingDescription'],
                'default_duration_days' => $validated['editingDuration'],
                'widgets' => $this->prepareWidgetsForSave($validated['widgets'] ?? []), // Config'i temizle ve boolean'ları düzelt
            ]);
        }
        session()->flash('success', 'Adım başarıyla güncellendi.');
        $this->cancelEdit();
        $this->loadSteps();
    }

    public function cancelEdit()
    {
        $this->reset('editingStepId', 'editingName', 'editingDescription', 'editingDuration', 'widgets');
    }

    public function resetForm()
    {
        $this->reset('name', 'description', 'widgets');
        $this->default_duration_days = 5;
    }

    // === WIDGET METODLARI ===
    public function addWidget()
    {
        $this->widgets[] = [
            'type' => $this->selectedWidgetType,
            'config' => $this->getDefaultWidgetConfig($this->selectedWidgetType)
        ];
    }

    public function removeWidget($index)
    {
        unset($this->widgets[$index]);
        $this->widgets = array_values($this->widgets);
    }

    public function updateWidgetOrder($list)
    {
        usort($list, fn($a, $b) => $a['order'] <=> $b['order']);
        $orderedWidgets = [];
        foreach ($list as $item) {
            if (isset($this->widgets[$item['value']])) {
                $orderedWidgets[] = $this->widgets[$item['value']];
            } else {
                Log::warning("updateWidgetOrder: Eski index {$item['value']} bulunamadı.");
            }
        }
        $this->widgets = $orderedWidgets;
    }

    // --- WIDGET SIRALAMA (OK TUŞLARI) ---
    public function moveWidgetUp(int $index): void
    {
        if ($index <= 0 || !isset($this->widgets[$index]))
            return;

        $prev = $index - 1;
        [$this->widgets[$prev], $this->widgets[$index]] = [$this->widgets[$index], $this->widgets[$prev]];
        // indexleri 0,1,2... olarak yeniden sırala
        $this->widgets = array_values($this->widgets);
    }

    public function moveWidgetDown(int $index): void
    {
        if (!isset($this->widgets[$index]) || !isset($this->widgets[$index + 1]))
            return;

        $next = $index + 1;
        [$this->widgets[$next], $this->widgets[$index]] = [$this->widgets[$index], $this->widgets[$next]];
        $this->widgets = array_values($this->widgets);
    }




    // Yardımcı metodlar
    private function getDefaultWidgetConfig($type)
    {
        $defaultConfig = [];
        if (isset($this->availableWidgets[$type]['options'])) {
            foreach ($this->availableWidgets[$type]['options'] as $key => $label) {
                // Checkbox ise null yap (işaretlenmemiş), değilse boş string ata
                $defaultConfig[$key] = (str_contains(strtolower($label), '(checkbox)')) ? null : '';
            }
        }
        return $defaultConfig;
    }

    // Kaydetmeden önce config'deki boolean değerleri düzelt
    private function prepareWidgetsForSave(array $widgets): array
    {
        return array_map(function ($widget) {
            if (isset($widget['config']) && is_array($widget['config'])) {
                foreach ($widget['config'] as $key => &$value) {
                    // Checkbox'lar için boolean'a çevir ('true' -> true, null/'' -> false)
                    if (
                        isset($this->availableWidgets[$widget['type']]['options'][$key]) &&
                        str_contains(strtolower($this->availableWidgets[$widget['type']]['options'][$key]), '(checkbox)')
                    ) {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    }
                }
            }
            // Grafik widget'larının config'inden 'rows' verisini kaldır (bu toolsData'da tutulacak)
            if (isset($widget['config']['rows']) && in_array($widget['type'], ['bar_chart', 'line_chart', 'pareto'])) {
                unset($widget['config']['rows']);
            }
            return $widget;
        }, $widgets);
    }

    // Düzenleme için yüklerken checkbox boolean'larını string'e çevir
    private function prepareWidgetsForEdit(array $widgets): array
    {
        return array_map(function ($widget) {
            if (isset($widget['config']) && is_array($widget['config'])) {
                foreach ($widget['config'] as $key => &$value) {
                    if (
                        isset($this->availableWidgets[$widget['type']]['options'][$key]) &&
                        str_contains(strtolower($this->availableWidgets[$widget['type']]['options'][$key]), '(checkbox)')
                    ) {
                        // Sadece true ise 'true' string yap, false ise null bırak
                        $value = ($value === true) ? 'true' : null;
                    }
                }
            }
            return $widget;
        }, $widgets);
    }
    // =============================

    public function deleteStep($stepId)
    {
        // ... (Silme metodu aynı kalabilir) ...
        DB::beginTransaction();
        try {
            $step = IaaWorkflowStep::find($stepId);
            if ($step) {
                $workflowId = $step->iaa_workflow_id;
                $deletedOrder = $step->order;
                $step->delete();
                IaaWorkflowStep::where('iaa_workflow_id', $workflowId)->where('order', '>', $deletedOrder)->decrement('order');
                DB::commit();
                session()->flash('success', 'Adım başarıyla silindi.');
                $this->loadSteps();
            } else {
                DB::rollBack();
                session()->flash('error', 'Silinecek adım bulunamadı.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Adım silinirken hata: ' . $e->getMessage());
            session()->flash('error', 'Adım silinirken bir hata oluştu.');
        }
    }

    public function render()
    {
        return view('livewire.admin.workflow-steps-manager');
    }
}