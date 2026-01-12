<ul role="list" class="-mb-8">
    <?php $__currentLoopData = $case->logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li>
            <div class="relative pb-8">
                <?php if(!$loop->last): ?>
                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                <?php endif; ?>
                <div class="relative flex space-x-3">
                    <div>
                        <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                            <p class="text-sm text-gray-500">
                                <span class="font-medium text-gray-900"><?php echo e($log->islem); ?></span>: <?php echo e($log->detay); ?>

                            </p>
                        </div>
                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                            <time datetime="<?php echo e($log->created_at); ?>"><?php echo e($log->created_at->format('d.m.Y H:i')); ?></time>
                            <br>
                            <span class="text-xs text-gray-400"><?php echo e($log->user->name ?? 'Sistem'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/parcalar/sekme-gecmis-log.blade.php ENDPATH**/ ?>