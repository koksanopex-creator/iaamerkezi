<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    {{ $user->name }} - Puan Dökümü
                </h2>
                <a href="{{ route('puan-durumu') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border-2 border-gray-300 rounded-lg font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 transform hover:scale-105 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Liderlik Tablosuna Dön
                </a>
            </div>
            <p class="text-sm md:text-base text-gray-600">Kullanıcının puan geçmişini ve detaylarını görüntüleyin</p>
        </div>
    </x-slot>

    <div class="py-4 md:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Kullanıcı Bilgi Kartı -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6">
                        <!-- Kullanıcı Bilgileri -->
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-2xl md:text-3xl shadow-lg">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-4 border-white"></div>
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-1">{{ $user->name }}</h3>
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm md:text-base">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Toplam Puan Badge -->
                        <div class="text-center sm:text-right bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-4 md:p-6 shadow-lg min-w-[160px]">
                            <p class="text-indigo-100 text-xs md:text-sm font-semibold uppercase tracking-wider mb-1">Toplam Puan</p>
                            <div class="flex items-center justify-center sm:justify-end gap-2">
                                <svg class="w-6 h-6 md:w-8 md:h-8 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{-- Ebru'nun puanı artık burada doğru görünmeli --}}
                                <p class="text-3xl md:text-4xl font-bold text-white">{{ number_format($user->toplam_puan, 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Puan Dökümü Tablosu -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-4 md:p-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 md:w-7 md:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg md:text-xl font-bold text-white">Kazanılan Puanların Dökümü</h3>
                            <p class="text-xs md:text-sm text-emerald-100 mt-0.5">Toplam {{ $kazanilanlar->count() }} kayıt</p>
                        </div>
                    </div>
                </div>

                <!-- Table Section - Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Açıklama</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Kazanılan Puan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($kazanilanlar as $kayit)
                                <tr class="hover:bg-emerald-50/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 font-semibold text-sm">
                                            #{{ $kayit['id'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-start gap-2">
                                            <div class="flex-1">
                                                
                                                {{-- === BAŞLIK DÜZELTMESİ: ARTIK TEK BİR LİNK KULLANIYORUZ === --}}
                                                <a href="{{ $kayit['url'] }}" 
                                                   class="{{ $kayit['tip'] == 'Proje' ? 'text-indigo-600 hover:text-indigo-800' : 'text-emerald-600 hover:text-emerald-800' }} font-medium transition-colors duration-150 hover:underline" 
                                                   title="{{ $kayit['baslik'] }}">
                                                    {{ Str::limit($kayit['baslik'], 50) }}
                                                </a>
                                                {{-- ======================================================== --}}

                                                <div class="mt-1">
                                                    {{-- === ETİKET DÜZELTMESİ (GRAFİK@GRAFİK.COM ARTIK DOĞRU GÖRÜNECEK) === --}}
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $kayit['tip'] == 'Proje' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700' }}">
                                                        @if($kayit['tip'] == 'Proje')
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @endif
                                                        {{ $kayit['tip'] }}
                                                    </span>
                                                    {{-- ======================================================== --}}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2 text-sm text-gray-600">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{-- === TARİH DÜZELTMESİ (ARTIK DOLU GELECEK) === --}}
                                            {{ $kayit['tarih'] ? \Carbon\Carbon::parse($kayit['tarih'])->format('d.m.Y H:i') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-100 text-green-700 font-bold text-sm">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                            </svg>
                                            +{{ number_format($kayit['puan'], 0) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="p-3 bg-gray-100 rounded-full">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Bu kullanıcının kazandığı herhangi bir puan kaydı bulunmuyor</p>
                                                <p class="text-gray-400 text-sm mt-1">Henüz puan kazanılmamış</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Card View - Mobile -->
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($kazanilanlar as $kayit)
                        <div class="p-4 hover:bg-emerald-50/50 transition-colors duration-150">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-2 flex-1">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 font-bold text-xs">
                                        #{{ $kayit['id'] }}
                                    </span>
                                    {{-- === ETİKET DÜZELTMESİ (MOBİL) === --}}
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $kayit['tip'] == 'Proje' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700' }}">
                                        @if($kayit['tip'] == 'Proje')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        {{ $kayit['tip'] }}
                                    </span>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-green-100 text-green-700 font-bold text-sm whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                    </svg>
                                    +{{ number_format($kayit['puan'], 0) }}
                                </span>
                            </div>

                            <div class="mb-3">
                                {{-- === LİNK DÜZELTMESİ (MOBİL) === --}}
                                <a href="{{ $kayit['url'] }}" 
                                   class="{{ $kayit['tip'] == 'Proje' ? 'text-indigo-600 hover:text-indigo-800' : 'text-emerald-700 hover:text-emerald-900' }} font-medium text-sm transition-colors duration-150 hover:underline block" 
                                   title="{{ $kayit['baslik'] }}">
                                    {{ Str::limit($kayit['baslik'], 60) }}
                                </a>
                            </div>

                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{-- === TARİH DÜZELTMESİ (MOBİL) === --}}
                                {{ $kayit['tarih'] ? \Carbon\Carbon::parse($kayit['tarih'])->format('d.m.Y H:i') : '-' }}
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="p-3 bg-gray-100 rounded-full">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-medium text-sm">Bu kullanıcının kazandığı herhangi bir puan kaydı bulunmuyor</p>
                                    <p class="text-gray-400 text-xs mt-1">Henüz puan kazanılmamış</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>