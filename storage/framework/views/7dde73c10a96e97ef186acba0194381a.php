<div>
    <div class="space-y-4">
        
        <div class="flex justify-between items-center mb-2">
            <h5 class="text-sm font-semibold text-gray-700">Grafik Analizi Aracı</h5>
            <div x-data="{ saving: false }" 
                 x-on:tool-saved.window="saving = true; setTimeout(() => saving = false, 2000)" 
                 class="text-xs text-gray-500 flex items-center h-4">
                <span x-show="saving" x-transition.opacity class="text-green-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Kaydedildi
                </span>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Grafik Başlığı</label>
                        <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                            <input type="text" wire:model.live.debounce.1000ms="chartTitle" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Örn: Yıllık Satışlar">
                        <?php else: ?>
                            <div class="text-sm font-medium"><?php echo e($chartTitle ?: '-'); ?></div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Grafik Türü</label>
                            <select wire:model.live="chartType" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="bar">Sütun Grafiği (Bar)</option>
                                <option value="line">Çizgi Grafiği (Line)</option>
                                <option value="pie">Pasta Grafiği (Pie)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Birim (Y-Eksen)</label>
                            <input type="text" wire:model.live.debounce.1000ms="unit" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Adet, %, TL">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Alt Eksen (X)</label>
                            <input type="text" wire:model.live.debounce.1000ms="xAxisTitle" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Örn: Yıllar">
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase">Veri Seti Tablosu</label>
                        <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                            <div class="flex gap-2">
                                <button wire:click="addSeries" class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded border border-indigo-100 hover:bg-indigo-100 font-medium transition-colors">
                                    + Seri Sütunu Ekle
                                </button>
                                <button wire:click="addDataRow" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200 font-medium transition-colors">
                                    + Satır Ekle
                                </button>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    
                    <div class="overflow-x-auto max-h-80 overflow-y-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-left text-sm whitespace-nowrap min-w-max">
                            <thead class="bg-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="p-2 border-b border-r border-gray-200 font-semibold text-gray-600 min-w-[120px]">Kategoriler</th>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $series; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sIndex => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="p-2 border-b border-r border-gray-200 font-semibold text-gray-600 min-w-[100px]">
                                            <div class="flex items-center justify-between gap-2">
                                                <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                                                    <input type="text" wire:model.live.debounce.1000ms="series.<?php echo e($sIndex); ?>.name" class="w-full text-xs rounded border-gray-300 px-2 py-1 shadow-sm font-bold" placeholder="Seri Adı">
                                                    <!--[if BLOCK]><![endif]--><?php if(count($series) > 1): ?>
                                                        <button wire:click="removeSeries(<?php echo e($sIndex); ?>)" class="text-red-400 hover:text-red-600 p-1" title="Seriyi Sil">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <?php else: ?>
                                                    <span><?php echo e($s['name'] ?: '-'); ?></span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($canManage && count($labels) > 1): ?>
                                        <th class="w-8 p-2 border-b border-gray-200 bg-gray-50"></th>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rIndex => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="p-2 border-r border-gray-200 bg-gray-50">
                                            <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                                                <input type="text" wire:model.live.debounce.1000ms="labels.<?php echo e($rIndex); ?>" class="w-full text-xs rounded border-gray-300 shadow-sm" placeholder="Kategori">
                                            <?php else: ?>
                                                <div class="text-xs font-medium truncate" title="<?php echo e($label); ?>"><?php echo e($label ?: '-'); ?></div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $series; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sIndex => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="p-2 border-r border-gray-200">
                                                <!--[if BLOCK]><![endif]--><?php if($canManage): ?>
                                                    <input type="number" wire:model.live.debounce.1000ms="series.<?php echo e($sIndex); ?>.data.<?php echo e($rIndex); ?>" class="w-full text-xs rounded border-gray-300 shadow-sm text-right" placeholder="0">
                                                <?php else: ?>
                                                    <div class="text-xs text-right"><?php echo e($s['data'][$rIndex] ?? 0); ?></div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($canManage && count($labels) > 1): ?>
                                            <td class="p-2 text-center bg-gray-50">
                                                <button wire:click="removeDataRow(<?php echo e($rIndex); ?>)" class="text-red-500 hover:text-red-700 p-1" title="Satırı Sil">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </td>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="bg-white p-4 rounded-lg border border-gray-200 flex flex-col items-center justify-center min-h-[300px]">
                <h6 class="text-sm font-bold text-gray-800 mb-4 text-center"><?php echo e($chartTitle ?: 'Grafik Analizi'); ?></h6>
                
                <div id="chart-container-<?php echo e($tool->id); ?>" class="w-full h-full min-h-[300px]" wire:ignore
                     x-data="{
                        chartInstance: null,
                        async init() {
                            if (typeof ApexCharts === 'undefined') {
                                await this.loadScript('https://cdn.jsdelivr.net/npm/apexcharts');
                            }
                            this.renderChart('<?php echo e($chartType); ?>', <?php echo e(json_encode($labels)); ?>, <?php echo e(json_encode($series)); ?>, '<?php echo e($unit); ?>', '<?php echo e($xAxisTitle); ?>');
                        },
                        loadScript(src) {
                            return new Promise((resolve, reject) => {
                                const script = document.createElement('script');
                                script.src = src;
                                script.onload = resolve;
                                script.onerror = reject;
                                document.head.appendChild(script);
                            });
                        },
                        renderChart(type, labels, series, unit, xAxisTitle) {
                            if (this.chartInstance) {
                                this.chartInstance.destroy();
                            }
                            
                            const stringLabels = labels.map(l => String(l) || '');
                            
                            // Pie charts only support 1-dimensional array of numbers
                            let processedSeries = [];
                            if (type === 'pie') {
                                processedSeries = series.length > 0 ? series[0].data.map(v => Number(v) || 0) : [];
                            } else {
                                processedSeries = series.map(s => ({
                                    name: s.name || 'İsimsiz',
                                    data: s.data.map(v => Number(v) || 0)
                                }));
                            }

                            // Define a broader color palette for multi-series
                            const colors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#f97316', '#14b8a6', '#6366f1'];

                            const options = {
                                series: processedSeries,
                                chart: { type: type, height: 300, toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
                                colors: type === 'pie' ? colors : colors.slice(0, series.length),
                                dataLabels: { enabled: type === 'pie' },
                                labels: stringLabels,
                                stroke: type === 'line' ? { curve: 'smooth', width: 3 } : { show: true, width: 2, colors: ['transparent'] },
                                tooltip: {
                                    y: {
                                        formatter: function (val) {
                                            return unit ? val + ' ' + unit : val;
                                        }
                                    }
                                }
                            };

                            if (type === 'bar') {
                                options.plotOptions = {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        endingShape: 'rounded'
                                    },
                                };
                            }

                            if (unit && type !== 'pie') {
                                options.yaxis = {
                                    title: {
                                        text: unit,
                                        style: { fontWeight: 600, color: '#6B7280' }
                                    }
                                };
                            }

                            if (xAxisTitle && type !== 'pie') {
                                options.xaxis = options.xaxis || {};
                                options.xaxis.title = {
                                    text: xAxisTitle,
                                    style: { fontWeight: 600, color: '#6B7280' }
                                };
                            }

                            this.chartInstance = new ApexCharts(this.$el, options);
                            this.chartInstance.render();
                        }
                     }"
                     @update-chart-<?php echo e($tool->id); ?>.window="renderChart($event.detail[0].type, $event.detail[0].labels, $event.detail[0].series, $event.detail[0].unit, $event.detail[0].xAxisTitle)"
                ></div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/tools/chart-analysis.blade.php ENDPATH**/ ?>