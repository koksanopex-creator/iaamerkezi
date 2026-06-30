{{-- Risk Matrisi Widget --}}
@php
    $matrixSize = intval($config['size'] ?? 5);
    if (!in_array($matrixSize, [3, 5]))
        $matrixSize = 5;
    $selectedRow = $formData[$index]['risk_row'] ?? '';
    $selectedCol = $formData[$index]['risk_col'] ?? '';
@endphp

<div x-data="{
    selectedRow: {{ $selectedRow ?: 'null' }},
    selectedCol: {{ $selectedCol ?: 'null' }},
    selectCell(row, col) {
        this.selectedRow = row;
        this.selectedCol = col;
        $wire.set('formData.{{ $index }}.risk_row', row);
        $wire.set('formData.{{ $index }}.risk_col', col);
    }
}">
    <h4 class="text-lg font-semibold text-gray-800 mb-1">{{ $config['title'] ?? 'Risk Matrisi' }}</h4>
    <p class="text-sm text-gray-500 mb-4">Riskin olasılığını ve etkisini değerlendirmek için matristeki uygun hücreye
        tıklayın.</p>

    <div class="flex items-end gap-2">
        {{-- Y Ekseni Etiketi --}}
        <div class="flex flex-col items-center justify-center pr-1" style="height: {{ $matrixSize * 44 }}px">
            <span class="text-xs font-bold text-gray-500 -rotate-90 whitespace-nowrap tracking-wider">OLASILIK →</span>
        </div>

        {{-- Matris Grid --}}
        <div class="flex-grow">
            {{-- Sütun Başlıkları --}}
            <div class="grid gap-1 mb-1" style="grid-template-columns: repeat({{ $matrixSize }}, 1fr);">
                @for($col = 1; $col <= $matrixSize; $col++)
                    <div class="text-center text-[10px] font-bold text-gray-500">{{ $col }}</div>
                @endfor
            </div>

            {{-- Matris Hücreleri --}}
            <div class="grid gap-1" style="grid-template-columns: repeat({{ $matrixSize }}, 1fr);">
                @for($row = $matrixSize; $row >= 1; $row--)
                    @for($col = 1; $col <= $matrixSize; $col++)
                        @php
                            $riskScore = $row * $col;
                            $maxScore = $matrixSize * $matrixSize;
                            $pct = $riskScore / $maxScore;
                            if ($pct >= 0.6)
                                $cellColor = 'bg-red-400 hover:bg-red-500 text-white';
                            elseif ($pct >= 0.35)
                                $cellColor = 'bg-amber-300 hover:bg-amber-400 text-amber-900';
                            elseif ($pct >= 0.15)
                                $cellColor = 'bg-yellow-200 hover:bg-yellow-300 text-yellow-800';
                            else
                                $cellColor = 'bg-green-200 hover:bg-green-300 text-green-800';
                        @endphp
                        <button type="button" @click="selectCell({{ $row }}, {{ $col }})"
                            :class="selectedRow == {{ $row }} && selectedCol == {{ $col }} ? 'ring-4 ring-indigo-500 ring-offset-2 scale-110 z-10' : ''"
                            class="h-10 rounded-md flex items-center justify-center text-xs font-bold cursor-pointer transition-all duration-200 {{ $cellColor }}">
                            {{ $riskScore }}
                        </button>
                    @endfor
                @endfor
            </div>

            {{-- X Ekseni Etiketi --}}
            <p class="text-center text-xs font-bold text-gray-500 mt-2 tracking-wider">ETKİ →</p>
        </div>

        {{-- Satır Numaraları --}}
        <div class="flex flex-col-reverse gap-1 pl-1" style="height: {{ $matrixSize * 44 - 4 }}px">
            @for($row = 1; $row <= $matrixSize; $row++)
                <div class="flex-1 flex items-center justify-center text-[10px] font-bold text-gray-500">{{ $row }}</div>
            @endfor
        </div>
    </div>

    {{-- Lejant --}}
    <div class="flex items-center justify-center gap-4 mt-4">
        <span class="flex items-center gap-1.5 text-xs"><span
                class="w-4 h-4 rounded bg-green-200 border border-green-300"></span> Düşük</span>
        <span class="flex items-center gap-1.5 text-xs"><span
                class="w-4 h-4 rounded bg-yellow-200 border border-yellow-300"></span> Orta</span>
        <span class="flex items-center gap-1.5 text-xs"><span
                class="w-4 h-4 rounded bg-amber-300 border border-amber-400"></span> Yüksek</span>
        <span class="flex items-center gap-1.5 text-xs"><span
                class="w-4 h-4 rounded bg-red-400 border border-red-500"></span> Kritik</span>
    </div>

    {{-- Seçim Sonucu --}}
    <div class="mt-4" x-show="selectedRow && selectedCol" x-transition>
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
                <span class="text-sm font-bold text-indigo-700">Seçilen Risk:</span>
                <span class="px-3 py-1 rounded-full text-sm font-bold" :class="{
                          'bg-green-200 text-green-800': (selectedRow * selectedCol) / {{ $maxScore }} < 0.15,
                          'bg-yellow-200 text-yellow-800': (selectedRow * selectedCol) / {{ $maxScore }} >= 0.15 && (selectedRow * selectedCol) / {{ $maxScore }} < 0.35,
                          'bg-amber-300 text-amber-900': (selectedRow * selectedCol) / {{ $maxScore }} >= 0.35 && (selectedRow * selectedCol) / {{ $maxScore }} < 0.6,
                          'bg-red-400 text-white': (selectedRow * selectedCol) / {{ $maxScore }} >= 0.6
                      }">
                    Olasılık: <span x-text="selectedRow"></span> × Etki: <span x-text="selectedCol"></span> = <span
                        x-text="selectedRow * selectedCol"></span>
                </span>
            </div>
            <label class="block text-sm font-medium text-indigo-700 mb-1">Açıklama / Notlar</label>
            <textarea wire:model="formData.{{ $index }}.risk_notes" rows="3"
                class="block w-full rounded-lg border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white"
                placeholder="Risk hakkında ek notlar veya alınacak önlemler..."></textarea>
        </div>
    </div>

    {{-- Hidden Inputs --}}
    <input type="hidden" wire:model="formData.{{ $index }}.risk_row" x-bind:value="selectedRow">
    <input type="hidden" wire:model="formData.{{ $index }}.risk_col" x-bind:value="selectedCol">
</div>