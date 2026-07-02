@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Müşteri Hatırlatma Talepleri') }}
    </h2>
@endsection

@push('pageTitle')
    Müşteri Hatırlatma Talepleri | 
@endpush

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Üst Kartlar (İstatistik) --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-10">
            @php
                $stats = [
                    [
                        'label' => 'Toplam Talep', 
                        'val' => $statsData['toplam'], 
                        'icon' => '📊', 
                        'color' => 'bg-indigo-600',
                        'link' => route('admin.sikayet-hatirlatma.index'),
                        'active' => !request('durum') && !request('tekrarlanan')
                    ],
                    [
                        'label' => 'Bekleyen', 
                        'val' => $statsData['bekleyen'], 
                        'icon' => '🟡', 
                        'color' => 'bg-amber-500',
                        'link' => route('admin.sikayet-hatirlatma.index', ['durum' => 'bilgi_girisi_bekleniyor']),
                        'active' => request('durum') == 'bilgi_girisi_bekleniyor'
                    ],
                    [
                        'label' => 'Yanıtlanan', 
                        'val' => $statsData['yanitlanan'], 
                        'icon' => '🔵', 
                        'color' => 'bg-blue-600',
                        'link' => route('admin.sikayet-hatirlatma.index', ['durum' => 'bilgi_girildi']),
                        'active' => request('durum') == 'bilgi_girildi'
                    ],
                    [
                        'label' => 'İkna Oldu', 
                        'val' => $statsData['ikna_oldu'], 
                        'icon' => '🟢', 
                        'color' => 'bg-emerald-600',
                        'link' => route('admin.sikayet-hatirlatma.index', ['durum' => 'musteri_ikna_oldu']),
                        'active' => request('durum') == 'musteri_ikna_oldu'
                    ],
                    [
                        'label' => 'Tekrarlanan', 
                        'val' => $statsData['tekrarlanan'], 
                        'icon' => '🔄', 
                        'color' => 'bg-rose-500',
                        'link' => route('admin.sikayet-hatirlatma.index', ['tekrarlanan' => 1]),
                        'active' => request('tekrarlanan') == 1
                    ],
                ];
            @endphp

            @foreach($stats as $s)
                <a href="{{ $s['link'] }}" class="bg-white rounded-3xl p-6 shadow-sm border {{ $s['active'] ? 'border-indigo-500 ring-2 ring-indigo-50' : 'border-slate-100' }} flex items-center space-x-4 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="w-14 h-14 rounded-2xl {{ $s['color'] }} flex items-center justify-center text-2xl shadow-lg shadow-slate-200">
                        {{ $s['icon'] }}
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ $s['label'] }}</p>
                        <p class="text-2xl font-black text-slate-800">{{ $s['val'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Filtreler ve Tablo --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            
            {{-- Tablo Başlığı ve Filtreleme --}}
            <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase flex items-center gap-3">
                            Hatırlatma Talepleri
                            <span class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full text-xs font-black">{{ $hatirlatmalar->total() }} Toplam</span>
                        </h1>
                        <p class="text-sm text-slate-500 font-medium">Müşteri temsilcilerinden gelen aciliyet bildirimleri</p>
                    </div>

                    <form action="{{ route('admin.sikayet-hatirlatma.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                        <select name="durum" class="bg-white border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:ring-indigo-500 min-w-[150px]">
                            <option value="">Tüm Durumlar</option>
                            <option value="bilgi_girisi_bekleniyor" {{ request('durum') == 'bilgi_girisi_bekleniyor' ? 'selected' : '' }}>Bilgi Bekleniyor</option>
                            <option value="bilgi_girildi" {{ request('durum') == 'bilgi_girildi' ? 'selected' : '' }}>Yanıtlananlar</option>
                            <option value="musteri_ikna_oldu" {{ request('durum') == 'musteri_ikna_oldu' ? 'selected' : '' }}>İkna Olanlar</option>
                        </select>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Müşteri veya konu ara..." 
                                   class="bg-white border-slate-200 rounded-xl text-sm font-bold text-slate-600 focus:ring-indigo-500 pl-10 w-64">
                            <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-xl font-bold transition-all shadow-lg shadow-indigo-100">Filtrele</button>
                        <a href="{{ route('admin.sikayet-hatirlatma.ayarlar') }}" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all" title="Ayarlar">
                            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </a>
                    </form>
                </div>
            </div>

            {{-- Tablo --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    @php
                        function getSortUrl($column) {
                            $direction = request('sort') == $column && request('direction') == 'asc' ? 'desc' : 'asc';
                            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $direction]);
                        }
                        
                        function getSortIcon($column) {
                            if (request('sort') != $column) return '<svg class="w-3 h-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';
                            return request('direction') == 'asc' 
                                ? '<svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>'
                                : '<svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                        }
                    @endphp
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">
                                <a href="{{ getSortUrl('customer') }}" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                                    Müşteri / Şikayet {!! getSortIcon('customer') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4">
                                <a href="{{ getSortUrl('representative') }}" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                                    Temsilci {!! getSortIcon('representative') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4">
                                <a href="{{ getSortUrl('durum') }}" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                                    Durum {!! getSortIcon('durum') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-center">
                                <a href="{{ getSortUrl('hatirlatma_sayisi') }}" class="flex justify-center items-center gap-2 hover:text-indigo-600 transition-colors">
                                    Hatırlatma {!! getSortIcon('hatirlatma_sayisi') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4 text-center">
                                <a href="{{ getSortUrl('yorumlar_count') }}" class="flex justify-center items-center gap-2 hover:text-indigo-600 transition-colors">
                                    Ticket {!! getSortIcon('yorumlar_count') !!}
                                </a>
                            </th>
                            <th class="px-6 py-4">
                                <a href="{{ getSortUrl('created_at') }}" class="flex items-center gap-2 hover:text-indigo-600 transition-colors">
                                    Tarih {!! getSortIcon('created_at') !!}
                                </a>
                            </th>
                            <th class="px-8 py-4 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($hatirlatmalar as $h)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        {{-- LİNK DÜZELTİLDİ: admin/ prefixi kaldırıldı --}}
                                        <a href="{{ url('/musteri-profil/' . $h->musteriSikayeti->customer_id) }}" class="text-xs font-black text-indigo-600 hover:underline mb-1 uppercase tracking-tighter">
                                            {{ $h->musteriSikayeti->customer->name ?? 'Genel' }}
                                        </a>
                                        <span class="text-sm font-bold text-slate-800 line-clamp-1">{{ $h->musteriSikayeti->musteri_sikayet_konusu }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs border border-slate-200">
                                            {{ substr($h->gonderen->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-700">{{ $h->gonderen->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    @php
                                        $statusConfig = [
                                            'bilgi_girisi_bekleniyor' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200/60', 'dot' => 'bg-amber-400', 'label' => 'Bilgi Bekleniyor'],
                                            'bilgi_girildi' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200/60', 'dot' => 'bg-blue-400', 'label' => 'Bilgi Girildi'],
                                            'musteri_ikna_oldu' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200/60', 'dot' => 'bg-emerald-400', 'label' => 'İkna Oldu'],
                                            'kapatildi' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200/60', 'dot' => 'bg-slate-400', 'label' => 'Kapatıldı'],
                                        ];
                                        $cfg = $statusConfig[$h->durum] ?? $statusConfig['kapatildi'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border whitespace-nowrap {{ $cfg['bg'] }} {{ $cfg['text'] }} {{ $cfg['border'] }} shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }} {{ $h->durum == 'bilgi_girisi_bekleniyor' ? 'animate-pulse' : '' }}"></span>
                                        {{ $cfg['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-lg text-xs font-black border border-indigo-100 shadow-sm">
                                        {{ $h->hatirlatma_sayisi }}. Kez
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded-lg text-xs font-bold border border-slate-200">
                                        {{ $h->yorumlar_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-sm text-slate-500 font-medium">
                                    {{ $h->created_at->format('d.m.Y') }}
                                    <span class="block text-[10px] text-slate-400">{{ $h->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.sikayet-hatirlatma.show', $h->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-xs font-black text-slate-700 rounded-xl shadow-sm hover:bg-slate-50 hover:border-slate-300 transition-all group-hover:-translate-x-1">
                                        Detay
                                        <svg class="w-3 h-3 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center opacity-30 grayscale">
                                        <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 012-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <p class="mt-4 font-black uppercase text-xs tracking-widest">Henüz bir hatırlatma talebi yok</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($hatirlatmalar->hasPages())
                <div class="p-8 bg-slate-50/50 border-t border-slate-100">
                    {{ $hatirlatmalar->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
