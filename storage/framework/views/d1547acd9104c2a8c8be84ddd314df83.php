<div class="bg-white/60 backdrop-blur-sm overflow-hidden">
    <?php if($takimlar->isNotEmpty()): ?>
        <table class="block sm:table min-w-full">
            <thead class="hidden sm:table-header-group">
                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                    <th scope="col" class="px-6 py-4">Takım Adı</th>
                    <th scope="col" class="px-6 py-4">Lider</th>
                    <th scope="col" class="px-6 py-4">Üye Sayısı</th>
                    <?php if($type === 'katildigim'): ?>
                        <th scope="col" class="px-6 py-4">Oluşturulma</th>
                    <?php endif; ?>
                    <th scope="col" class="relative px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="block sm:table-row-group">
                <?php $__currentLoopData = $takimlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $takim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-gray-50 transition-colors duration-200 group">
                        
                        <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span>
                            <div class="text-right sm:text-left flex items-center">
                                <div class="flex-shrink-0 h-10 w-10"><div class="h-10 w-10 rounded-full bg-gradient-to-r <?php echo e($type === 'katildigim' ? 'from-blue-400 to-indigo-500' : 'from-gray-400 to-gray-500'); ?> flex items-center justify-center"><span class="text-sm font-bold text-white"><?php echo e(Str::substr($takim->ad, 0, 1)); ?></span></div></div>
                                <div class="ml-4"><div class="text-sm font-semibold text-gray-900"><?php echo e($takim->ad); ?></div></div>
                            </div>
                        </td>
                        
                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Lider:</span>
                            <div class="text-right sm:text-left">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($takim->lider->name); ?></div>
                                <div class="text-xs text-gray-500"><?php echo e($takim->lider->bolum->ad ?? 'Bölüm Yok'); ?></div>
                            </div>
                        </td>

                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                             <span class="font-semibold text-sm text-gray-500 sm:hidden">Üyeler:</span>
                             <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200"><?php echo e($takim->uyeler_count); ?> Üye</span>
                        </td>

                        <?php if($type === 'katildigim'): ?>
                        <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                            <span class="font-semibold text-sm text-gray-500 sm:hidden">Oluşturulma:</span>
                            <span class="text-right sm:text-left text-sm text-gray-500"><?php echo e($takim->created_at->format('d.m.Y')); ?></span>
                        </td>
                        <?php endif; ?>

                        <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                            <div class="flex flex-col sm:flex-row sm:justify-end sm:items-center sm:space-x-2 space-y-2 sm:space-y-0">
                                <?php if($type === 'katildigim'): ?>
                                    <?php if($takim->lider_user_id === auth()->id()): ?>
                                        <a href="<?php echo e(route('takimlar.show', $takim)); ?>" class="group inline-flex justify-center items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-lg text-xs hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg">Yönet</a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('takimlar.show', $takim)); ?>" class="group inline-flex justify-center items-center px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg text-xs hover:bg-gray-600">Görüntüle</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if(isset($davetAlinanTakimIdleri) && $davetAlinanTakimIdleri->contains($takim->id)): ?>
                                        <a href="<?php echo e(route('takimlar.davetlerim')); ?>" class="px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg text-xs hover:bg-yellow-600 shadow-md">Davetiniz Var</a>
                                    <?php elseif(isset($istekGonderilenTakimIdleri) && $istekGonderilenTakimIdleri->contains($takim->id)): ?>
                                        <button class="px-4 py-2 bg-gray-300 text-gray-500 font-semibold rounded-lg text-xs cursor-not-allowed" disabled>İstek Gönderildi</button>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('takimlar.katilmaIstegi', $takim)); ?>" method="POST"> <?php echo csrf_field(); ?> <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg text-xs hover:from-green-600 hover:to-emerald-700 shadow-md">Katılma İsteği Gönder</button></form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="p-6 text-center text-gray-500">
            <?php if($type === 'katildigim'): ?> Henüz bir takıma üye değilsiniz. <?php else: ?> Katılabileceğiniz başka bir takım bulunmamaktadır. <?php endif; ?>
        </p>
    <?php endif; ?>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/takimlar/partials/takim-table.blade.php ENDPATH**/ ?>