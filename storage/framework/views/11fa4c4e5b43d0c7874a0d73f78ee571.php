
<div class="bg-gradient-to-br from-gray-50 via-white to-gray-100 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    <div class="p-6 sm:p-8 text-gray-900">
        
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-2 h-8 bg-gradient-to-b from-gray-400 to-gray-600 rounded-full"></div>
            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
                <?php echo e($title); ?>

                <span class="inline-flex items-center justify-center w-8 h-8 ml-2 text-sm font-semibold text-gray-600 bg-gray-200 rounded-full ring-2 ring-gray-300">
                    <?php echo e($iaas->count()); ?>

                </span>
            </h3>
        </div>

        
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
            <table class="block sm:table min-w-full">
                <thead class="hidden sm:table-header-group">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-3">Proje Bilgileri</th>
                        <th class="px-6 py-3">Başlangıç Tarihi</th>
                        <th class="px-6 py-3">Onaylanma Tarihi</th>
                        <th class="px-6 py-3 text-center">Tamamlanma Süresi</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="block sm:table-row-group">
                    <?php $__empty_1 = true; $__currentLoopData = $iaas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-gray-50">
                            <td class="p-4 align-middle font-semibold"><?php echo e($iaa->baslik); ?> <br> <span class="text-xs text-gray-500 font-normal"><?php echo e($iaa->atananTakim->ad ?? 'N/A'); ?></span></td>
                            <td class="p-4 align-middle text-sm text-gray-600"><?php echo e($iaa->iaaTalebi ? \Carbon\Carbon::parse($iaa->iaaTalebi->start_date)->format('d.m.Y') : '-'); ?></td>
                            <td class="p-4 align-middle text-sm text-gray-600"><?php echo e($iaa->onaylanma_tarihi ? \Carbon\Carbon::parse($iaa->onaylanma_tarihi)->format('d.m.Y') : '-'); ?></td>
                            <td class="p-4 align-middle text-center">
                                <?php if($iaa->completion_duration_in_days !== null): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo e($iaa->completion_duration_in_days); ?> gün
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="p-4 align-middle text-right">
                                <div class="flex justify-end items-center space-x-2">
                                    
                                    
                                    <a href="<?php echo e(route('proje.workspace.show', $iaa)); ?>" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Detay
                                    </a>
                                    
                                    
                                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
                                    <form action="<?php echo e(route('admin.iaa-yonetim.geriAl', $iaa)); ?>" method="POST" onsubmit="return confirm('Bu projeyi tekrar \'Yönetici Onayı Bekliyor\' durumuna almak istediğinizden emin misiniz?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-yellow-400 border border-transparent rounded-md font-semibold text-xs text-yellow-900 uppercase tracking-widest hover:bg-yellow-500 active:bg-yellow-600 focus:outline-none focus:border-yellow-600 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            Geri Al
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr class="block sm:table-row">
                            <td colspan="5" class="p-8 text-center text-gray-500">Henüz tamamlanmış bir proje bulunmuyor.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        
        <div class="mt-6 text-right">
            <a href="<?php echo e(route('admin.iaa-yonetim.arsiv')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                Tüm Tamamlanmış Projeleri Gör &rarr;
            </a>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/tamamlanmis-projeler-ozet-table.blade.php ENDPATH**/ ?>