{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- MÜŞTERİ ŞİKAYETİ ÇÖZÜM LİDERİ DASHBOARD                              --}}
{{-- Yapı: Üst İstatistikler > Onay Bekleyenler > Sorumlu Olunan Squad'lar --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}

<div class="space-y-10 animate-fade-in-up mt-8 pb-4 relative clear-both block w-full">

    {{-- YENİ: TARİH FİLTRESİ --}}
    @if(request()->filled('start_date') || request()->filled('end_date'))
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-blue-700 font-medium">Şu an <strong>{{ request('start_date') ?? '...' }}</strong> ile <strong>{{ request('end_date') ?? '...' }}</strong> tarihleri arasındaki verilere bakıyorsunuz.</p>
                <a href="{{ route('dashboard') }}" class="ml-auto text-xs font-bold text-blue-600 hover:text-blue-800 underline flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Temizle</a>
            </div>
        </div>
    @endif

    <div class="bg-white/70 backdrop-blur-md rounded-2xl p-4 border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-end gap-3 w-full md:w-auto">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="text-sm rounded-lg border-gray-200 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="text-sm rounded-lg border-gray-200 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <button type="submit" class="px-4 py-2 h-[42px] bg-gray-800 hover:bg-gray-900 text-white text-sm font-bold rounded-lg transition-colors flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> Filtrele</button>
        </form>
    </div>
    


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- 0. BÖLÜM: HIZLI ERİŞİM LİNKLERİ                                  --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white/70 backdrop-blur-md rounded-2xl p-4 border border-gray-100 shadow-sm mb-6">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                HIZLI ERİŞİM
            </span>
            <a href="#onay-bekleyenler"
               class="relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100 hover:bg-purple-100 hover:shadow-sm transition-all group overflow-visible">
@if(count($stats['onay_bekleyen_sikayetler'] ?? []) > 0)
                   <span class="absolute -top-1 -right-1 flex h-3 w-3">
                       <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                       <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                   </span>
               @endif
                <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Onay Bekleyenler ({{ count($stats['onay_bekleyen_sikayetler'] ?? []) }})
                <svg class="w-3 h-3 text-purple-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#takim-listesi"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100 hover:bg-blue-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Müşteri Şikayetleri ({{ $stats['toplam_sikayet_sayisi'] ?? 0 }})
                <svg class="w-3 h-3 text-blue-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#musteri-temsilcileri"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100 hover:bg-emerald-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Müşteri & Temsilciler
                <svg class="w-3 h-3 text-emerald-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#iadeler"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-700 rounded-lg text-xs font-bold border border-rose-100 hover:bg-rose-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                İadeler
                <svg class="w-3 h-3 text-rose-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#ziyaretler"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100 hover:bg-indigo-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Ziyaretler
                <svg class="w-3 h-3 text-indigo-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
            <a href="#bekleyen-davetler"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-100 hover:bg-amber-100 hover:shadow-sm transition-all group">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Takım Davetleri
                <svg class="w-3 h-3 text-amber-400 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
            </a>
        </div>
    </div>


    @if($stats['has_teams'])
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- 1. BÖLÜM: YÖNETİCİ ÖZET KARTLARI (KPI) — Üst Şerit               --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            {{-- KPI 1: Squad / Liderlik Başlığı --}}
            <div class="group relative bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-5 shadow-lg overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                <div class="relative z-10 text-white">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-white/20 rounded-xl backdrop-blur-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-indigo-100 text-xs font-medium">Toplam Sorumlu Olunan Şikayet/Takım</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $stats['toplam_sikayet_sayisi'] ?? 0 }}</h3>
                    <div class="mt-3 flex items-center text-[10px] text-indigo-200 font-medium">
                        Çözüm Lideri Liderliği
                    </div>
                </div>
            </div>

            {{-- KPI 2: Onay Bekleyenler --}}
            <a href="#onay-bekleyenler" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-purple-50 group-hover:bg-purple-100 text-purple-600 transition-colors rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-gray-400 text-[10px] group-hover:text-purple-600 transition-colors font-medium">İncele →</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">{{ count($stats['onay_bekleyen_sikayetler'] ?? []) }}</h3>
                <p class="text-gray-500 text-xs mt-0.5">Onay Aşamasındaki Projeler</p>
                <div class="mt-3 w-full bg-gray-100 rounded-full h-1">
                    @php
                        $oranOnay = ($stats['toplam_sikayet_sayisi'] ?? 0) > 0 ? (count($stats['onay_bekleyen_sikayetler']) / $stats['toplam_sikayet_sayisi']) * 100 : 0;
                    @endphp
                    <div class="bg-purple-400 h-1 rounded-full transition-all duration-500" style="width: {{ $oranOnay }}%"></div>
                </div>
            </a>

            {{-- KPI 3: Çözülenler/Tamamlananlar --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-green-50 text-green-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['cozulen_sikayetler_count'] ?? 0 }}</h3>
                <p class="text-gray-500 text-xs mt-0.5">Tamamlanan Şikayetler</p>
                <p class="text-green-600 text-[11px] mt-2 font-medium flex items-center">
                    @if(($stats['toplam_sikayet_sayisi'] ?? 0) > 0)
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        %{{ round((($stats['cozulen_sikayetler_count'] ?? 0) / ($stats['toplam_sikayet_sayisi'] ?? 1)) * 100) }} Başarı
                    @else
                        %0 Başarı
                    @endif
                </p>
            </div>
            
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
@if(count($stats['onay_bekleyen_sikayetler']) > 0)
        <div id="onay-bekleyenler" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 scroll-mt-24 mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white">
                <h3 class="font-black text-purple-900 text-sm tracking-tight uppercase flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                    Onay Aşamasındaki Şikayet Projeleri
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($stats['onay_bekleyen_sikayetler']->values() as $index => $sikayet)
                    <div class="p-6 hover:bg-gray-50 transition-colors toggle-row {{ $index >= 5 ? 'hidden' : '' }}" data-index="{{ $index }}">
                        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4">
                            <div class="flex-1">
                                @if($sikayet->customer)
                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md text-[10px] font-black border border-indigo-100 uppercase tracking-tight mb-2">
                                        <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $sikayet->customer->firma_adi ?? $sikayet->customer->name ?? $sikayet->customer->ad_soyad ?? 'Müşteri Detayı Yok' }}
                                    </div>
                                @endif
                                <a href="{{ $sikayet->iaa_id ? route('proje.workspace.show', $sikayet->iaa_id) : route('admin.sikayetler.show', $sikayet->id) }}" class="text-sm font-bold text-gray-800 hover:text-purple-600 transition-colors mb-1 block">
                                    {{ $sikayet->musteri_sikayet_konusu }}
                                </a>
                                <div class="flex items-center gap-2 flex-wrap mt-2">
                                    {!! $sikayet->musteri_durum_badge !!}

                                    {{-- BÖLÜM BİLGİSİ --}}
                                    @if($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum)
                                        <span class="text-[11px] font-semibold text-gray-600 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-md flex items-center gap-1">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            {{ $sikayet->sikayetKategori->bolum->ad }}
                                        </span>
                                    @endif
                                    
                                    {{-- Kimin Onayı Bekleniyor Bilgisi --}}
                                    @php
                                        $gecenGunTutari = ceil($sikayet->updated_at->diffInHours(now()) / 24);
                                        $gecenGun = $gecenGunTutari == 0 ? 1 : $gecenGunTutari;
                                    @endphp
                                    <span class="text-[11px] font-bold text-gray-600 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-md flex items-center gap-1" title="Son İşlem: {{ $sikayet->updated_at->format('d.m.Y H:i') }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="opacity-70 font-normal">({{ $gecenGun }} gündür onayda bekliyor)</span>
                                    </span>
                                </div>
                            </div>
                            <div class="lg:text-right flex lg:flex-col items-center lg:items-end justify-between">
                                <a href="{{ $sikayet->iaa_id ? route('proje.workspace.show', $sikayet->iaa_id) : route('admin.sikayetler.show', $sikayet->id) }}" class="text-xs font-bold text-purple-600 hover:text-purple-800 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-100 hover:bg-purple-100 transition-colors">
                                    Detayları Görüntüle &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(count($stats['onay_bekleyen_sikayetler']) > 5)
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center">
                <button onclick="
                    let isExpanded = this.dataset.expanded === 'true';
                    this.closest('div[id]').querySelectorAll('.toggle-row').forEach(el => {
                        Number(el.dataset.index) >= 5 && (isExpanded ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    });
                    this.dataset.expanded = isExpanded ? 'false' : 'true';
                    this.querySelector('span').innerText = isExpanded ? 'Tümünü Göster ({{ count($stats['onay_bekleyen_sikayetler']) }})' : 'Gizle';
                    this.querySelector('svg').classList.toggle('rotate-180');
                " data-expanded="false" class="text-xs font-bold text-purple-600 hover:text-purple-800 inline-flex items-center gap-1">
                    <span>Tümünü Göster ({{ count($stats['onay_bekleyen_sikayetler']) }})</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            @endif
        </div>
    @endif

    <div id="takim-listesi" 
         x-data="{ 
            search: '{{ request('sikayet_arama', '') }}', 
            customer: '{{ request('sikayet_musteri_id', '') }}', 
            bolum: '{{ request('sikayet_bolum_id', '') }}', 
            status: '{{ request('sikayet_durum', '') }}',
            expanded: false,
            limit: 4
         }" 
         x-init="$watch('search, customer, bolum, status', () => { expanded = false; limit = 4; })"
         class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-10 scroll-mt-24">
            
            <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 p-2.5 rounded-xl text-blue-600 shadow-sm border border-blue-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 text-lg tracking-tight uppercase">AKTİF ŞİKAYETLER</h3>
                            <p class="text-xs text-gray-500 font-medium">Lideri olduğunuz ve işlemde olan şikayetlerin takımları ve ilerlemeleri.</p>
                        </div>
                    </div>
                </div>

                {{-- TABLO İÇİ FİLTRELEME (Alpine.js ile anlık) --}}
                <div class="mt-4 flex items-center gap-2 flex-wrap bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-1 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg> FİLTRELE:</span>
                    </div>
                    
                    {{-- ARAMA KUTUSU --}}
                    <div class="relative">
                        <input type="text" x-model="search"
                               placeholder="Başlık ile ara..." 
                               class="text-xs rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1.5 h-auto ps-8 w-40 md:w-56">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    {{-- MÜŞTERİ FİLTRESİ --}}
                    <select x-model="customer" class="text-xs rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1.5 h-auto max-w-[200px]">
                        <option value="">Tüm Müşteriler</option>
                        @foreach($stats['aktif_sikayet_musterileri'] as $mO)
                            <option value="{{ $mO->id }}">{{ $mO->firma_adi ?? $mO->name ?? $mO->ad_soyad }}</option>
                        @endforeach
                    </select>

                    <select x-model="bolum" class="text-xs rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1.5 h-auto">
                        <option value="">Tüm Bölümler</option>
                        @foreach(isset($tumBolumler) ? $tumBolumler : \App\Models\Bolum::orderBy('ad')->get() as $bolumO)
                            <option value="{{ $bolumO->id }}">{{ $bolumO->ad }}</option>
                        @endforeach
                    </select>

                    <select x-model="status" class="text-xs rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1.5 h-auto">
                        <option value="">Tüm Durumlar</option>
                        <option value="Yeni">Yeni</option>
                        <option value="İşlemde">İşlemde</option>
                        <option value="Atandı">Atandı</option>
                        <option value="İnceleniyor">İnceleniyor</option>
                        <option value="Devam Ediyor">Devam Ediyor</option>
                    </select>
                    
                    <button type="button" @click="search = ''; customer = ''; bolum = ''; status = '';" class="px-3 py-1.5 bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs font-bold rounded-lg border border-gray-200 transition-colors">Sıfırla</button>
                    <p class="ml-auto text-[10px] text-gray-400 font-bold hidden md:block">Sayfa yenilemeden anlık filtrelenir.</p>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start" id="aktif-sikayet-cards">
                    @forelse($stats['aktif_sikayetler_projeler']->values() as $index => $sikayet)
                        @php
                            $workflow = $sikayet->iaaProjesi ? $sikayet->iaaProjesi->ilerleme_verisi : null;
                            $squad = $sikayet->cozumTakimi;
                            
                            // Arama/Filtreleme için JSON dostu veri
                            $cardData = [
                                't' => mb_strtolower($sikayet->musteri_sikayet_konusu),
                                'c' => (string)$sikayet->customer_id,
                                'b' => (string)($sikayet->sikayetKategori->bolum_id ?? ''),
                                's' => (string)$sikayet->musteri_durum,
                                'i' => $index
                            ];
                        @endphp
                        <div 
                            class="rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md bg-white transition-all duration-300 flex flex-col justify-between overflow-hidden sikayet-card"
                            x-data="{ 
                                info: {{ json_encode($cardData) }},
                                isVisible() {
                                    const matchSearch = this.search === '' || this.info.t.includes(this.search.toLowerCase());
                                    const matchCustomer = this.customer === '' || this.info.c === this.customer;
                                    const matchBolum = this.bolum === '' || this.info.b === this.bolum;
                                    const matchStatus = this.status === '' || this.info.s === this.status;
                                    return matchSearch && matchCustomer && matchBolum && matchStatus;
                                }
                            }"
                            x-show="isVisible()"
                            {{-- Limit Kontrolü: Filtreye uyan elemanlar arasından limit kadar göster (Eğer expanded değilse) --}}
                            :class="isVisible() ? 'matched-complaint' : ''"
                            style="display: none;"
                        >
                            
                            {{-- Kart Üst: Başlık --}}
                            <div class="px-5 pt-5 pb-4 border-b border-gray-50">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <a href="{{ $sikayet->iaa_id ? route('proje.workspace.show', $sikayet->iaa_id) : route('admin.sikayetler.show', $sikayet->id) }}" class="text-sm font-bold text-gray-800 hover:text-blue-600 transition-colors leading-snug flex-1">
                                        {{ Str::limit($sikayet->musteri_sikayet_konusu, 70) }}
                                    </a>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap mb-3">
                                    {!! $sikayet->musteri_durum_badge !!}
                                    <a href="{{ route('admin.sikayetler.index', ['filtreKategori' => [$sikayet->sikayet_kategorisi_id]]) }}" class="text-[10px] font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 hover:text-gray-700 transition-colors px-2.5 py-1 rounded-md border border-gray-200 flex items-center gap-1">
                                        {{ $sikayet->sikayetKategori->ad ?? 'Kategori Yok' }}
                                    </a>
                                    <span class="{{ $sikayet->oncelik_badge_class }} px-2 py-0.5 rounded text-[10px] font-bold border border-current opacity-80">Öncelik: {{ mb_strtoupper($sikayet->musteri_oncelik) }}</span>
                                </div>
                                @if($sikayet->customer)
                                    <p class="text-[11px] font-bold text-blue-700 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $sikayet->customer->firma_adi ?? $sikayet->customer->name ?? $sikayet->customer->ad_soyad ?? 'N/A' }}
                                    </p>
                                @endif
                            </div>

                            {{-- Kart Orta: SQUAD Üyeleri --}}
                            <div class="px-5 py-4 bg-gray-50 flex-1">
                                <h6 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">TAKIM: ({{ $squad ? $squad->ad : 'Atanmadı' }}) Üyeleri</h6>
                                @if($squad)
                                    <div class="space-y-3">
                                        @if($squad->uyeler->count() > 0)
                                            <div class="flex -space-x-2 overflow-hidden">
                                                @foreach($squad->uyeler->take(5) as $uye)
                                                    <a href="{{ route('profile.show', $uye->id) }}" title="{{ $uye->name }} - {{ $uye->pivot->gorev_tanimi ?? 'Üye' }}" class="inline-block h-8 w-8 rounded-full ring-2 ring-white hover:z-10 focus:outline-none focus:z-10">
                                                        <img class="h-full w-full rounded-full object-cover" src="{{ $uye->profile_photo_url }}" alt="{{ $uye->name }}">
                                                    </a>
                                                @endforeach
                                                @if($squad->uyeler->count() > 5)
                                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white bg-gray-200 text-xs font-medium text-gray-500">
                                                        +{{ $squad->uyeler->count() - 5 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-500 italic">Takımda henüz üye yok.</p>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-xs text-gray-500 italic">Bu şikayet için özel bir takım atanmamış.</p>
                                @endif
                            </div>

                            {{-- Kart Alt: İş Akışı İlerleme Çubuğu --}}
                            @if($workflow && $workflow['toplam'] > 0)
                                @php
                                    $durumRenk = $sikayet->iaaProjesi ? $sikayet->iaaProjesi->durum_rengi : 'blue';
                                    $progressBg = match($durumRenk) {
                                        'purple' => 'bg-purple-50',
                                        'pink' => 'bg-pink-50',
                                        'orange' => 'bg-orange-50',
                                        'emerald', 'green' => 'bg-emerald-50',
                                        'red' => 'bg-red-50',
                                        'gray' => 'bg-gray-50',
                                        'indigo' => 'bg-indigo-50',
                                        default => 'bg-blue-50',
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
                                        'gray' => 'text-gray-700',
                                        'indigo' => 'text-indigo-700',
                                        default => 'text-blue-700',
                                    };
                                @endphp
                                <div class="px-5 py-3 border-t border-gray-100 {{ $progressBg }}">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[10px] font-bold {{ $progressText }} truncate pr-2 uppercase">Aşama: {{ $sikayet->iaaProjesi->aktif_asama_metni ?? 'Belirlenmedi' }}</span>
                                        <span class="text-[10px] font-black {{ $progressText }}">{{ round($workflow['yuzde']) }}%</span>
                                    </div>
                                    <div class="w-full bg-white/50 rounded-full h-1.5">
                                        <div class="{{ $progressBarFill }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $workflow['yuzde'] }}%"></div>
                                    </div>
                                </div>
                            @else
                                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
                                    <p class="text-[10px] text-gray-500 font-medium italic">Bu şikayet için proje iş akışı henüz başlamadı.</p>
                                </div>
                            @endif

                        </div>
                    @empty
                        <div class="col-span-1 xl:col-span-2 bg-gray-50 border border-gray-200 border-dashed rounded-xl p-8 text-center flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h4 class="text-gray-900 font-bold mb-1">Takıma Atanmış Şikayet Yok</h4>
                            <p class="text-sm text-gray-500">Şu anda lideri olduğunuz çözüm takımı için aktif bir şikayet bulunmamaktadır.</p>
                        </div>
                    @endforelse
                </div>
                
                {{-- Boş Sonuç Uyarısı (Alpine.js ile) --}}
                <div x-show="document.querySelectorAll('.matched-complaint').length === 0" class="bg-gray-50 border border-gray-200 border-dashed rounded-xl p-8 text-center flex flex-col items-center justify-center mt-6">
                    <p class="text-sm text-gray-500">Aradığınız kriterlere uygun şikayet bulunamadı.</p>
                </div>
            </div>

            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center" x-show="document.querySelectorAll('.matched-complaint').length > limit">
                <button type="button" @click="expanded = !expanded; limit = expanded ? 999 : 4" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
                    <span x-text="expanded ? 'Gizle' : 'Tümünü Göster (' + document.querySelectorAll('.matched-complaint').length + ')' "></span>
                    <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>

<div id="musteri-temsilcileri" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8 scroll-mt-24">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white flex justify-between items-center">
                <h3 class="font-black text-emerald-900 text-sm tracking-tight uppercase flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Sorumlu Olunan Müşteriler ({{ isset($stats['sorumlu_musteriler']) ? count($stats['sorumlu_musteriler']) : 0 }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[10px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3 font-medium">Müşteri Firması & Şikayetler</th>
                            <th class="px-6 py-3 font-medium">İletişim & Adres</th>
                            <th class="px-6 py-3 font-medium">Firma Yetkilileri</th>
                            <th class="px-6 py-3 font-medium text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stats['sorumlu_musteriler']->values() as $index => $musteri)
                            @php
                                $aktifler = isset($stats['liderin_sikayetleri']) ? $stats['liderin_sikayetleri']->where('customer_id', $musteri->id)->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'Atandı', 'İnceleniyor', 'Devam Ediyor', 'Revize Ediliyor'])->count() : 0;
                                $cozulenler = isset($stats['liderin_sikayetleri']) ? $stats['liderin_sikayetleri']->where('customer_id', $musteri->id)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->count() : 0;
                            @endphp
                            <tr class="hover:bg-emerald-50/30 transition-colors toggle-row {{ $index >= 5 ? 'hidden' : '' }}" data-index="{{ $index }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($musteri->logo_path)
                                            <img src="{{ asset('storage/' . $musteri->logo_path) }}" class="w-10 h-10 rounded-lg object-contain bg-white border border-gray-100 shadow-sm" alt="{{ $musteri->firma_adi ?? $musteri->name }}">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($musteri->firma_adi ?? $musteri->name ?? '-') }}&color=047857&background=d1fae5&rounded=true&bold=true" class="w-10 h-10 rounded-lg border border-gray-100 shadow-sm" alt="">
                                        @endif
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('musteri.profil.show', $musteri->id) }}" class="font-bold text-emerald-800 hover:text-emerald-500 transition-colors underline decoration-emerald-200 underline-offset-2">{{ $musteri->name ?? $musteri->firma_adi ?? '-' }}</a>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                                @if($aktifler > 0)
                                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">{{ $aktifler }} Aktif Şikayet</span>
                                                @endif
                                                @if($cozulenler > 0)
                                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">{{ $cozulenler }} Çözümlenmiş</span>
                                                @endif
                                                @if($aktifler == 0 && $cozulenler == 0)
                                                    <span class="text-[10px] text-gray-400">Şikayet Yok</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700 flex items-center gap-1"><svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> {{ $musteri->phone ?? $musteri->telefon ?? 'Telefon Yok' }}</div>
                                    <div class="text-xs text-gray-700 mt-1 flex items-center gap-1"><svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg> {{ $musteri->email ?? $musteri->eposta ?? 'E-posta Yok' }}</div>
                                    <div class="text-[10px] text-gray-500 mt-1 max-w-[200px] truncate" title="{{ $musteri->address ?? $musteri->adres ?? '-' }}">{{ $musteri->address ?? $musteri->adres ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($musteri->users && $musteri->users->count() > 0)
                                        <div class="flex flex-col gap-2">
                                            @foreach($musteri->users as $yetkili)
                                                <div class="group relative flex items-center gap-2 cursor-help p-1 -ml-1 hover:bg-gray-50 rounded">
                                                    <img src="{{ $yetkili->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($yetkili->name) }}" class="w-6 h-6 rounded-full border border-gray-200" alt="">
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-semibold text-gray-800">{{ $yetkili->name }} <span class="text-[10px] text-gray-500 font-normal">({{ $yetkili->gorev_tanimi ?? 'Yetkili' }})</span></span>
                                                    </div>
                                                    <!-- Hover Tooltip -->
                                                    <div class="absolute left-0 bottom-full mb-1 hidden group-hover:block w-56 bg-gray-900 border border-gray-700 shadow-xl rounded-lg p-3 z-50">
                                                        <div class="mb-2 pb-2 border-b border-gray-700">
                                                            <p class="text-white font-bold text-xs">{{ $yetkili->name }}</p>
                                                            <p class="text-gray-400 text-[10px]">{{ $yetkili->gorev_tanimi ?? 'Firma Yetkilisi' }}</p>
                                                        </div>
                                                        <div class="flex flex-col gap-1.5">
                                                            <div class="flex items-center gap-2 text-gray-300 text-[11px]">
                                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                                <span>{{ $yetkili->email ?? 'Belirtilmemiş' }}</span>
                                                            </div>
                                                            <div class="flex items-center gap-2 text-gray-300 text-[11px]">
                                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                                <span>{{ $yetkili->phone ?? $yetkili->telefon ?? 'Belirtilmemiş' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Yetkili ataması yok</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('musteri.profil.show', $musteri->id) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 hover:bg-emerald-100 transition-colors">Profili Aç</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        <p class="text-sm">Şu anda sorumlu olduğunuz kayıtlı müşteri bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($stats['sorumlu_musteriler']) && count($stats['sorumlu_musteriler']) > 5)
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center">
                <button onclick="
                    let isExpanded = this.dataset.expanded === 'true';
                    this.closest('div[id]').querySelectorAll('.toggle-row').forEach(el => {
                        Number(el.dataset.index) >= 5 && (isExpanded ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    });
                    this.dataset.expanded = isExpanded ? 'false' : 'true';
                    this.querySelector('span').innerText = isExpanded ? 'Tümünü Göster ({{ count($stats['sorumlu_musteriler']) }})' : 'Gizle';
                    this.querySelector('svg').classList.toggle('rotate-180');
                " data-expanded="false" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 inline-flex items-center gap-1">
                    <span>Tümünü Göster ({{ count($stats['sorumlu_musteriler']) }})</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            @endif
        </div>

<div id="bekleyen-davetler" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8 scroll-mt-24">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white flex justify-between items-center">
                <h3 class="font-black text-amber-900 text-sm tracking-tight uppercase flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    TAKIM DAVETLERİ
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[10px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3 font-medium">Şikayet Projesi</th>
                            <th class="px-6 py-3 font-medium">Davet Edilen Takım</th>
                            <th class="px-6 py-3 font-medium">Davetli Kullanıcı</th>
                            <th class="px-6 py-3 font-medium">Geçen Süre</th>
                            <th class="px-6 py-3 font-medium text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $hasAnyInvites = false;
                            $inviteCount = 0;
                        @endphp
                        @foreach($stats['aktif_sikayetler_projeler'] as $sikayet)
                            @if($sikayet->cozumTakimi && $sikayet->cozumTakimi->davetiyeler && $sikayet->cozumTakimi->davetiyeler->count() > 0)
                                @php $hasAnyInvites = true; @endphp
                                @foreach($sikayet->cozumTakimi->davetiyeler as $davet)
                                    @php $inviteCount++; @endphp
                                    <tr class="hover:bg-amber-50/30 transition-colors toggle-row {{ $inviteCount > 5 ? 'hidden' : '' }}" data-index="{{ $inviteCount }}">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">{{ Str::limit($sikayet->musteri_sikayet_konusu, 40) }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-gray-700 font-semibold">{{ $sikayet->cozumTakimi->ad }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-[11px] text-gray-800 font-bold bg-gray-100 px-2 py-1 rounded-md inline-block">{{ $davet->davetEdilen->name ?? 'Bilinmiyor' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-[11px] text-gray-500">{{ $davet->created_at->diffForHumans() }} gönderildi</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-200">Yanıt Bekliyor</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                        
                        @if(!$hasAnyInvites)
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <p class="text-sm">Şu anda yanıt bekleyen bir takım davetiyeniz bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($inviteCount) && $inviteCount > 5)
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center">
                <button onclick="
                    let isExpanded = this.dataset.expanded === 'true';
                    this.closest('div[id]').querySelectorAll('.toggle-row').forEach(el => {
                        Number(el.dataset.index) > 5 && (isExpanded ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    });
                    this.dataset.expanded = isExpanded ? 'false' : 'true';
                    this.querySelector('span').innerText = isExpanded ? 'Tümünü Göster ({{ $inviteCount }})' : 'Gizle';
                    this.querySelector('svg').classList.toggle('rotate-180');
                " data-expanded="false" class="text-xs font-bold text-amber-600 hover:text-amber-800 inline-flex items-center gap-1">
                    <span>Tümünü Göster ({{ $inviteCount }})</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            @endif
        </div>

<div id="iadeler" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8 scroll-mt-24">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-50 to-white flex justify-between items-center">
                <h3 class="font-black text-rose-900 text-sm tracking-tight uppercase flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Sorumlu Olunan Şikayetlere Bağlı İadeler
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[10px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3 font-medium">Şikayet & Müşteri</th>
                            <th class="px-6 py-3 font-medium">İade Tarihi</th>
                            <th class="px-6 py-3 font-medium">Miktar</th>
                            <th class="px-6 py-3 font-medium">Sebep / Tür</th>
                            <th class="px-6 py-3 font-medium text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse(isset($stats['iadeler']) ? $stats['iadeler'] : [] as $index => $iade)
                            @php
                                $sikayetUrl = $iade->musteriSikayeti->iaa_id 
                                    ? route('proje.workspace.show', $iade->musteriSikayeti->iaa_id) 
                                    : route('admin.sikayetler.show', $iade->musteri_sikayeti_id);
                            @endphp
                            <tr class="hover:bg-rose-50/30 transition-colors toggle-row {{ $index >= 5 ? 'hidden' : '' }}" data-index="{{ $index }}">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800"><a href="{{ $sikayetUrl }}" class="hover:text-rose-600">{{ Str::limit($iade->musteriSikayeti->musteri_sikayet_konusu ?? 'Bilinmiyor', 30) }}</a></div>
                                    <div class="text-[11px] text-gray-500">{{ $iade->musteriSikayeti->customer->firma_adi ?? $iade->musteriSikayeti->customer->name ?? $iade->musteriSikayeti->customer->ad_soyad ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700 font-semibold">{{ $iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-800 font-bold">{{ $iade->miktar }} {{ $iade->birim }}</div>
                                    <div class="text-[10px] text-gray-500">Toplam PM: {{ $iade->toplam_parti_miktari ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700">{{ Str::limit($iade->iade_sebebi ?? '-', 40) }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $iade->urun_turu ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ $sikayetUrl }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 hover:bg-rose-100 transition-colors">İncele</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        <p class="text-sm">Şu anda onaylanmış şikayetlere ait iade bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($stats['iadeler']) && count($stats['iadeler']) > 5)
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center">
                <button onclick="
                    let isExpanded = this.dataset.expanded === 'true';
                    this.closest('div[id]').querySelectorAll('.toggle-row').forEach(el => {
                        Number(el.dataset.index) >= 5 && (isExpanded ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    });
                    this.dataset.expanded = isExpanded ? 'false' : 'true';
                    this.querySelector('span').innerText = isExpanded ? 'Tümünü Göster ({{ count($stats['iadeler']) }})' : 'Gizle';
                    this.querySelector('svg').classList.toggle('rotate-180');
                " data-expanded="false" class="text-xs font-bold text-rose-600 hover:text-rose-800 inline-flex items-center gap-1">
                    <span>Tümünü Göster ({{ count($stats['iadeler']) }})</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            @endif
        </div>

<div id="ziyaretler" class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-0 scroll-mt-24">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white flex justify-between items-center">
                <h3 class="font-black text-indigo-900 text-sm tracking-tight uppercase flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Sorumlu Olunan Şikayetlere Bağlı Ziyaretler
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-[10px] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-3 font-medium">Ziyaret Edilen Proje/Firma</th>
                            <th class="px-6 py-3 font-medium">Ziyaretçi</th>
                            <th class="px-6 py-3 font-medium">Ziyaret Tarihi</th>
                            <th class="px-6 py-3 font-medium">Sebep / Durum</th>
                            <th class="px-6 py-3 font-medium text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse(isset($stats['ziyaretler']) ? $stats['ziyaretler'] : [] as $index => $ziyaret)
                            <tr class="hover:bg-indigo-50/30 transition-colors toggle-row {{ $index >= 5 ? 'hidden' : '' }}" data-index="{{ $index }}">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">
                                        <a href="{{ route('proje.workspace.show', $ziyaret->iaa_id) }}" class="hover:text-indigo-600 transition-colors">
                                            {{ $ziyaret->iaa?->baslik ?? '-' }}
                                        </a>
                                    </div>
                                    <div class="text-[11px] text-gray-500">
                                        {{ $ziyaret->iaa?->musteriSikayeti?->customer?->name ?? $ziyaret->iaa?->musteriSikayeti?->musteri_adi ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($ziyaret->visitor)
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $ziyaret->visitor->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($ziyaret->visitor->name) }}" class="w-6 h-6 rounded-full" alt="">
                                            <span class="text-xs font-semibold text-gray-700">{{ $ziyaret->visitor->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">{{ $ziyaret->visitor_name ?? 'Atanmamış' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700 font-semibold">{{ $ziyaret->visit_date ? $ziyaret->visit_date->format('d.m.Y H:i') : '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-700">{{ Str::limit($ziyaret->visit_reason ?? '-', 30) }}</div>
                                    <div class="text-[10px] font-bold mt-1">
                                        @if($ziyaret->status == 'completed')
                                            <span class="text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Tamamlandı</span>
                                        @elseif($ziyaret->status == 'approved')
                                            <span class="text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">Planlandı (Onaylandı)</span>
                                        @elseif(str_contains($ziyaret->status, 'pending'))
                                            <span class="text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">Onay Bekliyor</span>
                                        @else
                                            <span class="text-gray-600 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">{{ $ziyaret->status }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('proje.workspace.show', $ziyaret->iaa_id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 hover:bg-indigo-100 transition-colors">Projeye Git</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        <p class="text-sm">Şu anda planlanmış veya tamamlanmış ziyaret bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($stats['ziyaretler']) && count($stats['ziyaretler']) > 5)
            <div class="bg-gray-50 border-t border-gray-100 p-3 text-center">
                <button onclick="
                    let isExpanded = this.dataset.expanded === 'true';
                    this.closest('div[id]').querySelectorAll('.toggle-row').forEach(el => {
                        Number(el.dataset.index) >= 5 && (isExpanded ? el.classList.add('hidden') : el.classList.remove('hidden'));
                    });
                    this.dataset.expanded = isExpanded ? 'false' : 'true';
                    this.querySelector('span').innerText = isExpanded ? 'Tümünü Göster ({{ count($stats['ziyaretler']) }})' : 'Gizle';
                    this.querySelector('svg').classList.toggle('rotate-180');
                " data-expanded="false" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                    <span>Tümünü Göster ({{ count($stats['ziyaretler']) }})</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            @endif
        </div>

        {{-- Bölüm Sonu: Takım Listesi --}}
    @else
        <div class="bg-gradient-to-br from-yellow-50 to-orange-100 border border-yellow-200 rounded-2xl shadow-lg overflow-hidden p-8 flex flex-col items-center justify-center text-center mt-8">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-yellow-900 mb-2">Henüz Bir Takıma Lider Değilsiniz</h3>
            <p class="text-yellow-700">Şu anda herhangi bir şikayet için özel olarak oluşturulmuş "Çözüm Takımı" lideri olarak atanmamışsınız.</p>
        </div>
    @endif
</div>