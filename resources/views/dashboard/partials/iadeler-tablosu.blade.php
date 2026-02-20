<div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 border border-gray-100 mt-8">
    <div
        class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between flex-wrap gap-4">
        <div
            class="px-6 py-4 border-b border-gray-100 bg-red-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    Şikayet Kaynaklı İadeler
                </h3>
                @if(isset($iadeToplamlari) && count($iadeToplamlari) > 0)
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Toplam:</span>
                        @foreach($iadeToplamlari as $birim => $miktar)
                            <span
                                class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded border border-red-200 shadow-sm">
                                {{ number_format($miktar, 0, ',', '.') }} {{ $birim }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-red-600/70">Müşteri şikayetlerine bağlı iade kayıtları</p>
                @endif
            </div>

            <form method="GET" class="flex items-center gap-2">
                <input type="date" name="return_start_date" value="{{ request('return_start_date') }}"
                    class="text-xs border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                <span class="text-gray-400">-</span>
                <input type="date" name="return_end_date" value="{{ request('return_end_date') }}"
                    class="text-xs border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filtrele
                </button>
            </form>
        </div>

        {{-- Filtre Formu --}}
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="relative">
                <input type="date" name="return_start_date" value="{{ request('return_start_date') }}"
                    class="pl-3 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm"
                    placeholder="Başlangıç">
            </div>
            <span class="text-gray-400">-</span>
            <div class="relative">
                <input type="date" name="return_end_date" value="{{ request('return_end_date') }}"
                    class="pl-3 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm"
                    placeholder="Bitiş">
            </div>
            <button type="submit"
                class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                    </path>
                </svg>
                Filtrele
            </button>
            @if(request('return_start_date') || request('return_end_date'))
                <a href="{{ route('dashboard') }}"
                    class="px-3 py-1.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Temizle
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İade
                        Tarihi</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri /
                        Bölüm</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şikayet &
                        Proje</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün /
                        Sebep</th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İade /
                        Toplam Miktar</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($iadeVerileri as $iade)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <div class="font-medium text-gray-900">
                                {{ $iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : $iade->created_at->format('d.m.Y') }}
                            </div>
                            <div class="text-xs text-gray-400">{{ $iade->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900 line-clamp-1"
                                    title="{{ $iade->musteriSikayeti->musteri_adi ?? '-' }}">
                                    {{ $iade->musteriSikayeti->musteri_adi ?? 'Belirtilmemiş' }}
                                </span>
                                @if($iade->musteriSikayeti->sikayetKategori && $iade->musteriSikayeti->sikayetKategori->bolum)
                                    <span
                                        class="text-xs inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium w-fit">
                                        {{ $iade->musteriSikayeti->sikayetKategori->bolum->ad }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium line-clamp-1"
                                title="{{ $iade->musteriSikayeti->musteri_sikayet_konusu ?? '' }}">
                                {{ $iade->musteriSikayeti->musteri_sikayet_konusu ?? '-' }}
                            </div>

                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                {{-- Şikayet Linki --}}
                                <a href="{{ route('admin.sikayetler.show', $iade->musteri_sikayeti_id) }}"
                                    class="text-[10px] bg-red-50 text-red-600 px-2 py-0.5 rounded border border-red-100 hover:bg-red-100 transition-colors flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Şikayet #{{ $iade->musteriSikayeti->id }}
                                </a>

                                {{-- Proje Linki (Varsa) --}}
                                @if($iade->musteriSikayeti->iaaProjesi)
                                    <a href="{{ route('proje.workspace.show', $iade->musteriSikayeti->iaaProjesi->id) }}"
                                        class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded border border-indigo-100 hover:bg-indigo-100 transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        Proje #{{ $iade->musteriSikayeti->iaaProjesi->id }}
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $iade->urun_turu }}</div>
                            <div class="text-xs text-red-500">{{ $iade->iade_sebebi }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex flex-col items-end">
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                    {{ number_format($iade->miktar, 0, ',', '.') }} {{ $iade->birim }}
                                </span>
                                @if($iade->toplam_parti_miktari)
                                    <span class="text-xs text-gray-500 mt-1 font-medium">
                                        / {{ number_format($iade->toplam_parti_miktari, 0, ',', '.') }} {{ $iade->birim }}
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

    @if(isset($iadeVerileri) && $iadeVerileri instanceof \Illuminate\Pagination\LengthAwarePaginator && $iadeVerileri->count() > 0)
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $iadeVerileri->links() }}
        </div>
    @endif
</div>