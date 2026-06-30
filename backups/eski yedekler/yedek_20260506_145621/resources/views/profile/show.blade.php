@php
    // Yetki kontrolü: Şikayet sekmesini kimler görebilir?
    $sikayetGormeYetkisi = $user->hasRole([
        'Superadmin',
        'Müşteri Şikayeti Kurulu',
        'Müşteri Şikayeti Çözüm Lideri',
        'Bölüm Kalite Yöneticisi'
    ]);
@endphp

@push('pageTitle')
    {{ $user->name }} | 
@endpush

<x-app-layout>
    {{-- Grafik Kütüphanesi --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- 1. ÜST BANNER VE PROFİL KARTI --}}
    @include('profile.partials.show.header')

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-12">

        {{-- 2. ANA İSTATİSTİKLER --}}
        @include('profile.partials.show.stats')

        {{-- 3. SEKME YAPISI (Tab Container) --}}
        <div x-data="{ activeTab: '{{ session('active_tab', request('tab', (isset($isCustomerRep) && $isCustomerRep ? 'sikayetler' : 'performans'))) }}' }"
            class="bg-white rounded-2xl shadow-xl overflow-hidden min-h-[600px]">

            {{-- Sekme Butonları --}}
            @include('profile.partials.show.tabs-nav', ['activeTasksCount' => $activeTasks->count()])

            {{-- Sekme İçerikleri --}}
            <div class="p-6 bg-gray-50 min-h-[500px]">

                @if(!isset($isCustomerRep) || !$isCustomerRep)
                    @include('profile.partials.show.tab-performance')
                @endif

                @if($canViewActiveTasks && (!isset($isCustomerRep) || !$isCustomerRep))
                    @include('profile.partials.show.tab-aktif-gorevler')
                @endif

                @if($sikayetGormeYetkisi || (isset($isCustomerRep) && $isCustomerRep))
                    @include('profile.partials.show.tab-complaints')
                @endif

                @if(isset($isCustomerRep) && $isCustomerRep)
                    @include('profile.partials.show.tab-colleagues')
                @endif

                @include('profile.partials.show.tab-comments')

                @role('Superadmin')
                @include('profile.partials.show.tab-security')
                @endrole

                {{-- DİSİPLİN İÇERİĞİ (Müşteriler Göremez) --}}
                @if(!isset($isCustomerRep) || !$isCustomerRep)
                    @include('profile.partials.show.tab-disciplinary')
                @endif
            </div>
        </div>
    </div>

    {{-- 4. JAVASCRIPT --}}
    @include('profile.partials.show.scripts')

</x-app-layout>