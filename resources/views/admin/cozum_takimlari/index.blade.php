<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
             <div class="p-2 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg shadow">
                 {{-- İkon rengi de header ile uyumlu olsun --}}
                 <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                 </svg>
             </div>
             <div>
                <h2 class="font-bold text-2xl md:text-3xl text-gray-800">
                    {{ __('Çözüm Takımları Yönetimi') }}
                </h2>
                <p class="text-sm md:text-base text-gray-600 mt-1">Şikayet çözüm takımlarını ve liderlerini yönetin</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 md:py-12 bg-gradient-to-br from-gray-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Başarı/Hata Mesajları --}}
            @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fade-in">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm md:text-base text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
             @if(session('error'))
                <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fade-in">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm md:text-base text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden border border-gray-200/50">

                {{-- Header Section (RENK DÜZELTİLDİ) --}}
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-4 md:p-6">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <svg class="w-6 h-6 md:w-7 md:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg md:text-xl font-bold text-white">Çözüm Takımları</h3>
                                <p class="text-xs md:text-sm text-blue-100 mt-0.5">Toplam {{ $cozumTakimlari->count() }} takım</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.cozum-takimlari.create') }}"
                           class="inline-flex items-center justify-center gap-2 px-4 md:px-5 py-2.5 md:py-3 bg-white text-blue-600 font-semibold rounded-lg shadow-lg hover:shadow-xl hover:bg-blue-50 transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="text-sm md:text-base">Yeni Takım Ekle</span>
                        </a>
                    </div>
                </div>

                {{-- Table Section - Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Takım Adı</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Lider</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Oluşturulma Tarihi</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($cozumTakimlari as $takim)
                                <tr class="hover:bg-blue-50/30 transition-colors duration-150 group"> {{-- Hover rengi maviye döndü --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-semibold text-sm"> {{-- İkon rengi maviye döndü --}}
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $takim->ad }}</span> {{-- Hover rengi maviye döndü --}}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($takim->lider)
                                            <div class="flex items-center gap-2">
                                                 {{-- Lider ikonu rengi maviye döndü --}}
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-cyan-500 flex items-center justify-center text-white font-semibold text-xs shadow">
                                                    {{ strtoupper(substr($takim->lider->name, 0, 2)) }}
                                                </div>
                                                <span class="text-sm font-medium text-gray-700">{{ $takim->lider->name }}</span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                                Atanmamış
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2 text-sm text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $takim->created_at->format('d.m.Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end items-center gap-2">
                                            {{-- Edit Link (Doğru parametre adı ile) --}}
                                            <a href="{{ route('admin.cozum-takimlari.edit', ['cozumTakimi' => $takim->id]) }}"  {{-- <--- DÜZELTİLDİ --}}
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium text-sm transition-colors duration-150 transform hover:scale-105">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Düzenle
                                            </a>
                                            {{-- Silme Formu (Doğru parametre adı ile) --}}
                                            <form action="{{ route('admin.cozum-takimlari.destroy', ['cozumTakimi' => $takim->id]) }}" method="POST" onsubmit="return confirm('Bu takımı silmek istediğinizden emin misiniz? Takıma atanmış şikayet varsa silinemez.');" class="inline"> {{-- <--- DÜZELTİLDİ --}}
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 font-medium text-sm transition-colors duration-150 transform hover:scale-105">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Sil
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="p-3 bg-gray-100 rounded-full">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-gray-600 font-medium">Henüz oluşturulmuş bir çözüm takımı bulunmamaktadır</p>
                                                <p class="text-gray-400 text-sm mt-1">Yeni bir takım ekleyerek başlayın</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Card View - Mobile --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($cozumTakimlari as $takim)
                        <div class="p-4 hover:bg-blue-50/30 transition-colors duration-150"> {{-- Hover rengi maviye döndü --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold text-sm flex-shrink-0 shadow"> {{-- İkon rengi maviye döndü --}}
                                        {{ $loop->iteration }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-base truncate">{{ $takim->ad }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">Çözüm Takımı</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2.5 mb-3 pl-13">
                                {{-- Lider Bilgisi --}}
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-500">Lider</p>
                                        @if($takim->lider)
                                            <div class="flex items-center gap-2 mt-1">
                                                 {{-- Lider ikonu rengi maviye döndü --}}
                                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-cyan-500 flex items-center justify-center text-white font-semibold text-xs shadow">
                                                    {{ strtoupper(substr($takim->lider->name, 0, 2)) }}
                                                </div>
                                                <span class="text-sm text-gray-700 font-medium truncate">{{ $takim->lider->name }}</span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 mt-1">
                                                Atanmamış
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Tarih Bilgisi --}}
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Oluşturulma</p>
                                        <p class="text-sm text-gray-700">{{ $takim->created_at->format('d.m.Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2 pl-13 pt-3 border-t border-gray-100">
                                {{-- Edit Link (Doğru parametre adı ile) --}}
                                <a href="{{ route('admin.cozum-takimlari.edit', ['cozumTakimi' => $takim->id]) }}" {{-- <--- DÜZELTİLDİ --}}
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium text-sm transition-colors duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Düzenle
                                </a>
                                {{-- Silme Formu (Doğru parametre adı ile) --}}
                                <form action="{{ route('admin.cozum-takimlari.destroy', ['cozumTakimi' => $takim->id]) }}" method="POST" onsubmit="return confirm('Bu takımı silmek istediğinizden emin misiniz? Takıma atanmış şikayet varsa silinemez.');" class="flex-1"> {{-- <--- DÜZELTİLDİ --}}
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 font-medium text-sm transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Sil
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="p-3 bg-gray-100 rounded-full">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-medium">Henüz oluşturulmuş bir çözüm takımı bulunmamaktadır</p>
                                    <p class="text-gray-400 text-sm mt-1">Yeni bir takım ekleyerek başlayın</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

