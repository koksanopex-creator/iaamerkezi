<div x-show="activeTab === 'performans'" class="space-y-6">
    <h3 class="text-lg font-bold text-gray-800">İAA Çözme Performansı (Son 6 Ay)</h3>
    
    
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div id="performanceChart"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h4 class="text-sm font-bold text-gray-600 uppercase mb-4">Son Aktiviteler</h4>
            <ul class="space-y-4">
                <?php $__currentLoopData = $sonAktiviteler->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-start space-x-3 text-sm">
                        <div class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-indigo-500"></div>
                        <div>
                            <span class="font-semibold text-gray-800"><?php echo e($log->eylem); ?></span>
                            <p class="text-gray-500 text-xs"><?php echo e($log->aciklama); ?></p>
                            <span class="text-xs text-gray-400 block mt-1"><?php echo e($log->created_at->diffForHumans()); ?></span>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>

        
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h4 class="text-sm font-bold text-gray-600 uppercase mb-4">En Son Katıldığı Proje</h4>
            <?php if($sonProje): ?>
                <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                    <p class="font-bold text-indigo-900"><?php echo e($sonProje->baslik); ?></p>
                    <p class="text-sm text-indigo-700 mt-1">Durum: <?php echo e($sonProje->durum); ?></p>
                    <p class="text-xs text-indigo-500 mt-2">Son İşlem: <?php echo e($sonProje->updated_at->format('d.m.Y H:i')); ?></p>
                    <a href="<?php echo e(route('proje.workspace.show', $sonProje->id)); ?>" class="mt-3 inline-block text-xs font-bold text-indigo-600 hover:underline">Projeye Git &rarr;</a>
                </div>
            <?php else: ?>
                <p class="text-gray-500">Henüz bir projede yer almadı.</p>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/profile/partials/show/tab-performance.blade.php ENDPATH**/ ?>