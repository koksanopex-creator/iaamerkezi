<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gelişmiş İAA Raporlama & Analiz') }}
        </h2>
    </x-slot>

    @push('styles')
    <style>
        .chart-card { background: #fff; border-radius: 1rem; padding: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); height: 100%; border:1px solid #e5e7eb; min-height: 350px; }
        @media print { .no-print { display: none !important; } }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('admin.raporlar-tablosu')
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush
</x-app-layout>