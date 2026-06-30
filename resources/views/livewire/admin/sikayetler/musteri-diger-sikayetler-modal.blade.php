<div>
    @if($isOpen)
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"
            x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)"
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <!-- Modal Content -->
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden border border-slate-200 relative transform transition-all"
                @click.away="$wire.closeModal()"
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-8 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-8 sm:scale-95">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center sticky top-0 z-10 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 text-indigo-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002 2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">Firmanın Diğer Şikayetleri</h3>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $sikayetler->first()->customer->name ?? '' }}</p>
                        </div>
                    </div>
                    
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Info Alert -->
                <div class="px-6 py-4 bg-blue-50/50 border-b border-blue-100 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-medium text-blue-800 leading-snug">
                        {{ $mesaj }}
                    </p>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto flex-1 bg-slate-50/30">
                    @if($sikayetler->isEmpty())
                        <div class="text-center py-10">
                            <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h4 class="text-base font-bold text-slate-700 mb-1">Görüntülenecek Şikayet Yok</h4>
                            <p class="text-sm text-slate-500">Kriterlerinize uygun başka bir şikayet bulunamadı.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($sikayetler as $s)
                                <a href="{{ route(auth()->user()->hasRole('Müşteri|Müşteri Temsilcisi') ? 'iaa.sikayetler.show' : 'admin.sikayetler.show', $s->id) }}" 
                                   class="block bg-white border {{ $s->id == $currentSikayetId ? 'border-indigo-300 ring-1 ring-indigo-300 shadow-md' : 'border-slate-200 hover:border-slate-300 shadow-sm' }} rounded-xl p-4 transition-all hover:-translate-y-0.5 relative">
                                    
                                    @if($s->id == $currentSikayetId)
                                        <div class="absolute -top-2 -right-2 bg-indigo-500 text-white text-[10px] font-bold px-2 py-1 rounded-lg uppercase tracking-widest shadow-sm">
                                            Şu Anki
                                        </div>
                                    @endif

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-slate-400">#{{ $s->id }}</span>
                                            <h4 class="font-bold text-slate-800 text-sm truncate">{{ $s->musteri_sikayet_konusu }}</h4>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border 
                                                {{ match($s->musteri_oncelik) {
                                                    'Acil' => 'bg-red-50 text-red-600 border-red-100',
                                                    'Yüksek' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                    'Normal' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'Düşük' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    default => 'bg-slate-50 text-slate-600 border-slate-100'
                                                } }}">
                                                {{ $s->musteri_oncelik }}
                                            </span>
                                            {!! $s->musteri_durum_badge !!}
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-0.5">Kategori</span>
                                            <span class="text-slate-600 font-medium">{{ $s->sikayetKategori->ad ?? 'Genel' }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-0.5">Tarih</span>
                                            <span class="text-slate-600 font-medium">{{ $s->created_at->format('d.m.Y') }}</span>
                                        </div>
                                        <div class="col-span-2">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-0.5">Çözüm Takımı</span>
                                            <span class="text-slate-600 font-medium">{{ $s->cozumTakimi->ad ?? 'Atanmadı' }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
