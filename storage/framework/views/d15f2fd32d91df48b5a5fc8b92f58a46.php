<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['iaa', 'statusDate' => null]));

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

foreach (array_filter((['iaa', 'statusDate' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?> 

<?php
    $sonucKutusu = match($iaa->durum) {
        'Tamamlandı' => [
            'renk' => 'green',
            'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'baslik' => 'Proje Başarıyla Onaylandı!',
            'mesaj' => 'Tüm adımlar tamamlandı ve proje yönetici tarafından onaylandı. Harika iş çıkardınız!'
        ],
        'Tamamlanması Reddedildi' => [
            'renk' => 'red',
            'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
            'baslik' => 'Proje Reddedildi',
            'mesaj' => $iaa->yonetici_notu ?? 'Proje adımları tamamlandı ancak yönetici tarafından reddedildi.'
        ],
        'Revize Ediliyor' => [
            'renk' => 'yellow',
            'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0011.667 0l3.182-3.182m0-4.991v4.99" />',
            'baslik' => 'Revizyon Bekleniyor',
            'mesaj' => $iaa->yonetici_notu ?? 'Proje, yönetici tarafından incelenip revizyona gönderildi.'
        ],
        default => [ // Yönetici Onayı Bekliyor durumu
            'renk' => 'blue',
            'ikon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'baslik' => 'Yönetici Onayı Bekleniyor',
            'mesaj' => 'Projenin tüm adımlarını başarıyla tamamladınız. Şimdi yönetici onayı bekleniyor.'
        ],
    };
?>

<div class="bg-gradient-to-r from-<?php echo e($sonucKutusu['renk']); ?>-50 to-<?php echo e($sonucKutusu['renk']); ?>-100 p-8 text-center rounded-xl shadow-lg border-2 border-<?php echo e($sonucKutusu['renk']); ?>-200">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-<?php echo e($sonucKutusu['renk']); ?>-500 rounded-full mb-4">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <?php echo $sonucKutusu['ikon']; ?>

        </svg>
    </div>
    <h3 class="text-2xl font-bold text-<?php echo e($sonucKutusu['renk']); ?>-700 mb-2"><?php echo e($sonucKutusu['baslik']); ?></h3>
    <p class="text-gray-700 mt-2 whitespace-pre-wrap"><?php echo e($sonucKutusu['mesaj']); ?></p>
</div><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_project-final-status.blade.php ENDPATH**/ ?>