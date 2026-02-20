<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('iaa.havuz') }}"
        class="group relative bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/5 to-teal-600/5 rounded-2xl"></div>
        <div class="relative">
            <div
                class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Havuzdaki Öneriler</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['havuz_oneri_sayisi'] }}</p>
            <div class="space-y-2">
                @forelse($stats['son_havuz_onerileri'] as $iaa)
                    <div class="flex justify-between items-center text-sm">
                        <span
                            class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($iaa->baslik, 20) }}</span>
                        <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">
                            {{ optional($iaa->created_at)->format('d.m.Y') ?? '-' }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm italic">Havuzda öneri yok.</p>
                @endforelse
            </div>
            <div class="flex items-center mt-6 text-emerald-600 font-semibold text-sm group-hover:text-emerald-700">
                <span>Havuzu İncele</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('takimlar.index') }}"
        class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
        <div class="relative">
            <div
                class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Takımlarım</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['takimlarim_sayisi'] }}</p>
            <div class="space-y-2">
                @forelse($stats['son_takimlarim'] as $takim)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-700 font-medium">{{ $takim->ad }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm italic">Henüz bir takıma üye değilsiniz.</p>
                @endforelse
            </div>
            <div class="flex items-center mt-6 text-blue-600 font-semibold text-sm group-hover:text-blue-700">
                <span>Takımlarımı Yönet</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('takimlar.index') }}"
        class="group relative bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-violet-600/5 rounded-2xl"></div>
        <div class="relative">
            <div
                class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Katılıma Açık Takımlar</h3>
            <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['acik_takim_sayisi'] }}</p>
            <div class="space-y-2">
                @forelse($stats['son_acik_takimlar'] as $takim)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-700 font-medium">{{ $takim->ad }}</span>
                        <span
                            class="text-purple-600 text-xs bg-purple-100 px-2 py-1 rounded-md font-medium">{{ $takim->uyeler_count }}
                            üye</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm italic">Katılıma açık takım yok.</p>
                @endforelse
            </div>
            <div class="flex items-center mt-6 text-purple-600 font-semibold text-sm group-hover:text-purple-700">
                <span>Takımlara Göz At</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
    </a>

    {{-- DEVAM EDEN İAA PROJELERİ --}}
    @if(isset($stats['iaa_projelerim_count']))
        <a href="{{ route('iaa.takimProjeleri') }}"
            class="group relative bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-600/5 to-orange-600/5 rounded-2xl"></div>
            <div class="relative">
                <div
                    class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Devam Eden İAA Projelerim</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['iaa_projelerim_count'] }}</p>
                <div class="space-y-2">
                    @forelse($stats['son_iaa_projelerim'] as $proje)
                        <div class="flex justify-between items-center text-sm">
                            <span
                                class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($proje->baslik, 20) }}</span>
                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">
                                {{ optional($proje->onaylanma_tarihi)->format('d.m.Y') ?? '-' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm italic">Devam eden İAA projeniz yok.</p>
                    @endforelse
                </div>
                <div class="flex items-center mt-6 text-yellow-600 font-semibold text-sm group-hover:text-yellow-700">
                    <span>Projelerime Git</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </a>
    @endif

    {{-- DEVAM EDEN ŞİKAYET PROJELERİ --}}
    @if(isset($stats['sikayet_projelerim_count']))
        <a href="{{ route('sikayet-gorevlerim.index') }}"
            class="group relative bg-gradient-to-br from-red-50 to-pink-50 border border-red-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-600/5 to-pink-600/5 rounded-2xl"></div>
            <div class="relative">
                <div
                    class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Devam Eden Şikayet Projelerim</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['sikayet_projelerim_count'] }}</p>
                <div class="space-y-2">
                    @forelse($stats['son_sikayet_projelerim'] as $proje)
                        <div class="flex justify-between items-center text-sm">
                            <span
                                class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($proje->baslik, 20) }}</span>
                            <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-md whitespace-nowrap">
                                {{ optional($proje->onaylanma_tarihi)->format('d.m.Y') ?? '-' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm italic">Devam eden şikayet projeniz yok.</p>
                    @endforelse
                </div>
                <div class="flex items-center mt-6 text-red-600 font-semibold text-sm group-hover:text-red-700">
                    <span>Projelerime Git</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </a>
    @endif

    {{-- YENİ: ONAY BEKLEYEN ŞİKAYET PROJELERİ --}}
    @if(isset($stats['onay_bekleyen_sikayet_count']) && $stats['onay_bekleyen_sikayet_count'] > 0)
        <a href="{{ route('sikayet-gorevlerim.index') }}"
            class="group relative bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/5 to-blue-600/5 rounded-2xl"></div>
            <div class="relative">
                <div
                    class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 mb-4 animate-pulse">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Onay Bekleyen Şikayet Projelerim</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4">{{ $stats['onay_bekleyen_sikayet_count'] }}</p>
                <div class="space-y-2">
                    @forelse($stats['son_onay_bekleyen_sikayetler'] as $proje)
                        <div class="flex justify-between items-center text-sm">
                            <span
                                class="text-gray-700 font-medium truncate flex-1 mr-2">{{ Str::limit($proje->baslik, 20) }}</span>
                            <span
                                class="text-indigo-600 text-xs bg-indigo-100 px-2 py-1 rounded-md whitespace-nowrap font-bold">
                                Onayda
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm italic">Onay bekleyen proje yok.</p>
                    @endforelse
                </div>
                <div class="flex items-center mt-6 text-indigo-600 font-semibold text-sm group-hover:text-indigo-700">
                    <span>Detayları Gör</span><svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </a>
    @endif

    @if(isset($bekleyenAdimGorevleri) && $bekleyenAdimGorevleri->count() > 0)
        <div
            class="group relative bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 rounded-2xl p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden col-span-1 md:col-span-2 xl:col-span-1">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600/5 to-amber-600/5 rounded-2xl"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-md shadow-orange-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded-full animate-pulse">
                        {{ $bekleyenAdimGorevleri->count() }} Görev Bekliyor
                    </span>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-1">Adım Görevlerim</h3>
                <p class="text-xs text-gray-500 mb-4">Size özel atanmış tamamlanmayı bekleyen adımlar.</p>

                <div class="space-y-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($bekleyenAdimGorevleri as $gorev)
                        <a href="{{ route('proje.workspace.show', $gorev->iaa_id) }}"
                            class="block bg-white p-3 rounded-lg border border-orange-100 shadow-sm hover:border-orange-300 hover:shadow-md transition-all group/item">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs font-bold text-orange-600 mb-0.5 uppercase tracking-wide">Adım:
                                        {{ $gorev->adim_adi }}
                                    </p>
                                    <p
                                        class="text-sm font-semibold text-gray-800 leading-tight group-hover/item:text-orange-700 transition-colors">
                                        {{ Str::limit($gorev->proje_baslik, 40) }}
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 group-hover/item:text-orange-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 text-right">
                                {{ \Carbon\Carbon::parse($gorev->atama_tarihi)->diffForHumans() }} atandı
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


</div>