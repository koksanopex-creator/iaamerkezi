<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Müşteri Şikayetleri Raporları (Canlı)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Livewire bileşenini çağır (TÜM HTML'yi bu dosya çizecek) --}}
            @livewire('musteri-sikayet-raporu', [
                'feedbackCounts' => $feedbackCounts, 
                'bolumMemnuniyeti' => $bolumMemnuniyeti
            ])
            
           
        </div>
    </div>

    @push('scripts')
    {{-- ApexCharts Kütüphanesini Yükle --}}
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

        // === YENİ EKLENEN GRAFİKLERİN TANIMLAMALARI ===
        
        // 1. Bölüm - Kategori (Yığılmış Sütun)
        var bolumKategoriChart = new ApexCharts(document.querySelector("#bolumKategoriChart"), {
            series: [], // Livewire'dan gelecek
            chart: { type: 'bar', height: 350, stacked: true, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: false, borderRadius: 4 } },
            xaxis: { categories: [] }, // Livewire'dan gelecek
            legend: { position: 'bottom' },
            fill: { opacity: 1 },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: (val) => val + " Şikayet" } }
        });
        bolumKategoriChart.render();

         // 2. Alt Kategori (Treemap) - GÜNCELLENDİ: Font Boyutu Artırıldı
        var altKategoriChart = new ApexCharts(document.querySelector("#altKategoriChart"), {
            series: [],
            chart: { height: 350, type: 'treemap', toolbar: { show: false } },
            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
            plotOptions: { treemap: { distributed: true, enableShades: true } },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '14px',     // Yazı boyutu artırıldı (Eskisi: 12px)
                    fontWeight: 'bold',   // Yazı kalınlaştırıldı
                    colors: ['#ffffff']   // Yazı rengi beyaz
                },
                formatter: function(text, op) {
                    return [text, op.value]; // Alt satıra değeri yazar
                },
                dropShadow: {             // Okunabilirlik için gölge eklendi
                    enabled: true,
                    top: 1,
                    left: 1,
                    blur: 1,
                    color: '#000',
                    opacity: 0.45
                }
            }
        });
        altKategoriChart.render();
        // ==============================================

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

            // --- YENİ GRAFİKLERİN GÜNCELLENMESİ ---
            if (data.bolumKategoriSeries && data.bolumKategoriXaxis) {
                bolumKategoriChart.updateOptions({
                    series: data.bolumKategoriSeries,
                    xaxis: { categories: data.bolumKategoriXaxis }
                });
            }

            if (data.altKategoriSeries) {
                altKategoriChart.updateSeries(data.altKategoriSeries);
            }

        });
    </script>
    @endpush
</x-app-layout>