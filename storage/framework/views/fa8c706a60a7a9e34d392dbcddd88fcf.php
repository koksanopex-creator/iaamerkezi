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
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                <?php echo e(__('Bölüm Yönetimi')); ?>

            </h2>
            <a href="<?php echo e(route('admin.bolumler.create')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition-all duration-300 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Yeni Bölüm Ekle
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500 flex items-center justify-between transition hover:-translate-y-1 hover:shadow-md">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Toplam Bölüm</p>
                        <p class="mt-1 text-3xl font-extrabold text-gray-900"><?php echo e($totalBolumCount); ?></p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-full text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>

                
                <?php $__currentLoopData = $categoryStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                     <?php
                        $colorMap = [
                            ['border-green-500', 'bg-green-50', 'text-green-600'],
                            ['border-blue-500', 'bg-blue-50', 'text-blue-600'],
                            ['border-orange-500', 'bg-orange-50', 'text-orange-600'],
                            ['border-purple-500', 'bg-purple-50', 'text-purple-600'],
                            ['border-pink-500', 'bg-pink-50', 'text-pink-600'],
                            ['border-teal-500', 'bg-teal-50', 'text-teal-600'],
                        ];
                        [$borderColor, $bgColor, $textColor] = $colorMap[$loop->index % count($colorMap)];
                     ?>
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 <?php echo e($borderColor); ?> flex items-center justify-between transition hover:-translate-y-1 hover:shadow-md">
                        <div>
                             <p class="text-xs font-bold text-gray-400 uppercase tracking-wider"><?php echo e($stat->ad); ?></p>
                            <p class="mt-1 text-3xl font-extrabold text-gray-900"><?php echo e($stat->bolumler_count); ?></p>
                        </div>
                        <div class="p-3 <?php echo e($bgColor); ?> rounded-full <?php echo e($textColor); ?>">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                 <form method="GET" action="<?php echo e(route('admin.bolumler.index')); ?>" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                     
                     <div class="md:col-span-5">
                         <label for="ad" class="block text-xs font-semibold text-gray-500 uppercase mb-1">BÖLÜM ARA</label>
                         <div class="relative">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                             </div>
                             <input type="text" name="ad" id="ad" value="<?php echo e(request('ad')); ?>" 
                                 class="pl-10 block w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 transition transaction-duration-200" 
                                 placeholder="Bölüm adı yazın...">
                         </div>
                     </div>

                     
                     <div class="md:col-span-3">
                         <label for="bolum_kategori_id" class="block text-xs font-semibold text-gray-500 uppercase mb-1">KATEGORİ</label>
                         <select name="bolum_kategori_id" id="bolum_kategori_id" 
                            class="block w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 transition">
                            <option value="">Tümü</option>
                            <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kategori->id); ?>" <?php echo e(request('bolum_kategori_id') == $kategori->id ? 'selected' : ''); ?>>
                                    <?php echo e($kategori->ad); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         </select>
                     </div>

                     
                     <div class="md:col-span-2">
                        <label for="sort_machines" class="block text-xs font-semibold text-gray-500 uppercase mb-1">SIRALAMA</label>
                        <select name="sort_machines" id="sort_machines" 
                           class="block w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-indigo-500 transition">
                           <option value="">Varsayılan</option>
                           <option value="desc" <?php echo e(request('sort_machines') == 'desc' ? 'selected' : ''); ?>>En Çok Makine</option>
                           <option value="asc" <?php echo e(request('sort_machines') == 'asc' ? 'selected' : ''); ?>>En Az Makine</option>
                        </select>
                    </div>

                    
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-800 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 shadow-sm flex justify-center items-center gap-2">
                            <span>Filtrele</span>
                        </button>
                        <?php if(request()->anyFilled(['ad', 'bolum_kategori_id', 'sort_machines'])): ?>
                            <a href="<?php echo e(route('admin.bolumler.index')); ?>" class="flex items-center justify-center w-12 bg-gray-200 text-gray-600 rounded-lg hover:bg-gray-300 transition" title="Temizle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                 </form>
            </div>

            
            <?php if(session('success')): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg shadow-sm flex items-center justify-between" role="alert">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium"><?php echo e(session('success')); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                <?php $__empty_1 = true; $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col h-full group">
                        
                        
                        <div class="p-6 pb-2 flex justify-between items-start">
                            
                            <div class="flex-shrink-0">
                                <?php if($bolum->logo_yolu): ?>
                                    <img src="<?php echo e(Storage::url($bolum->logo_yolu)); ?>" alt="<?php echo e($bolum->ad); ?>" class="h-16 w-16 rounded-2xl object-cover border-2 border-gray-50 shadow-sm">
                                <?php else: ?>
                                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-sm">
                                        <?php echo e(substr($bolum->ad, 0, 1)); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            
                            <div class="flex flex-col items-end gap-2">
                                <?php if($bolum->kategori): ?>
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full uppercase tracking-wider">
                                        <?php echo e($bolum->kategori->ad); ?>

                                    </span>
                                <?php endif; ?>
                                <span class="flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?php echo e($bolum->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'); ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?php echo e($bolum->is_active ? 'bg-green-500' : 'bg-red-500'); ?>"></span>
                                    <?php echo e($bolum->is_active ? 'AKTİF' : 'PASİF'); ?>

                                </span>
                            </div>
                        </div>

                        
                        <div class="p-6 pt-2 flex-grow">
                             <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2 min-h-[3.5rem] mb-4" title="<?php echo e($bolum->ad); ?>">
                                <?php echo e($bolum->ad); ?>

                             </h3>

                             <div class="space-y-4">
                                
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">LİDERLER</p>
                                    <?php if($bolum->users->isNotEmpty()): ?>
                                        <div class="flex flex-wrap gap-2">
                                            <?php $__currentLoopData = $bolum->users->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                 <a href="<?php echo e(route('profile.show', $lider->id)); ?>" class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 rounded-lg p-1.5 pr-3 transition border border-transparent hover:border-gray-200" title="<?php echo e($lider->name); ?>">
                                                     <?php if($lider->profile_photo_path): ?>
                                                        <img src="<?php echo e(Storage::url($lider->profile_photo_path)); ?>" class="w-6 h-6 rounded-full object-cover">
                                                     <?php else: ?>
                                                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] text-indigo-700 font-bold">
                                                            <?php echo e(substr($lider->name, 0, 1)); ?>

                                                        </div>
                                                     <?php endif; ?>
                                                     <span class="text-xs font-medium text-gray-700 truncate max-w-[80px]"><?php echo e($lider->name); ?></span>
                                                 </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($bolum->users->count() > 3): ?>
                                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-xs font-medium text-gray-500" title="Diğer liderler">
                                                    +<?php echo e($bolum->users->count() - 3); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs italic flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            Atanmamış
                                        </span>
                                    <?php endif; ?>
                                </div>

                                 
                                 <div class="grid grid-cols-2 gap-2">
                                     <?php if($bolum->has_machines): ?>
                                         <div class="flex flex-col bg-gray-50 rounded-lg p-2 text-center">
                                             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Makine</span>
                                             <div class="flex items-center justify-center gap-1 font-bold text-gray-800 text-sm">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                  </svg>
                                                 <?php echo e($bolum->machines_count); ?>

                                             </div>
                                         </div>
                                     <?php endif; ?>

                                     <?php if($bolum->sikayetler_count > 0 || in_array($bolum->kategori->ad ?? '', ['Üretim', 'Kalite', 'Sevkiyat'])): ?>
                                         <a href="<?php echo e(route('admin.bolumler.dashboard', $bolum->id)); ?>#sikayetler" class="flex flex-col bg-red-50 rounded-lg p-2 text-center <?php echo e(!$bolum->has_machines ? 'col-span-2' : ''); ?> hover:bg-red-100 transition duration-200 cursor-pointer group">
                                             <span class="text-[10px] font-bold text-red-400 uppercase tracking-wide group-hover:text-red-500">Şikayet</span>
                                             <div class="flex items-center justify-center gap-1 font-bold text-red-700 text-sm group-hover:text-red-800">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                  </svg>
                                                 <?php echo e($bolum->sikayetler_count); ?>

                                             </div>
                                         </a>
                                     <?php endif; ?>
                                 </div>
                             </div>
                        </div>

                        
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                            <a href="<?php echo e(route('admin.bolumler.dashboard', $bolum)); ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1 group-hover/link">
                                Yönetim Paneli
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover/link:translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('admin.bolumler.edit', $bolum)); ?>" class="text-gray-400 hover:text-indigo-600 transition p-2 hover:bg-white rounded-lg shadow-sm hover:shadow" title="Düzenle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                <form action="<?php echo e(route('admin.bolumler.destroy', $bolum)); ?>" method="POST" onsubmit="return confirm('Bu bölümü silmek istediğinizden emin misiniz?');" class="inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-white rounded-lg shadow-sm hover:shadow" title="Sil">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
                        <div class="bg-white rounded-full p-8 shadow-sm mb-6 border border-gray-100">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Henüz Bölüm Bulunamadı</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-8">Arama kriterlerinize uygun bir bölüm yok veya henüz sisteme bölüm eklenmemiş.</p>
                        <a href="<?php echo e(route('admin.bolumler.index')); ?>" class="text-indigo-600 hover:text-indigo-800 font-semibold border-b-2 border-indigo-100 hover:border-indigo-500 transition-colors pb-1">Filtreleri Temizle</a>
                    </div>
                <?php endif; ?>
            </div>

            
             <div class="mt-8">
                <?php echo e($bolumler->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/bolumler/index.blade.php ENDPATH**/ ?>