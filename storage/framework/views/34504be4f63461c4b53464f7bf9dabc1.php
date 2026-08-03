<div>
    <div class="mt-8 border-t border-gray-200 pt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.065-2.572z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Araçlar (Opsiyonel)
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    <?php echo e(count($tools)); ?>

                </span>
            </h3>
            
            <!--[if BLOCK]><![endif]--><?php if($this->canManageTools): ?>
                <button wire:click="openAddToolModal" wire:loading.attr="disabled" wire:target="openAddToolModal" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Araç Ekle
                </button>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!--[if BLOCK]><![endif]--><?php if(count($tools) === 0): ?>
            <div class="text-center py-10 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Araç Bulunmuyor</h3>
                <p class="mt-1 text-sm text-gray-500">Bu adıma henüz ek bir araç eklenmemiş (Opsiyonel).</p>
                <!--[if BLOCK]><![endif]--><?php if($this->canManageTools): ?>
                    <div class="mt-6">
                        <button wire:click="openAddToolModal" wire:loading.attr="disabled" wire:target="openAddToolModal" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            İlk Aracı Ekle
                        </button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden" wire:key="tool-<?php echo e($tool->id); ?>">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center">
                                <?php echo $availableTools[$tool->tool_type]['icon'] ?? ''; ?>

                                <h4 class="ml-2 text-base font-medium text-gray-900"><?php echo e($tool->title); ?></h4>
                                <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    <?php echo e($tool->user->name ?? 'Bilinmeyen'); ?> tarafından
                                </span>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if($this->canManageTools): ?>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center border border-gray-200 rounded overflow-hidden bg-white shadow-sm">
                                        <button wire:click="moveToolUp(<?php echo e($tool->id); ?>)" class="hover:bg-gray-100 px-1.5 py-1 text-gray-500 <?php echo e($loop->first ? 'opacity-30 cursor-not-allowed' : 'hover:text-indigo-600'); ?>" <?php echo e($loop->first ? 'disabled' : ''); ?> title="Yukarı Taşı">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                        </button>
                                        <div class="h-4 w-px bg-gray-200"></div>
                                        <button wire:click="moveToolDown(<?php echo e($tool->id); ?>)" class="hover:bg-gray-100 px-1.5 py-1 text-gray-500 <?php echo e($loop->last ? 'opacity-30 cursor-not-allowed' : 'hover:text-indigo-600'); ?>" <?php echo e($loop->last ? 'disabled' : ''); ?> title="Aşağı Taşı">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                    </div>
                                    <div class="h-6 w-px bg-gray-300 mx-1"></div>
                                    <button wire:click="removeTool(<?php echo e($tool->id); ?>)" wire:confirm="Bu aracı ve içindeki verileri silmek istediğinizden emin misiniz?" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Aracı Sil">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="p-4">
                            <!--[if BLOCK]><![endif]--><?php if($tool->tool_type === '5why'): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.tools.five-whys-analysis', ['tool' => $tool, 'canManage' => $this->canManageTools]);

$__html = app('livewire')->mount($__name, $__params, '5why-'.$tool->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php elseif($tool->tool_type === 'chart'): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.tools.chart-analysis', ['tool' => $tool, 'canManage' => $this->canManageTools]);

$__html = app('livewire')->mount($__name, $__params, 'chart-'.$tool->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php elseif($tool->tool_type === 'swot'): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.tools.swot-analysis', ['tool' => $tool, 'canManage' => $this->canManageTools]);

$__html = app('livewire')->mount($__name, $__params, 'swot-'.$tool->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php elseif($tool->tool_type === 'checklist'): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.tools.checklist-tool', ['tool' => $tool, 'canManage' => $this->canManageTools]);

$__html = app('livewire')->mount($__name, $__params, 'checklist-'.$tool->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php elseif($tool->tool_type === 'action_list'): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.tools.action-list-tool', ['tool' => $tool, 'canManage' => $this->canManageTools]);

$__html = app('livewire')->mount($__name, $__params, 'actionlist-'.$tool->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php elseif($tool->tool_type === 'fishbone'): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.tools.fishbone-analysis', ['tool' => $tool, 'canManage' => $this->canManageTools]);

$__html = app('livewire')->mount($__name, $__params, 'fishbone-'.$tool->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php elseif($tool->tool_type === 'pareto'): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.tools.pareto-analysis', ['tool' => $tool, 'canManage' => $this->canManageTools]);

$__html = app('livewire')->mount($__name, $__params, 'pareto-'.$tool->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php elseif($tool->tool_type === '4m_report'): ?>
                                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.tools.four-m-report', ['tool' => $tool, 'canManage' => $this->canManageTools]);

$__html = app('livewire')->mount($__name, $__params, '4m-'.$tool->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                            <?php else: ?>
                                <div class="p-4 bg-yellow-50 text-yellow-700 rounded-md">
                                    Bu araç tipi (<?php echo e($tool->tool_type); ?>) henüz desteklenmiyor veya yapım aşamasında.
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($showAddToolModal): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeAddToolModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-6 sm:p-8">
                        <div>
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="mt-5 text-center">
                                <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                    Yeni Analiz Aracı Ekle
                                </h3>
                                <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                                    Bu proje adımına özel veriler girebileceğiniz ve takımınızla ortak çalışabileceğiniz bir araç seçin.
                                </p>
                            </div>

                            <div class="mt-6 text-left max-w-full">
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6">
                                    <label for="toolTitle" class="block text-sm font-semibold text-gray-700">Özel Araç Başlığı (Opsiyonel)</label>
                                    <div class="mt-2">
                                        <input type="text" wire:model.live="toolTitle" id="toolTitle" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg p-3 transition-colors hover:bg-gray-50" placeholder="Örn: 2026 İlk Çeyrek Analizi (Boş bırakırsanız varsayılan isim kullanılır)">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableTools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div wire:click="addTool('<?php echo e($type); ?>')" 
                                             wire:loading.class="opacity-50 scale-95 pointer-events-none"
                                             wire:target="addTool"
                                             class="relative group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md flex items-center space-x-4 cursor-pointer transition-all duration-200 transform hover:-translate-y-1 hover:border-indigo-300">
                                            
                                            <div class="flex-shrink-0 bg-indigo-50 group-hover:bg-indigo-100 p-3 rounded-lg transition-colors">
                                                <?php echo $info['icon']; ?>

                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">
                                                    <?php echo e($info['name']); ?>

                                                </p>
                                                <p class="text-xs text-gray-500 truncate mt-1">
                                                    <?php echo e($info['description']); ?>

                                                </p>
                                            </div>

                                            
                                            <div wire:loading wire:target="addTool('<?php echo e($type); ?>')" class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl backdrop-blur-sm z-10">
                                                <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="closeAddToolModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            İptal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/step-tools-manager.blade.php ENDPATH**/ ?>