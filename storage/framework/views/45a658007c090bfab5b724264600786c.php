<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['progressUpdate', 'step', 'isAssignedToSomeoneElse' => false, 'assignments' => collect(), 'canEdit' => false]));

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

foreach (array_filter((['progressUpdate', 'step', 'isAssignedToSomeoneElse' => false, 'assignments' => collect(), 'canEdit' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-show="open" x-transition class="mt-4 border-t-2 border-gray-100 pt-4 space-y-6">
    
    
    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
        
        
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <span class="font-medium">Tamamlanma:</span>
                <span class="font-bold text-gray-800">
                    <?php echo e($progressUpdate->completed_at ? \Carbon\Carbon::parse($progressUpdate->completed_at)->format('d.m.Y H:i') : '-'); ?>

                </span>
                <span class="mx-1 font-medium text-gray-400">|</span>
                <span class="font-medium text-gray-600">Tamamlayan:</span>
                <span class="font-bold text-indigo-600"><?php echo e($progressUpdate->user->name ?? 'Bilinmeyen Kullanıcı'); ?></span>
            </div>
        </div>

        
        <?php
            // Projenin durumunu kontrol et (Controller'da yaptığımız kilit mantığı)
            // Not: Bu partial içinde $iaa değişkeni direkt gelmeyebilir, $progressUpdate üzerinden erişiriz.
            // $iaa değişkeni show.blade.php'den gelebilir ama garanti olsun diye sorgulayalım:
            
            $iaaDurum = null;
            // Eğer üst katmandan $iaa geldiyse kullan, yoksa sorgula (Performans için üstten gelmesi iyidir)
            if(isset($iaa)) {
                $iaaDurum = $iaa->durum;
            } else {
                // Veritabanından bul (Maliyetli ama güvenli)
                $iaaDurum = DB::table('iaa_talepleri')
                    ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                    ->where('iaa_talepleri.id', $progressUpdate->iaa_talep_id)
                    ->value('iaas.durum');
            }

            $kilitliDurumlar = ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı'];
            $isLocked = in_array($iaaDurum, $kilitliDurumlar);
        ?>

        <?php if(!$isLocked && $canEdit): ?>
            <?php if(!$isAssignedToSomeoneElse): ?>
                <form action="<?php echo e(route('proje.workspace.reopenStep', $progressUpdate)); ?>" method="POST" onsubmit="return confirm('Dikkat: Bu adımı yeniden açmak, onay sürecini sıfırlayabilir. Devam etmek istiyor musunuz?');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Düzenle / Aç
                    </button>
                </form>
            <?php else: ?>
                <?php 
                    $sorumluUserIds = $assignments->pluck('user_id')->toArray();
                    $sorumluNames = \App\Models\User::whereIn('id', $sorumluUserIds)->pluck('name')->implode(', ');
                ?>
                <span class="inline-flex items-center px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-md font-semibold text-xs text-blue-500 uppercase tracking-widest cursor-help" title="Bu adım '<?php echo e($sorumluNames ?: 'ekibe'); ?>' atanmıştır. Sadece sorumlu kişiler veya lider düzenleyebilir.">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Sorumlu: <?php echo e($sorumluNames ? Str::limit($sorumluNames, 20) : 'Atanmış'); ?>

                </span>
            <?php endif; ?>
        <?php else: ?>
            <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-400 uppercase tracking-widest cursor-not-allowed" title="<?php echo e($iaaDurum == 'Direktör Onayı Bekliyor' ? 'Proje direktör onayında. Müdahale için önce onayınızı geri çekmelisiniz.' : 'Proje onay aşamasında veya tamamlandığı için düzenleme yapılamaz.'); ?>">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Kilitli
            </span>
        <?php endif; ?>
    </div>

    
    <?php
        $reportData = $progressUpdate->content ? json_decode($progressUpdate->content, true) : null;
        $formData = $reportData['form_data'] ?? [];
        $toolsData = $reportData['tools'] ?? []; // five_whys, fishbone, pareto, bar_chart_data, line_chart_data içerir
    ?>

    <?php if(!$reportData): ?>
        
        <div class="p-4 bg-white border border-gray-200 rounded-xl text-gray-800 text-sm whitespace-pre-wrap shadow-sm ring-1 ring-black/5">
            <div class="flex items-center gap-2 font-bold text-gray-400 uppercase text-[10px] mb-3 tracking-widest border-b border-gray-100 pb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                ADIM RAPORU / İÇERİK
            </div>
            <div class="leading-relaxed text-gray-700">
                <?php echo e($progressUpdate->content ?: 'Herhangi bir not girilmemiş.'); ?>

            </div>
        </div>
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
                 elseif ($widgetType === 'swot') $toolValue = $toolsData['swot'] ?? null;
                 elseif ($widgetType === '4m_report') $toolValue = $toolsData['4m_report'][$index] ?? null;

                $widgetTitle = $widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace(['_', 'chart', 'data'], [' ', '', ''], $widgetType));
            ?>

            <div class="mb-6"> 
                
                <?php if($widgetType === 'info_text'): ?>
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                         
                         <h5 class="text-base font-semibold text-blue-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? 'Bilgilendirme'); ?></h5>
                        <div class="mt-1 text-sm text-blue-700 prose prose-sm max-w-none">
                           <?php echo nl2br(e($widgetConfigDefaults['content'] ?? '')); ?>

                        </div>
                    </div>

                
                 <?php elseif(!in_array($widgetType, ['five_whys', 'fishbone', 'pareto', 'bar_chart', 'line_chart', 'swot', 'checklist', 'before_after', 'risk_matrix', '4m_report', 'task_list', 'action_list', 'prioritization_matrix', 'image_upload'])): ?>
                     <div class="text-sm max-w-none">
                         
                         <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace('_', ' ', $widgetType))); ?></h5>

                         <?php if($widgetType === 'textbox'): ?>
                         <p class="mt-1 text-gray-800 font-medium bg-gray-50 p-3 rounded-lg border border-gray-200">
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


                
                <?php elseif($widgetType === 'action_list'): ?>
                    <?php
                        $actionItems = $toolsData['action_list'][$index]['items'] ?? [];
                    ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetTitle); ?></h5>
                        <?php if(!empty($actionItems)): ?>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $actionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center gap-3 p-3 rounded-lg border <?php echo e($item['is_completed'] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'); ?>">
                                        <?php if($item['is_completed']): ?>
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <?php else: ?>
                                            <div class="w-5 h-5 rounded border-2 border-gray-300 flex-shrink-0"></div>
                                        <?php endif; ?>
                                        <span class="<?php echo e($item['is_completed'] ? 'line-through text-gray-500' : 'text-gray-800'); ?>"><?php echo e($item['text']); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Aksiyon listesi boş.</p>
                        <?php endif; ?>
                    </div>

                
                <?php elseif($widgetType === 'task_list'): ?>
                    <?php
                        $tasks = $toolsData['task_list'][$index]['tasks'] ?? [];
                    ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetTitle); ?></h5>
                        <?php if(!empty($tasks)): ?>
                            <div class="overflow-x-auto border rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Görev Tanımı</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sorumlu</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($task['description']); ?></td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    <?php $assignedUser = \App\Models\User::find($task['assigned_user_id']); ?>
                                                    <?php echo e($assignedUser->name ?? '-'); ?>

                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Görev listesi boş.</p>
                        <?php endif; ?>
                    </div>

                
                <?php elseif($widgetType === 'prioritization_matrix'): ?>
                    <?php
                        $matrixItems = $toolsData['prioritization_matrix'][$index]['items'] ?? [];
                    ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetTitle); ?></h5>
                        <?php if(!empty($matrixItems)): ?>
                            <div class="overflow-x-auto border rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aksiyon</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Efor</th>
                                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Etki</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = $matrixItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900"><?php echo e($item['action']); ?></td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                        <?php echo e($item['effort'] === 'yüksek' ? 'bg-red-100 text-red-700' : ($item['effort'] === 'orta' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')); ?>">
                                                        <?php echo e(Str::ucfirst($item['effort'])); ?>

                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                        <?php echo e($item['impact'] === 'düşük' ? 'bg-red-100 text-red-700' : ($item['impact'] === 'orta' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')); ?>">
                                                        <?php echo e(Str::ucfirst($item['impact'])); ?>

                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Önceliklendirme matrisi boş.</p>
                        <?php endif; ?>
                    </div>

                
                <?php elseif($widgetType === 'image_upload'): ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetTitle); ?></h5>
                        <?php if(!empty($widgetValue['files']) && is_array($widgetValue['files'])): ?>
                            <div class="mt-1 flex flex-wrap gap-3">
                                <?php $__currentLoopData = $widgetValue['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filePath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(asset('storage/' . $filePath)); ?>" data-fancybox="gallery-<?php echo e($step->id); ?>-<?php echo e($index); ?>" data-caption="<?php echo e(basename($filePath)); ?>" class="block">
                                        <img src="<?php echo e(asset('storage/' . $filePath)); ?>" alt="<?php echo e(basename($filePath)); ?>" class="h-28 w-28 object-cover rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Resim yüklenmemiş.</p>
                        <?php endif; ?>
                    </div>

                
                <?php elseif($widgetType === 'swot' && !empty($toolValue) && count(array_filter($toolValue)) > 0): ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-3"><?php echo e($widgetTitle); ?></h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <?php if(!empty($toolValue['strengths'])): ?>
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-lg bg-green-500 text-white flex items-center justify-center text-xs font-black">S</span><span class="text-sm font-bold text-green-700">Güçlü Yönler</span></div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($toolValue['strengths']); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($toolValue['weaknesses'])): ?>
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-lg bg-red-500 text-white flex items-center justify-center text-xs font-black">W</span><span class="text-sm font-bold text-red-700">Zayıf Yönler</span></div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($toolValue['weaknesses']); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($toolValue['opportunities'])): ?>
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-lg bg-blue-500 text-white flex items-center justify-center text-xs font-black">O</span><span class="text-sm font-bold text-blue-700">Fırsatlar</span></div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($toolValue['opportunities']); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($toolValue['threats'])): ?>
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center text-xs font-black">T</span><span class="text-sm font-bold text-amber-700">Tehditler</span></div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($toolValue['threats']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                
                <?php elseif($widgetType === 'checklist'): ?>
                    <?php
                        $items = !empty($widgetConfigDefaults['items']) ? array_values(array_filter(array_map('trim', explode("\n", $widgetConfigDefaults['items'])))) : [];
                        $checkedItems = $widgetValue['checklist'] ?? [];
                        $checkedCount = count(array_filter($checkedItems));
                        $totalItems = count($items);
                    ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? 'Kontrol Listesi'); ?></h5>
                        <?php if($totalItems > 0): ?>
                            <div class="mb-2 text-xs font-medium text-gray-500"><?php echo e($checkedCount); ?>/<?php echo e($totalItems); ?> tamamlandı</div>
                            <div class="space-y-1">
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemIndex => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $isChecked = !empty($checkedItems[$itemIndex]); ?>
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg <?php echo e($isChecked ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200'); ?>">
                                        <?php if($isChecked): ?>
                                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <?php else: ?>
                                            <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <?php endif; ?>
                                        <span class="<?php echo e($isChecked ? 'line-through text-gray-400' : 'text-gray-700'); ?>"><?php echo e($item); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Kontrol listesi maddeleri tanımlanmamış.</p>
                        <?php endif; ?>
                    </div>

                
                <?php elseif($widgetType === 'before_after'): ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-3"><?php echo e($widgetConfigDefaults['title'] ?? 'Önce/Sonra Karşılaştırma'); ?></h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">ÖNCE</span>
                                    </div>
                                    <?php if(!empty($widgetValue['before_image_path'])): ?>
                                        <a href="<?php echo e(asset('storage/' . $widgetValue['before_image_path'])); ?>" data-fancybox="gallery-<?php echo e($step->id); ?>-<?php echo e($index); ?>" data-caption="ÖNCESİ">
                                            <img src="<?php echo e(asset('storage/' . $widgetValue['before_image_path'])); ?>" alt="Önce" class="w-full h-48 object-cover rounded-lg border border-red-300 shadow-sm">
                                        </a>
                                    <?php endif; ?>
                                    <?php if(!empty($widgetValue['before_text'])): ?>
                                        <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($widgetValue['before_text']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 bg-green-500 text-white text-xs font-bold rounded-full">SONRA</span>
                                    </div>
                                    <?php if(!empty($widgetValue['after_image_path'])): ?>
                                        <a href="<?php echo e(asset('storage/' . $widgetValue['after_image_path'])); ?>" data-fancybox="gallery-<?php echo e($step->id); ?>-<?php echo e($index); ?>" data-caption="SONRASI">
                                            <img src="<?php echo e(asset('storage/' . $widgetValue['after_image_path'])); ?>" alt="Sonra" class="w-full h-48 object-cover rounded-lg border border-green-300 shadow-sm">
                                        </a>
                                    <?php endif; ?>
                                    <?php if(!empty($widgetValue['after_text'])): ?>
                                        <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($widgetValue['after_text']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                
                <?php elseif($widgetType === 'risk_matrix'): ?>
                    <?php
                        $matrixSize = intval($widgetConfigDefaults['size'] ?? 5);
                        if (!in_array($matrixSize, [3, 5])) $matrixSize = 5;
                        $selRow = intval($widgetValue['risk_row'] ?? 0);
                        $selCol = intval($widgetValue['risk_col'] ?? 0);
                        $riskScore = $selRow * $selCol;
                        $maxScore = $matrixSize * $matrixSize;
                    ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-3"><?php echo e($widgetConfigDefaults['title'] ?? 'Risk Matrisi'); ?></h5>
                        <?php if($selRow > 0 && $selCol > 0): ?>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-sm font-bold text-gray-700">Seçilen Risk:</span>
                                <?php
                                    $pct = $riskScore / $maxScore;
                                    if ($pct >= 0.6) $badgeColor = 'bg-red-400 text-white';
                                    elseif ($pct >= 0.35) $badgeColor = 'bg-amber-300 text-amber-900';
                                    elseif ($pct >= 0.15) $badgeColor = 'bg-yellow-200 text-yellow-800';
                                    else $badgeColor = 'bg-green-200 text-green-800';
                                ?>
                                <span class="px-3 py-1 rounded-full text-sm font-bold <?php echo e($badgeColor); ?>">Olasılık: <?php echo e($selRow); ?> × Etki: <?php echo e($selCol); ?> = <?php echo e($riskScore); ?></span>
                            </div>
                            <?php if(!empty($widgetValue['risk_notes'])): ?>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <span class="font-bold text-gray-600">Notlar:</span>
                                    <p class="text-gray-700 mt-1 whitespace-pre-wrap"><?php echo e($widgetValue['risk_notes']); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Risk değerlendirmesi yapılmamış.</p>
                        <?php endif; ?>
                    </div>

                
                <?php elseif($widgetType === '4m_report' && !empty($toolValue) && count(array_filter($toolValue)) > 0): ?>
                    <div class="text-sm max-w-none">
                        <h5 class="text-base font-semibold text-gray-800 mb-3"><?php echo e($widgetTitle); ?></h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="text-xs font-bold text-indigo-700 uppercase">İnsan (Man)</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($toolValue['man'] ?? ''); ?></p>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="text-xs font-bold text-indigo-700 uppercase">Makine (Machine)</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($toolValue['machine'] ?? ''); ?></p>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="text-xs font-bold text-indigo-700 uppercase">Malzeme (Material)</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($toolValue['material'] ?? ''); ?></p>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span class="text-xs font-bold text-indigo-700 uppercase">Metot (Method)</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap"><?php echo e($toolValue['method'] ?? ''); ?></p>
                            </div>
                        </div>
                    </div>
                
                 <?php elseif(in_array($widgetType, ['five_whys', 'fishbone', 'pareto', 'bar_chart', 'line_chart', 'swot'])): ?>
                      <div class="text-sm max-w-none">
                         
                         <h5 class="text-base font-semibold text-gray-800 mb-2"><?php echo e($widgetConfigDefaults['title'] ?? Str::ucfirst(str_replace(['_', 'chart', 'data'], [' ', '', ''], $widgetType))); ?></h5>
                         <p class="mt-1 text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">Bu araç için veri girilmemiş.</p>
                     </div>
                <?php endif; ?>
                
            </div> 
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
    <?php endif; ?> 
</div>


<?php if (! $__env->hasRenderedOnce('0a8bc16f-e197-47b9-9b26-f5e75b0112b5')): $__env->markAsRenderedOnce('0a8bc16f-e197-47b9-9b26-f5e75b0112b5');
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

<?php /**PATH /var/www/kys_koksan/iaa/resources/views/proje-calisma-alani/partials/_step-content-completed.blade.php ENDPATH**/ ?>