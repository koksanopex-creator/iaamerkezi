<?php if (! $__env->hasRenderedOnce('25f20cc4-9e0f-48cb-b370-f6496117a6f1')): $__env->markAsRenderedOnce('25f20cc4-9e0f-48cb-b370-f6496117a6f1'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('genericChartComponent', (componentId, chartType = 'bar') => ({
    chartInstance: null,
    chartId: 'genericChart-' + componentId,
    livewireEventName: (chartType === 'bar' ? 'updateBarChart-' : 'updateLineChart-') + componentId,

    initChart() {
      this.$nextTick(() => {
        const initialData = window['initialChartData_' + componentId];

        if (initialData) {
          this.drawChart(this.formatDataForChart(initialData, chartType));
        } else {
          // Livewire instance'i güvenli şekilde bul
          const bits = String(componentId).split('-');   // "<livewireId>-<index>"
          const widgetIndex = bits[bits.length - 1];
          const rootId = bits.slice(0, -1).join('-');
          const lw = window.Livewire?.find?.(rootId);

          if (lw?.$wire) {
            if (chartType === 'bar') lw.$wire.generateBarChartData(widgetIndex);
            else lw.$wire.generateLineChartData(widgetIndex);
          }
        }
      });

      // Güncellemeleri dinle
      window.Livewire?.on(this.livewireEventName, ({ data }) => {
        this.drawChart(data);
      });

      // Sayfadan ayrılırken / Livewire navigasyonunda emin ol: grafiği kapat
      window.addEventListener('beforeunload', () => this.destroyChart());
      document.addEventListener('livewire:navigating', () => this.destroyChart());
    },

    destroyChart() {
      try {
        if (this.chartInstance) {
          this.chartInstance.destroy();
          this.chartInstance = null;
        }
      } catch (e) {}
    },

    formatDataForChart(processedData, type) {
      if (!processedData) return null;

      if (processedData.labels && processedData.values) {
        return processedData;
      } else if (Array.isArray(processedData) && processedData.length > 0 && typeof processedData[0]?.label !== 'undefined') {
        return {
          labels: processedData.map(i => i.label),
          values: processedData.map(i => i.value),
          axis_x: 'Kategoriler',
          axis_y: 'Değerler',
          title: type === 'bar' ? 'Sütun Grafiği' : 'Çizgi Grafiği'
        };
      } else if (Array.isArray(processedData) && processedData.length === 0) {
        return { labels: [], values: [], axis_x: '', axis_y: '', title: '' };
      }
      return null;
    },

    drawChart(chartData) {
      requestAnimationFrame(() => {
        const chartCanvas = document.getElementById(this.chartId);

        // Canvas yoksa/dışıysa ya da veri yoksa: varsa grafiği kapat ve çık
        if (!chartCanvas || !document.body.contains(chartCanvas) || !chartData || !chartData.labels || !chartData.values) {
          this.destroyChart();
          return;
        }

        const ctx = chartCanvas.getContext('2d');
        if (!ctx) { this.destroyChart(); return; }

        // Eski grafik varsa kapat
        this.destroyChart();

        const chartOptions = {
          responsive: true,
          maintainAspectRatio: false,
          animation: false,               // <— animasyonu kapattık
          plugins: {
            title: { display: !!chartData.title, text: chartData.title || '' },
            legend: { display: false }
          },
          scales: {
            x: { title: { display: !!chartData.axis_x, text: chartData.axis_x || '' } },
            y: { beginAtZero: true, title: { display: !!chartData.axis_y, text: chartData.axis_y || '' } }
          }
        };

        const chartDataset = {
          label: chartData.axis_y || 'Değerler',
          data: chartData.values,
          backgroundColor: chartType === 'bar' ? 'rgba(75, 192, 192, 0.5)' : undefined,
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: chartType === 'bar' ? 1 : 2,
          tension: chartType === 'line' ? 0.1 : undefined,
          fill: chartType === 'line' ? false : undefined,
        };

        this.chartInstance = new Chart(ctx, {
          type: chartType,
          data: { labels: chartData.labels, datasets: [chartDataset] },
          options: chartOptions
        });
      });
    },
  }));
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/livewire/project/widgets/_generic-chart-script.blade.php ENDPATH**/ ?>