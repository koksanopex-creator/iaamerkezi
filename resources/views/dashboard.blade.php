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

        {{-- Disiplin Modülü Partialları --}}
            @include('dashboard.partials.disciplinary-waiting')
            @include('dashboard.partials.disciplinary-active')

            {{-- ========================================================= --}}
            {{-- === YENİ: BEKLEYEN PROJE DAVETLERİ (EN ÜSTTE GÖRÜNÜR) === --}}
            {{-- ========================================================= --}}
            @if(isset($bekleyenProjeDavetleri) && $bekleyenProjeDavetleri->isNotEmpty())
                <div class="mb-8 bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl shadow-xl overflow-hidden animate-fade-in-down">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4 text-white">
                            <h3 class="text-lg font-bold flex items-center gap-2">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                </div>
                                Bekleyen Proje Davetleriniz ({{ $bekleyenProjeDavetleri->count() }})
                            </h3>
                            <span class="text-sm bg-white/20 px-3 py-1 rounded-full backdrop-blur-md">Lütfen yanıtlayınız</span>
                        </div>

                        <div class="space-y-3">
                            @foreach($bekleyenProjeDavetleri as $davet)
                                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 transition hover:bg-white/15">
                                    
                                    {{-- Sol Taraf: Bilgi --}}
                                    <div class="flex items-start gap-4">
                                        <div class="hidden md:flex flex-shrink-0 w-12 h-12 bg-white rounded-full items-center justify-center text-indigo-600 font-bold text-lg shadow-sm">
                                            {{ substr($davet->baslik, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="text-white font-bold text-lg">{{ $davet->baslik }}</h4>
                                            <p class="text-indigo-100 text-sm mt-1 flex items-center gap-2">
                                                <span>Davet Eden:</span>
                                                <span class="font-semibold bg-indigo-800/50 px-2 py-0.5 rounded text-xs">
                                                    {{ $davet->atananTakim->lider->name ?? 'Takım Lideri' }}
                                                </span>
                                                <span class="text-indigo-300">•</span>
                                                <span>{{ $davet->created_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Sağ Taraf: Butonlar --}}
                                    <div class="flex items-center gap-3 w-full md:w-auto">
                                        {{-- Kabul Et Formu --}}
                                        <form action="{{ route('iaa.davetYanitla', $davet->id) }}" method="POST" class="w-full md:w-auto">
                                            @csrf
                                            <input type="hidden" name="yanit" value="kabul">
                                            <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-white text-indigo-700 font-bold rounded-lg shadow-lg hover:bg-indigo-50 hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Kabul Et
                                            </button>
                                        </form>

                                        {{-- Reddet Formu --}}
                                        <form action="{{ route('iaa.davetYanitla', $davet->id) }}" method="POST" class="w-full md:w-auto">
                                            @csrf
                                            <input type="hidden" name="yanit" value="red">
                                            <button type="submit" onclick="return confirm('Bu proje davetini reddetmek istediğinize emin misiniz?')" class="w-full md:w-auto px-6 py-2.5 bg-red-500/20 border border-red-400/30 text-white font-semibold rounded-lg hover:bg-red-500/40 transition-all duration-200 flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Reddet
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            {{-- ========================================================= --}}

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