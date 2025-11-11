<div class="bg-gradient-to-br from-blue-50 via-white to-blue-100 overflow-hidden shadow-xl sm:rounded-2xl border border-blue-200">
    <div class="p-6 sm:p-8 text-gray-900">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-2 h-8 bg-gradient-to-b from-blue-400 to-blue-600 rounded-full"></div>
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
                    <?php echo e($title); ?> 
                    <span class="inline-flex items-center justify-center w-8 h-8 ml-2 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full ring-2 ring-blue-200">
                        <?php echo e($iaas->count()); ?>

                    </span>
                </h3>
            </div>
        </div>

        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
            <table class="block sm:table min-w-full">
                <thead class="hidden sm:table-header-group">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-4">Başlık</th>
                        <th class="px-6 py-4">Talep Sayısı</th>
                        <th class="px-6 py-4 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="block sm:table-row-group">
                    <?php $__empty_1 = true; $__currentLoopData = $iaas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-blue-50 transition-colors">
                            <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Başlık:</span><span class="text-right sm:text-left font-medium text-gray-800"><?php echo e($iaa->baslik); ?></span></td>
                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Talepler:</span><div class="w-full text-left"><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800"><?php echo e($iaa->talep_eden_takimlar_count); ?> Takım</span></div></td>
                            <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><div class="flex justify-end"><a href="<?php echo e(route('admin.iaa-yonetim.talepleriGoster', $iaa)); ?>" class="inline-flex justify-center text-sm font-semibold text-white bg-indigo-600 px-3 py-2 rounded-md hover:bg-indigo-700">Talepleri Görüntüle</a></div></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr class="block sm:table-row"><td colspan="3" class="p-12 text-center text-gray-500">Henüz talep alan bir öneri bulunmamaktadır.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/talep-alan-oneriler-table.blade.php ENDPATH**/ ?>