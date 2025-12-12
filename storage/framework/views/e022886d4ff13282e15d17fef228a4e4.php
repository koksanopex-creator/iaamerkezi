<?php $__env->startPush('scripts'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ==============================================================
            // 1. ORTAK AYARLAR (Tüm Grafikler İçin Modern Stil)
            // ==============================================================
            const commonChartConfig = {
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false }
            };

            // Ortak Donut/Pasta Grafik Ayarları (Sizin istediğiniz stil)
            const commonPieOptions = {
                chart: {
                    type: 'donut',
                    height: 380,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                // Dilimler arasındaki beyaz boşluk (Modern görünüm için şart)
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['#ffffff']
                },
                // Veri Etiketleri (Yazıları Dışarı Alma Ayarı)
                dataLabels: {
                    enabled: true,
                    // Yazı Rengi: Koyu Gri (Okunabilirlik için)
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 600,
                        colors: ['#374151'] 
                    },
                    // O kalın gölgeyi kapatan sihirli kod:
                    dropShadow: { enabled: false }, 
                    // Yazının arkasına minik bir kutucuk koyar (Daha net okunur)
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
                        // Halkanın kalınlığı (Yüksek yüzde = İnce halka)
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
                        // Etiketleri dışarı itme ayarı
                        dataLabels: {
                            offset: 0, // 0 = Tam sınır, + değerler dışarı iter
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
            // 2. GRAFİK OLUŞTURMA İŞLEMLERİ
            // ==============================================================

            // --- 1) TREND CHART (Bar) ---
            // Bunu da sadeleştirdik
            const trendOptions = {
                chart: { type: 'bar', height: 360, ...commonChartConfig },
                series: [{ name: 'Şikayet', data: <?php echo json_encode($charts['trend_data'], 15, 512) ?> }],
                xaxis: { 
                    categories: <?php echo json_encode($charts['labels'], 15, 512) ?>, 
                    labels: { style: { fontSize: '11px', colors: '#6B7280' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { show: false }, // Y ekseni sayılarını kaldırdık (temiz görünüm)
                dataLabels: { 
                    enabled: true, 
                    offsetY: -20, // Çubuğun üstüne çıkar
                    style: { fontSize: '12px', colors: ['#374151'] },
                    background: { enabled: false }
                },
                plotOptions: { 
                    bar: { 
                        borderRadius: 6, // Köşeleri yuvarla
                        columnWidth: '50%', 
                        dataLabels: { position: 'top' } 
                    } 
                },
                colors: ['#6366f1'], // Modern Indigo rengi
                grid: { show: false } // Arkadaki çizgileri kaldır
            };
            new ApexCharts(document.querySelector('#trendChart'), trendOptions).render();


            // ==============================
            // 2) KATEGORİ CHART (Donut - REVİZE)
            // ==============================
            
            // 1. Önce ham verileri alalım
            const rawCatSeries = <?php echo json_encode($charts['cat_data'], 15, 512) ?>;
            const rawCatLabels = <?php echo json_encode($charts['cat_labels'], 15, 512) ?>;

            // 2. Değeri 0 olan kategorileri temizleyelim
            let catSeries = [];
            let catLabels = [];

            rawCatSeries.forEach((val, index) => {
                if (val > 0) { 
                    catSeries.push(val);
                    catLabels.push(rawCatLabels[index]);
                }
            });

            const catOptions = {
                ...commonPieOptions,
                
                series: catSeries,
                labels: catLabels,

                chart: {
                    type: 'donut',
                    height: 330,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },

                // --- LEJANT AYARLARI (DÜZELTİLDİ) ---
                legend: {
                    show: true,
                    position: 'right',        // Grafiğin sağında
                    verticalAlign: 'middle',  // Dikeyde tam ortala (Yukarı yapışmasın)
                    horizontalAlign: 'left',  // Yazıları sola blokla
                    
                    fontSize: '14px',
                    fontWeight: 400,          // Bölüm isimlerini KALIN yap
                    
                    width: 160,               // Yazı alanı genişliği
                    
                    markers: {
                        radius: 12,           // Yuvarlak ikonlar
                        width: 10,
                        height: 10
                    },
                    
                    itemMargin: {
                        horizontal: 0,
                        vertical: 6           // Satır aralarını biraz açtık
                    },
                    
                    // İstenilen Format: "Kategori Adı (15)"
                    formatter: function(seriesName, opts) {
                        const val = opts.w.globals.series[opts.seriesIndex];
                        return seriesName + " (" + val + ")";
                    }
                },
                // ------------------------------------

                // Modern renk paleti
                colors: ['#3B82F6', '#EC4899', '#10B981', '#F59E0B', '#8B5CF6', '#6366F1', '#14B8A6']
            };

            if (document.querySelector('#catChart')) {
                new ApexCharts(document.querySelector('#catChart'), catOptions).render();
            }


            // ==============================
            // 3) DURUM ANALİZİ (Donut - REVİZE)
            // ==============================

            // 1. Ham Veriler ve Sabitler
            const rawStatusSeries = <?php echo json_encode($charts['status_data'], 15, 512) ?>; // [Açık, Çözülen, Geciken]
            const rawStatusLabels = ['Açık', 'Çözülen', 'Geciken'];
            const rawStatusColors = ['#3B82F6', '#10B981', '#EF4444']; // Mavi, Yeşil, Kırmızı

            // 2. Filtreleme (0 Olanları ve Renklerini Temizle)
            let statusSeries = [];
            let statusLabels = [];
            let statusColors = [];

            rawStatusSeries.forEach((val, index) => {
                if (val > 0) { 
                    statusSeries.push(val);
                    statusLabels.push(rawStatusLabels[index]);
                    statusColors.push(rawStatusColors[index]); // Rengi de taşıyoruz ki karışmasın
                }
            });

            const statusOptions = {
                ...commonPieOptions, // Ortak ayarları miras al
                
                series: statusSeries,
                labels: statusLabels,
                colors: statusColors, // Filtrelenmiş doğru renkler

                chart: {
                    type: 'donut',
                    height: 320, // Diğerleriyle eşit yükseklik
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },

                // --- LEJANT AYARLARI (Kategori ile aynı yapıda) ---
                legend: {
                    show: true,
                    position: 'right',        // Sağda
                    verticalAlign: 'middle',  // Dikey ortala
                    horizontalAlign: 'left',  // Sola yasla
                    
                    fontSize: '12px',
                    fontWeight: 700,          // Kalın Font
                    
                    width: 140,               // Genişlik
                    
                    markers: {
                        radius: 12,
                        width: 10,
                        height: 10
                    },
                    
                    itemMargin: {
                        horizontal: 0,
                        vertical: 6 
                    },
                    
                    // Format: "Durum (Sayı)"
                    formatter: function(seriesName, opts) {
                        const val = opts.w.globals.series[opts.seriesIndex];
                        return seriesName + " (" + val + ")";
                    }
                }
            };

            if (document.querySelector('#statusChart')) {
                // Eğer hiç veri yoksa (hepsi 0 ise) boş mesaj göster
                if (statusSeries.length > 0) {
                    new ApexCharts(document.querySelector('#statusChart'), statusOptions).render();
                } else {
                    document.querySelector('#statusChart').innerHTML = '<div class="flex items-center justify-center h-full text-xs text-gray-400">Veri Yok</div>';
                }
            }


            // --- 4) SPEED CHART (Area/Line) ---
            // Bunu da "Line" yerine "Area" yapıp altını flu boyadık, daha şık durur.
            const speedOptions = {
                chart: { type: 'area', height: 360, ...commonChartConfig },
                series: [{ name: 'Ort. Gün', data: <?php echo json_encode($charts['speed_data'], 15, 512) ?> }],
                xaxis: { 
                    categories: <?php echo json_encode($charts['labels'], 15, 512) ?>, 
                    labels: { style: { fontSize: '11px', colors: '#6B7280' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { show: false },
                stroke: { curve: 'smooth', width: 3 },
                dataLabels: { 
                    enabled: true,
                    style: { fontSize: '12px', fontWeight: 'bold', colors: ['#6366f1'] },
                    background: { enabled: true, borderRadius: 4, padding: 4 }
                },
                colors: ['#8B5CF6'],
                fill: { 
                    type: 'gradient', 
                    gradient: { 
                        shadeIntensity: 1, 
                        opacityFrom: 0.4, 
                        opacityTo: 0.05, 
                        stops: [0, 100] 
                    } 
                },
                grid: { show: false }
            };
            new ApexCharts(document.querySelector('#speedChart'), speedOptions).render();


            // ==============================
            // 5) BÖLÜM BAZLI GERİ BİLDİRİM (AKILLI FİLTRELEME - 0 OLANLARI GİZLE)
            // ==============================

            // 1. HAM VERİLERİ ALIYORUZ
            const rawLabels   = <?php echo json_encode($bolumMemnuniyeti->pluck('bolum_adi'), 15, 512) ?>;
            const rawOnay     = <?php echo json_encode($bolumMemnuniyeti->pluck('onay_sayisi')->map(fn($v) => (int)$v), 15, 512) ?>;
            const rawRed      = <?php echo json_encode($bolumMemnuniyeti->pluck('red_sayisi')->map(fn($v) => (int)$v), 15, 512) ?>;
            const rawRevizyon = <?php echo json_encode($bolumMemnuniyeti->pluck('revizyon_sayisi')->map(fn($v) => (int)$v), 15, 512) ?>;

            // 2. YARDIMCI FONKSİYON: Sadece 0'dan büyük verileri ve isimlerini filtreler
            function filterChartData(values, labels) {
                let filteredSeries = [];
                let filteredLabels = [];
                
                values.forEach((val, index) => {
                    if (val > 0) { // SADECE 0'DAN BÜYÜKSE LİSTEYE EKLE
                        filteredSeries.push(val);
                        filteredLabels.push(labels[index]);
                    }
                });

                return { series: filteredSeries, labels: filteredLabels };
            }

            // 3. HER GRAFİK İÇİN AYRI VERİ SETİ OLUŞTURUYORUZ
            const dataOnay     = filterChartData(rawOnay, rawLabels);
            const dataRed      = filterChartData(rawRed, rawLabels);
            const dataRevizyon = filterChartData(rawRevizyon, rawLabels);

            // 4. ORTAK AYARLAR
            const baseFeedbackOptions = {
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                // Lejant (Sağ Taraf)
                legend: { 
                    show: true, 
                    position: 'right',
                    horizontalAlign: 'left',
                    fontSize: '11px',
                    fontWeight: 600,
                    width: 130,
                    offsetY: 20,
                    markers: { radius: 12 },
                    itemMargin: { horizontal: 0, vertical: 4 },
                    formatter: function(seriesName, opts) {
                        const val = opts.w.globals.series[opts.seriesIndex];
                        return seriesName + ": " + val;
                    }
                },
                // Grafik Üzerindeki Etiketler
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '10px', fontWeight: 800, colors: ['#111827'] },
                    dropShadow: { enabled: false },
                    background: { enabled: false },
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex]; 
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '55%',
                            labels: {
                                show: true,
                                name: { show: false },
                                value: { show: true, fontSize: '16px', fontWeight: 700, offsetY: 5 },
                                total: { 
                                    show: true, showAlways: true, label: 'Toplam', fontSize: '10px', color: '#6B7280',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        },
                        dataLabels: { offset: 0 } 
                    }
                },
                stroke: { show: true, width: 2, colors: ['#ffffff'] },
                colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6']
            };

            // 5. GRAFİKLERİ RENDER ET (Eğer veri varsa render et, yoksa boş kalsın)

            // --- A) ONAYLANANLAR ---
            if(document.querySelector('#feedbackApprovedChart')) {
                if (dataOnay.series.length > 0) {
                    new ApexCharts(document.querySelector('#feedbackApprovedChart'), {
                        ...baseFeedbackOptions,
                        series: dataOnay.series,
                        labels: dataOnay.labels
                    }).render();
                } else {
                    document.querySelector('#feedbackApprovedChart').innerHTML = '<div class="flex items-center justify-center h-full text-xs text-gray-400">Veri Yok</div>';
                }
            }

            // --- B) REDDEDİLENLER ---
            if(document.querySelector('#feedbackRejectedChart')) {
                if (dataRed.series.length > 0) {
                    new ApexCharts(document.querySelector('#feedbackRejectedChart'), {
                        ...baseFeedbackOptions,
                        series: dataRed.series,
                        labels: dataRed.labels
                    }).render();
                } else {
                    document.querySelector('#feedbackRejectedChart').innerHTML = '<div class="flex items-center justify-center h-full text-xs text-gray-400">Veri Yok</div>';
                }
            }

            // --- C) REVİZYON ---
            if(document.querySelector('#feedbackRevisionChart')) {
                if (dataRevizyon.series.length > 0) {
                    new ApexCharts(document.querySelector('#feedbackRevisionChart'), {
                        ...baseFeedbackOptions,
                        series: dataRevizyon.series,
                        labels: dataRevizyon.labels
                    }).render();
                } else {
                    document.querySelector('#feedbackRevisionChart').innerHTML = '<div class="flex items-center justify-center h-full text-xs text-gray-400">Veri Yok</div>';
                }
            }
        });
    </script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/admin/raporlar/partials/executive/scripts.blade.php ENDPATH**/ ?>