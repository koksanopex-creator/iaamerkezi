<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase font-bold">Süreç Türü</p>
        <p class="text-lg font-bold <?php echo e($case->type == 'zorunlu' ? 'text-red-600' : 'text-green-600'); ?>">
            <?php echo e(ucfirst($case->type)); ?> Arabuluculuk
        </p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase font-bold">Talep Edilen</p>
        <p class="text-lg font-bold text-gray-800"><?php echo e(number_format($case->talep_tutari, 2)); ?> TL</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase font-bold">Anlaşılan Tutar</p>
        <p class="text-lg font-bold text-indigo-600">
            <?php echo e($case->anlasilan_tutar ? number_format($case->anlasilan_tutar, 2) . ' TL' : '---'); ?>

        </p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase font-bold">Sorumlu Birim</p>
        <div class="flex items-center mt-1">
            <?php if($case->owner_role == 'hukuk'): ?>
                <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Hukuk
            <?php else: ?>
                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Personel
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/arabuluculuk/parcalar/genel-istatistik-kartlari.blade.php ENDPATH**/ ?>