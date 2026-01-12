<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'Tamamlanan Proje',
                data: <?php echo json_encode(array_values($aylikPerformans), 15, 512) ?> 
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
                categories: <?php echo json_encode(array_keys($aylikPerformans), 15, 512) ?>, 
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
</script><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/profile/partials/show/scripts.blade.php ENDPATH**/ ?>