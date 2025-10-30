<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            İAA Önerisini Düzenle: {{ $iaa->baslik }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Hata Mesajları --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('iaa.update', $iaa) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        {{-- Başlık, Bölüm, Mevcut Durum, Öneri fields are the same --}}
                        <div class="mb-4">
                            <label for="baslik" class="block text-gray-700 text-sm font-bold mb-2">Konu / Başlık:</label>
                            <input type="text" name="baslik" id="baslik" value="{{ old('baslik', $iaa->baslik) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">İlgili Bölüm:</label>
                            <input type="text" value="{{ $iaa->bolum->ad ?? 'Atanmamış' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight bg-gray-200" disabled>
                        </div>
                        <div class="mb-4">
                            <label for="mevcut_durum" class="block text-gray-700 text-sm font-bold mb-2">Mevcut Durum / Problem Tanımı:</label>
                            <textarea name="mevcut_durum" id="mevcut_durum" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('mevcut_durum', $iaa->mevcut_durum) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="oneri" class="block text-gray-700 text-sm font-bold mb-2">İyileştirme Önerisi:</label>
                            <textarea name="oneri" id="oneri" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('oneri', $iaa->oneri) }}</textarea>
                        </div>

                        {{-- ================= YENİ EKLENEN FİNANSAL BİLGİLER BÖLÜMÜ ================= --}}
                        <hr class="my-6 border-t">
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Tahmini Finansal Etki (Opsiyonel)</h3>
                        <div class="mb-6">
                            <label for="para_birimi" class="block text-gray-700 text-sm font-bold mb-2">Kullanılacak Para Birimi</label>
                            <select name="para_birimi" x-model="currency" id="para_birimi" class="shadow border rounded w-full md:w-1/3 py-2 px-3">
                                @foreach($paraBirimleri as $birim)
                                    <option value="{{ $birim }}">{{ $birim }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="oneren_kazanc_miktar" class="block text-gray-700 text-sm font-bold mb-2">Tahmini Yıllık Kazanç (<span x-text="currency"></span>)</label>
                                <input type="number" step="0.01" name="oneren_kazanc_miktar" id="oneren_kazanc_miktar" value="{{ old('oneren_kazanc_miktar', $iaa->oneren_kazanc_miktar) }}" class="shadow appearance-none border rounded w-full py-2 px-3">
                            </div>
                            <div>
                                <label for="oneren_butce_miktar" class="block text-gray-700 text-sm font-bold mb-2">Tahmini Bütçe (<span x-text="currency"></span>)</label>
                                <input type="number" step="0.01" name="oneren_butce_miktar" id="oneren_butce_miktar" value="{{ old('oneren_butce_miktar', $iaa->oneren_butce_miktar) }}" class="shadow appearance-none border rounded w-full py-2 px-3">
                            </div>
                        </div>
                        <hr class="my-6 border-t">
                        {{-- ================= FİNANSAL BİLGİLER BÖLÜMÜ SONU ================= --}}
                        

                        {{-- Mevcut Resimler --}}
                        @if($iaa->resimler->isNotEmpty())
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Mevcut Resimler:</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="current-images-container">
                                    @foreach($iaa->resimler as $resim)
                                        <div class="relative group" id="resim-{{ $resim->id }}">
                                            <img src="{{ Storage::url($resim->dosya_yolu) }}" alt="Mevcut Resim" class="rounded-md object-cover h-40 w-full border border-gray-300">
                                            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs opacity-0 group-hover:opacity-100 transition-opacity delete-image-btn" data-image-id="{{ $resim->id }}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            {{-- DÜZELTME: 'name' attribute'ü başlangıçta olmayacak --}}
                                            <input type="hidden" class="deleted-image-input" value="">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Yeni Resimler --}}
                        <div class="mb-4">
                            <label for="yeni_resimler" class="block text-gray-700 text-sm font-bold mb-2">Yeni Resimler (Opsiyonel, Birden Fazla Seçebilirsiniz):</label>
                            <input type="file" name="yeni_resimler[]" id="yeni_resimler" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" multiple>
                            <div id="new-image-previews" class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                        </div>
                        
                        {{-- Butonlar --}}
                        <div class="flex items-center justify-end">
                            <a href="{{ route('iaa.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-4">
                                İptal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for image preview and deletion --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Yeni resim önizlemesi (Bu kısım aynı kalıyor)
            const yeniResimlerInput = document.getElementById('yeni_resimler');
            const newImagePreviewsContainer = document.getElementById('new-image-previews');
            if (yeniResimlerInput) {
                yeniResimlerInput.addEventListener('change', function(event) {
                    newImagePreviewsContainer.innerHTML = '';
                    if (this.files) {
                        Array.from(this.files).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const imgDiv = document.createElement('div');
                                imgDiv.className = 'relative';
                                imgDiv.innerHTML = `<img src="${e.target.result}" class="rounded-md object-cover h-40 w-full">`;
                                newImagePreviewsContainer.appendChild(imgDiv);
                            };
                            reader.readAsDataURL(file);
                        });
                    }
                });
            }

            // Mevcut resim silme işlemi
            const currentImagesContainer = document.getElementById('current-images-container');
            if (currentImagesContainer) {
                currentImagesContainer.addEventListener('click', function(event) {
                    const button = event.target.closest('.delete-image-btn');
                    if (button) {
                        const imageId = button.dataset.imageId;
                        const imageDiv = document.getElementById(`resim-${imageId}`);
                        
                        if (confirm('Bu resmi silmek istediğinize emin misiniz?')) {
                            imageDiv.style.display = 'none';
                            const input = imageDiv.querySelector('.deleted-image-input');
                            if (input) {
                                input.value = imageId;
                                // DÜZELTME: Sadece silme butonuna basıldığında 'name' attribute'ünü ekliyoruz.
                                input.name = 'silinecek_resimler[]';
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>