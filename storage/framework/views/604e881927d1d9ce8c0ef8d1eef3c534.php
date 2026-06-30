<?php $__env->startPush('pageTitle'); ?>
    Katılma İsteklerim | 
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
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <?php echo e(__('Gelen Katılma İstekleri')); ?>

            </h2>
            
            <a href="<?php echo e(route('takimlar.index')); ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150 group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Takımlara Dön
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <?php if($istekler->isEmpty()): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-16 text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Her Şey Sakin</h3>
                    <p class="text-gray-500">Takımlarınıza katılmak için bekleyen yeni bir istek bulunmuyor.</p>
                </div>
            <?php else: ?>
                <div class="space-y-8">
                    <?php $__currentLoopData = $istekler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $istek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $user = $istek->davetEden;
                            // === PERFORMANS VE İŞ YÜKÜ VERİLERİ ===
                            // 1. Devam Eden Projeler (HATA DÜZELTİLDİ: 'iaas.durum' kullanıldı)
                            $aktifGorevler = $user->gorevliOlduguProjeler()
                                ->whereIn('iaas.durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor'])
                                ->with('musteriSikayeti') 
                                ->latest('iaas.updated_at')
                                ->take(3)
                                ->get();

                            $aktifGorevSayisi = $user->gorevliOlduguProjeler()
                                ->whereIn('iaas.durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor'])
                                ->count();

                            // 2. Tamamlanan Başarılı Projeler
                            $tamamlananSayisi = $user->gorevliOlduguProjeler()
                                ->where('iaas.durum', 'Tamamlandı')
                                ->count();
                        ?>

                        
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 relative">
                            
                            <div class="bg-gradient-to-r from-orange-50 to-white px-6 py-3 border-b border-orange-100 flex justify-between items-center rounded-t-2xl">
                                <div class="text-sm text-orange-800 font-medium flex items-center gap-2">
                                    <span class="flex h-2 w-2 rounded-full bg-orange-500 animate-pulse"></span>
                                    Bu istek <span class="font-bold">"<?php echo e($istek->takim->ad); ?>"</span> takımı için gönderildi.
                                </div>
                                <span class="text-xs text-gray-400 font-medium"><?php echo e($istek->created_at->diffForHumans()); ?></span>
                            </div>

                            <div class="p-6">
                                <div class="flex flex-col xl:flex-row gap-8">
                                    
                                    
                                    <div class="xl:w-1/3 border-b xl:border-b-0 xl:border-r border-gray-100 pb-6 xl:pb-0 xl:pr-6">
                                        <div class="flex items-start gap-5">
                                            
                                            <div class="relative flex-shrink-0">
                                                <a href="<?php echo e(route('profile.show', $user->id)); ?>" target="_blank">
                                                    <?php if($user->profile_photo_path): ?>
                                                        <img class="h-20 w-20 rounded-2xl object-cover border-2 border-gray-100 shadow-sm" src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>" alt="<?php echo e($user->name); ?>">
                                                    <?php else: ?>
                                                        <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-sm">
                                                            <?php echo e(substr($user->name, 0, 1)); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($user->isOnline()): ?>
                                                        <span class="absolute -bottom-1 -right-1 h-5 w-5 bg-green-500 border-4 border-white rounded-full" title="Şu an çevrimiçi"></span>
                                                    <?php endif; ?>
                                                </a>
                                            </div>

                                            <div>
                                                <a href="<?php echo e(route('profile.show', $user->id)); ?>" target="_blank" class="text-xl font-bold text-gray-900 hover:text-indigo-600 transition-colors flex items-center gap-2">
                                                    <?php echo e($user->name); ?>

                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                                <p class="text-sm text-gray-500 font-medium mt-1">
                                                    <?php echo e($user->bolum->ad ?? 'Bölüm Yok'); ?> 
                                                    <?php if($user->unvan): ?> <span class="mx-1 text-gray-300">|</span> <?php echo e($user->unvan); ?> <?php endif; ?>
                                                </p>
                                                
                                                
                                                <div class="flex items-center gap-2 mt-3">
                                                    <div class="bg-yellow-50 text-yellow-700 px-3 py-1 rounded-lg text-sm font-bold border border-yellow-100 flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        <?php echo e(number_format($user->toplam_puan)); ?> Puan
                                                    </div>
                                                    <div class="bg-green-50 text-green-700 px-3 py-1 rounded-lg text-sm font-bold border border-green-100 flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <?php echo e($tamamlananSayisi); ?> Bitmiş İş
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                Mevcut İşler (<?php echo e($aktifGorevSayisi); ?>)
                                            </h4>

                                            
                                            
                                            
                                            <div class="relative group">
                                                <div class="cursor-help flex items-center gap-1.5 px-2 py-1 bg-indigo-50 text-indigo-600 rounded text-xs font-bold border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                    <?php echo e($istek->takim->uyeler->count()); ?> Üye
                                                </div>
                                                
                                                
                                                <div class="absolute bottom-full right-0 mb-2 w-48 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pointer-events-none shadow-xl">
                                                    <div class="font-bold border-b border-gray-700 mb-1 pb-1 text-gray-300">Takım Üyeleri</div>
                                                    <ul class="max-h-32 overflow-y-auto custom-scrollbar space-y-1">
                                                        <?php $__currentLoopData = $istek->takim->uyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="flex items-center gap-2">
                                                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                                                                <?php echo e($uye->name); ?>

                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                    
                                                    <div class="absolute top-full right-4 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <?php if($aktifGorevler->count() > 0): ?>
                                            <div class="space-y-3">
                                                <?php $__currentLoopData = $aktifGorevler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gorev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 hover:border-indigo-200 transition-colors group/item">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <div class="flex items-center gap-2">
                                                                    <?php if($gorev->tur == 'sikayet' || $gorev->musteriSikayeti): ?>
                                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 border border-red-200">ŞİKAYET</span>
                                                                    <?php else: ?>
                                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-600 border border-blue-200">İAA</span>
                                                                    <?php endif; ?>
                                                                    <a href="<?php echo e(route('proje.workspace.show', $gorev->id)); ?>" target="_blank" class="text-sm font-semibold text-gray-800 hover:text-indigo-600 line-clamp-1">
                                                                        <?php echo e($gorev->baslik); ?>

                                                                    </a>
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1 pl-1">
                                                                    Durum: <span class="text-indigo-600 font-medium"><?php echo e($gorev->durum); ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($aktifGorevSayisi > 3): ?>
                                                    <div class="text-center">
                                                        <span class="text-xs font-medium text-gray-400 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                                                            +<?php echo e($aktifGorevSayisi - 3); ?> diğer görev daha var
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-green-50 border border-green-100 rounded-lg p-4 text-center">
                                                <p class="text-sm text-green-700 font-medium flex items-center justify-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Şu an üzerinde aktif bir iş yükü yok.
                                                </p>
                                                <p class="text-xs text-green-600 mt-1">Takımınıza hemen katkı sağlayabilir!</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <div class="xl:w-48 flex flex-col justify-center gap-3 border-t xl:border-t-0 xl:border-l border-gray-100 pt-6 xl:pt-0 xl:pl-6">
                                        <form action="<?php echo e(route('takimlar.istekKabulEt', $istek->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="w-full group/btn relative flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-md hover:from-emerald-600 hover:to-green-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                                <svg class="w-5 h-5 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Kabul Et
                                            </button>
                                        </form>

                                        <form action="<?php echo e(route('takimlar.istekReddet', $istek)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white text-red-600 border-2 border-red-100 rounded-xl font-bold hover:bg-red-50 hover:border-red-200 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Reddet
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
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
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/takimlar/isteklerim.blade.php ENDPATH**/ ?>