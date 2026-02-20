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
        <div class="grid grid-cols-1 gap-4">
            @foreach($activeTasks as $task)
                <div
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
                        <div class="flex-1 md:text-center">
                            @if($task->aktifAdim)
                                <div class="inline-block text-left">
                                    <span class="text-[10px] uppercase text-gray-400 font-bold block mb-0.5">Mevcut Adım</span>
                                    <span class="text-sm font-medium text-gray-700 flex items-center gap-1 md:justify-center">
                                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                        {{ $task->aktifAdim->adim_adi ?? 'İşlem Bekleniyor' }}
                                    </span>
                                </div>
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