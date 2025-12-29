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
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">
                    <?php echo e($takim->ad); ?>

                </h2>
                <p class="text-gray-600 mt-1 font-medium">Takım üyelerinizi ve projelerinizi buradan yönetin.</p>
            </div>
            <a href="<?php echo e(route('takimlar.index')); ?>" class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Tüm Takımlara Geri Dön
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    
    <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            
            
            
            <div class="lg:col-span-2 space-y-6">
                
                
                <?php if(session('success')): ?> 
                    <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded-xl" role="alert">
                        <p class="font-semibold"><?php echo e(session('success')); ?></p>
                    </div> 
                <?php endif; ?>
                <?php if(session('error')): ?> 
                    <div class="bg-red-100 border border-red-200 text-red-800 p-4 rounded-xl" role="alert">
                        <p class="font-semibold"><?php echo e(session('error')); ?></p>
                    </div> 
                <?php endif; ?>
                
                
                <?php if(Auth::id() === $takim->lider_user_id && $gelenIstekler->isNotEmpty()): ?>
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                Gelen Katılma İstekleri (<?php echo e($gelenIstekler->count()); ?>)
                            </h3>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            <?php $__currentLoopData = $gelenIstekler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $istek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="p-4 sm:p-6 hover:bg-gray-50/70 flex flex-col sm:flex-row items-start sm:items-center justify-between">
                                <div class="flex items-center mb-3 sm:mb-0">
                                        
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <a href="<?php echo e(route('profile.show', $istek->davetEden->id)); ?>" target="_blank">
                                                <?php if($istek->davetEden->profile_photo_path): ?>
                                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200 hover:border-indigo-500 transition-colors" src="<?php echo e(asset('storage/' . $istek->davetEden->profile_photo_path)); ?>" alt="<?php echo e($istek->davetEden->name); ?>">
                                                <?php else: ?>
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-indigo-100 transition-colors">
                                                        <span class="text-sm font-bold text-gray-600 hover:text-indigo-700"><?php echo e(Str::substr($istek->davetEden->name, 0, 1)); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="ml-4">
                                            
                                            <p class="font-semibold text-gray-800">
                                                <a href="<?php echo e(route('profile.show', $istek->davetEden->id)); ?>" target="_blank" class="hover:text-indigo-600 hover:underline transition-colors">
                                                    <?php echo e($istek->davetEden->name); ?>

                                                </a>
                                            </p>
                                            <p class="text-sm text-gray-500"><?php echo e($istek->davetEden->bolum->ad ?? 'Bölüm Atanmamış'); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                                        <form class="flex-1" action="<?php echo e(route('takimlar.istekKabulEt', $istek)); ?>" method="POST"> 
                                            <?php echo csrf_field(); ?> 
                                            <button type="submit" class="w-full px-3 py-2 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 text-xs font-semibold transition-colors">Kabul Et</button>
                                        </form>
                                        <form class="flex-1" action="<?php echo e(route('takimlar.istegiReddet', $istek)); ?>" method="POST"> 
                                            <?php echo csrf_field(); ?> 
                                            <button type="submit" class="w-full px-3 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 text-xs font-semibold transition-colors">Reddet</button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.368-.822a4 4 0 00-3.654 1.967 4 4 0 01-3.654-1.967l-2.368.822a2 2 0 00-1.022.547H3V19a2 2 0 002 2h14a2 2 0 002-2v-3.572h-2.572zM12 12a4 4 0 01-4-4h8a4 4 0 01-4 4z"></path></svg>
                            Takımın Aktif Projeleri (<?php echo e($aktifProjeler->count()); ?>)
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $aktifProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            
                            
                            <?php if($proje->durum == 'Revize Ediliyor'): ?>
                                <div class="bg-yellow-50/70 p-5 rounded-xl border border-yellow-200 shadow-sm">
                                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                        <div>
                                            
                                            <?php if($proje->musteriSikayeti): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 mb-1.5">
                                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 5a1 1 0 011 1v3a1 1 0 11-2 0V6a1 1 0 011-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                </svg>
                                                    Müşteri Şikayeti
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 mb-1.5">
                                                    IAA Önerisi
                                                </span>
                                            <?php endif; ?>
                                            
                                            <h4 class="font-bold text-amber-800 text-lg"><?php echo e($proje->baslik); ?></h4>
                                            
                                            <div class="flex items-center mt-2">
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800" style="white-space: nowrap;">
                                                    Revizyon Bekliyor
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 w-full sm:w-auto">
                                            
                                            <a href="<?php echo e(route('proje.workspace.show', $proje)); ?>" 
                                               class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 transition-all">
                                                Projeye Git
                                            </a>
                                        </div>
                                    </div>
                                    
                                    
                                    <?php $revisionLog = $proje->logs->first(); ?>
                                    <?php if(!empty($proje->yonetici_notu)): ?>
                                        <div class="mt-4 pt-3 border-t border-yellow-200">
                                            <p class="text-sm font-semibold text-gray-700">Revizyon Nedeni:</p>
                                            <p class="text-sm text-gray-600 mt-1 italic">"<?php echo e($proje->yonetici_notu); ?>"</p>
                                            <?php if($revisionLog && $revisionLog->user): ?>
                                                <p class="text-xs text-gray-500 mt-2 text-right font-medium">
                                                    <strong><?php echo e($revisionLog->user->name); ?></strong> tarafından (<?php echo e($revisionLog->created_at->format('d.m.Y H:i')); ?>)
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            
                            
                            <?php else: ?>
                                <div class="bg-blue-50/70 p-5 rounded-xl border border-blue-200 shadow-sm">
                                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                        <div>
                                            
                                            <?php if($proje->musteriSikayeti): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 mb-1.5">
                                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 5a1 1 0 011 1v3a1 1 0 11-2 0V6a1 1 0 011-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                    </svg>
                                                    Müşteri Şikayeti
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 mb-1.5">
                                                    IAA Önerisi
                                                </span>
                                            <?php endif; ?>
                                            
                                            <h4 class="font-bold text-indigo-800 text-lg"><?php echo e($proje->baslik); ?></h4>
                                            <p class="text-sm text-gray-600 mt-1">Çalışmalar devam ediyor.</p>
                                        </div>
                                        <div class="flex-shrink-0 w-full sm:w-auto">
                                            
                                            <a href="<?php echo e(route('proje.workspace.show', $proje)); ?>" 
                                               class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition-all">
                                                Projeye Git
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-center text-gray-500 py-8 font-medium">Bu takımın şu anda üzerinde çalıştığı bir proje bulunmuyor.</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tamamlanan Projeler (<?php echo e($tamamlananProjeler->count()); ?>)
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $tamamlananProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="bg-green-50/70 p-5 rounded-xl border border-green-200 flex justify-between items-center gap-4">
                                <div>
                                    <h4 class="font-bold text-green-800 text-lg"><?php echo e($proje->baslik); ?></h4>
                                    <p class="text-sm text-gray-600 mt-1">Onay Tarihi: <?php echo e(\Carbon\Carbon::parse($proje->onaylanma_tarihi)->format('d.m.Y')); ?></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-2xl font-black text-green-600"><?php echo e(number_format($proje->puan, 0)); ?></p>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">PUAN</p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-center text-gray-500 py-8 font-medium">Bu takım henüz bir proje tamamlamadı.</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Onay Bekleyen Tamamlanmış Projeler (<?php echo e($yoneticiOnayiBekleyenProjeler->count()); ?>)
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $yoneticiOnayiBekleyenProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="bg-amber-50/70 p-5 rounded-xl border border-amber-200 flex justify-between items-center gap-4">
                                <div>
                                    <a href="<?php echo e(route('proje.workspace.show', $proje)); ?>" class="font-bold text-amber-800 text-lg hover:underline" title="Projeyi İncele">
                                        <?php echo e($proje->baslik); ?>

                                    </a>
                                    <?php if(!empty($proje->yonetici_notu)): ?>
                                        <div class="mt-4 pt-3 border-t border-amber-200">
                                            <p class="text-sm font-semibold text-gray-700">Revizyon Nedeni:</p>
                                            <p class="text-sm text-gray-600 mt-1 italic">"<?php echo e($proje->yonetici_notu); ?>"</p>
                                        </div>
                                    <?php endif; ?>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Onaya Gönderilme: <?php echo e($proje->updated_at->format('d.m.Y')); ?>

                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-2xl font-black text-amber-600"><?php echo e(number_format($proje->puan, 0)); ?></p>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">PUAN</p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-center text-gray-500 py-8 font-medium">Bu takımın yönetici onayı bekleyen bir projesi bulunmuyor.</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Onay Bekleyen Proje Talepleri (<?php echo e($bekleyenTalepler->count()); ?>)
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if($bekleyenTalepler->isEmpty()): ?>
                            <p class="text-center text-gray-500 py-6">Yönetici onayında bekleyen bir proje talebi bulunmuyor.</p>
                        <?php else: ?>
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proje Başlığı</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Puan</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Talep Tarihi</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = $bekleyenTalepler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $talep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="<?php echo e(route('iaa.show', $talep->iaa_id)); ?>" class="text-gray-900 hover:text-indigo-600 hover:underline"><?php echo e($talep->baslik); ?></a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-indigo-600"><?php echo e(number_format($talep->puan, 0)); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e(\Carbon\Carbon::parse($talep->talep_tarihi)->format('d.m.Y')); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Onay Bekliyor</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-2.356M17 20H7m10 0v-2c0-1.657-1.343-3-3-3H7m10 0-3 3m0 0l-3-3m3 3V6a3 3 0 00-3-3H7a3 3 0 00-3 3v11m0 0h4m-4 0V6a3 3 0 013-3h4a3 3 0 013 3v3"></path></svg>
                            Takım Üyeleri & Davetler
                        </h3>
                    </div>
                    <?php if(Auth::id() === $takim->lider_user_id): ?>
                        <form action="<?php echo e(route('takimlar.davetGonder', $takim)); ?>" method="POST" class="p-6 bg-gray-50 flex flex-col sm:flex-row items-center gap-3 border-b border-gray-200">
                            <?php echo csrf_field(); ?>
                            <select name="user_id" class="w-full flex-grow border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Takıma davet etmek için bir kullanıcı seçin...</option>
                                <?php $__currentLoopData = $potansiyelUyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                    <option value="<?php echo e($uye->id); ?>"><?php echo e($uye->name); ?> (<?php echo e($uye->email); ?>)</option> 
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="submit" class="w-full sm:w-auto flex-shrink-0 bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition-colors">Davet Gönder</button>
                        </form>
                    <?php endif; ?>
                    <ul class="divide-y divide-gray-100">
                        <li class="px-6 py-3 bg-gray-50/70"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Mevcut Üyeler (<?php echo e($takim->uyeler->count()); ?>)</h4></li>
                        <?php $__empty_1 = true; $__currentLoopData = $takim->uyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="p-4 sm:p-6 hover:bg-gray-50/70 flex items-center justify-between">
                            <div class="flex items-center">
                                    
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <a href="<?php echo e(route('profile.show', $uye->id)); ?>" target="_blank">
                                            <?php if($uye->profile_photo_path): ?>
                                                <img class="h-10 w-10 rounded-full object-cover border border-gray-200 hover:border-indigo-500 transition-colors" src="<?php echo e(asset('storage/' . $uye->profile_photo_path)); ?>" alt="<?php echo e($uye->name); ?>">
                                            <?php else: ?>
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center hover:from-green-500 hover:to-blue-600 transition-colors">
                                                    <span class="text-sm font-bold text-white"><?php echo e(Str::substr($uye->name, 0, 1)); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    
                                    <div class="ml-4">
                                        
                                        <p class="font-semibold text-gray-800">
                                            <a href="<?php echo e(route('profile.show', $uye->id)); ?>" target="_blank" class="hover:text-indigo-600 hover:underline transition-colors">
                                                <?php echo e($uye->name); ?>

                                            </a>
                                            <?php if($uye->id === $takim->lider_user_id): ?> 
                                                <span class="ms-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Lider</span> 
                                            <?php endif; ?>
                                        </p>
                                        <p class="text-sm text-gray-500"><?php echo e($uye->bolum->ad ?? 'Bölüm Atanmamış'); ?></p>
                                        <p class="text-xs text-gray-400 mt-1"><?php echo e($uye->pivot->created_at->format('d.m.Y')); ?> tarihinde <?php echo e($uye->pivot->katilma_sekli); ?></p>
                                    </div>
                                </div>
                                <?php if(Auth::id() === $takim->lider_user_id && $uye->id !== Auth::id()): ?>
                                    <form action="<?php echo e(route('takimlar.uyeCikar', ['takim' => $takim, 'user' => $uye])); ?>" method="POST" onsubmit="return confirm('Bu üyeyi takımdan çıkarmak istediğinizden emin misiniz?');">
                                        <?php echo csrf_field(); ?> 
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold transition-colors">Çıkar</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="p-6 text-sm text-gray-500">Takımda henüz hiç üye yok.</li>
                        <?php endif; ?>
                        
                        <?php if(Auth::id() === $takim->lider_user_id && $gonderilenDavetler->isNotEmpty()): ?>
                            <li class="px-6 py-3 bg-gray-50/70 border-t border-gray-100"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Gönderilen Davetler (<?php echo e($gonderilenDavetler->count()); ?>)</h4></li>
                            <?php $__currentLoopData = $gonderilenDavetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $davet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="p-4 sm:p-6 hover:bg-gray-50/70 flex items-center justify-between">
                                <div class="flex items-center">
                                        
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <a href="<?php echo e(route('profile.show', $davet->davetEdilen->id)); ?>" target="_blank">
                                                <?php if($davet->davetEdilen->profile_photo_path): ?>
                                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200 hover:border-indigo-500 transition-colors" src="<?php echo e(asset('storage/' . $davet->davetEdilen->profile_photo_path)); ?>" alt="<?php echo e($davet->davetEdilen->name); ?>">
                                                <?php else: ?>
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center hover:bg-indigo-100 transition-colors">
                                                        <span class="text-sm font-bold text-gray-600 hover:text-indigo-700"><?php echo e(Str::substr($davet->davetEdilen->name, 0, 1)); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="ml-4">
                                            
                                            <p class="font-semibold text-gray-800">
                                                <a href="<?php echo e(route('profile.show', $davet->davetEdilen->id)); ?>" target="_blank" class="hover:text-indigo-600 hover:underline transition-colors">
                                                    <?php echo e($davet->davetEdilen->name); ?>

                                                </a>
                                            </p>
                                            <p class="text-sm text-gray-500"><?php echo e($davet->davetEdilen->bolum->ad ?? 'Bölüm Atanmamış'); ?></p>
                                        </div>
                                    </div>
                                    <form action="<?php echo e(route('takimlar.davetiIptalEt', $davet)); ?>" method="POST" onsubmit="return confirm('Bu daveti iptal etmek istediğinizden emin misiniz?');">
                                        <?php echo csrf_field(); ?> 
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-gray-500 hover:text-red-700 text-sm font-semibold transition-colors">İptal Et</button>
                                    </form>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            
            
            
            <div class="lg:col-span-1">
                
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                    <div class="p-6 bg-gradient-to-r from-indigo-600 to-blue-600">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-3xl font-bold text-indigo-600"><?php echo e(Str::substr($takim->ad, 0, 1)); ?></span>
                            </div>
                            <div>
                                <p class="text-xs text-indigo-200 uppercase font-bold tracking-widest">Takım Adı</p>
                                <h1 class="text-3xl font-black text-white tracking-tight"><?php echo e($takim->ad); ?></h1>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 text-center bg-gradient-to-br from-indigo-50 to-blue-50/70 border-b border-gray-100">
                        
                        <a href="<?php echo e(route('profile.show', $takim->lider->id)); ?>" target="_blank" class="group block">
                            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full mb-3 shadow-lg transition-transform transform group-hover:scale-105">
                                <?php if($takim->lider->profile_photo_path): ?>
                                    <img class="h-16 w-16 rounded-full object-cover border-4 border-white" src="<?php echo e(asset('storage/' . $takim->lider->profile_photo_path)); ?>" alt="<?php echo e($takim->lider->name); ?>">
                                <?php else: ?>
                                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-indigo-600 to-blue-600 flex items-center justify-center text-white font-bold text-xl border-4 border-white">
                                        <?php echo e(Str::substr($takim->lider->name, 0, 1)); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-indigo-700 uppercase font-bold tracking-widest mb-1">Takım Lideri</p>
                            <h2 class="text-xl font-bold text-gray-900 truncate group-hover:text-indigo-600 transition-colors underline-offset-2 group-hover:underline">
                                <?php echo e($takim->lider->name); ?>

                            </h2>
                        </a>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white border border-gray-200 p-4 rounded-xl text-center hover:shadow-md transition-shadow">
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-2">Üye Sayısı</p>
                                <p class="text-3xl font-black text-gray-800"><?php echo e($takim->uyeler->count()); ?></p>
                            </div>
                            <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl text-center shadow-lg hover:shadow-xl transition-shadow">
                                <p class="text-xs text-amber-800 uppercase font-bold tracking-wider mb-2">Takım Puanı</p>
                                <p class="text-3xl font-black text-amber-700"><?php echo e(number_format($takim->toplam_puan, 0)); ?></p>
                            </div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-4">
                            <?php
                                $detaylar = [
                                    ['baslik' => 'Amaç', 'deger' => $takim->amac],
                                    ['baslik' => 'Vizyon', 'deger' => $takim->vizyon],
                                    ['baslik' => 'Misyon', 'deger' => $takim->misyon],
                                ];
                            ?>
                            <?php $__currentLoopData = $detaylar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"><?php echo e($detay['baslik']); ?></h4>
                                    <p class="text-gray-700 text-sm leading-relaxed font-medium"><?php echo e($detay['deger'] ?? 'Belirtilmemiş'); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php if(Auth::id() === $takim->lider_user_id): ?>
                        <div class="p-6 bg-gray-50/50 border-t border-gray-100">
                            <a href="<?php echo e(route('takimlar.edit', $takim)); ?>" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z"></path></svg>
                                Takım Bilgilerini Düzenle
                            </a>
                        </div>
                    <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/takimlar/show.blade.php ENDPATH**/ ?>