<div class="space-y-8">
    
    <!-- 1. ÜST KARTLAR (GRID) -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        <!-- KULLANICILAR -->
        <a href="{{ route('admin.users.index') }}" class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path></svg>
                    </div>
                    @if($stats['onay_bekleyen_kullanici'] > 0)
                        <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm animate-pulse">{{ $stats['onay_bekleyen_kullanici'] }} Onay</span>
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

        <!-- MÜŞTERİLER (YENİ) -->
        <a href="{{ route('admin.musteriler.index') }}" class="group relative bg-gradient-to-br from-cyan-50 to-sky-50 border border-cyan-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-600/5 to-sky-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-cyan-600/80">Toplam Müşteri</p>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['toplam_musteri'] ?? 0 }}</h3>
                    </div>
                </div>
                <div class="space-y-2 pt-3 border-t border-cyan-200/50">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Son Eklenenler</p>
                    @forelse($stats['son_musteriler_listesi'] ?? [] as $customer)
                        <div class="flex justify-between items-center text-xs">
                            <div class="flex items-center gap-2 truncate">
                                <div class="w-1.5 h-1.5 rounded-full bg-cyan-400"></div>
                                <span class="text-gray-700 font-medium truncate">{{ Str::limit($customer->name, 15) }}</span>
                            </div>
                            <span class="text-gray-500">{{ $customer->created_at->format('d.m') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">Müşteri yok.</p>
                    @endforelse
                </div>
            </div>
        </a>

        <!-- İAA / ÖNERİLER (SAF IAA) -->
        <a href="{{ route('admin.iaa-yonetim.index') }}" class="group relative bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/5 to-teal-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        @if($stats['onay_bekleyen_iaa'] > 0)
                            <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">{{ $stats['onay_bekleyen_iaa'] }} Onay</span>
                        @endif
                        @if($stats['atama_bekleyen_iaa'] > 0)
                            <span class="bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $stats['atama_bekleyen_iaa'] }} Atama</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-emerald-600/80">Toplam İAA (Proje)</p>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['toplam_iaa'] }}</h3>
                    </div>
                </div>
                <div class="space-y-2 pt-3 border-t border-emerald-200/50">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Son Saf İAA Projeleri</p>
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

        <!-- TAKIMLAR -->
        <a href="{{ route('admin.takim-yonetim.index') }}" class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
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
        
        <!-- MÜŞTERİ ŞİKAYETLERİ -->
        <a href="{{ route('admin.sikayetler.index') }}" class="group relative bg-gradient-to-br from-red-50 to-orange-50 border border-red-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-600/5 to-orange-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        @if(($stats['onay_bekleyen_sikayet'] ?? 0) > 0)
                            <span class="bg-purple-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">{{ $stats['onay_bekleyen_sikayet'] }} Onay</span>
                        @endif
                        @if($stats['yeni_sikayet'] > 0)
                            <span class="bg-yellow-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">{{ $stats['yeni_sikayet'] }} Yeni</span>
                        @endif
                        @if($stats['islemde_sikayet'] > 0)
                            <span class="bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">{{ $stats['islemde_sikayet'] }} İşlemde</span>
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

        <!-- DİSİPLİN (YENİ) -->
        <a href="{{ route('admin.disiplin.index') }}" class="group relative bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-rose-600/5 to-pink-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-rose-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-rose-600/80">Disiplin Süreci</p>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['aktif_disiplin'] }} <span class="text-sm text-gray-500 font-normal">/ {{ $stats['toplam_disiplin'] }}</span></h3>
                    </div>
                </div>
                <div class="pt-3 border-t border-rose-200/50">
                    <p class="text-xs text-rose-600">Aktif Vakalar / Toplam</p>
                </div>
            </div>
        </a>

         <!-- ARABULUCULUK (YENİ) -->
         <a href="{{ route('admin.arabuluculuk.index') }}" class="group relative bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 rounded-2xl p-5 hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600/5 to-amber-600/5 rounded-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-sm font-medium text-orange-600/80">Arabuluculuk</p>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['aktif_arabuluculuk'] }} <span class="text-sm text-gray-500 font-normal">/ {{ $stats['toplam_arabuluculuk'] }}</span></h3>
                    </div>
                </div>
                <div class="pt-3 border-t border-orange-200/50">
                    <p class="text-xs text-orange-600">Aktif Süreçler / Toplam</p>
                </div>
            </div>
        </a>

    </div>

    {{-- İADE TABLOSU (AŞAĞIYA TAŞINDI) --}}

    {{-- CANLI TAKİP VE EKSTRA TABLOLAR --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-fade-in-up">
        
        <div class="bg-white rounded-xl shadow-md border border-gray-100 relative" x-data="{ showTooltip: false, logs: [] }">
            <!-- TOOLTIP OVERLAY (TABLODAN BAĞIMSIZ) -->
            <div x-show="showTooltip" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="absolute top-16 right-4 z-[100] w-[280px] bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden pointer-events-none">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Son Giriş Kayıtları</span>
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="p-2">
                    <table class="w-full">
                        <template x-for="log in logs" :key="log.date">
                            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                <td class="py-2 px-3 text-xs text-gray-600 font-medium">
                                    <div x-text="log.date"></div>
                                    <div class="text-[9px] text-gray-300 font-light" x-text="log.ago"></div>
                                </td>
                                <td class="py-2 px-3 text-[10px] text-gray-400 text-right font-mono tracking-tighter" x-text="log.ip"></td>
                            </tr>
                        </template>
                        <tr x-show="logs.length === 0"><td colspan="2" class="p-4 text-center text-xs text-gray-400 italic">Kayıt yok.</td></tr>
                    </table>
                </div>
            </div>

            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span></span>
                    Anlık Online ({{ $onlineKullanicilar->count() }})
                </h3>
            </div>
            <div class="overflow-y-auto max-h-80 relative">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0 z-10"><tr><th class="px-6 py-3">Kullanıcı</th><th class="px-6 py-3">Bölüm</th><th class="px-6 py-3 text-right">Süre</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($onlineKullanicilar as $online)
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-900 relative" 
                                    @mouseenter="logs = {{ $online->loginActivities->slice(0, 7)->map(fn($l) => ['date' => $l->created_at->format('d.m.Y H:i'), 'ip' => $l->ip_address, 'ago' => $l->created_at->diffForHumans()]) }}; showTooltip = true" 
                                    @mouseleave="showTooltip = false">
                                    <div class="group relative inline-block">
                                        <a href="{{ route('profile.show', $online->id) }}" target="_blank" class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600 border group-hover:border-blue-400">{{ substr($online->name, 0, 1) }}</div>
                                            <div class="flex flex-col"><span class="group-hover:text-blue-600 group-hover:underline">{{ $online->name }}</span><span class="text-xs text-gray-400 font-normal">{{ $online->getRoleNames()->first() ?? 'Kullanıcı' }}</span></div>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-600">{{ $online->bolum->ad ?? '-' }}</td>
                                <td class="px-6 py-3 text-right text-green-600 text-xs font-bold">{{ \Carbon\Carbon::parse($online->last_seen_at)->diffForHumans() }}</td>
                            </tr>
                        @empty <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">Aktif yok.</td></tr> @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 relative" x-data="{ showTooltip: false, logs: [] }">
            <!-- TOOLTIP OVERLAY (TABLODAN BAĞIMSIZ) -->
            <div x-show="showTooltip" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="absolute top-16 right-4 z-[100] w-[280px] bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden pointer-events-none">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Son Giriş Kayıtları</span>
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="p-2">
                    <table class="w-full">
                        <template x-for="log in logs" :key="log.date">
                            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                <td class="py-2 px-3 text-xs text-gray-600 font-medium">
                                    <div x-text="log.date"></div>
                                    <div class="text-[9px] text-gray-300 font-light" x-text="log.ago"></div>
                                </td>
                                <td class="py-2 px-3 text-[10px] text-gray-400 text-right font-mono tracking-tighter" x-text="log.ip"></td>
                            </tr>
                        </template>
                        <tr x-show="logs.length === 0"><td colspan="2" class="p-4 text-center text-xs text-gray-400 italic">Kayıt yok.</td></tr>
                    </table>
                </div>
            </div>

            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Son Görülen 10 Kişi
                </h3>
                <a href="{{ route('logs.login.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wider">Tümünü Gör &rarr;</a>
            </div>
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Kullanıcı</th>
                            <th class="px-6 py-3">Bölüm</th>
                            <th class="px-6 py-3 text-right">Zaman</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sonAktifKullanicilar as $last)
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-900 relative"
                                    @mouseenter="logs = {{ $last->loginActivities->slice(0, 7)->map(fn($l) => ['date' => $l->created_at->format('d.m.Y H:i'), 'ip' => $l->ip_address, 'ago' => $l->created_at->diffForHumans()]) }}; showTooltip = true" 
                                    @mouseleave="showTooltip = false">
                                    <a href="{{ route('profile.show', $last->id) }}" target="_blank" class="hover:text-blue-600 hover:underline flex items-center gap-2">
                                        {{ $last->name }} <span class="text-xs text-gray-400 font-normal">({{ $last->getRoleNames()->first() ?? '-' }})</span>
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-600">{{ $last->bolum->ad ?? '-' }}</td>
                                <td class="px-6 py-3 text-right text-gray-500 text-xs">{{ \Carbon\Carbon::parse($last->last_seen_at)->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- SON TAMAMLANAN IAA -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-purple-50 flex justify-between items-center"><h3 class="font-bold text-gray-800">Son Tamamlanan İAA (Saf) ({{ $ekstraTablolar['son_tamamlanan_iaa']->count() }})</h3><a href="{{ route('admin.iaa-yonetim.index') }}" onclick="localStorage.setItem('activeTab', 'tamamlananlar')" class="text-xs text-purple-600 hover:underline font-semibold cursor-pointer">Tümünü Gör →</a></div>
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-3">Proje</th><th class="px-4 py-3">Takım</th><th class="px-4 py-3 text-right">Tarih</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($ekstraTablolar['son_tamamlanan_iaa'] ?? [] as $iaa)<tr class="bg-white hover:bg-gray-50"><td class="px-4 py-3 font-medium text-gray-900"><a href="{{ route('proje.workspace.show', $iaa->id) }}" target="_blank" class="hover:text-purple-600 hover:underline block truncate max-w-[180px]">{{ Str::limit($iaa->baslik, 25) }}</a></td><td class="px-4 py-3 text-xs">{{ $iaa->atananTakim->ad ?? '-' }}</td><td class="px-4 py-3 text-right text-xs">{{ $iaa->updated_at->format('d.m.Y') }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>@endforelse</tbody></table>
        </div>
        <!-- SON ÇÖZÜLEN ŞİKAYETLER (MÜŞTERİ EKLENDİ) -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex justify-between items-center"><h3 class="font-bold text-gray-800">Son Çözülen Şikayetler ({{ $ekstraTablolar['son_cozulen_sikayetler']->count() }})</h3><a href="{{ route('admin.sikayetler.index') }}" class="text-xs text-red-600 hover:underline font-semibold">Tümünü Gör →</a></div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Konu</th>
                        <th class="px-4 py-3">Müşteri</th>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ekstraTablolar['son_cozulen_sikayetler'] ?? [] as $sikayet)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" target="_blank" class="hover:text-red-600 hover:underline block truncate max-w-[150px]">{{ Str::limit($sikayet->musteri_sikayet_konusu, 20) }}</a>
                                @if($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Talep Olarak Kapatıldı']))
                                    <div class="mt-1">
                                        {!! $sikayet->iaaProjesi->durum_etiketi !!}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($sikayet->customer)
                                    <a href="{{ route('musteri.profil.show', $sikayet->customer->id) }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $sikayet->customer->name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $sikayet->cozumTakimi->ad ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-xs">{{ $sikayet->updated_at->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- SON KURULAN TAKIMLAR (SEKMELİ) -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden" x-data="{ activeTab: 'iaa' }">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Son Kurulan Takımlar</h3>
                <!-- Sekmeler -->
                <div class="flex space-x-2 text-xs">
                    <button @click="activeTab = 'iaa'" :class="activeTab === 'iaa' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-3 py-1 rounded-md transition-colors font-semibold shadow-sm border border-transparent">
                        Bireysel (İAA)
                    </button>
                    <button @click="activeTab = 'sikayet'" :class="activeTab === 'sikayet' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-3 py-1 rounded-md transition-colors font-semibold shadow-sm border border-transparent">
                        Şikayet Çözüm
                    </button>
                </div>
            </div>
            
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Takım</th>
                        <th class="px-4 py-3">Lider</th>
                        <th class="px-4 py-3 text-right">Tarih</th>
                    </tr>
                </thead>
                <!-- IAA TAKIMLARI -->
                <tbody x-show="activeTab === 'iaa'" class="divide-y divide-gray-100">
                    @forelse($ekstraTablolar['son_iaa_takimlari'] ?? [] as $takim)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('takimlar.show', $takim->id) }}" target="_blank" class="hover:text-indigo-600 hover:underline flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                    {{ $takim->ad }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($takim->lider)
                                    <a href="{{ route('profile.show', $takim->lider_user_id) }}" target="_blank" class="hover:text-indigo-600 hover:underline">{{ $takim->lider->name }}</a>
                                @else - @endif
                            </td>
                            <td class="px-4 py-3 text-right text-xs">{{ $takim->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">İAA takımı bulunamadı.</td></tr>
                    @endforelse
                </tbody>
                <!-- ŞİKAYET TAKIMLARI -->
                <tbody x-show="activeTab === 'sikayet'" style="display: none;" class="divide-y divide-gray-100">
                    @forelse($ekstraTablolar['son_sikayet_takimlari'] ?? [] as $takim)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('takimlar.show', $takim->id) }}" target="_blank" class="hover:text-purple-600 hover:underline flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                    {{ $takim->ad }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($takim->lider)
                                    <a href="{{ route('profile.show', $takim->lider_user_id) }}" target="_blank" class="hover:text-purple-600 hover:underline">{{ $takim->lider->name }}</a>
                                @else - @endif
                            </td>
                            <td class="px-4 py-3 text-right text-xs">{{ $takim->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Şikayet takımı bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- SON DİSİPLİN VAKALARI (YENİ) -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-rose-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Son Disiplin Vakaları ({{ $ekstraTablolar['son_disiplin_vakalari']->count() }})</h3>
                <a href="{{ route('admin.disiplin.index') }}" class="text-xs text-rose-600 hover:underline font-semibold">Tümünü Gör →</a>
            </div>
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Kişi</th>
                        <th class="px-4 py-3">Suç / İhlal</th>
                        <th class="px-4 py-3 text-right">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ekstraTablolar['son_disiplin_vakalari'] ?? [] as $case)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('profile.show', $case->user_id) }}" target="_blank" class="flex items-center gap-2 group">
                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                    <span class="group-hover:text-rose-600 group-hover:underline">{{ $case->user->name ?? '-' }}</span>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-700">
                                 <a href="{{ route('admin.disiplin.show', $case->id) }}" target="_blank" class="hover:text-rose-600 hover:underline" title="{{ $case->behavior->tanim ?? '-' }}">
                                    {{ Str::limit($case->behavior->tanim ?? '-', 35) }}
                                 </a>
                            </td>
                            <td class="px-4 py-3 text-right text-xs">{{ $case->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">Veri yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- İADE TABLOSU (ARA KISIM - YENİ YER: V5.12) --}}
    @if(isset($iadeVerileri))
        <div class="col-span-1 xl:col-span-2">
            @include('dashboard.partials.iadeler-tablosu')
        </div>
    @endif

    <!-- SON KAZANILAN PUANLAR (DETAYLI + RESİMLİ) -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden col-span-1 xl:col-span-2">
            <div class="px-6 py-4 border-b border-gray-100 bg-emerald-500 text-white flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Kazanılan Puanların Dökümü</h3>
                        <p class="text-xs text-emerald-100 opacity-90">Son 10 puan kaydı</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">TİP</th>
                            <th class="px-6 py-3">AÇIKLAMA</th>
                            <th class="px-6 py-3">Kişi / Takım</th>
                            <th class="px-6 py-3">Kategori / Bölüm</th>
                            <th class="px-6 py-3">TARİH</th>
                            <th class="px-6 py-3 text-right">PUAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ekstraTablolar['son_kazanilan_puanlar'] ?? [] as $stats)
                            <tr class="bg-white hover:bg-gray-50 group transition-colors">
                                <!-- TİP -->
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded text-[10px] font-bold whitespace-nowrap {{ $stats['badge_color'] }}">
                                        {{ $stats['tip'] }}
                                    </span>
                                </td>
                                <!-- AÇIKLAMA (TIKLANABİLİR) -->
                                <td class="px-6 py-4 font-medium text-gray-900 group-hover:text-emerald-600 transition-colors">
                                    <a href="{{ $stats['url'] ?? '#' }}" target="_blank" class="hover:underline" title="{{ $stats['baslik'] }}">
                                        {{ Str::limit($stats['baslik'], 40) }}
                                    </a>
                                </td>
                                <!-- KİŞİ / TAKIM (RESİMLİ) -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($stats['user'])
                                            <a href="{{ route('profile.show', $stats['user']->id) }}" target="_blank" class="flex items-center gap-2 group/user">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border border-gray-200">
                                                    @if($stats['user']->profile_photo_path)
                                                        <img src="/storage/{{ $stats['user']->profile_photo_path }}" alt="{{ $stats['user']->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-xs font-bold text-gray-500">{{ substr($stats['user']->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-gray-700 group-hover/user:text-blue-600 group-hover/user:underline">{{ $stats['user']->name }}</span>
                                                    <span class="text-[10px] text-gray-400">{{ $stats['takim'] }}</span>
                                                </div>
                                            </a>
                                        @else
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-700">-</span>
                                                <span class="text-[10px] text-gray-400">{{ $stats['takim'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <!-- KATEGORİ -->
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    {{ $stats['kategori'] }}
                                    @if(isset($stats['user']->bolum))
                                        <div class="text-[10px] text-gray-400 mt-0.5">{{ $stats['user']->bolum->ad ?? '-' }}</div>
                                    @endif
                                </td>
                                <!-- TARİH -->
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $stats['tarih']->format('d.m.Y H:i') }}
                                </td>
                                <!-- PUAN -->
                                <td class="px-6 py-4 text-right">
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg font-bold shadow-sm">
                                        +{{ $stats['puan'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">Henüz puan kaydı yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SON YORUMLAR -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50"><h3 class="font-bold text-gray-800">Son Yapılan Profil Yorumları</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-6 py-3">Yazan</th><th class="px-6 py-3">Kime</th><th class="px-6 py-3">Yorum</th><th class="px-6 py-3 text-right">Zaman</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($ekstraTablolar['son_profil_yorumlari'] ?? [] as $yorum)<tr class="bg-white hover:bg-gray-50"><td class="px-6 py-3 font-medium text-gray-900"><a href="{{ route('profile.show', $yorum->yazan_user_id) }}" target="_blank" class="hover:text-blue-600 hover:underline">{{ $yorum->yazan->name ?? 'Bilinmiyor' }}</a></td><td class="px-6 py-3 text-blue-600"><a href="{{ route('profile.show', $yorum->user_id) }}" target="_blank" class="hover:underline">{{ $yorum->profileUser->name ?? 'Bilinmiyor' }}</a></td><td class="px-6 py-3 italic text-gray-600 truncate max-w-md"><a href="{{ route('profile.show', $yorum->user_id) }}" target="_blank" class="hover:text-gray-900 hover:underline" title="{{ strip_tags($yorum->yorum) }}">{{ Str::limit(strip_tags($yorum->yorum), 60) }}</a></td><td class="px-6 py-3 text-right text-xs whitespace-nowrap">{{ $yorum->created_at->diffForHumans() }}</td></tr>@empty<tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Yorum yok.</td></tr>@endforelse</tbody></table>
        </div>
    </div>
</div>