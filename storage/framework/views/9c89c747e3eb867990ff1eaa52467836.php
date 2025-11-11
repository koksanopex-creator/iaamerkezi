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
            <?php echo e(__('İyileştirmeye Açık Alan Yönetimi')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <?php if(session('success')): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert"><p><?php echo e(session('success')); ?></p></div>
            <?php endif; ?>
            
            <?php echo $__env->make('admin.iaa-yonetim.partials.stats-cards', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            

            <?php echo $__env->make('admin.iaa-yonetim.partials.talep-alan-oneriler-table', [
                'iaas' => $talepAlanOneriler, 
                'title' => 'Talep Alan Öneriler', 
                'color' => 'blue'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('admin.iaa-yonetim.partials.onay-bekleyen-misafirler-table', [
                'iaas' => $onayBekleyenMisafirler, 
                'type' => 'onay', 
                'title' => 'Misafirlerden Gelen Öneriler', 
                'color' => 'yellow'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('admin.iaa-yonetim.partials.onay-bekleyen-kullanicilar-table', [
                'iaas' => $onayBekleyenKullanicilar, 
                'type' => 'onay', 
                'title' => 'Kayıtlı Kullanıcılardan Gelen Öneriler', 
                'color' => 'yellow'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('admin.iaa-yonetim.partials.yonetici-onayi-bekleyenler-table', [
                'iaas' => $yoneticiOnayiBekleyenler, 
                'title' => 'Onay Bekleyen Tamamlanmış Projeler', 
                'color' => 'purple'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('admin.iaa-yonetim.partials.atanmis-projeler-table', [
                'iaas' => $atanmisOlanlar, 
                'type' => 'atanmis', 
                'title' => 'Atanmış Projeler', 
                'color' => 'green'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('admin.iaa-yonetim.partials.havuzdaki-oneriler-table', [
                'iaas' => $havuzdakiler, 
                'type' => 'havuz', 
                'title' => 'Havuzdaki Öneriler', 
                'color' => 'gray'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('admin.iaa-yonetim.partials.reddedilen-oneriler-table', [
                'iaas' => $reddedilenler, 
                'type' => 'reddedilmis', 
                'title' => 'Reddedilen Öneriler', 
                'color' => 'red'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('admin.iaa-yonetim.partials.tamamlanmasi-reddedilen-projeler-table', [
                'iaas' => $tamamlanmasiReddedilenler,
                'title' => 'Tamamlanması Reddedilen Projeler'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            
            <?php echo $__env->make('admin.iaa-yonetim.partials.tamamlanmis-projeler-ozet-table', [
                'iaas' => $sonTamamlananlar,
                'title' => 'Son 5 Tamamlanan Proje',
                'color' => 'gray'
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>
    </div>

    
    <?php echo $__env->make('admin.iaa-yonetim.partials.all-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    
    <?php if($errors->any() && session('error_modal_id')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reddet-modal-<?php echo e(session('error_modal_id')); ?>' }));
        });
    </script>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/index.blade.php ENDPATH**/ ?>