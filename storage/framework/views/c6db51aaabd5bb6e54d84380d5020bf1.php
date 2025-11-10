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
            Müşteri Şikayetleri Raporları (Canlı)
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('musteri-sikayet-raporu');

$__html = app('livewire')->mount($__name, $__params, 'lw-1928213012-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // === 1. GRAFİK TANIMLAMALARI (Toplam 9 grafik) ===
        const priorityColors = ['#6B7280', '#3B82F6', '#F59E0B', '#EF4444']; // Düşük, Normal, Yüksek, Acil
        const priorityLabels = ['Düşük', 'Normal', 'Yüksek', 'Acil'];

        // === YENİ (DÜZELTME): Durum Renk Haritası ===
        // Renkleri artık sırayla değil, isme göre atayacağız
        const statusColors = {
            'Yeni': '#FACC15',             // Sarı
            'İşlemde': '#4F46E5',           // Mavi/İndigo
            'Çözümlendi': '#16A34A',       // Koyu Yeşil
            'Kapatıldı': '#10B981',        // Açık Yeşil (veya #16A34A da olabilir)
            'Yeniden Açıldı': '#EF4444',   // Kırmızı
            'Diğer': '#6B7280'             // Gri
        };
        // === DÜZELTME SONU ===


        // === Orijinal 4 Grafik ===
        var durumChart = new ApexCharts(document.querySelector("#sikayetDurumChart"), {
            series: [], 
            chart: { type: 'donut', height: 350 }, 
            labels: [],
            responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }],
            // 'colors' satırı buradan kaldırıldı. Dinamik olarak atanacak.
            legend: { position: 'bottom' }
        });
        durumChart.render();

        var kategoriChart = new ApexCharts(document.querySelector("#sikayetKategoriChart"), {
            series: [{ name: 'Şikayet Sayısı', data: [] }], chart: { type: 'bar', height: 350 },
            plotOptions: { bar: { horizontal: true } }, xaxis: { categories: [] },
            tooltip: { y: { formatter: (val) => val + " adet" } }
        });
        kategoriChart.render();

        var takimChart = new ApexCharts(document.querySelector("#sikayetTakimChart"), {
            series: [{ name: 'Şikayet Sayısı', data: [] }], chart: { type: 'bar', height: 350 },
            plotOptions: { bar: { horizontal: true } }, xaxis: { categories: [] },
            tooltip: { y: { formatter: (val) => val + " adet" } }
        });
        takimChart.render();

        var trendChart = new ApexCharts(document.querySelector("#sikayetTrendChart"), {
            series: [{ name: 'Aylık Kayıt Sayısı', data: [] }],
            chart: { type: 'line', height: 350, zoom: { enabled: false } },
            xaxis: { categories: [], type: 'datetime' }, stroke: { curve: 'smooth' },
            tooltip: { x: { format: 'MMMM yyyy' } }
        });
        trendChart.render();

        // === Yeni 5 Grafik ===
        var cozulenChart = new ApexCharts(document.querySelector("#cozulenChart"), {
            series: [], chart: { type: 'donut', height: 250 }, labels: [],
            colors: priorityColors, legend: { position: 'bottom' }
        });
        cozulenChart.render();

        var islemdeChart = new ApexCharts(document.querySelector("#islemdeChart"), {
            series: [], chart: { type: 'donut', height: 250 }, labels: [],
            colors: priorityColors, legend: { position: 'bottom' }
        });
        islemdeChart.render();

        var yeniChart = new ApexCharts(document.querySelector("#yeniChart"), {
            series: [], chart: { type: 'donut', height: 250 }, labels: [],
            colors: priorityColors, legend: { position: 'bottom' }
        });
        yeniChart.render();
        
        var projeyeDonusenChart = new ApexCharts(document.querySelector("#projeyeDonusenChart"), {
            series: [], chart: { type: 'donut', height: 250 }, labels: [],
            colors: priorityColors, legend: { position: 'bottom' }
        });
        projeyeDonusenChart.render();

        var aylikCozulenChart = new ApexCharts(document.querySelector("#aylikCozulenChart"), {
            series: [{ name: 'Aylık Çözülen Şikayet', data: [] }],
            chart: { type: 'area', height: 350, zoom: { enabled: false } },
            xaxis: { categories: [], type: 'datetime' },
            stroke: { curve: 'smooth' }, colors: ['#16A34A'],
            tooltip: { x: { format: 'MMMM yyyy' } }
        });
        aylikCozulenChart.render();


        // === 2. HELPER FONKSİYONLAR ===
        
        // Donut grafikleri için veriyi sıralı ve etiketli hale getirir
        function prepareDonutData(data) {
            const series = []; const labels = [];
            priorityLabels.forEach(label => {
                if (data[label]) { series.push(data[label]); labels.push(label); }
            });
            return { series, labels };
        }
        
        // Aylık trend verisini etiketli hale getirir
        function prepareTrendData(data) {
            const labels = Object.keys(data).map(ay => `${ay}-01T00:00:00.000Z`);
            const series = Object.values(data);
            return { labels, series };
        }

        // === 3. ANA LIVEWIRE DİNLEYİCİSİ ===
        window.addEventListener('updateSikayetRaporlari', event => {
            const data = event.detail[0]; 

            // --- Orijinal 4 Grafiği Güncelle ---
            if (data.durumData) {
                
                // === YENİ (DÜZELTME): Renkleri dinamik olarak ata ===
                const labels = Object.keys(data.durumData);
                const series = Object.values(data.durumData);
                // Gelen etiketlere göre (örn: 'Yeni') renk haritasından doğru rengi bul
                const dynamicColors = labels.map(label => statusColors[label] || statusColors['Diğer']);
                
                durumChart.updateOptions({
                    series: series,
                    labels: labels,
                    colors: dynamicColors // Renkleri sırayla değil, dinamik olarak ata
                });
                // === DÜZELTME SONU ===
            }
            if (data.kategoriData) {
                kategoriChart.updateOptions({
                    series: [{ data: Object.values(data.kategoriData) }],
                    xaxis: { categories: Object.keys(data.kategoriData) }
                });
            }
            if (data.takimData) {
                takimChart.updateOptions({
                    series: [{ data: Object.values(data.takimData) }],
                    xaxis: { categories: Object.keys(data.takimData) }
                });
            }
            if (data.aylikTrend) {
                const trendData = prepareTrendData(data.aylikTrend);
                trendChart.updateOptions({
                    series: [{ data: trendData.series }],
                    xaxis: { categories: trendData.labels }
                });
            }

            // --- Yeni 5 Grafiği Güncelle ---
            if (data.cozulenChartData) {
                const chartData = prepareDonutData(data.cozulenChartData);
                cozulenChart.updateOptions({ series: chartData.series, labels: chartData.labels });
            }
            if (data.islemdeChartData) {
                const chartData = prepareDonutData(data.islemdeChartData);
                islemdeChart.updateOptions({ series: chartData.series, labels: chartData.labels });
            }
            if (data.yeniChartData) {
                const chartData = prepareDonutData(data.yeniChartData);
                yeniChart.updateOptions({ series: chartData.series, labels: chartData.labels });
            }
            if (data.projeyeDonusenChartData) {
                const chartData = prepareDonutData(data.projeyeDonusenChartData);
                projeyeDonusenChart.updateOptions({ series: chartData.series, labels: chartData.labels });
            }
            if (data.aylikCozulenTrend) {
                const trendData = prepareTrendData(data.aylikCozulenTrend);
                aylikCozulenChart.updateOptions({
                    series: [{ data: trendData.series }],
                    xaxis: { categories: trendData.labels }
                });
            }
        });
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
<?php endif; ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/sikayet-raporlari.blade.php ENDPATH**/ ?>