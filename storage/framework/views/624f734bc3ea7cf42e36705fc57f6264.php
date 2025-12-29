<div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Takımın Mevcut Projeleri (<?php echo e($takim->atanmisProjeler->count()); ?>)</h3>
        
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $takim->atananProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('proje.workspace.show', $proje)); ?>" class="block bg-gray-50 p-4 rounded-xl border border-gray-200 hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                <p class="font-bold text-gray-800"><?php echo e($proje->baslik); ?></p>
                    <p class="font-bold text-gray-800"><?php echo e($proje->baslik); ?></p>
                    <div class="mt-2 flex items-center justify-between text-sm text-gray-600">
                        <span class="font-semibold px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            <?php echo e($proje->durum); ?>

                        </span>
                        
                    </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" /></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Proje Yok</h3>
                    <p class="mt-1 text-sm text-gray-500">Bu takıma henüz atanmış bir proje bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/takim-yonetim/partials/atanmis-projeler-karti.blade.php ENDPATH**/ ?>