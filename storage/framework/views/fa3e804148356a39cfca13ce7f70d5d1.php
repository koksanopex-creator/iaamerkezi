<?php $__env->startPush('pageTitle'); ?>
    İAA Havuzu | 
<?php $__env->stopPush(); ?>

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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('İyileştirme Havuzu')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-gray-50 via-white to-blue-50 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">

                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                 <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">İyileştirme Havuzu</h3>
                                <p class="mt-1 text-base text-gray-600">Onaylanmış ve hayata geçirilmek üzere bekleyen öneriler.</p>
                            </div>
                        </div>
                         <?php if(session('success')): ?><div class="mt-4 sm:mt-0 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p class="font-bold">Başarılı!</p><p><?php echo e(session('success')); ?></p></div><?php endif; ?>
                         <?php if(session('error')): ?><div class="mt-4 sm:mt-0 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert"><p class="font-bold">Hata!</p><p><?php echo e(session('error')); ?></p></div><?php endif; ?>
                    </div>
                    
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                        <table class="block sm:table min-w-full">
                            <thead class="hidden sm:table-header-group">
                                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <th class="px-6 py-4 text-center">Puan</th>
                                    <th class="px-6 py-4">Başlık</th>
                                    <th class="px-6 py-4">Öneren</th>
                                    <th class="px-6 py-4">Bölüm</th>
                                    <th class="px-6 py-4">Onay Tarihi</th>
                                    <th class="px-6 py-4 text-right">İşlem</th>
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group">
                                <?php $__empty_1 = true; $__currentLoopData = $havuzdakiler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-indigo-50 transition-colors duration-200 group">
                                        
                                        <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Puan:</span><div class="text-right sm:text-center"><button x-data @click="$dispatch('open-modal', 'puan-detay-<?php echo e($iaa->id); ?>')" class="relative inline-flex items-center justify-center w-14 h-14 font-bold text-sm text-white bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-200"><span><?php echo e(number_format($iaa->puan, 0, ',', '.')); ?></span></button></div></td>
                                        <td class="flex justify-between items-start p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Başlık:</span><div class="text-right sm:text-left"><p class="text-gray-800 font-medium group-hover:text-indigo-700 transition-colors"><?php echo e($iaa->baslik); ?></p></div></td>
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Öneren:</span><div class="text-right sm:text-left"><div class="inline-flex items-center space-x-2"><div class="w-7 h-7 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-white"><?php echo e(substr($iaa->gonderen->name ?? 'M', 0, 1)); ?></span></div><span class="text-sm font-medium text-gray-700"><?php echo e($iaa->gonderen->name ?? $iaa->guest_name); ?></span></div></div></td>
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Bölüm:</span><span class="text-right sm:text-left text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full"><?php echo e($iaa->bolum->ad ?? $iaa->ilgili_alan); ?></span></td>
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Onay Tarihi:</span><span class="text-right sm:text-left text-sm text-gray-500"><?php echo e($iaa->onaylanma_tarihi ? \Carbon\Carbon::parse($iaa->onaylanma_tarihi)->format('d.m.Y') : 'N/A'); ?></span></td>
                                        
                                        <!-- ================== İŞLEM HÜCRESİ (GÜNCELLENDİ) ================== -->
                                        <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                                <a href="<?php echo e(route('iaa.show', $iaa)); ?>" class="w-full sm:w-auto inline-flex justify-center text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 hover:bg-gray-50">İncele</a>
                                                
                                                
                                                <?php if(auth()->user()->lideriOlduguTakimlar->isNotEmpty()): ?>
                                                    
                                                    
                                                    <?php if($talepEdilenIaaIdleri->contains($iaa->id)): ?>
                                                        
                                                        <button class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-gray-500 bg-gray-200 border border-transparent rounded-md shadow-sm px-3 py-2 cursor-not-allowed" disabled>Talep Edildi</button>
                                                        
                                                        
                                                        <form action="<?php echo e(route('iaa.talebiGeriCek', $iaa->id)); ?>" method="POST" onsubmit="return confirm('Talebinizi geri çekmek istediğinize emin misiniz?');" class="inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-red-600 bg-red-50 border border-red-200 rounded-md shadow-sm px-3 py-2 hover:bg-red-100 transition-colors" title="Talebi Geri Çek">
                                                                <svg class="w-4 h-4 mr-1 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                Geri Çek
                                                            </button>
                                                        </form>
                                                    
                                                    <?php else: ?>
                                                        <button x-data @click="$dispatch('open-modal', 'talep-et-modal-<?php echo e($iaa->id); ?>')" class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-indigo-700">Talep Et</button>
                                                    <?php endif; ?>
                                                
                                                
                                                <?php else: ?>
                                                    
                                                    <div title="Öneri talep edebilmek için bir takım lideri olmalısınız.">
                                                        <button class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-white bg-indigo-300 border border-transparent rounded-md shadow-sm px-3 py-2 cursor-not-allowed">
                                                            Talep Et
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="block sm:table-row"><td colspan="6" class="p-12 text-center text-gray-500">Havuzda gösterilecek bir öneri bulunmamaktadır.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <?php $__currentLoopData = $havuzdakiler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($iaa->puan): ?>
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
                    <div class="flex items-start justify-between"><div class="flex items-center space-x-3"><div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg></div><div><h3 class="text-xl font-bold text-gray-800 tracking-tight">Puan Detayları</h3><p class="text-sm text-gray-500">Hesaplama dökümü</p></div></div><div class="inline-flex items-center justify-center px-4 py-2 text-2xl font-bold text-indigo-700 bg-indigo-100 rounded-full ring-2 ring-indigo-200"><?php echo e(number_format($iaa->puan, 0, ',', '.')); ?></div></div>
                    <hr class="my-6 border-gray-200">
                    <div class="space-y-4"><div class="flex items-center justify-between"><div class="flex items-center space-x-2"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg><span class="text-base font-medium text-gray-700">Risk</span></div><div class="px-3 py-1 text-base font-semibold text-gray-800 bg-gray-100 rounded-full"><?php echo e($iaa->risk); ?> / 5</div></div><div class="flex items-center justify-between"><div class="flex items-center space-x-2"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01M12 6v-1m0-1V4m0 2.01M18 10a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg><span class="text-base font-medium text-gray-700">Tahmini Kazanç</span></div><div class="px-3 py-1 text-base font-semibold text-green-800 bg-green-100 rounded-full"><?php echo e(number_format($iaa->kazanc_miktar, 0, ',', '.')); ?> <?php echo e($iaa->kazanc_birim); ?></div></div><div class="flex items-center justify-between"><div class="flex items-center space-x-2"><svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg><span class="text-base font-medium text-gray-700">Tahmini Bütçe</span></div><div class="px-3 py-1 text-base font-semibold text-red-800 bg-red-100 rounded-full"><?php echo e(number_format($iaa->butce_miktar, 0, ',', '.')); ?> <?php echo e($iaa->butce_birim); ?></div></div></div>
                    <div class="mt-6 p-3 bg-gray-100 rounded-lg text-center"><p class="text-sm text-gray-600"><span class="font-semibold">Formül:</span> (Risk × Kazanç) / Bütçe</p></div>
                    <div class="mt-8 flex justify-end"><button x-on:click="$dispatch('close')" class="inline-flex items-center justify-center bg-white px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">Kapat</button></div>
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
<?php endif; ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php if(auth()->user() && auth()->user()->lideriOlduguTakimlar->isNotEmpty()): ?>
        <?php $__currentLoopData = $havuzdakiler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('iaa.partials.talep-et-modal', ['iaa' => $iaa, 'liderOlduguTakimlar' => $liderOlduguTakimlar], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/iaa/havuz.blade.php ENDPATH**/ ?>