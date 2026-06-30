<div class="premium-card">
    <div class="p-card-header flex-col md:flex-row gap-4">
        <h3 class="p-card-title">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            İAA (İyileştirme Adımları) Projeleri ({{ $iaaProjeleri->total() }})
        </h3>
        
        {{-- Filtreler --}}
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative min-w-[150px]">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Proje adı ile ara..." class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 w-full pl-8">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase">ÖNEREN:</span>
                <select wire:model.live="suggesterId" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 min-w-[120px]">
                    <option value="">Tümü</option>
                    @foreach($suggesters as $suggester)
                        <option value="{{ $suggester->id }}">{{ $suggester->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase">TAKIM:</span>
                <select wire:model.live="teamId" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500 min-w-[120px]">
                    <option value="">Tümü</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->ad }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase">DURUM:</span>
                <select wire:model.live="status" class="text-xs border-gray-200 rounded-lg focus:ring-indigo-500">
                    <option value="">Tümü</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            @if($search || $status || $suggesterId || $teamId)
                <button wire:click="$set('search', ''); $set('status', ''); $set('suggesterId', ''); $set('teamId', '');" class="text-[10px] font-bold text-red-500 hover:underline">Temizle</button>
            @endif
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="p-table">
            <thead>
                <tr>
                    <th class="text-left">Proje Adı</th>
                    <th class="text-left">Öneren</th>
                    <th class="text-left">Takım</th>
                    <th class="text-left">Durum</th>
                    <th class="text-left">Tarih</th>
                    <th class="text-right pr-6">İŞLEMLER</th>
                </tr>
            </thead>
            <tbody>
                @forelse($iaaProjeleri as $proje)
                <tr wire:key="iaa-{{ $proje->id }}">
                    <td><span class="font-bold text-gray-900">{{ $proje->baslik }}</span></td>
                    <td>
                        @if($proje->gonderen)
                            <a href="{{ route('profile.show', $proje->gonderen->id) }}" class="text-indigo-600 font-bold hover:underline text-xs">
                                {{ $proje->gonderen->name }}
                            </a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if($proje->atananTakim)
                            <span class="text-xs font-medium text-gray-700">{{ $proje->atananTakim->ad }}</span>
                        @else
                            <span class="text-gray-400 text-xs">Atanmadı</span>
                        @endif
                    </td>
                    <td>{!! $proje->durum_etiketi !!}</td>
                    <td>{{ $proje->created_at->format('d.m.Y') }}</td>
                    <td class="text-right">
                        <a href="{{ route('proje.workspace.show', $proje->id) }}" class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-black hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                            Projeye Git
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-400 italic">Kayıtlı proje bulunamadı.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($iaaProjeleri->hasMorePages())
    <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
        <button wire:click="loadMore" wire:loading.attr="disabled" class="text-xs font-black text-indigo-600 hover:text-indigo-800 transition-all flex items-center justify-center gap-2 mx-auto group">
            <span wire:loading.remove>Devamını Göster ({{ $iaaProjeleri->total() - $iaaProjeleri->count() }} kayıt daha)</span>
            <span wire:loading>Yükleniyor...</span>
            <svg wire:loading.remove class="w-4 h-4 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            <svg wire:loading class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
    </div>
    @endif
</div>
