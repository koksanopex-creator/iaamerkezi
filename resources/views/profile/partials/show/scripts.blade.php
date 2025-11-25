<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'Tamamlanan Proje',
                data: @json(array_values($aylikPerformans)) 
            }],
            chart: {
                height: 350,
                type: 'area',
                fontFamily: 'Figtree, sans-serif',
                toolbar: { show: false }
            },
            colors: ['#6366f1'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth' },
            xaxis: {
                categories: @json(array_keys($aylikPerformans)), 
                tooltip: { enabled: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true
            },
            tooltip: { x: { format: 'dd/MM/yy' } },
        };

        var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
        chart.render();
    });
</script>