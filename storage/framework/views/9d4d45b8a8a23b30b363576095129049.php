<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['iaa', 'assignment', 'currentStep', 'progressUpdate', 'isTeamMember', 'takim']));

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

foreach (array_filter((['iaa', 'assignment', 'currentStep', 'progressUpdate', 'isTeamMember', 'takim']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-show="open" x-transition>
    <?php if($isTeamMember): ?>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('project.active-step', [
            'iaa' => $iaa, 
            'assignment' => $assignment, 
            'currentStep' => $currentStep, 
            'progressUpdate' => $progressUpdate
        ]);

$__html = app('livewire')->mount($__name, $__params, 'lw-2634632524-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php else: ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg shadow mt-4">
            <h3 class="text-xl font-bold text-yellow-800">Yönetici Gözlem Modu</h3>
            <p class="text-yellow-700 mt-2">
                Bu projenin ilerlemesini izliyorsunuz. Projeyi sadece atanmış olan <strong><?php echo e($takim->ad); ?></strong> takımı ilerletebilir.
                <br>
                Şu anki aktif adım: <strong><?php echo e($currentStep->name); ?></strong>.
            </p>
        </div>
    <?php endif; ?>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_step-content-active.blade.php ENDPATH**/ ?>