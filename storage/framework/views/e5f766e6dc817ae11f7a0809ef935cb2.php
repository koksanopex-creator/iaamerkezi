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
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                    <?php if(Auth::user()->hasRole('Superadmin')): ?>
                        <?php echo e(__('Yönetici Paneli')); ?>

                    <?php else: ?>
                        <?php echo e(__('Dashboard')); ?>

                    <?php endif; ?>
                </h2>
                <p class="text-gray-600 mt-1">
                    <?php if(Auth::user()->hasRole('Superadmin')): ?>
                        Sistem durumunu ve verileri yönetin
                    <?php else: ?>
                        Sistemdeki genel durumunuzu görüntüleyin
                    <?php endif; ?>
                </p>
            </div>
            <div class="hidden md:flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-500">Sistem Aktif</span>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            
            
            
            <?php if(isset($bekleyenProjeDavetleri) && $bekleyenProjeDavetleri->isNotEmpty()): ?>
                <div class="mb-8 bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl shadow-xl overflow-hidden animate-fade-in-down">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4 text-white">
                            <h3 class="text-lg font-bold flex items-center gap-2">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                </div>
                                Bekleyen Proje Davetleriniz (<?php echo e($bekleyenProjeDavetleri->count()); ?>)
                            </h3>
                            <span class="text-sm bg-white/20 px-3 py-1 rounded-full backdrop-blur-md">Lütfen yanıtlayınız</span>
                        </div>

                        <div class="space-y-3">
                            <?php $__currentLoopData = $bekleyenProjeDavetleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $davet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 transition hover:bg-white/15">
                                    
                                    
                                    <div class="flex items-start gap-4">
                                        <div class="hidden md:flex flex-shrink-0 w-12 h-12 bg-white rounded-full items-center justify-center text-indigo-600 font-bold text-lg shadow-sm">
                                            <?php echo e(substr($davet->baslik, 0, 1)); ?>

                                        </div>
                                        <div>
                                            <h4 class="text-white font-bold text-lg"><?php echo e($davet->baslik); ?></h4>
                                            <p class="text-indigo-100 text-sm mt-1 flex items-center gap-2">
                                                <span>Davet Eden:</span>
                                                <span class="font-semibold bg-indigo-800/50 px-2 py-0.5 rounded text-xs">
                                                    <?php echo e($davet->atananTakim->lider->name ?? 'Takım Lideri'); ?>

                                                </span>
                                                <span class="text-indigo-300">•</span>
                                                <span><?php echo e($davet->created_at->diffForHumans()); ?></span>
                                            </p>
                                        </div>
                                    </div>

                                    
                                    <div class="flex items-center gap-3 w-full md:w-auto">
                                        
                                        <form action="<?php echo e(route('iaa.davetYanitla', $davet->id)); ?>" method="POST" class="w-full md:w-auto">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="yanit" value="kabul">
                                            <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-white text-indigo-700 font-bold rounded-lg shadow-lg hover:bg-indigo-50 hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Kabul Et
                                            </button>
                                        </form>

                                        
                                        <form action="<?php echo e(route('iaa.davetYanitla', $davet->id)); ?>" method="POST" class="w-full md:w-auto">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="yanit" value="red">
                                            <button type="submit" onclick="return confirm('Bu proje davetini reddetmek istediğinize emin misiniz?')" class="w-full md:w-auto px-6 py-2.5 bg-red-500/20 border border-red-400/30 text-white font-semibold rounded-lg hover:bg-red-500/40 transition-all duration-200 flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Reddet
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            

            
            <?php if(!Auth::user()->hasRole('Superadmin')): ?>
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-2xl shadow-lg text-white mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-indigo-200 uppercase tracking-wider">Toplam Puanınız</p>
                            <p class="text-4xl font-black tracking-tight"><?php echo e(number_format(Auth::user()->toplam_puan, 0)); ?></p>
                        </div>
                        <a href="<?php echo e(route('puan-durumu')); ?>" class="text-indigo-200 hover:text-white transition-colors" title="Liderlik Tablosu">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

  

            <?php if(isset($stats)): ?>
                
                <?php if(Auth::user()->hasRole('Superadmin')): ?>
                    <?php echo $__env->make('dashboard.partials.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
                <?php elseif(Auth::user()->hasRole('Müşteri Şikayeti Kurulu')): ?>
                    <?php echo $__env->make('dashboard.partials.sikayet-kurulu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    
                    <?php echo $__env->make('dashboard.partials.standart-kullanici', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
                <?php elseif(Auth::user()->hasRole('Müşteri Şikayeti Çözüm Lideri')): ?>
                    <?php echo $__env->make('dashboard.partials.cozum-lideri', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    
                    <?php echo $__env->make('dashboard.partials.standart-kullanici', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
                <?php elseif(Auth::user()->hasRole('Bölüm Kalite Yöneticisi')): ?>
                    <?php echo $__env->make('dashboard.partials.bolum-yoneticisi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    
                
                <?php else: ?>
                    <?php echo $__env->make('dashboard.partials.standart-kullanici', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/dashboard.blade.php ENDPATH**/ ?>