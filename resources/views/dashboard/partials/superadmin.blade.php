<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    
    <a href="{{ route('admin.users.index') }}" class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>
                </div>
                @if($stats['onay_bekleyen_kullanici'] > 0)
                    <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm animate-pulse">{{ $stats['onay_bekleyen_kullanici'] }} Onay Bekliyor</span>
                @endif
            </div>
            
            <div class="flex justify-between items-end mb-4">
                <div>
                    <p class="text-sm font-medium text-blue-600/80">Toplam Kullanıcı</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['toplam_kullanici'] }}</h3>
                </div>
            </div>

            <div class="space-y-2 pt-3 border-t border-blue-200/50">
                <p class="text-xs font-semibold text-gray-500 mb-2">Son Eklenenler</p>
                @forelse($stats['son_kullanicilar']->take(3) as $user)
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2 truncate">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                            <span class="text-gray-700 font-medium truncate">{{ Str::limit($user->name, 15) }}</span>
                        </div>
                        <span class="text-gray-500">{{ $user->created_at->format('d.m') }}</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Kayıt yok.</p>
                @endforelse
            </div>
        </div>
    </a>

    <a href="{{ route('admin.iaa-yonetim.index') }}" class="group relative bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/5 to-teal-600/5 rounded-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
                <div class="flex flex-col items-end gap-1">
                    @if($stats['onay_bekleyen_iaa'] > 0)
                        <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $stats['onay_bekleyen_iaa'] }} Onay</span>
                    @endif
                    @if($stats['atama_bekleyen_iaa'] > 0)
                        <span class="bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $stats['atama_bekleyen_iaa'] }} Atama</span>
                    @endif
                </div>
            </div>

            <div class="flex justify-between items-end mb-4">
                <div>
                    <p class="text-sm font-medium text-emerald-600/80">Toplam Öneri</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['toplam_iaa'] }}</h3>
                </div>
            </div>

            <div class="space-y-2 pt-3 border-t border-emerald-200/50">
                <p class="text-xs font-semibold text-gray-500 mb-2">Son Gelen Öneriler</p>
                @forelse($stats['son_iaalar']->take(3) as $iaa)
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2 truncate w-3/4">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></div>
                            <span class="text-gray-700 font-medium truncate" title="{{ $iaa->baslik }}">{{ $iaa->baslik }}</span>
                        </div>
                        <span class="text-gray-500 flex-shrink-0">{{ $iaa->created_at->format('d.m') }}</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Öneri yok.</p>
                @endforelse
            </div>
        </div>
    </a>

    <a href="{{ route('takimlar.index') }}" class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-violet-600/5 rounded-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>

            <div class="flex justify-between items-end mb-4">
                <div>
                    <p class="text-sm font-medium text-purple-600/80">Toplam Takım</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['toplam_takim'] }}</h3>
                </div>
            </div>

            <div class="space-y-2 pt-3 border-t border-purple-200/50">
                <p class="text-xs font-semibold text-gray-500 mb-2">Son Kurulan Takımlar</p>
                @forelse($stats['son_takimlar']->take(3) as $takim)
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2 truncate">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-400"></div>
                            <span class="text-gray-700 font-medium truncate">{{ Str::limit($takim->ad, 15) }}</span>
                        </div>
                        <span class="bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded font-bold">{{ $takim->uyeler_count }} üye</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Takım yok.</p>
                @endforelse
            </div>
        </div>
    </a>
    
    <a href="{{ route('admin.sikayetler.index') }}" class="group relative bg-gradient-to-br from-red-50 to-orange-50 border border-red-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-red-600/5 to-orange-600/5 rounded-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </div>
                <div class="flex flex-col items-end gap-1">
                    @if($stats['yeni_sikayet'] > 0)
                        <span class="bg-yellow-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">{{ $stats['yeni_sikayet'] }} Yeni</span>
                    @endif
                    @if($stats['islemde_sikayet'] > 0)
                        <span class="bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $stats['islemde_sikayet'] }} İşlemde</span>
                    @endif
                </div>
            </div>

            <div class="flex justify-between items-end mb-4">
                <div>
                    <p class="text-sm font-medium text-red-600/80">Toplam Müşteri Şikayeti</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['toplam_sikayet'] }}</h3>
                </div>
            </div>

            <div class="space-y-2 pt-3 border-t border-red-200/50">
                <p class="text-xs font-semibold text-gray-500 mb-2">Son Gelen Şikayetler</p>
                @forelse($stats['son_sikayetler']->take(3) as $sikayet)
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2 truncate w-3/4">
                            <div class="w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></div>
                            <span class="text-gray-700 font-medium truncate" title="{{ $sikayet->musteri_sikayet_konusu }}">{{ $sikayet->musteri_sikayet_konusu }}</span>
                        </div>
                        <span class="text-gray-500 flex-shrink-0">{{ $sikayet->created_at->format('d.m') }}</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic">Şikayet yok.</p>
                @endforelse
            </div>
        </div>
    </a>
</div>

{{-- CANLI TAKİP VE EKSTRA TABLOLAR --}}
<div class="space-y-8 animate-fade-in-up">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span></span>
                    Anlık Online ({{ $onlineKullanicilar->count() }})
                </h3>
            </div>
            <div class="overflow-y-auto max-h-80">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0"><tr><th class="px-6 py-3">Kullanıcı</th><th class="px-6 py-3 text-right">Süre</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($onlineKullanicilar as $online)
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-900"><a href="{{ route('profile.show', $online->id) }}" target="_blank" class="flex items-center gap-3 group"><div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600 border group-hover:border-blue-400">{{ substr($online->name, 0, 1) }}</div><div class="flex flex-col"><span class="group-hover:text-blue-600 group-hover:underline">{{ $online->name }}</span><span class="text-xs text-gray-400 font-normal">{{ $online->getRoleNames()->first() ?? 'Kullanıcı' }}</span></div></a></td>
                                <td class="px-6 py-3 text-right text-green-600 text-xs font-bold">{{ \Carbon\Carbon::parse($online->last_seen_at)->diffForHumans() }}</td>
                            </tr>
                        @empty <tr><td colspan="2" class="px-6 py-8 text-center text-gray-400 italic">Aktif yok.</td></tr> @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Son Görülen 10 Kişi
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Kullanıcı</th>
                            <th class="px-6 py-3 text-right">Zaman</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sonAktifKullanicilar as $last)
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-900">
                                    <a href="{{ route('profile.show', $last->id) }}" target="_blank" class="hover:text-blue-600 hover:underline flex items-center gap-2">
                                        {{ $last->name }} <span class="text-xs text-gray-400 font-normal">({{ $last->getRoleNames()->first() ?? '-' }})</span>
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-right text-gray-500 text-xs">{{ \Carbon\Carbon::parse($last->last_seen_at)->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-purple-50 flex justify-between items-center"><h3 class="font-bold text-gray-800">Son Tamamlanan İAA</h3><a href="{{ route('admin.iaa-yonetim.index') }}" onclick="localStorage.setItem('activeTab', 'tamamlananlar')" class="text-xs text-purple-600 hover:underline font-semibold cursor-pointer">Tümünü Gör →</a></div>
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-3">Proje</th><th class="px-4 py-3">Takım</th><th class="px-4 py-3 text-right">Tarih</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($ekstraTablolar['son_tamamlanan_iaa'] ?? [] as $iaa)<tr class="bg-white hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-900"><a href="{{ route('proje.workspace.show', $iaa->id) }}" target="_blank" class="hover:text-purple-600 hover:underline block truncate max-w-[180px]">{{ Str::limit($iaa->baslik, 25) }}</a></td><td class="px-4 py-3 text-xs">{{ $iaa->atananTakim->ad ?? '-' }}</td><td class="px-4 py-3 text-right text-xs">{{ $iaa->updated_at->format('d.m.Y') }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>@endforelse</tbody></table>
        </div>
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex justify-between items-center"><h3 class="font-bold text-gray-800">Son Çözülen Şikayetler</h3><a href="{{ route('admin.sikayetler.index') }}" class="text-xs text-red-600 hover:underline font-semibold">Tümünü Gör →</a></div>
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-3">Konu</th><th class="px-4 py-3">Takım</th><th class="px-4 py-3 text-right">Tarih</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($ekstraTablolar['son_cozulen_sikayetler'] ?? [] as $sikayet)<tr class="bg-white hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-900"><a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" target="_blank" class="hover:text-red-600 hover:underline block truncate max-w-[180px]">{{ Str::limit($sikayet->musteri_sikayet_konusu, 25) }}</a></td><td class="px-4 py-3 text-xs">{{ $sikayet->cozumTakimi->ad ?? '-' }}</td><td class="px-4 py-3 text-right text-xs">{{ $sikayet->updated_at->format('d.m.Y') }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>@endforelse</tbody></table>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50"><h3 class="font-bold text-gray-800">Son Kurulan Takımlar</h3></div>
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-3">Takım</th><th class="px-4 py-3">Lider</th><th class="px-4 py-3 text-right">Tarih</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($ekstraTablolar['son_takimlar'] ?? [] as $takim)<tr class="bg-white hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-900"><a href="{{ route('takimlar.show', $takim->id) }}" target="_blank" class="hover:text-indigo-600 hover:underline">{{ $takim->ad }}</a></td><td class="px-4 py-3 text-xs">@if($takim->lider)<a href="{{ route('profile.show', $takim->lider_user_id) }}" target="_blank" class="hover:text-indigo-600 hover:underline">{{ $takim->lider->name }}</a>@else - @endif</td><td class="px-4 py-3 text-right text-xs">{{ $takim->created_at->format('d.m.Y') }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>@endforelse</tbody></table>
        </div>
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-yellow-50"><h3 class="font-bold text-gray-800">Son Kazanılan Puanlar</h3></div>
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-3">Proje</th><th class="px-4 py-3">Takım</th><th class="px-4 py-3 text-right">Puan</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($ekstraTablolar['son_kazanilan_puanlar'] ?? [] as $iaa)<tr class="bg-white hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-900 truncate max-w-[150px]"><a href="{{ route('proje.workspace.show', $iaa->id) }}" target="_blank" class="hover:text-yellow-600 hover:underline">{{ Str::limit($iaa->baslik, 25) }}</a></td><td class="px-4 py-3 text-xs">{{ $iaa->atananTakim->ad ?? '-' }}</td><td class="px-4 py-3 text-right font-bold text-green-600">+{{ number_format($iaa->puan, 0) }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>@endforelse</tbody></table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Son Yapılan Profil Yorumları</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-6 py-3">Yazan</th><th class="px-6 py-3">Kime</th><th class="px-6 py-3">Yorum</th><th class="px-6 py-3 text-right">Zaman</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($ekstraTablolar['son_profil_yorumlari'] ?? [] as $yorum)<tr class="bg-white hover:bg-gray-50"><td class="px-6 py-3 font-medium text-gray-900"><a href="{{ route('profile.show', $yorum->yazan_user_id) }}" target="_blank" class="hover:text-blue-600 hover:underline">{{ $yorum->yazan->name ?? 'Bilinmiyor' }}</a></td><td class="px-6 py-3 text-blue-600"><a href="{{ route('profile.show', $yorum->user_id) }}" target="_blank" class="hover:underline">{{ $yorum->profileUser->name ?? 'Bilinmiyor' }}</a></td><td class="px-6 py-3 italic text-gray-600 truncate max-w-md" title="{{ strip_tags($yorum->yorum) }}">{{ Str::limit(strip_tags($yorum->yorum), 60) }}</td><td class="px-6 py-3 text-right text-xs whitespace-nowrap">{{ $yorum->created_at->diffForHumans() }}</td></tr>@empty<tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Yorum yok.</td></tr>@endforelse</tbody></table>
        </div>
    </div>
</div>