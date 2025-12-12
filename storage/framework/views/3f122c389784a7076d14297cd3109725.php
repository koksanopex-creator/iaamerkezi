<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            İhlal ve Olay Detayları
        </h3>
        <span class="text-xs font-mono text-gray-400">Dosya #<?php echo e($case->id); ?></span>
    </div>

    <div class="p-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            
            
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                        <?php echo e(substr($case->user->name, 0, 1)); ?>

                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">İlgili Personel</p>
                    <p class="font-bold text-gray-900"><?php echo e($case->user->name); ?></p>
                    <p class="text-xs text-gray-500"><?php echo e($case->user->bolum->ad ?? 'Bölüm Yok'); ?></p>
                </div>
            </div>

            
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">İhlal Kategorisi</p>
                    <p class="font-bold text-gray-900"><?php echo e($case->behavior->category->ad ?? 'Genel'); ?></p>
                    <p class="text-xs text-red-500 font-medium"><?php echo e(Str::limit($case->behavior->tanim, 40)); ?></p>
                </div>
            </div>

            
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Olay Tarihi</p>
                    <p class="font-bold text-gray-900"><?php echo e($case->olay_tarihi->format('d.m.Y')); ?></p>
                    <p class="text-xs text-gray-500">Raporlayan: <?php echo e($case->reporter->name); ?></p>
                </div>
            </div>
        </div>

        
        <hr class="border-gray-100 my-6">

        
        <div class="mb-6">
            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                Olay Açıklaması / Tutanak Metni
            </h4>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed">
                <?php echo e($case->olay_aciklamasi); ?>

            </div>
        </div>

        
        <div>
            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                Ekli Kanıtlar ve Belgeler
            </h4>
            
            <?php if(empty($case->kanit_dosyalari)): ?>
                <div class="flex items-center gap-2 text-gray-400 text-sm italic bg-gray-50 px-3 py-2 rounded border border-gray-100 border-dashed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    Bu tutanağa eklenmiş dijital kanıt dosyası bulunmuyor.
                </div>
            <?php else: ?>
                <div class="flex flex-wrap gap-3">
                    <?php $__currentLoopData = $case->kanit_dosyalari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kanit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(asset('storage/'.$kanit)); ?>" target="_blank" class="group flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg hover:border-indigo-500 hover:shadow-sm transition">
                            <div class="bg-indigo-50 p-1.5 rounded text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-700 truncate max-w-[150px]">
                                <?php echo e(basename($kanit)); ?>

                            </span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/partials/case-details.blade.php ENDPATH**/ ?>