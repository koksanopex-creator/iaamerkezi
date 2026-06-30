@push('pageTitle')
    {{ $bolum->ad }} Puan Dökümü | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($bolum->logo_yolu)
                    <img src="{{ asset('storage/'.$bolum->logo_yolu) }}" class="w-12 h-12 rounded-xl object-contain shadow-sm bg-white p-1">
                @else
                    @php
                        $colors = ['from-purple-500 to-indigo-600', 'from-blue-500 to-cyan-600', 'from-emerald-500 to-teal-600', 'from-rose-500 to-pink-600', 'from-amber-500 to-orange-600'];
                        $gradient = $colors[$bolum->id % count($colors)];
                    @endphp
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-white font-black shadow-md text-xl">
                        {{ mb_substr($bolum->ad, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">
                        {{ $bolum->ad }}
                    </h2>
                    <p class="text-xs text-gray-500 font-medium">Bölüm Puan Analizi ve Personel Katkı Dökümü</p>
                </div>
            </div>
            <div class="flex items-center gap-2 no-print">
                <div class="flex bg-white border border-gray-100 rounded-xl p-1 shadow-sm mr-2">
                    <a href="{{ route('bolum-puanlari.export.excel', array_merge(['bolum' => $bolum->id], request()->all())) }}" class="p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Excel İndir">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </a>
                    <a href="{{ route('bolum-puanlari.export.pdf', array_merge(['bolum' => $bolum->id], request()->all())) }}" class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="PDF İndir">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </a>
                    <button onclick="window.print()" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Yazdır">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2m8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2z"></path></svg>
                    </button>
                </div>
                <a href="{{ route('tum-bolum-puanlari', ['start_date' => $start_date, 'end_date' => $end_date]) }}" class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-100 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Analiz Listesine Dön
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        search: '', 
        tab: 'all', 
        limit: 20,
        allUsers: @js($users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'unvan' => $u->unvan ?? 'Personel',
            'is_mavi_yaka' => (bool)$u->is_mavi_yaka,
            'period_puan' => (float)$u->period_puan,
            'contribution_percentage' => (float)$u->contribution_percentage,
            'photo' => $u->profile_photo_path ? asset('storage/'.$u->profile_photo_path) : null,
            'profile_url' => route('profile.show', $u->id),
            'archive_url' => route('profile.puanlar', ['user' => $u->id, 'start_date' => $start_date, 'end_date' => $end_date])
        ])),
        get filteredUsers() {
            return this.allUsers.filter(u => {
                const matchesTab = this.tab === 'all' || (this.tab === 'blue' && u.is_mavi_yaka) || (this.tab === 'white' && !u.is_mavi_yaka);
                const matchesSearch = u.name.toLowerCase().includes(this.search.toLowerCase());
                return matchesTab && matchesSearch;
            });
        },
        get visibleUsers() {
            return this.filteredUsers.slice(0, this.limit);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filtre Bilgi Mesajı -->
            @if(request('start_date') || request('end_date'))
                <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded-r-2xl shadow-sm animate-pulse-slow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-indigo-700">
                                <span class="font-black">FİLTRE AKTİF:</span> 
                                <span class="font-bold underline">{{ \Carbon\Carbon::parse($start_date)->format('d.m.Y') }}</span> ile 
                                <span class="font-bold underline">{{ \Carbon\Carbon::parse($end_date)->format('d.m.Y') }}</span> tarihleri arasındaki performans verileri görüntülenmektedir.
                            </p>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('bolum-puanlari', $bolum->id) }}" class="text-xs font-bold text-indigo-500 hover:text-indigo-700 uppercase tracking-tighter">Filtreyi Kaldır</a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tarih Filtreleme Paneli -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 no-print">
                <form action="{{ route('bolum-puanlari', $bolum->id) }}" method="GET" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Başlangıç -->
                        <div class="relative group">
                            <span class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-purple-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            <label class="absolute -top-2 left-3 bg-white px-1 text-[10px] font-bold text-gray-400">BAŞLANGIÇ</label>
                            <input type="date" name="start_date" value="{{ $start_date }}" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/10 focus:border-purple-500 transition focus:bg-white outline-none">
                        </div>

                        <!-- Bitiş -->
                        <div class="relative group">
                            <span class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-purple-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            <label class="absolute -top-2 left-3 bg-white px-1 text-[10px] font-bold text-gray-400">BİTİŞ</label>
                            <input type="date" name="end_date" value="{{ $end_date }}" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-purple-500/10 focus:border-purple-500 transition focus:bg-white outline-none">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-gray-50">
                        <div class="flex flex-wrap gap-2">
                            @php
                                $quickFilters = [
                                    'Bu Hafta' => [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')],
                                    'Geçen Hafta' => [now()->subWeek()->startOfWeek()->format('Y-m-d'), now()->subWeek()->endOfWeek()->format('Y-m-d')],
                                    'Bu Ay' => [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')],
                                    'Bu Yıl' => [now()->startOfYear()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')],
                                ];
                            @endphp
                            @foreach($quickFilters as $label => $dates)
                                <a href="{{ route('bolum-puanlari', ['bolum' => $bolum->id, 'start_date' => $dates[0], 'end_date' => $dates[1]]) }}" 
                                   class="px-3 py-1.5 rounded-lg text-[10px] font-black tracking-wider uppercase transition border {{ $start_date == $dates[0] && $end_date == $dates[1] ? 'bg-purple-600 border-purple-600 text-white shadow-md' : 'bg-white border-gray-100 text-gray-500 hover:border-purple-200 hover:text-purple-600' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('bolum-puanlari', $bolum->id) }}" class="px-5 py-2 text-xs font-bold text-gray-400 hover:text-gray-600 transition">Temizle</a>
                            <button type="submit" class="px-8 py-2 bg-purple-600 text-white text-xs font-black rounded-xl hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                                Filtrele
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Toplam Puan Geniş Kart -->
                <div class="md:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center text-center">
                    <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center text-purple-600 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Bölüm Toplam Puanı</div>
                    <div class="text-4xl font-black text-purple-600">{{ number_format($totalBolumPuan, 0) }}</div>
                </div>

                <!-- Yaka Ayrımı Kartları -->
                <div class="md:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Yaka Bazlı Katkı Analizi
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Beyaz Yaka -->
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">Beyaz Yaka</span>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ $stats->white_count }} Personel</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-black text-blue-600">{{ number_format($stats->white_puan, 0) }} Puan</span>
                                    <span class="text-[10px] text-gray-400 block font-bold">%{{ $stats->white_percentage }} Katkı</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-500 h-2 rounded-full shadow-sm" style="width: {{ $stats->white_percentage }}%"></div>
                            </div>
                        </div>

                        <!-- Mavi Yaka -->
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">Mavi Yaka</span>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ $stats->blue_count }} Personel</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-black text-amber-600">{{ number_format($stats->blue_puan, 0) }} Puan</span>
                                    <span class="text-[10px] text-gray-400 block font-bold">%{{ $stats->blue_percentage }} Katkı</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-amber-500 h-2 rounded-full shadow-sm" style="width: {{ $stats->blue_percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kategori Bazlı Performans Kartları (Büyükten Küçüğe) -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($categoryBreakdown as $key => $cat)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 relative overflow-hidden transition hover:shadow-md group">
                        @php
                            $iconColors = [
                                'iaa' => 'bg-emerald-50 text-emerald-600',
                                'resol' => 'bg-blue-50 text-blue-600',
                                'suggest' => 'bg-amber-50 text-amber-600',
                                'entry' => 'bg-indigo-50 text-indigo-600',
                                'disc' => 'bg-rose-50 text-rose-600',
                            ];
                            $icons = [
                                'iaa' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                'resol' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.757c.738 0 1.107.892.586 1.414l-7.414 7.414a1 1 0 01-1.414 0L3.414 11.414c-.522-.522-.152-1.414.586-1.414H8V4a1 1 0 011-1h4a1 1 0 011 1v6z"></path></svg>',
                                'suggest' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>',
                                'entry' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
                                'disc' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                            ];
                        @endphp
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $iconColors[$cat['icon']] }} transition group-hover:scale-110">
                                {!! $icons[$cat['icon']] !!}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $cat['label'] }}</span>
                                <span class="text-sm font-black text-gray-800">{{ number_format($cat['score'], 0) }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 bg-gray-100 h-1 rounded-full overflow-hidden mr-2">
                                <div class="h-full bg-indigo-500 opacity-50" style="width: {{ $cat['percentage'] }}%"></div>
                            </div>
                            <span class="text-[9px] font-black text-indigo-500 uppercase tracking-tighter">%{{ $cat['percentage'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Filtreleme Araçları -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Sekmeler -->
                <div class="flex p-1 bg-gray-50 rounded-xl w-fit border border-gray-100">
                    <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2">
                        Tümü <span class="bg-gray-100 text-gray-400 px-1.5 py-0.5 rounded-md text-[10px]" x-text="allUsers.length"></span>
                    </button>
                    <button @click="tab = 'white'" :class="tab === 'white' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2">
                        Beyaz Yaka <span class="bg-blue-50 text-blue-400 px-1.5 py-0.5 rounded-md text-[10px]" x-text="allUsers.filter(u => !u.is_mavi_yaka).length"></span>
                    </button>
                    <button @click="tab = 'blue'" :class="tab === 'blue' ? 'bg-white text-amber-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2">
                        Mavi Yaka <span class="bg-amber-50 text-amber-400 px-1.5 py-0.5 rounded-md text-[10px]" x-text="allUsers.filter(u => u.is_mavi_yaka).length"></span>
                    </button>
                </div>

                <!-- Arama Kutusu -->
                <div class="relative w-full md:w-64 group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400 group-focus-within:text-purple-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Personel ara..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition bg-gray-50/50 group-hover:bg-white">
                </div>
            </div>

            <!-- Personel Listesi -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-20">Sıra</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Personel</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Yaka</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Puan Değeri</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">% Katkı Payı</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <template x-for="(user, index) in visibleUsers" :key="user.id">
                                <tr class="hover:bg-gray-50/50 transition group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-sm" 
                                             :class="index == 0 ? 'bg-amber-100 text-amber-700' : (index == 1 ? 'bg-slate-100 text-slate-600' : (index == 2 ? 'bg-orange-50 text-orange-600' : 'text-gray-400'))">
                                            <span x-text="'#' + (index + 1)"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a :href="user.profile_url" class="flex items-center group/name">
                                            <div class="relative">
                                                <template x-if="user.photo">
                                                    <img class="h-10 w-10 rounded-full object-cover shadow-sm group-hover/name:ring-2 group-hover/name:ring-purple-400 transition" :src="user.photo" alt="">
                                                </template>
                                                <template x-if="!user.photo">
                                                    <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold shadow-sm text-sm group-hover/name:ring-2 group-hover/name:ring-purple-400 transition" x-text="user.name.substring(0,1)"></div>
                                                </template>
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white" :class="user.is_mavi_yaka ? 'bg-amber-500' : 'bg-blue-500'"></div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-gray-900 group-hover/name:text-purple-600 transition" x-text="user.name"></div>
                                                <div class="text-[10px] text-gray-400 font-medium" x-text="user.unvan"></div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase" :class="user.is_mavi_yaka ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600'" x-text="user.is_mavi_yaka ? 'Mavi Yaka' : 'Beyaz Yaka'"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-black text-gray-900" x-text="'+' + parseInt(user.period_puan).toLocaleString()"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-16 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-purple-500 h-1.5 transition-all duration-500" :style="'width: ' + user.contribution_percentage + '%'"></div>
                                            </div>
                                            <span class="text-xs font-bold text-purple-600 w-10 text-right" x-text="'%' + user.contribution_percentage"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a :href="user.archive_url" class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition shadow-sm hover:shadow-md">
                                            Detay Arşivi
                                            <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Sonuç Yok veya Daha Fazla Göster -->
                <div class="p-8 border-t border-gray-50 flex flex-col items-center">
                    <template x-if="filteredUsers.length === 0">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <span class="text-sm text-gray-400 font-medium">Aradığınız kriterlere uygun personel bulunamadı.</span>
                        </div>
                    </template>

                    <template x-if="limit < filteredUsers.length">
                        <button @click="limit += 20" class="flex items-center gap-2 px-6 py-2.5 bg-gray-50 text-gray-600 rounded-xl text-sm font-bold hover:bg-purple-50 hover:text-purple-600 transition border border-gray-100 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            Daha Fazla Personel Göster (<span x-text="filteredUsers.length - limit"></span> kalan)
                        </button>
                    </template>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
