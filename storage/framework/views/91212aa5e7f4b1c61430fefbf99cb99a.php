<div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
    
    <div class="flex items-center justify-between mb-8">
        <?php $__env->startPush('pageTitle'); ?>
    Takvim Eşleştirme | 
<?php $__env->stopPush(); ?>

<div>
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                Takvim Fabrika Eşleştirmesi
            </h2>
            <p class="mt-1 text-gray-500 text-sm">IAA bölümlerini Takvim uygulamasındaki ilgili fabrikalarla eşleştirin.</p>
        </div>
        
        <button wire:click="saveMappings" 
                class="inline-flex items-center px-6 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all active:scale-95 shadow-lg shadow-indigo-600/20">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Değişiklikleri Kaydet
        </button>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl flex items-center gap-3 animate-bounce">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <?php echo e(session('message')); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('error')): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="overflow-hidden border border-gray-100 rounded-2xl">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">IAA Bölümü</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Takvim Karşılığı (Fabrika)</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $bolumler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bolum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center font-bold text-indigo-600 text-xs">
                                    <?php echo e(substr($bolum->ad, 0, 1)); ?>

                                </div>
                                <span class="text-sm font-bold text-gray-900"><?php echo e($bolum->ad); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select wire:model="mappings.<?php echo e($bolum->id); ?>" 
                                    class="block w-full max-w-xs rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500/20 transition-all">
                                <option value="">-- Eşleştirilmemiş --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $takvimBusinessUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bu['id']); ?>"><?php echo e($bu['name']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <!--[if BLOCK]><![endif]--><?php if(isset($mappings[$bolum->id]) && $mappings[$bolum->id] != ''): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    Eşleştirildi
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Bekliyor
                                </span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    
    <div class="mt-8 p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100">
        <h3 class="text-sm font-bold text-indigo-900 flex items-center gap-2 mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Nasıl Çalışır?
        </h3>
        <p class="text-sm text-indigo-700 leading-relaxed">
            Burada yaptığınız eşleştirmeler, IAA üzerinden açılan şikayetlerin Takvim uygulamasındaki doğru fabrikada (Business Unit) görünmesini sağlar. 
            Eğer bir bölüm eşleştirilmemişse, Takvim uygulaması ilgili şikayeti müşterinin kayıtlı olduğu varsayılan fabrikaya atar.
        </p>
    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/admin/takvim-mapping.blade.php ENDPATH**/ ?>