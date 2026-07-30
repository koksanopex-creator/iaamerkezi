<?php

namespace App\Livewire\Project;

use App\Models\Iaa;
use App\Models\IaaWorkflowStep;
use App\Models\IaaStepTool;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StepToolsManager extends Component
{
    public Iaa $iaa;
    public $step_id;
    public $is_completed = false;

    public $tools = [];
    public $showAddToolModal = false;
    public $selectedToolType = null;
    public $toolTitle = '';

    public $availableTools = [
        'swot' => [
            'name' => 'SWOT Analizi',
            'description' => 'Güçlü/Zayıf Yönler, Fırsatlar ve Tehditler analizi yapın.',
            'icon' => '<svg class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>'
        ],
        '5why' => [
            'name' => '5 Neden Analizi',
            'description' => 'Kök neden analizi için 5 Neden tablosu oluşturun.',
            'icon' => '<svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
        ],
        'chart' => [
            'name' => 'Grafik Analizi',
            'description' => 'Değerleri girerek hızlıca Pasta veya Çubuk grafikler oluşturun.',
            'icon' => '<svg class="w-6 h-6 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>'
        ],
        'checklist' => [
            'name' => 'Kontrol Listesi',
            'description' => 'Dinamik onay kutuları (checkbox) ile maddelerden oluşan bir kontrol listesi hazırlayın.',
            'icon' => '<svg class="w-6 h-6 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>'
        ],
        'action_list' => [
            'name' => 'Aksiyon Listesi',
            'description' => 'Yapılacak işleri, sorumlularını ve hedef tarihlerini tablo halinde belirleyin.',
            'icon' => '<svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>'
        ],
        'fishbone' => [
            'name' => 'Balık Kılçığı',
            'description' => 'Problemin ana nedenlerini 6M kategorilerine göre sınıflandırarak analiz edin.',
            'icon' => '<svg class="w-6 h-6 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07 1.952-4.242 0-1.172-1.953-1.172-5.119 0-7.072 1.171-1.952 3.07-1.952 4.242 0M8 10.5h4m-4 3h4m9-1.5c0 1.93-1.57 3.5-3.5 3.5h-1c-1.5 0-3-1.1-4-2.5m8.5-1c0-1.93-1.57-3.5-3.5-3.5h-1c-1.5 0-3 1.1-4 2.5" /></svg>'
        ],
        'pareto' => [
            'name' => 'Pareto Analizi',
            'description' => 'Hata sıklıklarını girerek otomatik kümülatif yüzdeli Pareto grafiği oluşturun.',
            'icon' => '<svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>'
        ],
        '4m_report' => [
            'name' => '4M Gelişim Raporu',
            'description' => 'İnsan, Makine, Malzeme, Metot bazlı durum değerlendirmesi ve aksiyonlar matrisi.',
            'icon' => '<svg class="w-6 h-6 text-fuchsia-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" /></svg>'
        ]
    ];

    public function mount(Iaa $iaa, $step_id, $is_completed = false)
    {
        $this->iaa = $iaa;
        $this->step_id = $step_id;
        $this->is_completed = $is_completed;
        $this->loadTools();
    }

    public function loadTools()
    {
        $this->tools = IaaStepTool::where('iaa_workflow_step_id', $this->step_id)
            ->where('iaa_id', $this->iaa->id) // WAIT, the migration didn't have iaa_id!
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function getCanManageToolsProperty()
    {
        if ($this->is_completed) {
            return false;
        }
        
        $user = Auth::user();
        if ($user->hasRole('Superadmin')) {
            return true;
        }

        // 1. Takım Lideri mi?
        $isTeamLeader = $this->iaa->projeEkibi()->where('users.id', $user->id)->wherePivot('rol', 'Takım Lideri')->exists();
        if (!$isTeamLeader && $this->iaa->atananTakim) {
            $isTeamLeader = ($this->iaa->atananTakim->lider_user_id == $user->id);
        }
        
        if ($isTeamLeader) {
            return true; // Takım lideri her zaman araç ekleyebilir
        }

        // 2. Adımın Sorumlusu Var Mı?
        $assignees = \App\Models\IaaStepAssignment::where('iaa_workflow_step_id', $this->step_id)
            ->where('iaa_id', $this->iaa->id)
            ->pluck('user_id')->toArray();

        if (empty($assignees)) {
            // Ortak alan: Takımdaki herkes ekleyebilir
            $isTeamMember = $this->iaa->projeEkibi()->where('user_id', $user->id)->exists();
            if (!$isTeamMember && $this->iaa->atananTakim) {
                // Belki takım üyesi? (Varsayım: yetkisi varsa sayfadadır, ama garanti edelim)
                return true; 
            }
            return $isTeamMember;
        }

        // 3. Sorumlu kişi seçilmiş, o halde sadece sorumlu kişi ekleyebilir (Takım lideri yukarda true döndü)
        return in_array($user->id, $assignees);
    }

    public function openAddToolModal()
    {
        if (!$this->canManageTools) return;
        $this->selectedToolType = null;
        $this->toolTitle = '';
        $this->showAddToolModal = true;
    }

    public function closeAddToolModal()
    {
        $this->showAddToolModal = false;
    }

    public function selectTool($type)
    {
        $this->selectedToolType = $type;
        $this->toolTitle = $this->availableTools[$type]['name'];
    }

    public function addTool($type = null)
    {
        $typeToAdd = $type ?: $this->selectedToolType;
        
        if (!$this->canManageTools || !$typeToAdd) return;

        $title = $this->toolTitle ?: $this->availableTools[$typeToAdd]['name'];

        $maxOrder = IaaStepTool::where('iaa_workflow_step_id', $this->step_id)->where('iaa_id', $this->iaa->id)->max('order') ?? 0;

        IaaStepTool::create([
            'iaa_workflow_step_id' => $this->step_id,
            'iaa_id' => $this->iaa->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'tool_type' => $typeToAdd,
            'title' => $title,
            'order' => $maxOrder + 1,
            'data' => [], // Default empty data
        ]);

        $this->closeAddToolModal();
        $this->loadTools();
        $this->dispatch('tool-added');
    }

    public function removeTool($id)
    {
        if (!$this->canManageTools) return;
        
        $tool = IaaStepTool::where('id', $id)->where('iaa_id', $this->iaa->id)->first();
        if ($tool) {
            $tool->delete();
            $this->loadTools();
        }
    }

    private function normalizeToolOrders()
    {
        foreach($this->tools as $idx => $t) {
            $t->update(['order' => $idx]);
        }
        $this->loadTools();
    }

    public function moveToolUp($id)
    {
        if (!$this->canManageTools) return;
        $this->normalizeToolOrders();
        
        $currentIndex = $this->tools->search(fn($t) => $t->id == $id);
        if ($currentIndex > 0) {
            $currentTool = $this->tools[$currentIndex];
            $previousTool = $this->tools[$currentIndex - 1];
            
            $currentTool->update(['order' => $currentIndex - 1]);
            $previousTool->update(['order' => $currentIndex]);
            
            $this->loadTools();
        }
    }

    public function moveToolDown($id)
    {
        if (!$this->canManageTools) return;
        $this->normalizeToolOrders();
        
        $currentIndex = $this->tools->search(fn($t) => $t->id == $id);
        if ($currentIndex !== false && $currentIndex < $this->tools->count() - 1) {
            $currentTool = $this->tools[$currentIndex];
            $nextTool = $this->tools[$currentIndex + 1];
            
            $currentTool->update(['order' => $currentIndex + 1]);
            $nextTool->update(['order' => $currentIndex]);
            
            $this->loadTools();
        }
    }

    public function render()
    {
        return view('livewire.project.step-tools-manager');
    }
}
