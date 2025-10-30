<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ selectedIds: [], get isAllSelected() { const checkboxes = $el.querySelectorAll('tbody .iaa-checkbox'); return checkboxes.length > 0 && this.selectedIds.length === checkboxes.length }, toggleAll() { const checkboxes = $el.querySelectorAll('tbody .iaa-checkbox'); if (this.isAllSelected) { this.selectedIds = [] } else { this.selectedIds = Array.from(checkboxes).map(cb => cb.value) } } }">
    <div class="p-6 text-gray-900">
        <h3 class="text-lg font-semibold mb-4 text-green-600">Atanmış Projeler ({{ $iaas->count() }})</h3>
        
        <form method="POST" action="{{ route('admin.iaa-yonetim.bulkDestroy') }}" onsubmit="return confirm('Seçili ' + selectedIds.length + ' adet öneriyi kalıcı olarak silmek istediğinizden emin misiniz?');">
            @csrf
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="iaa_ids[]" :value="id">
            </template>
            <div x-show="selectedIds.length > 0" x-transition class="mb-4"><button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg"> <span x-text="selectedIds.length"></span> Adet Seçili Öneriyi Sil</button></div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="p-4"><input type="checkbox" @click="toggleAll" :checked="isAllSelected"></th>
                        <th class="px-6 py-3">Başlık</th><th class="px-6 py-3">Atanan Kişi</th><th class="px-6 py-3">Atanma Tarihi</th><th class="px-6 py-3 text-right">İşlemler</th>
                    </tr></thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($iaas as $iaa)
                            <tr>
                                <td class="p-4"><input type="checkbox" class="iaa-checkbox" value="{{ $iaa->id }}" x-model="selectedIds"></td>
                                <td class="px-6 py-4">{{ $iaa->baslik }}</td><td class="px-6 py-4">{{ $iaa->atanan->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $iaa->atanma_tarihi ? \Carbon\Carbon::parse($iaa->atanma_tarihi)->format('d.m.Y H:i') : 'N/A' }}</td>
                                <td class="px-6 py-4">@include('admin.iaa-yonetim.partials.actions', ['type' => 'atanmis', 'iaa' => $iaa])</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-4 text-center">Bu kategoride bir öneri bulunmamaktadır.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>