<x-app-layout>
    @push('pageTitle', 'Anlaşma Maddeleri Havuzu | ')
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Anlaşma Maddeleri Havuzu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- BAŞARI MESAJI --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- 1. YENİ MADDE EKLEME KARTI --}}
            @if(auth()->user()->can('arabuluculuk.settings_create') || auth()->user()->hasRole('Superadmin'))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-indigo-100">
                <div class="p-6 bg-white">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-indigo-100 text-indigo-700 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </span>
                        Yeni Madde Tanımla
                    </h3>
                    
                    <form action="{{ route('admin.arabuluculuk.tanim.storeMadde') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="flex-grow w-full">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Madde İçeriği</label>
                            <input type="text" name="icerik" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Örn: İşçi, tüm alacaklarını aldığını beyan eder." required>
                        </div>
                        <div class="w-full md:w-1/4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hukuki Dayanak (Opsiyonel)</label>
                            <input type="text" name="hukuki_dayanak" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Örn: İş Kanunu Md. 25">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold transition shadow-md w-full md:w-auto">
                            Kaydet
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- 2. MEVCUT MADDELER LİSTESİ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Mevcut Maddeler Listesi</h3>
                    <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $maddeler->count() }} Kayıt</span>
                </div>

                @if($maddeler->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Madde İçeriği</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Hukuki Dayanak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Oluşturma</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($maddeler as $index => $madde)
                            <tr x-data="{ 
                                    editing: false, 
                                    icerik: '{{ addslashes($madde->icerik) }}', 
                                    dayanak: '{{ addslashes($madde->hukuki_dayanak) }}',
                                    // Orjinal verileri yedekliyoruz
                                    oldIcerik: '{{ addslashes($madde->icerik) }}',
                                    oldDayanak: '{{ addslashes($madde->hukuki_dayanak) }}',
                                    
                                    // İptal Fonksiyonu
                                    cancelEdit() {
                                        this.editing = false;
                                        this.icerik = this.oldIcerik;   // Eskiye döndür
                                        this.dayanak = this.oldDayanak; // Eskiye döndür
                                    }
                                }" 
                                class="hover:bg-gray-50 transition">
                                
                                {{-- SIRA NO --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                                    {{ $index + 1 }}
                                </td>

                                {{-- İÇERİK --}}
                                <td class="px-6 py-4">
                                    <div x-show="!editing" class="text-sm text-gray-900 font-medium">
                                        <span x-text="icerik"></span>
                                    </div>
                                    <div x-show="editing" x-cloak>
                                        <form id="form-update-{{ $madde->id }}" action="{{ route('admin.arabuluculuk.tanim.updateMadde', $madde->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <textarea name="icerik" x-model="icerik" class="w-full text-sm border-gray-300 rounded p-1 focus:ring-indigo-500 focus:border-indigo-500" rows="2"></textarea>
                                        </form>
                                    </div>
                                </td>

                                {{-- DAYANAK --}}
                                <td class="px-6 py-4">
                                    <div x-show="!editing">
                                        <span x-show="dayanak" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded border border-blue-200" x-text="dayanak"></span>
                                        <span x-show="!dayanak" class="text-gray-400 text-xs">-</span>
                                    </div>
                                    <div x-show="editing" x-cloak>
                                        <input form="form-update-{{ $madde->id }}" type="text" name="hukuki_dayanak" x-model="dayanak" class="w-full text-xs border-gray-300 rounded p-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </td>

                                {{-- TARİH --}}
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $madde->created_at->format('d.m.Y') }}
                                </td>

                                {{-- İŞLEMLER --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    
                                    <div x-show="!editing" class="flex justify-end gap-2">
                                        @if(auth()->user()->can('arabuluculuk.settings_edit') || auth()->user()->hasRole('Superadmin'))
                                            <button @click="editing = true" class="text-indigo-600 hover:text-indigo-900" title="Düzenle">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                        @endif

                                        @if(auth()->user()->can('arabuluculuk.settings_delete') || auth()->user()->hasRole('Superadmin'))
                                            <form action="{{ route('admin.arabuluculuk.tanim.destroyMadde', $madde->id) }}" method="POST" onsubmit="return confirm('Bu maddeyi silmek istediğinize emin misiniz?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Sil">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- DÜZENLEME BUTONLARI --}}
                                    <div x-show="editing" class="flex justify-end gap-2" x-cloak>
                                        <button form="form-update-{{ $madde->id }}" type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-xs font-bold hover:bg-green-700">Kaydet</button>
                                        {{-- İPTAL BUTONU GÜNCELLENDİ --}}
                                        <button type="button" @click="cancelEdit()" class="bg-gray-300 text-gray-700 px-2 py-1 rounded text-xs font-bold hover:bg-gray-400">İptal</button>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-10 text-center flex flex-col items-center justify-center text-gray-500">
                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-lg font-medium">Henüz kayıtlı anlaşma maddesi bulunmuyor.</p>
                        <p class="text-sm">Yukarıdaki formdan yeni maddeler ekleyebilirsiniz.</p>
                    </div>
                @endif
            </div>

            {{-- 3. LOG KAYITLARI (EN ALTTA VE TAM GENİŞLİK) --}}
            @role('Superadmin')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 mt-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        İşlem Geçmişi (Loglar)
                    </h3>
                </div>
                <div class="p-6 max-h-[300px] overflow-y-auto">
                    <div class="grid grid-cols-1 gap-2">
                        @foreach($logs as $log)
                            <div class="text-xs flex items-center p-2 rounded hover:bg-gray-50 border-l-4 {{ $log->islem_turu == 'SİLME' ? 'border-red-400' : ($log->islem_turu == 'DÜZENLEME' ? 'border-blue-400' : 'border-green-400') }}">
                                <span class="font-bold text-gray-700 w-32 truncate">{{ $log->user->name }}</span>
                                <span class="text-gray-400 w-32">{{ $log->created_at->format('d.m.Y H:i') }}</span>
                                <span class="font-bold {{ $log->islem_turu == 'SİLME' ? 'text-red-600' : ($log->islem_turu == 'DÜZENLEME' ? 'text-blue-600' : 'text-green-600') }} w-24">
                                    {{ $log->islem_turu }}
                                </span>
                                <span class="text-gray-600 flex-1">{{ $log->detay }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>


                {{-- YENİ EKLENEN: TÜMÜNÜ GÖR BUTONU --}}
                    <div class="p-3 border-t border-gray-200 bg-gray-50 text-center">
                        <a href="{{ route('admin.arabuluculuk.tanim.showAllLogs') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                            Tüm Geçmişi Gör &rarr;
                        </a>
                    </div>
                    {{-- BİTİŞ --}}

            </div>
            @endrole

        </div>
    </div>
</x-app-layout>