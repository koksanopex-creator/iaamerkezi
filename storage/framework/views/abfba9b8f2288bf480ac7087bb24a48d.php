<?php $__env->startPush('pageTitle'); ?>
    Takım Projeleri | 
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
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Takım Projeleri ve Yönetimi')); ?>

            </h2>
            <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Geri Dön
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    
    <div class="pt-6 pb-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-indigo-900 rounded-xl shadow-lg overflow-hidden relative">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-indigo-800 opacity-50"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-indigo-700 opacity-50"></div>
                <div class="p-6 relative z-10">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Bağlı Olduğum Takımlar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php $__empty_1 = true; $__currentLoopData = $katildigimTakimlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="bg-white/10 backdrop-blur-sm border border-indigo-500/30 rounded-lg p-4 hover:bg-white/20 transition duration-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-white text-lg"><?php echo e($takim->ad); ?></h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <?php if($takim->lider_user_id == auth()->id()): ?>
                                                <span class="bg-amber-500 text-white text-xs px-2 py-0.5 rounded font-bold">LİDER</span>
                                            <?php else: ?>
                                                <span class="bg-indigo-600 text-indigo-100 text-xs px-2 py-0.5 rounded">ÜYE</span>
                                            <?php endif; ?>
                                            <span class="text-xs text-indigo-200"><?php echo e(\Carbon\Carbon::parse($takim->pivot->created_at)->format('d.m.Y')); ?> tarihinden beri</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <span class="block text-2xl font-bold text-white"><?php echo e($takim->atanan_projeler_count); ?></span>
                                        <span class="text-[10px] uppercase text-indigo-200 tracking-wide">Proje</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-span-full text-center text-indigo-200 py-4">Henüz bir takıma dahil değilsiniz.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="pt-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <a href="#aktif-projeler" class="block p-6 bg-white border border-gray-100 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-blue-600 transition-colors">Aktif Projeler</p>
                            <p class="text-3xl font-black text-gray-800 mt-1 group-hover:text-blue-600"><?php echo e($stats['aktif']); ?></p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-full group-hover:bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </a>
                
                <a href="#onay-bekleyen-tamamlanmis" class="block p-6 bg-white border border-gray-100 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-red-600 transition-colors">Onay Bekleyenler</p>
                            <p class="text-3xl font-black text-gray-800 mt-1 group-hover:text-red-600"><?php echo e($stats['onay_bekleyen_tamamlanmis']); ?></p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-full group-hover:bg-red-100">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </a>
                
                <a href="#tamamlanan-projeler" class="block p-6 bg-white border border-gray-100 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-green-600 transition-colors">Tamamlanan</p>
                            <p class="text-3xl font-black text-gray-800 mt-1 group-hover:text-green-600"><?php echo e($stats['tamamlanan']); ?></p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-full group-hover:bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </a>
                
                <div class="block p-6 bg-white border border-gray-100 rounded-xl shadow-md group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bana Atanan Adımlar</p>
                            <p class="text-3xl font-black text-gray-800 mt-1"><?php echo e(count($banaAtananAdimlar)); ?></p>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-full">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(count($banaAtananAdimlar) > 0): ?>
    <div class="pb-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-red-50 border-l-8 border-red-500 p-6 shadow-md rounded-r-lg animate-pulse"> 
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-500" viewBox="0 0 20 20" fill="currentColor"> 
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-red-700">DİKKAT: EYLEM BEKLEYEN GÖREVLERİNİZ VAR!</h3> 
                        <div class="mt-3 text-base text-red-800">
                            <ul class="list-disc pl-5 space-y-2">
                                <?php $__currentLoopData = $banaAtananAdimlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gorev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <span class="font-semibold"><?php echo e($gorev->baslik); ?></span> projesinde 
                                        <span class="font-bold underline">"<?php echo e($gorev->adim_adi); ?>"</span> adımı işlem bekliyor.
                                        <a href="<?php echo e(route('proje.workspace.show', $gorev->iaa_id)); ?>" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700 ml-3 shadow-sm transition">
                                            GİT &rarr;
                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            
            <div id="onay-bekleyen-talepler" class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 scroll-mt-20">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 bg-yellow-400 rounded-full"></div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Onay Bekleyen Talepler</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Yönetici havuzunda onay bekleyen proje talepleriniz.</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">Proje Başlığı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Talep Eden Takım</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Talep Tarihi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Durum</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $bekleyenTalepler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-400"><?php echo e($loop->iteration); ?></td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo e($talep->baslik); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($talep->takim_adi); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e(\Carbon\Carbon::parse($talep->created_at)->format('d.m.Y')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <?php echo e(Str::ucfirst($talep->talep_durumu)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="<?php echo e(route('iaa.show', $talep->iaa_id)); ?>" class="text-indigo-600 hover:text-indigo-900 font-bold">İncele</a>
                                        <form action="<?php echo e(route('iaa.talebiGeriCek', $talep->iaa_id)); ?>" method="POST" onsubmit="return confirm('Talebinizi geri çekmek istediğinize emin misiniz?');" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Geri Çek</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Yönetici onayında bekleyen bir talebiniz bulunmamaktadır.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div id="aktif-projeler" class="bg-white shadow-xl sm:rounded-2xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Üstlenilen Projeler</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Takımlarınızın üzerinde çalıştığı talebi onaylanmış ve devam eden projeler.</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[250px]">Proje Başlığı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Takım</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Başlangıç</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Durum</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $atanmisProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-400"><?php echo e($loop->iteration); ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        <div class="flex flex-col">
                                            <span><?php echo e($proje->baslik); ?></span>
                                            <?php if($proje->musteriSikayeti): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800 mt-1 w-fit uppercase tracking-wide">Müşteri Şikayeti</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 mt-1 w-fit uppercase tracking-wide">İyileştirme (İAA)</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($proje->atananTakim->ad ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($proje->created_at->format('d.m.Y')); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo e($proje->durum); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="text-indigo-600 hover:text-indigo-900 font-bold">Projeye Git &rarr;</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">Takımlarınızın üstlendiği aktif bir proje bulunmamaktadır.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div id="onay-bekleyen-tamamlanmis" class="bg-white shadow-xl sm:rounded-2xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 bg-red-500 rounded-full"></div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Onay Bekleyenler</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Yönetici onayı veya revizyonu bekleyen tamamlanmış projeleriniz.</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[250px]">Proje Başlığı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Takım</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Onaya Gönderim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Durum</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $onayBekleyenTamamlanmisProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php 
                                    $revisionLog = $proje->logs->first(); 
                                    $onayTarihi = $revisionLog ? $revisionLog->created_at->format('d.m.Y H:i') : $proje->updated_at->format('d.m.Y H:i');
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-400"><?php echo e($loop->iteration); ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        <div class="flex flex-col">
                                            <span><?php echo e($proje->baslik); ?></span>
                                            <?php if($proje->musteriSikayeti): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800 mt-1 w-fit uppercase">Müşteri Şikayeti</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 mt-1 w-fit uppercase">İyileştirme</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($proje->atananTakim->ad ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($onayTarihi); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php if($revisionLog && str_contains($revisionLog->eylem, 'Revizyon')): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Revizyon Bekliyor</span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Yönetici Onayı</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="text-indigo-600 hover:text-indigo-900 font-bold">İncele &rarr;</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">Onay bekleyen projeniz yok.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div id="tamamlanan-projeler" class="bg-white shadow-xl sm:rounded-2xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 bg-green-500 rounded-full"></div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Tamamlanan Projeler</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Takımlarınızın başarıyla tamamladığı ve puan kazandıran projeler.</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[250px]">Proje Başlığı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Takım</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Bitiş Tarihi</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Puan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $tamamlananProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-400"><?php echo e($loop->iteration); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div class="flex flex-col">
                                            <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                                <?php echo e($proje->baslik); ?>

                                            </a>
                                            <?php if($proje->musteriSikayeti): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800 mt-1 w-fit uppercase">Müşteri Şikayeti</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 mt-1 w-fit uppercase">İyileştirme</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($proje->atananTakim->ad ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($proje->onaylanma_tarihi ? $proje->onaylanma_tarihi->format('d.m.Y') : '-'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">+<?php echo e(number_format($proje->puan, 0)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">Henüz tamamlanan proje yok.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/iaa/takim-projeleri.blade.php ENDPATH**/ ?>