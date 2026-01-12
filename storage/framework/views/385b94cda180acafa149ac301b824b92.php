<?php if(isset($isAdmin) && $isAdmin): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-b-4 border-green-500">
            <p class="text-gray-500 text-xs font-bold uppercase">Onaylanan Proje</p>
            <p class="text-3xl font-black text-gray-800"><?php echo e($adminStats['onaylanan_proje']); ?></p>
            <p class="text-xs text-gray-400 mt-1">Yönetici onayı verilen</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-b-4 border-red-500">
            <p class="text-gray-500 text-xs font-bold uppercase">Reddedilen Proje</p>
            <p class="text-3xl font-black text-gray-800"><?php echo e($adminStats['reddedilen_proje']); ?></p>
            <p class="text-xs text-gray-400 mt-1">Kapanışı uygun bulunmayan</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-b-4 border-blue-500">
            <p class="text-gray-500 text-xs font-bold uppercase">Havuza Eklenen</p>
            <p class="text-3xl font-black text-gray-800"><?php echo e($adminStats['havuza_eklenen']); ?></p>
            <p class="text-xs text-gray-400 mt-1">Öneri aşamasından geçen</p>
        </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-5 border-b-4 border-indigo-500">
            <p class="text-gray-500 text-xs font-bold uppercase">Tamamlanan Proje</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo e($tamamlananProjeSayisi); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-b-4 border-yellow-500">
            <p class="text-gray-500 text-xs font-bold uppercase">Aktif Görev</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo e($aktifProjeSayisi); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-b-4 border-green-500">
            <p class="text-gray-500 text-xs font-bold uppercase">Şikayet Bildirimi</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo e($girilenSikayetler->count()); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-b-4 border-purple-500">
            <p class="text-gray-500 text-xs font-bold uppercase">Son Proje Tarihi</p>
            <p class="text-lg font-bold text-gray-800"><?php echo e($sonProje ? $sonProje->updated_at->format('d.m.Y') : '-'); ?></p>
        </div>
    </div>
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/profile/partials/show/stats.blade.php ENDPATH**/ ?>