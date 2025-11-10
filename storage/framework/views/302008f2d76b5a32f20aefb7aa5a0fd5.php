

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'step',
    'isCompleted',
    'isCurrent',
    'progressUpdate',
    'isTeamMember',
    'iaa',
    'assignment',
    'takim'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'step',
    'isCompleted',
    'isCurrent',
    'progressUpdate',
    'isTeamMember',
    'iaa',
    'assignment',
    'takim'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="mb-10 ml-6" x-data="{ open: <?php echo e($isCompleted ? 'false' : ($isCurrent ? 'true' : 'false')); ?> }">
    
    
    <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white shadow-md transition-all duration-300
        <?php echo e($isCompleted ? 'bg-gradient-to-br from-green-400 to-green-600' : ($isCurrent ? 'bg-gradient-to-br from-blue-400 to-blue-600 animate-pulse' : 'bg-gray-300')); ?>">
        <?php if($isCompleted): ?> 
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        <?php elseif($isCurrent): ?>
            <span class="w-3 h-3 bg-white rounded-full"></span>
        <?php endif; ?>
    </span>
    
    
    <div class="bg-white border-2 <?php echo e($isCurrent ? 'border-blue-300 shadow-lg' : 'border-gray-200'); ?> rounded-xl p-5 hover:shadow-md transition-shadow duration-300">
        
        
        <div <?php if($isCompleted || $isCurrent): ?> @click="open = !open" class="cursor-pointer" <?php else: ?> class="cursor-default" <?php endif; ?>>
            <h4 class="flex items-center justify-between text-base font-semibold <?php echo e($isCurrent ? 'text-blue-700' : 'text-gray-900'); ?>">
                <span><?php echo e($step->order); ?>. <?php echo e($step->name); ?></span>
                <div>
                    <?php if($isCompleted): ?> 
                        <span class="bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm">✓ Tamamlandı</span> 
                    <?php endif; ?>
                    <?php if($isCurrent): ?> 
                        <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm animate-pulse">● Aktif Adım</span> 
                    <?php endif; ?>
                </div>
            </h4>
            <p class="text-sm font-normal text-gray-600 mt-2"><?php echo e($step->description); ?></p>
        </div>
        
        
        <?php if($isCompleted && $progressUpdate): ?>
            <?php echo $__env->make('proje-calisma-alani.partials._step-content-completed', [
                'progressUpdate' => $progressUpdate,
                'step' => $step
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        
        <?php if($isCurrent): ?>
             <?php echo $__env->make('proje-calisma-alani.partials._step-content-active', [
                'iaa' => $iaa,
                'assignment' => $assignment,
                'currentStep' => $step, // $currentStep yerine $step kullanmak daha doğru
                'progressUpdate' => $progressUpdate,
                'isTeamMember' => $isTeamMember,
                'takim' => $takim
             ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_step-item.blade.php ENDPATH**/ ?>