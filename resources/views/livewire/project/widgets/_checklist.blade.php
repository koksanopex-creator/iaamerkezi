{{-- Kontrol Listesi (Checklist) Widget --}}
<div>
    <h4 class="text-lg font-semibold text-gray-800 mb-1">{{ $config['title'] ?? 'Kontrol Listesi' }}</h4>
    <p class="text-sm text-gray-500 mb-4">Aşağıdaki maddeleri kontrol ederek tamamlayın.</p>

    @php
        $items = !empty($config['items']) ? array_values(array_filter(array_map('trim', explode("\n", $config['items'])))) : [];
        $checkedCount = 0;
        if (isset($formData[$index]['checklist'])) {
            foreach ($formData[$index]['checklist'] as $val) {
                if ($val)
                    $checkedCount++;
            }
        }
        $totalItems = count($items);
        $percentage = $totalItems > 0 ? round(($checkedCount / $totalItems) * 100) : 0;
    @endphp

    @if(count($items) > 0)
        {{-- İlerleme Çubuğu --}}
        <div class="mb-4">
            <div class="flex items-center justify-between text-sm mb-1">
                <span class="font-medium text-gray-600">İlerleme</span>
                <span
                    class="font-bold {{ $percentage === 100 ? 'text-green-600' : 'text-indigo-600' }}">{{ $checkedCount }}/{{ $totalItems }}
                    ({{ $percentage }}%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="h-2.5 rounded-full transition-all duration-500 {{ $percentage === 100 ? 'bg-green-500' : 'bg-indigo-500' }}"
                    style="width: {{ $percentage }}%"></div>
            </div>
        </div>

        {{-- Madde Listesi --}}
        <div class="space-y-2">
            @foreach($items as $itemIndex => $item)
                <label
                    class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg px-4 py-3 cursor-pointer hover:bg-gray-50 transition-colors group">
                    <input type="checkbox" wire:model.live="formData.{{ $index }}.checklist.{{ $itemIndex }}"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5">
                    <span
                        class="text-sm text-gray-700 group-hover:text-gray-900 {{ isset($formData[$index]['checklist'][$itemIndex]) && $formData[$index]['checklist'][$itemIndex] ? 'line-through text-gray-400' : '' }}">
                        {{ $item }}
                    </span>
                </label>
            @endforeach
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-700">
            <strong>Uyarı:</strong> Bu kontrol listesi için madde tanımlanmamış. Admin panelinden widget ayarlarında
            "Maddeler" alanına her satıra bir madde yazın.
        </div>
    @endif
</div>