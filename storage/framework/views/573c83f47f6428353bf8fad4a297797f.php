<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 max-w-5xl">

    
    <div class="p-3 bg-white rounded-xl shadow border border-gray-100">
        <p class="text-[10px] font-bold text-gray-500 uppercase">Toplam</p>
        <p x-show="mode === 'month'" class="text-xl font-black text-gray-800">
            <?php echo e($kpiMonthly['toplam']); ?>

        </p>
        <p x-show="mode === 'all'" class="text-xl font-black text-indigo-700">
            <?php echo e($kpi['toplam']); ?>

        </p>
    </div>

    
    <div class="p-3 bg-white rounded-xl shadow border border-gray-100">
        <p class="text-[10px] font-bold text-blue-600 uppercase">Açık / İşlemde</p>
        <p x-show="mode === 'month'" class="text-xl font-black text-blue-600">
            <?php echo e($kpiMonthly['acik']); ?>

        </p>
        <p x-show="mode === 'all'" class="text-xl font-black text-blue-600">
            <?php echo e($kpi['acik']); ?>

        </p>
    </div>

    
    <div class="p-3 bg-white rounded-xl shadow border border-gray-100">
        <p class="text-[10px] font-bold text-red-600 uppercase">Geciken</p>
        <p x-show="mode === 'month'" class="text-xl font-black text-red-600">
            <?php echo e($kpiMonthly['geciken']); ?>

        </p>
        <p x-show="mode === 'all'" class="text-xl font-black text-red-600">
            <?php echo e($kpi['geciken']); ?>

        </p>
    </div>

    
    <div class="p-3 bg-white rounded-xl shadow border border-gray-100">
        <p class="text-[10px] font-bold text-green-600 uppercase">Çözülen</p>
        <p x-show="mode === 'month'" class="text-xl font-black text-green-600">
            <?php echo e($kpiMonthly['cozulen']); ?>

        </p>
        <p x-show="mode === 'all'" class="text-xl font-black text-green-600">
            <?php echo e($kpi['cozulen']); ?>

        </p>
    </div>

    
    <div class="p-3 bg-white rounded-xl shadow border border-gray-100">
        <p class="text-[10px] font-bold text-purple-600 uppercase">Ort. Çözüm Hızı</p>
        <p x-show="mode === 'month'" class="text-xl font-black text-purple-600">
            <?php echo e($kpiMonthly['ortalama_sure']); ?> gün
        </p>
        <p x-show="mode === 'all'" class="text-xl font-black text-purple-600">
            <?php echo e($kpi['ortalama_sure']); ?> gün
        </p>
    </div>

</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/partials/executive/kpi-cards.blade.php ENDPATH**/ ?>