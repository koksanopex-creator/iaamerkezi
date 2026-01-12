@php
    $canEditDetails = auth()->user()->can('arabuluculuk.edit') || 
                      auth()->user()->can('arabuluculuk.approve_legal') || 
                      auth()->user()->hasRole('Superadmin');
@endphp

<div x-data="{ editMode: {{ $canEditDetails && !$case->anlasilan_tutar ? 'true' : 'false' }} }" class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 mb-6 shadow-sm">
    <div class="flex justify-between items-start mb-4">
        <h3 class="font-bold text-indigo-800 flex items-center text-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Anlaşma Detayları
        </h3>
        @if($canEditDetails)
            <button x-show="!editMode" @click="editMode = true" type="button" class="text-sm text-indigo-600 hover:text-indigo-800 font-bold underline flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Düzenle
            </button>
        @endif
    </div>

    {{-- GÖRÜNTÜLEME MODU --}}
    <div x-show="!editMode">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded border border-indigo-100 shadow-sm">
                <label class="block text-xs font-bold text-indigo-400 uppercase mb-1">Anlaşma Tutarı</label>
                <p class="text-2xl font-bold text-indigo-700">{{ $case->anlasilan_tutar ? number_format($case->anlasilan_tutar, 2) . ' TL' : '---' }}</p>
            </div>
            <div class="col-span-2 bg-white p-4 rounded border border-indigo-100 shadow-sm">
                <label class="block text-xs font-bold text-indigo-400 uppercase mb-2">Anlaşılan Maddeler ve Notlar</label>
                @if($case->anlasma_maddeleri)
                    <div class="text-sm text-gray-700 space-y-2 leading-relaxed">
                        {!! nl2br(e($case->anlasma_maddeleri)) !!}
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Henüz detay girilmemiş.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- DÜZENLEME MODU --}}
    @if($canEditDetails)
        <div x-show="editMode" class="mt-4 pt-4 border-t border-indigo-200">
            <form action="{{ route('admin.arabuluculuk.update', $case->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-2 text-gray-700">Anlaşma Tutarı (TL)</label>
                    <input type="number" step="0.01" name="anlasilan_tutar" value="{{ $case->anlasilan_tutar }}" class="w-full md:w-1/3 border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 font-bold text-lg" placeholder="0.00">
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">
                        Anlaşma Maddeleri (Zorunlu Seçim) <span class="text-red-500">*</span>
                    </label>
                    @if(isset($anlasmaMaddeleri) && $anlasmaMaddeleri->count() > 0)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 max-h-60 overflow-y-auto space-y-3 shadow-inner">
                            @foreach($anlasmaMaddeleri as $madde)
                                <label class="flex items-start gap-3 p-2 hover:bg-gray-50 rounded cursor-pointer transition select-none">
                                    <input type="checkbox" name="maddeler_secim[]" value="{{ $madde->icerik }}" 
                                        {{ Str::contains($case->anlasma_maddeleri ?? '', $madde->icerik) ? 'checked' : '' }}
                                        class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <div class="text-sm text-gray-700">
                                        <span class="font-medium">{{ $madde->icerik }}</span>
                                        @if($madde->hukuki_dayanak)
                                            <span class="block text-xs text-gray-400 mt-0.5">({{ $madde->hukuki_dayanak }})</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-2 italic">* Birden fazla madde seçebilirsiniz. Seçimleriniz otomatik birleştirilecektir.</p>
                    @else
                        <div class="text-red-500 text-sm p-2 bg-red-50 rounded">Sistemde tanımlı madde bulunamadı.</div>
                    @endif
                </div>
                @php
                    $mevcutNot = '';
                    if($case->anlasma_maddeleri && Str::contains($case->anlasma_maddeleri, 'EK NOTLAR:')) {
                        $parts = explode('EK NOTLAR:', $case->anlasma_maddeleri);
                        $mevcutNot = trim(end($parts));
                    }
                @endphp
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ek Notlar (Opsiyonel)</label>
                    <textarea name="ek_notlar" class="w-full border-gray-300 rounded text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" placeholder="Listede olmayan özel bir durum varsa buraya yazınız...">{{ $mevcutNot }}</textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="editMode = false" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded font-medium hover:bg-gray-50 transition">Vazgeç</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded font-bold shadow hover:bg-indigo-700 transition">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    @endif
</div>