<div x-show="activeTab === 'gorevler'" class="space-y-6" style="display: none;">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
            {{ auth()->id() == $user->id ? 'Aktif Görevlerim' : 'Kişinin Aktif Görevleri' }}
        </h3>
        <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">
            {{ count($activeTasks) }} Görev
        </span>
    </div>

    @if(count($activeTasks) > 0)
        <div x-data="{ limit: 5, total: {{ count($activeTasks) }} }" class="space-y-4">
            @foreach($activeTasks as $task)
                <div
                    x-show="{{ $loop->index }} < limit"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pl-3">
                        {{-- Sol Kısım: Proje Bilgisi --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-gray-100 text-gray-600">
                                    #{{ $task->id }}
                                </span>
                                {!! $task->durum_etiketi !!}
                            </div>

                            <h4 class="font-bold text-gray-800 text-base mb-1">
                                <a href="{{ route('proje.workspace.show', $task->id) }}"
                                    class="hover:text-orange-600 transition-colors">
                                    {{ $task->baslik }}
                                </a>
                            </h4>

                            @if($task->musteriSikayeti)
                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    {{ $task->musteriSikayeti->sikayetKategori->ad ?? 'Genel Kategori' }}
                                </p>
                            @endif
                        </div>

                        {{-- Orta Kısım: Adım Bilgisi --}}
                        <div class="flex-shrink-0 w-56 text-center">
                            @if($task->aktifAdim)
                                <span class="text-[10px] uppercase text-gray-400 font-bold block mb-0.5">Mevcut Adım</span>
                                <span class="text-sm font-medium text-gray-700 inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse inline-block"></span>
                                    {{ $task->aktifAdim->adim_adi ?? 'İşlem Bekleniyor' }}
                                </span>
                            @elseif($task->aktif_asama_metni)
                                <span class="text-[10px] uppercase text-gray-400 font-bold block mb-0.5">Mevcut Adım</span>
                                <span class="text-sm font-medium text-amber-700 inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse inline-block"></span>
                                    {{ $task->aktif_asama_metni }}
                                </span>
                            @endif
                        </div>

                        {{-- Sağ Kısım: Tarih ve Buton --}}
                        <div class="flex items-center justify-between md:justify-end gap-4 min-w-[200px]">
                            <div class="text-right">
                                <span class="text-[10px] uppercase text-gray-400 font-bold block mb-0.5">Son Güncelleme</span>
                                <span class="text-xs font-semibold text-gray-600 block">
                                    {{ $task->updated_at->diffForHumans() }}
                                </span>
                            </div>

                            <a href="{{ route('proje.workspace.show', $task->id) }}"
                                class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- DAHA FAZLA GÖSTER BUTONU --}}
            <template x-if="limit < total">
                <div class="mt-6 flex justify-center">
                    <button @click="limit += 5" 
                            class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 rounded-xl font-bold text-sm text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all transform hover:scale-105 active:scale-95 group">
                        <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                        </svg>
                        Daha Fazla Göster
                        <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-500 rounded-lg text-xs" x-text="'(' + (total - limit) + ' kaldı)'"></span>
                    </button>
                </div>
            </template>
        </div>
    @else
        <div
            class="flex flex-col items-center justify-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Aktif Görev Bulunmuyor</h3>
            <p class="text-gray-500 max-w-sm mt-1">Şu anda onay bekleyen veya işlem yapılması gereken aktif bir görev
                bulunmamaktadır.</p>
        </div>
    @endif
</div>