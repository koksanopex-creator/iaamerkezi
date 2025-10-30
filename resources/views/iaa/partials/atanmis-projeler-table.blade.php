{{-- ======================================================== --}}
{{-- BU DOSYA, TAKIMLARA ATANMIŞ PROJELERİ LİSTELER --}}
{{-- ======================================================== --}}

<div class="bg-white/60 backdrop-blur-sm rounded-xl shadow-inner border border-gray-200/80 overflow-hidden">
    <table class="block sm:table min-w-full">
        <thead class="hidden sm:table-header-group">
            <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                <th class="px-6 py-4">Proje Başlığı</th>
                <th class="px-6 py-4">Atanan Takım</th>
                <th class="px-6 py-4 text-center">Puan</th>
                <th class="px-6 py-4 text-right">İşlem</th>
            </tr>
        </thead>
        <tbody class="block sm:table-row-group">
            @forelse($atanmisProjeler as $proje)
                <tr class="block mb-4 border bg-white border-gray-200 rounded-lg sm:table-row sm:mb-0 sm:border-0 sm:border-b sm:border-gray-100 hover:bg-green-50 transition-colors">
                    
                    <td class="flex justify-between items-center p-3 sm:table-cell sm:p-4 align-middle">
                        <span class="font-semibold text-sm text-gray-500 sm:hidden">Proje:</span>
                        <span class="text-right sm:text-left font-medium text-indigo-600">{{ $proje->baslik }}</span>
                    </td>

                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                        <span class="font-semibold text-sm text-gray-500 sm:hidden">Takım:</span>
                        <span class="text-right sm:text-left text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full">{{ $proje->atananTakim->ad }}</span>
                    </td>

                    <td class="flex justify-between items-center p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                        <span class="font-semibold text-sm text-gray-500 sm:hidden">Puan:</span>
                        <div class="w-full text-right sm:text-center">
                            <span class="font-bold text-lg text-indigo-600">{{ number_format($proje->puan, 2) }}</span>
                        </div>
                    </td>

                    <td class="p-3 border-t sm:border-0 sm:table-cell sm:p-4 align-middle">
                        <div class="flex justify-end">
                            <a href="{{ route('iaa.show', $proje) }}" class="inline-flex justify-center text-sm font-semibold text-white bg-gray-600 px-3 py-2 rounded-md hover:bg-gray-700">Detayları Gör</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="block sm:table-row">
                    <td colspan="4" class="p-12 text-center text-gray-500">
                        Takımlarınızın üstlendiği bir proje bulunmamaktadır.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>