<div class="space-y-8 animate-fade-in-up">
    
    {{-- 1. BÖLÜM: YÖNETİCİ ÖZET KARTLARI (KPI) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        <a href="{{ route('admin.iaa-yonetim.index') }}" onclick="localStorage.setItem('activeTab', 'onay-bekleyenler')" 
           class="group relative bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative z-10 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    @if($stats['bolum_onay_sayisi'] > 0)
                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold animate-pulse">{{ $stats['bolum_onay_sayisi'] }} Bekleyen</span>
                    @endif
                </div>
                <p class="text-indigo-100 text-sm font-medium">Onayınızı Bekleyen Projeler</p>
                <h3 class="text-4xl font-bold mt-1">{{ $stats['bolum_onay_sayisi'] }}</h3>
                <div class="mt-4 flex items-center text-xs text-indigo-200 font-medium group-hover:text-white transition-colors">
                    İncele ve Onayla <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.sikayetler.index') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 group-hover:bg-blue-100 text-blue-600 transition-colors rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </div>
                <span class="text-gray-400 text-xs group-hover:text-blue-500 transition-colors">Tümünü Gör →</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ $stats['toplam_sikayet'] }}</h3>
            
            {{-- Sorumlu Olduğu Kategorileri Listele --}}
            <div class="mt-3">
                <p class="text-gray-500 text-xs mb-2">Sorumlu Olduğunuz Alanlar:</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(Auth::user()->yonettigiSikayetKategorileri as $kategori)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $kategori->ad }}
                        </span>
                    @endforeach
                </div>
            </div>
        </a>

        <a href="{{ route('admin.sikayetler.index') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-yellow-200 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-50 group-hover:bg-yellow-100 text-yellow-600 transition-colors rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-gray-400 text-xs group-hover:text-yellow-600 transition-colors">Listeyi Aç →</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ $stats['islemdeki_sikayet'] }}</h3>
            <p class="text-gray-500 text-sm mt-1">Çözüm bekleyen şikayetler</p>
            <div class="mt-4 w-full bg-gray-100 rounded-full h-1.5">
                @php 
                    $oran = $stats['toplam_sikayet'] > 0 ? ($stats['islemdeki_sikayet'] / $stats['toplam_sikayet']) * 100 : 0; 
                @endphp
                <div class="bg-yellow-400 h-1.5 rounded-full" style="width: {{ $oran }}%"></div>
            </div>
        </a>

        <a href="{{ route('admin.sikayetler.index') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-green-200 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 group-hover:bg-green-100 text-green-600 transition-colors rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-gray-400 text-xs group-hover:text-green-600 transition-colors">Listeyi Aç →</span>
            </div>
            <h3 class="text-3xl font-bold text-gray-800">{{ $stats['cozulen_sikayet'] }}</h3>
            <p class="text-green-600 text-sm mt-1 font-medium flex items-center">
                @if($stats['toplam_sikayet'] > 0)
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    %{{ round(($stats['cozulen_sikayet'] / $stats['toplam_sikayet']) * 100) }} Başarı Oranı
                @else
                    %0 Başarı
                @endif
            </p>
        </a>
    </div>

    {{-- 2. BÖLÜM: LİSTELER --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                    Onayınızı Bekleyenler
                </h3>
                <a href="{{ route('admin.iaa-yonetim.index') }}" onclick="localStorage.setItem('activeTab', 'onay-bekleyenler')" class="text-xs text-purple-600 hover:underline font-semibold">Tümünü Yönet</a>
            </div>
            <div class="flex-1 overflow-y-auto max-h-80">
                @if($stats['onay_bekleyen_liste']->isNotEmpty())
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-gray-100">
                            @foreach($stats['onay_bekleyen_liste'] as $proje)
                                <tr class="hover:bg-purple-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center font-bold text-xs">
                                                    #{{ $proje->id }}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900 group-hover:text-purple-700 transition-colors">{{ Str::limit($proje->baslik, 40) }}</p>
                                                <p class="text-xs text-gray-500">{{ $proje->atananTakim->ad ?? 'Takım Yok' }} • {{ $proje->updated_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('proje.workspace.show', $proje->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-white border border-purple-200 rounded-lg text-xs font-bold text-purple-700 shadow-sm hover:bg-purple-50 transition-colors">
                                            İncele
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm">Şu an onayınızı bekleyen proje yok.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    Departman Şikayet Akışı
                </h3>
            </div>
            <div class="flex-1 overflow-y-auto max-h-80">
                @if($stats['son_departman_sikayetleri']->isNotEmpty())
                    <div class="space-y-0">
                        @foreach($stats['son_departman_sikayetleri'] as $sikayet)
                            <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" class="block px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition-colors group">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded 
                                        {{ $sikayet->musteri_durum == 'Kapatıldı' ? 'bg-green-100 text-green-700' : 
                                          ($sikayet->musteri_durum == 'Yeni' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ $sikayet->musteri_durum }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $sikayet->created_at->format('d.m.Y') }}</span>
                                </div>
                                <p class="text-sm font-medium text-gray-800 mb-1 group-hover:text-blue-600 transition-colors">{{ Str::limit($sikayet->musteri_sikayet_konusu, 60) }}</p>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span>Müşteri: {{ Str::limit($sikayet->musteri_adi, 15) }}</span>
                                    <span>•</span>
                                    <span>Takım: {{ $sikayet->cozumTakimi->ad ?? 'Atanmadı' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                        <p class="text-sm">Departmanınızda kayıtlı şikayet bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
    
    {{-- 3. BÖLÜM: STANDART KARTLAR (Havuz vs. için partial çağırıyoruz) --}}
    <div class="pt-4 border-t border-gray-200">
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Diğer Araçlar</h4>
        {{-- Standart Kullanıcı Partial'ını çağırıyoruz --}}
        @include('dashboard.partials.standart-kullanici')
    </div>

</div>