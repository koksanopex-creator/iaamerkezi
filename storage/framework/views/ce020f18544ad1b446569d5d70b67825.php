<?php $__env->startPush('pageTitle'); ?>
    İAA Önerilerim | 
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
            <?php echo e(__('İyileştirmeye Açık Alan Önerilerim')); ?>

        </h2>
     <?php $__env->endSlot(); ?>
    

    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">

                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                                 <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">İAA Önerilerim</h3>
                                <p class="mt-1 text-base text-gray-600">Oluşturduğunuz tüm iyileştirme önerilerini buradan takip edebilirsiniz.</p>
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 flex-shrink-0">
                            <a href="<?php echo e(route('iaa.create')); ?>" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-sm hover:from-indigo-700 hover:to-blue-600 transform hover:-translate-y-0.5 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Yeni İAA Öner
                            </a>
                        </div>
                    </div>
                    
                    
                    <?php if(session('success')): ?>
                        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
                            <p class="font-bold">Başarılı!</p>
                            <p><?php echo e(session('success')); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('error')): ?>
                         <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                            <p class="font-bold">Hata!</p>
                            <p><?php echo e(session('error')); ?></p>
                        </div>
                    <?php endif; ?>

                    
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                        <table class="block sm:table min-w-full">
                            
                            <thead class="hidden sm:table-header-group">
                                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <th class="px-6 py-4">Başlık</th>
                                    <th class="px-6 py-4">Bölüm</th>
                                    <th class="px-6 py-4">Gönderim Tarihi</th>
                                    <th class="px-6 py-4">Durum</th>
                                    <th class="px-6 py-4 text-right">Aksiyonlar</th>
                                </tr>
                            </thead>
                            
                            <tbody class="block sm:table-row-group">
                                
                                <?php $__empty_1 = true; $__currentLoopData = $iaas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-indigo-50 transition-colors duration-200 group">
                                        
                                        
                                        <td class="flex justify-between items-start p-3 sm:table-cell sm:p-4 align-middle">
                                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Başlık:</span>
                                            <div class="text-right sm:text-left"><p class="text-gray-800 font-medium group-hover:text-indigo-700 transition-colors"><?php echo e($iaa->baslik); ?></p></div>
                                        </td>

                                        
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Bölüm:</span>
                                            <span class="text-right sm:text-left text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full"><?php echo e($iaa->bolum->ad ?? 'N/A'); ?></span>
                                        </td>

                                        
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Gönderim Tarihi:</span>
                                            <span class="text-right sm:text-left text-sm text-gray-500"><?php echo e($iaa->created_at->format('d.m.Y')); ?></span>
                                        </td>
                                        
                                        
                                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Durum:</span>
                                            <div class="w-full text-right sm:text-left">
                                                <?php switch($iaa->durum):
                                                    case ('Onay Bekliyor'): ?>
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"><?php echo e($iaa->durum); ?></span>
                                                        <?php break; ?>
                                                    <?php case ('Reddedildi'): ?>
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"><?php echo e($iaa->durum); ?></span>
                                                        <?php break; ?>
                                                    <?php default: ?>
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"><?php echo e($iaa->durum); ?></span>
                                                <?php endswitch; ?>
                                            </div>
                                        </td>

                                        
                                        <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                                <a href="<?php echo e(route('iaa.show', $iaa)); ?>" class="w-full sm:w-auto inline-flex justify-center text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm px-3 py-2 hover:bg-gray-50">Detay Gör</a>
                                                <?php if($iaa->durum == 'Onay Bekliyor'): ?>
                                                    <a href="<?php echo e(route('iaa.edit', $iaa)); ?>" class="w-full sm:w-auto inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm px-3 py-2 hover:bg-indigo-700">Düzenle</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="block sm:table-row">
                                        <td colspan="5" class="p-12 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-4">
                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>
                                                <div class="text-center">
                                                    <h4 class="text-lg font-semibold text-gray-600 mb-1">Henüz Bir Öneri Oluşturmadınız</h4>
                                                    <p class="text-gray-500">İlk iyileştirme önerinizi şimdi oluşturun!</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/iaa/index.blade.php ENDPATH**/ ?>