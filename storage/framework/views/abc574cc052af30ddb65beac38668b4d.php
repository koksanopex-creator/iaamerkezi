
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['index', 'config']));

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

foreach (array_filter((['index', 'config']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
  $rootId = $this->getId();                      // <-- EKLENDİ
  $chartComponentId = $rootId . '-' . $index;    // <-- GÜNCELLENDİ
?>

<div x-data="genericChartComponent('<?php echo e($chartComponentId); ?>', 'bar', '<?php echo e($rootId); ?>')" x-init="initChart()">
    
    <h4 class="text-lg font-semibold text-gray-800 mb-1">
        
        <input type="text" wire:model="toolsData.bar_chart_data.<?php echo e($index); ?>.title" placeholder="Grafik Başlığı Girin" class="text-lg font-semibold text-gray-800 border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-indigo-500 p-0 w-full">
         <!--[if BLOCK]><![endif]--><?php if($config['required'] ?? false): ?> <span class="text-red-500">*</span> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </h4>
     <div class="grid grid-cols-2 gap-4 mb-4 text-sm mt-2">
         <div>
            <label class="text-xs font-medium text-gray-500">X Ekseni Başlığı</label>
            
            <input type="text" wire:model="toolsData.bar_chart_data.<?php echo e($index); ?>.axis_x_label" placeholder="Örn: Aylar, Kategoriler" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
         <div>
            <label class="text-xs font-medium text-gray-500">Y Ekseni Başlığı</label>
            
            <input type="text" wire:model="toolsData.bar_chart_data.<?php echo e($index); ?>.axis_y_label" placeholder="Örn: Miktar, Adet" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
    </div>

    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-6">
        <div>
            <div class="flex justify-between items-center mb-2">
                
                <h5 class="font-semibold text-gray-700"><?php echo e($toolsData['bar_chart_data'][$index]['title'] ?? $config['title'] ?? 'Grafik Önizleme'); ?></h5>
                <button type="button" wire:click="generateBarChartData(<?php echo e($index); ?>)" class="bg-indigo-600 text-white font-semibold py-1 px-3 rounded-md text-sm hover:bg-indigo-700">Grafiği Oluştur/Güncelle</button>
            </div>
            <div wire:ignore class="bg-white p-2 rounded-lg border" style="height: 300px;">
                <canvas id="genericChart-<?php echo e($chartComponentId); ?>"></canvas>
            </div>
        </div>
        <div>
            <h5 class="font-semibold text-gray-700 mb-2">Veri Girişi</h5>
            <div class="overflow-x-auto bg-white rounded-lg border">
                <table class="min-w-full"><thead class="bg-gray-100"><tr>
                <th class="p-2 text-left text-sm font-semibold text-gray-600">Etiket (X Ekseni)</th>
                <th class="p-2 text-left text-sm font-semibold text-gray-600">Değer (Y Ekseni)</th>
                <th class="w-16"></th></tr></thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $toolsData['bar_chart_data'][$index]['rows'] ?? [['label'=>'', 'value'=>'']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rowIndex => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b" wire:key="bar-chart-row-<?php echo e($index); ?>-<?php echo e($rowIndex); ?>">
                        
                        <td class="p-1"><input type="text" wire:model="toolsData.bar_chart_data.<?php echo e($index); ?>.rows.<?php echo e($rowIndex); ?>.label" class="w-full text-sm border-gray-300 rounded-md"></td>
                        <td class="p-1"><input type="number" step="any" wire:model="toolsData.bar_chart_data.<?php echo e($index); ?>.rows.<?php echo e($rowIndex); ?>.value" class="w-full text-sm border-gray-300 rounded-md"></td>
                        <td class="p-1 text-center">
                            <!--[if BLOCK]><![endif]--><?php if(count($toolsData['bar_chart_data'][$index]['rows']) > 1): ?>
                            <button type="button" wire:click="removeBarChartRow(<?php echo e($index); ?>, <?php echo e($rowIndex); ?>)" class="text-red-500 hover:text-red-700 p-1 rounded-full font-bold text-lg">&times;</button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
                </table>
            </div>
            <button type="button" wire:click="addBarChartRow(<?php echo e($index); ?>)" class="mt-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Satır Ekle</button>
        </div>
    </div>
</div>

<?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/project/widgets/_bar-chart.blade.php ENDPATH**/ ?>