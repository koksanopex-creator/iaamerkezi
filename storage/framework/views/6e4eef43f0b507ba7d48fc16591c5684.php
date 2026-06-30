<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-yellow-200">
    <div class="bg-gradient-to-r from-yellow-50 to-white px-6 py-5 border-b border-yellow-200"><h3 class="text-lg font-semibold text-yellow-800">Misafirlerden Gelen Öneriler (<?php echo e($onayBekleyenMisafirler->count()); ?>)</h3></div>
    
    <?php echo $__env->make('admin.iaa-yonetim.partials.table-content', ['iaas' => $onayBekleyenMisafirler, 'type' => 'onay'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/iaa-yonetim/partials/onay-bekleyen-misafirler-table.blade.php ENDPATH**/ ?>