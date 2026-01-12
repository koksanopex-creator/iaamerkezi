{{-- MUTABAKAT VE DURUM YÖNETİMİ --}}
@if($case->status != 'kapatildi' && $case->status != 'odeme_bekliyor')
    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
        <div class="flex justify-between items-center">
            
            {{-- SOL TARAFA: DURUM BİLGİSİ --}}
            <div>
                <h4 class="font-bold text-yellow-800">Mutabakat Durumu</h4>
                <p class="text-sm text-yellow-600">
                    Şu anki durum: 
                    <span class="font-bold uppercase text-black">
                        {{ $case->mutabakat == 'beklemede' ? 'HENÜZ KARAR VERİLMEDİ' : $case->mutabakat }}
                    </span>
                </p>
            </div>

            {{-- SAĞ TARAFA: İŞLEM BUTONLARI --}}
            @if($case->mutabakat == 'beklemede')
                @if(auth()->user()->can('arabuluculuk.approve_legal') || 
                    auth()->user()->hasRole('Superadmin') || 
                    (auth()->user()->can('arabuluculuk.assign_mediator') && auth()->id() == $case->created_by))
                    
                    <form action="{{ route('admin.arabuluculuk.updateStatus', $case->id) }}" method="POST" class="flex gap-3">
                        @csrf @method('PATCH')
                        <button type="submit" name="mutabakat" value="anlasildi" class="bg-green-600 text-white px-4 py-2 rounded shadow font-bold hover:bg-green-700">Anlaşıldı</button>
                        <button type="submit" name="mutabakat" value="anlasilmadi" class="bg-red-600 text-white px-4 py-2 rounded shadow font-bold hover:bg-red-700">Anlaşılmadı</button>
                    </form>
                @else
                    <span class="text-sm text-gray-400 italic bg-white px-3 py-1 rounded border border-gray-200">
                        Mutabakat kararı bekleniyor...
                    </span>
                @endif
            @else
                @if( 
                    (in_array($case->status, ['hukuk_incelemesinde', 'anlasma_saglanamadi']) && auth()->id() == $case->created_by) || 
                    (in_array($case->status, ['yonetim_onayinda', 'arabulucuda']) && (auth()->user()->can('arabuluculuk.approve_legal') || auth()->user()->hasRole('Superadmin')))
                )
                    <form action="{{ route('admin.arabuluculuk.revertStatus', $case->id) }}" method="POST" onsubmit="return confirm('İşlemi geri alıp taslak moduna dönmek istiyor musunuz?');">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-gray-500 hover:text-red-600 underline flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Kararı Geri Al (Düzenlemeye Aç)
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
@endif

{{-- ARABULUCU ATAMA ALANI --}}
@if(in_array($case->status, ['yonetim_onayinda', 'arabulucuda']) && (auth()->user()->can('arabuluculuk.assign_mediator') || auth()->user()->hasRole('Superadmin') || auth()->user()->can('arabuluculuk.approve_legal')))
    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 p-2 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-blue-900">Arabulucu Bilgisi</h4>
                    @if($case->arabulucu)
                        <p class="text-sm text-blue-700">Atanan: <span class="font-bold">{{ $case->arabulucu->name }}</span></p>
                    @else
                        <p class="text-sm text-red-500">Henüz arabulucu atanmadı.</p>
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.arabuluculuk.assignMediator', $case->id) }}" method="POST" class="flex gap-2 w-full md:w-auto">
                @csrf @method('PATCH')
                <select name="arabulucu_id" class="border-gray-300 rounded text-sm w-full md:w-64">
                    <option value="">Arabulucu Seçiniz...</option>
                    @foreach($arabulucular as $arabulucu)
                        <option value="{{ $arabulucu->id }}" {{ $case->arabulucu_id == $arabulucu->id ? 'selected' : '' }}>
                            {{ $arabulucu->name }} ({{ $arabulucu->sicil_no ?? 'Sicil Yok' }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow font-bold text-sm whitespace-nowrap">
                    {{ $case->arabulucu_id ? 'Değiştir' : 'Ata' }}
                </button>
            </form>
        </div>
    </div>
@endif