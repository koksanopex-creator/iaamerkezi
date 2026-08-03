<div>
    <div class="space-y-4">
        <div class="flex justify-between items-center mb-2">
            <h5 class="text-sm font-semibold text-gray-700">Balık Kılçığı Analizi (Ishikawa)</h5>
            <div x-data="{ saving: false }" 
                 x-on:tool-saved.window="saving = true; setTimeout(() => saving = false, 2000)" 
                 class="text-xs text-gray-500 flex items-center h-4">
                <span x-show="saving" x-transition.opacity class="text-green-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Kaydedildi
                </span>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-800 mb-2">Problem Tanımı (Balığın Başı)</label>
                <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                    <input type="text" wire:model.live.debounce.1000ms="problem_statement" class="w-full text-base rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3" placeholder="Analiz edilecek temel sorunu veya etkiyi buraya yazın...">
                <?php else: ?>
                    <div class="w-full text-base bg-white border border-gray-200 rounded-md p-3 text-gray-800 font-semibold">
                        <?php echo e($problem_statement ?: 'Problem tanımı girilmemiş.'); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <?php
                $categoryLabels = [
                    'insan' => 'İnsan (Man)',
                    'makine' => 'Makine (Machine)',
                    'malzeme' => 'Malzeme (Material)',
                    'metot' => 'Metot (Method)',
                    'olcum' => 'Ölçüm (Measurement)',
                    'cevre' => 'Çevre (Mother Nature)'
                ];
                $categoryColors = [
                    'insan' => 'text-blue-700 bg-blue-100',
                    'makine' => 'text-emerald-700 bg-emerald-100',
                    'malzeme' => 'text-amber-700 bg-amber-100',
                    'metot' => 'text-purple-700 bg-purple-100',
                    'olcum' => 'text-rose-700 bg-rose-100',
                    'cevre' => 'text-cyan-700 bg-cyan-100'
                ];
            ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $categoryLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col">
                        <div class="px-3 py-2 border-b border-gray-200 flex items-center <?php echo e($categoryColors[$key]); ?>">
                            <h6 class="text-sm font-bold"><?php echo e($label); ?></h6>
                        </div>
                        <div class="p-3 flex-1 flex flex-col">
                            <ul class="space-y-2 mb-3 flex-1">
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $categories[$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li class="flex items-start text-sm group">
                                        <span class="mr-2 text-gray-400 mt-0.5">•</span>
                                        <span class="flex-1 text-gray-700"><?php echo e($item); ?></span>
                                        <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                                            <button wire:click="removeItem('<?php echo e($key); ?>', <?php echo e($index); ?>)" class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity ml-1">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <li class="text-xs text-gray-400 italic">Henüz neden eklenmedi.</li>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </ul>
                            
                            <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                                <div class="mt-auto pt-2 border-t border-gray-100">
                                    <div class="flex">
                                        <input type="text" wire:model="newItems.<?php echo e($key); ?>" wire:keydown.enter="addItem('<?php echo e($key); ?>')" class="block w-full text-xs border-gray-300 rounded-l-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Yeni neden yazıp Enter'a basın">
                                        <button wire:click="addItem('<?php echo e($key); ?>')" class="bg-gray-100 text-gray-600 border border-gray-300 border-l-0 rounded-r-md px-2 hover:bg-gray-200">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/tools/fishbone-analysis.blade.php ENDPATH**/ ?>