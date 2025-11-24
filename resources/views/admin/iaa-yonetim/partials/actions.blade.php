<div class="flex items-center justify-end space-x-2">

    {{-- ========================================================== --}}
    {{-- 1. HERKESİN GÖREBİLECEĞİ BUTONLAR (İZLEME/İNCELEME)       --}}
    {{-- ========================================================== --}}
    @if($type === 'atanmis')
        <a href="{{ route('proje.workspace.show', $iaa) }}" target="_blank" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">İlerleme İzle</a>
    @else
        <a href="{{ route('iaa.show', $iaa) }}" target="_blank" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700">İncele</a>
    @endif


    {{-- ========================================================== --}}
    {{-- 2. SADECE SUPERADMIN'İN GÖREBİLECEĞİ YÖNETİM BUTONLARI    --}}
    {{-- ========================================================== --}}
    @role('Superadmin')
    
        {{-- Onay/Red (İlk Triyaj Aşaması) --}}
        @if($type === 'onay')
            <button x-data @click="$dispatch('open-modal', 'onayla-modal-{{ $iaa->id }}')" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">Onayla</button>
            <button x-data @click="$dispatch('open-modal', 'reddet-modal-{{ $iaa->id }}')" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">Reddet</button>
        @endif
        
        {{-- Diğer Yönetimsel İşlemler (Sil, Puanla, Geri Al) --}}
        @if(in_array($type, ['atanmis', 'havuz', 'reddedilmis', 'yonetici-onayi']))
            
            {{-- Puan Düzenle --}}
            @if ($iaa->puan)
                <button x-data @click.prevent="$dispatch('open-modal', 'puan-duzenle-modal-{{ $iaa->id }}')" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-slate-600 hover:bg-slate-700">Puanı Düzenle</button>
            @endif
            
            {{-- Önereni Değiştir --}}
            @if($iaa->gonderen_user_id)
                <a href="{{ route('admin.iaa-yonetim.reassignForm', $iaa) }}" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700">Önereni Değiştir</a>
            @endif

            {{-- Geri Al --}}
            <form method="POST" action="{{ route('admin.iaa-yonetim.geriAl', $iaa) }}" class="inline" onsubmit="return confirm('İşlemi geri almak istediğinize emin misiniz?');"> 
                @csrf @method('patch') 
                <button type="submit" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600">Geri Al</button>
            </form>
        @endif

        {{-- Sil --}}
        <form method="POST" action="{{ route('admin.iaa-yonetim.destroy', $iaa) }}" class="inline" onsubmit="return confirm('Bu öneriyi kalıcı olarak silmek istediğinizden emin misiniz?');">
            @csrf @method('delete')
            <button type="submit" class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium rounded-md shadow-sm text-white bg-black hover:bg-gray-800">Sil</button>
        </form>

    @endrole

</div>