<div x-show="activeTab === 'guvenlik'" class="space-y-6">
    <div class="bg-red-50 p-4 rounded-lg border border-red-200 mb-4">
        <p class="text-red-800 text-sm font-bold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Bu alan sadece Süper Yöneticiler tarafından görülebilir.
        </p>
    </div>

    {{-- 1. SON GİRİŞ HAREKETLERİ --}}
    @php
        $loginLoglari = \App\Models\LoginActivity::where('user_id', $user->id)->latest()->take(5)->get();
    @endphp
    
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            Son Giriş Hareketleri
        </h3>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">IP Adresi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cihaz/Tarayıcı</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($loginLoglari as $login)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700 font-mono">{{ $login->ip_address }}</td>
                            <td class="px-4 py-2 text-xs text-gray-500 truncate max-w-xs" title="{{ $login->user_agent }}">{{ Str::limit($login->user_agent, 50) }}</td>
                            <td class="px-4 py-2 text-right text-sm text-gray-600">{{ $login->created_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">Kayıt bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. TÜM PROJE HAREKETLERİ (LİMİTLİ VE GRUPLANMIŞ) --}}
    @php
        $rawLogs = \App\Models\IaaLog::where('user_id', $user->id)
            ->with('iaa')
            ->latest()
            ->take(50)
            ->get();

        $groupedLogs = $rawLogs->groupBy(function($item) {
            return $item->iaa ? $item->iaa->id : 'deleted';
        });
    @endphp

    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Son Proje ve Sistem Aktiviteleri
            </h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Son 50 İşlem</span>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 overflow-y-auto custom-scrollbar space-y-6" style="max-height: 500px;">
            @forelse($groupedLogs as $iaaId => $logs)
                <div class="relative pl-4 border-l-2 border-indigo-100">
                    <div class="mb-3 sticky top-0 bg-white z-10 py-1">
                        @if($iaaId !== 'deleted' && $logs->first()->iaa)
                            <a href="{{ route('proje.workspace.show', $iaaId) }}" class="text-sm font-bold text-indigo-700 hover:text-indigo-900 hover:underline flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded border border-indigo-200">#{{ $iaaId }}</span>
                                {{ Str::limit($logs->first()->iaa->baslik, 60) }}
                            </a>
                        @else
                            <span class="text-sm font-bold text-gray-500 flex items-center gap-2">
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded border border-gray-200">Silinmiş</span>
                                (Proje kaydı bulunamadı)
                            </span>
                        @endif
                    </div>

                    <ul class="space-y-3">
                        @foreach($logs as $log)
                            <li class="flex items-start group">
                                <div class="absolute -left-[5px] mt-1.5 w-2.5 h-2.5 rounded-full bg-gray-300 border-2 border-white group-hover:bg-indigo-500 transition-colors"></div>
                                <div class="flex-1 ml-2 bg-gray-50 rounded-lg p-2.5 hover:bg-indigo-50 transition-colors border border-gray-100 hover:border-indigo-100">
                                    <div class="flex justify-between items-start">
                                        <span class="text-xs font-bold text-gray-700 group-hover:text-indigo-700">{{ $log->eylem }}</span>
                                        <span class="text-[10px] text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $log->aciklama }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <div class="text-center text-gray-500 py-12 text-sm flex flex-col items-center">
                    <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Henüz bir aktivite kaydı yok.
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>

    {{-- 3. YÖNETİM LOGLARI --}}
    @if(isset($adminStats) && !empty($adminStats['son_yonetim_loglari']))
        <div>
            <h3 class="text-lg font-bold text-amber-700 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Kritik Yönetim İşlemleri
            </h3>
            <div class="bg-amber-50 rounded-xl shadow-sm border border-amber-200 p-4">
                <ul class="divide-y divide-amber-200/50">
                    @foreach($adminStats['son_yonetim_loglari'] as $log)
                        <li class="py-2 text-sm">
                            <span class="font-bold text-amber-900">{{ $log->eylem }}</span>
                            <span class="text-amber-700 text-xs ml-2">({{ $log->created_at->diffForHumans() }})</span>
                            <p class="text-amber-600 text-xs mt-0.5">{{ $log->aciklama }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>