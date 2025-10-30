<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Başlık</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Öneren</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bölüm</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                @if($type != 'pending')
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem Yapan</th>
                @endif
                <th class="relative px-6 py-3"><span class="sr-only">İşlemler</span></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($iaas as $iaa)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $iaa->baslik }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $iaa->gonderen->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $iaa->bolum->ad ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $iaa->created_at->format('d.m.Y') }}</td>
                    @if($type != 'pending')
                        <td class="px-6 py-4 whitespace-nowrap">{{ $iaa->onaylayan->name ?? 'N/A' }}</td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center space-x-2 justify-end">
                            <a href="{{ route('iaa.show', $iaa) }}" class="px-3 py-1 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">İncele</a>
                            @if($type == 'pending')
                                <button x-data="{ id: {{ $iaa->id }} }" @click="$dispatch('open-modal', 'onayla-modal-' + id)" class="px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">Onayla</button>
                                <button x-data="{ id: {{ $iaa->id }} }" @click="$dispatch('open-modal', 'reddet-modal-' + id)" class="px-3 py-1 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">Reddet</button>
                            @else
                                <form method="post" action="{{ route('admin.iaa-yonetim.geriAl', $iaa) }}">
                                    @csrf
                                    @method('patch')
                                    <button type="submit" class="px-3 py-1 bg-yellow-500 text-white rounded-md text-sm hover:bg-yellow-600">Geri Al</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        Bu kategoride bir İAA önerisi bulunmamaktadır.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>