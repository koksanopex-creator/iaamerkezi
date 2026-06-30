@php
    $user = Auth::user();
    
    // Aktif Müşteri Nesnesini Belirle
    $activeCustomer = null;
    if (isset($userCustomers) && isset($activeCustomerId)) {
        $activeCustomer = $userCustomers->where('id', $activeCustomerId)->first();
    } else {
        $activeCustomer = $user->customer;
    }

    // YILLIK İSTATİSTİKLER (Aktif Müşteriye Göre)
    $yillikIstatistikler = collect();
    if($activeCustomer) {
        $yillikIstatistikler = $activeCustomer->sikayetler()
            ->selectRaw('YEAR(created_at) as yil, count(*) as toplam')
            ->groupBy('yil')
            ->orderBy('yil', 'desc')
            ->get();
    }

    // FİRMA YETKİLİLERİ (Aktif Müşteriye Göre)
    $digerYetkililer = collect();
    if($activeCustomer) {
        $digerYetkililer = $activeCustomer->representatives->where('id', '!=', $user->id);
    }
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6">

    {{-- FLASH MESAJLARI (BAŞARI/HATA) --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in">
            <div class="bg-emerald-100 p-2 rounded-full text-emerald-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in">
            <div class="bg-red-100 p-2 rounded-full text-red-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- 1. ÜST BİLGİ KARTI --}}
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4 relative overflow-hidden">
        {{-- Arkaplan Süsü --}}
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 blur-xl pointer-events-none"></div>

        <div class="flex items-center gap-4 relative z-10 w-full md:w-auto">
            {{-- LOGO ALANI --}}
            <div class="h-14 w-14 flex-shrink-0 bg-white rounded-lg border border-indigo-100 shadow-sm flex items-center justify-center overflow-hidden p-1 group transition-transform hover:scale-105">
                @if($activeCustomer && $activeCustomer->logo_path)
                    <img src="{{ asset('storage/' . $activeCustomer->logo_path) }}" 
                         alt="{{ $activeCustomer->name }}" 
                         class="h-full w-full object-contain">
                @else
                    <div class="h-full w-full bg-indigo-50 rounded-md flex items-center justify-center text-indigo-600 font-black text-xl">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
            </div>

            {{-- BİLGİ ALANI --}}
            <div class="flex flex-col">
                @php
                    $activeTitle = $activeCustomer ? ($activeCustomer->users->find($user->id)?->pivot?->unvan ?? $user->unvan) : $user->unvan;
                @endphp
                <h2 class="text-lg font-bold text-gray-900 leading-tight">Sn. {{ $user->name }} <span class="text-xs text-indigo-600/70 font-medium">({{ $activeTitle ?? 'Yetkili' }})</span></h2>
                
                @if($activeCustomer)
                    <div class="flex flex-wrap items-center gap-1 sm:gap-2 mt-0.5">
                        <span class="text-indigo-700 font-bold text-xs">{{ $activeCustomer->name }}</span>
                        <span class="hidden sm:inline text-gray-300 text-xs">|</span>
                        <span class="text-gray-500 text-[10px] font-medium bg-gray-100 px-1.5 py-0.5 rounded">Müşteri Paneli</span>
                    </div>
                @else
                    <p class="text-gray-500 text-xs mt-0.5">Firma Yetkilisi | Müşteri Paneli</p>
                @endif

                <div class="flex items-center mt-1 text-[10px] text-gray-400">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Son Giriş: {{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->format('d.m.Y H:i') : 'İlk Giriş' }}
                </div>
            </div>
        </div>

        {{-- BUTONLAR --}}
        <div class="flex gap-2 w-full md:w-auto relative z-10">
            @php
                $pendingRemindersCount = \App\Models\SikayetHatirlatma::whereHas('musteriSikayeti', function($q) use ($activeCustomer) {
                    $q->where('customer_id', $activeCustomer ? $activeCustomer->id : 0);
                })->whereIn('durum', ['bilgi_girisi_bekleniyor', 'bilgi_girildi'])->count();
            @endphp

            <a href="{{ route('iaa.hatirlatmalarim.index') }}" class="flex-1 md:flex-none justify-center px-4 py-2 bg-white border border-gray-200 hover:border-red-300 hover:bg-red-50 text-gray-700 hover:text-red-700 text-xs font-bold rounded-lg transition-all shadow-sm hover:shadow flex items-center gap-2 transform hover:-translate-y-0.5 relative">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                Hatırlatmalarım
                @if($pendingRemindersCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600 text-[9px] text-white items-center justify-center font-black">{{ $pendingRemindersCount }}</span>
                    </span>
                @endif
            </a>

            @if($activeCustomer)
                <a href="{{ route('musteri.profil.show', $activeCustomer->id) }}" class="flex-1 md:flex-none justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm hover:shadow flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Firma Profili
                </a>
            @endif
            
            <a href="{{ route('iaa.sikayetler.create', ['musteri_id' => $activeCustomer ? $activeCustomer->id : null]) }}" 
               class="flex-1 md:flex-none justify-center px-4 py-2 bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 text-xs font-bold rounded-lg transition-all shadow-sm hover:shadow flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Yeni Şikayet
            </a>
        </div>
    </div>

    {{-- ÇOKLU FİRMA GEÇİŞ PANELİ (YENİ - DAHA BELİRGİN) --}}
    @if(isset($userCustomers) && $userCustomers->count() > 1)
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-4 border border-indigo-100 shadow-sm animate-fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white rounded-lg shadow-sm">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-indigo-900 uppercase tracking-wider">Yetkili Olduğunuz Firmalar</p>
                        <p class="text-[10px] text-indigo-600 font-medium">Panel verilerini görmek istediğiniz firmayı seçin</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($userCustomers as $customer)
                        @if($customer->id == $activeCustomerId)
                            <div class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg shadow-md border border-indigo-700 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                {{ $customer->name }}
                            </div>
                        @else
                            <form action="{{ route('dashboard.switch-customer') }}" method="POST">
                                @csrf
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <button type="submit" class="px-4 py-2 bg-white hover:bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-100 transition-all hover:shadow-sm transform hover:-translate-y-0.5 flex items-center gap-2">
                                    {{ $customer->name }}
                                </button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- TARİH FİLTRESİ --}}
    <form method="GET" action="{{ route('dashboard') }}" class="bg-white rounded-xl p-3 shadow-sm border border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-sm text-gray-600 font-bold">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Tarih Aralığı:
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <input type="date" name="start_date" value="{{ request('start_date') }}" title="Başlangıç Tarihi" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 hover:border-gray-300 transition-colors shadow-sm cursor-pointer p-2">
            <span class="text-gray-400 text-xs font-bold">-</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" title="Bitiş Tarihi" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 hover:border-gray-300 transition-colors shadow-sm cursor-pointer p-2">
            <button type="submit" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold text-xs rounded-lg transition-colors border border-indigo-100 flex items-center gap-1 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Uygula
            </button>
            @if(request('start_date') || request('end_date'))
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 font-bold text-xs rounded-lg transition-colors border border-red-100 flex items-center gap-1 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Temizle
                </a>
            @endif
        </div>
    </form>

    {{-- 2. İSTATİSTİKLER VE GRAFİK --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        {{-- Sol Taraf: Sayısal Veriler --}}
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
            
            {{-- Toplam Şikayet --}}
            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Toplam Kayıt</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $stats['toplam_sikayet'] ?? 0 }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Aktif Süreç --}}
            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1 bg-orange-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">İşlemdekiler</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $stats['aktif_sikayet'] ?? 0 }}</h3>
                        <p class="text-[9px] text-orange-600 mt-0.5 font-bold bg-orange-50 inline-block px-1.5 py-0.5 rounded">Çözüm Bekleyen</p>
                    </div>
                    <div class="p-2 bg-orange-50 rounded-lg text-orange-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Çözülen --}}
            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Çözümlenen</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $stats['cozulen_sikayet'] ?? 0 }}</h3>
                    </div>
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Ortalama Hız --}}
            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 h-full w-1 bg-indigo-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ort. Çözüm Hızı</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-0.5">{{ $stats['ortalama_sure'] ?? 0 }} <span class="text-xs font-normal text-gray-400">Gün</span></h3>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sağ Taraf: Grafik ve Yıllık Özet --}}
        <div class="flex flex-col gap-3">
            {{-- Grafik --}}
            <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center justify-center flex-1">
                <h4 class="text-[10px] font-bold text-gray-500 uppercase w-full text-left mb-2">Durum Dağılımı</h4>
                <div class="h-32 w-full flex justify-center relative">
                    <canvas id="sikayetChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="text-xs font-bold text-gray-400 opacity-50">Özet</span>
                    </div>
                </div>
            </div>

            {{-- YILLIK İSTATİSTİKLER --}}
            @if($yillikIstatistikler->isNotEmpty())
                <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
                    <h4 class="text-[10px] font-bold text-gray-500 uppercase mb-2 border-b border-gray-100 pb-1">Yıllık Kayıtlar</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($yillikIstatistikler as $stat)
                            <div class="flex items-center justify-between px-2 py-1.5 bg-gray-50 rounded-lg border border-gray-100 flex-1 min-w-[70px]">
                                <span class="text-[10px] font-bold text-gray-600">{{ $stat->yil }}</span>
                                <span class="text-xs font-black text-indigo-600">{{ $stat->toplam }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 3. FİRMA YETKİLİLERİ --}}
    @if($digerYetkililer->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800 text-sm">Firma Yetkilileri</h3>
                <p class="text-[10px] text-gray-500">Sizin dışınızda firmayı temsil eden yetkili kişiler.</p>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($digerYetkililer as $yetkili)
                    <div class="flex items-start gap-3 p-2.5 rounded-lg border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-sm hover:border-indigo-100 transition-all">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-bold flex-shrink-0">
                            {{ substr($yetkili->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="text-xs font-bold text-gray-900 truncate" title="{{ $yetkili->name }}">{{ $yetkili->name }}</h4>
                            <p class="text-[10px] text-gray-500 truncate">{{ $yetkili->unvan ?? 'Yetkili' }}</p>
                            <div class="mt-0.5 flex flex-col gap-0.5">
                                <a href="mailto:{{ $yetkili->email }}" class="text-[9px] text-indigo-600 hover:underline truncate">{{ $yetkili->email }}</a>
                                @if($yetkili->telefon)
                                    <span class="text-[9px] text-gray-400">{{ $yetkili->telefon }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- 4. DETAYLI LİSTE TABLOSU --}}
    @if(isset($stats['son_sikayetler']) && $stats['son_sikayetler']->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Aktif Süreç Takibi</h3>
                    <p class="text-[10px] text-gray-500">Çözüm takımlarının üzerinde çalıştığı son kayıtlar.</p>
                </div>
                
                @if(request('start_date') || request('end_date'))
                    <div class="flex items-center gap-2 bg-amber-50 px-4 py-2 rounded-lg border-2 border-amber-400 shadow-md relative overflow-hidden">
                        <div class="absolute inset-0 bg-amber-400 opacity-10 animate-pulse pointer-events-none"></div>
                        <span class="flex relative h-3 w-3 flex-shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-500 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-600"></span>
                        </span>
                        <div class="relative z-10 flex flex-col">
                            <span class="text-[11px] font-black text-amber-800 uppercase tracking-wide">
                                DİKKAT: FİLTRELİ GÖRÜNÜMDESİNİZ
                            </span>
                            <span class="text-[10px] font-bold text-amber-700">
                                Sadece <span class="bg-amber-200 px-1 rounded text-amber-900">{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d.m.Y') : 'Başlangıç' }} - {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d.m.Y') : 'Günümüz' }}</span> aralığındaki kayıtlar listeleniyor.
                            </span>
                        </div>
                    </div>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Oluşturan</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Konu & Kategori</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Sorumlu Takım</th>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                            <th class="px-5 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tarihler</th>
                            <th class="px-5 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stats['son_sikayetler'] as $sikayet)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                
                                {{-- OLUŞTURAN (GÜNCELLENEN ALAN) --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @php
                                        // Yaratıcıyı belirle (user veya olusturanKurulUyesi)
                                        $creator = $sikayet->user ?? $sikayet->olusturanKurulUyesi;
                                        $creatorName = $creator ? $creator->name : 'Misafir';
                                        
                                        // Ünvan Belirleme Mantığı
                                        $creatorTitle = '';
                                        $creatorColorClass = 'text-gray-500'; // Varsayılan renk

                                        if($creator) {
                                            // 1. Durum: Firma Yetkilisi mi? (customer_id varsa)
                                            if(!empty($creator->customer_id)) {
                                                $creatorTitle = 'Firma Yetkilisi';
                                                $creatorColorClass = 'text-gray-500';
                                            } else {
                                                // 2. Durum: Personel/Kurul Üyesi (customer_id yoksa)
                                                // Rolünü çek, yoksa 'Personel' yaz
                                                $roleName = $creator->roles->isNotEmpty() ? $creator->roles->first()->name : 'Personel';
                                                $creatorTitle = $roleName;
                                                $creatorColorClass = 'text-indigo-400';
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-900">{{ $creatorName }}</span>
                                        <span class="text-[9px] {{ $creatorColorClass }}">{{ $creatorTitle }}</span>
                                    </div>
                                </td>

                                {{-- Konu --}}
                                <td class="px-5 py-3">
                                    <div class="text-xs font-bold text-gray-900 line-clamp-1 max-w-[120px]" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                        {{ Str::limit($sikayet->musteri_sikayet_konusu, 40) }}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <div class="text-[10px] text-gray-500">{{ $sikayet->sikayetKategori->ad ?? 'Genel' }}</div>
                                        
                                        @if(\App\Models\SikayetIadesi::where('musteri_sikayeti_id', $sikayet->id)->exists())
                                            <span class="inline-flex items-center gap-1 text-[8px] tracking-wider font-bold text-red-600 bg-red-50 border border-red-100 px-1.5 py-0.5 rounded" title="Bu şikayet için iade kaydı oluşturuldu">
                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                                                İADE
                                            </span>
                                        @endif
                                        
                                        @if($sikayet->iaa_id && \App\Models\IaaZiyaretPlani::where('iaa_id', $sikayet->iaa_id)->exists())
                                            <span class="inline-flex items-center gap-1 text-[8px] tracking-wider font-bold text-teal-600 bg-teal-50 border border-teal-100 px-1.5 py-0.5 rounded" title="Bu şikayet kapsamında firmanıza ziyaret planlandı">
                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                ZİYARET
                                            </span>
                                        @endif
                                    </div>

                                    @if($sikayet->iaaProjesi)
                                        <div class="mt-2 w-full max-w-[120px]">
                                            @php $ilerleme = $sikayet->iaaProjesi->ilerlemeVerisi; @endphp
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-[9px] font-bold text-gray-500 uppercase">{{ $ilerleme['tamamlanan'] }}/{{ $ilerleme['toplam'] }} Adım</span>
                                                <span class="text-[9px] font-black text-indigo-600">%{{ $ilerleme['yuzde'] }}</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-1 overflow-hidden border border-gray-200">
                                                <div class="bg-indigo-500 h-full transition-all duration-500" style="width: {{ $ilerleme['yuzde'] }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                {{-- Sorumlu Takım --}}
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($sikayet->cozumTakimi)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            {{ $sikayet->cozumTakimi->ad }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                            Atanmadı
                                        </span>
                                    @endif
                                </td>

                                {{-- Durum --}}
                                <td class="px-5 py-3 whitespace-nowrap text-center">
                                    @php
                                        $isCompleted = in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı', 'Tamamlandı']);
                                        $statusClass = match($sikayet->musteri_durum) {
                                            'Yeni' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'İşlemde', 'İnceleniyor', 'Atandı' => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'Çözümlendi', 'Kapatıldı', 'Tamamlandı' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            default => 'bg-gray-50 text-gray-700 border-gray-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $statusClass }}">
                                        {{ $sikayet->musteri_durum }}
                                    </span>
                                    
                                    @if($sikayet->iaaProjesi)
                                        <div class="mt-1 space-y-1">
                                            @php
                                                $pDurum = $sikayet->iaaProjesi->durum;
                                                $pRenk = $sikayet->iaaProjesi->durum_rengi;
                                                $bgClass = "bg-{$pRenk}-50";
                                                $textClass = "text-{$pRenk}-700";
                                                $borderClass = "border-{$pRenk}-100";
                                                if($pRenk == 'purple') { $textClass = "text-purple-600"; }
                                            @endphp
                                            @if(in_array($pDurum, ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor']))
                                                <div class="text-[9px] font-bold {{ $textClass }} animate-pulse flex items-center justify-center gap-1 {{ $bgClass }} px-1.5 py-0.5 rounded border {{ $borderClass }}">
                                                    {{ $pDurum }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- Tarihler --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex flex-col gap-0.5">
                                        <div class="text-[10px] text-gray-500">
                                            <span class="font-bold">Kayıt:</span> {{ $sikayet->created_at->format('d.m.Y') }}
                                        </div>
                                        @if($isCompleted)
                                            <div class="text-[10px] text-emerald-600 font-bold">
                                                <span>Çözüm:</span> {{ $sikayet->updated_at->format('d.m.Y') }}
                                            </div>
                                        @else
                                            <div class="text-[9px] text-gray-400 italic">
                                                {{ $sikayet->created_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- İşlemler --}}
                                <td class="px-5 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex flex-col items-end gap-1.5" x-data="{ openReminderModal: false }">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($sikayet->iaaProjesi)
                                                <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg transition-colors border border-indigo-200" title="Proje Alanı">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('iaa.sikayetler.show', $sikayet->id) }}" class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1 rounded-lg transition-colors text-[10px] font-bold border border-gray-200">
                                                Detay
                                            </a>
                                        </div>

                                        {{-- HATIRLATMA BUTONU --}}
                                        @if($sikayet->musteri_durum === 'İşlemde')
                                            @php $hDurum = $sikayet->musteri_hatirlatma_durumu; @endphp
                                            @if($hDurum['can_send'])
                                                <button @click="openReminderModal = true" class="text-red-600 hover:text-white bg-red-50 hover:bg-red-600 px-3 py-1 rounded-lg transition-all text-[10px] font-black border border-red-200 flex items-center gap-1 shadow-sm w-full justify-center" title="Süreci Hatırlat">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                                    {{ $hDurum['message'] }}
                                                </button>
                                                <template x-teleport="body">
                                                    <div x-show="openReminderModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fade-in" @keydown.escape.window="openReminderModal = false">
                                                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 transform transition-all scale-100" @click.away="openReminderModal = false">
                                                            <div class="bg-red-600 px-6 py-4 flex items-center justify-between">
                                                                <h3 class="text-white font-black text-lg flex items-center gap-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg> Süreci Hatırlat</h3>
                                                                <button @click="openReminderModal = false" class="text-white/80 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                                            </div>
                                                            <form action="{{ route('iaa.hatirlatmalarim.dashboardGonder', $sikayet->id) }}" method="POST" class="p-6">
                                                                @csrf
                                                                <div class="mb-4">
                                                                    <label class="block text-sm font-bold text-gray-700 mb-2">Hatırlatma Notunuz <span class="text-red-500">*</span></label>
                                                                    <textarea name="aciklama" required rows="4" class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring focus:ring-red-200 transition-all text-sm" placeholder="Sürecin hızlandırılması ile ilgili detaylı notunuzu buraya yazınız..."></textarea>
                                                                </div>
                                                                <div class="flex justify-end gap-3">
                                                                    <button type="button" @click="openReminderModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm">Vazgeç</button>
                                                                    <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-black rounded-xl text-sm">Hatırlatmayı Gönder</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </template>
                                            @else
                                                @php
                                                    $reminderId = $hDurum['id'] ?? null;
                                                @endphp
                                                @if($reminderId)
                                                    @php
                                                        $isCustomer = auth()->user()->customer_id !== null;
                                                        $targetRoute = $isCustomer ? 'iaa.hatirlatmalarim.show' : 'admin.sikayet-hatirlatma.show';
                                                    @endphp
                                                    <a href="{{ route($targetRoute, $reminderId) }}" class="text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 py-1 rounded-lg text-[10px] font-black border border-amber-200 flex items-center gap-1 w-full justify-center transition-all animate-pulse" title="Detayı görmek için tıklayın (Henüz yeni hatırlatma gönderilemez)">
                                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $hDurum['message'] }}
                                                    </a>
                                                @else
                                                    <div class="text-amber-700 bg-amber-50 px-3 py-1 rounded-lg text-[10px] font-black border border-amber-200 flex items-center gap-1 w-full justify-center animate-pulse" title="İlk hatırlatma için sürenin dolması bekleniyor">
                                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $hDurum['message'] }}
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- GRAFİK SCRİPTİ --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('sikayetChart').getContext('2d');
        
        const bekleyen = {{ ($stats['toplam_sikayet'] ?? 0) - ($stats['aktif_sikayet'] ?? 0) - ($stats['cozulen_sikayet'] ?? 0) }};
        const islemde = {{ $stats['aktif_sikayet'] ?? 0 }};
        const cozulen = {{ $stats['cozulen_sikayet'] ?? 0 }};
        
        const data = (bekleyen + islemde + cozulen) === 0 ? [1] : [bekleyen, islemde, cozulen];
        const colors = (bekleyen + islemde + cozulen) === 0 ? ['#f3f4f6'] : ['#E5E7EB', '#F97316', '#10B981'];
        const labels = (bekleyen + islemde + cozulen) === 0 ? ['Veri Yok'] : ['Bekleyen', 'İşlemde', 'Çözülen'];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 6,
                            font: { size: 10, family: "'Figtree', sans-serif" }
                        }
                    },
                    tooltip: {
                        enabled: (bekleyen + islemde + cozulen) > 0
                    }
                },
                cutout: '70%',
            }
        });
    });
</script>