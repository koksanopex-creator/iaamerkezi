<div x-show="activeTab === 'sikayetler'" class="space-y-4">
    <h3 class="text-lg font-bold text-gray-800">Bildirilen Şikayetler</h3>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şikayet Konusu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kazanılan Puan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $girilenSikayetler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sikayet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($sikayet->musteri_sikayet_konusu); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo $sikayet->getMusteriDurumBadgeAttribute(); ?>

                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-bold">
                            <?php echo e($sikayet->kazanilan_puan > 0 ? '+' . $sikayet->kazanilan_puan : '-'); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($sikayet->created_at->format('d.m.Y')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Kayıt yok.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/profile/partials/show/tab-complaints.blade.php ENDPATH**/ ?>