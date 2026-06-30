{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- BÖLÜM KALİTE YÖNETİCİSİ DASHBOARD                                  --}}
{{-- Yapı: Hızlı Erişim > KPI > Sorumlu Alanlar > Listeler > İadeler > Ziyaretler > Araçlar --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}

<div x-data="{ activeKategori: 'all' }" class="space-y-8 animate-fade-in-up">

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- 0. BÖLÜM: HIZLI ERİŞİM LİNKLERİ                                  --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white/70 backdrop-blur-md rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                HIZLI ERİŞİM
            </span>
            <a href="#sorumlu-alanlar-bolumu"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100 hover:bg-indigo-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Sorumlu Alanlar
                <svg class="w-3 h-3 text-indigo-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#listeler-bolumu"
               class="relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100 hover:bg-purple-100 hover:shadow-sm transition-all group">
                @if(($stats['bolum_onay_sayisi'] ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-1.5 flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-500 border-2 border-white shadow-sm"></span>
                    </span>
                @endif
                <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Onay & Şikayet Listeleri
                <svg class="w-3 h-3 text-purple-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            @if(isset($iadeVerileri))
            <a href="#iade-tablosu-bolumu"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-bold border border-red-100 hover:bg-red-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                İade Tablosu
                <svg class="w-3 h-3 text-red-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            @endif
            <a href="#bolum-ziyaretleri-tablosu"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 text-teal-700 rounded-lg text-xs font-bold border border-teal-100 hover:bg-teal-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Müşteri Ziyaretleri
                <svg class="w-3 h-3 text-teal-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- 1. BÖLÜM: YÖNETİCİ ÖZET KARTLARI (KPI) — Üst Şerit               --}}
    {{-- "Sorumlu Alanlar" kartı kaldırıldı, 3 sütun yapıldı                --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- KPI 1: Onay Bekleyen Projeler --}}
        <a href="{{ route('admin.sikayetler.index', ['tab' => 'onay_bekleyenler']) }}"
           class="group relative bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-5 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative z-10 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    @if(($stats['bolum_onay_sayisi'] ?? 0) > 0)
                        <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-[10px] font-bold animate-pulse">{{ $stats['bolum_onay_sayisi'] }} Bekleyen</span>
                    @endif
                </div>
                <p class="text-indigo-100 text-xs font-medium">Onayınızı Bekleyen Projeler</p>
                <h3 class="text-3xl font-bold mt-1">{{ $stats['bolum_onay_sayisi'] ?? 0 }}</h3>
                <div class="mt-3 flex items-center text-[10px] text-indigo-200 font-medium group-hover:text-white transition-colors">
                    İncele ve Onayla
                    <svg class="w-3.5 h-3.5 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </div>
        </a>

        {{-- KPI 2: Çözüm Bekleyenler --}}
        <a href="{{ route('admin.sikayetler.index', ['tab' => 'islemde']) }}"
           class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-200 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 bg-yellow-50 group-hover:bg-yellow-100 text-yellow-600 transition-colors rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-gray-400 text-[10px] group-hover:text-yellow-600 transition-colors font-medium">Listeyi Aç →</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['islemdeki_sikayet'] ?? 0 }}</h3>
            <p class="text-gray-500 text-xs mt-0.5">Çözüm bekleyen şikayetler</p>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-1">
                @php
                    $oran = ($stats['toplam_sikayet'] ?? 0) > 0
                        ? (($stats['islemdeki_sikayet'] ?? 0) / ($stats['toplam_sikayet'] ?? 1)) * 100
                        : 0;
                @endphp
                <div class="bg-yellow-400 h-1 rounded-full transition-all duration-500" style="width: {{ $oran }}%"></div>
            </div>
        </a>

        {{-- KPI 3: Çözülenler --}}
        <a href="{{ route('admin.sikayetler.index', ['tab' => 'cozulmus']) }}"
           class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-green-200 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 bg-green-50 group-hover:bg-green-100 text-green-600 transition-colors rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-gray-400 text-[10px] group-hover:text-green-600 transition-colors font-medium">Listeyi Aç →</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['cozulen_sikayet'] ?? 0 }}</h3>
            <p class="text-green-600 text-[11px] mt-0.5 font-medium flex items-center">
                @if(($stats['toplam_sikayet'] ?? 0) > 0)
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    %{{ round((($stats['cozulen_sikayet'] ?? 0) / ($stats['toplam_sikayet'] ?? 1)) * 100) }} Başarı
                @else
                    %0 Başarı
                @endif
            </p>
        </a>

    </div>{{-- / KPI Grid --}}


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- 2. BÖLÜM: SORUMLU OLDUĞUNUZ ALANLAR (Kategori Bazlı İstatistikler) --}}
    {{-- İade tablosu stili: Gradient başlık + beyaz içerik                 --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div id="sorumlu-alanlar-bolumu" class="scroll-mt-24 bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        {{-- Gradient Başlık Alanı --}}
        <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-br from-indigo-600 to-purple-700 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="bg-white/20 backdrop-blur-md p-3 rounded-2xl shadow-inner border border-white/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-white text-xl tracking-tight uppercase">SORUMLU OLDUĞUNUZ ALANLAR</h3>
                            <p class="text-xs text-indigo-100 font-medium opacity-80 uppercase tracking-widest">Kategori bazlı şikayet ve proje istatistikleri • {{ $yonetilenKategoriler->count() }} Kategori</p>
                        </div>
                    </div>
                </div>

                {{-- Filtre Alanı --}}
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm w-full lg:w-auto">
                    <form method="GET" action="{{ url()->current() }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4">
                        <div class="flex items-center gap-2 flex-1">
                            <input type="date" name="areas_start_date" value="{{ request('areas_start_date') }}" class="w-full text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3 shadow-inner bg-gray-50/50">
                            <span class="text-gray-300">-</span>
                            <input type="date" name="areas_end_date" value="{{ request('areas_end_date') }}" class="w-full text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-2.5 px-3 shadow-inner bg-gray-50/50">
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-xs font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Filtrele
                            </button>
                            @if(request('areas_start_date') || request('areas_end_date'))
                                <a href="{{ url()->current() }}" class="p-2.5 bg-gray-50 text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 rounded-lg transition-colors border border-gray-100 flex items-center justify-center" title="Filtreyi Temizle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- İçerik Alanı (Beyaz Arka Plan) --}}
        <div class="p-6 lg:p-8">
            {{-- Kategori Sekmeleri --}}
            <div class="bg-gray-100/50 p-1.5 rounded-2xl border border-gray-100 flex flex-wrap gap-1 mb-6">
                <button @click="activeKategori = 'all'"
                        :class="activeKategori === 'all' ? 'bg-white text-indigo-600 shadow-md border-gray-100 scale-105' : 'text-gray-500 hover:text-gray-700 hover:bg-white/50 border-transparent'"
                        class="px-4 py-2.5 rounded-xl text-xs font-black transition-all border duration-300">
                    Tümü
                </button>
                @foreach($yonetilenKategoriler as $kat)
                    <button @click="activeKategori = 'kat-{{ $kat->id }}'"
                            :class="activeKategori === 'kat-{{ $kat->id }}' ? 'bg-white text-indigo-600 shadow-md border-gray-100 scale-105' : 'text-gray-500 hover:text-gray-700 hover:bg-white/50 border-transparent'"
                            class="px-4 py-2.5 rounded-xl text-xs font-black transition-all border duration-300">
                        {{ $kat->ad }}
                    </button>
                @endforeach
            </div>

            {{-- Kategori Kartları Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($yonetilenKategoriler as $kat)
                    @php
                        $katStats = $stats['kategori_bazli_stats'][$kat->id] ?? ['toplam' => 0, 'islemde' => 0, 'onay_bekleyen' => 0, 'cozulen' => 0];
                    @endphp
                    <div x-show="activeKategori === 'all' || activeKategori === 'kat-{{ $kat->id }}'"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 translate-y-8"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-500 group/card relative overflow-hidden">

                        <div class="relative z-10">
                            {{-- Kategori Başlığı --}}
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-8 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
                                    <h5 class="font-black text-gray-900 group-hover/card:text-indigo-600 transition-colors uppercase tracking-wide text-base lg:text-lg">{{ $kat->ad }}</h5>
                                </div>
                                <a href="{{ route('admin.sikayetler.index', ['filtreKategori' => $kat->id]) }}" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl group-hover/card:bg-indigo-600 group-hover/card:text-white transition-all shadow-sm hover:scale-110" title="Tüm şikayetleri göster">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>

                            {{-- İstatistik Metrikleri --}}
                            <div class="grid grid-cols-2 gap-4">
                                <a href="{{ route('admin.sikayetler.index', ['filtreKategori' => $kat->id]) }}"
                                   class="bg-gray-50/50 rounded-xl p-4 border border-gray-100/50 hover:bg-white hover:shadow-lg hover:border-blue-200 transition-all duration-300 block cursor-pointer">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">TOPLAM</p>
                                    </div>
                                    <p class="text-2xl lg:text-3xl font-black text-gray-900">{{ $katStats['toplam'] }}</p>
                                </a>
                                <a href="{{ route('admin.sikayetler.index', ['filtreKategori' => $kat->id, 'tab' => 'islemde']) }}"
                                   class="bg-amber-50/30 rounded-xl p-4 border border-amber-100/30 hover:bg-white hover:shadow-lg hover:border-amber-200 transition-all duration-300 block cursor-pointer">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                                        <p class="text-[10px] font-black text-amber-600/70 uppercase tracking-widest">İŞLEMDE</p>
                                    </div>
                                    <p class="text-2xl lg:text-3xl font-black text-amber-800">{{ $katStats['islemde'] }}</p>
                                </a>
                                <a href="{{ route('admin.sikayetler.index', ['filtreKategori' => $kat->id, 'tab' => 'onay_bekleyenler']) }}"
                                   class="bg-purple-50/30 rounded-xl p-4 border border-purple-100/30 hover:bg-white hover:shadow-lg hover:border-purple-200 transition-all duration-300 block cursor-pointer">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                        <p class="text-[10px] font-black text-purple-600/70 uppercase tracking-widest">ONAY BEKLEYEN</p>
                                    </div>
                                    <p class="text-2xl lg:text-3xl font-black text-purple-900">{{ $katStats['onay_bekleyen'] }}</p>
                                </a>
                                <a href="{{ route('admin.sikayetler.index', ['filtreKategori' => $kat->id, 'tab' => 'cozulmus']) }}"
                                   class="bg-green-50/30 rounded-xl p-4 border border-green-100/30 hover:bg-white hover:shadow-lg hover:border-green-200 transition-all duration-300 block cursor-pointer">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <p class="text-[10px] font-black text-green-600/70 uppercase tracking-widest">ÇÖZÜLEN</p>
                                    </div>
                                    <p class="text-2xl lg:text-3xl font-black text-green-900">{{ $katStats['cozulen'] }}</p>
                                </a>
                            </div>

                            {{-- Alt Bilgi: Verimlilik --}}
                            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-end">
                                <div class="flex items-center gap-2">
                                    <div class="text-right">
                                        <p class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">Verimlilik</p>
                                        <p class="text-sm font-black text-indigo-600 leading-none">%{{ $katStats['toplam'] > 0 ? round(($katStats['cozulen'] / $katStats['toplam']) * 100) : 0 }}</p>
                                    </div>
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>{{-- / Kategori Kartları Grid --}}
        </div>
    </div>{{-- / Sorumlu Alanlar Bölümü --}}


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- 3. BÖLÜM: LİSTELER (Onay Bekleyenler + Departman Şikayet Akışı) --}}
    {{-- Departman Şikayet Akışı yeniden tasarlandı: her kayıt kenarlıklı kart --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div id="listeler-bolumu" class="scroll-mt-24 grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Liste 1: Onayınızı Bekleyenler (Son 5 Kayıt) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                    ONAYINIZI BEKLEYENLER
                    <span class="text-xs font-normal text-gray-500 ml-1">(Son 5 Kayıt)</span>
                </h3>
                <a href="{{ route('admin.sikayetler.index', ['tab' => 'onay_bekleyenler']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white rounded-lg text-xs font-bold hover:bg-purple-700 transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Tümünü Yönet
                </a>
            </div>
            <div class="flex-1 overflow-y-auto max-h-[500px]">
                @if(($stats['onay_bekleyen_liste'] ?? collect())->isNotEmpty())
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-gray-100">
                            @foreach($stats['onay_bekleyen_liste'] ?? [] as $proje)
                                <tr class="hover:bg-purple-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center font-bold text-xs">
                                                    #{{ $proje->id }}
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="font-bold text-gray-800 group-hover:text-purple-700 transition-colors truncate">{{ Str::limit($proje->baslik, 45) }}</p>
                                                    <span class="scale-75 origin-left">
                                                        {!! $proje->durum_etiketi !!}
                                                    </span>
                                                </div>
                                                <p class="text-[10px] text-gray-400 font-bold flex items-center gap-1.5 mt-1.5 flex-wrap">
                                                    <span class="flex items-center gap-1 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">
                                                        <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                        {{ $proje->atananTakim->ad ?? 'Takım Yok' }}
                                                    </span>
                                                    @if(isset($proje->atananTakim->lider))
                                                        <span class="text-gray-200">•</span>
                                                        <a href="{{ route('profile.show', $proje->atananTakim->lider->id) }}" class="flex items-center gap-1 text-gray-500 hover:text-purple-600 transition-colors">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                            {{ $proje->atananTakim->lider->name }}
                                                        </a>
                                                    @endif
                                                    <span class="text-gray-200">•</span>
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-2.5 h-2.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $proje->updated_at->diffForHumans() }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('proje.workspace.show', $proje->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-white border border-purple-200 rounded-lg text-xs font-bold text-purple-700 shadow-sm hover:bg-purple-50 transition-colors">
                                            İncele
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm">Şu an onayınızı bekleyen proje yok.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Liste 2: Departman Şikayet Akışı (Tıklanabilir linkler + Sistem etiket renkleri) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    DEPARTMAN ŞİKAYET AKIŞI
                    <span class="text-xs font-normal text-gray-500 ml-1">(Son 5 Kayıt)</span>
                </h3>
                <a href="{{ route('admin.sikayetler.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">
                    Tümünü Gör &rarr;
                </a>
            </div>
            <div class="flex-1 overflow-y-auto max-h-[500px] p-4 space-y-3">
                @if(($stats['son_departman_sikayetleri'] ?? collect())->isNotEmpty())
                    @foreach($stats['son_departman_sikayetleri'] ?? [] as $sikayet)
                        @php
                            $isSorumlu = in_array($sikayet->sikayet_kategorisi_id, $yonetilenKategoriIds);
                            $workflow = $sikayet->iaaProjesi ? $sikayet->iaaProjesi->ilerleme_verisi : null;
                            $aktifAsamaMetni = $sikayet->iaaProjesi ? $sikayet->iaaProjesi->aktif_asama_metni : 'Belirlenmedi';
                            // Başlık link hedefi: Proje varsa workspace, yoksa şikayet detay
                            $baslikLink = $sikayet->iaaProjesi
                                ? route('proje.workspace.show', $sikayet->iaaProjesi->id)
                                : route('admin.sikayetler.show', $sikayet->id);
                        @endphp
                        <div class="rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md bg-white transition-all duration-300 group/item overflow-hidden">

                            {{-- Kart Üst: Başlık + Tarih --}}
                            <div class="px-4 pt-4 pb-3">
                                <div class="flex items-start justify-between gap-3 mb-2.5">
                                    <a href="{{ $baslikLink }}" class="text-sm font-bold text-gray-800 hover:text-blue-600 transition-colors leading-snug flex-1">
                                        {{ Str::limit($sikayet->musteri_sikayet_konusu, 70) }}
                                    </a>
                                    <span class="text-[10px] font-semibold text-gray-400 bg-gray-50 px-2 py-1 rounded flex-shrink-0 whitespace-nowrap border border-gray-100">
                                        {{ $sikayet->created_at->format('d.m.Y H:i') }}
                                    </span>
                                </div>

                                {{-- Etiketler satırı — Sistem etiket renkleri kullanılıyor --}}
                                <div class="flex items-center gap-2 flex-wrap">
                                    {{-- Proje durum etiketi (varsa, sistem renklerinde) --}}
                                    @if($sikayet->iaaProjesi)
                                        <span class="scale-90 origin-left">
                                            {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                        </span>
                                    @else
                                        {!! $sikayet->musteri_dur_badge !!}
                                    @endif

                                    {{-- Kategori etiketi — tıklanabilir (filtreye yönlendirir) --}}
                                    @if($sikayet->sikayetKategori)
                                        <a href="{{ route('admin.sikayetler.index', ['filtreKategori' => $sikayet->sikayet_kategorisi_id]) }}"
                                           class="px-2 py-0.5 rounded text-[10px] font-bold transition-all hover:scale-105 hover:shadow-sm {{ $isSorumlu ? 'bg-blue-600 text-white shadow-sm hover:bg-blue-700' : 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200' }}">
                                            {{ $sikayet->sikayetKategori->ad }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Kart Orta: Müşteri + Takım Bilgisi — Tıklanabilir linkler --}}
                            <div class="px-4 py-2.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-3 flex-wrap">
                                {{-- Müşteri — Tıklanabilir (müşteri profil sayfası) --}}
                                @if($sikayet->customer_id)
                                    <a href="{{ route('musteri.profil.show', $sikayet->customer_id) }}"
                                       class="flex items-center gap-1.5 text-[11px] font-bold text-blue-700 hover:text-blue-900 transition-colors group/customer">
                                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span class="group-hover/customer:underline">{{ Str::limit($sikayet->musteri_adi, 25) }}</span>
                                        <svg class="w-2.5 h-2.5 opacity-0 group-hover/customer:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @else
                                    <span class="flex items-center gap-1.5 text-[11px] font-bold text-blue-700">
                                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ Str::limit($sikayet->musteri_adi, 25) }}
                                    </span>
                                @endif

                                {{-- Takım + Lider — Lider tıklanabilir (kullanıcı profil sayfası) --}}
                                <div class="flex items-center gap-2 text-[11px] font-bold text-gray-500">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>{{ $sikayet->cozumTakimi->ad ?? 'Atanmadı' }}</span>
                                    @if(isset($sikayet->cozumTakimi->lider))
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('profile.show', $sikayet->cozumTakimi->lider->id) }}"
                                           class="text-gray-500 hover:text-indigo-600 hover:underline transition-colors">
                                            {{ $sikayet->cozumTakimi->lider->name }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Kart Alt: İş Akışı İlerleme Çubuğu --}}
                            @if($workflow && $workflow['toplam'] > 0)
                                @php
                                    // Durum rengine göre ilerleme çubuğu rengi
                                    $durumRenk = $sikayet->iaaProjesi ? $sikayet->iaaProjesi->durum_rengi : 'blue';
                                    $progressBg = match($durumRenk) {
                                        'purple' => 'bg-purple-50/30',
                                        'pink' => 'bg-pink-50/30',
                                        'orange' => 'bg-orange-50/30',
                                        'emerald', 'green' => 'bg-emerald-50/30',
                                        'red' => 'bg-red-50/30',
                                        'gray' => 'bg-gray-50/30',
                                        'indigo' => 'bg-indigo-50/30',
                                        default => 'bg-blue-50/30',
                                    };
                                    $progressBarBg = match($durumRenk) {
                                        'purple' => 'bg-purple-100',
                                        'pink' => 'bg-pink-100',
                                        'orange' => 'bg-orange-100',
                                        'emerald', 'green' => 'bg-emerald-100',
                                        'red' => 'bg-red-100',
                                        'gray' => 'bg-gray-200',
                                        'indigo' => 'bg-indigo-100',
                                        default => 'bg-blue-100',
                                    };
                                    $progressBarFill = match($durumRenk) {
                                        'purple' => 'bg-purple-500',
                                        'pink' => 'bg-pink-500',
                                        'orange' => 'bg-orange-500',
                                        'emerald', 'green' => 'bg-emerald-500',
                                        'red' => 'bg-red-500',
                                        'gray' => 'bg-gray-400',
                                        'indigo' => 'bg-indigo-500',
                                        default => 'bg-blue-500',
                                    };
                                    $progressText = match($durumRenk) {
                                        'purple' => 'text-purple-700',
                                        'pink' => 'text-pink-700',
                                        'orange' => 'text-orange-700',
                                        'emerald', 'green' => 'text-emerald-700',
                                        'red' => 'text-red-700',
                                        'gray' => 'text-gray-500',
                                        'indigo' => 'text-indigo-700',
                                        default => 'text-blue-700',
                                    };
                                @endphp
                                <div class="px-4 py-2.5 border-t border-gray-100 {{ $progressBg }}">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <div class="flex flex-col">
                                            <span class="text-[9px] uppercase font-bold text-gray-400 leading-none mb-0.5">Şu Anki Aşama</span>
                                            <span class="text-[11px] font-bold {{ $progressText }} leading-none">{{ $aktifAsamaMetni }}</span>
                                        </div>
                                        <span class="text-[10px] font-black text-gray-500">{{ $workflow['tamamlanan'] }}/{{ $workflow['toplam'] }} Adım</span>
                                    </div>
                                    <div class="w-full {{ $progressBarBg }} rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $progressBarFill }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $workflow['yuzde'] }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <p class="text-sm">Departmanınızda kayıtlı şikayet bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- / Listeler Grid --}}


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- 4. BÖLÜM: İADE TABLOSU (Son 5 + Devamını Yükle/Gizle)           --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @if(isset($iadeVerileri) && count($iadeVerileri) > 0)
        <div id="iade-tablosu-bolumu" class="scroll-mt-24" x-data="{ iadeShowAll: false }">
            @include('dashboard.partials.iadeler-tablosu', ['iadeLimit' => 5])
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- 5. BÖLÜM: BÖLÜM MÜŞTERİ ZİYARETLERİ                             --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div id="bolum-ziyaretleri-tablosu" class="scroll-mt-24">
        <div class="bg-white rounded-3xl border border-indigo-100 shadow-[0_10px_40px_rgba(79,70,229,0.05)] overflow-hidden relative">
            {{-- Ziyaret Başlık --}}
            <div class="px-6 lg:px-8 py-6 border-b border-indigo-50 bg-gradient-to-r from-indigo-50/50 to-purple-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-600 text-white rounded-2xl shadow-lg shadow-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-lg lg:text-xl font-black text-gray-900 tracking-tight">BÖLÜM MÜŞTERİ ZİYARETLERİ</h4>
                        <p class="text-xs text-gray-500 font-bold mt-0.5">Planlanan ve gerçekleştirilen ziyaret takibi</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 bg-white/60 p-1 rounded-xl border border-gray-100">
                        <input type="date" name="ziyaret_start_date" value="{{ request('ziyaret_start_date') }}" class="text-[10px] border-transparent bg-transparent rounded-lg px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-gray-600">
                        <span class="text-gray-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </span>
                        <input type="date" name="ziyaret_end_date" value="{{ request('ziyaret_end_date') }}" class="text-[10px] border-transparent bg-transparent rounded-lg px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-gray-600">
                        <button type="submit" class="p-2 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 shadow-sm">ZİYARET TAKİBİ</span>
                </div>
            </div>

            {{-- Livewire Ziyaret Tablosu --}}
            @livewire('dashboard.super-admin-visit-table', [
                'bolumIds' => [Auth::user()->bolum_id],
                'hideHeader' => true,
                'startDate' => request('ziyaret_start_date'),
                'endDate' => request('ziyaret_end_date')
            ], key('dept-visit-table-kalite-'.Auth::id()))
        </div>
    </div>{{-- / Ziyaretler --}}


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- 6. BÖLÜM: DİĞER SİSTEM ARAÇLARI (Standart Kartlar)              --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="pt-6 border-t border-gray-200">
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            DİĞER SİSTEM ARAÇLARI
        </h4>
        @include('dashboard.partials.standart-kullanici')
    </div>

</div>{{-- / Ana Container --}}
