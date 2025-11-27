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
            <?php echo e(__('Takım Projeleri ve Talepleri')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    
    <div class="pt-6"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

                
                <a href="#aktif-projeler" class="block p-6 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-100 uppercase tracking-wider">Aktif Projeler</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($stats['aktif']); ?></p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </a>

                
                 <a href="#onay-bekleyen-talepler" class="block p-6 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-100 uppercase tracking-wider">Bekleyen Talepler</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($stats['talep']); ?></p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                             
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </a>

                 
                 <a href="#onay-bekleyen-tamamlanmis" class="block p-6 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-100 uppercase tracking-wider">Onay Bekleyenler</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($stats['onay_bekleyen_tamamlanmis']); ?></p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            
                             <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </a>

                
                 <a href="#tamamlanan-projeler" class="block p-6 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-emerald-100 uppercase tracking-wider">Tamamlanan Projeler</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($stats['tamamlanan']); ?></p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </a>
                
                 
                 <div class="block p-6 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg col-span-1 md:col-span-1 lg:col-span-1"> 
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-pink-100 uppercase tracking-wider">Toplam Kazanılan Puan</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e(number_format($stats['toplam_puan'], 0)); ?></p>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                           
                           <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        
            <div id="onay-bekleyen-talepler" class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 scroll-mt-20"> 
                 <div class="p-6 sm:p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Onay Bekleyen Talepler</h3>
                            <p class="text-gray-600">Yönetici onayına gönderilen proje talepleriniz.</p>
                        </div>
                    </div>
                    
                     <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
                        <table class="block sm:table min-w-full">
                            <thead class="hidden sm:table-header-group">
                                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <th class="px-6 py-4">Proje Başlığı</th>
                                    <th class="px-6 py-4">Talep Eden Takım</th>
                                    <th class="px-6 py-4">Talep Tarihi</th>
                                    <th class="px-6 py-4">Durum</th>         
                                    <th class="px-6 py-4 text-right">İşlem</th> 
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group">
                                <?php $__empty_1 = true; $__currentLoopData = $bekleyenTalepler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100">
                                    <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Proje:</span><span class="text-right sm:text-left font-medium text-gray-800"><?php echo e($talep->baslik); ?></span></td>
                                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span><span class="text-right sm:text-left text-sm text-gray-600"><?php echo e($talep->takim_adi); ?></span></td>
                                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span><span class="text-right sm:text-left text-sm text-gray-500"><?php echo e(\Carbon\Carbon::parse($talep->created_at)->format('d.m.Y')); ?></span></td>
                                    
                                    
                                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                        <span class="font-semibold text-sm text-gray-500 sm:hidden">Durum:</span>
                                        <div class="w-full text-right sm:text-left">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"><?php echo e(Str::ucfirst($talep->talep_durumu)); ?></span>
                                        </div>
                                    </td>
                                    
                                    
                                    <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                        <div class="flex justify-end">
                                            <a href="<?php echo e(route('iaa.show', $talep->iaa_id)); ?>" class="inline-flex justify-center text-sm font-semibold text-white bg-gray-600 px-3 py-2 rounded-md hover:bg-gray-700">İncele</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="block sm:table-row"><td colspan="5" class="p-12 text-center text-gray-500">Yönetici onayında bekleyen bir talebiniz bulunmamaktadır.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



        
            <div id="aktif-projeler" class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 scroll-mt-20"> 
                <div class="p-6 sm:p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        
                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Üstlenilen Projeler</h3>
                            <p class="text-gray-600">Takımlarınızın üzerinde çalıştığı talebi onaylanmış projeler.</p>
                        </div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
                        
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proje Başlığı</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atanan Takım</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $atanmisProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($proje->baslik); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($proje->atananTakim->ad ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo e($proje->durum); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="text-indigo-600 hover:text-indigo-900">Projeye Git</a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            Takımlarınızın üstlendiği bir proje bulunmamaktadır.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            

            
            <div id="onay-bekleyen-tamamlanmis" class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 scroll-mt-20"> 
        <div class="p-6 sm:p-8">
            <div class="flex items-center space-x-4 mb-6">
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center shadow-lg">
                    
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Onay Bekleyen Tamamlanmış Projeler</h3>
                    <p class="text-gray-600">Yönetici onayı veya revizyonu bekleyen tamamlanmış projeleriniz.</p>
                </div>
            </div>
            <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proje Başlığı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atanan Takım</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum / Not</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $onayBekleyenTamamlanmisProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                // Controller'da çektiğimiz logu alalım
                                $revisionLog = $proje->logs->first(); 
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($proje->baslik); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($proje->atananTakim->ad ?? 'N/A'); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php if($revisionLog): ?> 
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Revizyon Bekliyor
                                        </span>
                                        <?php if($proje->yonetici_notu): ?>
                                         <p class="text-xs text-gray-500 mt-1 italic">"<?php echo e($proje->yonetici_notu); ?>"</p>
                                        <?php endif; ?>
                                        <?php if($revisionLog->user): ?>
                                            <p class="text-xs text-gray-400 mt-1">(<?php echo e($revisionLog->user->name); ?> - <?php echo e($revisionLog->created_at->format('d.m.Y H:i')); ?>)</p>
                                        <?php endif; ?>
                                    <?php else: ?> 
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Yönetici Onayı Bekliyor
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="text-indigo-600 hover:text-indigo-900">Projeyi Gör</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    Onay bekleyen tamamlanmış bir projeniz bulunmamaktadır.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    

    
    <div id="tamamlanan-projeler" class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 scroll-mt-20"> 
        <div class="p-6 sm:p-8">
            <div class="flex items-center space-x-4 mb-6">
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                    
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18zm0 18a9 9 0 005.35-16.65M12 21a9 9 0 01-5.35-16.65"></path></svg> 
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Tamamlanan ve Puan Kazandıran Projeler</h3>
                    <p class="text-gray-600">Takımlarınızın başarıyla tamamladığı projeler.</p>
                </div>
            </div>
            <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proje Başlığı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamamlayan Takım</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Onay Tarihi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kazanılan Puan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $tamamlananProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="text-indigo-600 hover:text-indigo-800 hover:underline" title="Projeyi Görüntüle">
                                        <?php echo e($proje->baslik); ?>

                                    </a>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($proje->atananTakim->ad ?? 'N/A'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($proje->onaylanma_tarihi ? $proje->onaylanma_tarihi->format('d.m.Y') : '-'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                    +<?php echo e(number_format($proje->puan, 0)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    Takımlarınız henüz puan kazandıran bir proje tamamlamadı.
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/iaa/takim-projeleri.blade.php ENDPATH**/ ?>