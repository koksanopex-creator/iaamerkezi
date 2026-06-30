@push('pageTitle')
    Müşteri Şikayetleri Raporları | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Müşteri Şikayetleri Raporları (Canlı)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Livewire bileşenini çağır --}}
            @livewire('musteri-sikayet-raporu')
            
           
        </div>
    </div>

    @push('scripts')
    {{-- ApexCharts Kütüphanesini Yükle --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush
</x-app-layout>