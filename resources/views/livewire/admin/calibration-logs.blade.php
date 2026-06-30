<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Kalibrasyon Günlüğü</h2>
                <p class="text-slate-500 font-medium">Tüm veri ve puan düzeltme işlemlerinin tarihçesi.</p>
            </div>
            <a href="{{ route('admin.health.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Paneli Geri Dön
            </a>
        </div>

        {{-- FİLTRELER --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 mb-8 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px]">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Model, alan veya açıklama ara..." 
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>
            <select wire:model.live="type" class="px-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                <option value="">Tüm Türler</option>
                <option value="veri">Veri Kalibrasyonu</option>
                <option value="puan">Puan Kalibrasyonu</option>
            </select>
        </div>

        {{-- LOG LİSTESİ --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-6 py-4">Tarih</th>
                            <th class="px-6 py-4">Yapan</th>
                            <th class="px-6 py-4">Tür</th>
                            <th class="px-6 py-4">Model / Kayıt</th>
                            <th class="px-6 py-4">Alan</th>
                            <th class="px-6 py-4">Değişim</th>
                            <th class="px-6 py-4">Açıklama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-500">
                                    {{ $log->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs font-black">
                                            {{ strtoupper(substr($log->causer->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-xs font-bold text-slate-700">{{ $log->causer->name ?? 'Sistem' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase {{ $log->type === 'puan' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $log->type === 'puan' ? 'PUAN' : 'VERİ' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-600">
                                    @php
                                        $link = null;
                                        try {
                                            if ($log->model_type === 'User') {
                                                $link = $log->type === 'puan' ? route('profile.puanlar', $log->model_id) : route('profile.show', $log->model_id);
                                            } elseif ($log->model_type === 'Takim') {
                                                $link = route('takim-puanlari', $log->model_id);
                                            } elseif ($log->model_type === 'Iaa') {
                                                $link = route('proje.workspace.show', $log->model_id);
                                            } elseif ($log->model_type === 'MusteriSikayeti') {
                                                $link = route('iaa.sikayetler.show', $log->model_id);
                                            }
                                        } catch (\Exception $e) { $link = null; }
                                    @endphp

                                    @if($link)
                                        <a href="{{ $link }}" target="_blank" class="hover:text-indigo-600 hover:underline flex items-center gap-1">
                                            {{ $log->model_type }} #{{ $log->model_id }}
                                            <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    @else
                                        {{ $log->model_type }} #{{ $log->model_id }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-black text-indigo-600">
                                    {{ $log->field }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold text-slate-400 line-through">{{ $log->old_value }}</span>
                                        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                        <span class="text-[10px] font-black text-emerald-600">{{ $log->new_value }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-500">
                                    @php
                                        $parts = explode('|', $log->description);
                                        $mainDesc = trim($parts[0] ?? '');
                                        $details = isset($parts[1]) ? trim($parts[1]) : null;
                                        $projects = isset($parts[2]) ? trim($parts[2]) : null;

                                        // Proje, Şikayet ve Öneri ID'lerini linke çevir (Regex)
                                        if ($projects) {
                                            $projects = preg_replace_callback('/(?:(Proje|Şikayet|Öneri)\s+)?#(\d+)/', function($m) {
                                                try {
                                                    $type = !empty($m[1]) ? $m[1] : 'Proje'; // Varsayılan Proje (Eski loglar için)
                                                    $id = $m[2];
                                                    $url = '#';
                                                    
                                                    if ($type === 'Şikayet') {
                                                        $url = route('iaa.sikayetler.show', $id);
                                                    } else {
                                                        // Proje ve Öneri aynı rotayı kullanıyor
                                                        $url = route('proje.workspace.show', $id);
                                                    }
                                                    
                                                    $prefix = !empty($m[1]) ? '<span class="text-slate-400">'.$type.'</span> ' : '';
                                                    return $prefix . '<a href="'.$url.'" target="_blank" class="text-indigo-500 font-bold hover:underline">#'.$id.'</a>';
                                                } catch (\Exception $e) {
                                                    return $m[0];
                                                }
                                            }, $projects);
                                        }
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <span class="text-slate-700 font-black">{{ $mainDesc }}</span>
                                        @if($details)
                                            <div class="flex items-center gap-1">
                                                <div class="w-1 h-1 rounded-full bg-indigo-400"></div>
                                                <span class="text-[10px] text-indigo-500 font-black uppercase tracking-tight">{{ $details }}</span>
                                            </div>
                                        @endif
                                        @if($projects)
                                            <div class="flex items-start gap-1">
                                                <svg class="w-3 h-3 text-slate-300 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span class="text-[10px] text-slate-400 font-medium leading-relaxed">{!! $projects !!}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center text-slate-400 italic">
                                    Kayıt bulunamadı.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
                <div class="p-6 border-t border-slate-50 bg-slate-50/30">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
