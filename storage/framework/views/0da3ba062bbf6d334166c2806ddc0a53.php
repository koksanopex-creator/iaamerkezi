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
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Arabuluculuk Dosyaları')); ?>

            </h2>
            
            
            <?php if(auth()->user()->hasRole('Superadmin') || auth()->user()->canAny(['arabuluculuk.create_ihtiyari', 'arabuluculuk.create_zorunlu'])): ?>
                <a href="<?php echo e(route('admin.arabuluculuk.create')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Yeni Dosya Aç
                </a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-xs font-bold uppercase">Toplam Dosya</div>
                    <div class="text-2xl font-bold text-gray-800"><?php echo e($cases->total()); ?></div>
                </div>
                
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosya No / Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Çalışan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür / Sorumlu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arabulucu / Avukat</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900"><?php echo e($case->dosya_no ?? '---'); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($case->created_at->format('d.m.Y')); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                                <?php echo e(substr($case->calisan->name ?? '?', 0, 1)); ?>

                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($case->calisan->name ?? 'Silinmiş Kullanıcı'); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo e($case->calisan->email ?? ''); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($case->type == 'ihtiyari'): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">İhtiyari</span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Zorunlu</span>
                                        <?php endif; ?>
                                        <div class="text-xs text-gray-500 mt-1">Yöneten: <?php echo e(ucfirst($case->owner_role)); ?></div>
                                        <div class="text-[10px] text-indigo-400 mt-0.5">
                                            <span class="font-semibold">Açan:</span> <?php echo e($case->creator->name ?? 'Sistem'); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo e($case->arabulucu->name ?? '-'); ?></div>
                                        <?php if($case->external_lawyer_id): ?>
                                            <div class="text-xs text-purple-600 font-semibold">Av. <?php echo e($case->externalLawyer->name ?? ''); ?> (Dış)</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        
                                        <?php
                                            $statusColors = [
                                                'taslak' => 'bg-gray-100 text-gray-800',
                                                'hukuk_incelemesinde' => 'bg-yellow-100 text-yellow-800',
                                                'yonetim_onayinda' => 'bg-purple-100 text-purple-800',
                                                'arabulucuda' => 'bg-blue-100 text-blue-800',
                                                'imza_asamasinda' => 'bg-indigo-100 text-indigo-800',
                                                'odeme_bekliyor' => 'bg-orange-100 text-orange-800 border border-orange-500 animate-pulse font-black',
                                                'kapatildi' => 'bg-green-100 text-green-800',
                                                'anlasma_saglanamadi' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$case->status] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-md <?php echo e($color); ?>">
                                            <?php echo e(str_replace('_', ' ', strtoupper($case->status))); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo e(route('admin.arabuluculuk.show', $case->id)); ?>" class="text-indigo-600 hover:text-indigo-900 font-bold">Detay &rarr;</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        Henüz kayıtlı bir arabuluculuk dosyası bulunmamaktadır.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    <?php echo e($cases->links()); ?>

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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/index.blade.php ENDPATH**/ ?>