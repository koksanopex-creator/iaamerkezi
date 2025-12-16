<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-blue-200">
    <div class="bg-gradient-to-r from-blue-50 to-white px-6 py-5 border-b border-blue-200 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-blue-800">
                Onayladıklarım (Üst Yönetim Bekleyenler)
            </h3>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proje</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Takım</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Onay Tarihiniz</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__currentLoopData = $iaas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iaa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-blue-50/30">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($iaa->baslik); ?></div>
                            <div class="text-xs text-gray-500">ID: #<?php echo e($iaa->id); ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo e($iaa->atananTakim->ad ?? '-'); ?>

                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            
                            <?php echo e($iaa->updated_at->format('d.m.Y H:i')); ?>

                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                
                                <a href="<?php echo e(route('proje.workspace.show', $iaa->id)); ?>" target="_blank" class="text-gray-600 hover:text-blue-600 text-xs font-bold border border-gray-300 px-2 py-1 rounded">
                                    İncele
                                </a>

                                
                                
                                <?php if(auth()->user()->hasRole('Superadmin')): ?>
                                    
                                    <form action="<?php echo e(route('admin.iaa-yonetim.geriAl', $iaa->id)); ?>" method="POST" class="inline-block">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?> 
                                        <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-xs font-bold transition flex items-center" onclick="return confirm('DİKKAT: Bu proje TAMAMLANDI statüsündedir.\nOnayı geri alırsanız dağıtılan puanlar geri alınacaktır.\nDevam etmek istiyor musunuz?')">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            Onayı & Puanı Geri Al
                                        </button>
                                    </form>
                                <?php else: ?>
                                    
                                    <form action="<?php echo e(route('admin.iaa-yonetim.bolumOnayiGeriAl', $iaa->id)); ?>" method="POST" class="inline-block">
                                        <?php echo csrf_field(); ?>
                                        
                                        <button type="submit" class="text-white bg-orange-500 hover:bg-orange-600 px-3 py-1 rounded text-xs font-bold transition flex items-center" onclick="return confirm('Onayınızı geri almak istediğinize emin misiniz? Proje tekrar onay listenize düşecek.')">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            Geri Al
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/bolum-onayladiklari-table.blade.php ENDPATH**/ ?>