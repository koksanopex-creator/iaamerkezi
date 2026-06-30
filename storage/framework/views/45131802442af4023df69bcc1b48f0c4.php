
<div x-data="paretoChartComponent('<?php echo e($this->getId()); ?>')" x-init="initChart()"> 
    <h4 class="text-lg font-semibold text-gray-800">Pareto Analizi</h4>
    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-6">
        <div>
            <div class="flex justify-between items-center mb-2">
                <h5 class="font-semibold text-gray-700">Pareto Diagramı</h5>
                
                <button type="button" wire:click="generateChartData" class="bg-indigo-600 text-white font-semibold py-1 px-3 rounded-md text-sm hover:bg-indigo-700">Grafiği Oluştur/Güncelle</button>
            </div>
            <div wire:ignore class="bg-white p-2 rounded-lg border" style="height: 300px;">
                <canvas id="paretoChart-<?php echo e($this->getId()); ?>"></canvas>
            </div>
        </div>
        <div>
            <h5 class="font-semibold text-gray-700 mb-2">Analiz Sonuçları</h5>
            <div class="overflow-x-auto bg-white rounded-lg border">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100"><tr class="text-left">
                    <th class="p-2 font-semibold text-gray-600">#</th><th class="p-2 font-semibold text-gray-600"><?php echo e($toolsData['pareto']['header1']); ?></th>
                    <th class="p-2 font-semibold text-gray-600 text-right"><?php echo e($toolsData['pareto']['header2']); ?></th><th class="p-2 font-semibold text-gray-600 text-right">Toplam <?php echo e($toolsData['pareto']['header2']); ?></th>
                    <th class="p-2 font-semibold text-gray-600 text-right">Kümülatif %</th></tr></thead>
                    <tbody class="divide-y">
                        
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $paretoProcessedData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr><td class="p-2"><?php echo e($index + 1); ?></td><td class="p-2"><?php echo e($data['problem']); ?></td><td class="p-2 text-right"><?php echo e(number_format($data['frequency'], 0)); ?></td>
                        <td class="p-2 text-right"><?php echo e(number_format($data['cumulative_sum'], 0)); ?></td><td class="p-2 text-right font-bold"><?php echo e(number_format($data['cumulative_percentage'], 2)); ?>%</td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="p-4 text-center text-gray-500">Analiz için veri girin.</td></tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <h5 class="font-semibold text-gray-700 mb-2">Veri Girişi</h5>
            <div class="grid grid-cols-2 gap-4 mb-2">
                
                <div><label class="text-xs font-medium text-gray-500">Kolon 1 Başlığı</label><input type="text" wire:model="toolsData.pareto.header1" class="mt-1 block w-full text-sm rounded-md border-gray-300"></div>
                <div><label class="text-xs font-medium text-gray-500">Kolon 2 Başlığı</label><input type="text" wire:model="toolsData.pareto.header2" class="mt-1 block w-full text-sm rounded-md border-gray-300"></div>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg border">
                <table class="min-w-full"><thead class="bg-gray-100"><tr>
                <th class="p-2 text-left text-sm font-semibold text-gray-600"><?php echo e($toolsData['pareto']['header1']); ?></th><th class="p-2 text-left text-sm font-semibold text-gray-600"><?php echo e($toolsData['pareto']['header2']); ?></th>
                <th class="w-16"></th></tr></thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $toolsData['pareto']['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b" wire:key="pareto-row-<?php echo e($index); ?>">
                         
                        <td class="p-1"><input type="text" wire:model="toolsData.pareto.rows.<?php echo e($index); ?>.problem" class="w-full text-sm border-gray-300 rounded-md"></td>
                        <td class="p-1"><input type="number" wire:model="toolsData.pareto.rows.<?php echo e($index); ?>.frequency" class="w-full text-sm border-gray-300 rounded-md"></td>
                        <td class="p-1 text-center"><button type="button" wire:click="removeParetoRow(<?php echo e($index); ?>)" class="text-red-500 hover:text-red-700 p-1 rounded-full font-bold text-lg">&times;</button></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
                </table>
            </div>
            <button type="button" wire:click="addParetoRow" class="mt-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Satır Ekle</button>
        </div>
    </div>
</div>


<script>
    window['initialParetoData_<?php echo e($this->getId()); ?>'] = <?php echo json_encode($paretoProcessedData->toArray(), 15, 512) ?>;
</script>


<?php $__env->startPush('scripts'); ?>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('paretoChartComponent', (componentId) => ({
            chartInstance: null,
            chartId: 'paretoChart-' + componentId,

            initChart() {
                this.$nextTick(() => {
                    const initialData = window['initialParetoData_' + componentId];
                    const initialChartData = this.formatDataForChart(initialData);
                    this.drawChart(initialChartData);
                });

                Livewire.on('updateParetoChart-' + componentId, ({ data }) => {
                    // DÜZELTME: updateChart yerine doğrudan drawChart'ı çağır
                    this.drawChart(data);
                });
            },

            formatDataForChart(processedData) {
                 if (!processedData || !Array.isArray(processedData)) return null;
                // Gelen veri zaten event'ten geliyorsa (labels, frequencies içeriyorsa)
                // doğrudan kullan, değilse (PHP'den geliyorsa) formatla.
                if (processedData.length > 0 && typeof processedData[0].problem !== 'undefined') {
                    // Bu PHP'den gelen $paretoProcessedData formatı
                     return {
                        labels: processedData.map(item => item.problem),
                        frequencies: processedData.map(item => item.frequency),
                        percentages: processedData.map(item => item.cumulative_percentage),
                        header1: 'Problem', 
                        header2: 'Sıklık' 
                    };
                } else if (processedData.labels && processedData.frequencies) {
                     // Bu Livewire event'inden gelen data formatı
                     return processedData;
                }
                return null; // Geçersiz veri
            },

            drawChart(chartData) {
                 // requestAnimationFrame güvenlik için kalsın
                 requestAnimationFrame(() => { 
                    const chartCanvas = document.getElementById(this.chartId);
                    // DÜZELTME: chartData null/undefined kontrolü eklendi
                    if (!chartCanvas || !chartData || !chartData.labels) { 
                        // Eğer eski grafik varsa ve yeni veri yoksa, eskiyi yok et
                         if (this.chartInstance) {
                            this.chartInstance.destroy();
                            this.chartInstance = null;
                        }
                        return; 
                    } 
                    const ctx = chartCanvas.getContext('2d');
                    if (!ctx) { return; }

                    // Önce eskiyi yok et (varsa)
                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                        this.chartInstance = null;
                    }

                    // Sonra yenisini oluştur
                    this.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartData.labels,
                            datasets: [
                                {
                                    label: chartData.header2 || 'Sıklık',
                                    data: chartData.frequencies,
                                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 1,
                                    yAxisID: 'y',
                                },
                                {
                                    label: 'Kümülatif %',
                                    data: chartData.percentages,
                                    type: 'line',
                                    borderColor: 'rgba(239, 68, 68, 1)',
                                    tension: 0.1,
                                    yAxisID: 'y1',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false }, 
                            scales: {
                                y: { type: 'linear', display: true, position: 'left', beginAtZero: true, title: { display: true, text: chartData.header2 || 'Sıklık' } },
                                y1: { type: 'linear', display: true, position: 'right', min: 0, max: 100, grid: { drawOnChartArea: false }, ticks: { callback: value => value + '%' }, title: { display: true, text: 'Kümülatif %' } }
                            }
                        }
                    });
                 });
            },

            // DÜZELTME: updateChart fonksiyonu kaldırıldı, artık direkt drawChart kullanılıyor.
            // updateChart(newData) { ... } 
        }));
    });
</script>
<?php $__env->stopPush(); ?>

<?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/project/widgets/_pareto.blade.php ENDPATH**/ ?>