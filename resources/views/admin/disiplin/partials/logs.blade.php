{{-- İŞLEM GEÇMİŞİ (Sadece Yetkililere) --}}
@if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Yonetim', 'Yönetim']))
    <div x-data="{ showLogs: false }" class="w-full mt-12 mb-20 animate-in fade-in slide-in-from-bottom-4 duration-700" style="width: 100% !important; display: block !important; clear: both !important;">
        <div class="w-full bg-white rounded-[2rem] border border-slate-200 shadow-2xl shadow-slate-200/40 overflow-hidden" style="width: 100% !important;">
            <button @click="showLogs = !showLogs" class="w-full px-8 py-6 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-left">
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Dosya İşlem Geçmişi</h4>
                        <p class="text-[10px] text-slate-400 font-bold">TOPLAM {{ $case->logs->count() }} ADET KAYIT BULUNMAKTADIR</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span x-text="showLogs ? 'DARALT' : 'DETAYLARI GÖSTER'" class="text-[10px] font-black text-slate-400 tracking-widest group-hover:text-indigo-600 transition-colors"></span>
                    <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-400 transition-all duration-300" :class="showLogs ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>
            
            <div x-show="showLogs" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="border-t border-slate-100 bg-slate-50/30">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-100/50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-8 py-4 border-b border-slate-100">Tarih / Saat</th>
                                <th class="px-8 py-4 border-b border-slate-100">İşlem Yapan</th>
                                <th class="px-8 py-4 border-b border-slate-100">Eylem Türü</th>
                                <th class="px-8 py-4 border-b border-slate-100">Açıklama & Detay</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($case->logs()->with('user')->orderBy('created_at', 'desc')->get() as $log)
                                <tr class="hover:bg-white transition-colors group/row">
                                    <td class="px-8 py-5">
                                        <div class="text-xs font-black text-slate-700">{{ $log->created_at->format('d.m.Y') }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold">{{ $log->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            @if($log->user)
                                                <img src="{{ $log->user->profile_photo_url }}" class="w-8 h-8 rounded-lg border border-slate-200 shadow-sm object-cover">
                                                <div>
                                                    <a href="{{ url('/kullanici-profil/'.$log->user_id) }}" class="text-xs font-black text-slate-900 hover:text-indigo-600 transition-colors">{{ $log->user->name }}</a>
                                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">{{ $log->user->bolum->ad ?? '-' }}</p>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </div>
                                                <span class="text-xs font-bold text-slate-400 italic">Sistem Kaydı</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        @php
                                            $badgeColor = match(true) {
                                                str_contains($log->eylem, 'İptal') => 'rose',
                                                str_contains($log->eylem, 'Geri Alındı') => 'amber',
                                                str_contains($log->eylem, 'Düzenlendi') => 'indigo',
                                                str_contains($log->eylem, 'Oluşturuldu') => 'emerald',
                                                str_contains($log->eylem, 'Sevk') => 'violet',
                                                default => 'slate'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-{{ $badgeColor }}-50 text-{{ $badgeColor }}-700 text-[10px] font-black uppercase tracking-widest border border-{{ $badgeColor }}-100">
                                            {{ $log->eylem }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-xs text-slate-600 font-medium leading-relaxed italic">"{{ $log->aciklama }}"</p>
                                        @if($log->eski_metin)
                                            <div x-data="{ showPrev: false }" class="mt-2">
                                                <button @click="showPrev = !showPrev" class="text-[10px] font-black text-indigo-500 hover:text-indigo-700 underline underline-offset-2 uppercase tracking-tighter transition-colors">Değişiklik Öncesi Veriyi Gör</button>
                                                <div x-show="showPrev" x-cloak x-transition class="mt-3 p-4 bg-amber-50/50 border border-amber-100 rounded-2xl text-xs text-slate-600 italic whitespace-pre-wrap leading-relaxed shadow-inner">
                                                    <span class="text-[10px] font-black text-amber-600 uppercase block mb-1">Eski Kayıt:</span>
                                                    {{ $log->eski_metin }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <p class="text-sm font-black text-slate-300 uppercase italic tracking-widest">Henüz bir işlem kaydı bulunmuyor.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
