<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gelişmiş Raporlama') }}
        </h2>
    </x-slot>

    @push('styles')
    <style>
        @media print { .no-print { display: none !important; } }
        
        /* Grafik Kartları için Ana Stiller */
        .chart-card { background-color: #fff; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; overflow: hidden; }
        .chart-header { padding: 1.5rem; }
        .chart-content { padding: 1rem; }
        .grid-2-col { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; }
        @media (max-width: 1024px) { .grid-2-col { grid-template-columns: 1fr; } }

        /* ================================================= */
        /* BAŞLIKLAR İÇİN YENİ VE GELİŞTİRİLMİŞ STİL BURADA */
        /* ================================================= */
        .chart-title {
            font-size: 1.25rem;       /* text-xl */
            font-weight: 700;         /* font-bold -> DAHA KALIN */
            color: #111827;            /* text-gray-900 -> DAHA KOYU RENK */
            letter-spacing: -0.025em;  /* tracking-tight -> MODERN DOKUNUŞ */
        }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Modern Sayfa Başlığı ve Butonlar --}}
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200 mb-8 print-no-shadow">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Raporlar ve Analiz</h3>
                            <p class="mt-1 text-base text-gray-600">Sistem verilerini filtreleyin, analiz edin ve dışa aktarın.</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0 flex items-center space-x-2 no-print">
                        <button onclick="window.print()" class="p-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" title="Yazdır"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></button>
                        <a href="{{ route('admin.raporlar.exportPdf') }}" class="inline-flex items-center space-x-2 bg-red-600 text-white font-semibold py-2 px-3 rounded-lg hover:bg-red-700" title="PDF olarak indir"><span class="text-xs font-bold">PDF</span></a>
                        <a href="{{ route('admin.raporlar.exportExcel') }}" class="inline-flex items-center space-x-2 bg-green-600 text-white font-semibold py-2 px-3 rounded-lg hover:bg-green-700" title="Excel olarak indir"><span class="text-xs font-bold">XLSX</span></a>
                    </div>
                </div>
            </div>

            {{-- KPI Kartları --}}
            @if(isset($stats))
            <div class="space-y-8 mb-8 print-no-shadow">
                <div>
                    <h4 class="text-md font-bold text-gray-600 uppercase tracking-wider mb-3">Genel Durum</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 text-center">
                        <div class="bg-blue-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-blue-800">Toplam Öneri</p><p class="text-3xl font-bold text-blue-900">{{ $stats['toplam_oneri'] }}</p></div>
                        <div class="bg-yellow-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-yellow-800">Onay Bekleyen</p><p class="text-3xl font-bold text-yellow-900">{{ $stats['onay_bekleyen_oneri'] }}</p></div>
                        <div class="bg-gray-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-gray-800">Havuzda</p><p class="text-3xl font-bold text-gray-900">{{ $stats['havuzdaki_oneri'] }}</p></div>
                        <div class="bg-indigo-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-indigo-800">Atanmış Proje</p><p class="text-3xl font-bold text-indigo-900">{{ $stats['atanmis_proje'] }}</p></div>
                        <div class="bg-green-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-green-800">Tamamlanan</p><p class="text-3xl font-bold text-green-900">{{ $stats['tamamlanan_proje'] }}</p></div>
                        <div class="bg-red-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-red-800">Reddedilen</p><p class="text-3xl font-bold text-red-900">{{ $stats['reddedilen_oneri'] }}</p></div>
                    </div>
                </div>
                <div>
                    <h4 class="text-md font-bold text-gray-600 uppercase tracking-wider mb-3">Kaynak ve Sistem İstatistikleri</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="bg-teal-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-teal-800">Kayıtlı Kullanıcı Önerileri</p><p class="text-3xl font-bold text-teal-900">{{ $stats['kullanici_onerileri'] }}</p></div>
                        <div class="bg-cyan-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-cyan-800">Misafir Önerileri</p><p class="text-3xl font-bold text-cyan-900">{{ $stats['misafir_onerileri'] }}</p></div>
                        <div class="bg-purple-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-purple-800">Toplam Çözüm Takımı</p><p class="text-3xl font-bold text-purple-900">{{ $stats['toplam_takim'] }}</p></div>
                        <div class="bg-pink-100 p-4 rounded-lg shadow"><p class="text-sm font-medium text-pink-800">Toplam Sistem Kullanıcısı</p><p class="text-3xl font-bold text-pink-900">{{ $stats['toplam_kullanici'] }}</p></div>
                    </div>
                </div>
            </div>
            @endif
            
            <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-8 no-print">
                <div @click="open = !open" class="p-6 cursor-pointer flex justify-between items-center hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Grafiksel Raporlar</h3>
                            <p class="text-sm text-gray-500 mt-1">Detaylı analizleri görüntülemek için bu alana tıklayın.</p>
                        </div>
                    </div>
                    <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{'rotate-180': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>

                <div x-show="open" x-transition class="p-6 bg-gray-50/50 border-t border-gray-200 space-y-6">
                    <div class="chart-card">
                        <div class="chart-header"><h3 class="chart-title" id="trendChartTitle">Aylık Öneri Trendi</h3></div>
                        <div class="chart-content"><div id="trendChart"></div></div>
                    </div>
                    <div class="grid-2-col">
                        <div class="chart-card">
                            <div class="chart-header"><h3 class="chart-title">Öneri Başarı Oranı</h3></div>
                            <div class="chart-content"><div id="oranChart"></div></div>
                        </div>
                        <div class="chart-card">
                            <div class="chart-header"><h3 class="chart-title">En Aktif Üyeler (Takım Üyeliği)</h3></div>
                            <div class="chart-content"><div id="cokluUyelikChart"></div></div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header"><h3 class="chart-title">Puan Liderlik Tablosu (Top 5)</h3></div>
                        <div class="chart-content"><div id="puanChart"></div></div>
                    </div>
                    <div class="grid-2-col">
                        <div class="chart-card">
                            <div class="chart-header"><h3 class="chart-title">En Yüksek Puanlı 5 Proje (Havuzda)</h3></div>
                            <div class="chart-content"><div id="havuzPuanChart"></div></div>
                        </div>
                        <div class="chart-card">
                            <div class="chart-header"><h3 class="chart-title">En Yüksek Puanlı 5 Proje (Tamamlanan)</h3></div>
                            <div class="chart-content"><div id="tamamlananPuanChart"></div></div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header"><h3 class="chart-title">En Kısa Sürede Biten 5 Proje (Gün)</h3></div>
                        <div class="chart-content"><div id="hizliProjeChart"></div></div>
                    </div>
                </div>
            </div>

            {{-- LIVEWIRE BİLEŞENİ (FİLTRELER VE TABLO) --}}
            @livewire('admin.raporlar-tablosu')
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let trendChart = null;

        document.addEventListener('DOMContentLoaded', function() {
            var oranChart = new ApexCharts(document.querySelector("#oranChart"), { series: {!! json_encode($oranChartData) !!}, chart: { type: 'donut', height: 350 }, labels: ['Tamamlanan', 'Diğer'], colors: ['#10b981', '#d1d5db'], legend: { position: 'bottom' } });
            oranChart.render();
            
            var puanOptions = { series: [ { name: 'Toplam Puan', type: 'column', data: {!! json_encode($puanChartData) !!} }, { name: 'Tamamlanan Proje Sayısı', type: 'line', data: {!! json_encode($projeChartData) !!} } ], chart: { height: 400, type: 'line', stacked: false, toolbar: { show: false } }, stroke: { width: [0, 4] }, xaxis: { categories: {!! json_encode($puanChartLabels) !!} }, yaxis: [ { seriesName: 'Toplam Puan', axisTicks: { show: true }, axisBorder: { show: true, color: '#818cf8' }, labels: { style: { colors: '#818cf8' } }, title: { text: "Toplam Puan", style: { color: '#818cf8' } } }, { seriesName: 'Tamamlanan Proje Sayısı', opposite: true, min: 0, max: Math.max(...{!! json_encode($projeChartData) !!}) + 2, tickAmount: 5, axisTicks: { show: true }, axisBorder: { show: true, color: '#f87171' }, labels: { style: { colors: '#f87171' } }, title: { text: "Proje Sayısı", style: { color: '#f87171' } } } ], colors: ['#818cf8', '#f87171'], tooltip: { shared: true, intersect: false }, legend: { position: 'bottom', horizontalAlign: 'center', offsetY: 10 } };
            var puanChart = new ApexCharts(document.querySelector("#puanChart"), puanOptions);
            puanChart.render();
            
            trendChart = new ApexCharts(document.querySelector("#trendChart"), { series: [{ name: 'Öneri Sayısı', data: {!! json_encode($chartData) !!} }], chart: { height: 350, type: 'bar', toolbar: { show: false } }, plotOptions: { bar: { borderRadius: 4, horizontal: false } }, dataLabels: { enabled: true }, xaxis: { categories: {!! json_encode($chartLabels) !!} }, colors: ['#3b82f6'] });
            trendChart.render();

            var cokluUyelikChart = new ApexCharts(document.querySelector("#cokluUyelikChart"), { series: [{ name: 'Takım Sayısı', data: {!! json_encode($cokluUyelikData['series']) !!} }], chart: { type: 'bar', height: 350, toolbar: { show: false } }, plotOptions: { bar: { borderRadius: 4, horizontal: true } }, dataLabels: { enabled: true }, xaxis: { categories: {!! json_encode($cokluUyelikData['labels']) !!} } });
            cokluUyelikChart.render();
            
            var havuzPuanChart = new ApexCharts(document.querySelector("#havuzPuanChart"), { series: [{ name: 'Puan', data: {!! json_encode($havuzPuanData['series']) !!} }], chart: { type: 'bar', height: 350, toolbar: { show: false } }, plotOptions: { bar: { borderRadius: 4, horizontal: true } }, dataLabels: { enabled: true }, xaxis: { categories: {!! json_encode($havuzPuanData['labels']) !!} }, tooltip: { y: { formatter: (val) => val + " Puan" } } });
            havuzPuanChart.render();

            var tamamlananPuanChart = new ApexCharts(document.querySelector("#tamamlananPuanChart"), { series: [{ name: 'Puan', data: {!! json_encode($tamamlananPuanData['series']) !!} }], chart: { type: 'bar', height: 350, toolbar: { show: false } }, plotOptions: { bar: { borderRadius: 4, horizontal: true } }, dataLabels: { enabled: true }, xaxis: { categories: {!! json_encode($tamamlananPuanData['labels']) !!} }, colors: ['#22c55e'], tooltip: { y: { formatter: (val) => val + " Puan" } } });
            tamamlananPuanChart.render();
            
            var hizliProjeChart = new ApexCharts(document.querySelector("#hizliProjeChart"), { series: [{ name: 'Tamamlanma Süresi (Gün)', data: {!! json_encode($hizliProjeData['series']) !!} }], chart: { type: 'bar', height: 350, toolbar: { show: false } }, plotOptions: { bar: { borderRadius: 4, horizontal: true } }, dataLabels: { enabled: true, offsetX: 40, style: { fontSize: '12px', colors: ['#333'] }, formatter: (val) => val + " gün" }, xaxis: { categories: {!! json_encode($hizliProjeData['labels']) !!} }, colors: ['#f59e0b'], tooltip: { y: { formatter: (val) => val + " Günde Tamamlandı" } } });
            hizliProjeChart.render();
        });

        window.addEventListener('updateChart', event => {
            let payload = null;
            if (event.detail && Array.isArray(event.detail) && event.detail[0]) { payload = event.detail[0]; }
            else if (event.detail) { payload = event.detail; }
            if (!payload || typeof payload.labels === 'undefined') { return; }

            const { labels, data, label } = payload;
            
            document.getElementById('trendChartTitle').innerText = label;
            if (trendChart) {
                trendChart.updateOptions({
                    series: [{ name: 'Öneri Sayısı', data: data }],
                    xaxis: { categories: labels }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>