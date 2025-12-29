<div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Takıma Havuzdan Proje Ata</h3>
        <?php if($havuzdakiOneriler->isNotEmpty()): ?>
            <form action="<?php echo e(route('admin.takim-yonetim.projeAta', $takim)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <select name="iaa_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Atamak için bir proje seçin...</option>
                        <?php $__currentLoopData = $havuzdakiOneriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oneri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($oneri->id); ?>"><?php echo e(Str::limit($oneri->baslik, 50)); ?> (Puan: <?php echo e(number_format($oneri->puan, 2)); ?>)</option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="w-full bg-green-600 text-white font-semibold py-2 px-4 rounded-md">Projeyi Bu Takıma Ata</button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-sm text-center text-gray-500">Havuzda atanabilecek bir proje bulunmamaktadır.</p>
        <?php endif; ?>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/takim-yonetim/partials/proje-atama-karti.blade.php ENDPATH**/ ?>