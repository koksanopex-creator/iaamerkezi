<div x-data="{}" class="space-y-6">
    {{-- Üst Bar: Arama ve Hızlı Aksiyonlar --}}
    <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4 bg-white/80 backdrop-blur-md p-5 rounded-2xl border border-slate-200 shadow-sm sticky top-0 z-10 transition-all">
        
        {{-- Sol: Arama Barı --}}
        <div class="relative flex-1 group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Bildirimlerde ara... (Örn: Proje adı, mesaj içeriği)" 
                   class="block w-full pl-11 pr-4 py-2.5 bg-slate-50 border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400">
        </div>

        {{-- Sağ: Filtreler ve Butonlar --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-2 py-1">
                <input type="date" wire:model.live="startDate" class="bg-transparent border-none text-xs text-slate-600 focus:ring-0 py-1">
                <span class="text-slate-300">-</span>
                <input type="date" wire:model.live="endDate" class="bg-transparent border-none text-xs text-slate-600 focus:ring-0 py-1">
            </div>

            <select wire:model.live="status" class="text-xs font-semibold border-slate-200 rounded-xl bg-slate-50 text-slate-600 focus:ring-indigo-500/20 focus:border-indigo-500 py-2 px-3">
                <option value="all">Tüm Durumlar</option>
                <option value="unread">Okunmamış</option>
                <option value="read">Okunmuş</option>
            </select>

            <button wire:click="markAllAsRead" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md hover:shadow-indigo-500/20 active:scale-95">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Hepsini Okundu Say
            </button>

            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-xs font-bold rounded-xl border border-slate-200 hover:bg-slate-50 transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Ana Sayfa
            </a>
        </div>
    </div>

    {{-- Bildirim Listesi --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <ul class="divide-y divide-slate-100">
                @forelse($notifications as $notification)
                    <li class="p-5 transition-all {{ $notification->read_at ? 'bg-white' : 'bg-blue-50/80 shadow-inner' }} hover:bg-slate-50/80 group">
                        <div class="flex items-start gap-4">
                            
                            {{-- İkon ve Durum --}}
                            <div class="relative flex-shrink-0">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 {{ $notification->read_at ? 'bg-slate-100 text-slate-400' : 'bg-'.$notification->computed_color.'-100 text-'.$notification->computed_color.'-600' }}">
                                    @if($notification->computed_color == 'red')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    @elseif($notification->computed_color == 'amber')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    @endif
                                </div>
                                @if(!$notification->read_at)
                                    <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-4 w-4 bg-indigo-500 border-2 border-white shadow-sm"></span>
                                    </span>
                                @endif
                            </div>

                            {{-- İçerik --}}
                            <div class="flex-1 space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    {{-- Etiket: Tip --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-{{ $notification->computed_color }}-50 text-{{ $notification->computed_color }}-700 border border-{{ $notification->computed_color }}-100 shadow-sm">
                                        {{ $notification->computed_type }}
                                    </span>
                                    
                                    {{-- Etiket: Departman --}}
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-{{ $notification->computed_dept_color }}-100 text-{{ $notification->computed_dept_color }}-600 border border-{{ $notification->computed_dept_color }}-200 uppercase tracking-tight">
                                            {{ $notification->computed_dept }}
                                        </span>

                                    <span class="text-[11px] font-medium text-slate-400 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $notification->created_at->diffForHumans() }}
                                        <span class="mx-2 text-slate-200">|</span>
                                        {{ $notification->created_at->format('d.m.Y H:i') }}
                                    </span>
                                </div>

                                @php
                                    $serverLink = $notification->data['url'] ?? $notification->data['link'] ?? $notification->data['action_url'] ?? null;
                                    if (!$serverLink && isset($notification->data['iaa_id'])) {
                                        $serverLink = route('proje.workspace.show', $notification->data['iaa_id']);
                                    }
                                @endphp

                                <a href="{{ route('notifications.readAndRedirect', $notification->id) }}" 
                                   class="block text-[15px] leading-snug {{ $notification->read_at ? 'text-slate-600 font-medium' : 'text-slate-900 font-extrabold' }} hover:text-indigo-600 transition-colors">
                                    {{ $notification->data['message'] ?? 'Bildirim içeriği bulunamadı.' }}
                                </a>
                            </div>

                            {{-- Sağ: Tekil Aksiyon (Toggle) --}}
                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <button wire:click="toggleRead('{{ $notification->id }}')" 
                                        class="p-2.5 rounded-xl transition-all shadow-sm hover:shadow-md active:scale-90 flex items-center gap-2 {{ $notification->read_at ? 'text-slate-400 hover:text-indigo-600' : 'text-blue-600 bg-white' }}"
                                        title="{{ $notification->read_at ? 'Okunmadı İşaretle' : 'Okundu İşaretle' }}">
                                    
                                    @if($notification->read_at)
                                        {{-- Okunmuş ise: Okunmadı Yap butonu (Boş Daire) --}}
                                        <div class="w-5 h-5 rounded-full border-2 border-slate-300"></div>
                                        <span class="text-[10px] font-bold uppercase hidden group-hover:inline">Okunmadı Yap</span>
                                    @else
                                        {{-- Okunmamış ise: Okundu Yap butonu (Dolu Daire) --}}
                                        <div class="w-5 h-5 rounded-full border-2 border-blue-600 bg-blue-600 flex items-center justify-center">
                                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase">Okunmamış</span>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="p-20 text-center bg-slate-50/50">
                        <div class="flex flex-col items-center max-w-sm mx-auto">
                            <div class="w-20 h-20 bg-white rounded-3xl shadow-lg flex items-center justify-center text-slate-200 mb-6 border border-slate-100 italic">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800">Sonuç Bulunamadı</h3>
                            <p class="text-slate-500 mt-2">Arama kriterlerinize uygun bildirim bulunmuyor. Lütfen farklı bir anahtar kelime veya tarih aralığı deneyin.</p>
                            <button wire:click="clearFilters" class="mt-6 text-indigo-600 font-bold text-sm hover:underline">Filtreleri Sıfırla</button>
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Sayfalama: Modern Styling --}}
    <div class="mt-8 px-2 pb-12">
        {{ $notifications->links() }}
    </div>
</div>
