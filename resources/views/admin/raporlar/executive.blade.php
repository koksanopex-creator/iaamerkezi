<x-app-layout>
    
    {{-- HEADER --}}
    @include('admin.raporlar.partials.executive.header')

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-[98%] mx-auto space-y-6">

            {{-- 1. KPI ve ÖZET BLOĞU (Toggle Logic burada) --}}
            @include('admin.raporlar.partials.executive.kpi-overview')

            {{-- 2. KAYAN HABER BANDI (Yatay Slider) --}}
            @include('admin.raporlar.partials.executive.horizontal-ticker')

            {{-- 3. SON ŞİKAYETLER AKIŞI (Dikey Scroll) --}}
            @include('admin.raporlar.partials.executive.vertical-flow')

            {{-- 4. BÖLÜM PERFORMANS KARNESİ --}}
            @include('admin.raporlar.partials.executive.department-performance')

            {{-- 5. GRAFİK ANALİZLERİ (ApexCharts Slider) --}}
            @include('admin.raporlar.partials.executive.charts-slider')

        </div>
    </div>

    {{-- SCRIPTS --}}
    @include('admin.raporlar.partials.executive.scripts')

</x-app-layout>