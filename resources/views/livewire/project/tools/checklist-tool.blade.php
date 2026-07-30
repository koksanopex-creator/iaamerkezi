<div>
    <div class="space-y-4">
        <div class="flex justify-between items-center mb-2">
            <h5 class="text-sm font-semibold text-gray-700">Kontrol Listesi Aracı</h5>
            <div x-data="{ saving: false }" 
                 x-on:tool-saved.window="saving = true; setTimeout(() => saving = false, 2000)" 
                 class="text-xs text-gray-500 flex items-center h-4">
                <span x-show="saving" x-transition.opacity class="text-green-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Kaydedildi
                </span>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h6 class="text-sm font-bold text-gray-800">Maddeler</h6>
                @if($canManage)
                    <button wire:click="addItem" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded hover:bg-indigo-200 font-bold transition-colors shadow-sm">
                        + Yeni Madde Ekle
                    </button>
                @endif
            </div>

            <div class="space-y-3">
                @foreach($items as $index => $item)
                    <div class="flex items-start gap-3 bg-white p-3 rounded-md border border-gray-100 shadow-sm transition-all hover:border-indigo-100" wire:key="item-{{ $index }}-{{ $item['checked'] ? 'checked' : 'unchecked' }}">
                        <div class="pt-1">
                            @if($canManage)
                                <input type="checkbox" wire:click="toggleItem({{ $index }})" @if($item['checked'] ?? false) checked @endif class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                            @else
                                @if($item['checked'] ?? false)
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            @if($canManage)
                                <input type="text" wire:model.live.debounce.1000ms="items.{{ $index }}.text" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @if($item['checked'] ?? false) line-through text-gray-400 bg-gray-50 @endif" placeholder="Kontrol edilecek maddeyi yazın...">
                            @else
                                <div class="text-sm py-1 @if($item['checked'] ?? false) line-through text-gray-400 @else text-gray-700 @endif">
                                    {{ $item['text'] ?: '-' }}
                                </div>
                            @endif
                        </div>

                        @if($canManage && count($items) > 1)
                            <button wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-600 p-1.5 transition-colors bg-red-50 hover:bg-red-100 rounded">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
            
            {{-- İlerleme Çubuğu --}}
            @php
                $total = count($items);
                $checked = collect($items)->where('checked', true)->count();
                $percentage = $total > 0 ? round(($checked / $total) * 100) : 0;
            @endphp
            <div class="mt-6">
                <div class="flex justify-between text-xs mb-1 font-semibold text-gray-600">
                    <span>İlerleme</span>
                    <span>{{ $checked }} / {{ $total }} (%{{ $percentage }})</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
