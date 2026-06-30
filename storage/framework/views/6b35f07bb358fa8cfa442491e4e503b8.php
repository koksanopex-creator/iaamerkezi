<?php $__env->startPush('pageTitle'); ?> İAA (Öneri Sistemi) Raporları | <?php $__env->stopPush(); ?>

<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            İAA (Öneri Sistemi) Raporları
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $__currentLoopData = $kazancRaporu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kazanc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-sm font-medium text-gray-500 truncate">
                            Toplam Kazanç / Tasarruf (<?php echo e($kazanc->kazanc_birim); ?>)
                        </div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            <?php echo e(number_format($kazanc->toplam_kazanc, 0, ',', '.')); ?> <?php echo e($kazanc->kazanc_birim); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($kazancRaporu->isEmpty()): ?>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-300 col-span-3">
                        <div class="text-sm font-medium text-gray-500">Henüz hesaplanmış bir kazanç/tasarruf verisi
                            bulunmuyor.</div>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Proje Durum Dağılımı</h3>
                    <div id="durumChart"></div>
                </div>

                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bölüm Bazlı Proje Sayıları (Top 10)</h3>
                    <div id="bolumChart"></div>
                </div>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Son Eklenen Projeler</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tarih</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bölüm</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Gönderen</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Başlık</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Durum</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $sonProjeler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proje): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($proje->created_at->format('d.m.Y')); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($proje->bolum->ad ?? '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($proje->gonderen->name ?? 'Misafir'); ?>

                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo e(Str::limit($proje->baslik, 40)); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo $proje->durum_etiketi; ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo e(route('proje.workspace.show', $proje->id)); ?>"
                                            class="text-indigo-600 hover:text-indigo-900">İncele</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200 bg-gray-50 text-right">
                    <a href="<?php echo e(route('iaa.index')); ?>"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500">üm Projeleri Görüntüle
                        &rarr;</a>
                </div>
            </div>

        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // === DURUM GRAFİĞİ ===
            var durumData = <?php echo json_encode($durumDagilimi, 15, 512) ?>;
            var durumLabels = durumData.map(d => d.durum);
            var durumSeries = durumData.map(d => parseInt(d.total));

            var durumChart = new ApexCharts(document.querySelector("#durumChart"), {
                series: durumSeries,
                chart: { type: 'donut', height: 350 },
                labels: durumLabels,
                colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#6B7280'],
                legend: { position: 'bottom' },
                responsive: [{
                    breakpoint: 480,
                    options: { chart: { width: 300 }, legend: { position: 'bottom' } }
                }]
            });
            durumChart.render();

            // === BÖLÜM GRAFİĞİ ===
            var bolumData = <?php echo json_encode($bolumPerformansi, 15, 512) ?>;
            var bolumLabels = bolumData.map(d => d.bolum_adi);
            var bolumSeries = bolumData.map(d => parseInt(d.toplam));

            var bolumChart = new ApexCharts(document.querySelector("#bolumChart"), {
                series: [{ name: 'Proje Sayısı', data: bolumSeries }],
                chart: { type: 'bar', height: 350 },
                plotOptions: {
                    bar: { borderRadius: 4, horizontal: true, }
                },
                dataLabels: { enabled: true },
                xaxis: { categories: bolumLabels },
                tooltip: { y: { formatter: function (val) { return val + " Proje" } } }
            });
            bolumChart.render();
        </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/kys_koksan/iaa/resources/views/admin/raporlar/iaa-raporlari.blade.php ENDPATH**/ ?>