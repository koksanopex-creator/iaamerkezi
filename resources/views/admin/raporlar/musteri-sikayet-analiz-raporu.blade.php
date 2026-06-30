@push('pageTitle')
    Müşteri Şikayet Analiz Raporu | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Müşteri Şikayet Analiz Raporu') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            @livewire('admin.musteri-sikayet-analiz-raporu')
        </div>
    </div>

    @push('scripts')
    {{-- ApexCharts Kütüphanesini Yükle --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush
</x-app-layout>
