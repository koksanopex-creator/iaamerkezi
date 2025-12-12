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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <a href="javascript:history.back()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100" title="Geri Dön">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
</a>
                Disiplin Dosyası #<?php echo e($case->id); ?>

            </h2>
            <div class="flex gap-2">
                
                <?php if($case->durum != 'Karar Verildi' && Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Bölüm Lideri'])): ?>
                    <a href="<?php echo e(route('admin.disiplin.edit', $case->id)); ?>" class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-100 border border-indigo-200">Düzenle</a>
                <?php endif; ?>
                <button onclick="window.print()" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 border border-gray-300">Yazdır / PDF</button>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <?php
                $durumRenk = match($case->durum) {
                    'Savunma Bekleniyor' => 'yellow',
                    'Yönetici Değerlendirmesi' => 'blue',
                    'Kurulda' => 'purple',
                    'Karar Verildi' => 'green',
                    'İptal' => 'red',
                    default => 'gray'
                };
                $durumMetni = match($case->durum) {
                    'Savunma Bekleniyor' => 'Personelden savunma bekleniyor.',
                    'Yönetici Değerlendirmesi' => 'Savunma girildi, yönetici onayı bekleniyor.',
                    'Karar Verildi' => 'Dosya kapatıldı ve karar kesinleşti.',
                    default => 'İşlem bekleniyor.'
                };
            ?>
            <div class="bg-<?php echo e($durumRenk); ?>-50 border-l-4 border-<?php echo e($durumRenk); ?>-500 p-4 mb-6 rounded-r shadow-sm flex justify-between items-center transition-all duration-500">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white rounded-full text-<?php echo e($durumRenk); ?>-600 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-<?php echo e($durumRenk); ?>-800 text-lg">Dosya Durumu: <?php echo e($case->durum); ?></p>
                        <p class="text-xs text-<?php echo e($durumRenk); ?>-600 font-semibold"><?php echo e($durumMetni); ?></p>
                    </div>
                </div>
            </div>

            
            <?php if($case->durum == 'Kurulda' && !Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])): ?>
                <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6 text-center mb-6">
                    <div class="inline-block p-3 bg-gray-200 rounded-full mb-3">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Disiplin Kurulu Değerlendirmesi</h3>
                    <p class="text-gray-600 mt-2">Dosyanız Disiplin Kurulu'na sevk edilmiştir. Kurul üyeleri tarafından incelenmektedir.</p>
                    <?php if($case->toplanti_tarihi): ?>
                        <p class="mt-3 text-sm font-bold text-indigo-600 bg-indigo-50 inline-block px-3 py-1 rounded">📅 Planlanan Toplantı: <?php echo e($case->toplanti_tarihi->format('d.m.Y H:i')); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                
                <div class="md:col-span-2 space-y-6">
                    
                    
                    <?php echo $__env->make('admin.disiplin.partials.case-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    
                    <?php echo $__env->make('admin.disiplin.partials.defense-section', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    
                    <?php echo $__env->make('admin.disiplin.partials.manager-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    
                    <?php echo $__env->make('admin.disiplin.partials.comments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                </div>

                
                <div class="space-y-6">
                    <?php echo $__env->make('admin.disiplin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

            </div> 

            
            <div class="w-full">
                <?php echo $__env->make('admin.disiplin.partials.council-room', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/show.blade.php ENDPATH**/ ?>