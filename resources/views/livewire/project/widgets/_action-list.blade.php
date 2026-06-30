{{-- Aksiyon Listesi Widget'ı --}}
<div>
    <div class="flex items-center justify-between mb-2">
        <div>
            <h4 class="text-lg font-semibold text-gray-800">{{ $config['title'] ?? 'Aksiyon Listesi' }}</h4>
            <p class="text-sm text-gray-500">Yapılacak aksiyonları dinamil olarak ekleyip durumlarını takip edin.</p>
        </div>
        <button type="button" wire:click="addActionListRow({{ $index }})"
            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <svg class="-ml-1 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Yeni Aksiyon Ekle
        </button>
    </div>

    <div class="bg-white border text-sm border-gray-200 rounded-lg overflow-hidden shadow-sm">
        @if(isset($toolsData['action_list'][$index]['items']) && count($toolsData['action_list'][$index]['items']) > 0)
            <div class="divide-y divide-gray-200">
                @foreach($toolsData['action_list'][$index]['items'] as $itemIndex => $item)
                    <div class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">

                        {{-- Durum Checkbox'ı --}}
                        <div class="flex-shrink-0">
                            <input type="checkbox"
                                wire:model.live="toolsData.action_list.{{ $index }}.items.{{ $itemIndex }}.is_completed"
                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                        </div>

                        {{-- Aksiyon Metni --}}
                        <div class="flex-grow">
                            <textarea wire:model="toolsData.action_list.{{ $index }}.items.{{ $itemIndex }}.text" rows="1"
                                class="block w-full border-0 border-b border-transparent bg-transparent focus:border-indigo-500 focus:ring-0 sm:text-sm pl-0 {{ $item['is_completed'] ? 'line-through text-gray-400' : 'text-gray-900' }}"
                                placeholder="Aksiyon detayını buraya yazın..."></textarea>
                        </div>

                        {{-- Sil Butonu --}}
                        <div class="flex-shrink-0">
                            <button type="button" wire:click="removeActionListRow({{ $index }}, {{ $itemIndex }})"
                                class="text-gray-400 hover:text-red-500 focus:outline-none transition-colors"
                                title="Satırı Sil">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-sm text-gray-500">
                Henüz aksiyon eklenmedi. Sağ üstteki butonu kullanarak ekleyebilirsiniz.
            </div>
        @endif
    </div>
</div>