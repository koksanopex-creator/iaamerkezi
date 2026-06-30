<div class="space-y-8 animate-fade-in relative">
    {{-- 0. PRINT HEADER (Sadece Yazdırmada Görünür) --}}
    <div id="print-header" class="hidden print:flex flex-col items-center justify-center p-8 border-b-2 border-slate-900 mb-8">
        <div class="text-center leading-tight mb-4">
            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">İADE ANALİZ RAPORU</h1>
            <p class="text-sm font-bold text-slate-500 uppercase">Kurumsal Raporlama Sistemi</p>
        </div>
        <div class="flex items-center gap-2 px-4 py-1.5 bg-slate-100 rounded-full text-xs font-black text-slate-700 uppercase italic">
            Rapor Dönemi: {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}
        </div>
    </div>

    {{-- YENİ: ÜST AKSİYON BARI (Başlık ve Sağ Üst Butonlar) --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">İade Analiz Dashboard</h1>
            <p class="text-xs font-bold text-slate-400 uppercase flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Gerçek Zamanlı Veri Analizi ve Raporlama
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- 1. EXCEL İNDİR (Sunucu taraflı - Logo ve Formatlı) --}}
            <button wire:click="exportExcel" class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-2xl text-xs font-black hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100 hover:-translate-y-0.5 active:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>EXCEL İNDİR</span>
            </button>

            {{-- 2. PDF İNDİR (Sunucu taraflı - Sadece Tablo ve Logo) --}}
            <button wire:click="exportPdf" class="flex items-center gap-2 px-5 py-2.5 bg-rose-600 text-white rounded-2xl text-xs font-black hover:bg-rose-700 transition-all shadow-xl shadow-rose-100 hover:-translate-y-0.5 active:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1M9 13h1M9 17h1M13 9h1M13 13h1M13 17h1" /></svg>
                <span>PDF İNDİR</span>
            </button>

            {{-- 3. YAZDIR (Tarayıcı tabanlı - Tüm Dashboard ve Grafiklerle) --}}
            <button onclick="window.print()" class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-2xl text-xs font-black hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 hover:-translate-y-0.5 active:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2-2v4a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>YAZDIR</span>
            </button>
        </div>
    </div>

    {{-- 1. FİLTRE BAR --}}
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden sticky top-4 z-40">
        <div class="p-4 flex flex-wrap items-center justify-between gap-4 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Hızlı Filtre Butonları --}}
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200">
                    <button wire:click="setFilterPeriod('all')" class="px-3 py-1 text-[10px] font-black uppercase rounded-lg transition-all {{ $startDate == null && $endDate == null ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Hepsi</button>
                    <button wire:click="setFilterPeriod('month')" class="px-3 py-1 text-[10px] font-black uppercase rounded-lg transition-all {{ $startDate == Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') ? 'bg-rose-600 text-white shadow-md' : 'text-slate-500 hover:text-slate-700' }}">Bu Ay</button>
                    <button wire:click="setFilterPeriod('quarter')" class="px-3 py-1 text-[10px] font-black uppercase rounded-lg transition-all {{ $startDate == Carbon\Carbon::now()->startOfQuarter()->format('Y-m-d') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:text-slate-700' }}">Bu Çeyrek</button>
                    <button wire:click="setFilterPeriod('year')" class="px-3 py-1 text-[10px] font-black uppercase rounded-lg transition-all {{ $startDate == Carbon\Carbon::now()->startOfYear()->format('Y-m-d') ? 'bg-amber-600 text-white shadow-md' : 'text-slate-500 hover:text-slate-700' }}">Bu Yıl</button>
                </div>

                {{-- Tarih Aralığı --}}
                <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm group hover:border-indigo-300 transition-all">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <input type="date" wire:model.live="startDate" class="border-0 p-0 text-sm focus:ring-0 cursor-pointer text-slate-600 font-bold bg-transparent">
                    <span class="text-slate-300">-</span>
                    <input type="date" wire:model.live="endDate" class="border-0 p-0 text-sm focus:ring-0 cursor-pointer text-slate-600 font-bold bg-transparent">
                </div>

                {{-- Bölüm Filtresi --}}
                <div class="relative">
                    <select wire:model.live="selectedBolumId" class="pl-10 pr-8 py-2 text-sm border-slate-200 bg-white rounded-xl shadow-sm focus:ring-2 focus:ring-rose-500 min-w-[200px] text-slate-700 font-bold appearance-none transition-all">
                        <option value="">Tüm Bölümler</option>
                        @foreach($bolumler as $bolum)
                            <option value="{{ $bolum->id }}">{{ $bolum->ad }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>

                {{-- Sebep Filtresi --}}
                <div class="relative">
                    <select wire:model.live="selectedReason" class="pl-10 pr-8 py-2 text-sm border-slate-200 bg-white rounded-xl shadow-sm focus:ring-2 focus:ring-rose-500 min-w-[200px] text-slate-700 font-bold appearance-none transition-all">
                        <option value="">Tüm İade Sebepleri</option>
                        @foreach($reasons as $reason)
                            <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Arama --}}
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="İade, müşteri veya açıklama ara..." class="pl-10 pr-4 py-2 text-sm border-slate-200 bg-white rounded-xl shadow-sm focus:ring-2 focus:ring-rose-500 w-64 text-slate-700 placeholder-slate-400 transition-all">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>

                <button wire:click="clearFilters" class="p-2 text-slate-400 hover:text-rose-600 transition-colors" title="Filtreleri Temizle">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. KPI KARTLARI --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Toplam İade Sayısı --}}
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" /></svg>
            </div>
            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Toplam İade Adedi</h3>
                <div class="text-lg font-black text-slate-900">{{ number_format($totalIadeCount) }} <span class="text-xs text-slate-400">Kayıt</span></div>
            </div>
        </div>

        {{-- Birim Bazlı Miktarlar --}}
        @foreach($unitTotals as $unitTotal)
            <div wire:click="setFilterUnit('{{ $unitTotal->birim }}')" 
                 class="bg-white rounded-2xl p-4 shadow-sm border {{ $selectedUnit === $unitTotal->birim ? 'border-indigo-500 ring-2 ring-indigo-50' : 'border-slate-100' }} flex items-center gap-4 hover:shadow-md transition-all cursor-pointer group">
                <div class="w-12 h-12 {{ $selectedUnit === $unitTotal->birim ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600' }} rounded-2xl flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-black {{ $selectedUnit === $unitTotal->birim ? 'text-indigo-600' : 'text-slate-400' }} uppercase tracking-widest mb-0.5">Toplam Miktar ({{ $unitTotal->birim }})</h3>
                    <div class="text-lg font-black text-slate-900">{{ number_format($unitTotal->total_amount, 2) }} <span class="text-xs text-slate-400">{{ $unitTotal->birim }}</span></div>
                </div>
            </div>
        @endforeach

        {{-- En Çok İade Sebebi --}}
        <div wire:click="setFilterReason('{{ $topReason->iade_sebebi ?? '' }}')" 
             class="bg-white rounded-2xl p-4 shadow-sm border {{ $selectedReason === ($topReason->iade_sebebi ?? '') ? 'border-rose-500 ring-2 ring-rose-50' : 'border-slate-100' }} flex items-center gap-4 hover:shadow-md transition-all cursor-pointer group">
            <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center shrink-0 border border-rose-100">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">En Sık İade Sebebi</h3>
                <div class="text-sm font-black text-slate-900">{{ $topReason->iade_sebebi ?? '-' }}</div>
                <div class="text-[10px] text-slate-500 font-bold">({{ $topReason->count ?? 0 }} kez tekrarlandı)</div>
            </div>
        </div>
    </div>

    {{-- 3. GRAFİKLER --}}
    @php
        $iadeDateStr = '<span class="font-bold text-slate-700">Tüm Zamanlar</span>';
        if($startDate && $endDate) {
            $iadeDateStr = '<span class="font-bold text-slate-700">' . \Carbon\Carbon::parse($startDate)->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d.m.Y') . '</span>';
        } elseif($startDate) {
            $iadeDateStr = '<span class="font-bold text-slate-700">' . \Carbon\Carbon::parse($startDate)->format('d.m.Y') . ' sonrası</span>';
        } elseif($endDate) {
            $iadeDateStr = '<span class="font-bold text-slate-700">' . \Carbon\Carbon::parse($endDate)->format('d.m.Y') . ' öncesi</span>';
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- İade Sebepleri Dağılımı (Donut) --}}
        <div class="bg-white rounded-3xl p-6 shadow-lg border border-slate-100 flex flex-col">
            <h4 class="text-slate-900 font-black flex items-center gap-2 text-sm uppercase tracking-wider mb-1">
                <span class="w-1.5 h-4 bg-rose-600 rounded-full"></span>
                İade Sebepleri Dağılımı
            </h4>
            <p class="text-[10px] text-slate-400 mb-6 ml-3.5">({!! $iadeDateStr !!} İşlem Tarihli)</p>
            <div id="reasonsChart" class="min-h-[300px] flex-1" wire:ignore></div>
        </div>

        {{-- Bölüm Bazlı İade Dağılımı (Bar) --}}
        <div class="bg-white rounded-3xl p-6 shadow-lg border border-slate-100 flex flex-col">
            <h4 class="text-slate-900 font-black flex items-center gap-2 text-sm uppercase tracking-wider mb-1">
                <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                Bölüm Bazlı İade Yoğunluğu
            </h4>
            <p class="text-[10px] text-slate-400 mb-6 ml-3.5">({!! $iadeDateStr !!} İşlem Tarihli)</p>
            <div id="deptChart" class="min-h-[300px] flex-1" wire:ignore></div>
        </div>

        {{-- YENİ: İade Trend Eğrisi (Aylık) --}}
        <div class="bg-white rounded-3xl p-6 shadow-lg border border-slate-100 lg:col-span-2 flex flex-col">
            <h4 class="text-slate-900 font-black flex items-center gap-2 text-sm uppercase tracking-wider mb-1">
                <span class="w-1.5 h-4 bg-blue-600 rounded-full"></span>
                İade Kaydı Trend Eğrisi (Aylık)
            </h4>
            <p class="text-[10px] text-slate-400 mb-6 ml-3.5">({!! $iadeDateStr !!} İşlem Tarihli)</p>
            <div id="trendChart" class="min-h-[300px]" wire:ignore></div>
        </div>

        {{-- YENİ: Şikayet / İade Oranı (Bar) --}}
        <div class="bg-white rounded-3xl p-6 shadow-lg border border-slate-100 lg:col-span-2 flex flex-col">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h4 class="text-slate-900 font-black flex items-center gap-2 text-sm uppercase tracking-wider mb-1">
                        <span class="w-1.5 h-4 bg-emerald-600 rounded-full"></span>
                        <span>Toplam Şikayet / İadeli Şikayet Oranı</span>
                    </h4>
                    <p class="text-[10px] text-slate-400 ml-3.5">({!! $iadeDateStr !!} Açılış Tarihli)</p>
                </div>
                <span id="returnRateBadge" class="px-2 py-1 bg-rose-100 text-rose-700 text-xs font-black rounded-full uppercase italic">İade Oranı: %{{ $returnRate }}</span>
            </div>
            <div id="ratioChart" class="min-h-[300px]" wire:ignore></div>
        </div>

        {{-- YENİ: Bölüm Bazlı İade Sebepleri (Metin & Mini Progress Bar) --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden lg:col-span-2">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="space-y-1">
                    <h4 class="text-slate-900 font-black flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        <span>Bölüm Bazlı Detaylı İade Sebepleri Dağılımı</span>
                    </h4>
                    <p class="text-[10px] text-slate-500 font-bold flex items-center gap-1">
                        ({!! $iadeDateStr !!} İşlem Tarihli)
                    </p>
                </div>
            </div>
            
            <div class="p-6 bg-slate-50/30">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($deptReasonStats ?? [] as $dept => $data)
                        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col">
                            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                                <h5 class="font-black text-slate-800 uppercase tracking-wide text-[13px] truncate pr-2" title="{{ $dept }}">{{ $dept }}</h5>
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-600 text-[10px] font-black rounded-lg border border-rose-100 whitespace-nowrap">{{ $data['total'] }} İade</span>
                            </div>
                            <div class="space-y-4 max-h-[280px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($data['reasons'] as $reason)
                                    <div>
                                        <div class="flex justify-between items-end mb-1.5">
                                            <span class="font-bold text-slate-600 text-xs truncate pr-2" title="{{ $reason['sebep'] }}">{{ $reason['sebep'] }}</span>
                                            <div class="flex items-center gap-1.5 whitespace-nowrap">
                                                <span class="font-black text-slate-900 text-sm">{{ $reason['count'] }}</span>
                                                <span class="text-[9px] px-1.5 py-0.5 bg-slate-100 text-slate-500 font-bold rounded">
                                                    %{{ $reason['percent'] }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $reason['percent'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400 font-medium text-sm border-2 border-dashed border-slate-200 rounded-2xl">
                            Seçili kriterlere uygun veri bulunamadı.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- 4. DETAY TABLOSU --}}
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="space-y-1">
                <h4 class="text-slate-900 font-black flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                    <span>Detaylı İade Listesi</span>
                    <span class="px-2.5 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-black rounded-full uppercase">Toplam {{ $totalIadeCount }} Kayıt</span>
                </h4>
                @if(!$startDate && !$endDate)
                    <p class="text-[10px] text-slate-500 font-bold flex items-center gap-1">
                        <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                        Sistemdeki tüm zamanlara ait iade kayıtları listelenmektedir. Filtreleyerek daraltabilirsiniz.
                    </p>
                @endif
            </div>
            <div class="text-xs font-bold text-slate-400 uppercase">
                En Son Kayıttan Başlayarak
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[11px] font-black uppercase text-slate-400 tracking-wider">Tarihler</th>
                        <th class="px-6 py-4 text-[11px] font-black uppercase text-slate-400 tracking-wider">Müşteri / Şikayet</th>
                        <th class="px-6 py-4 text-[11px] font-black uppercase text-slate-400 tracking-wider">Bölüm</th>
                        <th class="px-6 py-4 text-[11px] font-black uppercase text-slate-400 tracking-wider text-right">Miktar</th>
                        <th class="px-6 py-4 text-[11px] font-black uppercase text-slate-400 tracking-wider">İade Sebebi</th>
                        <th class="px-6 py-4 text-[11px] font-black uppercase text-slate-400 tracking-wider">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($iadeler as $iade)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-1.5" title="İade Tarihi">
                                        <span class="text-[10px] font-bold text-rose-500 w-14 uppercase">İade:</span>
                                        <span class="text-xs font-black text-slate-900">{{ $iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : $iade->created_at->format('d.m.Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5" title="Şikayet Açılış Tarihi">
                                        <span class="text-[10px] font-bold text-slate-400 w-14 uppercase">Şikayet:</span>
                                        <span class="text-xs font-bold text-slate-600">{{ $iade->musteriSikayeti->created_at->format('d.m.Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5" title="Kapanış Tarihi">
                                        <span class="text-[10px] font-bold text-slate-400 w-14 uppercase">Kapanış:</span>
                                        <span class="text-xs font-bold text-slate-600">
                                            @if(in_array($iade->musteriSikayeti->musteri_durum, ['Çözümlendi', 'Kapatıldı', 'Talep Olarak Kapatıldı', 'Hatalı Bildirim Olarak Kapatıldı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']))
                                                {{ $iade->musteriSikayeti->updated_at->format('d.m.Y') }}
                                            @else
                                                <span class="text-[10px] font-black text-amber-600 uppercase">Açık</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($iade->musteriSikayeti->customer && $iade->musteriSikayeti->customer->logo_path)
                                        <img src="{{ asset('storage/' . $iade->musteriSikayeti->customer->logo_path) }}" class="w-8 h-8 rounded-lg object-cover shadow-sm border border-slate-200" alt="Logo">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200 shadow-inner">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </div>
                                    @endif

                                    <div class="flex flex-col">
                                        @if($iade->musteriSikayeti->customer_id)
                                            <a href="{{ route('musteri.profil.show', $iade->musteriSikayeti->customer_id) }}" class="text-sm font-bold text-indigo-600 hover:text-rose-600 transition-colors tracking-tight">
                                                {{ $iade->musteriSikayeti->musteri_adi }}
                                            </a>
                                        @else
                                            <div class="text-sm font-bold text-slate-700 tracking-tight">{{ $iade->musteriSikayeti->musteri_adi }}</div>
                                        @endif
                                        <div class="text-[11px] text-slate-400 font-medium truncate max-w-[200px]" title="{{ $iade->musteriSikayeti->musteri_sikayet_konusu }}">
                                            #{{ $iade->musteriSikayeti->id }} - {{ $iade->musteriSikayeti->musteri_sikayet_konusu }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase rounded-lg border border-indigo-100">
                                    {{ $iade->musteriSikayeti->sikayetKategori->bolum->ad ?? 'Genel' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-black text-slate-900">{{ number_format($iade->miktar, 2) }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $iade->birim }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    <span class="text-xs font-bold text-slate-700">{{ $iade->iade_sebebi }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 truncate max-w-[150px] italic mt-0.5">{{ $iade->aciklama }}</div>
                            </td>
                            <td class="px-6 py-4 flex items-center gap-1">
                                <a href="{{ route('admin.sikayetler.show', $iade->musteriSikayeti->id) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all inline-block" title="Şikayet Detayı">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                @if($iade->musteriSikayeti->iaaProjesi)
                                <a href="{{ route('proje.workspace.show', $iade->musteriSikayeti->iaaProjesi->id) }}" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all inline-block" title="Proje Çalışma Alanı">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="max-w-xs mx-auto">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    </div>
                                    <h5 class="text-slate-900 font-bold mb-1">İade Kaydı Bulunamadı</h5>
                                    <p class="text-xs text-slate-400 font-medium">Seçilen kriterlere uygun herhangi bir iade işlemi bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-center">
            @if($totalIadeCount > 5)
                <button wire:click="toggleShowAll" class="flex items-center gap-2 px-6 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-black uppercase rounded-full shadow-sm hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-100 transition-all group">
                    @if($showAll)
                        <svg class="w-4 h-4 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                        <span>Daha Az Göster</span>
                    @else
                        <svg class="w-4 h-4 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        <span>Devamını Görüntüle ({{ $totalIadeCount - 5 }} Kayıt Daha)</span>
                    @endif
                </button>
            @endif
        </div>
    </div>

    {{-- CHART SCRIPTS --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const colors = ['#e11d48', '#4f46e5', '#0891b2', '#059669', '#d97706', '#7c3aed', '#db2777'];

            // APEXCHARTS TÜRKÇE YERELLEŞTİRME
            const trLocale = {
                "name": "tr",
                "options": {
                    "months": ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],
                    "shortMonths": ["Oca", "Şub", "Mar", "Nis", "May", "Haz", "Tem", "Ağu", "Eyl", "Eki", "Kas", "Ara"],
                    "days": ["Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi"],
                    "shortDays": ["Paz", "Pzt", "Sal", "Çar", "Per", "Cum", "Cmt"],
                    "toolbar": {
                        "exportToSVG": "SVG İndir",
                        "exportToPNG": "PNG İndir",
                        "exportToCSV": "CSV İndir",
                        "menu": "Menü",
                        "selection": "Seçim",
                        "selectionZoom": "Seçim Yakınlaştır",
                        "zoomIn": "Yakınlaştır",
                        "zoomOut": "Uzaklaştır",
                        "pan": "Kaydır",
                        "reset": "Sıfırla"
                    }
                }
            };

            const commonOptions = {
                chart: {
                    locales: [trLocale],
                    defaultLocale: 'tr',
                    toolbar: { show: false }
                }
            };

            // 1. İade Sebepleri Donut
            const reasonsChart = new ApexCharts(document.querySelector("#reasonsChart"), {
                ...commonOptions,
                series: @json($reasonDistribution->pluck('count')),
                labels: @json($reasonDistribution->pluck('iade_sebebi')),
                chart: { type: 'donut', height: 350, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
                colors: colors,
                dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + "%" },
                legend: { position: 'bottom', labels: { colors: '#64748b', useSeriesColors: false } },
                stroke: { width: 0 },
                plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'Toplam', color: '#1e293b', fontWeight: 900 } } } } }
            });
            reasonsChart.render();

            // 2. Bölüm Dağılımı Bar
            const deptChart = new ApexCharts(document.querySelector("#deptChart"), {
                ...commonOptions,
                series: [{ name: 'İade Adedi', data: @json($deptDistribution->pluck('count')) }],
                chart: { ...commonOptions.chart, type: 'bar', height: 350 },
                plotOptions: { bar: { borderRadius: 8, distributed: true, columnWidth: '45%' } },
                colors: ['#4f46e5', '#6366f1', '#818cf8', '#a5b4fc', '#c7d2fe'],
                dataLabels: { enabled: true, style: { fontWeight: 900 } },
                xaxis: { categories: @json($deptDistribution->pluck('bolum_adi')), labels: { style: { colors: '#64748b', fontWeight: 600 } } },
                yaxis: { labels: { style: { colors: '#64748b' } } },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
            });
            deptChart.render();

            // 3. Trend Eğrisi Area
            const trendChart = new ApexCharts(document.querySelector("#trendChart"), {
                ...commonOptions,
                series: [{ name: 'İade Adedi', data: @json($trendCounts) }],
                chart: { ...commonOptions.chart, type: 'area', height: 350, zoom: { enabled: false } },
                colors: ['#4f46e5'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100, 100, 100] } },
                xaxis: { categories: @json($trendDates), labels: { style: { colors: '#64748b', fontWeight: 600 } } },
                yaxis: { labels: { style: { colors: '#64748b' } } },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
            });
            trendChart.render();

            // 4. Oran Grafiği Bar
            const ratioChart = new ApexCharts(document.querySelector("#ratioChart"), {
                ...commonOptions,
                series: [{ name: 'Şikayet Sayısı', data: @json($ratioData) }],
                chart: { ...commonOptions.chart, type: 'bar', height: 350 },
                plotOptions: { bar: { borderRadius: 10, distributed: true, columnWidth: '35%' } },
                colors: ['#e11d48', '#64748b'],
                dataLabels: { enabled: true, style: { fontWeight: 900 } },
                xaxis: { categories: ['İadeli Şikayetler', 'İadesiz Şikayetler'], labels: { style: { colors: '#64748b', fontWeight: 600 } } },
                yaxis: { labels: { style: { colors: '#64748b' } } },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
            });
            ratioChart.render();


            // Livewire Refresh Event (Dinamik Güncelleme)
            window.addEventListener('contentChanged', (event) => {
                const data = event.detail[0]; // Livewire 3 formatı
                
                reasonsChart.updateSeries(data.reasons.map(i => i.count));
                reasonsChart.updateOptions({ labels: data.reasons.map(i => i.iade_sebebi) });

                deptChart.updateSeries([{ data: data.depts.map(i => i.count) }]);
                deptChart.updateOptions({ xaxis: { categories: data.depts.map(i => i.bolum_adi) } });

                trendChart.updateSeries([{ data: data.trendCounts }]);
                trendChart.updateOptions({ xaxis: { categories: data.trendDates } });

                ratioChart.updateSeries([{ data: data.ratioData }]);
                if (document.getElementById('returnRateBadge')) {
                    document.getElementById('returnRateBadge').innerText = `İade Oranı: %${data.returnRate}`;
                }

            });
        });
    </script>
    @endpush

    {{-- CSS: PRINT OPTIMIZATION --}}
    <style>
        @media print {
            .sticky { position: static !important; }
            .shadow-xl, .shadow-lg, .shadow-md, .shadow-sm { shadow: none !important; box-shadow: none !important; }
            #print-header { display: flex !important; }
            .bg-slate-50, .bg-slate-100 { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
            .bg-rose-50, .bg-indigo-50 { -webkit-print-color-adjust: exact; }
            button, .print-hidden, select, input { display: none !important; }
            .overflow-x-auto { overflow: visible !important; }
            body { background: white !important; }
            .grid { display: block !important; }
            .grid > div { margin-bottom: 2rem !important; break-inside: avoid; }
            #reasonsChart, #deptChart, #trendChart, #ratioChart { break-inside: avoid; min-height: 250px !important; }
        }
    </style>
</div>
