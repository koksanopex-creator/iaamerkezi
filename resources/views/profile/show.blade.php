@php
    // Yetki kontrolü: Şikayet sekmesini kimler görebilir?
    $sikayetGormeYetkisi = $user->hasRole([
        'Superadmin', 
        'Müşteri Şikayeti Kurulu', 
        'Müşteri Şikayeti Çözüm Lideri', 
        'Bölüm Kalite Yöneticisi'
    ]);
@endphp

<x-app-layout>
    {{-- Grafik Kütüphanesi --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- 1. ÜST BANNER VE PROFİL KARTI --}}
    @include('profile.partials.show.header')

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-12">
        
        {{-- 2. ANA İSTATİSTİKLER --}}
        @include('profile.partials.show.stats')

        {{-- 3. SEKME YAPISI (Tab Container) --}}
        <div x-data="{ activeTab: '{{ session('active_tab', request('tab', 'performans')) }}' }" class="bg-white rounded-2xl shadow-xl overflow-hidden min-h-[600px]">
            
            {{-- Sekme Butonları --}}
            @include('profile.partials.show.tabs-nav')

            {{-- Sekme İçerikleri --}}
            <div class="p-6 bg-gray-50 min-h-[500px]">
                
                @include('profile.partials.show.tab-performance')
                
                @if($sikayetGormeYetkisi)
                    @include('profile.partials.show.tab-complaints')
                @endif
                
                @include('profile.partials.show.tab-comments')
                
                @role('Superadmin')
                    @include('profile.partials.show.tab-security')
                @endrole

                {{-- DİSİPLİN İÇERİĞİ --}}
                @include('profile.partials.show.tab-disciplinary')

            </div>
        </div>
    </div>

    {{-- 4. JAVASCRIPT --}}
    @include('profile.partials.show.scripts')

</x-app-layout>