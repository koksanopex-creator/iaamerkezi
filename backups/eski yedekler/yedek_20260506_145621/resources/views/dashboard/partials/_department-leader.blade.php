<div x-data="{ activeModal: null }" class="mb-8 border-2 border-indigo-100 rounded-2xl p-6 bg-white shadow-lg relative">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span>{{ Auth::user()->bolum->ad ?? 'Bölüm' }} Yönetim Paneli</span>
            </h3>
            <p class="text-sm text-gray-500 mt-1 ml-14">Bölüm performans, proje ve personel durum özeti</p>
        </div>

        <!-- Filtreler -->
        <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2 bg-gray-50 p-2 rounded-xl border border-gray-200">
            <select name="month" class="text-sm border-none bg-transparent focus:ring-0 text-gray-700 font-medium cursor-pointer">
                <option value="">Tüm Aylar</option>
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                @endfor
            </select>
            <span class="text-gray-300">|</span>
            <select name="year" class="text-sm border-none bg-transparent focus:ring-0 text-gray-700 font-medium cursor-pointer">
                <option value="">Tüm Yıllar</option>
                @foreach(range(date('Y'), date('Y')-2) as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white p-2 rounded-lg transition shadow-sm" title="Filtrele">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </button>
            @if(request('year') || request('month'))
                <a href="{{ route('dashboard') }}" class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition" title="Temizle">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- ÜST KARTLAR (Grid) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <!-- İAA Projeleri Total -->
        <div @click="activeTab = 'iaa'; document.getElementById('proje-dagilimi-bolumu').scrollIntoView({ behavior: 'smooth' })" 
             class="bg-white p-5 rounded-xl border border-cyan-100 shadow-sm hover:shadow-md transition group relative overflow-hidden cursor-pointer">
            <div class="absolute right-0 top-0 h-full w-1 bg-cyan-500"></div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bölüm İAA Projeleri</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-extrabold text-gray-800">{{ $stats['total_iaa_count'] ?? 0 }}</span>
                <div class="bg-cyan-50 p-2 rounded-lg text-cyan-600 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
        </div>

        @if($stats['is_responsible_for_sikayet'])
        <!-- Şikayetler Total -->
        <div @click="activeTab = 'sikayet'; document.getElementById('proje-dagilimi-bolumu').scrollIntoView({ behavior: 'smooth' })" 
             class="bg-white p-5 rounded-xl border border-red-100 shadow-sm hover:shadow-md transition group relative overflow-hidden cursor-pointer">
             <div class="absolute right-0 top-0 h-full w-1 bg-red-500"></div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bölüm Müşteri Şikayetleri</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-extrabold text-gray-800">{{ $stats['total_sikayet_count'] ?? 0 }}</span>
                <div class="bg-red-50 p-2 rounded-lg text-red-600 group-hover:scale-110 transition">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>
        @endif

        @if($stats['has_mediation_access'])
        <!-- Arabuluculuk Total -->
        <a href="{{ route('admin.arabuluculuk.index') }}" class="block group">
            <div class="bg-white p-5 rounded-xl border border-blue-100 shadow-sm hover:shadow-md transition relative overflow-hidden">
                <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Arabuluculuk Dosyaları</p>
                <div class="flex items-end justify-between">
                    <div class="flex flex-col">
                        <span class="text-3xl font-extrabold text-gray-800">{{ $stats['total_arabuluculuk_count'] ?? 0 }}</span>
                        @if(isset($stats['count_ihtiyari']) || isset($stats['count_zorunlu']))
                            <div class="text-[10px] text-gray-500 font-medium mt-1">
                                @if(isset($stats['count_ihtiyari'])) <span class="text-blue-600">{{ $stats['count_ihtiyari'] }} İhtiyari</span> @endif
                                @if(isset($stats['count_ihtiyari']) && isset($stats['count_zorunlu'])) | @endif
                                @if(isset($stats['count_zorunlu'])) <span class="text-red-500">{{ $stats['count_zorunlu'] }} Zorunlu</span> @endif
                            </div>
                        @endif
                    </div>
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    </div>
                </div>
            </div>
        </a>
        @endif

        <!-- Disiplin -->
        <div @click="document.getElementById('bolum-disiplin-takibi-bolumu').scrollIntoView({ behavior: 'smooth' })" 
             class="bg-white p-5 rounded-xl border border-orange-100 shadow-sm hover:shadow-md transition group relative overflow-hidden cursor-pointer">
             <div class="absolute right-0 top-0 h-full w-1 bg-orange-500"></div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bölüm Disiplin Olayları</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-extrabold text-gray-800">{{ $stats['bolum_disiplin_count'] ?? 0 }}</span>
                <div class="bg-orange-50 p-2 rounded-lg text-orange-600 group-hover:scale-110 transition">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- İK -->
        <div class="bg-white p-5 rounded-xl border border-green-100 shadow-sm hover:shadow-md transition group relative overflow-hidden">
             <div class="absolute right-0 top-0 h-full w-1 bg-green-500"></div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Aktif Personel</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-extrabold text-gray-800">{{ $stats['bu_ay_aktif_personel_count'] ?? 0 }} <span class="text-lg text-gray-400 font-medium">/ {{ $stats['tum_personel_listesi']->count() }}</span></span>
                <div class="bg-green-50 p-2 rounded-lg text-green-600 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- === SOL ANA KOLON === -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Proje Durum Dağılımı (TABLI YAPI) -->
            <div id="proje-dagilimi-bolumu" x-data="{ activeTab: '{{ $stats['is_responsible_for_sikayet'] ? 'sikayet' : 'iaa' }}' }" class="bg-gray-50 rounded-2xl p-6 border border-gray-100 scroll-mt-24">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Proje Durum Dağılımı
                    </h4>
                    
                    <!-- Tabs -->
                    <div class="flex bg-white rounded-lg p-1 border border-gray-200">
                        @if($stats['is_responsible_for_sikayet'])
                        <button @click="activeTab = 'sikayet'" :class="{ 'bg-red-50 text-red-700 shadow-sm font-bold': activeTab === 'sikayet', 'text-gray-500 hover:text-gray-700': activeTab !== 'sikayet' }" class="px-4 py-1.5 rounded-md text-sm transition">Müşteri Şikayetleri</button>
                        @endif
                        <button @click="activeTab = 'iaa'" :class="{ 'bg-cyan-50 text-cyan-700 shadow-sm font-bold': activeTab === 'iaa', 'text-gray-500 hover:text-gray-700': activeTab !== 'iaa' }" class="px-4 py-1.5 rounded-md text-sm transition">İAA Projeleri</button>
                        @if($stats['has_mediation_access'])
                        <button @click="activeTab = 'arabuluculuk'" :class="{ 'bg-blue-50 text-blue-700 shadow-sm font-bold': activeTab === 'arabuluculuk', 'text-gray-500 hover:text-gray-700': activeTab !== 'arabuluculuk' }" class="px-4 py-1.5 rounded-md text-sm transition relative">
                            Arabuluculuk
                            @if(($stats['dagilim']['arabuluculuk']['devam_eden'] ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-white text-[9px] items-center justify-center">
                                    {{ $stats['dagilim']['arabuluculuk']['devam_eden'] }}
                                  </span>
                                </span>
                            @endif
                        </button>
                        @endif
                    </div>
                </div>
                
                 <!-- Şikayetler Tab (Varsayılan) -->
                @if($stats['is_responsible_for_sikayet'])
                <div x-show="activeTab === 'sikayet'" x-transition:enter class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     @foreach(['tamamlanan' => ['color' => 'green', 'label' => 'Tamamlanan'], 'devam_eden' => ['color' => 'blue', 'label' => 'Devam Eden'], 'onay_bekleyen' => ['color' => 'orange', 'label' => 'Onay Bekleyen']] as $key => $meta)
                        <div @click="activeModal = 'sikayet_{{ $key }}'" class="bg-white p-4 rounded-xl border border-gray-200 cursor-pointer hover:border-{{ $meta['color'] }}-400 hover:shadow-md transition text-center group">
                            <div class="text-3xl font-extrabold text-{{ $meta['color'] }}-600 mb-1 group-hover:scale-110 transition-transform">
                                {{ $stats['dagilim']['sikayet'][$key] ?? 0 }}
                            </div>
                            <div class="text-sm font-semibold text-gray-600">{{ $meta['label'] }}</div>
                            <div class="text-[10px] text-gray-400 mt-2 flex justify-center items-center gap-1 group-hover:text-{{ $meta['color'] }}-600">
                                 İncele <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                     @endforeach
                </div>
                @endif

                <!-- İAA Tab -->
                <div x-show="activeTab === 'iaa'" x-transition:enter class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">
                     @foreach(['tamamlanan' => ['color' => 'green', 'label' => 'Tamamlanan'], 'devam_eden' => ['color' => 'cyan', 'label' => 'Devam Eden'], 'onay_bekleyen' => ['color' => 'orange', 'label' => 'Onay Bekleyen']] as $key => $meta)
                        <div @click="activeModal = 'iaa_{{ $key }}'" class="bg-white p-4 rounded-xl border border-gray-200 cursor-pointer hover:border-{{ $meta['color'] }}-400 hover:shadow-md transition text-center group">
                            <div class="text-3xl font-extrabold text-{{ $meta['color'] }}-600 mb-1 group-hover:scale-110 transition-transform">
                                {{ $stats['dagilim']['iaa'][$key] ?? 0 }}
                            </div>
                            <div class="text-sm font-semibold text-gray-600">{{ $meta['label'] }}</div>
                            <div class="text-[10px] text-gray-400 mt-2 flex justify-center items-center gap-1 group-hover:text-{{ $meta['color'] }}-600">
                                 İncele <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                     @endforeach
                </div>

                <!-- Arabuluculuk Tab -->
                @if($stats['has_mediation_access'])
                <div x-show="activeTab === 'arabuluculuk'" x-transition:enter class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
                     @foreach(['tamamlanan' => ['color' => 'green', 'label' => 'Tamamlanan'], 'devam_eden' => ['color' => 'blue', 'label' => 'Devam Eden']] as $key => $meta)
                        <div @click="activeModal = 'arabuluculuk_{{ $key }}'" class="bg-white p-4 rounded-xl border border-gray-200 cursor-pointer hover:border-{{ $meta['color'] }}-400 hover:shadow-md transition text-center group">
                            <div class="text-3xl font-extrabold text-{{ $meta['color'] }}-600 mb-1 group-hover:scale-110 transition-transform">
                                {{ $stats['dagilim']['arabuluculuk'][$key] ?? 0 }}
                            </div>
                            <div class="text-sm font-semibold text-gray-600">{{ $meta['label'] }}</div>
                            <div class="text-[10px] text-gray-400 mt-2 flex justify-center items-center gap-1 group-hover:text-{{ $meta['color'] }}-600">
                                 İncele <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                     @endforeach
                </div>
                @endif


                <!-- Son Hareketler Loop (Tabs'a göre değişir) -->
                <div class="mt-8">
                    <h5 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">
                        <span x-text="activeTab === 'sikayet' ? 'Son Hareketler (Şikayetler)' : (activeTab === 'iaa' ? 'Son Hareketler (İAA)' : 'Son Hareketler (Arabuluculuk)')"></span>
                    </h5>
                    
                    <!-- Şikayet Listesi -->
                    @if($stats['is_responsible_for_sikayet'])
                    <div x-show="activeTab === 'sikayet'">
                        @if(isset($stats['last_moves_sikayet']) && $stats['last_moves_sikayet']->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($stats['last_moves_sikayet'] as $proje)
                                    <div class="flex items-start justify-between group hover:bg-white p-2 rounded-lg transition hover:shadow-sm">
                                        <div class="flex items-start gap-3 w-full">
                                            <div class="mt-1 w-2 h-2 rounded-full flex-shrink-0 bg-red-400"></div>
                                            <div class="w-full">
                                                <a href="{{ route('admin.sikayetler.show', $proje->id) }}" class="font-semibold text-gray-800 hover:text-red-600 text-sm block">
                                                    {{ $proje->iaaProjesi->baslik ?? ($proje->musteri_sikayet_konusu ?? 'Şikayet #' . $proje->id) }}
                                                </a>
                                                        <div class="flex flex-col gap-1 w-full">
                                                            <div class="flex items-center justify-between gap-2">
                                                                @if($proje->iaaProjesi)
                                                                    {!! $proje->iaaProjesi->durum_etiketi !!}
                                                                @else
                                                                    @php
                                                                        $colorMap = [
                                                                            'Yeni' => 'blue',
                                                                            'İşlemde' => 'yellow',
                                                                            'Atandı' => 'indigo',
                                                                            'İptal Edildi' => 'gray',
                                                                            'Kapatıldı' => 'green',
                                                                        ];
                                                                        $statusColor = $colorMap[$proje->musteri_durum] ?? 'gray';
                                                                    @endphp
                                                                    <span class="text-[10px] bg-{{$statusColor}}-50 text-{{$statusColor}}-600 px-1.5 py-0.5 rounded border border-{{$statusColor}}-100">{{ $proje->musteri_durum }}</span>
                                                                @endif
                                                                <span class="text-[10px] text-gray-400">{{ $proje->updated_at->diffForHumans() }}</span>
                                                            </div>

                                                            @if($proje->iaaProjesi)
                                                                @php $progress = $proje->iaaProjesi->ilerleme_verisi; @endphp
                                                                @if($progress['toplam'] > 0)
                                                                    <div class="mt-0.5 w-full">
                                                                        <div class="flex items-center justify-between mb-0.5">
                                                                            <span class="text-[9px] text-gray-500 font-bold">
                                                                                @if($proje->iaaProjesi->aktif_adim)
                                                                                    {{ $proje->iaaProjesi->aktif_adim->name }} 
                                                                                @endif
                                                                                <span class="text-gray-400 font-normal">({{ $progress['tamamlanan'] }}/{{ $progress['toplam'] }})</span>
                                                                            </span>
                                                                            <span class="text-[9px] font-bold text-blue-600">{{ $progress['yuzde'] }}%</span>
                                                                        </div>
                                                                        <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden border border-gray-100">
                                                                            <div class="h-full bg-{{ $progress['yuzde'] == 100 ? 'green' : 'blue' }}-500 transition-all duration-500" style="width: {{ $progress['yuzde'] }}%"></div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                             @if($stats['total_sikayet_count'] > 5)
                                <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                                    <a href="{{ route('admin.sikayetler.index') }}" class="text-sm font-bold text-red-600 hover:text-red-800 transition flex items-center justify-center gap-1">
                                        <span>Tümünü Gör (Toplam {{ $stats['total_sikayet_count'] }} Kayıt)</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </a>
                                </div>
                            @endif
                        @else
                             <div class="text-sm text-gray-400 italic py-4">Görüntülenecek şikayet yok.</div>
                        @endif
                    </div>
                    @endif

                    <!-- İAA Listesi -->
                    <div x-show="activeTab === 'iaa'" style="display: none;">
                        @if(isset($stats['last_moves_iaa']) && $stats['last_moves_iaa']->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($stats['last_moves_iaa'] as $proje)
                                    <div class="flex items-start justify-between group hover:bg-white p-2 rounded-lg transition hover:shadow-sm">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 w-2 h-2 rounded-full flex-shrink-0 bg-cyan-400"></div>
                                            <div>
                                                <a href="{{ route('proje.workspace.show', $proje->id) }}" class="font-semibold text-gray-800 hover:text-cyan-600 text-sm block">
                                                    {{ $proje->baslik }}
                                                </a>
                                                <div class="flex flex-col gap-1 w-full max-w-[200px]">
                                                    <div class="flex items-center justify-between gap-2">
                                                        {!! $proje->durum_etiketi !!}
                                                        <span class="text-[10px] text-gray-400">{{ $proje->updated_at->diffForHumans() }}</span>
                                                    </div>
                                                    
                                                    @php $progress = $proje->ilerleme_verisi; @endphp
                                                    @if($progress['toplam'] > 0)
                                                        <div class="mt-0.5 w-full">
                                                            <div class="flex items-center justify-between mb-0.5">
                                                                <span class="text-[9px] text-gray-500 font-bold">
                                                                    @if($proje->aktif_adim)
                                                                        {{ $proje->aktif_adim->name }} 
                                                                    @endif
                                                                    <span class="text-gray-400 font-normal">({{ $progress['tamamlanan'] }}/{{ $progress['toplam'] }})</span>
                                                                </span>
                                                                <span class="text-[9px] font-bold text-cyan-600">{{ $progress['yuzde'] }}%</span>
                                                            </div>
                                                            <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden border border-gray-100">
                                                                <div class="h-full bg-{{ $progress['yuzde'] == 100 ? 'green' : 'cyan' }}-500 transition-all duration-500" style="width: {{ $progress['yuzde'] }}%"></div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex -space-x-2">
                                             @foreach($proje->projeEkibi->take(3) as $uye)
                                                <div class="w-6 h-6 rounded-full ring-2 ring-white overflow-hidden" title="{{ $uye->name }}">
                                                    @if($uye->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $uye->profile_photo_path) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-[9px] font-bold">
                                                            {{ substr($uye->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                             @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(($stats['total_iaa_count'] ?? 0) > 5)
                                <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                                    <a href="{{ route('iaa.index') }}" class="text-sm font-bold text-cyan-600 hover:text-cyan-800 transition flex items-center justify-center gap-1">
                                        <span>Tümünü Gör (Toplam {{ $stats['total_iaa_count'] }} Kayıt)</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </a>
                                </div>
                            @endif
                        @else
                             <div class="text-sm text-gray-400 italic py-4">Görüntülenecek İAA projesi yok.</div>
                        @endif
                    </div>

                    <!-- Arabuluculuk Listesi -->
                    @if($stats['has_mediation_access'])
                    <div x-show="activeTab === 'arabuluculuk'" style="display: none;">
                        @if(isset($stats['last_moves_arabuluculuk']) && $stats['last_moves_arabuluculuk']->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($stats['last_moves_arabuluculuk'] as $case)
                                    <div class="flex items-start justify-between group hover:bg-white p-2 rounded-lg transition hover:shadow-sm">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 w-2 h-2 rounded-full flex-shrink-0 bg-blue-400"></div>
                                            <div>
                                                <a href="{{ route('admin.arabuluculuk.show', $case->id) }}" class="font-semibold text-gray-800 hover:text-blue-600 text-sm block">
                                                    @if($case->dosya_no)
                                                        {{ $case->dosya_no }}
                                                    @else
                                                        {{ $case->calisan->name ?? 'İsimsiz' }} (Taslak)
                                                    @endif
                                                </a>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @php
                                                        $statusColors = [
                                                            'taslak' => 'gray', 'TASLAK' => 'gray',
                                                            'islemde' => 'blue', 'ISLEMDE' => 'blue',
                                                            'kapatildi' => 'green', 'KAPATILDI' => 'green',
                                                            'hukuk_incelemesinde' => 'purple',
                                                            'arabulucuda' => 'indigo',
                                                            'imza_asamasinda' => 'orange',
                                                            'odeme_bekliyor' => 'pink',
                                                            'yonetim_onayinda' => 'yellow'
                                                        ];
                                                        $color = $statusColors[$case->status] ?? 'blue';
                                                    @endphp
                                                    <span class="text-[10px] bg-{{ $color }}-50 text-{{ $color }}-600 px-1.5 py-0.5 rounded border border-{{ $color }}-100 uppercase">{{ $case->status }}</span>
                                                     <span class="text-[10px] text-gray-400">{{ $case->updated_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(($stats['total_arabuluculuk_count'] ?? 0) > 5)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('admin.arabuluculuk.index') }}" class="text-xs text-blue-600 font-bold hover:underline">Tümünü Gör ({{ $stats['total_arabuluculuk_count'] }})</a>
                                </div>
                            @endif
                        @else
                            <div class="text-sm text-gray-400 italic py-4">Görüntülenecek arabuluculuk dosyası yok.</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Disiplin Cezaları -->
                <div id="bolum-disiplin-takibi-bolumu" class="bg-white rounded-2xl shadow-sm border border-red-100 p-6 relative overflow-hidden scroll-mt-24">
                    <div class="absolute top-0 right-0 p-4 opacity-5">
                         <svg class="w-24 h-24 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                 <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Bölüm Disiplin Takibi
                            </h4>
                            <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">{{ $stats['bolum_disiplin_count'] ?? 0 }} Olay</span>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($stats['bolum_disiplin_cezalari'] ?? [] as $ceza)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition border border-transparent hover:border-red-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white shadow-sm flex-shrink-0">
                                            @if($ceza->user->profile_photo_path)
                                                <img src="{{ asset('storage/' . $ceza->user->profile_photo_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-red-100 flex items-center justify-center text-red-700 text-[10px] font-bold">
                                                    {{ substr($ceza->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-800">{{ $ceza->user->name }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $ceza->behavior->name ?? 'Disiplin İhlali' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] font-bold text-red-600">
                                            @if($ceza->karar_tarihi)
                                                {{ $ceza->karar_tarihi->format('d.m.Y') }}
                                            @else
                                                <span class="text-blue-500">İşlemde</span>
                                            @endif
                                        </div>
                                        <div class="text-[9px] text-gray-400 capitalize">{{ $ceza->durum }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-gray-400 italic text-xs">Aktif disiplin dosyası bulunamadı.</div>
                            @endforelse
                        </div>

                        @if(($stats['bolum_disiplin_count'] ?? 0) > 5)
                            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                                <a href="{{ route('disiplin.index') }}" class="text-sm font-bold text-red-600 hover:text-red-800 transition flex items-center justify-center gap-1">
                                    <span>Tümünü Gör (Toplam {{ $stats['bolum_disiplin_count'] }} Kayıt)</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Havuzdaki Öneriler (YENİ) -->
                <div class="bg-white rounded-2xl shadow-sm border border-purple-100 p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5">
                         <svg class="w-24 h-24 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path></svg>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2 relative z-10">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Havuzdaki Öneriler
                        <span class="ml-auto bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded-full">{{ $stats['havuzdaki_oneriler_count'] ?? 0 }}</span>
                    </h4>
                    
                     @if(isset($stats['havuzdaki_oneriler']) && $stats['havuzdaki_oneriler']->isNotEmpty())
                        <div class="space-y-4 relative z-10">
                            @foreach($stats['havuzdaki_oneriler'] as $oneri)
                                <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                                    <a href="{{ route('proje.workspace.show', $oneri->id) }}" class="font-semibold text-purple-900 text-sm hover:underline block truncate">
                                        {{ $oneri->baslik }}
                                    </a>
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex items-center gap-1.5">
                                            @if($oneri->gonderen && $oneri->gonderen->profile_photo_path)
                                                <img src="{{ asset('storage/'.$oneri->gonderen->profile_photo_path) }}" class="w-4 h-4 rounded-full">
                                            @endif
                                            <span class="text-[10px] text-purple-700">{{ $oneri->gonderen->name ?? 'Anonim' }}</span>
                                        </div>
                                        <span class="text-[9px] bg-white px-1.5 py-0.5 rounded text-gray-500 border border-purple-100">{{ $oneri->durum }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                     @else
                        <div class="text-center py-8 text-gray-400 text-sm relative z-10">Havuzda öneri yok.</div>
                     @endif
                </div>

                <!-- Tuttuğum Tutanaklar (YENİ) -->
                 <div class="col-span-1 md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Bölüm Personel Tutanakları
                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full ml-auto">{{ $stats['tuttugum_tutanaklar_count'] ?? 0 }}</span>
                    </h4>
                    
                    @if(isset($stats['tuttugum_tutanaklar']) && $stats['tuttugum_tutanaklar']->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2">Personel</th>
                                        <th class="px-4 py-2">İhlal</th>
                                        <th class="px-4 py-2">Tarih</th>
                                        <th class="px-4 py-2">Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['tuttugum_tutanaklar'] as $tutanak)
                                        @php
                                            $durumClass = match ($tutanak->durum) {
                                                'Taslak' => 'bg-gray-50 text-gray-700 border-gray-200',
                                                'Savunma Bekleniyor' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'Kurul İncelemesinde', 'Kurulda' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'Yönetici Değerlendirmesi' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'Karar Verildi' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'İptal Edildi' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                default => 'bg-gray-100 text-gray-600 border-gray-200'
                                            };
                                        @endphp
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900">
                                                <a href="{{ route('profile.show', $tutanak->user->id) }}" class="hover:underline flex items-center gap-2">
                                                    @if($tutanak->user->profile_photo_path)
                                                        <img src="{{ asset('storage/'.$tutanak->user->profile_photo_path) }}" class="w-6 h-6 rounded-full object-cover">
                                                    @endif
                                                    {{ $tutanak->user->name }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('admin.disiplin.show', $tutanak->id) }}" class="text-blue-600 hover:underline">
                                                    {{ Str::limit($tutanak->behavior->tanim ?? 'Kural İhlali', 30) }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">{{ $tutanak->created_at->format('d.m.Y') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-lg border {{ $durumClass }}">
                                                    {{ $tutanak->durum }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-400 italic">Bölüm personeline ait tutanak bulunamadı.</div>
                    @endif
                </div>

            </div>
        </div>

        <!-- === SAĞ KOLON (PERSONEL & RANKING) === -->
        <div class="space-y-6">
            
            <!-- Personel Listesi (SEKMELİ) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" x-data="{ personelTab: 'diger' }">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-gray-800">Personel Listesi</h4>
                        <a href="{{ route('admin.mavi-yaka.create') }}"
                            class="text-[10px] font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded-lg transition flex items-center gap-1"
                            x-show="personelTab === 'mavi-yaka'">
                            + Yeni Ekle
                        </a>
                    </div>
                    {{-- Sekmeler --}}
                    <div class="flex bg-white rounded-lg p-0.5 border border-gray-200 gap-1">
                        <button @click="personelTab = 'diger'"
                            :class="personelTab === 'diger' ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-3 py-1.5 rounded-md text-xs transition flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Beyaz Yaka Personellerim
                        </button>
                        <button @click="personelTab = 'mavi-yaka'"
                            :class="personelTab === 'mavi-yaka' ? 'bg-blue-50 text-blue-700 font-bold shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-3 py-1.5 rounded-md text-xs transition flex items-center justify-center gap-1">
                            <span class="inline-flex items-center justify-center w-4 h-4 bg-blue-500 rounded-full text-white text-[8px] font-black">MY</span>
                            Mavi Yaka
                        </button>
                    </div>
                </div>

                {{-- DİĞER PERSONELLER --}}
                <div x-show="personelTab === 'diger'" class="overflow-y-auto max-h-[550px]">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-xs text-gray-400 uppercase font-medium sticky top-0">
                            <tr>
                                <th class="px-5 py-3 text-left">Personel</th>
                                <th class="px-5 py-3 text-right">Puan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @foreach($stats['tum_personel_listesi']->where('is_mavi_yaka', false) as $personel)
                                @php $isMe = ($personel->id === Auth::id()); @endphp
                                <tr class="group hover:bg-gray-50 transition {{ $isMe ? 'bg-indigo-50/50' : '' }}">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="relative flex-shrink-0">
                                                <a href="{{ route('profile.show', $personel->id) }}">
                                                    @if($personel->profile_photo_path)
                                                        <img class="w-10 h-10 rounded-full object-cover ring-2 {{ $isMe ? 'ring-indigo-400' : 'ring-white shadow-sm' }} group-hover:ring-indigo-100 transition" src="{{ asset('storage/' . $personel->profile_photo_path) }}" alt="{{ $personel->name }}">
                                                    @else
                                                         <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold shadow-sm">
                                                            {{ substr($personel->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </a>
                                                 <span class="absolute bottom-0 right-0 w-3 h-3 border-2 border-white rounded-full {{ $personel->isOnline() ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    <a href="{{ route('profile.show', $personel->id) }}" class="font-bold text-gray-800 hover:text-indigo-600 block transition">{{ $personel->name }}</a>
                                                    @if($isMe)
                                                        <span title="Bölüm Lideri" class="text-indigo-600">
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    @if($isMe)
                                                        <span class="text-indigo-600 font-bold uppercase tracking-wider text-[9px]">Bölüm Lideri</span>
                                                    @else
                                                        {{ $personel->roles->first()->name ?? $personel->unvan ?? 'Personel' }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('profile.puanlar', $personel->id) }}" class="inline-block hover:bg-indigo-50 px-2 py-1 rounded transition group/point">
                                            <div class="font-bold text-indigo-700 group-hover/point:text-indigo-900">{{ $personel->cached_total_score ?? 0 }} P</div>
                                            @if($personel->gorevli_oldugu_projeler_count > 0)
                                                <div class="text-[10px] text-blue-500">{{ $personel->gorevli_oldugu_projeler_count }} Proje</div>
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                                @if($isMe)
                                    <tr class="h-px">
                                        <td colspan="2" class="p-0">
                                            <div class="h-[2px] w-full bg-gradient-to-r from-indigo-100 via-indigo-500 to-indigo-100"></div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MAVİ YAKA --}}
                <div x-show="personelTab === 'mavi-yaka'" class="overflow-y-auto max-h-[550px]" style="display:none;">
                    @php
                        $maviYakalar = \App\Models\User::where('is_mavi_yaka', true)
                            ->where('bolum_id', Auth::user()->bolum_id)
                            ->orderBy('name')
                            ->get();
                    @endphp

                    @if($maviYakalar->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                            <span class="text-4xl mb-2">👷</span>
                            <p class="text-sm">Henüz mavi yaka personeli yok.</p>
                            <a href="{{ route('admin.mavi-yaka.create') }}"
                                class="mt-3 text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition">
                                İlk Mavi Yaka'yı Ekle
                            </a>
                        </div>
                    @else
                        <table class="w-full">
                            <thead class="bg-blue-50 text-xs text-blue-500 uppercase font-medium sticky top-0">
                                <tr>
                                    <th class="px-5 py-3 text-left">Personel</th>
                                    <th class="px-5 py-3 text-right">Puan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @foreach($maviYakalar as $my)
                                    <tr class="group hover:bg-blue-50/30 transition">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm flex-shrink-0">
                                                    {{ strtoupper(substr($my->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-800">{{ $my->name }}</div>
                                                    <div class="text-xs text-blue-500">{{ $my->unvan ?? 'Mavi Yaka' }}</div>
                                                    @if($my->sicil_no)
                                                        <div class="text-[10px] text-gray-400">Sicil: {{ $my->sicil_no }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <a href="{{ route('profile.puanlar', $my->id) }}" class="inline-block hover:bg-blue-50 px-2 py-1 rounded transition group/point">
                                                <div class="font-bold text-blue-700 group-hover/point:text-blue-900">{{ $my->cached_total_score ?? 0 }} P</div>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="p-3 border-t border-gray-100 text-center">
                            <a href="{{ route('admin.mavi-yaka.index') }}"
                                class="text-xs text-blue-600 font-bold hover:underline">
                                Tüm Mavi Yaka Listesi →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- En Çok Projeye Katılanlar -->
             <div class="bg-gradient-to-b from-indigo-700 to-purple-800 rounded-2xl shadow-lg text-white p-6 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                
                <h4 class="font-bold text-white mb-6 text-sm flex items-center gap-2 relative z-10">
                    <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    En Çok Projeye Katılanlar
                </h4>
                @if(isset($stats['en_cok_projeye_katilanlar']) && $stats['en_cok_projeye_katilanlar']->isNotEmpty())
                    <ul class="space-y-3 relative z-10">
                        @foreach($stats['en_cok_projeye_katilanlar'] as $index => $personel)
                            <li class="flex items-center justify-between bg-white/10 p-3 rounded-xl border border-white/5 hover:bg-white/20 transition">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-white/50 w-4">{{ $index + 1 }}.</span>
                                    <div class="w-8 h-8 rounded-full bg-white/20 overflow-hidden ring-1 ring-white/30">
                                        @if($personel->profile_photo_path)
                                            <img src="{{ asset('storage/' . $personel->profile_photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center w-full h-full text-xs font-bold">{{ substr($personel->name, 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold text-white">{{ $personel->name }}</span>
                                </div>
                                <span class="text-sm font-bold text-yellow-300">{{ $personel->proje_sayisi }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-white/50 italic">Veri yok.</p>
                @endif
            </div>

        </div>
    </div>
    
    <!-- MODALS -->
    <!-- Backdrop -->
    <div x-show="activeModal" x-transition.opacity class="fixed inset-0 bg-black/50 z-40" @click="activeModal = null" style="display: none;"></div>
    
    <!-- Modal Template Logic -->
    @php
        $modalTypes = [
            'sikayet' => ['title' => 'Müşteri Şikayetleri', 'color' => 'red'],
            'iaa' => ['title' => 'İAA Projeleri', 'color' => 'cyan'],
            'arabuluculuk' => ['title' => 'Arabuluculuk Dosyaları', 'color' => 'blue']
        ];
        $modalStatuses = [
            'tamamlanan' => 'Tamamlanan', 
            'devam_eden' => 'Devam Eden', 
            'onay_bekleyen' => 'Onay Bekleyen'
        ];
    @endphp

    @foreach($modalTypes as $typeKey => $typeMeta)
        @foreach($modalStatuses as $statusKey => $statusLabel)
            @php 
                $modalId = $typeKey . '_' . $statusKey; 
                $listItems = $stats['list'][$typeKey][$statusKey] ?? collect();
            @endphp
            
            <div x-show="activeModal === '{{ $modalId }}'" x-transition.scale 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" style="display: none;">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden pointer-events-auto flex flex-col border-t-4 border-{{ $typeMeta['color'] }}-500">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-lg text-gray-800">
                            <span class="text-{{ $typeMeta['color'] }}-600">{{ $typeMeta['title'] }}</span> - {{ $statusLabel }}
                        </h3>
                        <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="p-4 overflow-y-auto">
                        @if($listItems->isNotEmpty())
                            <div class="space-y-3">
                                 @foreach($listItems as $item)
                                     @if($typeKey == 'arabuluculuk')
                                        {{-- ARABULUCULUK KART TASARIMI --}}
                                        <div class="flex items-start gap-4 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition group">
                                            <div class="bg-blue-50 text-blue-600 font-bold p-2 text-xs rounded text-center min-w-[50px]">
                                                @if($item->basvuru_turu == 'ihtiyari') İHT @else ZOR @endif
                                            </div>
                                            <div class="flex-1">
                                                <a href="{{ route('admin.arabuluculuk.show', $item->id) }}" class="font-bold text-gray-800 hover:text-blue-600 block">
                                                    {{ $item->dosya_no ?? 'Taslak Dosya' }}
                                                </a>
                                                <div class="text-xs text-gray-500 mt-1 flex gap-2">
                                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded uppercase">{{ $item->status }}</span>
                                                    <span>•</span>
                                                    <span>{{ $item->updated_at->format('d.m.Y') }}</span>
                                                </div>
                                            </div>
                                            <div class="text-xs font-bold text-blue-600">
                                                Detay
                                            </div>
                                        </div>
                                     @elseif($typeKey == 'sikayet')
                                        {{-- ŞİKAYET KART TASARIMI --}}
                                        <div class="flex items-start gap-4 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition group">
                                            <div class="bg-red-50 text-red-600 font-bold p-2 text-xs rounded text-center min-w-[60px]">
                                                {{ $item->gerceklesecek_puan ?? '100.00' }} P
                                            </div>
                                            <div class="flex-1">
                                                <a href="{{ route('admin.sikayetler.show', $item->id) }}" class="font-bold text-gray-800 hover:text-red-600 block">
                                                    {{ $item->iaaProjesi->baslik ?? ($item->musteri_sikayet_konusu ?? ($item->firma_adi ?? 'Şikayet #' . $item->id)) }}
                                                </a>
                                                <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-2 items-center">
                                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded">{{ $item->cozumTakimi->ad ?? 'Takımsız' }}</span>
                                                    <span>•</span>
                                                    <span>{{ $item->created_at->format('d.m.Y') }}</span>
                                                    @if($item->musteri_adi || $item->firma_adi)
                                                        <span>•</span>
                                                        <span class="text-red-500">{{ $item->musteri_adi ?? $item->firma_adi }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-xs font-bold text-blue-600">
                                                @if($item->musteri_durum == 'İşlemde' && $item->iaaProjesi)
                                                    {!! $item->iaaProjesi->durum_etiketi !!}
                                                @else
                                                    {{ $item->musteri_durum }}
                                                @endif
                                            </div>
                                        </div>
                                     @else
                                        {{-- IAA KART TASARIMI --}}
                                        <div class="flex items-start gap-4 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition group">
                                            <div class="bg-{{ $typeMeta['color'] }}-50 text-{{ $typeMeta['color'] }}-600 font-bold p-2 text-xs rounded text-center min-w-[50px]">
                                                {{ $item->puan ?? '-' }} P
                                            </div>
                                            <div class="flex-1">
                                                <a href="{{ route('proje.workspace.show', $item->id) }}" class="font-bold text-gray-800 hover:text-{{ $typeMeta['color'] }}-600 block">
                                                    {{ $item->baslik }}
                                                </a>
                                                <div class="text-xs text-gray-500 mt-1 flex gap-2">
                                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded">{{ $item->atananTakim->ad ?? 'Takımsız' }}</span>
                                                    <span>•</span>
                                                    <span>{{ $item->updated_at->format('d.m.Y') }}</span>
                                                    @if($typeKey == 'sikayet' && $item->musteriSikayeti)
                                                        <span>•</span>
                                                        <span class="text-red-500">{{ $item->musteriSikayeti->musteri_adi ?? 'Müşteri' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-xs font-bold 
                                                @if($item->durum == 'Tamamlandı') text-green-600 
                                                @elseif(Str::contains($item->durum, 'Bekliyor')) text-orange-500 
                                                @else text-blue-600 @endif">
                                                {{ $item->durum }}
                                            </div>
                                        </div>
                                     @endif
                                 @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 text-gray-400">Bu kategoride proje bulunmuyor.</div>
                        @endif
                    </div>
                     <div class="p-3 border-t border-gray-100 bg-gray-50 text-center">
                        <button @click="activeModal = null" class="text-sm text-gray-500 hover:text-gray-800 font-medium">Kapat</button>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach


    {{-- İADE TABLOSU (EN ALT) - Sadece Şikayet Sorumluluğu Varsa --}}
    @if(isset($iadeVerileri) && $stats['is_responsible_for_sikayet'])
        @include('dashboard.partials.iadeler-tablosu')
    @endif

    @include('dashboard.partials._users-activity')

    <div class="mt-12 pt-8 border-t border-gray-100">
        <h4 class="font-bold text-xl text-gray-800 flex items-center gap-2 mb-6">
             <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
             Kişisel Durum Özeti
        </h4>
        @include('dashboard.partials.standart-kullanici')
    </div>

</div>