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
    
    <div class="relative bg-gradient-to-r from-indigo-900 to-blue-800 pb-32 overflow-hidden shadow-xl">
        <div class="relative max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">Profilim</h1>
            <p class="mt-2 text-xl text-indigo-200">Hoşgeldiniz, <?php echo e($user->name); ?></p>
            <p class="mt-1 text-sm text-indigo-300 opacity-80">Son İşlem: <?php echo e($user->updated_at->format('d.m.Y H:i')); ?></p>
        </div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12">
        
        
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="sm:flex sm:items-center sm:justify-between p-6 sm:p-8">
                <div class="sm:flex sm:items-center">
                    <div class="flex-shrink-0">
                        <?php if($user->profile_photo_path): ?>
                            <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg" src="<?php echo e(asset('storage/' . $user->profile_photo_path)); ?>" alt="<?php echo e($user->name); ?>">
                        <?php else: ?>
                            <div class="h-24 w-24 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 p-1 shadow-lg">
                                <div class="h-full w-full rounded-full bg-white flex items-center justify-center text-3xl font-bold text-indigo-600 uppercase">
                                    <?php echo e(substr($user->name, 0, 1)); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6 text-center sm:text-left">
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo e($user->name); ?></h2>
                        <p class="text-sm font-medium text-gray-500"><?php echo e($user->email); ?></p>
                        <?php if($user->telefon): ?>
                            <p class="text-sm text-gray-400 mt-1 flex items-center justify-center sm:justify-start gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <?php echo e($user->telefon); ?>

                            </p>
                        <?php endif; ?>
                        <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <?php echo e($user->roles->first()->name ?? 'Kullanıcı'); ?>

                            </span>

                            
                            <a href="<?php echo e(route('profile.show', $user->id)); ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors border border-gray-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Herkese Açık Profili Gör
                            </a>
                        </div>
                    </div>
                </div>
                
                
                <div class="mt-5 sm:mt-0 text-center">
                    <a href="<?php echo e(route('kullanici.puanlari', $user->id)); ?>" class="inline-block p-4 rounded-xl bg-gradient-to-br from-yellow-50 to-amber-50 border border-amber-200 shadow-sm hover:shadow-md transition-all transform hover:scale-105 cursor-pointer">
                        <p class="text-xs font-bold text-amber-600 uppercase tracking-wide">TOPLAM PUAN</p>
                        <p class="text-3xl font-extrabold text-amber-500 mt-1"><?php echo e(number_format($user->toplam_puan ?? 0, 0, ',', '.')); ?></p>
                        <span class="text-[10px] text-amber-400 block mt-1">Detaylar &rarr;</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50 grid grid-cols-3 divide-x divide-gray-200">
                <div class="p-4 text-center">
                    <span class="block text-xl font-bold text-gray-800"><?php echo e($tamamlananProjeSayisi); ?></span>
                    <span class="block text-xs text-gray-500 font-medium uppercase">Tamamlanan</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-xl font-bold text-gray-800"><?php echo e($aktifProjeSayisi); ?></span>
                    <span class="block text-xs text-gray-500 font-medium uppercase">Aktif Görev</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-xl font-bold text-gray-800"><?php echo e($takimlar->count()); ?></span>
                    <span class="block text-xs text-gray-500 font-medium uppercase">Takım</span>
                </div>
            </div>
        </div>

        
        <div x-data="{ activeTab: 'timeline' }" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <nav class="space-y-2 bg-white rounded-xl shadow-sm p-2">
                    <button @click="activeTab = 'timeline'" :class="activeTab === 'timeline' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Son Aktiviteler
                    </button>
                    <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Projelerim (<?php echo e($kullaniciProjeleri->count()); ?>)
                    </button>
                    <button @click="activeTab = 'teams'" :class="activeTab === 'teams' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Takımlarım
                    </button>
                    <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'" class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Hesap Ayarları
                    </button>
                </nav>
            </div>

            <div class="lg:col-span-2">
                
                
                <div x-show="activeTab === 'timeline'" x-transition class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Son Aktiviteler</h3>
                    <ul class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $sonAktiviteler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="py-4">
                                <div class="flex space-x-3">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 font-medium"><?php echo e($log->eylem); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($log->aciklama); ?></p>
                                        <span class="text-xs text-gray-400 block mt-1"><?php echo e($log->created_at->diffForHumans()); ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="py-4 text-center text-gray-500 text-sm">Henüz bir aktivite yok.</li>
                        <?php endif; ?>
                    </ul>
                </div>

                
                <div x-show="activeTab === 'projects'" x-transition style="display: none;" class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Dahil Olduğum Projeler</h3>
                    <div class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $kullaniciProjeleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                                <div>
                                    <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>" class="font-bold text-indigo-600 hover:underline">
                                        <?php echo e($proje->baslik); ?>

                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">Takım: <?php echo e($proje->atananTakim->ad ?? '-'); ?></p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo e($proje->durum == 'Tamamlandı' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                    <?php echo e($proje->durum); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-center text-gray-500">Henüz bir projede yer almıyorsunuz.</p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div x-show="activeTab === 'teams'" x-transition style="display: none;" class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Takımlarım</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <?php $__empty_1 = true; $__currentLoopData = $takimlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="p-4 border rounded-lg flex items-center justify-between hover:bg-gray-50">
                                <div>
                                    <p class="font-bold text-gray-900"><?php echo e($takim->ad); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e($takim->lider ? $takim->lider->name . ' (Lider)' : ''); ?></p>
                                </div>
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold"><?php echo e(substr($takim->ad, 0, 1)); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center text-gray-500 text-sm">Henüz bir takıma dahil değilsiniz.</div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div x-show="activeTab === 'settings'" x-transition style="display: none;">
                    <div class="space-y-6">
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <?php echo $__env->make('profile.partials.update-profile-information-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <?php echo $__env->make('profile.partials.update-password-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/profile/edit.blade.php ENDPATH**/ ?>