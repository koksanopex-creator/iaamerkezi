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
    <?php $__env->startPush('pageTitle'); ?>Kullanıcı Rehberi | <?php $__env->stopPush(); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Kullanıcı Rehberi')); ?>

            </h2>
            
            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold">
                Toplam: <?php echo e($totalUserCount); ?> Kişi
            </span>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <?php if($canSeeCustomers): ?>
                <div class="mb-8 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="<?php echo e(route('user-directory.index', ['tab' => 'personel', 'search' => $search])); ?>" 
                           class="<?php echo e($activeTab === 'personel' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-all duration-200">
                            <i class="fas fa-users-cog mr-2"></i>Şirket Personeli
                        </a>
                        <a href="<?php echo e(route('user-directory.index', ['tab' => 'musteri', 'search' => $search])); ?>" 
                           class="<?php echo e($activeTab === 'musteri' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-all duration-200">
                            <i class="fas fa-user-tie mr-2"></i>Müşteri Temsilcileri
                        </a>
                    </nav>
                </div>
            <?php endif; ?>

            
            <div class="mb-8 space-y-4">
                <form method="GET" action="<?php echo e(route('user-directory.index')); ?>" id="searchForm" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <input type="hidden" name="tab" value="<?php echo e($activeTab); ?>">
                    
                    
                    <div class="lg:col-span-5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="<?php echo e($search); ?>" onchange="this.form.submit()" placeholder="<?php echo e($activeTab === 'musteri' ? 'Müşteri ismi veya e-posta ara...' : 'İsim, E-posta veya Bölüm ara...'); ?>" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm shadow-sm transition duration-150 ease-in-out">
                    </div>

                    
                    <?php if($activeTab === 'personel'): ?>
                    <div class="lg:col-span-3">
                        <select name="bolum_id" onchange="this.form.submit()" class="block w-full py-3 px-4 border border-gray-200 rounded-xl bg-white text-gray-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm shadow-sm font-medium">
                            <option value="">Tüm Bölümler</option>
                            <?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bolum->id); ?>" <?php echo e($bolumId == $bolum->id ? 'selected' : ''); ?>><?php echo e($bolum->ad); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="lg:col-span-4 flex items-center gap-2">
                        <input type="hidden" name="mavi_yaka" id="maviYakaInput" value="<?php echo e($isMaviYaka ? '1' : '0'); ?>">
                        <button type="button" 
                                onclick="document.getElementById('maviYakaInput').value = (document.getElementById('maviYakaInput').value === '1' ? '0' : '1'); this.form.submit();"
                                class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-xl text-sm font-bold border-2 transition-all <?php echo e($isMaviYaka ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-blue-600 border-blue-100 hover:border-blue-400 hover:bg-blue-50'); ?>">
                            <i class="fas fa-hard-hat mr-2"></i>Mavi Yaka Göster
                        </button>
                        
                        <?php if($search || $isMaviYaka || $bolumId): ?>
                            <a href="<?php echo e(route('user-directory.index', ['tab' => $activeTab])); ?>" class="p-3 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all" title="Filtreleri Temizle">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="lg:col-span-7 flex justify-end items-center">
                        <?php if($search): ?>
                            <a href="<?php echo e(route('user-directory.index', ['tab' => $activeTab])); ?>" class="inline-flex items-center px-4 py-2 text-sm text-gray-500 hover:text-red-500 transition-all">
                                <i class="fas fa-times-circle mr-2"></i>Filtreleri Temizle
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('profile.show', $user->id)); ?>" class="group block <?php echo e($user->is_mavi_yaka ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-100'); ?> rounded-xl shadow-sm hover:shadow-md border overflow-hidden transition-all duration-200 transform hover:-translate-y-1">
                        <div class="p-6 text-center">
                            
                            <div class="mx-auto h-20 w-20 rounded-full bg-gradient-to-br <?php echo e($user->is_mavi_yaka ? 'from-blue-400 to-blue-600' : ($user->is_personnel ? 'from-indigo-500 to-purple-600' : 'from-emerald-500 to-teal-600')); ?> p-0.5 shadow-md group-hover:shadow-lg transition-all">
                                <?php if($user->profile_photo_path): ?>
                                    <img class="h-full w-full rounded-full object-cover border-2 border-white" src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>" alt="<?php echo e($user->name); ?>">
                                <?php else: ?>
                                    <div class="h-full w-full rounded-full bg-white flex items-center justify-center text-2xl font-bold <?php echo e($user->is_mavi_yaka ? 'text-blue-600' : ($user->is_personnel ? 'text-indigo-600' : 'text-emerald-600')); ?> uppercase">
                                        <?php echo e(substr($user->name, 0, 1)); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="mt-4 text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors"><?php echo e($user->name); ?></h3>
                            <p class="text-sm text-gray-500 truncate"><?php echo e($user->email); ?></p>
                            
                            <div class="mt-3 flex flex-wrap justify-center gap-2">
                                <?php if($user->is_mavi_yaka): ?>
                                    <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-600 text-white shadow-sm">
                                        <i class="fas fa-hard-hat mr-1"></i>Mavi Yaka
                                    </span>
                                <?php endif; ?>

                                <?php if($user->is_personnel && !$user->is_mavi_yaka): ?>
                                    <?php 
                                        $rolesToDisplay = auth()->user()->hasRole('Superadmin') ? $user->roles->pluck('name') : $user->getPublicRoleNames();
                                    ?>
                                    <?php $__currentLoopData = $rolesToDisplay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100">
                                            <?php echo e($roleName); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($rolesToDisplay->isEmpty()): ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                            Personel
                                        </span>
                                    <?php endif; ?>
                                <?php elseif(!$user->is_personnel): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">
                                        Müşteri
                                    </span>
                                <?php endif; ?>
                                
                                <?php if($user->bolum): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                                        <?php echo e($user->bolum->ad); ?>

                                    </span>
                                <?php endif; ?>
                                <?php if($user->customer): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-50 text-amber-600 border border-amber-100">
                                        <?php echo e($user->customer->ad); ?>

                                    </span>
                                <?php endif; ?>
                            </div>

                            
                            <?php if($user->is_personnel && !$user->hasRole('Superadmin')): ?>
                                <div class="mt-4 border-t border-gray-100 pt-3">
                                    <div class="text-xs font-bold text-amber-500 uppercase tracking-wider">PUAN</div>
                                    <div class="text-xl font-black text-gray-800"><?php echo e(number_format($user->toplam_puan, 0)); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900"><?php echo e($activeTab === 'musteri' ? 'Müşteri Bulunamadı' : 'Kullanıcı Bulunamadı'); ?></h3>
                        <p class="mt-1 text-sm text-gray-500">Arama kriterlerinize uygun bir sonuç yok.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-6">
                <?php echo e($users->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/user-directory/index.blade.php ENDPATH**/ ?>