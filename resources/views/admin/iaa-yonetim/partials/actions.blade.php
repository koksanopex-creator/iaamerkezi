<div class="flex items-center justify-end gap-3">

    {{-- ========================================================== --}}
    {{-- 1. TEMEL İŞLEM: İNCELE / İZLE (SOLDA SABİT KALACAK)       --}}
    {{-- ========================================================== --}}
    @if($iaa->atanan_takim_id)
        <a href="{{ route('proje.workspace.show', $iaa) }}" target="_blank" 
           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors whitespace-nowrap"
           title="İlerleme İzle">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            İzle
        </a>
        <a href="{{ route('iaa.show', $iaa) }}" target="_blank" 
           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-colors whitespace-nowrap"
           title="Orijinal Öneriyi Görüntüle">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Öneri
        </a>
    @else
        <a href="{{ route('iaa.show', $iaa) }}" target="_blank" 
           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 shadow-sm transition-colors whitespace-nowrap"
           title="Detaylı İncele">
            <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            İncele
        </a>
    @endif


    {{-- ========================================================== --}}
    {{-- 2. YÖNETİCİ BUTONLARI (SUPERADMIN)                        --}}
    {{-- ========================================================== --}}
    @role('Superadmin')
    
        {{-- 
            DÜZELTME BURADA: 
            'min-w-[160px]' ekleyerek bu alanın genişliğini sabitledik.
            'justify-end' ile ikonları sağa yasladık.
            Böylece ikon sayısı değişse bile 'İncele' butonunu itip kakmaz.
        --}}
        <div class="flex items-center justify-center bg-gray-50 rounded-md border border-gray-200 p-0.5 min-w-[120px]">
            
            {{-- Onay/Red (Sadece Onay Aşamasında Görünür) --}}
            @if($type === 'onay')
                <button x-data @click="$dispatch('open-modal', 'onayla-modal-{{ $iaa->id }}')" 
                        class="p-1.5 text-green-600 hover:bg-green-100 rounded-md transition-colors" title="Onayla">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
                <button x-data @click="$dispatch('open-modal', 'reddet-modal-{{ $iaa->id }}')" 
                        class="p-1.5 text-red-600 hover:bg-red-100 rounded-md transition-colors" title="Reddet">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
            
            {{-- Diğer Yönetimsel İşlemler --}}
            @if(in_array($type, ['atanmis', 'havuz', 'reddedilmis', 'yonetici-onayi']))
                
                {{-- Puan Düzenle --}}
                @if ($iaa->puan)
                    <button x-data @click.prevent="$dispatch('open-modal', 'puan-duzenle-modal-{{ $iaa->id }}')" 
                            class="p-1.5 text-indigo-600 hover:bg-indigo-100 rounded-md transition-colors" title="Puanı Düzenle">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                @endif
                
                {{-- Önereni Değiştir --}}
                @if($iaa->gonderen_user_id)
                    <a href="{{ route('admin.iaa-yonetim.reassignForm', $iaa) }}" 
                       class="p-1.5 text-purple-600 hover:bg-purple-100 rounded-md transition-colors" title="Önereni Değiştir">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </a>
                @endif

                {{-- Geri Al --}}
                <form method="POST" action="{{ route('admin.iaa-yonetim.geriAl', $iaa) }}" class="inline-flex" onsubmit="return confirm('İşlemi geri almak istediğinize emin misiniz?');"> 
                    @csrf @method('patch') 
                    <button type="submit" class="p-1.5 text-amber-600 hover:bg-amber-100 rounded-md transition-colors" title="Geri Al (Havuz'a Döndür)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                </form>
            @endif

            {{-- Sil (Her zaman en sonda) --}}
            <form method="POST" action="{{ route('admin.iaa-yonetim.destroy', $iaa) }}" class="inline-flex" onsubmit="return confirm('Bu öneriyi kalıcı olarak silmek istediğinizden emin misiniz?');">
                @csrf @method('delete')
                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Kalıcı Olarak Sil">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>

        </div>

    @endrole

</div>