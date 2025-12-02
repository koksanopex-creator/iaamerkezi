<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['data', 'title']));

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

foreach (array_filter((['data', 'title']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="grid grid-cols-5 gap-2">
    <div class="bg-white p-2 rounded-lg border shadow-sm flex flex-col justify-center items-center h-24">
        <span class="text-[9px] uppercase text-gray-400 font-bold mb-1 text-center leading-tight"><?php echo e($title); ?></span>
        <span class="text-2xl font-black text-gray-800"><?php echo e($data['toplam'] ?? 0); ?></span>
        <span class="text-[8px] text-gray-400">Şikayet</span>
    </div>

    <div class="bg-blue-50 p-2 rounded-lg border border-blue-100 flex flex-col justify-center items-center h-24">
        <span class="text-[9px] uppercase text-blue-400 font-bold mb-1">AÇIK</span>
        <span class="text-2xl font-black text-blue-600"><?php echo e($data['acik'] ?? 0); ?></span>
    </div>

    <div class="bg-red-50 p-2 rounded-lg border border-red-100 flex flex-col justify-center items-center h-24 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-bl-lg"></div>
        <span class="text-[9px] uppercase text-red-400 font-bold mb-1">GECİKEN</span>
        <span class="text-2xl font-black text-red-600"><?php echo e($data['geciken'] ?? 0); ?></span>
    </div>

    <div class="bg-green-50 p-2 rounded-lg border border-green-100 flex flex-col justify-center items-center h-24">
        <span class="text-[9px] uppercase text-green-400 font-bold mb-1">ÇÖZÜLEN</span>
        <span class="text-2xl font-black text-green-600"><?php echo e($data['cozulen'] ?? 0); ?></span>
    </div>

    <div class="bg-indigo-50 p-2 rounded-lg border border-indigo-100 flex flex-col justify-center items-center h-24">
        <span class="text-[9px] uppercase text-indigo-400 font-bold mb-1">HIZ</span>
        <span class="text-2xl font-black text-indigo-600"><?php echo e($data['ortalama_sure'] ?? 0); ?></span>
        <span class="text-[8px] text-indigo-400">Gün/Ort</span>
    </div>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/components/kpi-row.blade.php ENDPATH**/ ?>