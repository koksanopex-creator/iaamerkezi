<div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    
    <div class="p-6 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Takıma Yeni Üye Ekle</h3>
        <form action="<?php echo e(route('admin.takim-yonetim.uyeEkle', $takim)); ?>" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
            <?php echo csrf_field(); ?>
            <select name="user_id" class="w-full flex-grow border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="">Eklemek için bir kullanıcı seçin...</option>
                <?php $__currentLoopData = $potansiyelUyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                    <option value="<?php echo e($uye->id); ?>"><?php echo e($uye->name); ?> (<?php echo e($uye->email); ?>)</option> 
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="w-full sm:w-auto flex-shrink-0 bg-indigo-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors">Ekle</button>
        </form>
    </div>

    
    <ul class="divide-y divide-gray-200">
        <?php $__empty_1 = true; $__currentLoopData = $takim->uyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-3">
                    
                    <a href="<?php echo e(route('profile.show', $uye->id)); ?>" target="_blank" class="flex-shrink-0 group">
                        <?php if($uye->profile_photo_path): ?>
                            <img class="w-10 h-10 rounded-full object-cover border border-gray-200 group-hover:border-indigo-500 transition-colors" src="<?php echo e(asset('storage/' . $uye->profile_photo_path)); ?>" alt="<?php echo e($uye->name); ?>">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-bold text-gray-600 group-hover:bg-indigo-100 group-hover:text-indigo-700 transition-colors">
                                <?php echo e(substr($uye->name, 0, 1)); ?>

                            </div>
                        <?php endif; ?>
                    </a>

                    
                    <div>
                        <a href="<?php echo e(route('profile.show', $uye->id)); ?>" target="_blank" class="font-medium text-gray-900 hover:text-indigo-600 transition-colors flex items-center">
                            <?php echo e($uye->name); ?>

                            <?php if($uye->id === $takim->lider_user_id): ?>
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    Lider
                                </span>
                            <?php endif; ?>
                        </a>
                        <p class="text-xs text-gray-500"><?php echo e($uye->bolum->ad ?? 'Bölüm Yok'); ?></p>
                    </div>
                </div>

                
                <?php if($uye->id !== $takim->lider_user_id): ?>
                    <form action="<?php echo e(route('admin.takim-yonetim.uyeCikar', ['takim' => $takim, 'user' => $uye])); ?>" method="POST" onsubmit="return confirm('Bu üyeyi çıkarmak istediğinizden emin misiniz?');">
                        <?php echo csrf_field(); ?> 
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">Çıkar</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="p-4 text-center text-gray-500 italic">Takımda henüz üye yok.</li>
        <?php endif; ?>
    </ul>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/takim-yonetim/partials/uye-yonetim-karti.blade.php ENDPATH**/ ?>