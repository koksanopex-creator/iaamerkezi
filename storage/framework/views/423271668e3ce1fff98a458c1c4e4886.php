<div>
    <div class="space-y-4">
        
        <div class="flex justify-between items-center mb-2">
            <h5 class="text-sm font-semibold text-gray-700">5 Neden Analizi Aracı</h5>
            <div x-data="{ saving: false }" 
                 x-on:tool-saved.window="saving = true; setTimeout(() => saving = false, 2000)" 
                 class="text-xs text-gray-500 flex items-center h-4">
                <span x-show="saving" x-transition.opacity class="text-green-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Kaydedildi
                </span>
            </div>
        </div>

        
        <div class="bg-red-50 p-4 rounded-lg border border-red-100">
            <label class="block text-xs font-bold text-red-700 uppercase mb-1">Problem Tanımı</label>
            <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                <textarea wire:model.live.debounce.1000ms="problemStatement" rows="2" class="w-full text-sm rounded-md border-red-200 shadow-sm focus:border-red-500 focus:ring-red-500 bg-white" placeholder="Buraya asıl problemi net ve kısa bir şekilde yazın..."></textarea>
            <?php else: ?>
                <div class="text-sm text-gray-800 bg-white p-2 rounded border border-red-100 min-h-[2.5rem]">
                    <?php echo e($problemStatement ?: 'Problem tanımı girilmemiş.'); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="space-y-3 relative pl-4 border-l-2 border-indigo-200 ml-2 mt-4">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $whys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $why): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="relative">
                    
                    <div class="absolute -left-[21px] top-3 w-4 h-0.5 bg-indigo-200"></div>
                    <div class="absolute -left-[27px] top-1.5 bg-indigo-100 text-indigo-700 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold border border-indigo-200">
                        <?php echo e($index + 1); ?>

                    </div>

                    <div class="ml-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Neden <?php echo e($index + 1); ?>?</label>
                        <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                            <input type="text" wire:model.live.debounce.1000ms="whys.<?php echo e($index); ?>" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Neden oldu?">
                        <?php else: ?>
                            <div class="text-sm text-gray-800 bg-gray-50 p-2 rounded border border-gray-200 min-h-[2.5rem]">
                                <?php echo e($why ?: '-'); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="mt-4 bg-green-50 p-4 rounded-lg border border-green-200 flex items-start gap-3 shadow-inner">
            <div class="p-2 bg-green-100 rounded-lg text-green-600 mt-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-bold text-green-700 uppercase mb-1">Tespit Edilen Kök Neden</label>
                <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                    <textarea wire:model.live.debounce.1000ms="rootCause" rows="2" class="w-full text-sm rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white" placeholder="Analiz sonucunda bulduğunuz asıl kök nedeni buraya yazın..."></textarea>
                <?php else: ?>
                    <div class="text-sm text-gray-800 font-medium">
                        <?php echo e($rootCause ?: 'Kök neden henüz belirlenmemiş.'); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/tools/five-whys-analysis.blade.php ENDPATH**/ ?>