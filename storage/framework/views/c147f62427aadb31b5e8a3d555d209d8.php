<div class="flex justify-between items-start">
    <h2 class="text-2xl font-bold text-gray-900 pr-4"><?php echo e($iaa->baslik); ?></h2>
    <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['xOn:click' => '$dispatch(\'close\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$dispatch(\'close\')']); ?>&times; <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
</div>
<hr class="my-4">

<div class="space-y-4 text-sm max-h-[70vh] overflow-y-auto pr-2">
    
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
            <p class="text-xs text-gray-500 uppercase font-semibold">Öneren</p>
            <p class="font-medium text-gray-900">
                <!--[if BLOCK]><![endif]--><?php if($iaa->gonderen): ?>
                    <?php echo e($iaa->gonderen->name); ?> (<?php echo e($iaa->gonderen->bolum->ad ?? '-'); ?>)
                <?php else: ?>
                    <?php echo e($iaa->guest_name); ?> <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Misafir</span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </p>
        </div>
        
        
        <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
            <p class="text-xs text-blue-500 uppercase font-semibold">Sorumlu Bölüm (İlgili Alan)</p>
            <p class="font-bold text-blue-900 text-lg">
                <?php echo e($iaa->bolum->ad ?? $iaa->ilgili_alan ?? 'Genel'); ?>

            </p>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($iaa->atananTakim): ?>
        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 mt-2">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-indigo-200 rounded text-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h4 class="font-bold text-indigo-900">Atanan Çözüm Takımı: <?php echo e($iaa->atananTakim->ad); ?></h4>
            </div>
            
            <div class="pl-8">
                <p class="text-xs text-indigo-500 mb-1">Takım Üyeleri:</p>
                <div class="flex flex-wrap gap-2">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $iaa->atananTakim->uyeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uye): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white border border-indigo-200 text-indigo-800 shadow-sm">
                            <?php echo e($uye->name); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <span class="text-sm text-gray-500 italic">Üye bulunamadı.</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="grid grid-cols-2 gap-4 text-xs text-gray-500 mt-2">
        <p><strong>Gönderim Tarihi:</strong> <?php echo e($iaa->created_at->format('d.m.Y H:i')); ?></p>
        <!--[if BLOCK]><![endif]--><?php if($iaa->puan): ?>
            <p class="text-right"><span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded font-bold">Hesaplanan Puan: <?php echo e($iaa->puan); ?></span></p>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <div class="mt-4 p-4 bg-gray-50 rounded-md border">
        <h4 class="font-semibold mb-2 text-gray-700">Mevcut Durum:</h4>
        <p class="prose prose-sm max-w-none text-gray-600"><?php echo nl2br(e($iaa->mevcut_durum)); ?></p>
    </div>

    <div class="mt-4 p-4 bg-green-50 rounded-md border border-green-100">
        <h4 class="font-semibold mb-2 text-green-800">Öneri:</h4>
        <p class="prose prose-sm max-w-none text-gray-700"><?php echo nl2br(e($iaa->oneri)); ?></p>
    </div>
    
    
    <!--[if BLOCK]><![endif]--><?php if($iaa->resimler->isNotEmpty()): ?>
    <div class="mt-4">
        <h4 class="font-semibold mb-2">Eklenen Resimler:</h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $iaa->resimler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(asset('storage/' . $resim->dosya_yolu)); ?>" target="_blank" class="block group relative overflow-hidden rounded-lg shadow-sm border border-gray-200">
                    <img src="<?php echo e(asset('storage/' . $resim->dosya_yolu)); ?>" alt="İAA Resmi" class="object-cover w-full h-28 transform group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


    
    <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3 no-print">
        
        
        <a href="<?php echo e(url('iyilestirme/' . $iaa->id)); ?>" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            Öneri Detayına Git
        </a>

        
        <!--[if BLOCK]><![endif]--><?php if(in_array($iaa->durum, ['Atandı', 'Tamamlandı', 'Revize Ediliyor', 'Yönetici Onayı Bekliyor'])): ?>
            <a href="<?php echo e(url('proje-calisma-alani/' . $iaa->id)); ?>" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Projeye Git
            </a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    </div>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/raporlar/partials/iaa-detay-content.blade.php ENDPATH**/ ?>