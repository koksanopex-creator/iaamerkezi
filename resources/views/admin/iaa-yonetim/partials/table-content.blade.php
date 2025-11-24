<div class="bg-gradient-to-br from-{{ $color }}-50 via-white to-{{ $color }}-100 overflow-hidden shadow-xl sm:rounded-2xl border border-{{ $color }}-200" x-data="tableManager()">
    
    <div class="p-6 sm:p-8 text-gray-900">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-2 h-8 bg-gradient-to-b from-{{ $color }}-400 to-{{ $color }}-600 rounded-full"></div>
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
                    {{ $title }} 
                    <span class="inline-flex items-center justify-center w-8 h-8 ml-2 text-sm font-semibold text-{{ $color }}-600 bg-{{ $color }}-100 rounded-full ring-2 ring-{{ $color }}-200">
                        {{ $iaas->count() }}
                    </span>
                </h3>
            </div>
            
            {{-- SADECE SUPERADMIN GÖREBİLİR: TOPLU SİLME BUTONU --}}
            @role('Superadmin')
            <div x-show="selectedIds.length > 0" x-transition class="mt-4 sm:mt-0">
                <button type="button" @click="submitBulkDelete" class="group relative w-full sm:w-auto inline-flex items-center justify-center bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-2 px-5 rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                    <span class="absolute -top-2 -left-2 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-800 rounded-full ring-2 ring-white" x-text="selectedIds.length"></span>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Seçilenleri Sil
                </button>
            </div>
            @endrole
        </div>

        <div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
            <table class="block sm:table min-w-full">
                
                <thead class="hidden sm:table-header-group">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                        {{-- SADECE SUPERADMIN GÖREBİLİR: TOPLU SEÇİM KUTUSU --}}
                        @role('Superadmin')
                        <th class="p-4 w-12"><input type="checkbox" @click="toggleAll" :checked="isAllSelected" class="w-4 h-4 text-{{ $color }}-600 bg-gray-100 border-gray-300 rounded focus:ring-{{ $color }}-500 focus:ring-2"></th>
                        @endrole

                        @if(in_array($type, ['havuz', 'atanmis']))<th class="px-6 py-3 text-center">Puan</th>@endif
                        <th class="px-6 py-3">Başlık</th>
                        @if(in_array($type, ['onay', 'havuz', 'reddedilmis']))<th class="px-6 py-3">Öneren</th>@endif
                        @if(in_array($type, ['atanmis']))<th class="px-6 py-3">Atanan Takım</th>@endif
                        <th class="px-6 py-3">Tarih</th>
                        <th class="px-6 py-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                
                <tbody class="block sm:table-row-group">
                    @forelse ($iaas as $iaa)
                        <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-{{ $color }}-50 transition-colors group">
                            
                            {{-- SADECE SUPERADMIN GÖREBİLİR: SATIR SEÇİM KUTUSU --}}
                            @role('Superadmin')
                            <td class="p-4 align-middle sm:table-cell"><input type="checkbox" class="iaa-checkbox w-4 h-4 text-{{ $color }}-600 bg-gray-100 border-gray-300 rounded focus:ring-{{ $color }}-500 focus:ring-2" value="{{ $iaa->id }}" x-model="selectedIds"></td>
                            @endrole

                            @if(in_array($type, ['havuz', 'atanmis']))
                                <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Puan:</span>
                                    <div class="text-right sm:text-center">
                                        @if($iaa->puan)
                                        <button x-data @click="$dispatch('open-modal', 'puan-detay-{{ $iaa->id }}')" class="relative inline-flex items-center justify-center w-14 h-14 font-bold text-sm text-white bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all focus:outline-none focus:ring-4 focus:ring-indigo-200">
                                            <span>{{ number_format($iaa->puan, 0, ',', '.') }}</span>
                                        </button>
                                        @else
                                        <div class="inline-flex items-center justify-center w-12 h-12 text-xs font-medium text-gray-400 bg-gray-100 rounded-full border-2 border-dashed border-gray-300">
                                            N/A
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            @endif
                            
                            <td class="flex justify-between items-start p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                <span class="font-semibold text-sm text-gray-500 sm:hidden">Başlık:</span>
                                <div class="text-right sm:text-left">
                                    <p class="text-gray-800 font-medium group-hover:text-{{ $color }}-700 transition-colors inline-flex items-center space-x-2">
                                        <span>{{ $iaa->baslik }}</span>

                                        {{-- REVİZYON BEKLİYOR ETİKETİ --}}
                                        @if ($type == 'atanmis' && $iaa->durum == 'Revize Ediliyor')
                                            <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-yellow-300">
                                                Revizyon Bekliyor
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </td>

                            @if(in_array($type, ['onay', 'havuz', 'reddedilmis']))
                                <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Öneren:</span><div class="text-right sm:text-left">@if ($iaa->gonderen)<div class="inline-flex items-center space-x-2"><div class="w-7 h-7 bg-gradient-to-br from-{{ $color }}-400 to-{{ $color }}-600 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-white">{{ substr($iaa->gonderen->name, 0, 1) }}</span></div><span class="text-sm font-medium text-gray-700">{{ $iaa->gonderen->name }}</span></div>@else<div class="inline-flex items-center space-x-2"><div class="w-7 h-7 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-white">M</span></div><div><span class="text-sm font-medium text-gray-700">{{ $iaa->guest_name }}</span><span class="text-xs text-white bg-gray-500 px-1.5 py-0.5 rounded-full ml-1">Misafir</span></div></div>@endif</div></td>
                            @endif
                            
                            @if(in_array($type, ['atanmis']))
                                 <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                                    <span class="font-semibold text-sm text-gray-500 sm:hidden">Atanan Takım:</span>
                                    <div class="text-right sm:text-left">
                                        @if($iaa->atananTakim)
                                             <div class="inline-flex items-center space-x-2"><div class="w-7 h-7 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-white">{{ Str::substr($iaa->atananTakim->ad, 0, 1) }}</span></div><span class="text-sm font-medium text-gray-700">{{ $iaa->atananTakim->ad }}</span></div>
                                        @else
                                            <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </div>
                                </td>
                            @endif
                            
                            <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><span class="font-semibold text-sm text-gray-500 sm:hidden">Tarih:</span><span class="text-right sm:text-left text-sm text-gray-500">
                                    @if($type === 'onay') {{ $iaa->created_at->format('d.m.Y') }} @endif
                                    @if($type === 'atanmis') {{ $iaa->updated_at->format('d.m.Y') }} @endif
                                    @if($type === 'havuz') {{ $iaa->onaylanma_tarihi ? \Carbon\Carbon::parse($iaa->onaylanma_tarihi)->format('d.m.Y') : 'N/A' }} @endif
                                    @if($type === 'reddedilmis') {{ $iaa->updated_at->format('d.m.Y') }} @endif
                                </span></td>
                            
                            <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle"><div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-2 space-y-2 sm:space-y-0">@include('admin.iaa-yonetim.partials.actions', ['type' => $type, 'iaa' => $iaa])</div></td>
                        </tr>
                    @empty
                        <tr class="block sm:table-row"><td colspan="7" class="p-12 text-center"><div class="flex flex-col items-center justify-center space-y-4"><div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center"><svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><div class="text-center"><h4 class="text-lg font-semibold text-gray-600 mb-1">Henüz öneri bulunmuyor</h4><p class="text-gray-500">Bu kategoride bir öneri bulunmamaktadır.</p></div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>