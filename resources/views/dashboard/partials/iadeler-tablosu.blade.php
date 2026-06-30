<div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 border border-gray-100 mt-8">
    @if(!isset($hideHeader) || !$hideHeader)
    <div class="px-6 py-6 border-b border-gray-100 bg-gradient-to-br from-red-600 to-rose-700 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-2">
                    <div class="bg-white/20 backdrop-blur-md p-3 rounded-2xl shadow-inner border border-white/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-white text-xl tracking-tight uppercase">ŞİKAYET KAYNAKLI İADELER</h3>
                        <p class="text-xs text-red-100 font-medium opacity-80 uppercase tracking-widest">Mali Kayıp ve İade Yönetimi</p>
                    </div>
                </div>
                
                @if(isset($iadeToplamlari) && count($iadeToplamlari) > 0)
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Genel Toplam:</span>
                        @foreach($iadeToplamlari as $birim => $miktar)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black bg-white/10 backdrop-blur-md text-white border border-white/20 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-red-300 mr-2 animate-pulse"></span>
                                {{ number_format($miktar, 0, ',', '.') }} {{ $birim }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm w-full lg:w-auto">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4">
                    <input type="hidden" name="active_dashboard" value="{{ $activeDashboard ?? '' }}">
                    
                    <div class="flex flex-col sm:flex-row items-center gap-2 flex-1">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="return_search" value="{{ request('return_search') }}"
                                class="w-full text-xs border-gray-200 rounded-lg focus:ring-red-500 focus:border-red-500 py-2.5 pl-9 pr-3 shadow-inner bg-gray-50/50"
                                placeholder="Müşteri veya şikayet ara...">
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative w-full">
                                <input type="date" name="return_start_date" value="{{ request('return_start_date') }}"
                                    class="w-full text-xs border-gray-200 rounded-lg focus:ring-red-500 focus:border-red-500 py-2.5 px-3 shadow-inner bg-gray-50/50"
                                    placeholder="Başlangıç">
                            </div>
                            <span class="text-gray-300">-</span>
                            <div class="relative w-full">
                                <input type="date" name="return_end_date" value="{{ request('return_end_date') }}"
                                    class="w-full text-xs border-gray-200 rounded-lg focus:ring-red-500 focus:border-red-500 py-2.5 px-3 shadow-inner bg-gray-50/50"
                                    placeholder="Bitiş">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="flex-1 sm:flex-none bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg text-xs font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Filtrele
                        </button>
                        
                        @if(request('return_start_date') || request('return_end_date') || request('return_search'))
                            <a href="{{ route('dashboard', array_filter(['active_dashboard' => $activeDashboard ?? null])) }}"
                                class="p-2.5 bg-gray-50 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-gray-100 flex items-center justify-center"
                                title="Filtreyi Temizle">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif


    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50/50">
                    <th scope="col"
                        class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">İade Tarihi</th>
                    <th scope="col"
                        class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Müşteri / Bölüm</th>
                    <th scope="col"
                        class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Şikayet & Proje</th>
                    <th scope="col"
                        class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Ürün / Sebep</th>
                    <th scope="col"
                        class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">İade Miktarı</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @php $iadeLimit = $iadeLimit ?? null; @endphp
                @forelse($iadeVerileri as $iade)
                    <tr class="hover:bg-red-50/30 transition-all group border-b border-gray-50 last:border-0"
                        @if($iadeLimit && $loop->index >= $iadeLimit)
                            x-show="iadeShowAll"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                        @endif
                    >
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                            <div class="font-bold text-gray-800">
                                {{ $iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : $iade->created_at->format('d.m.Y') }}
                            </div>
                            <div class="text-[10px] text-gray-400 font-medium">{{ $iade->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                @if(isset($iade->musteriSikayeti->customer_id))
                                    <a href="{{ route('musteri.profil.show', $iade->musteriSikayeti->customer_id) }}" 
                                       class="text-sm font-bold text-gray-900 line-clamp-1 hover:text-red-600 transition-all flex items-center gap-1 group/link"
                                       title="{{ $iade->musteriSikayeti->musteri_adi ?? '-' }}">
                                        {{ $iade->musteriSikayeti->musteri_adi ?? 'Belirtilmemiş' }}
                                        <svg class="w-3 h-3 opacity-0 group-hover/link:opacity-100 transform translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                @else
                                    <span class="text-sm font-semibold text-gray-900 line-clamp-1"
                                        title="{{ $iade->musteriSikayeti->musteri_adi ?? '-' }}">
                                        {{ $iade->musteriSikayeti->musteri_adi ?? 'Belirtilmemiş' }}
                                    </span>
                                @endif
                                @if($iade->musteriSikayeti->sikayetKategori && $iade->musteriSikayeti->sikayetKategori->bolum)
                                    <span
                                        class="text-[10px] inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 font-bold w-fit border border-blue-100">
                                        {{ $iade->musteriSikayeti->sikayetKategori->bolum->ad }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm text-gray-800 font-bold line-clamp-1 group-hover:text-red-700 transition-colors"
                                title="{{ $iade->musteriSikayeti->musteri_sikayet_konusu ?? '' }}">
                                {{ $iade->musteriSikayeti->musteri_sikayet_konusu ?? '-' }}
                            </div>

                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                {{-- Şikayet Linki --}}
                                <a href="{{ route('admin.sikayetler.show', $iade->musteri_sikayeti_id) }}"
                                    class="text-[10px] font-bold bg-white text-gray-500 px-2 py-1 rounded-lg border border-gray-100 hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all shadow-sm flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    #{{ $iade->musteriSikayeti->id }}
                                </a>

                                {{-- Proje Linki (Varsa) --}}
                                @if($iade->musteriSikayeti->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $iade->musteriSikayeti->iaaProjesi->id) }}"
                                        class="text-[10px] font-bold bg-white text-gray-500 px-2 py-1 rounded-lg border border-gray-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        Prj #{{ $iade->musteriSikayeti->iaaProjesi->id }}
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm text-gray-800 font-medium">{{ $iade->urun_turu }}</div>
                            <div class="text-[11px] text-red-500 font-bold bg-red-50 px-2 py-0.5 rounded-full w-fit mt-1">{{ $iade->iade_sebebi }}</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            <div class="flex flex-col items-end">
                                <span
                                    class="px-4 py-1.5 inline-flex text-sm leading-5 font-black rounded-xl bg-gradient-to-r from-red-50 to-rose-50 text-red-700 border border-red-100 shadow-sm transition-all group-hover:scale-105 group-hover:shadow-md">
                                    {{ number_format($iade->miktar, 0, ',', '.') }} {{ $iade->birim }}
                                </span>
                                @if($iade->toplam_parti_miktari)
                                    <span class="text-[10px] text-gray-400 mt-1.5 font-bold uppercase tracking-tighter">
                                        Parti: {{ number_format($iade->toplam_parti_miktari, 0, ',', '.') }} {{ $iade->birim }}
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                                <span class="font-medium">Görüntülenecek iade kaydı bulunamadı.</span>
                                <span class="text-xs mt-1">Filtre kriterlerinizi kontrol ediniz.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Devamını Yükle / Gizle Butonları --}}
    @if($iadeLimit && count($iadeVerileri) > $iadeLimit)
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-center gap-3">
            {{-- Devamını Yükle --}}
            <button x-show="!iadeShowAll" @click="iadeShowAll = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-50 to-rose-50 text-red-700 rounded-xl text-xs font-bold border border-red-100 hover:from-red-100 hover:to-rose-100 hover:shadow-md transition-all duration-300 group/btn">
                <svg class="w-4 h-4 text-red-500 group-hover/btn:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
                Devamını Yükle
                <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded-lg text-[10px] font-black">+{{ count($iadeVerileri) - $iadeLimit }}</span>
            </button>

            {{-- Devamını Gizle --}}
            <button x-show="iadeShowAll" x-cloak @click="iadeShowAll = false"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold border border-gray-200 hover:bg-gray-100 hover:shadow-md transition-all duration-300 group/btn">
                <svg class="w-4 h-4 text-gray-400 group-hover/btn:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
                Devamını Gizle
            </button>
        </div>
    @endif

    @if(isset($iadeVerileri) && $iadeVerileri instanceof \Illuminate\Pagination\LengthAwarePaginator && $iadeVerileri->count() > 0 && !isset($iadeLimit))
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $iadeVerileri->links() }}
        </div>
    @endif
</div>