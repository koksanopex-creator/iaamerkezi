<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tanımlanmış Adımlar</h3>
        <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                <p><?php echo e(session('success')); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        
        <div class="space-y-4" wire:sortable="updateStepOrder" wire:sortable-group="steps">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                
                <div wire:sortable.item="<?php echo e($step->id); ?>" wire:key="step-<?php echo e($step->id); ?>" wire:sortable-group.item="steps" class="bg-gray-50 p-4 rounded-lg border flex justify-between items-start group">
                    <div class="flex-grow">
                        <div wire:sortable.handle wire:sortable-group.handle class="cursor-grab flex items-center mb-2">
                             
                             <svg class="w-5 h-5 text-gray-400 mr-2 group-hover:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                             </svg>
                            <p class="font-bold text-lg text-gray-800"><?php echo e($step->order); ?>. <?php echo e($step->name); ?></p>
                        </div>
                        <p class="text-sm text-gray-600 ml-7"><?php echo e($step->description); ?></p>
                        <p class="text-xs text-gray-500 mt-2 ml-7">Tahmini Süre: <?php echo e($step->default_duration_days); ?> gün</p>

                        
                        <!--[if BLOCK]><![endif]--><?php if(!empty($step->widgets)): ?>
                            <div class="mt-3 ml-7 border-t pt-2">
                                <p class="text-xs font-semibold text-gray-500 mb-1">Adım İçeriği (Widgetlar):</p>
                                <div class="flex flex-wrap gap-1">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $step->widgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full">
                                            <?php echo e($availableWidgets[$widget['type']]['label'] ?? $widget['type']); ?>

                                            
                                            <!--[if BLOCK]><![endif]--><?php if(!empty($widget['config']['title'])): ?>
                                                : <?php echo e(Str::limit($widget['config']['title'], 15)); ?>

                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        
                    </div>
                    <div class="flex space-x-3 flex-shrink-0 ml-4 pt-1"> 
                        <button wire:click="editStep(<?php echo e($step->id); ?>)" class="text-blue-600 hover:text-blue-900 text-sm font-semibold">Düzenle</button>
                        <button wire:click="deleteStep(<?php echo e($step->id); ?>)" wire:confirm="Bu adımı silmek istediğinizden emin misiniz?" class="text-red-600 hover:text-red-900 text-sm font-semibold">Sil</button>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-center text-gray-500 py-8">Bu şablon için henüz hiç adım tanımlanmamış.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    
    <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow sticky top-6"> 
         <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-3">Yeni Adım Ekle</h3>
         <form wire:submit="addStep">
             <div class="space-y-5"> 
                 <div>
                     <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Adım Adı</label>
                     <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                 </div>
                 <div>
                     <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                     <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                 </div>
                 <div>
                     <label for="default_duration_days" class="block text-sm font-medium text-gray-700 mb-1">Varsayılan Süre (Gün)</label>
                     <input type="number" wire:model="default_duration_days" id="default_duration_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required min="1">
                 </div>

                
                <div class="border-t pt-5 mt-5">
                     <h4 class="text-md font-medium text-gray-800 mb-3">Adım İçeriği (Widgetlar)</h4>

                     
                     <div class="flex items-center gap-2 mb-4">
                         <select wire:model="selectedWidgetType" class="flex-grow block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                             <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                 <option value="<?php echo e($type); ?>"><?php echo e($details['label']); ?></option>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                         </select>
                         <button type="button" wire:click="addWidget" class="px-3 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 text-sm font-semibold">+</button>
                     </div>

                     
                     <div class="space-y-3 max-h-60 overflow-y-auto border rounded p-2 bg-gray-50" wire:sortable="updateWidgetOrder" wire:sortable-group="editingStepWidgets.<?php echo e($editingStepId); ?>">
                         <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $widgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                             
                             <div wire:sortable.item="<?php echo e($index); ?>" wire:key="widget-edit-<?php echo e($index); ?>" wire:sortable-group.item="editingStepWidgets.<?php echo e($editingStepId); ?>" class="p-3 bg-white rounded border border-gray-200 shadow-sm"><div class="flex items-center justify-between mb-2">
                                     <div class="flex items-center gap-2">
                                     <span wire:sortable.handle wire:sortable-group.handle class="cursor-grab text-gray-400 hover:text-gray-600">
                                             <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                                         </span>
                                         <span class="text-sm font-semibold text-gray-700"><?php echo e($availableWidgets[$widget['type']]['label'] ?? $widget['type']); ?></span>
                                     </div>
                                     <div>
                                         
                                        <button type="button"
                                                wire:click.stop="moveWidgetUp(<?php echo e($index); ?>)"
                                                class="text-gray-400 hover:text-gray-600 disabled:opacity-50"
                                                <?php if($loop->first): ?> disabled <?php endif; ?>>↑</button>

                                        <button type="button"
                                                wire:click.stop="moveWidgetDown(<?php echo e($index); ?>)"
                                                class="text-gray-400 hover:text-gray-600 disabled:opacity-50"
                                                <?php if($loop->last): ?> disabled <?php endif; ?>>↓</button>


                                         <button type="button" wire:click="removeWidget(<?php echo e($index); ?>)" class="ml-2 text-red-500 hover:text-red-700 text-xs font-bold">X</button>
                                     </div>
                                 </div>
                                 
                                 <div class="space-y-2 pl-7 border-l-2 border-gray-200 ml-2">
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableWidgets[$widget['type']]['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionKey => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="text-xs">
            
            <label for="widget-<?php echo e($editingStepId ? 'edit-' : ''); ?><?php echo e($index); ?>-<?php echo e($optionKey); ?>" class="block font-medium text-gray-600 mb-0.5"><?php echo e($optionLabel); ?></label>

            <!--[if BLOCK]><![endif]--><?php if(str_contains($optionLabel, '(checkbox)')): ?>
                <input type="checkbox"
                       wire:model="widgets.<?php echo e($index); ?>.config.<?php echo e($optionKey); ?>" 
                       id="widget-<?php echo e($editingStepId ? 'edit-' : ''); ?><?php echo e($index); ?>-<?php echo e($optionKey); ?>"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">

            <?php elseif(str_contains($optionLabel, '(textarea)')): ?>
                <textarea wire:model.debounce.500ms="widgets.<?php echo e($index); ?>.config.<?php echo e($optionKey); ?>" 
                          id="widget-<?php echo e($editingStepId ? 'edit-' : ''); ?><?php echo e($index); ?>-<?php echo e($optionKey); ?>"
                          rows="2"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs"></textarea>

            <?php else: ?> 
                <input type="text"
                       wire:model.debounce.500ms="widgets.<?php echo e($index); ?>.config.<?php echo e($optionKey); ?>" 
                       id="widget-<?php echo e($editingStepId ? 'edit-' : ''); ?><?php echo e($index); ?>-<?php echo e($optionKey); ?>"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs">
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["widgets.{$index}.config.{$optionKey}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><?php if(empty($availableWidgets[$widget['type']]['options'])): ?>
        <p class="text-xs text-gray-400 italic">Bu widget için ek ayar yok.</p>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
                             </div>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                             <p class="text-center text-xs text-gray-500 py-3">Henüz widget eklenmedi.</p>
                         <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                     </div>
                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['widgets'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['widgets.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1">Widget yapılandırmasında hata var.</span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                 </div>
                

                 <div class="border-t pt-5"> 
                     <button type="submit" class="w-full justify-center py-2.5 px-4 rounded-md text-white bg-indigo-600 hover:bg-indigo-700 font-semibold shadow-md hover:shadow-lg transition-all duration-150">Adımı Ekle</button>
                 </div>
             </div>
         </form>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($editingStepId): ?>
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
         x-data="{ showModal: <?php if ((object) ('editingStepId') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('editingStepId'->value()); ?>')<?php echo e('editingStepId'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('editingStepId'); ?>')<?php endif; ?> }"
         x-show="showModal"
         x-on:keydown.escape.window="showModal = null" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         >
        <div class="relative mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-xl bg-white m-4" @click.outside="showModal = null"> 
            <div class="flex justify-between items-center border-b pb-3 mb-5">
                 <h3 class="text-xl leading-6 font-bold text-gray-900">Adımı Düzenle</h3>
                 <button type="button" @click="showModal = null" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            
            <form wire:submit="updateStep" class="space-y-5 max-h-[70vh] overflow-y-auto pr-2"> 
                
                 <div>
                     <label for="editingName" class="block text-sm font-medium text-gray-700 mb-1">Adım Adı</label>
                     <input type="text" wire:model="editingName" id="editingName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['editingName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                 </div>
                 <div>
                     <label for="editingDescription" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                     <textarea wire:model="editingDescription" id="editingDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                 </div>
                 <div>
                     <label for="editingDuration" class="block text-sm font-medium text-gray-700 mb-1">Varsayılan Süre (Gün)</label>
                     <input type="number" wire:model="editingDuration" id="editingDuration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required min="1">
                 </div>

                 
                 <div class="border-t pt-5 mt-5">
                     <h4 class="text-md font-medium text-gray-800 mb-3">Adım İçeriği (Widgetlar)</h4>
                     <div class="flex items-center gap-2 mb-4">
                         <select wire:model="selectedWidgetType" class="flex-grow block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                             <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                 <option value="<?php echo e($type); ?>"><?php echo e($details['label']); ?></option>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                         </select>
                         <button type="button" wire:click="addWidget" class="px-3 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 text-sm font-semibold">+</button>
                     </div>
                     <div class="space-y-3 max-h-60 overflow-y-auto border rounded p-2 bg-gray-50" wire:sortable="updateWidgetOrder">
                         <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $widgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                             <div wire:sortable.item="<?php echo e($index); ?>" wire:key="widget-edit-<?php echo e($index); ?>" class="p-3 bg-white rounded border border-gray-200 shadow-sm">
                                 <div class="flex items-center justify-between mb-2">
                                     <div class="flex items-center gap-2">
                                         <span wire:sortable.handle class="cursor-grab text-gray-400 hover:text-gray-600">
                                              <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                                         </span>
                                         <span class="text-sm font-semibold text-gray-700"><?php echo e($availableWidgets[$widget['type']]['label'] ?? $widget['type']); ?></span>
                                     </div>
                                     <div>
                                         <button type="button" wire:click="moveWidgetUp(<?php echo e($index); ?>)" class="text-gray-400 hover:text-gray-600 disabled:opacity-50" <?php if($loop->first): ?> disabled <?php endif; ?>>↑</button>
                                         <button type="button" wire:click="moveWidgetDown(<?php echo e($index); ?>)" class="text-gray-400 hover:text-gray-600 disabled:opacity-50" <?php if($loop->last): ?> disabled <?php endif; ?>>↓</button>
                                         <button type="button" wire:click="removeWidget(<?php echo e($index); ?>)" class="ml-2 text-red-500 hover:text-red-700 text-xs font-bold">X</button>
                                     </div>
                                 </div>
                                 <div class="space-y-2 pl-7 border-l-2 border-gray-200 ml-2">
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableWidgets[$widget['type']]['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionKey => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="text-xs">
            
            <label for="widget-<?php echo e($editingStepId ? 'edit-' : ''); ?><?php echo e($index); ?>-<?php echo e($optionKey); ?>" class="block font-medium text-gray-600 mb-0.5"><?php echo e($optionLabel); ?></label>

            <!--[if BLOCK]><![endif]--><?php if(str_contains($optionLabel, '(checkbox)')): ?>
                <input type="checkbox"
                       wire:model="widgets.<?php echo e($index); ?>.config.<?php echo e($optionKey); ?>" 
                       id="widget-<?php echo e($editingStepId ? 'edit-' : ''); ?><?php echo e($index); ?>-<?php echo e($optionKey); ?>"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">

            <?php elseif(str_contains($optionLabel, '(textarea)')): ?>
                <textarea wire:model.debounce.500ms="widgets.<?php echo e($index); ?>.config.<?php echo e($optionKey); ?>" 
                          id="widget-<?php echo e($editingStepId ? 'edit-' : ''); ?><?php echo e($index); ?>-<?php echo e($optionKey); ?>"
                          rows="2"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs"></textarea>

            <?php else: ?> 
                <input type="text"
                       wire:model.debounce.500ms="widgets.<?php echo e($index); ?>.config.<?php echo e($optionKey); ?>" 
                       id="widget-<?php echo e($editingStepId ? 'edit-' : ''); ?><?php echo e($index); ?>-<?php echo e($optionKey); ?>"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs">
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["widgets.{$index}.config.{$optionKey}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    <!--[if BLOCK]><![endif]--><?php if(empty($availableWidgets[$widget['type']]['options'])): ?>
        <p class="text-xs text-gray-400 italic">Bu widget için ek ayar yok.</p>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
                             </div>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                             <p class="text-center text-xs text-gray-500 py-3">Henüz widget eklenmedi.</p>
                         <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                     </div>
                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['widgets'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['widgets.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1">Widget yapılandırmasında hata var.</span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                 </div>
                 

                 <div class="mt-6 flex justify-end space-x-3 border-t pt-5">
                     
                     <button type="button" wire:click="cancelEdit" @click="showModal = null" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">İptal</button>
                     <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Adımı Güncelle</button>
                 </div>
            </form>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]--> 

</div> <?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/workflow-steps-manager.blade.php ENDPATH**/ ?>