{{-- Önceliklendirme Matrisi Widget'ı --}}
<div>
    <div class="flex items-center justify-between mb-2">
        <div>
            <h4 class="text-lg font-semibold text-gray-800">
                {{ $config['title'] ?? 'Önceliklendirme Matrisi (Efor / Etki)' }}</h4>
            <p class="text-sm text-gray-500">Maddelerin gerektirdiği eforu ve yaratacağı etkiyi değerlendirin.</p>
        </div>
        <button type="button" wire:click="addPrioritizationMatrixRow({{ $index }})"
            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
            <svg class="-ml-1 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Yeni Madde Ekle
        </button>
    </div>

    <div class="bg-white border text-sm border-gray-200 rounded-lg overflow-hidden shadow-sm overflow-x-auto">
        @if(isset($toolsData['prioritization_matrix'][$index]['items']) && count($toolsData['prioritization_matrix'][$index]['items']) > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksiyon /
                            Madde</th>
                        <th scope="col"
                            class="w-32 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Efor</th>
                        <th scope="col"
                            class="w-32 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Etki</th>
                        <th scope="col"
                            class="w-16 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sil
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($toolsData['prioritization_matrix'][$index]['items'] as $itemIndex => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3">
                                <textarea
                                    wire:model="toolsData.prioritization_matrix.{{ $index }}.items.{{ $itemIndex }}.action"
                                    rows="1"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                    placeholder="Aksiyon tanımı..."></textarea>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <select wire:model="toolsData.prioritization_matrix.{{ $index }}.items.{{ $itemIndex }}.effort"
                                    class="block w-full pl-3 pr-8 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md font-medium text-gray-700">
                                    <option value="düşük">Düşük</option>
                                    <option value="orta">Orta</option>
                                    <option value="yüksek">Yüksek</option>
                                </select>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <select wire:model="toolsData.prioritization_matrix.{{ $index }}.items.{{ $itemIndex }}.impact"
                                    class="block w-full pl-3 pr-8 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md font-medium text-gray-700">
                                    <option value="düşük">Düşük</option>
                                    <option value="orta">Orta</option>
                                    <option value="yüksek">Yüksek</option>
                                </select>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" wire:click="removePrioritizationMatrixRow({{ $index }}, {{ $itemIndex }})"
                                    class="text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-6 text-center text-sm text-gray-500">
                Matrise henüz madde eklenmedi. "Yeni Madde Ekle" butonunu kullanın.
            </div>
        @endif
    </div>
</div>