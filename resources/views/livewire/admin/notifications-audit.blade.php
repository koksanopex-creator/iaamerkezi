<div class="space-y-6">
    {{-- Üst Bar: Filtreler --}}
    <div class="bg-white/80 backdrop-blur-md p-6 rounded-2xl border border-slate-200 shadow-sm transition-all">
        <div class="flex flex-col lg:flex-row items-center gap-4">
            
            {{-- Sol: Arama --}}
            <div class="relative flex-1 group w-full lg:w-auto">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Bildirim içeriği veya kullanıcı ara..." 
                       class="block w-full pl-11 pr-4 py-2.5 bg-slate-50 border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400">
            </div>

            {{-- Filtreler --}}
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                
                {{-- Bölüm Filtresi (Sadece Superadmin/Yonetim) --}}
                @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim']))
                    <select wire:model.live="selectedBolum" class="text-xs font-semibold border-slate-200 rounded-xl bg-slate-50 text-slate-600 focus:ring-indigo-500/20 focus:border-indigo-500 py-2 px-3">
                        <option value="all">Tüm Bölümler</option>
                        @foreach($bolumler as $b)
                            <option value="{{ $b->id }}">{{ $b->ad }}</option>
                        @endforeach
                    </select>
                @endif

                {{-- Kullanıcı Filtresi --}}
                <select wire:model.live="selectedUser" class="text-xs font-semibold border-slate-200 rounded-xl bg-slate-50 text-slate-600 focus:ring-indigo-500/20 focus:border-indigo-500 py-2 px-3">
                    <option value="all">Tüm Kullanıcılar</option>
                    @foreach($usersList as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>

                {{-- Tarih Aralığı --}}
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-2 py-1">
                    <input type="date" wire:model.live="startDate" class="bg-transparent border-none text-xs text-slate-600 focus:ring-0 py-1">
                    <span class="text-slate-300">-</span>
                    <input type="date" wire:model.live="endDate" class="bg-transparent border-none text-xs text-slate-600 focus:ring-0 py-1">
                </div>

                <select wire:model.live="status" class="text-xs font-semibold border-slate-200 rounded-xl bg-slate-50 text-slate-600 focus:ring-indigo-500/20 focus:border-indigo-500 py-2 px-3">
                    <option value="all">Tüm Durumlar</option>
                    <option value="unread">Hala Okunmadı</option>
                    <option value="read">En Az Bir Kez Okundu</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Liste --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-bottom border-slate-200">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kullanıcı & Bölüm</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Bildirim Detayı</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Durum</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Denetim (İnkar Edilemezlik)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($notifications as $n)
                        @php
                            $notifData = json_decode($n->data, true);
                            $msg = $notifData['message'] ?? 'Mesaj yok';
                            $targetUrl = $notifData['url'] ?? $notifData['link'] ?? $notifData['action_url'] ?? '#';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            {{-- Kullanıcı & Bölüm --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-900">{{ $n->user_name }}</span>
                                    <span class="text-[10px] font-medium text-slate-400 uppercase">{{ $n->bolum_ad ?? 'Bölüm Yok' }}</span>
                                </div>
                            </td>

                            {{-- Bildirim Detayı --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[13px] text-slate-700 leading-snug">{{ $msg }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] text-slate-400 font-medium">Gönderim: {{ \Carbon\Carbon::parse($n->created_at)->format('d.m.Y H:i') }}</span>
                                        <span class="text-[10px] text-slate-300">|</span>
                                        <a href="{{ $targetUrl }}" class="text-[10px] text-indigo-500 hover:underline font-bold" target="_blank">Hedef Sayfa</a>
                                    </div>
                                </div>
                            </td>

                            {{-- Durum --}}
                            <td class="px-6 py-4 text-center">
                                @if($n->read_at)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        OKUNDU
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                        OKUNMADI
                                    </span>
                                @endif
                            </td>

                            {{-- Denetim (İnkar Edilemezlik) --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center justify-center space-y-1.5">
                                    @if($n->first_read_at)
                                        <div class="flex flex-col items-center p-2 rounded-xl bg-blue-50 border border-blue-100 w-full max-w-[140px]">
                                            <span class="text-[9px] font-bold text-blue-400 uppercase tracking-tighter">İLK OKUMA</span>
                                            <span class="text-[11px] font-black text-blue-700">{{ \Carbon\Carbon::parse($n->first_read_at)->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-slate-100 border border-slate-200">
                                            <span class="text-[9px] font-bold text-slate-500 uppercase">TIKLAMA:</span>
                                            <span class="text-[11px] font-black text-slate-700">{{ $n->read_count }}</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center opacity-40">
                                            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase">Hiç Açılmadı</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300 mb-4 border border-slate-100">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 uppercase">Kayıt Bulunamadı</h3>
                                    <p class="text-[11px] text-slate-500 mt-1">Seçilen filtrelere uygun bildirim hareketi bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Sayfalama --}}
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
