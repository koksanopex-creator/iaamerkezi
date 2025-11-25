<?php
    // Yetki kontrolü: Şikayet sekmesini kimler görebilir?
    $sikayetGormeYetkisi = $user->hasRole([
        'Superadmin', 
        'Müşteri Şikayeti Kurulu', 
        'Müşteri Şikayeti Çözüm Lideri', 
        'Bölüm Kalite Yöneticisi'
    ]);
?>

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
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    
    <?php echo $__env->make('profile.partials.show.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-12">
        
        
        <?php echo $__env->make('profile.partials.show.stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div x-data="{ activeTab: '<?php echo e(session('active_tab', request('tab', 'performans'))); ?>' }" class="bg-white rounded-2xl shadow-xl overflow-hidden min-h-[600px]">
            
            
            <?php echo $__env->make('profile.partials.show.tabs-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="p-6 bg-gray-50 min-h-[500px]">
                
                <?php echo $__env->make('profile.partials.show.tab-performance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                
                <?php if($sikayetGormeYetkisi): ?>
                    <?php echo $__env->make('profile.partials.show.tab-complaints', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                
                <?php echo $__env->make('profile.partials.show.tab-comments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                
                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                    <?php echo $__env->make('profile.partials.show.tab-security', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

    
    <?php echo $__env->make('profile.partials.show.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/profile/show.blade.php ENDPATH**/ ?>