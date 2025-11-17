<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['progressUpdate', 'step']));

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

foreach (array_filter((['progressUpdate', 'step']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-show="open" x-transition class="mt-4 border-t-2 border-gray-100 pt-4 space-y-6">
    
    <div class="flex justify-between items-center text-xs text-gray-500">
        <span class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <?php echo e($progressUpdate->completed_at ? \Carbon\Carbon::parse($progressUpdate->completed_at)->format('d.m.Y H:i') : '-'); ?>

        </span>
        <form action="<?php echo e(route('proje.workspace.reopenStep', $progressUpdate)); ?>" method="POST" onsubmit="return confirm('Bu adımı yeniden düzenlemeye açmak istediğinizden emin misiniz?');">
            <?php echo csrf_field(); ?>
            <button type="submit" class="font-semibold text-blue-600 hover:text-blue-800 transition-colors">Yeniden Düzenle</button>
        </form>
    </div>

    
    <?php
        $reportData = $progressUpdate->content ? json_decode($progressUpdate->content, true) : null;
        $formData = $reportData['form_data'] ?? [];
        $toolsData = $reportData['tools'] ?? []; // five_whys, fishbone, pareto, bar_chart_data, line_chart_data içerir
    ?>

    <?php if(!$reportData): ?>
         <div class="text-sm text-red-600 italic">Bu adım için veri bulunamadı veya veri formatı bozuk.</div>
    <?php else: ?>
        
        <?php $__currentLoopData = $step->widgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $widgetType = $widget['type'] ?? 'unknown';
                // Widget tanımından gelen config (sadece varsayılanlar için kullanılacak)
                $widgetConfigDefaults = $widget['config'] ?? []; 
                $widgetValue = $formData[$index] ?? null; // Form verisi
                 // Kaydedilmiş araç verisi (config + rows içerir)
                 $toolValue = null;
                 if ($widgetType === 'five_whys') $toolValue = $toolsData['five_whys'] ?? null;
                 elseif ($widgetType === 'fishbone') $toolValue = $toolsData['fishbone'] ?? null;
                 elseif ($widgetType === 'pareto') $toolValue = $toolsData['pareto'] ?? null;
                 elseif ($widgetType === 'bar_chart') $toolValue = $toolsData['bar_chart_data'][$index] ?? null;
                 elseif ($widgetType === 'line_chart') $toolValue = $toolsData['line_chart_data'][$index] ?? null;

            ?>

            <div class="mb-6"> 
                
                <?php if($widgetType === 'info_text'): ?>
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                         
                         <h5 class="text-base font-semibold text-blue-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? 'Bilgilendirme'); ?></h5>
                        <div class="mt-1 text-sm text-blue-700 prose prose-sm max-w-none">
                           <?php echo nl2br(e($widgetConfigDefaults['content'] ?? '')); ?>

                        </div>
                    </div>

                
                 <?php elseif(!in_array($widgetType, ['five_whys', 'fishbone', 'pareto', 'bar_chart', 'line_chart'])): ?>
                     <div class="text-sm max-w-none">
                         
                         <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace('_', ' ', $widgetType))); ?></h5>

                         <?php if($widgetType === 'textbox'): ?>
                            <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200 min-h-[50px] whitespace-pre-wrap">
                                <?php echo !empty($widgetValue['text']) ? nl2br(e($widgetValue['text'])) : '<span class="text-gray-400 italic">Girilmemiş</span>'; ?>

                            </p>
                        <?php elseif($widgetType === 'user_select'): ?>
                            <?php $user = isset($widgetValue['user_id']) ? \App\Models\User::find($widgetValue['user_id']) : null; ?>
                            <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <?php echo $user?->name ?? '<span class="text-gray-400 italic">Seçilmemiş</span>'; ?>

                            </p>
                         <?php elseif($widgetType === 'date_picker'): ?>
                            <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <?php echo isset($widgetValue['date']) && $widgetValue['date'] ? \Carbon\Carbon::parse($widgetValue['date'])->format('d.m.Y') : '<span class="text-gray-400 italic">Tarih Girilmemiş</span>'; ?>

                            </p>
                            <?php elseif($widgetType === 'file_upload'): ?>
                                <?php if(!empty($widgetValue['files']) && is_array($widgetValue['files'])): ?>
                                    <div class="mt-1 flex flex-wrap gap-3">
                                        <?php $__currentLoopData = $widgetValue['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filePath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $isImage = Str::endsWith(strtolower($filePath), ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.webp']); ?>
                                            <?php if($isImage): ?>
                                                
                                                <a href="<?php echo e(asset('storage/' . $filePath)); ?>" data-fancybox="gallery-<?php echo e($step->id); ?>-<?php echo e($index); ?>" data-caption="<?php echo e(basename($filePath)); ?>" class="block">
                                                    
                                                    <img src="<?php echo e(asset('storage/' . $filePath)); ?>" alt="<?php echo e(basename($filePath)); ?>" class="h-24 w-24 object-cover rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                                </a>
                                            <?php else: ?>
                                                
                                                <a href="<?php echo e(asset('storage/' . $filePath)); ?>" target="_blank" class="flex items-center gap-2 text-blue-600 hover:underline bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm">
                                                    <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" /></svg>
                                                    <span><?php echo e(basename($filePath)); ?></span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Dosya yüklenmemiş.</p>
                                <?php endif; ?>
                        <?php else: ?>
                             <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Veri gösterimi desteklenmiyor: <?php echo e($widgetType); ?></p>
                        <?php endif; ?>
                     </div>

                
                
                <?php elseif($widgetType === 'five_whys' && !empty($toolValue) && count(array_filter($toolValue)) > 0): ?>
                     
                     <div class="text-sm max-w-none"> <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? '5 Neden Analizi Sonuçları'); ?></h5> <dl class="border rounded-lg p-4 bg-indigo-50/50"> <?php $__currentLoopData = $toolValue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if(!empty($value) && str_starts_with($key, 'why')): ?> <dt class="font-bold text-gray-600"><?php echo e(str_replace('why', '', $key)); ?>. Neden?</dt> <dd class="ml-4 mb-2 text-gray-800 whitespace-pre-wrap"><?php echo e($value); ?></dd> <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> </dl> </div>
                
                 <?php elseif($widgetType === 'fishbone' && !empty($toolValue) && count(array_filter(array_slice($toolValue, 1))) > 0): ?>
                      
                      <div class="text-sm max-w-none mt-4"> <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? 'Balık Kılçığı Analizi Sonuçları'); ?></h5> <div class="border rounded-lg p-4 bg-gray-50"> <p class="mb-4"><span class="font-bold text-red-700">Problem:</span> <?php echo e($toolValue['problem'] ?? ''); ?></p> <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4"> <?php $__currentLoopData = ['insan', 'yontem', 'makine', 'malzeme', 'olcum', 'cevre']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if(!empty($toolValue[$key])): ?> <div> <dt class="font-bold text-gray-700 capitalize"><?php echo e($key); ?></dt> <dd class="ml-4 mt-1 text-gray-600 whitespace-pre-wrap"><?php echo e($toolValue[$key]); ?></dd> </div> <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> </dl> </div> </div>
                
                <?php elseif($widgetType === 'pareto' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'problem'))) > 0 ): ?>
                      <?php /* Pareto hesaplama */ $pareto = $toolValue; $rows = $pareto['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => !empty($row['problem']) && isset($row['frequency']) && is_numeric($row['frequency']) && $row['frequency'] > 0)->sortByDesc('frequency')->values(); $totalFrequency = $processedData->sum('frequency'); $cumulative = 0; $tableRows = $processedData->map(function ($item) use ($totalFrequency, &$cumulative) { $cumulative += (float)$item['frequency']; $item['cumulative_sum'] = $cumulative; $item['cumulative_percentage'] = $totalFrequency > 0 ? round(($cumulative / $totalFrequency) * 100, 2) : 0; return $item; }); $chartDataForJs = [ 'labels' => $tableRows->pluck('problem')->toArray(), 'frequencies' => $tableRows->pluck('frequency')->toArray(), 'percentages' => $tableRows->pluck('cumulative_percentage')->toArray(), 'header2' => $pareto['header2'] ?? 'Sıklık', ]; $header1 = $pareto['header1'] ?? 'Problem'; $header2 = $pareto['header2'] ?? 'Sıklık'; $chartId = "paretoChart-" . $progressUpdate->id . "-" . $index; ?> 
                      
                      <div class="text-sm max-w-none mt-4"> <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? 'Pareto Analizi Sonuçları'); ?></h5> <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="<?php echo e($chartId); ?>"></canvas> </div> <div class="overflow-x-auto border rounded-lg"> <table class="min-w-full text-sm"> <thead class="bg-gray-100"><tr><th class="p-2 text-left font-bold">#</th> <th class="p-2 text-left font-bold"><?php echo e($header1); ?></th> <th class="p-2 text-right font-bold"><?php echo e($header2); ?></th> <th class="p-2 text-right font-bold">Toplam <?php echo e($header2); ?></th> <th class="p-2 text-right font-bold">Kümülatif %</th></tr></thead> <tbody class="divide-y"> <?php $__currentLoopData = $tableRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <tr> <td class="p-2"><?php echo e($loop->iteration); ?></td> <td class="p-2"><?php echo e($row['problem']); ?></td> <td class="p-2 text-right"><?php echo e(number_format($row['frequency'], 0)); ?></td> <td class="p-2 text-right"><?php echo e(number_format($row['cumulative_sum'], 0)); ?></td> <td class="p-2 text-right font-bold"><?php echo e(number_format($row['cumulative_percentage'], 2)); ?>%</td> </tr> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> </tbody> </table> </div> </div> <?php $__env->startPush('scripts'); ?> <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('<?php echo e($chartId); ?>'); const chartData = <?php echo json_encode($chartDataForJs, 15, 512) ?>; if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'bar', data: { labels: chartData.labels, datasets: [ { label: chartData.header2, data: chartData.frequencies, backgroundColor: 'rgba(59, 130, 246, 0.5)', borderColor: 'rgba(59, 130, 246, 1)', yAxisID: 'y', }, { label: 'Kümülatif %', data: chartData.percentages, type: 'line', borderColor: 'rgba(239, 68, 68, 1)', tension: 0.1, yAxisID: 'y1', } ] }, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, scales: { y: { type: 'linear', display: true, position: 'left', beginAtZero: true, title: { display: true, text: chartData.header2 } }, y1: { type: 'linear', display: true, position: 'right', min: 0, max: 100, grid: { drawOnChartArea: false }, ticks: { callback: value => value + '%' }, title: { display: true, text: 'Kümülatif %' } } } } }); } }); </script> <?php $__env->stopPush(); ?>

                
                
                <?php elseif($widgetType === 'bar_chart' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'label'))) > 0 ): ?>
                     <?php /* Sütun Grafiği verisini hesapla */ $rows = $toolValue['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => isset($row['label']) && $row['label'] !== '' && isset($row['value']) && is_numeric($row['value']))->values(); 
                         // DÜZELTME: Başlıkları $toolValue'dan al, yoksa widgetConfig'den
                         $chartDataForJs = [ 
                             'labels' => $processedData->pluck('label')->toArray(), 
                             'values' => $processedData->pluck('value')->toArray(), 
                             'title' => $toolValue['title'] ?? $widgetConfigDefaults['title'] ?? 'Sütun Grafiği', 
                             'axis_x' => $toolValue['axis_x_label'] ?? $widgetConfigDefaults['axis_x_label'] ?? 'Kategoriler', 
                             'axis_y' => $toolValue['axis_y_label'] ?? $widgetConfigDefaults['axis_y_label'] ?? 'Değerler', 
                         ]; 
                         $chartId = "barChart-" . $progressUpdate->id . "-" . $index; 
                     ?> 
                     <div class="text-sm max-w-none mt-4"> 
                         <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($chartDataForJs['title']); ?></h5> 
                         <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="<?php echo e($chartId); ?>"></canvas> </div> 
                     </div> 
                     <?php $__env->startPush('scripts'); ?> <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('<?php echo e($chartId); ?>'); const chartData = <?php echo json_encode($chartDataForJs, 15, 512) ?>; if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'bar', data: { labels: chartData.labels, datasets: [{ label: chartData.axis_y, data: chartData.values, backgroundColor: 'rgba(75, 192, 192, 0.5)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: chartData.title }, legend: { display: false } }, scales: { x: { title: { display: true, text: chartData.axis_x } }, y: { beginAtZero: true, title: { display: true, text: chartData.axis_y } } } } }); } }); </script> <?php $__env->stopPush(); ?>
                
                <?php elseif($widgetType === 'line_chart' && !empty($toolValue) && !empty($toolValue['rows']) && count(array_filter(array_column($toolValue['rows'], 'label'))) > 0 ): ?>
                    <?php /* Çizgi Grafiği verisini hesapla */ $rows = $toolValue['rows'] ?? []; $processedData = collect($rows)->filter(fn($row) => isset($row['label']) && $row['label'] !== '' && isset($row['value']) && is_numeric($row['value']))->values(); 
                        // DÜZELTME: Başlıkları $toolValue'dan al, yoksa widgetConfig'den
                        $chartDataForJs = [ 
                            'labels' => $processedData->pluck('label')->toArray(), 
                            'values' => $processedData->pluck('value')->toArray(), 
                            'title' => $toolValue['title'] ?? $widgetConfigDefaults['title'] ?? 'Çizgi Grafiği', 
                            'axis_x' => $toolValue['axis_x_label'] ?? $widgetConfigDefaults['axis_x_label'] ?? 'Kategoriler', 
                            'axis_y' => $toolValue['axis_y_label'] ?? $widgetConfigDefaults['axis_y_label'] ?? 'Değerler', 
                        ]; 
                        $chartId = "lineChart-" . $progressUpdate->id . "-" . $index; 
                    ?> 
                    <div class="text-sm max-w-none mt-4"> 
                        <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($chartDataForJs['title']); ?></h5> 
                        <div class="border rounded-lg p-2 bg-white mb-4" style="height: 300px;"> <canvas id="<?php echo e($chartId); ?>"></canvas> </div> 
                    </div> 
                    <?php $__env->startPush('scripts'); ?> <script> document.addEventListener('DOMContentLoaded', function () { const canvas = document.getElementById('<?php echo e($chartId); ?>'); const chartData = <?php echo json_encode($chartDataForJs, 15, 512) ?>; if (canvas && chartData && typeof Chart !== 'undefined') { new Chart(canvas.getContext('2d'), { type: 'line', data: { labels: chartData.labels, datasets: [{ label: chartData.axis_y, data: chartData.values, borderColor: 'rgba(75, 192, 192, 1)', tension: 0.1, fill: false }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: chartData.title }, legend: { display: false } }, scales: { x: { title: { display: true, text: chartData.axis_x } }, y: { beginAtZero: true, title: { display: true, text: chartData.axis_y } } } } }); } }); </script> <?php $__env->stopPush(); ?>
                
                 <?php elseif(in_array($widgetType, ['five_whys', 'fishbone', 'pareto', 'bar_chart', 'line_chart'])): ?>
                      <div class="text-sm max-w-none">
                         
                         <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace(['_', 'chart', 'data'], [' ', '', ''], $widgetType))); ?></h5>
                         <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Bu araç için veri girilmemiş.</p>
                     </div>
                <?php endif; ?>
                
            </div> 
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
    <?php endif; ?> 
</div>


<?php if (! $__env->hasRenderedOnce('8bfed98d-b6ef-489a-9bcd-9d78fe167955')): $__env->markAsRenderedOnce('8bfed98d-b6ef-489a-9bcd-9d78fe167955');
$__env->startPush('scripts'); ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
         function initFancybox() { Fancybox.bind("[data-fancybox]", { /* Custom options */ }); }
         document.addEventListener('DOMContentLoaded', initFancybox);
         // Livewire v3 uses 'navigate' event
         document.addEventListener('livewire:navigated', () => { if (typeof Fancybox !== 'undefined') { Fancybox.destroy(); } initFancybox(); });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script> 
<?php $__env->stopPush(); endif; ?>

<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/proje-calisma-alani/partials/_step-content-completed.blade.php ENDPATH**/ ?>