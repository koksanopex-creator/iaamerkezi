
<div class="bg-gradient-to-br from-purple-50 via-white to-purple-100 overflow-hidden shadow-xl sm:rounded-2xl border border-purple-200" x-data="tableManager()" 
data-bulk-delete-url="<?php echo e(route('admin.iaa-yonetim.bulkDestroy')); ?>">
    
    <div class="p-6 sm:p-8 text-gray-900">
        
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-2 h-8 bg-gradient-to-b from-purple-400 to-purple-600 rounded-full"></div>
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
                    <?php echo e($title); ?> 
                    <span class="inline-flex items-center justify-center w-8 h-8 ml-2 text-sm font-semibold text-purple-600 bg-purple-100 rounded-full ring-2 ring-purple-200">
                        <?php echo e($iaas->count()); ?>

                    </span>
                </h3>
            </div>
            
            <div x-show="selectedIds.length > 0" x-transition class="mt-4 sm:mt-0">
                <div class="inline-flex items-center space-x-2 bg-purple-100 border-l-4 border-purple-600 px-4 py-2 rounded-r-lg">
                    <span class="text-sm font-medium text-purple-800" x-text="selectedIds.length + ' proje seçildi'"></span>
                </div>
            </div>
        </div>

        
        <form method="POST" action="#" onsubmit="return confirm('Seçili işlem yapılacak, emin misiniz?');">
            <?php echo csrf_field(); ?>
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="iaa_ids[]" :value="id">
            </template>

            
            <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                <table class="block sm:table min-w-full">
                    
                    <thead class="hidden sm:table-header-group">
                        <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                            <th class="p-4 w-12"><input type="checkbox" @click="toggleAll" :checked="isAllSelected" class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2"></th>
                            <th class="px-6 py-3">Proje Bilgileri</th>
                            <th class="px-6 py-3">Atanan Takım</th>
                            <th class="px-6 py-3 text-center">Proje Puanı</th>
                            <th class="px-6 py-3">Tamamlanma</th>
                            <th class="px-6 py-3 text-right">İşlemler</th>
                        </tr>
                    </thead>
                    
                    <tbody class="block sm:table-row-group">
                        <?php $__empty_1 = true; $__currentLoopData = $iaas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-purple-50 transition-colors group">
                                
                                <td class="p-4 align-middle sm:table-cell">
                                    <input type="checkbox" class="iaa-checkbox w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2" value="<?php echo e($iaa->id); ?>" x-model="selectedIds">
                                </td>

                                <td class="flex justify-between items-start p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Proje:</span>
                                    <div class="text-right sm:text-left">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                <?php echo e(substr($iaa->baslik, 0, 1)); ?>

                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 mb-1"><?php echo e($iaa->baslik); ?></p>
                                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                                                    <?php echo e($iaa->gonderen->name ?? $iaa->guest_name ?? 'N/A'); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span>
                                    <div class="text-right sm:text-left">
                                        <?php if($iaa->atananTakim): ?>
                                            <div class="inline-flex items-center space-x-2">
                                                <div class="w-7 h-7 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-bold text-white"><?php echo e(Str::substr($iaa->atananTakim->ad, 0, 1)); ?></span>
                                                </div>
                                                <span class="text-sm font-medium text-gray-700"><?php echo e($iaa->atananTakim->ad); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Atanmamış</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Puan:</span>
                                    <div class="text-right sm:text-center">
                                        <span class="font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-purple-800">
                                            <?php echo e(number_format($iaa->puan, 0, ',', '.')); ?>

                                        </span>
                                    </div>
                                </td>
                                
                                <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span>
                                    <span class="text-right sm:text-left text-sm text-gray-500"><?php echo e($iaa->updated_at->format('d.m.Y')); ?></span>
                                </td>
                                
                                <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-2 space-y-2 sm:space-y-0">
                                        <a href="<?php echo e(route('proje.workspace.show', $iaa)); ?>" class="px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors text-center" title="İlerleme İzle">İncele</a>
                                        
                                        
                                            <button type="button" @click="$dispatch('open-modal', 'revize-iste-modal-<?php echo e($iaa->id); ?>')" class="px-3 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition-colors">Revize</button>

                                            <button type="button" @click="$dispatch('open-modal', 'reddet-tamamlandi-modal-<?php echo e($iaa->id); ?>')" class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">Reddet</button>

                                            <button type="button" @click="$dispatch('open-modal', 'onayla-tamamlandi-modal-<?php echo e($iaa->id); ?>')" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">Onayla</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr class="block sm:table-row">
                                <td colspan="6" class="p-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="text-center">
                                            <h4 class="text-lg font-semibold text-gray-600 mb-1">Onay bekleyen proje bulunmuyor</h4>
                                            <p class="text-gray-500">Tamamlanmış projeler burada görünecektir.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/yonetici-onayi-bekleyenler-table.blade.php ENDPATH**/ ?>