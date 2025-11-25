<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 tracking-tight">
                    @if(Auth::user()->hasRole('Superadmin'))
                        {{ __('Yönetici Paneli') }}
                    @else
                        {{ __('Dashboard') }}
                    @endif
                </h2>
                <p class="text-gray-600 mt-1">
                    @if(Auth::user()->hasRole('Superadmin'))
                        Sistem durumunu ve verileri yönetin
                    @else
                        Sistemdeki genel durumunuzu görüntüleyin
                    @endif
                </p>
            </div>
            <div class="hidden md:flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-500">Sistem Aktif</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Puan Kartı (Superadmin hariç herkese gösterilir) --}}
            @if(!Auth::user()->hasRole('Superadmin'))
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-2xl shadow-lg text-white mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-indigo-200 uppercase tracking-wider">Toplam Puanınız</p>
                            <p class="text-4xl font-black tracking-tight">{{ number_format(Auth::user()->toplam_puan, 0) }}</p>
                        </div>
                        <a href="{{ route('puan-durumu') }}" class="text-indigo-200 hover:text-white transition-colors" title="Liderlik Tablosu">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </a>
                    </div>
                </div>
            @endif

            @if(isset($stats))
                {{-- 1. SUPERADMIN PANELİ --}}
                @if(Auth::user()->hasRole('Superadmin'))
                    @include('dashboard.partials.superadmin')

                {{-- 2. MÜŞTERİ ŞİKAYETİ KURULU --}}
                @elseif(Auth::user()->hasRole('Müşteri Şikayeti Kurulu'))
                    @include('dashboard.partials.sikayet-kurulu')

                    {{-- Kurul üyesi aynı zamanda standart kullanıcı istatistiklerini de görür --}}
                    @include('dashboard.partials.standart-kullanici')

                {{-- 3. ÇÖZÜM LİDERİ --}}
                @elseif(Auth::user()->hasRole('Müşteri Şikayeti Çözüm Lideri'))
                    @include('dashboard.partials.cozum-lideri')

                    {{-- Lider de standart istatistikleri görür --}}
                    @include('dashboard.partials.standart-kullanici')

                {{-- 4. BÖLÜM YÖNETİCİSİ --}}
                @elseif(Auth::user()->hasRole('Bölüm Kalite Yöneticisi'))
                    @include('dashboard.partials.bolum-yoneticisi')
                    
                {{-- 5. STANDART KULLANICI --}}
                @else
                    @include('dashboard.partials.standart-kullanici')
                @endif
            @endif

        </div>
    </div>
</x-app-layout>