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
            <?php echo e(__('Arabulucu İşlem Kayıtları (Loglar)')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-4">
                    <a href="<?php echo e(route('admin.arabulucular.index')); ?>" class="text-indigo-600 hover:underline">&larr; Listeye Dön</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">İşlemi Yapan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">İşlem Türü</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Etkilenen Arabulucu</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Detay</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 text-sm">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900"><?php echo e($log->user->name ?? 'Sistem'); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($log->ip_adres); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $renk = match($log->islem_turu) {
                                                'OLUŞTURMA' => 'bg-green-100 text-green-800',
                                                'DÜZENLEME' => 'bg-blue-100 text-blue-800',
                                                'SİLME' => 'bg-red-100 text-red-800',
                                                'DURUM DEĞİŞTİRME' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        ?>
                                        <span class="px-2 py-1 rounded text-xs font-bold <?php echo e($renk); ?>"><?php echo e($log->islem_turu); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo e($log->arabulucu->name ?? 'Silinmiş Kayıt'); ?>

                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <?php echo e(Str::limit($log->detay, 50)); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-500">
                                        <?php echo e($log->created_at->format('d.m.Y H:i')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <?php echo e($logs->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabulucular/logs.blade.php ENDPATH**/ ?>