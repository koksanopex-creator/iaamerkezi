<div class="p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
    <h5 class="block text-lg font-semibold text-blue-800">
        <?php echo e($config['title'] ?? 'Bilgilendirme'); ?>

    </h5>
    <div class="mt-2 text-sm text-blue-700 prose prose-sm max-w-none">
       <?php echo nl2br(e($config['content'] ?? 'Bu adım için bilgi metni girilmemiş.')); ?>

    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/widgets/_info-text.blade.php ENDPATH**/ ?>