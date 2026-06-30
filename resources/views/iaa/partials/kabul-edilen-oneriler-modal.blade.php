{{-- Öneren kişinin kabul edilen önerilerini listeleyen modal --}}
<x-modal name="kabul-edilen-oneriler-modal-{{ $iaa->id }}" focusable>
    <div class="p-6">
        {{-- Başlık --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Kabul Edilen Öneriler</h2>
                    @if($iaa->gonderen)
                        <p class="text-sm text-gray-500">{{ $iaa->gonderen->name }}</p>
                    @endif
                </div>
            </div>

            {{-- Sayaç badge --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800">
                {{ $kabul_edilen_oneriler->count() }} Öneri
            </span>
        </div>

        {{-- Tablo veya boş durum --}}
        @if($kabul_edilen_oneriler->isNotEmpty())
            <div class="overflow-y-auto max-h-80 border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Öneri Başlığı</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bölüm</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($kabul_edilen_oneriler as $oneri)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ route('iaa.show', $oneri->id) }}"
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline transition-colors"
                                       target="_blank">
                                        {{ Str::limit($oneri->baslik, 55) }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $oneri->bolum?->ad ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $durumRenk = match($oneri->durum) {
                                            'Tamamlandı' => 'emerald',
                                            'Atandı', 'Devam Ediyor' => 'blue',
                                            default => 'indigo',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-{{ $durumRenk }}-100 text-{{ $durumRenk }}-800">
                                        {{ $oneri->durum }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">Henüz kabul edilmiş öneri yok</p>
                <p class="text-xs text-gray-400 mt-1">Bu kullanıcının önerileri henüz değerlendirme sürecindedir.</p>
            </div>
        @endif

        {{-- Kapat butonu --}}
        <div class="mt-5 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">Kapat</x-secondary-button>
        </div>
    </div>
</x-modal>
