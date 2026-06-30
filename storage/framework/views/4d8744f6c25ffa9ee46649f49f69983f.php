<div class="bg-gradient-to-br from-<?php echo e($color); ?>-50 via-white to-<?php echo e($color); ?>-100 overflow-hidden shadow-xl sm:rounded-2xl border border-<?php echo e($color); ?>-200">
    
    <div class="p-6 sm:p-8 text-gray-900">
        
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-2 h-8 bg-gradient-to-b from-<?php echo e($color); ?>-400 to-<?php echo e($color); ?>-600 rounded-full"></div>
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
                    <?php echo e($title); ?> 
                    <span class="inline-flex items-center justify-center w-8 h-8 ml-2 text-sm font-semibold text-<?php echo e($color); ?>-600 bg-<?php echo e($color); ?>-100 rounded-full ring-2 ring-<?php echo e($color); ?>-200">
                        <?php echo e($iaas->count()); ?>

                    </span>
                </h3>
            </div>
        </div>

        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
            <table class="block sm:table min-w-full">
                
                <thead class="hidden sm:table-header-group">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                        
                        <th class="px-6 py-3 w-12 text-center">#</th>

                        <?php if(in_array($type, ['havuz', 'atanmis'])): ?><th class="px-6 py-3 text-center">Puan</th><?php endif; ?>
                        <th class="px-6 py-3">Başlık</th>
                        <th class="px-6 py-3">Bölüm</th>
                        <?php if(in_array($type, ['onay', 'havuz', 'reddedilmis'])): ?><th class="px-6 py-3">Öneren</th><?php endif; ?>
                        <?php if(in_array($type, ['atanmis'])): ?><th class="px-6 py-3">Atanan Takım</th><?php endif; ?>
                        <th class="px-6 py-3">Tarih</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                
                <tbody class="block sm:table-row-group">
                    <?php $__empty_1 = true; $__currentLoopData = $iaas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-<?php echo e($color); ?>-50 transition-colors group">
                            
                            
                            <td class="p-4 align-middle text-center sm:table-cell">
                                <span class="text-lg font-bold text-gray-300 group-hover:text-<?php echo e($color); ?>-400 transition-colors">
                                    <?php echo e($loop->iteration); ?>

                                </span>
                            </td>

                            
                            <?php if(in_array($type, ['havuz', 'atanmis'])): ?>
                                <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Puan:</span>
                                    <div class="text-right sm:text-center">
                                        <?php if($iaa->puan): ?>
                                        <button x-data @click="$dispatch('open-modal', 'puan-detay-<?php echo e($iaa->id); ?>')" class="relative inline-flex items-center justify-center w-12 h-12 font-bold text-sm text-white bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all focus:outline-none focus:ring-4 focus:ring-indigo-200">
                                            <span><?php echo e(number_format($iaa->puan, 0, ',', '.')); ?></span>
                                        </button>
                                        <?php else: ?>
                                        <div class="inline-flex items-center justify-center w-12 h-12 text-xs font-medium text-gray-400 bg-gray-100 rounded-full border-2 border-dashed border-gray-300">
                                            -
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                            
                            
                            <td class="flex justify-between items-start p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Başlık:</span>
                                <div class="text-right sm:text-left w-full">
                                    <p class="text-gray-800 font-medium group-hover:text-<?php echo e($color); ?>-700 transition-colors inline-flex items-center flex-wrap gap-2">
                                        <span><?php echo e($iaa->baslik); ?></span>

                                        
                                        <?php if($type == 'atanmis' && $iaa->durum == 'Revize Ediliyor'): ?>
                                            <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-yellow-200 uppercase tracking-wide">
                                                Revizyon
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </td>

                            
                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Bölüm:</span>
                                <div class="text-right sm:text-left">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                        <?php echo e($iaa->bolum->ad ?? 'Genel'); ?>

                                    </span>
                                </div>
                            </td>

                            
                            <?php if(in_array($type, ['onay', 'havuz', 'reddedilmis'])): ?>
                                <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Öneren:</span>
                                    <div class="text-right sm:text-left">
                                        <?php if($iaa->gonderen): ?>
                                            
                                            <a href="<?php echo e(route('profile.show', $iaa->gonderen->id)); ?>" class="inline-flex items-center space-x-2 group/user hover:opacity-80 transition-opacity" title="Profili Görüntüle">
                                                <div class="w-8 h-8 bg-white border border-gray-200 text-<?php echo e($color); ?>-600 rounded-full flex items-center justify-center shadow-sm group-hover/user:bg-<?php echo e($color); ?>-50 group-hover/user:border-<?php echo e($color); ?>-300 transition-colors">
                                                    <span class="text-xs font-bold"><?php echo e(substr($iaa->gonderen->name, 0, 1)); ?></span>
                                                </div>
                                                <span class="text-sm font-medium text-gray-700 group-hover/user:text-<?php echo e($color); ?>-700 group-hover/user:underline decoration-<?php echo e($color); ?>-300 transition-colors"><?php echo e($iaa->gonderen->name); ?></span>
                                            </a>
                                        <?php else: ?>
                                            <div class="inline-flex items-center space-x-2">
                                                <div class="w-8 h-8 bg-gray-100 border border-gray-200 text-gray-500 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-bold">M</span>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-700 block"><?php echo e($iaa->guest_name); ?></span>
                                                    <span class="text-[10px] text-gray-400 uppercase tracking-wider">Misafir</span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                            
                            
                            <?php if(in_array($type, ['atanmis'])): ?>
                                 <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Atanan Takım:</span>
                                    <div class="text-right sm:text-left">
                                        <?php if($iaa->atananTakim): ?>
                                             <div class="inline-flex items-center space-x-2">
                                                <div class="w-8 h-8 bg-white border border-gray-200 text-green-600 rounded-full flex items-center justify-center shadow-sm">
                                                    <span class="text-xs font-bold"><?php echo e(Str::substr($iaa->atananTakim->ad, 0, 1)); ?></span>
                                                </div>
                                                <span class="text-sm font-medium text-gray-700"><?php echo e($iaa->atananTakim->ad); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 italic">Henüz Atanmadı</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                            
                            
                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span>
                                <span class="text-right sm:text-left text-sm text-gray-600 tabular-nums font-medium">
                                    <?php if($type === 'onay'): ?> <?php echo e($iaa->created_at->format('d.m.Y')); ?> <?php endif; ?>
                                    <?php if($type === 'atanmis'): ?> <?php echo e($iaa->updated_at->format('d.m.Y')); ?> <?php endif; ?>
                                    <?php if($type === 'havuz'): ?> <?php echo e($iaa->onaylanma_tarihi ? \Carbon\Carbon::parse($iaa->onaylanma_tarihi)->format('d.m.Y') : '-'); ?> <?php endif; ?>
                                    <?php if($type === 'reddedilmis'): ?> <?php echo e($iaa->updated_at->format('d.m.Y')); ?> <?php endif; ?>
                                </span>
                            </td>
                            
                            
                            <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-2 space-y-2 sm:space-y-0">
                                    <?php echo $__env->make('admin.iaa-yonetim.partials.actions', ['type' => $type, 'iaa' => $iaa], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr class="block sm:table-row">
                            <td colspan="7" class="p-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="text-lg font-medium text-gray-600 mb-1">Kayıt Bulunamadı</h4>
                                        <p class="text-gray-400 text-sm">Bu kategoride henüz bir öneri mevcut değil.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/iaa-yonetim/partials/table-content.blade.php ENDPATH**/ ?>