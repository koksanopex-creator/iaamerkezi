{{-- Bu tablo, durumu "Tamamlandı" olan son 5 projeyi listeler --}}
<div class="bg-gradient-to-br from-gray-50 via-white to-gray-100 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    <div class="p-6 sm:p-8 text-gray-900">
        {{-- Başlık ve Sayaç --}}
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-2 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full"></div>
            <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
                {{ $title }}
                <span class="inline-flex items-center justify-center w-8 h-8 ml-2 text-sm font-semibold text-green-700 bg-green-100 rounded-full ring-2 ring-green-200">
                    {{ $iaas->count() }}
                </span>
            </h3>
        </div>

        {{-- Tablo --}}
        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
            <table class="block sm:table min-w-full">
                <thead class="hidden sm:table-header-group">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200 bg-gray-50/50">
                        {{-- 1. SIRA NO --}}
                        <th class="px-6 py-3 w-12 text-center">#</th>
                        {{-- 2. PUAN --}}
                        <th class="px-6 py-3 text-center">Puan</th>
                        
                        <th class="px-6 py-3">Proje Bilgileri</th>
                        <th class="px-6 py-3">Başlangıç Tarihi</th>
                        <th class="px-6 py-3">Onaylanma Tarihi</th>
                        <th class="px-6 py-3 text-center">Tamamlanma Süresi</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="block sm:table-row-group">
                    @forelse ($iaas as $iaa)
                        <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-gray-50 transition-colors group">
                            
                            {{-- 1. SIRA NO --}}
                            <td class="p-4 align-middle text-center sm:table-cell">
                                <span class="text-lg font-bold text-gray-300 group-hover:text-green-500 transition-colors">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            {{-- 2. PUAN --}}
                            <td class="p-4 align-middle text-center">
                                @if($iaa->puan)
                                    <div class="relative inline-flex items-center justify-center w-12 h-12 font-bold text-sm text-white bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all">
                                        {{ number_format($iaa->puan, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="inline-flex items-center justify-center w-12 h-12 text-xs font-medium text-gray-400 bg-gray-100 rounded-full border-2 border-dashed border-gray-300">
                                        -
                                    </div>
                                @endif
                            </td>

                            {{-- Proje Bilgisi --}}
                            <td class="p-4 align-middle font-semibold text-gray-800">
                                <div class="flex flex-col">
                                    <span>{{ $iaa->baslik }}</span>
                                    <span class="text-xs text-gray-500 font-normal mt-0.5 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        {{ $iaa->atananTakim->ad ?? 'Takım Atanmamış' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Tarihler --}}
                            <td class="p-4 align-middle text-sm text-gray-600">
                                {{ $iaa->iaaTalebi ? \Carbon\Carbon::parse($iaa->iaaTalebi->start_date)->format('d.m.Y') : '-' }}
                            </td>
                            <td class="p-4 align-middle text-sm text-gray-600">
                                {{ $iaa->onaylanma_tarihi ? \Carbon\Carbon::parse($iaa->onaylanma_tarihi)->format('d.m.Y') : '-' }}
                            </td>

                            {{-- Süre Rozeti --}}
                            <td class="p-4 align-middle text-center">
                                @if($iaa->completion_duration_in_days !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $iaa->completion_duration_in_days }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- BUTONLAR --}}
                            <td class="p-4 align-middle text-right">
                                <div class="flex justify-end items-center gap-3">
                                    
                                    {{-- 1. İNCELE BUTONU --}}
                                    <a href="{{ route('proje.workspace.show', $iaa) }}" 
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 shadow-sm transition-colors whitespace-nowrap"
                                       title="Proje Detaylarını İncele">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        İncele
                                    </a>

                                    {{-- YENİ: ÖNERİ BUTONU --}}
                                    <a href="{{ route('iaa.show', $iaa) }}" target="_blank" 
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-amber-700 bg-white hover:bg-amber-50 border border-amber-200 shadow-sm transition-colors whitespace-nowrap"
                                       title="Orijinal Öneriyi Görüntüle">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Öneri
                                    </a>
                                    
                                    {{-- 2. SUPERADMIN İŞLEMLERİ (GRİ KUTU) --}}
                                    @role('Superadmin')
                                    <div class="flex items-center justify-center bg-gray-50 rounded-md border border-gray-200 p-0.5">
                                        <form action="{{ route('admin.iaa-yonetim.geriAl', $iaa) }}" method="POST" class="inline-flex" onsubmit="return confirm('Bu projeyi tekrar \'Yönetici Onayı Bekliyor\' durumuna almak istediğinizden emin misiniz?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="p-1.5 text-amber-600 hover:bg-amber-100 rounded-md transition-colors"
                                                    title="Projeyi Geri Al (Onay Bekliyor Durumuna Döndür)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                    @endrole

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block sm:table-row">
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <p>Henüz tamamlanmış bir proje bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Tümünü Gör Butonu --}}
        <div class="mt-6 text-right">
            <a href="{{ route('admin.iaa-yonetim.arsiv') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-indigo-600 transition ease-in-out duration-150">
                Arşive Git &rarr;
            </a>
        </div>
    </div>
</div>