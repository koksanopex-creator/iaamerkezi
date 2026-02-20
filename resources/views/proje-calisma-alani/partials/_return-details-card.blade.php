{{-- [DÜZELTME] Sadece Müşteri Şikayeti Kaynaklı Projelerde Çalışsın --}}
@if($iaa->musteriSikayeti)

    @php
        // İlişki üzerinden iadeyi çekiyoruz
        $iade = $iaa->musteriSikayeti->iadeler->first();
        
        // Dosya uzantısı ve resim kontrolü
        $isImage = false;
        $fileExtension = '';
        if($iade && $iade->dosya_yolu) {
            $fileExtension = pathinfo($iade->dosya_yolu, PATHINFO_EXTENSION);
            $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        }

        // Yetki Kontrolü (Düzenleme butonu için)
        $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || auth()->user()->hasRole('Superadmin');
        $isOnayBekliyor = $iaa->durum == 'Bölüm Onayı Bekliyor';
    @endphp

    @if($iade)
    <div class="mt-8 mb-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md relative">
        
        {{-- GERİ AL / DÜZENLE BUTONU (Sadece Lider ve Onay Bekliyorsa) --}}
        @if($isLeader && $isOnayBekliyor)
            <div class="absolute top-4 right-4 z-10">
                <form action="{{ route('proje.recallSubmission', $iaa->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-lg shadow-sm text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Düzenle / Geri Al
                    </button>
                </form>
            </div>
        @endif

        {{-- BAŞLIK ALANI --}}
        <div class="bg-gradient-to-r from-red-50 via-white to-white px-6 py-5 border-b border-red-100 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                    </div>
                    İade ve Hurda Bildirimi
                </h3>
                <p class="text-xs text-red-600 mt-1 pl-11">Bu proje kapsamında müşteri iadesi kesinleşmiştir.</p>
            </div>
            
            <div class="flex items-center gap-3 md:mr-32"> {{-- mr-32 butona yer açmak için --}}
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200 shadow-sm">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    KAYDEDİLDİ
                </span>
            </div>
        </div>
        
        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- 1. ÜRÜN GRUBU --}}
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Ürün Grubu</p>
                        <p class="text-base font-bold text-gray-800 mt-0.5">{{ $iade->urun_turu }}</p>
                    </div>
                </div>

                {{-- 2. İADE SEBEBİ --}}
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">İade Sebebi</p>
                        <p class="text-base font-bold text-gray-800 mt-0.5">{{ $iade->iade_sebebi }}</p>
                    </div>
                </div>

                {{-- 3. MİKTAR (İADE / TOPLAM) - GARANTİLİ GÖSTERİM --}}
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <div class="w-full">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">İade / Toplam Parti</p>
                        <div class="flex items-baseline gap-1 mt-0.5">
                            <span class="text-xl font-black text-red-600">{{ floatval($iade->miktar) }}</span> 
                            <span class="text-gray-400 text-sm">/</span> 
                            <span class="text-lg font-bold text-gray-700">{{ floatval($iade->toplam_parti_miktari) }}</span> 
                            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">{{ $iade->birim }}</span>
                        </div>
                        
                        {{-- Progress Bar --}}
                        @php 
                            $toplam = floatval($iade->toplam_parti_miktari);
                            $miktar = floatval($iade->miktar);
                            $oran = ($toplam > 0) ? ($miktar / $toplam) * 100 : 0; 
                        @endphp
                        <div class="w-full bg-gray-100 rounded-full h-2 mt-2 overflow-hidden border border-gray-200">
                            <div class="bg-gradient-to-r from-red-500 to-pink-600 h-2 rounded-full" style="width: {{ $oran }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-[10px] font-bold text-red-500">%{{ number_format($oran, 1) }} İade</p>
                            <p class="text-[10px] text-gray-400">Kalan: {{ $toplam - $miktar }} {{ $iade->birim }}</p>
                        </div>
                    </div>
                </div>

                {{-- 4. TARİH VE LOKASYON --}}
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 00-2-2H7a2 2 0 00-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Tarih & Lokasyon</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">
                            {{ $iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : '-' }}
                        </p>
                        <p class="text-[10px] text-gray-500 mt-0.5 truncate max-w-[150px]" title="{{ $iaa->musteriSikayeti->customer->address }}">
                            {{ $iaa->musteriSikayeti->customer->address ?? 'Lokasyon Yok' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- İKİNCİ SATIR: AÇIKLAMA VE DOSYA --}}
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- AÇIKLAMA ALANI --}}
                <div class="lg:col-span-2">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        Ek Açıklama / Notlar
                    </h4>
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 text-sm text-slate-700 leading-relaxed min-h-[100px]">
                        @if($iade->aciklama)
                            {{ $iade->aciklama }}
                        @else
                            <span class="text-gray-400 italic">Herhangi bir açıklama girilmemiş.</span>
                        @endif
                    </div>
                </div>

                {{-- DOSYA ALANI (Görsel Önizlemeli) --}}
                <div class="lg:col-span-1">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Ekli Belge
                    </h4>

                    @if($iade->dosya_yolu)
                        @if($isImage)
                            {{-- RESİM ÖNİZLEMESİ --}}
                            <div class="group relative w-full h-40 bg-gray-100 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                                <img src="{{ asset('storage/' . $iade->dosya_yolu) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="İade Belgesi">
                                
                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <a href="{{ asset('storage/' . $iade->dosya_yolu) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white text-gray-900 rounded-lg font-bold text-xs shadow-lg hover:bg-gray-100 transform hover:scale-105 transition-all">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                        Büyüt
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- DOSYA KARTI --}}
                            <a href="{{ asset('storage/' . $iade->dosya_yolu) }}" target="_blank" class="block group">
                                <div class="flex items-center p-4 bg-white border border-gray-200 rounded-xl hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                                    <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="ml-3 overflow-hidden">
                                        <p class="text-sm font-bold text-gray-800 truncate group-hover:text-indigo-600 transition-colors">Dosyayı İndir</p>
                                        <p class="text-xs text-gray-500 uppercase">{{ $fileExtension }} Dosyası</p>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @else
                        {{-- DOSYA YOKSA --}}
                        <div class="h-full min-h-[100px] flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-300 text-gray-400">
                            <svg class="w-8 h-8 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <span class="text-xs">Dosya Eklenmemiş</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

@endif
{{-- Müşteri Şikayeti Değilse Hiçbir Şey Basılmaz --}}