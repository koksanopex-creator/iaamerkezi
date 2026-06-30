<div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-indigo-200">
    {{-- Başlık Alanı --}}
    <div
        class="bg-gradient-to-r from-indigo-50 to-white px-6 py-5 border-b border-indigo-200 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-indigo-800">
                Direktör Onayı Bekleyen Projeler
                <span class="ml-2 px-2.5 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                    {{ $iaas->count() }}
                </span>
            </h3>
        </div>
    </div>

    {{-- İçerik Alanı --}}
    @if($iaas->isEmpty())
        <div class="p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">Şu anda direktör onayı bekleyen proje bulunmamaktadır.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proje
                            Başlığı</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bölüm
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Çözüm
                            Takımı</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($iaas as $iaa)
                        <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $iaa->baslik }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">ID: #{{ $iaa->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700 block">{{ $iaa->bolum->ad ?? 'Genel' }}</span>
                                @if($iaa->yonetici_notu)
                                    <div
                                        class="mt-2 bg-yellow-50 border border-yellow-200 rounded-md p-2 flex items-start gap-2 max-w-xs">
                                        <svg class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <span class="text-xs font-bold text-yellow-700 block mb-0.5">Yönetici Notu:</span>
                                            <p class="text-xs text-yellow-800 leading-snug">{{ $iaa->yonetici_notu }}</p>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($iaa->atananTakim)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $iaa->atananTakim->ad }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $iaa->updated_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div x-data="{ showRevizeModal: false, showRedModal: false }"
                                    class="flex items-center justify-end space-x-2">

                                    {{-- İNCELE --}}
                                    <a href="{{ route('proje.workspace.show', $iaa->id) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition-colors">
                                        İncele
                                    </a>

                                    {{-- YENİ: ÖNERİ BUTONU --}}
                                    <a href="{{ route('iaa.show', $iaa->id) }}" target="_blank" 
                                    class="inline-flex items-center px-3 py-1.5 bg-white border border-amber-300 text-amber-700 text-xs font-bold rounded-lg hover:bg-amber-50 transition-colors"
                                    title="Orijinal Öneriyi Görüntüle">
                                        Öneri
                                    </a>

                                    {{-- ONAYLA --}}
                                    <form action="{{ route('admin.iaa-yonetim.direktorOnayiVer', $iaa->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700"
                                            onclick="return confirm('Projeyi onaylayıp yönetime iletmek istediğinize emin misiniz?')">
                                            Onayla
                                        </button>
                                    </form>

                                    {{-- REVİZE --}}
                                    <button @click="showRevizeModal = true" type="button"
                                        class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded-lg hover:bg-yellow-600">
                                        Revize
                                    </button>

                                    {{-- RED --}}
                                    <button @click="showRedModal = true" type="button"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700">
                                        Red
                                    </button>

                                    {{-- MODALS --}}
                                    {{-- Revize Modal --}}
                                    <div x-show="showRevizeModal" class="fixed inset-0 z-50 overflow-y-auto"
                                        style="display: none;">
                                        <div
                                            class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 bg-gray-500 opacity-75" @click="showRevizeModal = false">
                                            </div>
                                            <div
                                                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <form action="{{ route('admin.iaa-yonetim.direktorRevizyon', $iaa->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left">
                                                        <h3
                                                            class="text-lg font-medium text-gray-900 border-b pb-2 mb-4 italic text-yellow-600">
                                                            Direktör Revizyon Talebi</h3>
                                                        <textarea name="not" rows="4"
                                                            class="w-full border-gray-300 rounded-lg text-sm"
                                                            placeholder="Revizyon notu..." required></textarea>
                                                    </div>
                                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                                        <button type="submit"
                                                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Gönder</button>
                                                        <button @click="showRevizeModal = false" type="button"
                                                            class="bg-white border text-gray-700 px-4 py-2 rounded-lg text-sm font-bold">İptal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Red Modal --}}
                                    <div x-show="showRedModal" class="fixed inset-0 z-50 overflow-y-auto"
                                        style="display: none;">
                                        <div
                                            class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 bg-gray-500 opacity-75" @click="showRedModal = false">
                                            </div>
                                            <div
                                                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <form action="{{ route('admin.iaa-yonetim.direktorReddet', $iaa->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left">
                                                        <h3
                                                            class="text-lg font-medium text-gray-900 border-b pb-2 mb-4 italic text-red-600">
                                                            Projeyi Reddet (Direktör)</h3>
                                                        <textarea name="not" rows="4"
                                                            class="w-full border-gray-300 rounded-lg text-sm"
                                                            placeholder="Red gerekçesi..." required></textarea>
                                                    </div>
                                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                                        <button type="submit"
                                                            class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Reddet</button>
                                                        <button @click="showRedModal = false" type="button"
                                                            class="bg-white border text-gray-700 px-4 py-2 rounded-lg text-sm font-bold">İptal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>