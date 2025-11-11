<div class="bg-gradient-to-br from-red-50 via-white to-red-100 overflow-hidden shadow-xl sm:rounded-2xl border border-red-200">
    <div class="p-6 sm:p-8 text-gray-900">
        
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-2 h-8 bg-gradient-to-b from-red-400 to-red-600 rounded-full"></div>
            <h3 class="text-2xl font-bold text-red-800 tracking-tight">
                <?php echo e($title); ?>

                <span class="inline-flex items-center justify-center w-8 h-8 ml-2 text-sm font-semibold text-red-600 bg-red-200 rounded-full ring-2 ring-red-300">
                    <?php echo e($iaas->count()); ?>

                </span>
            </h3>
        </div>

        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-red-200/80 overflow-hidden">
            <table class="block sm:table min-w-full">
                <thead class="hidden sm:table-header-group">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-3">Proje / Takım</th>
                        <th class="px-6 py-3">Reddedilme Tarihi</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="block sm:table-row-group">
                    <?php $__empty_1 = true; $__currentLoopData = $iaas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="block mb-4 border bg-white border-red-50 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-red-50/50">
                            <td class="p-4 align-middle">
                                <div class="font-semibold text-gray-800"><?php echo e($iaa->baslik); ?></div>
                                <div class="text-xs text-gray-500 mt-1"><?php echo e($iaa->atananTakim->ad ?? 'N/A'); ?></div>
                            </td>
                            <td class="p-4 align-middle text-sm text-gray-600">
                                <?php echo e($iaa->updated_at->format('d.m.Y H:i')); ?>

                            </td>
                            <td class="p-4 align-middle text-right">
                                <div class="flex justify-end items-center space-x-2">
                                    
                                    <a href="<?php echo e(route('proje.workspace.show', $iaa)); ?>" class="px-3 py-1.5 bg-gray-800 text-white text-xs font-semibold rounded-md hover:bg-black transition-colors">
                                        İncele
                                    </a>
                                    
                                    
                                    <form action="<?php echo e(route('admin.iaa-yonetim.geriAl', $iaa)); ?>" method="POST" onsubmit="return confirm('Bu işlemi geri almak ve projeyi tekrar \'Yönetici Onayı Bekliyor\' durumuna getirmek istediğinizden emin misiniz?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="px-3 py-1.5 bg-yellow-500 text-black text-xs font-semibold rounded-md hover:bg-yellow-600 transition-colors">
                                            Geri Al
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr class="block sm:table-row">
                            <td colspan="3" class="p-8 text-center text-gray-500">Bu kategoride bir proje bulunmamaktadır.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/tamamlanmasi-reddedilen-projeler-table.blade.php ENDPATH**/ ?>