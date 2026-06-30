<div class="bg-white p-6 rounded-lg shadow-lg border-2 border-blue-300">
    <h3 class="text-xl font-bold text-blue-700 mb-2">Aktif Adım: <?php echo e($currentStep['name']); ?></h3>
    <p class="text-gray-600 mb-6"><?php echo e($currentStep['description']); ?></p>

    
     <!--[if BLOCK]><![endif]--><?php if(session()->has('error')): ?>
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <div x-data="{ errorMessage: '' }" <?php echo $__env->yieldSection(); ?>-error.window="errorMessage = $event.detail; setTimeout(() => errorMessage = '', 5000)">
        <template x-if="errorMessage">
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg" x-text="errorMessage"></div>
        </template>
    </div>


    <form wire:submit="save" id="main-form">

        <div class="space-y-6">
            <!--[if BLOCK]><![endif]--><?php if(empty($currentStep['widgets'])): ?>
                <div class="p-4 text-center bg-gray-50 rounded-lg border">
                    <p class="text-gray-500">Bu adım için herhangi bir form elemanı (widget) tanımlanmamış.</p>
                </div>
            <?php else: ?>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $currentStep['widgets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        // Widget config'ini ve index'i partial'a göndermek için hazırla
                        $widgetData = [
                            'index' => $index,
                            'config' => $widget['config'] ?? []
                        ];
                    ?>

                    <div class="border-t border-gray-200 pt-6">
                        <!--[if BLOCK]><![endif]--><?php switch($widget['type']):
                            case ('textbox'): ?>
                                <?php echo $__env->make('livewire.project.widgets._textbox', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('five_whys'): ?>
                                <?php echo $__env->make('livewire.project.widgets._five-whys', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('fishbone'): ?>
                                <?php echo $__env->make('livewire.project.widgets._fishbone', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('pareto'): ?>
                                
                                <?php $paretoId = $this->getId(); ?>
                                <script> window['initialParetoData_<?php echo e($paretoId); ?>'] = <?php echo json_encode($initialChartData['pareto'][$paretoId] ?? null, 15, 512) ?>; </script>

                                <?php echo $__env->make('livewire.project.widgets._pareto', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('user_select'): ?>
                                <?php echo $__env->make('livewire.project.widgets._user-select', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('date_picker'): ?>
                                <?php echo $__env->make('livewire.project.widgets._date-picker', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('file_upload'): ?>
                                <?php echo $__env->make('livewire.project.widgets._file-upload', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('info_text'): ?>
                                <?php echo $__env->make('livewire.project.widgets._info-text', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>

                            
                            <?php case ('bar_chart'): ?>
                                
                                <?php $chartId = $this->getId() . '-' . $index; ?>
                                
                                <script> window['initialChartData_<?php echo e($chartId); ?>'] = <?php echo json_encode($initialChartData['bar_chart'][$chartId] ?? null, 15, 512) ?>; </script>

                                <?php echo $__env->make('livewire.project.widgets._bar-chart', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('line_chart'): ?>
                                
                                <?php $chartId = $this->getId() . '-' . $index; ?>
                                <script> window['initialChartData_<?php echo e($chartId); ?>'] = <?php echo json_encode($initialChartData['line_chart'][$chartId] ?? null, 15, 512) ?>; </script>
                                <?php echo $__env->make('livewire.project.widgets._line-chart', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            

                            
                            <?php case ('swot'): ?>
                                <?php echo $__env->make('livewire.project.widgets._swot', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('checklist'): ?>
                                <?php echo $__env->make('livewire.project.widgets._checklist', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('before_after'): ?>
                                <?php echo $__env->make('livewire.project.widgets._before-after', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('risk_matrix'): ?>
                                <?php echo $__env->make('livewire.project.widgets._risk-matrix', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('image_upload'): ?>
                                <?php echo $__env->make('livewire.project.widgets._image-upload', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('action_list'): ?>
                                <?php echo $__env->make('livewire.project.widgets._action-list', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('task_list'): ?>
                                <?php echo $__env->make('livewire.project.widgets._task-list', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('prioritization_matrix'): ?>
                                <?php echo $__env->make('livewire.project.widgets._prioritization-matrix', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            <?php case ('4m_report'): ?>
                                <?php echo $__env->make('livewire.project.widgets._4m-report', $widgetData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php break; ?>
                            

                            <?php default: ?>
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                                    <strong>Hata:</strong> Tanımsız widget türü: '<?php echo e($widget['type']); ?>'
                                </div>
                        <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="mt-8 flex justify-end items-center gap-3 border-t pt-6">
            
            
            <!--[if BLOCK]><![endif]--><?php if(isset($progressUpdate) && $progressUpdate && $progressUpdate->id): ?>
                <button type="button" 
                        onclick="if(confirm('Değişiklikleri iptal edip adımı tekrar kapatmak istiyor musunuz?')) { document.getElementById('form-vazgec-<?php echo e($progressUpdate->id); ?>').submit(); }"
                        class="bg-red-50 border border-red-200 text-red-700 hover:bg-red-100 hover:border-red-300 py-2 px-4 rounded-md shadow-sm text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Vazgeç (Kapat)
                </button>
            <?php else: ?>
                
                 <button type="button" wire:click.prevent="cancel" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    İptal
                </button>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <?php
                // Bu adım için bir atama var mı diye kontrol et
                $assignmentRecord = \Illuminate\Support\Facades\DB::table('iaa_step_assignments')
                    ->where('iaa_id', $iaa instanceof \App\Models\Iaa ? $iaa->id : $iaa['id'])
                    ->where('iaa_workflow_step_id', $currentStep['id'])
                    ->first();
                
                $isLockedForUser = false;
                $assigneeName = '';

                if ($assignmentRecord) {
                    $userId = auth()->id();
                    // Atanan kişi ben değilsem
                    if ($assignmentRecord->user_id != $userId) {
                         // Lider miyim? (Lider her şeyi yapabilir)
                         $liderId = $iaa->atananTakim->lider_user_id ?? 0;
                         $isAdmin = auth()->user()->hasRole('Superadmin');
                         
                         if ($userId != $liderId && !$isAdmin) {
                             $isLockedForUser = true;
                             // Atanan kişinin ismini bul (Uyarı için)
                             $assigneeName = \App\Models\User::find($assignmentRecord->user_id)->name ?? 'Başkası';
                         }
                    }
                }
            ?>

            
            <button type="submit"
                    <?php if($isLockedForUser): ?> disabled <?php endif; ?>
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-md transition-colors
                    <?php echo e($isLockedForUser ? 'bg-gray-400 cursor-not-allowed hover:bg-gray-400' : 'bg-indigo-600 hover:bg-indigo-700'); ?>"
                    <?php if($isLockedForUser): ?> title="Bu adım <?php echo e($assigneeName); ?> sorumluluğundadır." <?php endif; ?>>
                
                <!--[if BLOCK]><![endif]--><?php if(!$isLockedForUser): ?>
                    <div wire:loading wire:target="save" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                <?php else: ?>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php echo e($isLockedForUser ? ($assigneeName . ' Bekleniyor') : 'Adımı Tamamla ve Kaydet'); ?>

            </button>
        </div>
    </form> 
    


    
    <!--[if BLOCK]><![endif]--><?php if(isset($progressUpdate) && $progressUpdate && $progressUpdate->id): ?>
        <form id="form-vazgec-<?php echo e($progressUpdate->id); ?>" 
              action="<?php echo e(route('proje.workspace.cancelReopenStep', ['id' => $progressUpdate->id])); ?>" 
              method="POST" 
              style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div>


<?php echo $__env->make('livewire.project.widgets._generic-chart-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>



<?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/project/active-step.blade.php ENDPATH**/ ?>