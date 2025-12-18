<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
         <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg shadow">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
             <h2 class="font-bold text-2xl text-gray-800">
                 Çözüm Takımını Düzenle: <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"><?php echo e($takim->ad); ?></span>
             </h2>
         </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-indigo-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200/50">
                <div class="p-6 sm:p-8">
                    
                    

                    
                    <form id="updateForm" action="<?php echo e(route('admin.cozum-takimlari.update', ['cozumTakimi' => $takim->id])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="space-y-6">
                            
                            <div>
                                <label for="ad" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                     <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                     Takım Adı <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" name="ad" id="ad" value="<?php echo e(old('ad', $takim->ad)); ?>" required
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out py-3 px-4 placeholder-gray-400">
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('ad'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('ad')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>

                             
                            <div>
                                <label for="lider_user_id" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                     <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                     Takım Lideri <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select name="lider_user_id" id="lider_user_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out py-3 px-4 appearance-none bg-white">
                                    <option value="" disabled <?php echo e(!$takim->lider_user_id ? 'selected' : ''); ?>>-- Lider Seçin --</option>
                                    <?php $__empty_1 = true; $__currentLoopData = $liderler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($lider->id); ?>" <?php echo e(old('lider_user_id', $takim->lider_user_id) == $lider->id ? 'selected' : ''); ?>>
                                            <?php echo e($lider->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option value="" disabled>Uygun lider bulunamadı.</option>
                                    <?php endif; ?>
                                </select>
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('lider_user_id'),'class' => 'mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('lider_user_id')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                <?php if($liderler->isEmpty() && !$takim->lider): ?>
                                 <p class="mt-2 text-xs text-orange-600">Lider olarak atanabilecek ("Müşteri Şikayeti Çözüm Lideri" rolüne sahip) kullanıcı bulunamadı. Lütfen önce kullanıcı yönetimi sayfasından rol ataması yapın.</p>
                                <?php elseif($liderler->isEmpty() && $takim->lider && !$takim->lider->hasRole('Müşteri Şikayeti Çözüm Lideri')): ?>
                                 <p class="mt-2 text-xs text-orange-600">Mevcut liderin ("<?php echo e($takim->lider->name); ?>") "Müşteri Şikayeti Çözüm Lideri" rolü bulunmuyor veya uygun başka lider yok. Lütfen rol ataması yapın veya farklı bir lider seçin.</p>
                                <?php endif; ?>
                            </div>

                             
                            <input type="hidden" name="tur" value="<?php echo e($takim->tur); ?>">
                        </div>
                    </form> 
                    


                    
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        
                        
                        
                        <div>
                             <form id="deleteForm" action="<?php echo e(route('admin.cozum-takimlari.destroy', ['cozumTakimi' => $takim->id])); ?>" method="POST" onsubmit="return confirm('Bu takımı silmek istediğinizden emin misiniz? Bu takıma atanmış şikayetler varsa silme işlemi başarısız olacaktır.');">
                                 <?php echo csrf_field(); ?>
                                 <?php echo method_field('DELETE'); ?>
                                 <button type="submit" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-5 border border-transparent rounded-lg shadow-sm transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                     <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                     Takımı Sil
                                 </button>
                             </form>
                        </div>

                        
                        <div class="flex items-center space-x-4">
                            
                            <a href="<?php echo e(route('admin.cozum-takimlari.index')); ?>" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2.5 px-5 border border-gray-300 rounded-lg shadow-sm transition-colors duration-150 ease-in-out">
                                İptal
                            </a>
                            
                            
                            
                            <button type="submit" form="updateForm" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md hover:from-indigo-700 hover:to-blue-700 transform hover:-translate-y-0.5 transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/cozum_takimlari/edit.blade.php ENDPATH**/ ?>