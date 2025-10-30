{{-- ApexCharts Kütüphanesi ve Gerekli Stil --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
    .chart-container { background-color: #fff; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    .grid-2-col { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    .grid-full-width { margin-bottom: 20px; }
    @media (max-width: 1024px) { .grid-2-col { grid-template-columns: 1fr; } }
</style>

{{-- Grafiklerin HTML İskeleti --}}
<div class="grid-full-width">
    <div class="chart-container">
        <h3>Aylık Öneri Trendi</h3>
        <div id="trendChart"></div>
    </div>
</div>

<div class="grid-2-col">
    <div class="chart-container">
        <h3>Öneri Başarı Oranı</h3>
        <div id="oranChart"></div>
    </div>
    <div class="chart-container">
        <h3>En Aktif Üyeler (5) (Takım Üyeliği Sayısına Göre)</h3>
        <div id="cokluUyelikChart"></div>
    </div>
</div>

<div class="grid-full-width">
    <div class="chart-container">
        <h3>Puan Liderlik Tablosu (Top 5)</h3>
        <div id="puanChart"></div>
    </div>
</div>

<div class="grid-2-col">
    <div class="chart-container">
        <h3>En Yüksek Puanlı 5 Proje (Havuzda)</h3>
        <div id="havuzPuanChart"></div>
    </div>
    <div class="chart-container">
        <h3>En Yüksek Puanlı 5 Proje (Tamamlanan)</h3>
        <div id="tamamlananPuanChart"></div>
    </div>
</div>

<div class="grid-full-width">
    <div class="chart-container">
        <h3>En Kısa Sürede Biten 5 Proje (Gün)</h3>
        <div id="hizliProjeChart"></div>
    </div>
</div>


{{-- Grafiklerin JavaScript Kodları --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- MEVCUT GRAFİKLER ---
    var oranChart = new ApexCharts(document.querySelector("#oranChart"), {
        series: {!! json_encode($oranChartData) !!}, chart: { type: 'donut', height: 350 }, labels: ['Tamamlanan', 'Diğer'], colors: ['#10b981', '#d1d5db'], legend: { position: 'bottom' }
    });
    oranChart.render();

    var puanChart = new ApexCharts(document.querySelector("#puanChart"), {
        series: [{ name: 'Toplam Puan', data: {!! json_encode($puanChartData) !!} }, { name: 'Tamamlanan Proje Sayısı', data: {!! json_encode($projeChartData) !!} }],
        chart: { type: 'bar', height: 400 }, plotOptions: { bar: { horizontal: false, columnWidth: '55%' } }, xaxis: { categories: {!! json_encode($puanChartLabels) !!} }, colors: ['#818cf8', '#f87171']
    });
    puanChart.render();

    var trendChart = new ApexCharts(document.querySelector("#trendChart"), {
        series: [{ name: 'Öneri Sayısı', data: {!! json_encode($chartData) !!} }], chart: { height: 350, type: 'bar' }, xaxis: { categories: {!! json_encode($chartLabels) !!} }, colors: ['#3b82f6']
    });
    trendChart.render();

    // --- YENİ GRAFİKLER ---

    // 1. En Çok Takıma Üye Olanlar
    var cokluUyelikChart = new ApexCharts(document.querySelector("#cokluUyelikChart"), {
        series: [{ name: 'Takım Sayısı', data: {!! json_encode($cokluUyelikData['series']) !!} }], chart: { type: 'bar', height: 350 },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } }, dataLabels: { enabled: true },
        xaxis: { categories: {!! json_encode($cokluUyelikData['labels']) !!} }
    });
    cokluUyelikChart.render();

    // 2. En Yüksek Puanlı 5 Proje (Havuzda)
    var havuzPuanChart = new ApexCharts(document.querySelector("#havuzPuanChart"), {
        series: [{ name: 'Puan', data: {!! json_encode($havuzPuanData['series']) !!} }], chart: { type: 'bar', height: 350 },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } }, dataLabels: { enabled: true },
        xaxis: { categories: {!! json_encode($havuzPuanData['labels']) !!} }, tooltip: { y: { formatter: (val) => val + " Puan" } }
    });
    havuzPuanChart.render();

    // 3. En Yüksek Puanlı 5 Proje (Tamamlanan)
    var tamamlananPuanChart = new ApexCharts(document.querySelector("#tamamlananPuanChart"), {
        series: [{ name: 'Puan', data: {!! json_encode($tamamlananPuanData['series']) !!} }], chart: { type: 'bar', height: 350 },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } }, dataLabels: { enabled: true },
        xaxis: { categories: {!! json_encode($tamamlananPuanData['labels']) !!} }, colors: ['#22c55e'], tooltip: { y: { formatter: (val) => val + " Puan" } }
    });
    tamamlananPuanChart.render();
    
    // 4. En Kısa Sürede Biten 5 Proje
    var hizliProjeChart = new ApexCharts(document.querySelector("#hizliProjeChart"), {
        series: [{ name: 'Tamamlanma Süresi (Gün)', data: {!! json_encode($hizliProjeData['series']) !!} }],
        chart: { type: 'bar', height: 350 },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true, // YATAY GRAFİK
                dataLabels: {
                    position: 'top', // Veri etiketleri çubuğun üstünde
                },
            }
        },
        dataLabels: {
            enabled: true,
            offsetX: 40, // Etiketleri çubuğun dışına taşır
            style: {
                fontSize: '12px',
                colors: ['#333']
            },
            formatter: (val) => val + " gün"
        },
        xaxis: { categories: {!! json_encode($hizliProjeData['labels']) !!} },
        colors: ['#f59e0b'],
        tooltip: { y: { formatter: (val) => val + " Günde Tamamlandı" } }
    });
    hizliProjeChart.render();
});
</script>