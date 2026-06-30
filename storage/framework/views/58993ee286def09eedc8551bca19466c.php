<?php $__env->startPush('pageTitle'); ?>
    Dış Avukat Listesi | 
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
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Dış Avukat Listesi')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-end mb-4">
                    <a href="<?php echo e(route('admin.dis_avukatlar.create')); ?>" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        + Yeni Dış Avukat Tanımla
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Avukat Adı</th>
                                <th class="py-3 px-6 text-left">E-posta</th>
                                <th class="py-3 px-6 text-left">Kayıt Tarihi</th>
                                <th class="py-3 px-6 text-center">Durum</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            
                            <?php $__empty_1 = true; $__currentLoopData = $lawyers ?? $avukatlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avukat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left whitespace-nowrap font-bold"><?php echo e($avukat->name); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo e($avukat->email); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo e($avukat->created_at->format('d.m.Y')); ?></td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Aktif</span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">Henüz tanımlı dış avukat yok.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/dis_avukatlar/index.blade.php ENDPATH**/ ?>