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
     <?php $__env->slot('header', null, []); ?> <h2 class="font-semibold text-xl text-gray-800 leading-tight"><?php echo e(__('Takım Davetlerim')); ?></h2> <?php $__env->endSlot(); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6 text-gray-900">
                <?php if(session('success')): ?> <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert"><?php echo e(session('success')); ?></div> <?php endif; ?>
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $davetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $davet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div>
                                <p><span class="font-bold"><?php echo e($davet->takim->lider->name); ?></span> sizi <span class="font-bold"><?php echo e($davet->takim->ad); ?></span> takımına davet ediyor.</p>
                                <p class="text-sm text-gray-500"><?php echo e($davet->created_at->diffForHumans()); ?></p>
                            </div>
                            <div class="flex space-x-2">
                                <form action="<?php echo e(route('takimlar.davetiKabulEt', $davet)); ?>" method="POST"> <?php echo csrf_field(); ?> <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Kabul Et</button></form>
                                <form action="<?php echo e(route('takimlar.davetiReddet', $davet)); ?>" method="POST"> <?php echo csrf_field(); ?> <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Reddet</button></form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-center text-gray-500">Bekleyen takım davetiniz bulunmamaktadır.</p>
                    <?php endif; ?>
                </div>
            </div></div>
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/takimlar/davetlerim.blade.php ENDPATH**/ ?>