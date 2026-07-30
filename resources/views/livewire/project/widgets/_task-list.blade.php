{{-- Görev Listesi (Kişi Atamalı) Widget'ı --}}
<div>
    <div class="flex items-center justify-between mb-2">
        <div>
            <h4 class="text-lg font-semibold text-gray-800">
                {{ $config['title'] ?? 'Görev Listesi / Sorumluluk Tablosu' }}</h4>
            <p class="text-sm text-gray-500">Görevleri tanımlayın ve sistemdeki kişilere sorumluluk atayın.</p>
        </div>
        <button type="button" wire:click="addTaskListRow({{ $index }})"
            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
            <svg class="-ml-1 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Yeni Görev Ekle
        </button>
    </div>

    <div class="bg-white border text-sm border-gray-200 rounded-lg overflow-hidden shadow-sm overflow-x-auto">
        @if(isset($toolsData['task_list'][$index]['tasks']) && count($toolsData['task_list'][$index]['tasks']) > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="w-12 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Görev
                            Tanımı</th>
                        <th scope="col"
                            class="w-64 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sorumlu Kişi</th>
                        <th scope="col"
                            class="w-16 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sil
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($toolsData['task_list'][$index]['tasks'] as $taskIndex => $task)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $taskIndex + 1 }}</td>
                            <td class="px-6 py-2">
                                <textarea wire:model="toolsData.task_list.{{ $index }}.tasks.{{ $taskIndex }}.description"
                                    rows="2"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Görev detayı..."></textarea>
                            </td>
                            <td class="px-6 py-2">
                                <select wire:model="toolsData.task_list.{{ $index }}.tasks.{{ $taskIndex }}.assigned_user_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">-- Kişi Seçin --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} @if(method_exists($u, 'getPublicRoleNames') && $u->getPublicRoleNames()->count() > 0) ({{ $u->getPublicRoleNames()->first() }}) @endif</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" wire:click="removeTaskListRow({{ $index }}, {{ $taskIndex }})"
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
                Görev listesi boş. "Yeni Görev Ekle" butonuna basarak görev atamaları yapabilirsiniz.
            </div>
        @endif
    </div>
</div>