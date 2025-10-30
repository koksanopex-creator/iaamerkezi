{{-- resources/views/livewire/project/widgets/_generic-chart-script.blade.php --}}
{{-- Bu dosya layout'a bir kere push edilecek --}}
@pushOnce('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Bar ve Line Chart için ortak Alpine component'i
        Alpine.data('genericChartComponent', (componentId, chartType = 'bar') => ({
            chartInstance: null,
            chartId: 'genericChart-' + componentId, // Canvas ID
            livewireEventName: (chartType === 'bar' ? 'updateBarChart-' : 'updateLineChart-') + componentId,

            // İlk veriyi global değişkenden okuyarak başlat
            initChart() {
                this.$nextTick(() => {
                    // İlk veriyi PHP'nin render ettiği global değişkenden al
                    const initialData = window['initialChartData_' + componentId];
                    if(initialData) {
                        // Eğer veri varsa, Chart.js'in beklediği formata çevirip çiz
                        this.drawChart(this.formatDataForChart(initialData, chartType));
                    } else {
                        // Eğer ilk veri yoksa (adım yeni oluşturulduysa vs.),
                        // backend'den veriyi iste (boş grafik çizilecek)
                        const widgetIndex = componentId.split('-').pop();
                        // İlk yüklemede hata almamak için varlık kontrolü
                        if (typeof @this !== 'undefined' && @this) {
                            if (chartType === 'bar' && typeof @this.call === 'function') {
                                 @this.call('generateBarChartData', widgetIndex);
                            } else if (chartType === 'line' && typeof @this.call === 'function') {
                                 @this.call('generateLineChartData', widgetIndex);
                            }
                        }
                    }
                });

                // Livewire'dan BUTONLA tetiklenen güncellemeleri dinle
                Livewire.on(this.livewireEventName, ({ data }) => {
                    this.drawChart(data); // Güncelleme = Yok et ve yeniden çiz
                });
            },

            // PHP'den gelen Collection->toArray() verisini veya Event verisini Chart.js formatına çevirir
            formatDataForChart(processedData, type) {
                if (!processedData) return null; // Null kontrolü
                // Gelen veri zaten event'ten geliyorsa (labels, values içeriyorsa)
                if (processedData.labels && processedData.values) {
                     return processedData;
                }
                // Gelen veri PHP'den geliyorsa (Collection->toArray())
                else if (Array.isArray(processedData) && processedData.length > 0 && typeof processedData[0]?.label !== 'undefined') {
                     return {
                        labels: processedData.map(item => item.label),
                        values: processedData.map(item => item.value),
                        // Eksik config bilgileri için varsayılanlar
                        axis_x: type === 'bar' ? 'Kategoriler' : 'Kategoriler',
                        axis_y: type === 'bar' ? 'Değerler' : 'Değerler',
                        title: type === 'bar' ? 'Sütun Grafiği' : 'Çizgi Grafiği'
                    };
                }
                 // Veri boş veya geçersizse null dön
                 else if (Array.isArray(processedData) && processedData.length === 0) {
                      return { labels: [], values: [], axis_x: '', axis_y: '', title: '' }; // Boş grafik için
                 }
                return null;
            },


            // Grafik çizme/yeniden çizme fonksiyonu
            drawChart(chartData) {
                requestAnimationFrame(() => { // Güvenlik için RAF kalsın
                    const chartCanvas = document.getElementById(this.chartId);
                    // chartData veya labels/values yoksa çizme
                    if (!chartCanvas || !chartData || !chartData.labels || !chartData.values) {
                        if (this.chartInstance) { // Eski grafik varsa yok et
                            this.chartInstance.destroy(); this.chartInstance = null;
                        }
                        return;
                    }
                    const ctx = chartCanvas.getContext('2d');
                    if (!ctx) { return; }

                    if (this.chartInstance) { // Eski grafik varsa yok et
                        this.chartInstance.destroy();
                        this.chartInstance = null;
                    }

                    // Grafik seçeneklerini ayarla
                    const chartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: !!chartData.title,
                                text: chartData.title || ''
                            },
                            legend: {
                                display: false // Tek dataset olduğu için gizle
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: !!chartData.axis_x,
                                    text: chartData.axis_x || ''
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: !!chartData.axis_y,
                                    text: chartData.axis_y || ''
                                }
                            }
                        }
                    };

                    // Veri setini ayarla
                    const chartDataset = {
                            label: chartData.axis_y || 'Değerler',
                            data: chartData.values,
                            backgroundColor: chartType === 'bar' ? 'rgba(75, 192, 192, 0.5)' : undefined, // Bar için renk
                            borderColor: 'rgba(75, 192, 192, 1)', // Bar border ve Line rengi
                            borderWidth: chartType === 'bar' ? 1 : 2,
                            tension: chartType === 'line' ? 0.1 : undefined,
                            fill: chartType === 'line' ? false : undefined,
                    };

                    // Yeni grafiği oluştur
                    this.chartInstance = new Chart(ctx, {
                        type: chartType, // 'bar' veya 'line'
                        data: {
                            labels: chartData.labels,
                            datasets: [chartDataset]
                        },
                        options: chartOptions
                    });
                });
            },
        }));
    });
</script>
@endpushOnce

