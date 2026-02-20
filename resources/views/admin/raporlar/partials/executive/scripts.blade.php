{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    (function () {
        function initCharts() {
            // ==============================================================
            // 1. ORTAK AYARLAR (Tüm Grafikler İçin Modern Stil)
            // ==============================================================
            let commonChartConfig = {
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false }
            };

            // Ortak Donut/Pasta Grafik Ayarları
            let commonPieOptions = {
                chart: {
                    type: 'donut',
                    height: 380,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['#ffffff']
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 600,
                        colors: ['#374151']
                    },
                    dropShadow: { enabled: false },
                    background: {
                        enabled: true,
                        foreColor: '#fff',
                        padding: 4,
                        borderRadius: 2,
                        borderWidth: 1,
                        borderColor: '#e5e7eb',
                        opacity: 0.9,
                    },
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '12px', color: '#6B7280', offsetY: -5 },
                                value: { show: true, fontSize: '24px', fontWeight: 700, color: '#111827', offsetY: 5 },
                                total: {
                                    show: true,
                                    label: 'Toplam',
                                    fontSize: '12px',
                                    color: '#6B7280',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            offset: 0,
                            minAngleToShowLabel: 10
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    markers: { radius: 12 },
                    itemMargin: { horizontal: 10, vertical: 5 }
                },
            };


            // ==============================================================
            // GRAFİK OLUŞTURMA YARDIMCISI (Eski grafiği temizler)
            // ==============================================================
            function createOrUpdateChart(selector, options) {
                const el = document.querySelector(selector);
                if (!el) return;

                // Varsa temizle
                el.innerHTML = "";

                new ApexCharts(el, options).render();
            }

            // ==============================================================
            // 2. GRAFİK OLUŞTURMA İŞLEMLERİ
            // ==============================================================

            // --- 1) TREND CHART (Bar) ---
            let trendOptions = {
                chart: { type: 'bar', height: 360, ...commonChartConfig },
                series: [{ name: 'Şikayet', data: @json($charts['trend_data']) }],
                xaxis: {
                    categories: @json($charts['labels']),
                    labels: { style: { fontSize: '11px', colors: '#6B7280' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { show: false },
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: { fontSize: '12px', colors: ['#374151'] },
                    background: { enabled: false }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '50%',
                        dataLabels: { position: 'top' }
                    }
                },
                colors: ['#6366f1'],
                grid: { show: false }
            };
            createOrUpdateChart('#trendChart', trendOptions);


            // ==============================
            // 2) KATEGORİ CHART (Donut)
            // ==============================
            let rawCatSeries = @json($charts['cat_data']);
            let rawCatLabels = @json($charts['cat_labels']);
            let catSeries = [];
            let catLabels = [];

            rawCatSeries.forEach((val, index) => {
                if (val > 0) {
                    catSeries.push(val);
                    catLabels.push(rawCatLabels[index]);
                }
            });

            let catOptions = {
                ...commonPieOptions,
                series: catSeries,
                labels: catLabels,
                chart: { type: 'donut', height: 330, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
                legend: {
                    show: true,
                    position: 'right',
                    verticalAlign: 'middle',
                    horizontalAlign: 'left',
                    fontSize: '14px',
                    fontWeight: 400,
                    width: 160,
                    markers: { radius: 12, width: 10, height: 10 },
                    itemMargin: { horizontal: 0, vertical: 6 },
                    formatter: function (seriesName, opts) {
                        const val = opts.w.globals.series[opts.seriesIndex];
                        return seriesName + " (" + val + ")";
                    }
                },
                colors: ['#3B82F6', '#EC4899', '#10B981', '#F59E0B', '#8B5CF6', '#6366F1', '#14B8A6']
            };
            createOrUpdateChart('#catChart', catOptions);


            // ==============================
            // 3) DURUM ANALİZİ
            // ==============================
            let rawStatusSeries = @json($charts['status_data']);
            let rawStatusLabels = ['Açık', 'Çözülen', 'Geciken'];
            let rawStatusColors = ['#3B82F6', '#10B981', '#EF4444'];
            let statusSeries = [];
            let statusLabels = [];
            let statusColors = [];

            rawStatusSeries.forEach((val, index) => {
                if (val > 0) {
                    statusSeries.push(val);
                    statusLabels.push(rawStatusLabels[index]);
                    statusColors.push(rawStatusColors[index]);
                }
            });

            let statusOptions = {
                ...commonPieOptions,
                series: statusSeries,
                labels: statusLabels,
                colors: statusColors,
                chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
                legend: {
                    show: true,
                    position: 'right',
                    verticalAlign: 'middle',
                    horizontalAlign: 'left',
                    fontSize: '12px',
                    fontWeight: 700,
                    width: 140,
                    markers: { radius: 12, width: 10, height: 10 },
                    itemMargin: { horizontal: 0, vertical: 6 },
                    formatter: function (seriesName, opts) {
                        const val = opts.w.globals.series[opts.seriesIndex];
                        return seriesName + " (" + val + ")";
                    }
                }
            };

            if (statusSeries.length > 0) {
                createOrUpdateChart('#statusChart', statusOptions);
            } else {
                const el = document.querySelector('#statusChart');
                if (el) el.innerHTML = '<div class="flex items-center justify-center h-full text-xs text-gray-400">Veri Yok</div>';
            }


            // --- 4) SPEED CHART ---
            let speedOptions = {
                chart: { type: 'area', height: 360, ...commonChartConfig },
                series: [{ name: 'Ort. Gün', data: @json($charts['speed_data']) }],
                xaxis: {
                    categories: @json($charts['labels']),
                    labels: { style: { fontSize: '11px', colors: '#6B7280' } },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: { show: false },
                stroke: { curve: 'smooth', width: 3 },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '12px', fontWeight: 'bold', colors: ['#6366f1'] },
                    background: { enabled: true, borderRadius: 4, padding: 4 }
                },
                colors: ['#8B5CF6'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
                grid: { show: false }
            };
            createOrUpdateChart('#speedChart', speedOptions);


            // ==============================
            // 5) BÖLÜM BAZLI GERİ BİLDİRİM
            // ==============================
            let rawLabels = @json($bolumMemnuniyeti->pluck('bolum_adi'));
            let rawOnay = @json($bolumMemnuniyeti->pluck('onay_sayisi')->map(fn($v) => (int) $v));
            let rawRed = @json($bolumMemnuniyeti->pluck('red_sayisi')->map(fn($v) => (int) $v));
            let rawRevizyon = @json($bolumMemnuniyeti->pluck('revizyon_sayisi')->map(fn($v) => (int) $v));

            function filterChartData(values, labels) {
                let filteredSeries = [];
                let filteredLabels = [];
                values.forEach((val, index) => {
                    if (val > 0) {
                        filteredSeries.push(val);
                        filteredLabels.push(labels[index]);
                    }
                });
                return { series: filteredSeries, labels: filteredLabels };
            }

            let dataOnay = filterChartData(rawOnay, rawLabels);
            let dataRed = filterChartData(rawRed, rawLabels);
            let dataRevizyon = filterChartData(rawRevizyon, rawLabels);

            let baseFeedbackOptions = {
                chart: { type: 'donut', height: 280, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
                legend: {
                    show: true, position: 'right', horizontalAlign: 'left', fontSize: '11px', fontWeight: 600, width: 130, offsetY: 20,
                    markers: { radius: 12 }, itemMargin: { horizontal: 0, vertical: 4 },
                    formatter: function (seriesName, opts) {
                        const val = opts.w.globals.series[opts.seriesIndex];
                        return seriesName + ": " + val;
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '10px', fontWeight: 800, colors: ['#111827'] },
                    dropShadow: { enabled: false }, background: { enabled: false },
                    formatter: function (val, opts) { return opts.w.config.series[opts.seriesIndex]; }
                },
                plotOptions: { pie: { donut: { size: '55%', labels: { show: true, name: { show: false }, value: { show: true, fontSize: '16px', fontWeight: 700, offsetY: 5 }, total: { show: true, showAlways: true, label: 'Toplam', fontSize: '10px', color: '#6B7280', formatter: function (w) { return w.globals.seriesTotals.reduce((a, b) => a + b, 0); } } } }, dataLabels: { offset: 0 } } },
                stroke: { show: true, width: 2, colors: ['#ffffff'] },
                colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6']
            };

            // --- A) ONAYLANANLAR ---
            if (dataOnay.series.length > 0) {
                createOrUpdateChart('#feedbackApprovedChart', { ...baseFeedbackOptions, series: dataOnay.series, labels: dataOnay.labels });
            } else {
                const el = document.querySelector('#feedbackApprovedChart');
                if (el) el.innerHTML = '<div class="flex items-center justify-center h-full text-xs text-gray-400">Veri Yok</div>';
            }

            // --- B) REDDEDİLENLER ---
            if (dataRed.series.length > 0) {
                createOrUpdateChart('#feedbackRejectedChart', { ...baseFeedbackOptions, series: dataRed.series, labels: dataRed.labels });
            } else {
                const el = document.querySelector('#feedbackRejectedChart');
                if (el) el.innerHTML = '<div class="flex items-center justify-center h-full text-xs text-gray-400">Veri Yok</div>';
            }

            // --- C) REVİZYON ---
            if (dataRevizyon.series.length > 0) {
                createOrUpdateChart('#feedbackRevisionChart', { ...baseFeedbackOptions, series: dataRevizyon.series, labels: dataRevizyon.labels });
            } else {
                const el = document.querySelector('#feedbackRevisionChart');
                if (el) el.innerHTML = '<div class="flex items-center justify-center h-full text-xs text-gray-400">Veri Yok</div>';
            }
        }

        // Başlat
        initCharts();

        // Livewire update sonrası tekrar başlat
        document.addEventListener('livewire:navigated', initCharts);
        document.addEventListener('livewire:initialized', initCharts);
    })();
</script>