<script>
    document.addEventListener('DOMContentLoaded', function () {

        // 1. İAA ÇÖZME PERFORMANSI
        if (document.querySelector("#performanceChart")) {
            var options = {
                series: [{
                    name: 'Tamamlanan Proje',
                    data: @json(array_values($aylikPerformans ?? []))
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
                    categories: @json(array_keys($aylikPerformans ?? [])),
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
        }

        // 2. MÜŞTERİ ŞİKAYETİ ÇÖZME PERFORMANSI
        if (document.querySelector("#complaintChart")) {
            var options2 = {
                series: [{
                    name: 'Çözülen Şikayet',
                    data: @json(array_values($sikayetPerformans ?? []))
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    fontFamily: 'Figtree, sans-serif',
                    toolbar: { show: false }
                },
                colors: ['#10b981'], // Emerald-500 (Çözüm = Yeşil)
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth' },
                xaxis: {
                    categories: @json(array_keys($sikayetPerformans ?? [])),
                    tooltip: { enabled: false }
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true
                },
                tooltip: { x: { format: 'dd/MM/yy' } },
            };

            var chart2 = new ApexCharts(document.querySelector("#complaintChart"), options2);
            chart2.render();
        }
    });
</script>