<div class="min-h-screen bg-gray-50/50">

    {{-- HEADER & FİLTRELER --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-[98%] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between py-4 gap-4">

                {{-- Başlık --}}
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Yönetim Kokpiti</h1>
                        <p class="text-xs text-gray-500 font-medium">Genel Bakış ve Performans Analizi</p>
                    </div>
                </div>

                {{-- FİLTRE BAR --}}
                <div class="flex items-center gap-3 bg-gray-50 p-1.5 rounded-xl border border-gray-200">

                    {{-- Bölüm Filtresi --}}
                    <div class="relative">
                        <select wire:model.live="selectedBolumId"
                            class="pl-3 pr-8 py-1.5 text-sm border-0 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 w-40 text-gray-700 font-medium cursor-pointer hover:bg-gray-50 transition-colors">
                            <option value="">Tüm Bölümler</option>
                            @foreach($bolumler as $bolum)
                                <option value="{{ $bolum->id }}">{{ $bolum->ad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="h-6 w-px bg-gray-300 mx-1"></div>

                    {{-- Tarih Filtresi --}}
                    <div class="flex items-center gap-2">
                        <input type="date" wire:model.live="startDate"
                            class="py-1.5 px-3 border-0 bg-white rounded-lg shadow-sm text-sm text-gray-700 focus:ring-2 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors">
                        <span class="text-gray-400 font-medium">-</span>
                        <input type="date" wire:model.live="endDate"
                            class="py-1.5 px-3 border-0 bg-white rounded-lg shadow-sm text-sm text-gray-700 focus:ring-2 focus:ring-indigo-500 cursor-pointer hover:bg-gray-50 transition-colors">
                    </div>

                    @if($startDate || $endDate || $selectedBolumId)
                        <button wire:click="clearFilters"
                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors ml-1"
                            title="Filtreleri Temizle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>

            </div>

            {{-- AKTİF FİLTRELER BİLGİ BANKI --}}
            @if($startDate || $endDate || $selectedBolumId)
                <div class="pb-3 animate-fade-in-down">
                    <div
                        class="flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-100 rounded-lg text-xs font-semibold text-indigo-700">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Şu an filtreleniyor:</span>

                        @if($selectedBolumId)
                            <span class="px-2 py-0.5 bg-white rounded border border-indigo-200 shadow-sm">
                                {{ $bolumler->find($selectedBolumId)->ad }} Bölümü
                            </span>
                        @endif

                        @if($startDate || $endDate)
                            <span class="px-2 py-0.5 bg-white rounded border border-indigo-200 shadow-sm">
                                {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d.m.Y') : '...' }} -
                                {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d.m.Y') : '...' }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>

    <div class="py-8">
        <div class="max-w-[98%] mx-auto space-y-6">

            {{-- 1. KPI ve ÖZET BLOĞU --}}
            @include('admin.raporlar.partials.executive.kpi-overview')

            {{-- 2. KAYAN HABER BANDI (Yatay Slider) --}}
            @include('admin.raporlar.partials.executive.horizontal-ticker')

            {{-- 3. SON ŞİKAYETLER AKIŞI (MODİFİYE EDİLMİŞ VERSİYON - DİKEY SCROLL) --}}
            <div class="bg-white rounded-2xl shadow-lg border-2 border-indigo-100 overflow-hidden relative">

                {{-- HEADER --}}
                <div
                    class="p-4 bg-gradient-to-r from-indigo-50 to-white border-b border-indigo-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100/50 rounded-lg">
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-600"></span>
                            </span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Canlı Şikayet Akışı</h3>
                            <p class="text-[11px] text-gray-500 font-medium">Anlık müşteri geri bildirimleri ve
                                durumları</p>
                        </div>
                    </div>
                    <span
                        class="text-xs font-mono bg-white border border-indigo-100 text-indigo-600 px-3 py-1 rounded-full shadow-sm">
                        Son {{ $sonSikayetler->count() }} Kayıt
                    </span>
                </div>

                {{-- SCROLL CONTAINER (Hover Durunca Kaydırma) --}}
                {{-- Height artırıldı, scroll alanı belirginleştirildi --}}
                <div class="relative h-[500px] overflow-hidden group hover:overflow-y-auto custom-scrollbar bg-gray-50/30"
                    onmouseenter="this.querySelector('.animate-marquee-vertical').style.animationPlayState = 'paused';"
                    onmouseleave="this.querySelector('.animate-marquee-vertical').style.animationPlayState = 'running';">

                    <div class="animate-marquee-vertical space-y-4 p-5">
                        @foreach($sonSikayetler as $sikayet)
                            @php
                                $statusColor = match ($sikayet->musteri_durum) {
                                    'Yeni' => 'yellow',
                                    'İşlemde' => 'blue',
                                    'Kapatıldı', 'Çözümlendi' => 'green',
                                    'Revize Ediliyor', 'Yeniden Açıldı', 'Geciken' => 'red',
                                    default => 'gray'
                                };
                                $isExternal = ($sikayet->olusturanKurulUyesi && $sikayet->olusturanKurulUyesi->is_personnel == 0) || ($sikayet->user_id && !$sikayet->olusturanKurulUyesi);

                                // Süre ve Özel Durum Metni Hesabı
                                $gecenSure = 0;
                                $sureMetni = "";

                                if ($sikayet->musteri_durum == 'Kapatıldı' || $sikayet->musteri_durum == 'Çözümlendi') {
                                    if ($sikayet->kurul_onay_tarihi) {
                                        $gecenSure = \Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->diffInDays(\Carbon\Carbon::parse($sikayet->kurul_onay_tarihi));
                                    }
                                    $gecenSure = round($gecenSure);
                                    $sureMetni = "$gecenSure Günde ÇÖZÜLDÜ";
                                } else {
                                    $gecenSure = round(\Carbon\Carbon::parse($sikayet->musteri_sikayet_tarihi)->diffInDays(now()));

                                    if ($sikayet->musteri_durum == 'Yeni') {
                                        $sureMetni = "$gecenSure Gündür İŞLEME ALINMASI BEKLENİYOR";
                                    } elseif ($sikayet->musteri_durum == 'İşlemde') {
                                        $sureMetni = "$gecenSure Gündür İŞLEMDE";
                                    } elseif ($sikayet->musteri_durum == 'Geciken') {
                                        $sureMetni = "$gecenSure Gündür GECİKMİŞ DURUMDA";
                                    } else {
                                        $sureMetni = "$gecenSure Gündür " . mb_strtoupper($sikayet->musteri_durum);
                                    }
                                }
                            @endphp

                            <div class="relative rounded-xl border-l-4 transition-all duration-300 hover:shadow-lg group/card
                                                            {{ $isExternal ? 'bg-red-50 border-red-500 ring-1 ring-red-200' : 'bg-' . $statusColor . '-50 border-' . $statusColor . '-500' }}
                                                            p-4 overflow-hidden mb-3">

                                {{-- HEADER: MÜŞTERİ GİRDİSİ & KONU --}}
                                <div
                                    class="mb-3 border-b {{ $isExternal ? 'border-red-200' : 'border-' . $statusColor . '-200' }} pb-2">
                                    <div class="flex justify-between items-start gap-2">
                                        {{-- Konu --}}
                                        <h4 class="text-sm font-bold text-gray-900 leading-snug flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-extrabold text-gray-500 uppercase">ŞİKAYET
                                                    KONUSU:</span>
                                                {{-- DURUM ROZETİ --}}
                                                <span
                                                    class="px-2 py-0.5 rounded-[4px] text-[10px] font-bold uppercase tracking-wide bg-white border border-{{ $statusColor }}-200 text-{{ $statusColor }}-700 shadow-sm">
                                                    {{ $sikayet->musteri_durum }}
                                                </span>
                                            </div>
                                            {{ $sikayet->musteri_sikayet_konusu }}
                                        </h4>

                                        {{-- Dış Kaynak Rozeti --}}
                                        @if($isExternal)
                                            <div
                                                class="flex-shrink-0 flex items-center gap-1.5 px-2 py-1 bg-red-600 text-white text-[9px] font-black rounded shadow-sm animate-pulse">
                                                MÜŞTERİ GİRDİSİ
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- BODY: 2 SÜTUNLU GRİD --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-xs">

                                    {{-- Sol Sütun --}}
                                    <div class="space-y-2">
                                        {{-- Müşteri --}}
                                        <div>
                                            <span
                                                class="block text-[10px] uppercase font-bold text-gray-500 opacity-75">Müşteri
                                                İsmi :</span>
                                            <span class="font-semibold text-gray-800">{{ $sikayet->musteri_adi }}</span>
                                        </div>

                                        {{-- Bölüm (VURGULU) --}}
                                        <div>
                                            <span
                                                class="block text-[10px] uppercase font-bold text-gray-500 opacity-75">Şikayet
                                                Açılan Bölüm :</span>
                                            <span
                                                class="text-sm font-extrabold text-indigo-900 bg-white/50 px-1 rounded -ml-1">
                                                {{ $sikayet->sikayetKategori->bolum->ad ?? 'Genel' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Sağ Sütun --}}
                                    <div class="space-y-2">
                                        {{-- Kategori --}}
                                        <div>
                                            <span
                                                class="block text-[10px] uppercase font-bold text-gray-500 opacity-75">Şikayet
                                                Kategorisi :</span>
                                            <span
                                                class="font-medium text-gray-700">{{ $sikayet->sikayetKategori->ad ?? '-' }}</span>
                                        </div>

                                        {{-- Alt Kategori --}}
                                        <div>
                                            <span
                                                class="block text-[10px] uppercase font-bold text-gray-500 opacity-75">Şikayet
                                                Alt Kategorisi :</span>
                                            <span
                                                class="font-medium text-gray-700">{{ $sikayet->sikayetAltKategori->ad ?? '-' }}</span>
                                        </div>
                                    </div>

                                </div>

                                {{-- FOOTER: SÜRE MESAJI (Yanıp Sönen Kırmızı) --}}
                                <div
                                    class="mt-4 pt-2 border-t {{ $isExternal ? 'border-red-200' : 'border-' . $statusColor . '-200' }} flex justify-between items-center">
                                    <div class="font-black text-xs text-red-600 animate-pulse flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $sureMetni }}
                                    </div>

                                    <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}"
                                        class="flex-shrink-0 text-[10px] font-bold text-gray-500 hover:text-indigo-700 bg-white/60 hover:bg-white px-2 py-1 rounded transition-colors border border-transparent hover:border-indigo-200">
                                        DETAY GİT &rarr;
                                    </a>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 4. BÖLÜM PERFORMANS KARNESİ --}}
            @include('admin.raporlar.partials.executive.department-performance')

            {{-- 5. GRAFİK ANALİZLERİ (ApexCharts Slider) --}}
            @include('admin.raporlar.partials.executive.charts-slider')

        </div>
        {{-- SCRIPTS --}}
        @include('admin.raporlar.partials.executive.scripts')

        <style>
            @keyframes marquee-vertical {
                0% {
                    transform: translateY(0);
                }

                100% {
                    transform: translateY(-50%);
                }

                /* Not: Sonsuz döngü için listenin kopyalanması gerekebilir, basitlik için yukarı kayıp bitince durabilir veya JS ile resetlenebilir. 
               CSS only infinite vertical scroll usually implies content duplication. 
               For "Flow", usually it resets. 
               Let's trust the existing 'animate-marquee-vertical' class if it was working, or add basic CSS. 
            */
            }

            .animate-marquee-vertical {
                animation: marquee-vertical 60s linear infinite;
            }

            /* Hover pause is handled inline with JS properly, but also CSS hover check */
            .group:hover .animate-marquee-vertical {
                animation-play-state: paused;
            }
        </style>
    </div>