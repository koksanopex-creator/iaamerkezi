<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('İşlem Geçmişi ve Denetim') }}
            </h2>
            <a href="{{ route('admin.arabuluculuk.tanim.anlasmaMaddeleri') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition">
                &larr; Geri Dön
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FİLTRELEME ALANI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{ showFilters: true }">
                <div class="flex justify-between items-center mb-4 cursor-pointer" @click="showFilters = !showFilters">
                    <h3 class="font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filtreleme Seçenekleri
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>

                <div x-show="showFilters" x-transition>
                    <form action="{{ route('admin.arabuluculuk.tanim.showAllLogs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        
                        {{-- Kullanıcı Seçimi --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kullanıcı</label>
                            <select name="user_id" class="w-full border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tümü</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- İşlem Türü --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">İşlem Türü</label>
                            <select name="islem_turu" class="w-full border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tümü</option>
                                <option value="EKLEME" {{ request('islem_turu') == 'EKLEME' ? 'selected' : '' }}>EKLEME</option>
                                <option value="DÜZENLEME" {{ request('islem_turu') == 'DÜZENLEME' ? 'selected' : '' }}>DÜZENLEME</option>
                                <option value="SİLME" {{ request('islem_turu') == 'SİLME' ? 'selected' : '' }}>SİLME</option>
                            </select>
                        </div>

                        {{-- Kelime Arama --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">İçerik Ara</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="w-full border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Madde içeriği...">
                        </div>

                        {{-- Tarih Aralığı --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tarih Aralığı</label>
                            <div class="flex gap-2">
                                <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full border-gray-300 rounded-lg text-sm">
                                <span class="text-gray-400 self-center">-</span>
                                <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>

                        {{-- Butonlar --}}
                        <div class="md:col-span-5 flex justify-end gap-2 mt-2">
                            <a href="{{ route('admin.arabuluculuk.tanim.showAllLogs') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 transition">Temizle</a>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow">Filtrele</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABLO ALANI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="p-6">
                    
                    <div class="flex justify-between mb-4">
                        <span class="text-sm text-gray-500">Toplam <strong>{{ $logs->total() }}</strong> kayıt bulundu.</span>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">Sıra No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48">Kullanıcı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">İşlem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">IP Adresi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    {{-- 
                                        SIRA NUMARASI MANTIĞI:
                                        Toplam Kayıt - ((Şuanki Sayfa - 1) * Sayfa Başına Adet) - Döngü İndeksi
                                        Örnek: 100 kayıt var. İlk satır (en yeni) 100 numara olmalı.
                                    --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-gray-400">
                                        {{ $logs->total() - ($logs->firstItem() + $loop->index - 1) }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('d.m.Y H:i:s') }}
                                        <div class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                        {{ $log->user->name ?? 'Silinmiş Kullanıcı' }}
                                        <div class="text-[10px] text-gray-400 font-normal">{{ $log->user->email ?? '' }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-bold rounded border
                                            {{ $log->islem_turu == 'SİLME' ? 'bg-red-50 text-red-700 border-red-200' : 
                                              ($log->islem_turu == 'DÜZENLEME' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                              'bg-green-50 text-green-700 border-green-200') }}">
                                            {{ $log->islem_turu }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-sm text-gray-600 break-words">
                                        {{ $log->detay }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-mono">
                                        {{ $log->ip_adresi ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Kayıt bulunamadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Sayfalama Linkleri --}}
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>