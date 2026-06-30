@push('pageTitle')
    İAA Düzenle | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Öneriyi Düzenle') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Hata Gösterimi --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Lütfen aşağıdaki hataları düzeltin:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('iaa.update', $iaa) }}" method="POST" enctype="multipart/form-data" 
                  x-data="{ currency: '{{ old('para_birimi', $iaa->oneren_kazanc_birim ?? 'TL') }}' }">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {{-- SOL SÜTUN: Temel Bilgiler --}}
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                            <div class="p-6 sm:p-8">
                                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b pb-2">Temel Bilgiler</h3>

                                {{-- Başlık --}}
<div class="mb-6">
    <label for="baslik" class="block text-sm font-medium text-gray-700 mb-1">Konu / Başlık</label>
    <input type="text" name="baslik" id="baslik" value="{{ old('baslik', $iaa->baslik) }}" 
           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition duration-150 ease-in-out" required>
</div>

{{-- BÖLÜM VE LOKASYON ALANI --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    
    {{-- Bölüm Seçimi --}}
    <div>
        <label for="bolum_id" class="block text-sm font-medium text-gray-700 mb-1">İlgili Bölüm</label>
        <div class="relative">
            <select name="bolum_id" id="bolum_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm appearance-none" required>
                <option value="" disabled>Seçiniz</option>
                @foreach($bolumler as $bolum)
                    <option value="{{ $bolum->id }}" @selected(old('bolum_id', $iaa->bolum_id) == $bolum->id)>
                        {{ $bolum->ad }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
            </div>
        </div>
    </div>

    {{-- Lokasyon / İlgili Alan --}}
    <div>
        <label for="ilgili_alan" class="block text-sm font-medium text-gray-700 mb-1">Lokasyon / Hat Bilgisi</label>
        <div class="relative">
            <input type="text" name="ilgili_alan" id="ilgili_alan" 
                   value="{{ old('ilgili_alan', $iaa->ilgili_alan) }}" 
                   placeholder="Örn: Paketleme Hattı 2"
                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
        </div>
    </div>
</div>

                                {{-- Mevcut Durum --}}
                                <div class="mb-6">
                                    <label for="mevcut_durum" class="block text-sm font-medium text-gray-700 mb-1">Mevcut Durum / Problem Tanımı</label>
                                    <div class="relative">
                                        <textarea name="mevcut_durum" id="mevcut_durum" rows="5" 
                                                  class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm transition duration-150 ease-in-out" required>{{ old('mevcut_durum', $iaa->mevcut_durum) }}</textarea>
                                        <div class="absolute top-3 right-3 text-red-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Öneri --}}
                                <div>
                                    <label for="oneri" class="block text-sm font-medium text-gray-700 mb-1">İyileştirme Önerisi</label>
                                    <div class="relative">
                                        <textarea name="oneri" id="oneri" rows="5" 
                                                  class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 shadow-sm transition duration-150 ease-in-out" required>{{ old('oneri', $iaa->oneri) }}</textarea>
                                        <div class="absolute top-3 right-3 text-green-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SAĞ SÜTUN: Finansal ve Medya --}}
                    <div class="space-y-6">
                        
                        {{-- Finansal Tahminler Kartı --}}
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tahmini Finansal Etki
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="para_birimi" class="block text-sm font-medium text-gray-700 mb-1">Para Birimi</label>
                                        <select name="para_birimi" x-model="currency" id="para_birimi" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                            @foreach($paraBirimleri as $birim)
                                                <option value="{{ $birim }}" @selected(old('para_birimi', $iaa->oneren_kazanc_birim ?? 'TL') == $birim)>{{ $birim }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                                        <label for="oneren_kazanc_miktar" class="block text-sm font-semibold text-green-800 mb-1">Tahmini Yıllık Kazanç</label>
                                        <div class="relative mt-1 rounded-md shadow-sm">
                                            <input type="number" step="0.01" name="oneren_kazanc_miktar" id="oneren_kazanc_miktar" 
                                                   value="{{ old('oneren_kazanc_miktar', $iaa->oneren_kazanc_miktar) }}" 
                                                   class="block w-full rounded-md border-gray-300 pr-12 focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="0.00">
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                <span class="text-gray-500 sm:text-sm" x-text="currency">TL</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-red-50 rounded-lg border border-red-100">
                                        <label for="oneren_butce_miktar" class="block text-sm font-semibold text-red-800 mb-1">Tahmini Bütçe</label>
                                        <div class="relative mt-1 rounded-md shadow-sm">
                                            <input type="number" step="0.01" name="oneren_butce_miktar" id="oneren_butce_miktar" 
                                                   value="{{ old('oneren_butce_miktar', $iaa->oneren_butce_miktar) }}" 
                                                   class="block w-full rounded-md border-gray-300 pr-12 focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="0.00">
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                <span class="text-gray-500 sm:text-sm" x-text="currency">TL</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Resimler Kartı --}}
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Görseller
                                </h3>

                                {{-- Mevcut Resimler --}}
                                @if($iaa->resimler->isNotEmpty())
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Mevcut Resimler</label>
                                        <div class="grid grid-cols-2 gap-2" id="current-images-container">
                                            @foreach($iaa->resimler as $resim)
                                                <div class="relative group rounded-lg overflow-hidden border border-gray-200" id="resim-{{ $resim->id }}">
                                                    <img src="{{ asset('storage/' . $resim->dosya_yolu) }}" class="object-cover h-24 w-full">
                                                    <button type="button" class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 flex items-center justify-center transition-all duration-200 delete-image-btn" data-image-id="{{ $resim->id }}">
                                                        <span class="bg-red-600 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100">Sil</span>
                                                    </button>
                                                    <input type="hidden" class="deleted-image-input" value="">
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-gray-400 mt-2 italic">* Silmek istediğiniz resmin üzerine tıklayın.</p>
                                    </div>
                                @endif

                                {{-- Yeni Resim Yükleme --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Yeni Resim Ekle</label>
                                    <label class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:border-indigo-500 hover:bg-gray-50 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <span class="relative bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Resim Seç</span>
                                                    <input id="yeni_resimler" name="yeni_resimler[]" type="file" class="sr-only" multiple>
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF (Max 10MB)</p>
                                        </div>
                                    </label>
                                    <div id="new-image-previews" class="mt-4 grid grid-cols-2 gap-2"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Aksiyon Butonları --}}
                        <div class="flex items-center justify-between space-x-4 pt-4">
                            <a href="{{ route('iaa.show', $iaa) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                İptal
                            </a>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Yeni resim önizlemesi
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
                                imgDiv.className = 'relative rounded-lg overflow-hidden border border-gray-200';
                                imgDiv.innerHTML = `<img src="${e.target.result}" class="object-cover h-24 w-full">`;
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