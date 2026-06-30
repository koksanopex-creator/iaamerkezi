@php
    $durum = $uye->pivot->durum; // onaylandi, bekliyor, reddedildi
    
    // Kim lider? 
    // 1. Proje tamamlanmış/onay sürecindeyse ve 'tamamlayan_lider_id' dondurulmuşsa o kişi liderdir.
    // 2. Proje devam ediyorsa, pivotta rolü 'Lider' olan ve hala aktif (işten ayrılmamış) kişi liderdir.
    // 3. VEYA projenin atanan takımının güncel lideri bu kişiyse (Halef-Selef durumu için).
    
    $isLider = false;
    if ($iaa->tamamlayan_lider_id) {
        $isLider = $iaa->tamamlayan_lider_id == $uye->id;
    } else {
        $pivotLiderMi = $uye->pivot->rol == 'Lider';
        $isLider = ($pivotLiderMi && !$uye->trashed()) || ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $uye->id);
    }
    
    // Stil Ayarları
    $cardClass = 'bg-white border-gray-200';
    
    if($uye->trashed()) {
        $cardClass = 'bg-gray-50 border-gray-200 opacity-75 grayscale';
    } elseif($isLider) {
        $cardClass = 'bg-gradient-to-br from-indigo-50 to-white border-indigo-200 shadow-sm ring-1 ring-indigo-100';
    } elseif($durum == 'bekliyor') {
        $cardClass = 'bg-amber-50/50 border-amber-200 border-dashed';
    } elseif($durum == 'reddedildi') {
        $cardClass = 'bg-red-50/50 border-red-200 opacity-75';
    }
@endphp

<div class="relative flex items-start gap-4 p-4 rounded-xl border transition-all duration-200 hover:shadow-md {{ $cardClass }} group">
    
    {{-- Avatar (Tıklanabilir) --}}
    <a href="{{ route('profile.show', $uye->id) }}" class="flex-shrink-0 relative block">
        @if($uye->profile_photo_path)
            <img class="h-14 w-14 rounded-full object-cover border-2 {{ $isLider ? 'border-indigo-200' : 'border-white' }} shadow-sm group-hover:scale-105 transition-transform duration-200 {{ ($durum == 'reddedildi' || $uye->trashed()) ? 'grayscale' : '' }}" 
                    src="{{ asset('storage/'.$uye->profile_photo_path) }}" 
                    alt="{{ $uye->name }}">
        @else
            <div class="h-14 w-14 rounded-full border-2 {{ $isLider ? 'border-indigo-200' : 'border-white' }} bg-gray-100 flex items-center justify-center text-lg font-bold text-gray-600 shadow-sm group-hover:scale-105 transition-transform duration-200 {{ ($durum == 'reddedildi' || $uye->trashed()) ? 'grayscale' : '' }}">
                {{ substr($uye->name, 0, 1) }}
            </div>
        @endif

        {{-- Durum Noktası (Avatar Üzerinde) --}}
        @if($durum == 'bekliyor' && !$uye->trashed())
            <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full ring-2 ring-white bg-amber-400 animate-pulse" title="Yanıt Bekleniyor"></span>
        @elseif(($durum == 'reddedildi' || $uye->trashed()) && !$isLider)
            <span class="absolute bottom-0 right-0 flex items-center justify-center h-4 w-4 rounded-full ring-2 ring-white bg-red-500 text-white text-[8px] font-bold" title="{{ $uye->trashed() ? 'İşten Ayrıldı' : 'Reddetti' }}">X</span>
        @endif
    </a>

    {{-- Bilgiler --}}
    <div class="flex-1 min-w-0 pt-0.5">
        <div class="flex items-start justify-between">
            <div>
                {{-- İsim --}}
                <a href="{{ route('profile.show', $uye->id) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600 transition-colors flex items-center gap-2 leading-tight">
                    {{ $uye->name }}
                    @if($uye->trashed())
                        <span class="text-[9px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded border border-red-200 whitespace-nowrap">İŞTEN AYRILDI{{ $uye->termination_date ? ' (' . \Carbon\Carbon::parse($uye->termination_date)->format('d.m.Y') . ')' : '' }}</span>
                    @endif
                </a>
                
                {{-- Bölüm --}}
                <p class="text-xs text-gray-500 mt-0.5 font-medium truncate max-w-[140px]" title="{{ optional($uye->bolum)->ad }}">
                    {{ optional($uye->bolum)->ad ?? 'Bölüm Yok' }}
                </p>
            </div>
            
            {{-- Lider Yıldızı ve Tooltip --}}
            @if($isLider)
                <div class="relative group/icon" title="Proje Lideri">
                    <div class="p-1 bg-amber-100 rounded-lg">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                </div>
            @endif
        </div>

        {{-- Alt Durum/Rol Yazısı --}}
        <div class="mt-2 flex items-center gap-2">
            @if($isLider)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-indigo-100 text-indigo-700">
                    Proje Lideri
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-600">
                    Ekip Üyesi
                </span>
            @endif

            @if($durum == 'bekliyor')
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">Yanıt Bekleniyor</span>
                    
                    {{-- ANA SAYFADA İPTAL BUTONU --}}
                    @if(isset($isLeader) && $isLeader && isset($isLocked) && !$isLocked)
                        <button onclick="if(confirm('Daveti iptal etmek istediğinize emin misiniz?')) { Livewire.dispatch('davetIptalFromOuter', { userId: {{ $uye->id }}, iaaId: {{ $iaa->id }} }) }" 
                                class="p-1 rounded-md bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 border border-red-100 shadow-sm" 
                                title="Daveti İptal Et">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            @elseif($durum == 'reddedildi')
                <span class="text-[10px] text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded border border-red-100">Reddetti</span>
            @endif
        </div>
    </div>
</div>