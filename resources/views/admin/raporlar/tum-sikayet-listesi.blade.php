@php
    use Illuminate\Support\Str;
@endphp

@push('head')
<style>
    @keyframes slowPulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.98); }
        100% { opacity: 1; transform: scale(1); }
    }
    .animate-slow-pulse {
        animation: slowPulse 3s ease-in-out infinite;
    }
</style>
@endpush

@push('pageTitle')
    Tüm Şikayet Listesi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tight uppercase">
                    {{ __('Müşteri Şikayet Kayıtları') }}
                </h2>
                <p class="text-xs font-medium text-slate-500 italic mt-1 uppercase tracking-widest">Filtrelenebilir Genişletilmiş Rapor Listesi</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                {{-- Dışa Aktar Butonları --}}
                <div class="flex items-center bg-white p-1 rounded-2xl shadow-sm border border-slate-200">
                    <a href="{{ route('admin.sikayet-raporlari.export-excel', request()->query()) }}" 
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-xs font-black rounded-xl hover:bg-emerald-700 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-emerald-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        EXCEL
                    </a>
                    <div class="w-px h-6 bg-slate-200 mx-1"></div>
                    <a href="{{ route('admin.sikayet-raporlari.export-pdf', request()->query()) }}" 
                       class="inline-flex items-center px-4 py-2 bg-rose-600 text-white text-xs font-black rounded-xl hover:bg-rose-700 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-rose-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        PDF
                    </a>
                </div>

                <a href="{{ route('admin.sikayet-raporlari.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-slate-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    GERİ DÖN
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Sayfada yatay scroll oluşmasın --}}
    <div class="py-10 bg-gradient-to-br from-gray-50 to-gray-100 overflow-x-hidden">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- === KPI KARTLARI GÜNCELLENDİ === --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Toplam Kayıt</div>
                    <div class="text-2xl font-black text-blue-600">{{ $sikayetler->total() }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200 cursor-pointer hover:border-yellow-400 transition-colors" onclick="window.location.href='{{ route('admin.sikayet-raporlari.tum-liste', ['durum' => 'Yeni']) }}'">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Yeni</div>
                    <div class="text-2xl font-black text-yellow-600">{{ $stats['yeni'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200 cursor-pointer hover:border-indigo-400 transition-colors" onclick="window.location.href='{{ route('admin.sikayet-raporlari.tum-liste', ['durum' => 'İşlemde']) }}'">
                    <div class="text-xs font-semibold text-gray-500 uppercase">İşlemde</div>
                    <div class="text-2xl font-black text-indigo-600">{{ $stats['islemde'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200 cursor-pointer hover:border-green-400 transition-colors" onclick="window.location.href='{{ route('admin.sikayet-raporlari.tum-liste', ['durum' => 'Kapatıldı']) }}'">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Çözülen</div>
                    <div class="text-2xl font-black text-green-600">{{ $stats['cozulen'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Talep Kapatılan</div>
                    <div class="text-2xl font-black text-purple-600">{{ $stats['talep_kapatilan'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Hatalı Bildirim</div>
                    <div class="text-2xl font-black text-orange-500">{{ $stats['hatali_bildirim'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow border-red-200 border-2 cursor-pointer hover:bg-red-50 transition-colors group" onclick="window.location.href='{{ route('admin.sikayet-raporlari.tum-liste', ['gecikmis' => 1]) }}'">
                    <div class="text-xs font-bold text-red-600 uppercase flex items-center gap-1">
                        Gecikmiş
                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    </div>
                    <div class="text-2xl font-black text-red-700 group-hover:scale-110 transition-transform">{{ $stats['gecikmis'] }}</div>
                </div>
            </div>

            @if($stats['enCokKategori'])
                <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
                    <div class="text-xs font-semibold text-gray-500 uppercase">En Yoğun Kategori</div>
                    <div class="text-lg font-bold text-gray-800 truncate" title="{{ $stats['enCokKategori']->ad }}">
                        {{ $stats['enCokKategori']->ad }}
                        <span class="text-base font-medium text-gray-500">({{ $stats['enCokKategori']->total }} adet)</span>
                    </div>
                </div>
            @endif
            {{-- === FİLTRELEME BÖLÜMÜ === --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <form action="{{ route('admin.sikayet-raporlari.tum-liste') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Başlangıç Tarihi</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bitiş Tarihi</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                        <select name="kategori_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tümü</option>
                            @foreach($kategoriler as $kat)
                                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->ad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Durum</label>
                        <select name="durum" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tümü</option>
                            <option value="Yeni" {{ request('durum') == 'Yeni' ? 'selected' : '' }}>Yeni</option>
                            <option value="İşlemde" {{ request('durum') == 'İşlemde' ? 'selected' : '' }}>İşlemde</option>
                            <option value="Kapatıldı" {{ request('durum') == 'Kapatıldı' ? 'selected' : '' }}>Kapatıldı</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">İade Durumu</label>
                        <input type="hidden" name="iade_durumu" id="iade_durumu_input" value="{{ request('iade_durumu') }}">
                        <div class="flex p-1 bg-gray-100 rounded-lg">
                            <button type="button" 
                                onclick="document.getElementById('iade_durumu_input').value=''; this.form.submit();"
                                class="flex-1 px-3 py-1.5 text-[10px] font-bold rounded-md transition-all {{ request('iade_durumu') == '' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                TÜMÜ
                            </button>
                            <button type="button" 
                                onclick="document.getElementById('iade_durumu_input').value='iadeli'; this.form.submit();"
                                class="flex-1 px-3 py-1.5 text-[10px] font-bold rounded-md transition-all {{ request('iade_durumu') == 'iadeli' ? 'bg-red-500 text-white shadow-sm' : 'text-gray-500 hover:text-red-600' }}">
                                İADELİ
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Gecikme Filtre</label>
                        <input type="hidden" name="gecikmis" id="gecikmis_input" value="{{ request('gecikmis') }}">
                        <div class="flex p-1 bg-gray-100 rounded-lg">
                            <button type="button" 
                                onclick="document.getElementById('gecikmis_input').value=''; this.form.submit();"
                                class="flex-1 px-3 py-1.5 text-[10px] font-bold rounded-md transition-all {{ request('gecikmis') == '' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                TÜMÜ
                            </button>
                            <button type="button" 
                                onclick="document.getElementById('gecikmis_input').value='1'; this.form.submit();"
                                class="flex-1 px-3 py-1.5 text-[10px] font-bold rounded-md transition-all {{ request('gecikmis') == '1' ? 'bg-rose-600 text-white shadow-sm' : 'text-gray-500 hover:text-rose-600' }}">
                                GECİKEN
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Gecikme Sıralama</label>
                        <select name="direction" onchange="this.form.sort.value='gecikme_suresi'; this.form.submit();" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Sıralama Seçin</option>
                            <option value="desc" {{ request('sort') == 'gecikme_suresi' && request('direction') == 'desc' ? 'selected' : '' }}>En Çok Geciken</option>
                            <option value="asc" {{ request('sort') == 'gecikme_suresi' && request('direction') == 'asc' ? 'selected' : '' }}>En Az Geciken</option>
                        </select>
                        <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-indigo-700 transition shadow-md">Filtrele</button>
                        <a href="{{ route('admin.sikayet-raporlari.tum-liste') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-200 transition text-center flex items-center justify-center">Temizle</a>
                    </div>
                </form>
            </div>
            {{-- === FİLTRELEME SONU === --}}

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-200">
                <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-gray-100 bg-gray-50/30 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Şikayet Kayıtları
                    </h3>
                    <div class="flex flex-wrap items-center gap-4 text-[11px] font-bold uppercase tracking-wide">
                        {{-- İadeli Şikayet Legend --}}
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white text-red-700 rounded-xl border border-red-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-6 h-6 flex items-center justify-center bg-red-500 rounded-lg text-white shadow-sm shadow-red-200 animate-pulse">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/>
                                </svg>
                            </div>
                            <span>İadeli Şikayet</span>
                        </div>
                        {{-- Ziyaret Planlı Legend --}}
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white text-blue-700 rounded-xl border border-blue-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-6 h-6 flex items-center justify-center bg-blue-500 rounded-lg text-white shadow-sm shadow-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span>Ziyaret Planlı</span>
                        </div>
                    </div>
                </div>

                {{-- === 1. MASAÜSTÜ TABLO GÖRÜNÜMÜ (md ve üzeri) === --}}
                {{-- (Kaydırmayı engellemek için table-fixed ve truncate kullandık) --}}
                <div class="hidden md:block overflow-hidden">
                    <table class="w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 4%;">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 10%;">Tarih</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 13%;">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 14%;">Müşteri</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 14%;">Başlık</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 12%;">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 8%;">Yorumlar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 10%;">Hedef / Kapanış</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 8%;">Resimler</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    style="width: 9%;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($sikayetler as $index => $sikayet)
                                @php
                                    // === RENKLENDİRME GÜNCELLENDİ ===
                                    $rowBg = 'hover:bg-gray-50';
                                    $rowBar = 'border-l-4 border-transparent';
                                    if ($sikayet->musteri_durum === 'İşlemde') {
                                        $rowBg = 'bg-blue-50/60 hover:bg-blue-100/60';
                                        $rowBar = 'border-l-4 border-blue-400';
                                    } elseif ($sikayet->musteri_durum === 'Yeni') {
                                        $rowBg = 'bg-yellow-50/60 hover:bg-yellow-100/60';
                                        $rowBar = 'border-l-4 border-yellow-400';
                                    } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) {
                                        $rowBg = 'bg-green-50/60 hover:bg-green-100/60';
                                        $rowBar = 'border-l-4 border-green-400';
                                    } elseif (in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) {
                                        $rowBg = 'bg-purple-50/60 hover:bg-purple-100/60';
                                        $rowBar = 'border-l-4 border-purple-400';
                                    } elseif (in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) {
                                        $rowBg = 'bg-orange-50/30 hover:bg-orange-100/30';
                                        $rowBar = 'border-l-4 border-orange-300';
                                    } else {
                                        // Diğer durumlar (örn: Yeniden Açıldı) için
                                        $rowBg = 'bg-gray-50/60 hover:bg-gray-100/60';
                                        $rowBar = 'border-l-4 border-gray-400';
                                    }
                                    // === RENKLENDİRME SONU ===
                                @endphp

                                <tr class="{{ $rowBg }} {{ $rowBar }} transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ $sikayetler->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                        {{ $sikayet->created_at?->format('d.m.Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 truncate"
                                        title="{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}">
                                        {{ $sikayet->sikayetKategori->ad ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 truncate">
                                        <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank" class="hover:text-indigo-600 transition-colors" title="{{ $sikayet->musteri_adi }}">
                                            {{ $sikayet->musteri_adi }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 truncate">
                                        <div class="font-bold text-gray-900 truncate" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                            {{ $sikayet->musteri_sikayet_konusu }}
                                        </div>
                                        <div class="mt-2 flex items-center flex-nowrap gap-1.5 overflow-x-auto no-scrollbar">
                                            @if(count($sikayet->iadeler) > 0)
                                                <div class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-red-600 text-white shadow-sm transition-all hover:bg-red-700 group" title="İade Kaydı Mevcut">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/>
                                                    </svg>
                                                    <span class="text-[8px] font-black uppercase tracking-tighter">İadeli</span>
                                                </div>
                                            @endif
                                            @if(isset($visitsByComplaint[$sikayet->id]))
                                                <div class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-600 text-white shadow-sm transition-all hover:bg-blue-700 group" title="Müşteri ziyareti planlanmış">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="text-[8px] font-black uppercase tracking-tighter">Ziyaret</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex flex-col gap-1.5">
                                            <div>
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                @if($sikayet->musteri_durum === 'Yeni') bg-yellow-100 text-yellow-800
                                                                @elseif($sikayet->musteri_durum === 'İşlemde') bg-blue-100 text-blue-800
                                                                @elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) bg-green-100 text-green-800
                                                                @elseif(in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) bg-purple-100 text-purple-700 border border-purple-200 font-bold
                                                                @elseif(in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) bg-orange-100 text-orange-800 border border-orange-200 line-through
                                                                @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $sikayet->musteri_durum ?? '—' }}
                                                </span>
                                            </div>
                                            @if(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']) && $sikayet->iaaProjesi)
                                                <div class="flex items-center gap-1 opacity-90 pl-1">
                                                    @php
                                                        $pDurum = $sikayet->iaaProjesi->durum;
                                                        $isFaulty = Str::contains($pDurum, 'hatali_bildirim');
                                                        $isRequest = Str::contains($pDurum, 'talep');
                                                        $tooltipText = $isFaulty ? 'Hatalı Bildirim Olarak Kapatıldı' : ($isRequest ? 'Talep Olarak Kapatıldı' : 'Proje Durumu: ' . $pDurum);
                                                    @endphp

                                                    @if($isFaulty)
                                                        <div class="group relative cursor-help">
                                                            <svg class="w-5 h-5 text-red-500 hover:text-red-700 transition-colors"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                                </path>
                                                            </svg>
                                                            <!-- Tooltip -->
                                                            <span
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                                                {{ $tooltipText }}
                                                            </span>
                                                        </div>
                                                    @elseif($isRequest)
                                                        <div class="group relative cursor-help">
                                                            <svg class="w-5 h-5 text-blue-500 hover:text-blue-700 transition-colors"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                                </path>
                                                            </svg>
                                                            <!-- Tooltip -->
                                                            <span
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                                                {{ $tooltipText }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div title="{{ $pDurum }}">
                                                            {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <div class="flex items-center space-x-1">
                                            @if($sikayet->proje_yorumlari_count > 0)
                                                <span class="font-bold text-gray-700 text-xs">{{ $sikayet->proje_yorumlari_count }}</span>
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                @if($sikayet->musteri_proje_yorumlari_count > 0)
                                                    <span class="text-yellow-500" title="Müşteri Yorumu Var">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-[10px] text-gray-400">Yok</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            @php
                                                $deadlineDate = $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi) : null;
                                                $isOverdue = $deadlineDate && $deadlineDate->isPast() && !in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']);
                                            @endphp
                                            <div class="flex flex-col gap-1 {{ $isOverdue ? 'text-red-700 font-black' : ($sikayet->musteri_cozum_son_tarihi ? 'text-red-600 font-semibold' : 'text-gray-500') }}" title="Hedef Çözüm Tarihi">
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    {{ $deadlineDate ? $deadlineDate->format('d.m.Y') : 'N/A' }}
                                                </div>
                                                @if($isOverdue)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-red-100 text-red-600 border border-red-200 animate-pulse uppercase tracking-tighter w-fit">
                                                        ⚠️ GECİKTİ ({{ (int) $deadlineDate->diffInDays(now()) }} GÜN)
                                                    </span>
                                                @endif
                                            </div>
                                            @if($sikayet->musteri_onay_tarihi || $sikayet->kurul_onay_tarihi)
                                                <div class="flex items-center gap-1 text-green-600 font-bold text-[11px]" title="Kapanış/Onay Tarihi">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    {{ ($sikayet->kurul_onay_tarihi ?? $sikayet->musteri_onay_tarihi)->format('d.m.Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        @php
                                            $imageFiles = $sikayet->dosyalar->filter(fn($d) => Str::startsWith($d->mime_tipi, 'image/'));
                                        @endphp
                                        <div class="flex items-center space-x-1">
                                            @forelse ($imageFiles->take(2) as $dosya)
                                                <a href="{{ asset('storage/' . $dosya->dosya_yolu) }}" target="_blank"
                                                    title="{{ $dosya->orijinal_adi }}">
                                                    <img src="{{ asset('storage/' . $dosya->dosya_yolu) }}"
                                                        class="h-8 w-8 rounded-md object-cover border border-gray-300 hover:scale-110 transition-transform"
                                                        alt="Önizleme">
                                                </a>
                                            @empty
                                                <span class="text-xs">Yok</span>
                                            @endforelse
                                            @if($imageFiles->count() > 2)
                                                <span
                                                    class="text-xs text-gray-400 font-bold ml-1">+{{ $imageFiles->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank"
                                                class="inline-flex items-center px-3 py-1.5 font-semibold rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                                Detay
                                            </a>
                                            @if($sikayet->iaaProjesi ?? null)
                                                <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center px-3 py-1.5 font-semibold rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200 transition">
                                                    Proje
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Kayıt bulunamadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- === 2. MOBİL KART GÖRÜNÜMÜ (md'den küçük) === --}}
                <div class="md:hidden">
                    <div class="space-y-4 p-4">
                        @forelse ($sikayetler as $index => $sikayet)
                            @php
                                // === RENKLENDİRME GÜNCELLENDİ ===
                                $rowBg = 'hover:bg-gray-50';
                                $rowBar = 'border-l-4 border-transparent';
                                if ($sikayet->musteri_durum === 'İşlemde') {
                                    $rowBg = 'bg-blue-50/60 hover:bg-blue-100/60';
                                    $rowBar = 'border-l-4 border-blue-400';
                                } elseif ($sikayet->musteri_durum === 'Yeni') {
                                    $rowBg = 'bg-yellow-50/60 hover:bg-yellow-100/60';
                                    $rowBar = 'border-l-4 border-yellow-400';
                                } elseif (in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) {
                                    $rowBg = 'bg-green-50/60 hover:bg-green-100/60';
                                    $rowBar = 'border-l-4 border-green-400';
                                } elseif (in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) {
                                    $rowBg = 'bg-gray-50 hover:bg-gray-100';
                                    $rowBar = 'border-l-4 border-gray-400';
                                } elseif (in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) {
                                    $rowBg = 'bg-rose-50/30 hover:bg-rose-100/30';
                                    $rowBar = 'border-l-4 border-rose-300';
                                } else {
                                    // Diğer durumlar için
                                    $rowBg = 'bg-gray-50/60 hover:bg-gray-100/60';
                                    $rowBar = 'border-l-4 border-gray-400';
                                }
                                // === RENKLENDİRME SONU ===
                            @endphp



                            <div class="rounded-lg shadow border {{ $rowBg }} {{ $rowBar }} p-4 space-y-3 cursor-pointer"
                                onclick="window.open('{{ route('admin.sikayetler.show', $sikayet) }}', '_blank')"
                                title="Şikayet detayını görmek için tıklayın">
                                {{-- Kart Başı: Tarih ve Durum --}}
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span
                                            class="font-semibold text-gray-700">#{{ ($sikayetler->currentPage() - 1) * $sikayetler->perPage() + $index + 1 }}</span>
                                        <span
                                            class="text-sm text-gray-600 ml-2">{{ $sikayet->created_at?->format('d.m.Y') }}</span>
                                    </div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            @if($sikayet->musteri_durum === 'Yeni') bg-yellow-100 text-yellow-800
                                                            @elseif($sikayet->musteri_durum === 'İşlemde') bg-blue-100 text-blue-800
                                                            @elseif(in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı'])) bg-green-100 text-green-800
                                                            @elseif(in_array($sikayet->musteri_durum, ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])) bg-gray-100 text-gray-800 border border-gray-300 font-bold
                                                            @elseif(in_array($sikayet->musteri_durum, ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])) bg-rose-100 text-rose-800 border border-rose-200 line-through
                                                            @else bg-gray-100 text-gray-800 @endif">
                                        {{ $sikayet->musteri_durum ?? '—' }}
                                    </span>
                                </div>

                                {{-- Kart Gövdesi: Kategori, Başlık, Müşteri --}}
                                <div>
                                    <p class="text-xs text-gray-500 uppercase truncate"
                                        title="{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}">
                                        {{ $sikayet->sikayetKategori->ad ?? 'N/A' }}
                                    </p>
                                    <div class="flex flex-col gap-1">
                                        <p class="text-base font-semibold text-gray-900 truncate"
                                            title="{{ $sikayet->musteri_sikayet_konusu }}">
                                            {{ $sikayet->musteri_sikayet_konusu }}
                                        </p>
                                        <div class="flex items-center flex-nowrap gap-1.5 overflow-x-auto no-scrollbar">
                                            @if(count($sikayet->iadeler) > 0)
                                                <div class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-red-600 text-white shadow-sm transition-all hover:bg-red-700 group">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3"/>
                                                    </svg>
                                                    <span class="text-[8px] font-black uppercase tracking-tighter">İadeli</span>
                                                </div>
                                            @endif
                                            @if(isset($visitsByComplaint[$sikayet->id]))
                                                <div class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-600 text-white shadow-sm transition-all hover:bg-blue-700 group">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="text-[8px] font-black uppercase tracking-tighter">Ziyaret</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank" class="text-sm font-medium text-gray-700 truncate hover:text-indigo-600 transition-colors"
                                            title="{{ $sikayet->musteri_adi }}">{{ $sikayet->musteri_adi }}</a>
                                        <div class="flex items-center gap-1 text-xs text-gray-500">
                                            @if($sikayet->proje_yorumlari_count > 0)
                                                <span>{{ $sikayet->proje_yorumlari_count }}</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Kart Altı: Son Tarih ve Resimler --}}
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                    <div class="text-xs space-y-1">
                                        @php
                                            $mDeadline = $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi) : null;
                                            $mIsOverdue = $mDeadline && $mDeadline->isPast() && !in_array($sikayet->musteri_durum, ['Çözümlendi', 'Kapatıldı']);
                                        @endphp
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-1 {{ $mIsOverdue ? 'text-red-700 font-black' : ($sikayet->musteri_cozum_son_tarihi ? 'text-red-600' : 'text-gray-500') }}">
                                                <span class="opacity-70">Hedef:</span>
                                                <span class="font-semibold">{{ $mDeadline ? $mDeadline->format('d.m.Y') : 'N/A' }}</span>
                                            </div>
                                            @if($mIsOverdue)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-red-100 text-red-600 border border-red-200 animate-pulse uppercase tracking-tighter w-fit">
                                                    ⚠️ GECİKTİ ({{ (int) $mDeadline->diffInDays(now()) }} GÜN)
                                                </span>
                                            @endif
                                        </div>
                                        @if($sikayet->musteri_onay_tarihi || $sikayet->kurul_onay_tarihi)
                                            <div class="flex items-center gap-1 text-green-600 font-bold">
                                                <span class="opacity-70">Kapanış:</span>
                                                <span>{{ ($sikayet->kurul_onay_tarihi ?? $sikayet->musteri_onay_tarihi)->format('d.m.Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @php
                                        $imageFiles = $sikayet->dosyalar->filter(fn($d) => Str::startsWith($d->mime_tipi, 'image/'));
                                    @endphp
                                    <div class="flex items-center space-x-1">
                                        @forelse ($imageFiles->take(2) as $dosya)
                                            <a href="{{ asset('storage/' . $dosya->dosya_yolu) }}" target="_blank"
                                                title="{{ $dosya->orijinal_adi }}">
                                                <img src="{{ asset('storage/' . $dosya->dosya_yolu) }}"
                                                    class="h-8 w-8 rounded-md object-cover border border-gray-300"
                                                    alt="Önizleme">
                                            </a>
                                        @empty
                                            <span class="text-xs text-gray-400">Resim Yok</span>
                                        @endforelse
                                        @if($imageFiles->count() > 2)
                                            <span
                                                class="text-xs text-gray-400 font-bold ml-1">+{{ $imageFiles->count() - 2 }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Butonlar --}}
                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                                    <a href="{{ route('admin.sikayetler.show', $sikayet) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition"
                                        onclick="event.stopPropagation()">
                                        Detay
                                    </a>
                                    @if($sikayet->iaaProjesi ?? null)
                                        <a href="{{ route('proje.workspace.show', $sikayet->iaaProjesi) }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200 transition"
                                            onclick="event.stopPropagation()">
                                            Proje Alanı
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-sm text-gray-500">
                                Kayıt bulunamadı.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Sayfalama Linkleri --}}
                <div class="px-4 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $sikayetler->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>