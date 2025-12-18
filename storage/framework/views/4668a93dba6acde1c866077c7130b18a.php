<div class="mb-8">
    <div class="rounded-2xl border border-gray-200/70 bg-white/80 backdrop-blur p-6 shadow-sm flex flex-col lg:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Müşteri Şikayetleri</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Şikayet havuzu ve yönetim paneli
            </p>
        </div>
        
        <div class="flex gap-3">
            <!--[if BLOCK]><![endif]--><?php if(!Auth::user()->hasRole('Yonetim')): ?>
                <a href="<?php echo e(route('admin.sikayetler.create')); ?>" class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Yeni Şikayet
                </a>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|Müşteri Şikayeti Kurulu')): ?>
                <a href="<?php echo e(route('admin.sikayetler.kurulGirdileri')); ?>" class="inline-flex items-center px-5 py-2.5 rounded-xl font-bold text-indigo-700 bg-white border border-indigo-200 hover:bg-indigo-50 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    Kurul Girdileri
                </a>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/sikayetler-partials/header.blade.php ENDPATH**/ ?>