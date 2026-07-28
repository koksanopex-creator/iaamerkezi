<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-4 text-center">
    
    
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
    <div onclick="switchTab('onay-bekleyenler')" class="cursor-pointer hover:scale-105 transition-transform p-4 bg-yellow-100 rounded-lg shadow-sm border border-yellow-200">
        <p class="text-sm font-medium text-yellow-800">Onay Bekleyen</p>
        <p class="mt-1 text-3xl font-bold text-yellow-900"><?php echo e($stats['onayBekleyen']); ?></p>
    </div>
    <?php endif; ?>

    
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
    <div onclick="switchTab('havuz-talepler')" class="cursor-pointer hover:scale-105 transition-transform p-4 bg-blue-100 rounded-lg shadow-sm border border-blue-200">
        <p class="text-sm font-medium text-blue-800">Havuzda</p>
        <p class="mt-1 text-3xl font-bold text-blue-900"><?php echo e($stats['havuzda']); ?></p>
    </div>
    <?php endif; ?>

    
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
    <div onclick="switchTab('havuz-talepler')" class="cursor-pointer hover:scale-105 transition-transform p-4 bg-cyan-100 rounded-lg shadow-sm border border-cyan-200">
        <p class="text-sm font-medium text-cyan-800">Talep Alan</p>
        <p class="mt-1 text-3xl font-bold text-cyan-900"><?php echo e($stats['talepAlan']); ?></p>
    </div>
    <?php endif; ?>




    
    <div onclick="switchTab('aktif-projeler')" class="cursor-pointer hover:scale-105 transition-transform p-4 bg-green-100 rounded-lg shadow-sm border border-green-200">
        <p class="text-sm font-medium text-green-800">Atanmış/Revize</p>
        <p class="mt-1 text-3xl font-bold text-green-900"><?php echo e($stats['atanmis']); ?></p>
    </div>

    
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin')): ?>
    <div onclick="switchTab('onay-bekleyenler')" class="cursor-pointer hover:scale-105 transition-transform p-4 bg-indigo-100 rounded-lg shadow-sm border border-indigo-200">
        <p class="text-sm font-medium text-indigo-800">Yönetici Onayı</p>
        <p class="mt-1 text-3xl font-bold text-indigo-900"><?php echo e($stats['yoneticiOnayi']); ?></p>
    </div>
    <?php endif; ?>

    
    <div onclick="switchTab('tamamlananlar')" class="cursor-pointer hover:scale-105 transition-transform p-4 bg-gray-200 rounded-lg shadow-sm border border-gray-300">
        <p class="text-sm font-medium text-gray-800">Tamamlanan</p>
        <p class="mt-1 text-3xl font-bold text-gray-900"><?php echo e($stats['tamamlanan']); ?></p>
    </div>

    
    <div onclick="switchTab('reddedilenler')" class="cursor-pointer hover:scale-105 transition-transform p-4 bg-red-100 rounded-lg shadow-sm border border-red-200">
        <p class="text-sm font-medium text-red-800">Reddedilen</p>
        <p class="mt-1 text-3xl font-bold text-red-900"><?php echo e($stats['reddedilen']); ?></p>
    </div>

</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/iaa-yonetim/partials/stats-cards.blade.php ENDPATH**/ ?>