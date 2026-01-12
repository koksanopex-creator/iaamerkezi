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
        
        <?php echo $__env->make('admin.arabuluculuk.parcalar.ust-baslik-ve-butonlar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
     <?php $__env->endSlot(); ?>

    <div class="py-8" x-data="{ activeTab: window.location.hash === '#files' ? 'dosyalar' : 'genel' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <?php echo $__env->make('admin.arabuluculuk.parcalar.uyarilar-ve-bildirimler', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <?php echo $__env->make('admin.arabuluculuk.parcalar.sekme-menusu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="bg-white shadow-lg rounded-b-xl min-h-[500px]">
                
                
                <div x-show="activeTab === 'genel'" class="p-6 space-y-6" x-transition>
                    
                    
                    <?php echo $__env->make('admin.arabuluculuk.parcalar.genel-istatistik-kartlari', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    
                    <?php echo $__env->make('admin.arabuluculuk.parcalar.genel-surec-sonuc-ekrani', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    
                    <?php echo $__env->make('admin.arabuluculuk.parcalar.genel-mutabakat-ve-atama', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    
                    <?php echo $__env->make('admin.arabuluculuk.parcalar.genel-anlasma-detaylari', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                
                <div x-show="activeTab === 'dosyalar'" class="p-6" style="display: none;" x-transition>
                    <?php echo $__env->make('admin.arabuluculuk.parcalar.sekme-dosyalar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                
                <?php if($case->board_required): ?>
                    <div x-show="activeTab === 'kurul'" class="p-6" style="display: none;" x-transition>
                        <?php echo $__env->make('admin.arabuluculuk.parcalar.sekme-kurul-degerlendirme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endif; ?>

                
                <div x-show="activeTab === 'odeme'" class="p-6" style="display: none;" x-transition>
                    <?php echo $__env->make('admin.arabuluculuk.parcalar.sekme-finans-ve-odeme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                
                <div x-show="activeTab === 'log'" class="p-6" style="display: none;" x-transition>
                    <?php echo $__env->make('admin.arabuluculuk.parcalar.sekme-gecmis-log', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

            </div>
        </div>
    </div>

    
    <?php echo $__env->make('admin.arabuluculuk.parcalar.sayfa-scriptleri', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/show.blade.php ENDPATH**/ ?>