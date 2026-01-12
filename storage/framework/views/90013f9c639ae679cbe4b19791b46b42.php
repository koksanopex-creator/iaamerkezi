<div class="bg-gray-50 border rounded-lg p-5 mb-6">
    <h4 class="font-bold text-gray-700 mb-3">Değerlendirme Ekle</h4>
    <form action="<?php echo e(route('admin.arabuluculuk.addComment', $case->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <textarea name="yorum" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Görüş ve değerlendirmenizi buraya yazınız..."></textarea>
        <div class="mt-3 flex justify-between items-center">
            <div class="w-1/3">
                <select name="karar" class="w-full border-gray-300 rounded-md text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Karar (Opsiyonel)</option>
                    <option value="Onay">Onay / Olumlu</option>
                    <option value="Red">Red / Olumsuz</option>
                    <option value="Revize">Revize Gerekli</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow text-sm font-bold transition">Kaydet</button>
        </div>
    </form>
</div>

<?php $__currentLoopData = $case->kurulDegerlendirmesi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $degerlendirme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3 shadow-sm">
        <div class="flex justify-between items-start mb-2">
            <div class="flex items-center">
                <div class="font-bold text-gray-900"><?php echo e($degerlendirme->user->name ?? 'Bilinmeyen'); ?></div>
                <span class="text-xs text-gray-500 ml-2"><?php echo e($degerlendirme->created_at->format('d.m.Y H:i')); ?></span>
            </div>
            <?php if($degerlendirme->karar): ?>
                <span class="px-2 py-1 text-xs font-bold rounded <?php echo e($degerlendirme->karar == 'Onay' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                    <?php echo e($degerlendirme->karar); ?>

                </span>
            <?php endif; ?>
        </div>
        <p class="text-gray-700 text-sm"><?php echo e($degerlendirme->yorum); ?></p>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/parcalar/sekme-kurul-degerlendirme.blade.php ENDPATH**/ ?>