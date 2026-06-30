<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kurul Girdileri Raporu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @unless(auth()->user()->hasRole('Superadmin'))
            <div class="mb-6 bg-white rounded-2xl shadow-lg border border-gray-200/50 overflow-hidden">
                <div class="px-6 py-5">
                    <h3 class="text-lg font-bold text-gray-900">
                        Benim İstatistiklerim
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-200/70">
                    <div class="bg-white px-4 py-5 text-center">
                        <p class="text-sm font-medium text-gray-500">Toplam Girdiğim Şikayet</p>
                        <p class="mt-1 text-3xl font-bold text-blue-600">{{ $stats_kisisel['toplam_benim_girdiklerim'] }}</p>
                    </div>
                    <div class="bg-white px-4 py-5 text-center">
                        <p class="text-sm font-medium text-gray-500">Girdiklerimden İşlemde Olan</p>
                        <p class="mt-1 text-3xl font-bold text-cyan-600">{{ $stats_kisisel['islemde_benim_girdiklerim'] }}</p>
                    </div>
                    <div class="bg-white px-4 py-5 text-center">
                        <p class="text-sm font-medium text-gray-500">Girdiklerimden Çözülen</p>
                        <p class="mt-1 text-3xl font-bold text-green-600">{{ $stats_kisisel['cozulen_benim_girdiklerim'] }}</p>
                    </div>
                </div>
            </div>
            @endunless
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                
                <div class="px-6 py-5">
                    <h3 class="text-lg font-bold text-gray-900">
                        @if($selectedUserId == 'all')
                            Tüm Kurul Girdileri İçin İstatistikler
                        @else
                            Filtrelenen Kişi: {{ $kurulUyeleri->find($selectedUserId)->name }}
                        @endif
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-200/70">
                    <div class="bg-white px-4 py-5 text-center">
                        <p class="text-sm font-medium text-gray-500">Toplam Filtrelenen Şikayet</p>
                        <p class="mt-1 text-3xl font-bold text-blue-600">{{ $stats_filtrelenmis['toplam'] }}</p>
                    </div>
                    <div class="bg-white px-4 py-5 text-center">
                        <p class="text-sm font-medium text-gray-500">İşlemde Olan (Filtrelenmiş)</p>
                        <p class="mt-1 text-3xl font-bold text-cyan-600">{{ $stats_filtrelenmis['islemde'] }}</p>
                    </div>
                    <div class="bg-white px-4 py-5 text-center">
                        <p class="text-sm font-medium text-gray-500">Çözüme Ulaşan (Filtrelenmiş)</p>
                        <p class="mt-1 text-3xl font-bold text-green-600">{{ $stats_filtrelenmis['cozulen'] }}</p>
                    </div>
                </div>
                @if($stats_filtrelenmis['kategoriler']->count() > 0)
                <div class="px-6 py-4 border-t border-gray-200/70">
                    <h4 class="text-md font-semibold text-gray-800 mb-3">Filtrelenmiş Kategori Dağılımı</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($stats_filtrelenmis['kategoriler'] as $kategori)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                {{ $kategori->ad }}:
                                <span class="font-bold ml-1.5">{{ $kategori->toplam }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="p-4 md:p-6 bg-gray-50/50 border-t border-gray-200/70">
                    <form method="GET" action="{{ route('admin.sikayetler.kurulGirdileri') }}">
                        <label for="kullanici_id" class="block text-sm font-medium text-gray-700 mb-1.5">Girdiyi Yapan Kurul Üyesi</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                            <select name="kullanici_id" id="kullanici_id" class="block w-full max-w-md rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                
                                @if(auth()->user()->hasRole('Superadmin'))
                                    <option value="all" @if($selectedUserId == 'all') selected @endif>Tüm Kurul Girdileri</option>
                                @else
                                    <option value="{{ auth()->id() }}" @if($selectedUserId == auth()->id()) selected @endif>Sadece Benim Girdiklerim</option>
                                    <option value="all" @if($selectedUserId == 'all') selected @endif>Tüm Kurul Girdileri</option>
                                @endif
                                
                                @foreach($kurulUyeleri as $uye)
                                    @if($uye->id != auth()->id()) 
                                        <option value="{{ $uye->id }}" @if($selectedUserId == $uye->id) selected @endif>{{ $uye->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 transition">
                                Filtrele
                            </button>
                            
                            <button type="button" onclick="history.back()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-gray-700 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                                Geri Dön
                            </button>
                            </div>
                    </form>
                </div>
                <div class="space-y-4 p-4 md:p-6">
                    @forelse ($sikayetler as $sikayet)
                        <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm hover:shadow-lg transition-all duration-300 p-4 md:p-6 group">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 gap-3">
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-sm shadow-md">
                                        #{{ $sikayet->id }}
                                    </span>
                                    <div class="font-semibold text-lg text-gray-900">{{ $sikayet->musteri_adi }}</div>
                                </div>
                                {!! $sikayet->musteri_durum_badge !!}
                            </div>
                            <p class="text-base text-gray-700 mb-4 sm:ml-14 font-medium" title="{{ $sikayet->musteri_sikayet_konusu }}">
                                {{ Str::limit($sikayet->musteri_sikayet_konusu, 60) }}
                            </p>
                            <div class="sm:ml-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-3 text-sm bg-gray-50/70 rounded-lg p-3 border border-gray-200/60">
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                    <span class="font-medium">Kategori:</span>
                                    <span class="font-semibold text-gray-800">{{ $sikayet->sikayetKategori->ad ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span class="font-medium">Takım:</span>
                                    <span class="font-semibold text-gray-800">{{ $sikayet->cozumTakimi->ad ?? 'Atanmadı' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="font-medium">Ekleyen:</span>
                                    <span class="font-semibold text-gray-800">{{ $sikayet->olusturanKurulUyesi->name ?? 'Sistem' }}</span>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap justify-end gap-2 pt-4 border-t border-gray-200/70">
                                <a href="{{ route('admin.sikayetler.show', $sikayet) }}"
                                        class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-blue-700 bg-gradient-to-r from-blue-50 to-cyan-50 hover:from-blue-100 hover:to-cyan-100 border border-blue-200/70 hover:border-blue-300 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Detay
                                </a>
                                @can('update', $sikayet)
                                    <a href="{{ route('admin.sikayetler.edit', $sikayet) }}"
                                            class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-indigo-700 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 border border-indigo-200/70 hover:border-indigo-300 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Düzenle
                                    </a>
                                @endcan
                                @can('delete', $sikayet)
                                    <form action="{{ route('admin.sikayetler.destroy', $sikayet) }}" method="POST"
                                          onsubmit="return confirm('Bu şikayeti silmek istediğinizden emin misiniz?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-bold text-red-700 bg-gradient-to-r from-red-50 to-rose-50 hover:from-red-100 hover:to-rose-100 border border-red-200/70 hover:border-red-300 transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Sil
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center">
                           <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-slate-100 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-gray-600 font-semibold text-lg">Bu filtreye uygun şikayet bulunamadı.</p>
                                <p class="text-gray-500 text-sm mt-2">Farklı bir kurul üyesi seçerek tekrar deneyebilirsiniz.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="px-6 py-4 border-t border-gray-200/70 bg-gray-50/50">
                    {{ $sikayetler->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>