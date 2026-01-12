
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
    <p><strong>Öneren:</strong> 
        <!--[if BLOCK]><![endif]--><?php if($iaa->gonderen): ?>
            <?php echo e($iaa->gonderen->name); ?> (<?php echo e($iaa->gonderen->bolum->ad ?? ''); ?>)
        <?php else: ?>
            <?php echo e($iaa->guest_name); ?> (Misafir)
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </p>
    <p><strong>İlgili Alan/Bölüm:</strong> <?php echo e($iaa->bolum->ad ?? $iaa->ilgili_alan); ?></p>
    <p><strong>Gönderim Tarihi:</strong> <?php echo e($iaa->created_at->format('d.m.Y H:i')); ?></p>
    <div class="mt-4 p-4 bg-gray-50 rounded-md border">
        <h4 class="font-semibold mb-2">Mevcut Durum:</h4>
        <p class="prose prose-sm max-w-none"><?php echo nl2br(e($iaa->mevcut_durum)); ?></p>
    </div>
    <div class="mt-4 p-4 bg-blue-50 rounded-md border">
        <h4 class="font-semibold mb-2">Öneri:</h4>
        <p class="prose prose-sm max-w-none"><?php echo nl2br(e($iaa->oneri)); ?></p>
    </div>
    
    
    <!--[if BLOCK]><![endif]--><?php if($iaa->resimler->isNotEmpty()): ?>
    <div class="mt-4">
        <h4 class="font-semibold mb-2">Eklenen Resimler:</h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $iaa->resimler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <a href="<?php echo e(asset('storage/' . $resim->dosya_yolu)); ?>" target="_blank" class="block group relative">
                    <img src="<?php echo e(asset('storage/' . $resim->dosya_yolu)); ?>" alt="İAA Resmi" class="rounded-lg object-cover w-full h-28 transform group-hover:scale-105 transition-transform duration-300">
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/partials/iaa-detay-content.blade.php ENDPATH**/ ?>