@php
    $yeniSikayetSayisi = $stats['yeni_sikayet'] ?? 0;
@endphp

<div class="space-y-12 animate-fade-in pb-10">
    {{-- 1. ÜST BÖLÜM: HOŞGELDİN VE ÖZET KARTLAR --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                Şikayet Kurulu Dashboard
            </h2>
            <p class="text-slate-500 text-sm font-medium mt-1 ml-5">Müşteri geri bildirimleri ve çözüm süreçleri genel yönetim paneli.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sikayetler.kurulGirdileri') }}" class="flex items-center gap-2 px-5 py-2.5 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-900 transition-all shadow-lg shadow-slate-100 group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m32-2v-2a4 4 0 00-4-4h-1a4 4 0 00-4 4v2m0 10H5m11 0a4 4 0 01-4-4v-4m0 4a4 4 0 01-4 4m12 0a4 4 0 01-4-4v-4m0 4h.01"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zM12 11c2.21 0 4-1.79-4-4s-1.79-4-4-4-4 1.79-4 4-1.79 4-4 4z"></path></svg>
                Kurul Girdileri
            </a>
            <a href="{{ route('admin.sikayetler.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Yeni Şikayet Kaydı
            </a>
        </div>
    </div>

    {{-- ACİL EYLEM UYARISI (YENİ ŞİKAYETLER VARSA) --}}
    @if($yeniSikayetSayisi > 0)
        <div class="bg-gradient-to-r from-rose-500 to-rose-600 rounded-2xl p-6 shadow-xl shadow-rose-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
            <div class="flex items-center gap-6 relative z-10">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center animate-pulse border border-white/30">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-white uppercase tracking-tight">Dikkat: {{ $yeniSikayetSayisi }} Yeni Şikayet Atama Bekliyor!</h3>
                    <p class="text-rose-100 font-medium">Sisteme yeni girilen şikayetlerin henüz çözüm takımı ataması yapılmadı. Lütfen süreci başlatmak için görevlendirme yapınız.</p>
                </div>
            </div>
            <a href="{{ route('admin.sikayetler.index', ['tab' => 'yeni']) }}" class="relative z-10 px-8 py-3 bg-white text-rose-600 font-black rounded-xl hover:bg-rose-50 transition-colors shadow-lg whitespace-nowrap">
                Atamaları Yap →
            </a>
        </div>
    @endif

    {{-- ÖZET İSTATİSTİKLER --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">TOPLAM</span>
            </div>
            <h4 class="text-4xl font-black text-slate-800">{{ $stats['toplam_sikayet'] }}</h4>
            <p class="text-slate-500 text-xs font-bold mt-1 uppercase">Kayıtlı Şikayet</p>
        </div>

        <a href="{{ route('admin.sikayetler.index', ['tab' => 'yeni']) }}" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">BEKLEYEN</span>
            </div>
            <h4 class="text-4xl font-black text-amber-600">{{ $stats['yeni_sikayet'] }}</h4>
            <p class="text-slate-500 text-xs font-bold mt-1 uppercase">Atama Bekleyen</p>
        </a>

        <a href="{{ route('admin.sikayetler.index', ['tab' => 'islemde']) }}" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">İSLEMDE</span>
            </div>
            <h4 class="text-4xl font-black text-indigo-600">{{ $stats['islemde_sikayet'] }}</h4>
            <p class="text-slate-500 text-xs font-bold mt-1 uppercase">Çözüm Sürecinde</p>
        </a>

        <a href="{{ route('admin.sikayetler.index', ['tab' => 'cozulmus']) }}" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">BAŞARI</span>
            </div>
            <h4 class="text-4xl font-black text-emerald-600">{{ $stats['tamamlanan_sikayet'] ?? 0 }}</h4>
            <p class="text-slate-500 text-xs font-bold mt-1 uppercase">Tamamlananlar</p>
        </a>
    </div>

    {{-- SON GELEN ŞİKAYETLER DETAYLI TABLO (TAM GENİŞLİK) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden"
         x-data="{ 
            limit: 5, 
            total: {{ count($stats['son_sikayetler'] ?? []) }},
            increment: 5
         }">
        <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
            <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Son Gelen Şikayetler (Detaylı Takip)
            </h3>
            <a href="{{ route('admin.sikayetler.index') }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-wider">Tümü →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4">Müşteri & Konu</th>
                        <th class="px-6 py-4">Bölüm & Kategori</th>
                        <th class="px-6 py-4">Sorumlu Takım</th>
                        <th class="px-6 py-4 text-center">Durum</th>
                        <th class="px-6 py-4 text-center">İlerleme</th>
                        <th class="px-6 py-4 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($stats['son_sikayetler'] as $index => $sikayet)
                        @php
                            $progress = $sikayet->iaaProjesi ? $sikayet->iaaProjesi->ilerleme_verisi['yuzde'] : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors" x-show="{{ $index }} < limit" x-transition>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-100 shadow-sm flex items-center justify-center p-1 overflow-hidden flex-shrink-0">
                                        @if($sikayet->customer && $sikayet->customer->logo_path)
                                            <img src="{{ asset('storage/' . $sikayet->customer->logo_path) }}" class="w-full h-full object-contain" alt="">
                                        @else
                                            <span class="text-[11px] font-black text-slate-400">{{ substr($sikayet->customer->name ?? $sikayet->musteri_adi, 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-slate-800 truncate leading-tight" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                            {{ Str::limit($sikayet->musteri_sikayet_konusu, 40) }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-tight">{{ Str::limit($sikayet->customer->name ?? $sikayet->musteri_adi, 25) }}</p>
                                            @if($sikayet->konum_tipi)
                                                <span class="text-[8px] px-1.5 py-0.5 rounded-sm {{ $sikayet->konum_tipi === 'Yurt İçi' ? 'bg-sky-50 text-sky-600 border border-sky-100' : 'bg-fuchsia-50 text-fuchsia-600 border border-fuchsia-100' }}">{{ $sikayet->konum_tipi }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-0.5">
                                    <p class="text-[11px] font-bold text-slate-700">{{ $sikayet->sikayetKategori->bolum->ad ?? '-' }}</p>
                                    <p class="text-[9px] text-slate-400 font-medium italic">{{ $sikayet->sikayetKategori->ad ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($sikayet->cozumTakimi)
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full bg-indigo-500 text-[8px] text-white flex items-center justify-center font-black">
                                            {{ substr($sikayet->cozumTakimi->ad, 0, 1) }}
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-600 truncate max-w-[100px]">{{ $sikayet->cozumTakimi->ad }}</span>
                                    </div>
                                @else
                                    <span class="text-[8px] font-black text-rose-500 uppercase tracking-tighter animate-pulse">Atanmadı</span>
                                @endif
                                @if($sikayet->olusturanKurulUyesi)
                                    <p class="text-[8px] text-slate-400 mt-1" title="Ekleyen Personel">Ekleyen: {{ $sikayet->olusturanKurulUyesi->name }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="scale-90 origin-center flex flex-col items-center gap-1.5">
                                    {!! $sikayet->musteri_durum_badge !!}
                                    
                                    @if($sikayet->iaaProjesi && $sikayet->iaaProjesi->ziyaretPlani()->exists())
                                        <span class="text-[9px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded border border-blue-100 whitespace-nowrap shadow-sm">📅 Ziyaret Planlandı</span>
                                    @endif
                                    
                                    @if($sikayet->iadeler()->exists())
                                        <span class="text-[9px] bg-rose-50 text-rose-600 px-1.5 py-0.5 rounded border border-rose-100 whitespace-nowrap shadow-sm animate-pulse">♻️ İade Var</span>
                                    @endif
                                    
                                    @if($sikayet->iaaProjesi && isset($sikayet->iaaProjesi->ilerleme_verisi['kapanis_bekleniyor']) && $sikayet->iaaProjesi->ilerleme_verisi['kapanis_bekleniyor'])
                                        <span class="text-[9px] bg-amber-50 text-amber-600 px-1.5 py-0.5 rounded border border-amber-100 whitespace-nowrap shadow-sm animate-pulse">⏳ Kapanış İşlemleri Bekleniyor!</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center gap-1 min-w-[70px]">
                                    <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                                        <div class="bg-indigo-500 h-full transition-all duration-700" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="text-[8px] font-black text-slate-400">{{ round($progress) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.sikayetler.show', $sikayet) }}" class="p-2 bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400 italic">Kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-slate-50/50 border-t border-slate-100 px-6 py-3 flex items-center justify-center gap-4" x-show="total > 5">
            <button x-show="limit < total" @click="limit += increment" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">Daha Fazla Göster (5+)</button>
            <button x-show="limit > 5" @click="limit = 5" class="text-[10px] font-black text-rose-600 uppercase tracking-widest hover:underline">Gizle / Daralt</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- KURUL ÜYELERİ PERFORMANSI (İLK 5 SIRALI) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                <h3 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Kurul Performans (Liderlik)
                </h3>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">En Çok Girdi Yapan 5 Üye</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-6 py-4">Kurul Üyesi</th>
                            <th class="px-4 py-4 text-center">Şikayet</th>
                            <th class="px-6 py-4 text-right">Puan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach(($stats['kurul_uyeleri'] ?? [])->take(5) as $index => $uye)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <img src="{{ $uye->profile_photo_url }}" class="w-8 h-8 rounded-full border border-slate-100 shadow-sm" alt="">
                                            <div class="absolute -top-1 -right-1 w-4 h-4 rounded-full {{ $index == 0 ? 'bg-amber-400' : 'bg-slate-200' }} flex items-center justify-center text-[8px] font-black border border-white">{{ $index + 1 }}</div>
                                        </div>
                                        <span class="text-xs font-black text-slate-800">{{ $uye->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-full border border-indigo-100">{{ $uye->girdigi_sikayetler_count }} Şikayet</span>
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-black text-emerald-600">
                                    {{ number_format($uye->girdigi_sikayetler_sum_kazanilan_puan ?: 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-slate-50/50 border-t border-slate-100 px-6 py-3 flex items-center justify-center">
                <a href="{{ route('admin.sikayetler.kurulGirdileri') }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">Tüm Üyeleri ve Raporları Görüntüle →</a>
            </div>
        </div>

        {{-- MÜŞTERİ HATIRLATMA TALEPLERİ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-50 bg-rose-50/30 flex items-center justify-between">
                <h3 class="font-black text-rose-900 uppercase tracking-tight flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Müşteri Hatırlatma Talepleri ({{ $stats['toplam_hatirlatma_sayisi'] ?? 0 }})
                </h3>
            </div>
            <div class="overflow-x-auto scrollbar-hide">
                <table class="w-full text-left border-collapse table-auto">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-4 py-3">Şikayet & Müşteri</th>
                            <th class="px-4 py-3 text-center">Durum</th>
                            <th class="px-4 py-3 text-right">Zaman</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($stats['hatirlatmalar'] ?? [] as $hatirlatma)
                            <tr class="hover:bg-rose-50/20 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black text-slate-800 truncate max-w-[150px] leading-tight" title="{{ $hatirlatma->musteriSikayeti->musteri_sikayet_konusu ?? '-' }}">
                                            {{ Str::limit($hatirlatma->musteriSikayeti->musteri_sikayet_konusu ?? '-', 35) }}
                                        </p>
                                        <p class="text-[9px] font-bold text-rose-600 uppercase tracking-tight">{{ Str::limit($hatirlatma->musteriSikayeti->customer->name ?? '-', 25) }}</p>
                                    </div>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    @php
                                        $colors = [
                                            'bilgi_girisi_bekleniyor' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'bilgi_girildi' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'musteri_ikna_oldu' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'kapatildi' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        ];
                                        $labels = [
                                            'bilgi_girisi_bekleniyor' => 'Bekliyor',
                                            'bilgi_girildi' => 'Girildi',
                                            'musteri_ikna_oldu' => 'İkna Oldu',
                                            'kapatildi' => 'Kapandı',
                                        ];
                                        $colorClass = $colors[$hatirlatma->durum] ?? 'bg-slate-50 text-slate-500 border-slate-100';
                                        $labelText = $labels[$hatirlatma->durum] ?? $hatirlatma->durum;
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-tighter border {{ $colorClass }}">
                                        {{ $labelText }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="text-[9px] font-black text-slate-400 uppercase whitespace-nowrap">{{ $hatirlatma->created_at->diffForHumans(null, true) }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic text-[10px]">Aktif hatırlatma talebi bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-slate-50/50 border-t border-slate-100 px-6 py-3 flex items-center justify-center">
                <a href="{{ route('admin.sikayet-hatirlatma.index') }}" class="text-[10px] font-black text-rose-600 uppercase tracking-widest hover:underline">Tüm Hatırlatma Süreçleri →</a>
            </div>
        </div>
    </div>

    {{-- İADELER TABLOSU --}}
    @if(isset($iadeVerileri))
        <div id="iadeler" class="scroll-mt-24">
            @include('dashboard.partials.iadeler-tablosu')
        </div>
    @endif

    {{-- MÜŞTERİ LİSTESİ --}}
    <div id="musteri-listesi" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" 
         x-data="{ 
            limit: 5, 
            total: {{ count($stats['sorumlu_musteriler'] ?? []) }},
            increment: 5
         }">
        <div class="px-6 py-5 border-b border-slate-50 bg-emerald-50/30">
            <h3 class="font-black text-emerald-900 uppercase tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Kayıtlı Müşteriler ({{ count($stats['sorumlu_musteriler'] ?? []) }})
            </h3>
        </div>
        <div class="overflow-x-auto min-w-full">
            <table class="w-full text-left text-sm whitespace-nowrap table-auto">
                <thead class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Müşteri Firması</th>
                        <th class="px-6 py-4">İstatistikler</th>
                        <th class="px-6 py-4">Lokasyon</th>
                        <th class="px-6 py-4">Yetkililer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($stats['sorumlu_musteriler'] as $index => $musteri)
                        <tr class="hover:bg-emerald-50/20 transition-colors" x-show="{{ $index }} < limit" x-transition>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-100 shadow-sm flex items-center justify-center p-1 overflow-hidden">
                                        @if($musteri->logo_path)
                                            <img src="{{ asset('storage/' . $musteri->logo_path) }}" class="w-full h-full object-contain" alt="">
                                        @else
                                            <div class="w-full h-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-[10px]">{{ substr($musteri->name, 0, 2) }}</div>
                                        @endif
                                    </div>
                                    <a href="{{ route('musteri.profil.show', $musteri->id) }}" class="font-black text-slate-800 hover:text-emerald-600 transition-colors text-xs">{{ Str::limit($musteri->name, 45) }}</a>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="bg-slate-50 px-2 py-0.5 rounded border border-slate-100 text-[10px] font-black text-slate-500">T: {{ $musteri->toplam_sikayet }}</div>
                                    <div class="bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 text-[10px] font-black text-emerald-600">Ç: {{ $musteri->cozulen_sikayet }}</div>
                                    <div class="bg-amber-50 px-2 py-0.5 rounded border border-amber-100 text-[10px] font-black text-amber-600">B: {{ $musteri->bekleyen_sikayet }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[11px] text-slate-500 font-medium">
                                {{ Str::limit($musteri->address ?? '-', 40) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex -space-x-2">
                                    @foreach($musteri->users->take(4) as $yetkili)
                                        <img src="{{ $yetkili->profile_photo_url }}" class="w-6 h-6 rounded-full border-2 border-white ring-1 ring-slate-100" title="{{ $yetkili->name }}">
                                    @endforeach
                                    @if($musteri->users->count() > 4)
                                        <div class="w-6 h-6 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[8px] font-black text-slate-500">+{{ $musteri->users->count() - 4 }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Müşteri kaydı bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- MÜŞTERİ LİSTESİ ALPINE.JS KONTROLLERİ --}}
        <div class="bg-slate-50/50 border-t border-slate-100 px-6 py-2 flex items-center justify-center gap-4" x-show="total > 5">
            <button x-show="limit < total" @click="limit += increment" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">Daha Fazla Göster (5+)</button>
            <button x-show="limit > 5" @click="limit = 5" class="text-[10px] font-black text-rose-600 uppercase tracking-widest hover:underline">Gizle</button>
        </div>
    </div>
</div>