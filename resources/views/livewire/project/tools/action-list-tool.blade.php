<div>
    <div class="space-y-4">
        <div class="flex justify-between items-center mb-2">
            <h5 class="text-sm font-semibold text-gray-700">Aksiyon Listesi Aracı</h5>
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
                <h6 class="text-sm font-bold text-gray-800">Aksiyon Planı Tablosu</h6>
                @if($canManage)
                    <button wire:click="addItem" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded hover:bg-indigo-200 font-bold transition-colors shadow-sm">
                        + Yeni Aksiyon Ekle
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksiyon (Yapılacak İş)</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Sorumlu</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Hedef Tarih</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Durum</th>
                            @if($canManage)
                                <th scope="col" class="px-2 py-3 w-10"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2">
                                    @if($canManage)
                                        <input type="text" wire:model.live.debounce.1000ms="items.{{ $index }}.action" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Aksiyon açıklaması">
                                    @else
                                        <div class="text-sm text-gray-900">{{ $item['action'] ?: '-' }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($canManage)
                                        <input type="text" wire:model.live.debounce.1000ms="items.{{ $index }}.owner" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="İsim / Departman">
                                    @else
                                        <div class="text-sm text-gray-600">{{ $item['owner'] ?: '-' }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($canManage)
                                        <input type="date" wire:model.live.debounce.1000ms="items.{{ $index }}.target_date" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @else
                                        <div class="text-sm text-gray-600">{{ $item['target_date'] ? \Carbon\Carbon::parse($item['target_date'])->format('d.m.Y') : '-' }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($canManage)
                                        <select wire:model.live="items.{{ $index }}.status" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="pending">Beklemede</option>
                                            <option value="in_progress">Devam Ediyor</option>
                                            <option value="completed">Tamamlandı</option>
                                            <option value="cancelled">İptal</option>
                                        </select>
                                    @else
                                        @php
                                            $statusLabels = [
                                                'pending' => ['label' => 'Beklemede', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                'in_progress' => ['label' => 'Devam Ediyor', 'class' => 'bg-blue-100 text-blue-800'],
                                                'completed' => ['label' => 'Tamamlandı', 'class' => 'bg-green-100 text-green-800'],
                                                'cancelled' => ['label' => 'İptal', 'class' => 'bg-red-100 text-red-800'],
                                            ];
                                            $currentStatus = $statusLabels[$item['status'] ?? 'pending'];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $currentStatus['class'] }}">
                                            {{ $currentStatus['label'] }}
                                        </span>
                                    @endif
                                </td>
                                @if($canManage)
                                    <td class="px-2 py-2 text-right">
                                        @if(count($items) > 1)
                                            <button wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-600 p-1.5 transition-colors bg-red-50 hover:bg-red-100 rounded">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
