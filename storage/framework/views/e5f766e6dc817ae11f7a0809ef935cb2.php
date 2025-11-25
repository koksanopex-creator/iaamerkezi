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