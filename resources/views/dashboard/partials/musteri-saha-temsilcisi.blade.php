@if(empty($stats['has_bolum']))
    <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-xl shadow-sm text-center">
        <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <h3 class="text-lg font-bold text-red-800 mb-2">Henüz Bir Bölüme Atanmamışsınız</h3>
        <p class="text-red-600">Müşteri Saha Temsilcisi rolüne sahipsiniz ancak henüz sorumlu olduğunuz bir saha/bölüm belirlenmemiş. Yönetici ile iletişime geçiniz.</p>
    </div>
@else
    <div class="space-y-6">
        <!-- Dashboard Header -->
        <div class="bg-gradient-to-r from-teal-600 to-emerald-700 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10 -mr-16 -mt-16">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div class="relative z-10 flex flex-col xl:flex-row justify-between items-start gap-8">
                <div>
                    <h3 class="text-2xl font-bold">Müşteri Saha Temsilcisi Paneli</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-white/90">Sorumlu Saha Bölgeleri:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($stats['sorumlu_bolum_isimleri'] as $bolumAdi)
                                <span class="px-3 py-1 bg-white text-teal-700 rounded-lg text-[10px] font-black border border-white shadow-sm ring-4 ring-white/10">{{ $bolumAdi }}</span>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-teal-50 mt-2 text-sm leading-relaxed">Sorumlu olduğunuz bölgelerdeki müşteri ziyaretlerini, şikayet süreçlerini ve performansınızı yönetin.</p>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 w-full lg:w-auto">
                    <a href="{{ url('/ziyaretler') }}" class="text-center px-4 py-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-lg hover:bg-white/20 transition-all cursor-pointer group/card flex flex-col justify-center min-h-[120px]">
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-80 mb-1 text-white group-hover/card:text-white transition-colors">Tüm Ziyaretler</p>
                        <p class="text-4xl font-black text-white leading-none">{{ $stats['ziyaret_count'] ?? 0 }}</p>
                    </a>
                    
                    <a href="{{ url('/ziyaretler') }}" class="text-center px-4 py-4 bg-amber-500/20 backdrop-blur-md rounded-2xl border border-amber-400/30 shadow-lg hover:bg-amber-500/30 transition-all cursor-pointer group/card flex flex-col justify-center min-h-[120px]">
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-80 mb-1 text-amber-50 group-hover/card:text-white transition-colors">Gecikmiş</p>
                        <p class="text-4xl font-black text-white leading-none">{{ $stats['gecikmis_ziyaret_count'] ?? 0 }}</p>
                    </a>

                    <a href="{{ url('/ziyaretler') }}" class="text-center px-4 py-4 bg-blue-500/20 backdrop-blur-md rounded-2xl border border-blue-400/30 shadow-lg hover:bg-blue-500/30 transition-all cursor-pointer group/card flex flex-col justify-center min-h-[120px]">
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-80 mb-1 text-blue-50 group-hover/card:text-white transition-colors">Yaklaşan Ziyaretler</p>
                        <p class="text-4xl font-black text-white leading-none">{{ $stats['yaklasan_ziyaret_count'] ?? 0 }}</p>
                    </a>

                    <a href="{{ route('admin.sikayetler.index') }}" class="text-center px-4 py-4 bg-emerald-500/20 backdrop-blur-md rounded-2xl border border-emerald-400/30 shadow-lg hover:bg-emerald-500/30 transition-all cursor-pointer group/card flex flex-col justify-center min-h-[120px]">
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-80 mb-1 text-emerald-50 group-hover/card:text-white transition-colors">Aktif Şikayetler</p>
                        <p class="text-4xl font-black text-white leading-none">{{ $stats['aktif_sikayet_sayisi'] ?? 0 }}</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Ziyaret Takvimi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-6 mb-6">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Ziyaret Takvimi
                </h3>
            </div>
            <div class="p-6">
                <div id="ziyaret-takvimi" style="min-height: 500px;"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Yaklaşan Ziyaretler -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Yaklaşan ve Gecikmiş Ziyaretler
                    </h3>
                    <a href="{{ url('/ziyaretler') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Tümü &rarr;</a>
                </div>
                <div class="p-0">
                    @php 
                        $yaklasanVeGecikmis = collect($stats['yaklasan_ziyaretler'])->merge($stats['gecikmis_ziyaretler'])->sortBy('visit_date')->take(5);
                    @endphp
                    @if(count($yaklasanVeGecikmis) > 0)
                        <ul class="divide-y divide-slate-100">
                            @foreach($yaklasanVeGecikmis as $ziyaret)
                                @php
                                    $gecikmis = \Carbon\Carbon::parse($ziyaret->visit_date)->startOfDay()->lt(now()->startOfDay());
                                @endphp
                                <li class="p-4 hover:bg-slate-50 transition-colors group">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-4">
                                            <!-- Müşteri Logo -->
                                            @php
                                                $customer = null;
                                                $bolumAd = 'Bilinmeyen Bölüm';
                                                if($ziyaret->iaa && $ziyaret->iaa->musteriSikayeti) {
                                                    $customer = $ziyaret->iaa->musteriSikayeti->customer;
                                                    if ($ziyaret->iaa->musteriSikayeti->sikayetKategori && $ziyaret->iaa->musteriSikayeti->sikayetKategori->bolum) {
                                                        $bolumAd = $ziyaret->iaa->musteriSikayeti->sikayetKategori->bolum->name;
                                                    }
                                                }
                                                
                                                $daysDiff = \Carbon\Carbon::parse($ziyaret->visit_date)->startOfDay()->diffInDays(now()->startOfDay());
                                                $daysText = $gecikmis ? $daysDiff . ' gün gecikti' : ($daysDiff == 0 ? 'Bugün' : $daysDiff . ' gün kaldı');
                                            @endphp
                                            
                                            <div class="shrink-0 flex flex-col items-center">
                                                @if($customer && $customer->logo_path)
                                                    <img src="{{ asset('storage/' . $customer->logo_path) }}" alt="{{ $customer->name }}" class="w-12 h-12 rounded-xl object-contain bg-white border border-slate-200 shadow-sm p-1">
                                                @else
                                                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-bold border border-slate-200 shadow-sm">
                                                        {{ $customer ? mb_substr($customer->name, 0, 2) : 'GZ' }}
                                                    </div>
                                                @endif
                                                <span class="mt-1 text-[10px] font-bold {{ $gecikmis ? 'text-rose-600' : 'text-indigo-600' }}">{{ \Carbon\Carbon::parse($ziyaret->visit_date)->translatedFormat('d M Y') }}</span>
                                            </div>
                                            
                                            <div>
                                                <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors truncate">
                                                    {{ $customer ? $customer->name : 'Genel Ziyaret' }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                                                    <span class="font-medium text-slate-700">{{ $bolumAd }}</span>
                                                    <span>&bull;</span>
                                                    <span class="{{ $gecikmis ? 'text-rose-600 font-bold animate-pulse' : 'text-indigo-600 font-medium' }}">{{ $daysText }}</span>
                                                </div>
                                                <p class="text-xs text-slate-500 mt-1 line-clamp-1 max-w-sm" title="{{ $ziyaret->visit_reason ?? 'Belirtilmedi' }}">
                                                    {{ $ziyaret->visit_reason ?? 'Amaç Belirtilmedi' }}
                                                </p>
                                                @php
                                                    $ziyaretcilerData = [];
                                                    
                                                    if ($ziyaret->visitor) {
                                                        $ziyaretcilerData[$ziyaret->visitor->id] = [
                                                            'name' => $ziyaret->visitor->name,
                                                            'image' => $ziyaret->visitor->profile_photo_path,
                                                            'is_self' => $ziyaret->visitor_id == Auth::id()
                                                        ];
                                                    }
                                                    
                                                    if (is_array($ziyaret->visitors) && count($ziyaret->visitors) > 0) {
                                                        $ekZiyaretciler = \App\Models\User::whereIn('id', $ziyaret->visitors)->get();
                                                        foreach($ekZiyaretciler as $ekVisitor) {
                                                            $ziyaretcilerData[$ekVisitor->id] = [
                                                                'name' => $ekVisitor->name,
                                                                'image' => $ekVisitor->profile_photo_path,
                                                                'is_self' => $ekVisitor->id == Auth::id()
                                                            ];
                                                        }
                                                    }
                                                    
                                                    if (empty($ziyaretcilerData) && !empty($ziyaret->visitor_name)) {
                                                        $ziyaretcilerData['text_visitor'] = [
                                                            'name' => $ziyaret->visitor_name,
                                                            'image' => null,
                                                            'is_self' => false
                                                        ];
                                                    }
                                                @endphp
                                                @if(count($ziyaretcilerData) > 0)
                                                    <div class="mt-2 flex flex-col gap-1.5">
                                                        @foreach($ziyaretcilerData as $z)
                                                            <div class="flex items-center gap-2 {{ $z['is_self'] ? 'bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-lg w-fit' : '' }}">
                                                                @if($z['image'])
                                                                    <img src="{{ asset('storage/' . $z['image']) }}" alt="{{ $z['name'] }}" class="w-5 h-5 rounded-full object-cover border border-slate-200 shadow-sm">
                                                                @else
                                                                    <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200 shadow-sm">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                                    </div>
                                                                @endif
                                                                <span class="text-xs {{ $z['is_self'] ? 'font-bold text-emerald-700' : 'text-slate-600' }}">
                                                                    {{ $z['name'] }}
                                                                    @if($z['is_self'])
                                                                        <span class="ml-1 text-[9px] uppercase tracking-wider font-black text-emerald-700">(SİZ)</span>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-xs mt-2 font-bold text-rose-500 bg-rose-50 px-2 py-1 rounded-lg inline-flex items-center w-fit border border-rose-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                        Belirtilmedi
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0 flex flex-col items-end gap-2">
                                            @php
                                                $displayStatus = $ziyaret->status;
                                                if (in_array($ziyaret->status, ['Beklemede', 'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetim Onayı Bekliyor']) && !empty($ziyaret->planner_revision_note)) {
                                                    $displayStatus = 'Revizyon Sonrası Onay Bekliyor';
                                                }
                                                
                                                $statusColors = [
                                                    'Onaylandı' => 'bg-emerald-100 text-emerald-800',
                                                    'Beklemede' => 'bg-amber-100 text-amber-800',
                                                    'Yönetim Onayı Bekliyor' => 'bg-purple-100 text-purple-800',
                                                    'Direktör Onayı Bekliyor' => 'bg-indigo-100 text-indigo-800',
                                                    'Reddedildi' => 'bg-rose-100 text-rose-800',
                                                    'Tamamlandı' => 'bg-green-100 text-green-800',
                                                    'İptal Edildi' => 'bg-gray-100 text-gray-800',
                                                    'Revize İsteniyor' => 'bg-orange-100 text-orange-800',
                                                    'Revizyon Sonrası Onay Bekliyor' => 'bg-blue-100 text-blue-800'
                                                ];
                                                $statusClass = $statusColors[$displayStatus] ?? 'bg-slate-100 text-slate-800';
                                            @endphp
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $statusClass }}">{{ $displayStatus }}</span>
                                            <a href="{{ $ziyaret->iaa_id ? route('proje.workspace.show', $ziyaret->iaa_id) . '#ziyaret-bilgileri-alani' : url('/ziyaretler') }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                                Görüntüle
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-8 text-center text-slate-500">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm">Yaklaşan veya gecikmiş bir ziyaret bulunmuyor.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Son Şikayetler -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Sorumluluk Alanınızdaki Son Şikayetler
                    </h3>
                    <a href="{{ route('admin.sikayetler.index') }}" class="text-sm font-bold text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-1.5 rounded-lg transition-colors">Tümü &rarr;</a>
                </div>
                <div class="p-0">
                    @if(count($stats['son_sikayetler']) > 0)
                        <ul class="divide-y divide-slate-100">
                            @foreach($stats['son_sikayetler']->take(5) as $sikayet)
                                <li class="p-4 hover:bg-slate-50 transition-colors group">
                                    <div class="flex justify-between items-start gap-4">
                                        <div class="shrink-0 pt-1 flex flex-col gap-1 items-center">
                                            @if($sikayet->customer && $sikayet->customer->logo_path)
                                                <img src="{{ asset('storage/' . $sikayet->customer->logo_path) }}" alt="Logo" class="w-10 h-10 rounded-lg object-contain bg-white border border-slate-200 p-0.5">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold border border-indigo-100">
                                                    {{ mb_substr($sikayet->customer ? $sikayet->customer->name : 'M', 0, 2) }}
                                                </div>
                                            @endif
                                            
                                            <span class="px-1.5 py-0.5 text-[9px] bg-slate-100 text-slate-600 rounded font-bold border border-slate-200 uppercase whitespace-nowrap">
                                                {{ $sikayet->sikayetKategori ? $sikayet->sikayetKategori->bolum->name : 'Bölüm Yok' }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('admin.sikayetler.show', $sikayet->id) }}" class="text-sm font-bold text-slate-800 group-hover:text-rose-600 transition-colors truncate block flex items-center gap-2">
                                                {{ $sikayet->musteri_sikayet_konusu }}
                                                
                                                @if($sikayet->iaaProjesi && $sikayet->iaaProjesi->ziyaretPlani)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-700 animate-pulse border border-indigo-200" title="Ziyaret Planlandı/Gerçekleşti">
                                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                        Ziyaret
                                                    </span>
                                                @endif
                                            </a>
                                            <div class="flex items-center gap-2 mt-1.5 text-xs text-slate-500">
                                                <span class="font-medium text-slate-700 truncate max-w-[120px]">{{ $sikayet->customer ? $sikayet->customer->name : 'Bilinmeyen Müşteri' }}</span>
                                                <span>&bull;</span>
                                                <span class="truncate max-w-[100px]">{{ $sikayet->sikayetKategori ? $sikayet->sikayetKategori->ad : 'Kategori Yok' }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $sikayet->created_at->translatedFormat('d M Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center justify-end w-[130px]">
                                            {!! str_replace('inline-flex', 'inline-flex w-full whitespace-normal', $sikayet->musteri_durum_badge) !!}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-8 text-center text-slate-500">
                            <p class="text-sm">Bölgenize ait bir şikayet bulunmuyor.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Müşteri Listesi (Hızlı Bakış) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Sorumlu Olduğunuz Müşteriler
                </h3>
                <a href="{{ route('admin.musteriler.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Tüm Müşteriler &rarr;</a>
            </div>
            <div class="p-6">
                @if(count($stats['sorumlu_musteriler']) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        @foreach($stats['sorumlu_musteriler']->take(10) as $musteri)
                            <a href="{{ route('musteri.profil.show', $musteri->id) }}" class="block border border-slate-100 rounded-xl p-4 hover:border-indigo-200 hover:shadow-md transition-all group cursor-pointer bg-slate-50 hover:bg-white text-center">
                                @if($musteri->logo_path)
                                    <img src="{{ asset('storage/' . $musteri->logo_path) }}" alt="{{ $musteri->name }}" class="w-12 h-12 mx-auto rounded-full object-contain bg-white border border-slate-200 shadow-sm p-1 mb-3 group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-12 h-12 mx-auto bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-700 font-bold mb-3 shadow-sm border border-white group-hover:scale-105 transition-transform">
                                        {{ mb_substr($musteri->name, 0, 2) }}
                                    </div>
                                @endif
                                <h4 class="font-bold text-slate-800 text-sm truncate group-hover:text-indigo-600 transition-colors" title="{{ $musteri->name }}">{{ $musteri->name }}</h4>
                                
                                @php
                                    $temsilciIsim = null;
                                    if($musteri->users && $musteri->users->isNotEmpty()) {
                                        $temsilciIsim = $musteri->users->first()->name;
                                    }
                                @endphp
                                
                                @if($temsilciIsim)
                                    <p class="text-[10px] text-slate-500 mt-1 truncate" title="Müşteri Temsilcisi: {{ $temsilciIsim }}">
                                        <svg class="w-3 h-3 inline mr-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $temsilciIsim }}
                                    </p>
                                @elseif($musteri->email)
                                    <p class="text-[10px] text-slate-500 mt-1 truncate" title="{{ $musteri->email }}">
                                        <svg class="w-3 h-3 inline mr-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $musteri->email }}
                                    </p>
                                @elseif($musteri->phone)
                                    <p class="text-[10px] text-slate-500 mt-1 truncate" title="{{ $musteri->phone }}">
                                        <svg class="w-3 h-3 inline mr-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $musteri->phone }}
                                    </p>
                                @else
                                    <p class="text-[10px] text-slate-400 mt-1">İletişim yok</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-500 text-sm">Hiç aktif müşteriniz bulunmuyor.</div>
                @endif
            </div>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/tr.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('ziyaret-takvimi');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'tr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'multiMonthThree,dayGridMonth,timeGridWeek,listWeek'
                },
                views: {
                    multiMonthThree: {
                        type: 'multiMonthYear',
                        duration: { months: 3 }
                    }
                },
                buttonText: {
                    today: 'Bugün',
                    month: 'Ay',
                    week: 'Hafta',
                    day: 'Gün',
                    list: 'Liste',
                    multiMonthThree: '3 Aylık'
                },
                events: @json($stats['takvim_etkinlikleri'] ?? []),
                eventContent: function(arg) {
                    if (arg.view.type.includes('list')) {
                        var props = arg.event.extendedProps;
                        var html = '<div style="padding: 4px 0;">';
                        html += '<div style="font-weight:bold; color:#1e293b; font-size:14px; margin-bottom: 6px;">' + arg.event.title + '</div>';
                        
                        if (props.department || props.complaint) {
                            html += '<div style="display:flex; align-items:center; flex-wrap:wrap; gap:6px; font-size:12px; color:#475569; margin-bottom: 6px;">';
                            if (props.department) html += '<span style="background:#f1f5f9; border:1px solid #e2e8f0; padding:2px 8px; border-radius:12px; font-weight:700; font-size:10px; text-transform:uppercase;">' + props.department + '</span>';
                            if (props.complaint) html += '<span style="font-weight:600;">' + props.complaint + '</span>';
                            html += '</div>';
                        }
                        
                        if (props.visitors) {
                            html += '<div style="font-size:11px; color:#4f46e5; margin-bottom:4px; font-weight:700;"><svg style="width:12px; height:12px; display:inline; margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>Giden Personel: <span style="font-weight:500;">' + props.visitors + '</span></div>';
                        }
                        
                        if (props.reps) {
                            html += '<div style="font-size:11px; color:#059669; font-weight:700;"><svg style="width:12px; height:12px; display:inline; margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>Firma Yetkilisi: <span style="font-weight:500;">' + props.reps + '</span></div>';
                        }
                        
                        html += '</div>';
                        return { html: html };
                    }
                },
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault(); // don't let the browser navigate normally
                    }
                },
                height: 600,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                }
            });
            calendar.render();
        }
    });
</script>
