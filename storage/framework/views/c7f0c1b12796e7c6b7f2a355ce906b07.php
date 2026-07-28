<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'puan-detay-'.e($iaa->id).'','focusable' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'puan-detay-'.e($iaa->id).'','focusable' => true]); ?>
    <div class="p-6 sm:p-8 bg-gradient-to-br from-gray-50 via-white to-indigo-50">

        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 tracking-tight">Puan Detayları</h3>
                    <p class="text-sm text-gray-500">Hesaplama dökümü</p>
                </div>
            </div>
            <div class="inline-flex items-center justify-center px-4 py-2 text-2xl font-bold text-indigo-700 bg-indigo-100 rounded-full ring-2 ring-indigo-200">
                
                <?php echo e(number_format($iaa->puan, 0, ',', '.')); ?>

            </div>
        </div>

        <hr class="my-6 border-gray-200">

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="text-base font-medium text-gray-700">Risk</span>
                </div>
                <div class="px-3 py-1 text-base font-semibold text-gray-800 bg-gray-100 rounded-full">
                    <?php echo e($iaa->risk); ?> / 5
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01M12 6v-1m0-1V4m0 2.01M18 10a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                    <span class="text-base font-medium text-gray-700">Tahmini Kazanç</span>
                </div>
                <div class="px-3 py-1 text-base font-semibold text-green-800 bg-green-100 rounded-full">
                    <?php echo e(number_format($iaa->kazanc_miktar, 0, ',', '.')); ?> <?php echo e($iaa->kazanc_birim); ?>

                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    <span class="text-base font-medium text-gray-700">Tahmini Bütçe</span>
                </div>
                <div class="px-3 py-1 text-base font-semibold text-red-800 bg-red-100 rounded-full">
                    <?php echo e(number_format($iaa->butce_miktar, 0, ',', '.')); ?> <?php echo e($iaa->butce_birim); ?>

                </div>
            </div>
        </div>

        <div class="mt-6 p-3 bg-gray-100 rounded-lg text-center">
            <p class="text-sm text-gray-600">
                <span class="font-semibold">Formül:</span> (Risk × Kazanç) / Bütçe
            </p>
        </div>

        <div class="mt-8 flex justify-end">
            <button x-on:click="$dispatch('close')" class="inline-flex items-center justify-center bg-white px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kapat
            </button>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/puan-detay-modal.blade.php ENDPATH**/ ?>