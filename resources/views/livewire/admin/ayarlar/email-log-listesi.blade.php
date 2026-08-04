<div class="space-y-6" x-data="{ modalAcik: false, seciliLog: null }">
    {{-- İstatistikler --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Bugün</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['bugun']) }}</p>
            </div>
            <div class="p-3 bg-cyan-50 rounded-lg text-cyan-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Bu Hafta</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['bu_hafta']) }}</p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Bu Ay</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['bu_ay']) }}</p>
            </div>
            <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Toplam</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['toplam']) }}</p>
            </div>
            <div class="p-3 bg-gray-100 rounded-lg text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Filtreleme Alanı --}}
    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex-1 w-full flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Kime veya Konu ara..." class="pl-10 w-full border-gray-200 rounded-xl text-sm focus:ring-cyan-500 focus:border-cyan-500 shadow-sm">
            </div>
            <div class="w-full sm:w-48">
                <select wire:model.live="kategori" class="w-full border-gray-200 rounded-xl text-sm focus:ring-cyan-500 focus:border-cyan-500 shadow-sm">
                    <option value="">Tüm Kategoriler</option>
                    @foreach($kategoriler as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <input wire:model.live="baslangicTarihi" type="date" class="w-full sm:w-auto border-gray-200 rounded-xl text-sm focus:ring-cyan-500 focus:border-cyan-500 shadow-sm" title="Başlangıç Tarihi">
                <span class="text-gray-400">-</span>
                <input wire:model.live="bitisTarihi" type="date" class="w-full sm:w-auto border-gray-200 rounded-xl text-sm focus:ring-cyan-500 focus:border-cyan-500 shadow-sm" title="Bitiş Tarihi">
            </div>
        </div>
        </div>
        <div wire:loading class="text-cyan-600">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>
    </div>

    {{-- Araç Çubuğu (Sayfalama ve Silme) --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <span class="text-sm text-gray-500 font-medium">Sayfada</span>
            <select wire:model.live="perPage" class="border-gray-200 rounded-xl text-sm focus:ring-cyan-500 focus:border-cyan-500 shadow-sm w-20">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-500 font-medium">kayıt</span>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            @if(count($seciliLoglar) > 0)
                <button wire:click="secilileriSil" wire:confirm="Seçili {{ count($seciliLoglar) }} kaydı silmek istediğinize emin misiniz?" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Seçilileri Sil ({{ count($seciliLoglar) }})
                </button>
            @endif

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select wire:model="silmePeriodu" class="w-full sm:w-auto border-gray-200 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 shadow-sm">
                    <option value="">Eski Kayıtları Sil</option>
                    <option value="1_ay">1 Aydan Eskiler</option>
                    <option value="3_ay">3 Aydan Eskiler</option>
                    <option value="6_ay">6 Aydan Eskiler</option>
                    <option value="1_yil">1 Yıldan Eskiler</option>
                </select>
                <button wire:click="periyodikSil" wire:confirm="Seçilen periyottan eski tüm kayıtlar silinecek. Emin misiniz?" class="inline-flex items-center justify-center p-2 border border-transparent rounded-xl shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50" {{ !$silmePeriodu ? 'disabled' : '' }} title="Periyodik Sil">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50/50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">
                            <input type="checkbox" wire:click="$set('seciliLoglar', {{ json_encode($logs->pluck('id')->toArray()) }})" @if(count($seciliLoglar) === $logs->count() && $logs->count() > 0) checked wire:click="$set('seciliLoglar', [])" @endif class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tarih</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kime</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Konu</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-cyan-50/30 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" wire:model.live="seciliLoglar" value="{{ $log->id }}" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="font-medium">{{ $log->created_at->format('d.m.Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $log->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $log->alici_email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->kategori)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                        {{ $log->kategori }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Diğer
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 truncate max-w-xs" title="{{ $log->konu }}">
                                    {{ $log->konu }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <script type="application/json" id="log-data-{{ $log->id }}">
                                        {!! json_encode([
                                            'created_at_formatted' => $log->created_at->format('d.m.Y H:i:s'),
                                            'alici_email' => $log->alici_email,
                                            'kategori' => $log->kategori,
                                            'konu' => $log->konu,
                                            'ilgili_model_type' => $log->ilgili_model_type ? class_basename($log->ilgili_model_type) : null,
                                            'ilgili_model_id' => $log->ilgili_model_id,
                                            'icerik' => $log->icerik,
                                        ]) !!}
                                    </script>
                                    <button type="button" @click="seciliLog = JSON.parse(document.getElementById('log-data-{{ $log->id }}').textContent); modalAcik = true" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-cyan-50 hover:text-cyan-600 hover:border-cyan-200 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500" title="Görüntüle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                    <button type="button" wire:click="sil({{ $log->id }})" wire:confirm="Bu e-posta kaydını silmek istediğinize emin misiniz?" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" title="Sil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900">E-posta Logu Bulunamadı</h3>
                                <p class="mt-1 text-sm text-gray-500">Arama kriterlerinize uygun e-posta kaydı yok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    {{-- E-posta Görüntüleme Modalı (AlpineJS ile İstemci Tarafı) --}}
    <div x-cloak x-show="modalAcik" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" 
                 x-show="modalAcik"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 aria-hidden="true" @click="modalAcik = false"></div>
                 
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                 x-show="modalAcik"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-cyan-100 rounded-lg text-cyan-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                E-posta Detayı
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                Gönderim Tarihi: <span x-text="seciliLog?.created_at_formatted"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="modalAcik = false" type="button" class="bg-white rounded-full p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-cyan-500 transition-colors">
                        <span class="sr-only">Kapat</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Kime</div>
                            <div class="text-sm font-medium text-gray-900" x-text="seciliLog?.alici_email"></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Kategori</div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800" x-text="seciliLog?.kategori || 'Diğer'">
                                </span>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Konu</div>
                            <div class="text-sm font-medium text-gray-900" x-text="seciliLog?.konu"></div>
                        </div>
                        <template x-if="seciliLog?.ilgili_model_type && seciliLog?.ilgili_model_id">
                            <div class="md:col-span-2">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">İlişkili Model</div>
                                <div class="text-xs text-gray-600 font-mono">
                                    <span x-text="seciliLog.ilgili_model_type"></span> #<span x-text="seciliLog.ilgili_model_id"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            E-posta İçeriği
                        </div>
                        <div class="h-[500px] w-full">
                            <iframe x-bind:srcdoc="seciliLog?.icerik" frameborder="0" class="w-full h-full" sandbox="allow-same-origin"></iframe>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 sm:flex sm:flex-row-reverse">
                    <button @click="modalAcik = false" type="button" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
