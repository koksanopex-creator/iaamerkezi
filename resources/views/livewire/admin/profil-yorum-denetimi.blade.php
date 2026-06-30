<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    {{-- Header & Stats --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Profil Yorum Denetimi</h1>
                <p class="text-slate-500 text-sm mt-1">Sistem genelindeki tüm personel yorumlarını ve etkileşimlerini buradan takip edebilirsiniz.</p>
            </div>
            <div class="flex gap-4">
                <div class="bg-indigo-600 p-4 rounded-2xl text-white shadow-xl shadow-indigo-100 flex items-center gap-4">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">Toplam Yorum</p>
                        <p class="text-2xl font-black">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
                <div class="bg-emerald-500 p-4 rounded-2xl text-white shadow-xl shadow-emerald-100 flex items-center gap-4">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">Bugün Yapılan</p>
                        <p class="text-2xl font-black">{{ number_format($stats['today']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Bar --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-2 tracking-wider">İçerikte Ara</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Anahtar kelime yazın..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Yorum Yapan</label>
                <select wire:model.live="yazan_user_id" class="w-full py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 appearance-none">
                    <option value="">Tümü</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Profil Sahibi</label>
                <select wire:model.live="user_id" class="w-full py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 appearance-none">
                    <option value="">Tümü</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Başlangıç</label>
                    <input type="date" wire:model.live="startDate" class="w-full py-2 bg-slate-50 border-none rounded-xl text-xs">
                </div>
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Bitiş</label>
                    <input type="date" wire:model.live="endDate" class="w-full py-2 bg-slate-50 border-none rounded-xl text-xs">
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tarih</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Yorum Yapan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Hangi Profile?</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Yorum İçeriği</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($yorumlar as $yorum)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-slate-700 block">{{ $yorum->created_at->format('d.m.Y') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $yorum->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($yorum->yazan->profile_photo_path)
                                        <img src="{{ asset('storage/' . $yorum->yazan->profile_photo_path) }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($yorum->yazan->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="text-sm font-bold text-slate-800 block">{{ $yorum->yazan->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $yorum->yazan->unvan ?? 'Personel' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($yorum->profilSahibi->profile_photo_path)
                                        <img src="{{ asset('storage/' . $yorum->profilSahibi->profile_photo_path) }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($yorum->profilSahibi->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="text-sm font-bold text-slate-800 block">{{ $yorum->profilSahibi->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $yorum->profilSahibi->unvan ?? 'Personel' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-md">
                                    @if($yorum->parent_id)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase mb-1">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            Cevap
                                        </span>
                                    @endif
                                    <p class="text-xs text-slate-600 leading-relaxed italic line-clamp-2 group-hover:line-clamp-none transition-all">"{{ $yorum->yorum }}"</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('profile.show', $yorum->user_id) }}?tab=yorumlar&focused_comment={{ $yorum->id }}" 
                                       target="_blank"
                                       class="p-2 text-slate-400 hover:text-indigo-600 transition-colors"
                                       title="Görüntüle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <button wire:click="deleteComment({{ $yorum->id }})" 
                                            wire:confirm="Bu yorumu (ve varsa cevaplarını) kalıcı olarak silmek istediğinize emin misiniz?"
                                            class="p-2 text-slate-400 hover:text-rose-600 transition-colors"
                                            title="Sil">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    </div>
                                    <p class="text-slate-400 font-medium">Kriterlere uygun yorum bulunamadı.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($yorumlar->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $yorumlar->links() }}
            </div>
        @endif
    </div>
</div>
