@push('pageTitle')
    Arabulucu Listesi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                {{ __('Arabulucu Listesi ve Performans') }}
            </h2>
            
            {{-- BUTON GRUBU (YAN YANA) --}}
            <div class="flex gap-2">
                {{-- 1. İŞLEM GEÇMİŞİ BUTONU (Sadece Superadmin) --}}
                @if(auth()->user()->hasRole('Superadmin'))
                    <a href="{{ route('admin.arabulucular.logs') }}" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded-lg shadow-md transition flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        İşlem Geçmişi
                    </a>
                @endif

                {{-- 2. YENİ EKLE BUTONU --}}
                <a href="{{ route('admin.arabulucular.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Yeni Arabulucu Ekle
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- İSTATİSTİK KARTLARI --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Toplam Arabulucu --}}
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Toplam Arabulucu</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $arabulucular->total() }}</p>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded-full text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>

                {{-- Toplam Dosya --}}
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Dağıtılan Toplam Dosya</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalCases }}</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-full text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>

                {{-- Ortalama --}}
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Ortalama Dosya / Kişi</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $arabulucular->total() > 0 ? number_format($totalCases / $arabulucular->total(), 1) : 0 }}
                        </p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-full text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>
            </div>

            {{-- LİSTE TABLOSU --}}
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Arabulucu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İletişim & Konum</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Atanan Dosya</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sistem Yükü (%)</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($arabulucular as $arabulucu)
                                @php
                                    $yuzde = $totalCasesCurrentYear > 0 ? ($arabulucu->current_year_count / $totalCasesCurrentYear) * 100 : 0;
                                    $barColor = 'bg-blue-500';
                                    if($yuzde > 20) $barColor = 'bg-yellow-500';
                                    if($yuzde > 40) $barColor = 'bg-red-500';
                                @endphp
                                <tr class="hover:bg-gray-50 transition duration-150 {{ !$arabulucu->is_active ? 'bg-gray-50 opacity-75' : '' }}">
                                    
                                    {{-- İSİM VE DURUM --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <a href="{{ route('admin.arabulucular.show', $arabulucu->id) }}" class="h-10 w-10 rounded-full {{ $arabulucu->is_active ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center font-bold border hover:bg-opacity-80 transition">
                                                    {{ substr($arabulucu->name, 0, 1) }}
                                                </a>
                                            </div>
                                            <div class="ml-4">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('admin.arabulucular.show', $arabulucu->id) }}" class="text-sm font-bold {{ $arabulucu->is_active ? 'text-indigo-600 hover:text-indigo-900' : 'text-gray-600 hover:text-gray-900' }} hover:underline">
                                                        {{ $arabulucu->name }}
                                                    </a>
                                                    
                                                    {{-- === AKTİF/PASİF BUTONU (FORM) === --}}
                                                    <form action="{{ route('admin.arabulucular.toggleStatus', $arabulucu->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" 
                                                            class="px-2 py-0.5 text-[10px] leading-4 font-bold rounded-full border transition-colors duration-200 cursor-pointer shadow-sm
                                                            {{ $arabulucu->is_active 
                                                                ? 'bg-green-100 text-green-800 border-green-200 hover:bg-green-200' 
                                                                : 'bg-gray-100 text-gray-600 border-gray-300 hover:bg-gray-200' }}" 
                                                            title="Durumu değiştirmek için tıklayın">
                                                            {{ $arabulucu->is_active ? 'AKTİF' : 'PASİF' }}
                                                        </button>
                                                    </form>
                                                    {{-- ============================== --}}

                                                </div>
                                                <div class="text-xs text-gray-500">Sicil: {{ $arabulucu->sicil_no }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- İLETİŞİM --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $arabulucu->sehir }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $arabulucu->telefon ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- DOSYA SAYISI --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-lg font-bold text-gray-800">{{ $arabulucu->total_cases }}</span>
                                            <span class="text-[10px] text-gray-500 font-mono bg-gray-100 px-2 py-0.5 rounded border border-gray-200" title="Kapalı / Açık">
                                                <span class="text-green-600 font-bold">{{ $arabulucu->closed_cases_count }}</span>
                                                <span class="text-gray-400 mx-1">/</span>
                                                <span class="text-orange-600 font-bold">{{ $arabulucu->open_cases_count }}</span>
                                            </span>
                                        </div>
                                    </td>

                                    {{-- SİSTEM YÜKÜ --}}
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                                            <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $yuzde }}%"></div>
                                        </div>
                                        <div class="flex justify-between text-[10px]">
                                            <span class="text-gray-400">{{ $currentYear }} Yılı Payı</span>
                                            <span class="text-gray-600 font-bold">%{{ number_format($yuzde, 1) }}</span>
                                        </div>
                                    </td>

                                    {{-- İŞLEMLER --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.arabulucular.edit', $arabulucu->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition" title="Düzenle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </a>
                                            
                                            <form action="{{ route('admin.arabulucular.destroy', $arabulucu->id) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Sil">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                        <p class="mt-2 text-sm font-medium text-gray-900">Henüz arabulucu eklenmemiş.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t border-gray-200 bg-gray-50">
                    {{ $arabulucular->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>