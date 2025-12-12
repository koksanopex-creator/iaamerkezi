<div class="space-y-6">
    
    <div class="bg-white shadow sm:rounded-lg p-6 border-t-4 border-indigo-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Sistem Değerlendirmesi</h3>
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600">Etki Puanı</span>
            <span class="font-bold text-gray-800"><?php echo e($case->impact->puan ?? 0); ?></span>
        </div>
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600">Kapsam Puanı</span>
            <span class="font-bold text-gray-800">x <?php echo e($case->scope->puan ?? 0); ?></span>
        </div>
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <span class="text-sm text-gray-600">Tekrar Katsayısı (<?php echo e($case->tekrar_sayisi); ?>. Kez)</span>
            <span class="font-bold text-indigo-600">x <?php echo e($case->tekrar_sayisi >= 4 ? 5 : $case->tekrar_sayisi); ?></span>
        </div>
        <div class="flex justify-between items-center bg-gray-100 p-3 rounded mb-4">
            <span class="font-bold text-gray-700">Toplam Puan</span>
            <span class="font-black text-2xl text-indigo-700"><?php echo e($case->hesaplanan_puan); ?></span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 uppercase">Sistem Önerisi</span>
            <div class="text-lg font-bold text-red-600 border border-red-200 bg-red-50 p-2 rounded mt-1">
                <?php echo e($case->sistem_oneri_ceza); ?>

            </div>
        </div>
    </div>

    
    <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Künye</h3>
        <div class="text-sm">
            <p class="text-gray-500 mb-1">Tutanak No:</p>
            <p class="font-mono font-bold text-gray-800 mb-3">#<?php echo e($case->id); ?></p>
            <p class="text-gray-500 mb-1">Raporlayan Amir:</p>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600"><?php echo e(substr($case->reporter->name ?? '?', 0, 1)); ?></div>
                <span class="font-medium text-gray-800"><?php echo e($case->reporter->name ?? 'Bilinmiyor'); ?></span>
            </div>
            <p class="text-gray-500 mt-3 mb-1">Oluşturulma:</p>
            <p class="text-gray-800"><?php echo e($case->created_at->format('d.m.Y H:i')); ?></p>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/disiplin/partials/sidebar.blade.php ENDPATH**/ ?>