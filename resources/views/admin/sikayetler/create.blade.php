<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h2 class="font-bold text-xl md:text-2xl text-gray-800">Yeni Müşteri Şikayeti Ekle</h2>
        </div>
    </x-slot>

    <div class="py-6 md:py-12 bg-gradient-to-br from-gray-50 to-gray-100 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-2xl rounded-xl sm:rounded-2xl">
                <div class="h-2 bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500"></div>

                <div class="p-4 sm:p-8">
                    <div class="mb-6 md:mb-8 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Şikayet Bilgileri</h3>
                        <p class="text-sm text-gray-600">Müşteri şikayetini detaylı bir şekilde kaydedin ve takip sürecini başlatın.</p>
                    </div>

                    <form action="{{ route('admin.sikayetler.store') }}" method="POST" enctype="multipart/form-data" x-data="fileUploadComponent()">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">

                            {{-- YENİ CANLI SEÇİM BİLEŞENİ --}}
                            {{-- YENİ CANLI SEÇİM BİLEŞENİ --}}
                            <div class="md:col-span-2 mb-6">
                                @php
                                    $currentUser = auth()->user();
                                    $isMusteri = !$currentUser->is_personnel && $currentUser->customer_id;
                                @endphp

                                @if($isMusteri)
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Firma</label>
                                    <div class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                        <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold mr-3">
                                            {{ substr($currentUser->customer->name ?? 'F', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $currentUser->customer->name ?? 'Firmanız' }}</p>
                                            <p class="text-xs text-gray-500">Bu şikayet firmanız adına kaydedilecektir.</p>
                                        </div>
                                    </div>
                                    {{-- Gizli input ile veriyi gönderiyoruz --}}
                                    <input type="hidden" name="customer_id" value="{{ $currentUser->customer_id }}">
                                    {{-- Livewire bileşenini render etmiyoruz çünkü müşteri seçmeyecek --}}
                                @else
                                    {{-- Controller'dan gelen ID'yi Livewire bileşenine aktarıyoruz --}}
                                    <livewire:admin.sikayet-musteri-secimi :preselected-customer-id="$preselectedCustomerId ?? null" />
                                @endif
                            </div>

                            <div class="group md:col-span-2 mb-6">
                                <label class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-6.998l-6 .75m-.75-7.5l6 .75m6-.75l-6 .75M3 12h18M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                    </svg>
                                    <span>Konum Tipi <span class="text-red-500 ml-1">*</span></span>
                                </label>
                                <div class="mt-2 flex flex-wrap items-center gap-4 md:space-x-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="konum_tipi" value="Yurt İçi" class="form-radio w-5 h-5 text-red-600 focus:ring-red-500" {{ old('konum_tipi', 'Yurt İçi') == 'Yurt İçi' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Yurt İçi</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="konum_tipi" value="Yurt Dışı" class="form-radio w-5 h-5 text-red-600 focus:ring-red-500" {{ old('konum_tipi') == 'Yurt Dışı' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">Yurt Dışı</span>
                                    </label>
                                </div>
                                @error('konum_tipi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- KATEGORİ SİSTEMİ --}}
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" 
                                 x-data="categoryDependency('{{ old('sikayet_kategorisi_id') }}', '{{ old('sikayet_alt_kategori_id', old('sikayet_alt_kategori_diger') ? 'other' : '') }}')">
                                
                                {{-- 1. Ana Kategori --}}
                                <div class="group">
                                    <label for="sikayet_kategorisi_id" class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                        <svg class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                        <span>Şikayet Kategorisi <span class="ml-1 text-red-500">*</span></span>
                                    </label>
                                    <div class="relative">
                                        <select name="sikayet_kategorisi_id" id="sikayet_kategorisi_id" required 
                                                x-model="selectedCategory"
                                                @change="fetchSubCategories(false)"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white">
                                            <option value="">-- Kategori Seçiniz --</option>
                                            @foreach($kategoriler as $kategori)
                                                <option value="{{ $kategori->id }}">{{ $kategori->ad }}</option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></div>
                                    </div>
                                    @error('sikayet_kategorisi_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- 2. Alt Kategori --}}
                                <div class="group" x-show="subCategories.length > 0 || showOtherOption" style="display: none;">
                                    <label for="sikayet_alt_kategori_id" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                        Alt Kategori
                                        <span x-show="isLoading" class="ml-2 text-xs text-gray-400">(Yükleniyor...)</span>
                                    </label>
                                    <div class="relative">
                                        <select name="sikayet_alt_kategori_id" id="sikayet_alt_kategori_id" 
                                                x-model="selectedSubCategory"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white">
                                            <option value="">-- Alt Kategori Seçiniz --</option>
                                            <template x-for="sub in subCategories" :key="sub.id">
                                                <option :value="sub.id" x-text="sub.ad"></option>
                                            </template>
                                            <template x-if="showOtherOption">
                                                <option value="other">Diğer / Belirtilmemiş</option>
                                            </template>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></div>
                                    </div>
                                    @error('sikayet_alt_kategori_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- 3. Diğer Açıklama --}}
                                <div class="group md:col-span-2 bg-gray-50 p-4 rounded border border-gray-200" 
                                     x-show="selectedSubCategory === 'other'" style="display: none;" x-transition>
                                    <label for="sikayet_alt_kategori_diger" class="block text-sm font-medium text-gray-800 mb-1" x-text="otherLabel"></label>
                                    <input type="text" name="sikayet_alt_kategori_diger" id="sikayet_alt_kategori_diger" 
                                           value="{{ old('sikayet_alt_kategori_diger') }}"
                                           placeholder="Lütfen sorunu kısaca tanımlayınız..."
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 py-3">
                                     @error('sikayet_alt_kategori_diger') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="group mb-6">
                                <label for="musteri_oncelik" class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/></svg>
                                    <span>Öncelik <span class="ml-1 text-red-500">*</span></span>
                                </label>
                                <div class="relative">
                                    <select name="musteri_oncelik" id="musteri_oncelik" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white">
                                        <option value="Düşük" {{ old('musteri_oncelik') == 'Düşük' ? 'selected' : '' }}>🟢 Düşük</option>
                                        <option value="Normal" {{ old('musteri_oncelik', 'Normal') == 'Normal' ? 'selected' : '' }}>🟡 Normal</option>
                                        <option value="Yüksek" {{ old('musteri_oncelik') == 'Yüksek' ? 'selected' : '' }}>🟠 Yüksek</option>
                                        <option value="Acil" {{ old('musteri_oncelik') == 'Acil' ? 'selected' : '' }}>🔴 Acil</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></div>
                                </div>
                                @error('musteri_oncelik') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="group mb-6">
                                <label for="musteri_sikayet_tarihi" class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                                    <span>Şikayet Tarihi <span class="ml-1 text-red-500">*</span></span>
                                </label>
                                <input type="date" name="musteri_sikayet_tarihi" id="musteri_sikayet_tarihi" value="{{ old('musteri_sikayet_tarihi', date('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900">
                                @error('musteri_sikayet_tarihi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 group mb-6">
                                <label for="musteri_sikayet_konusu" class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    <span>Şikayet Konusu <span class="ml-1 text-red-500">*</span></span>
                                </label>
                                <input type="text" name="musteri_sikayet_konusu" id="musteri_sikayet_konusu" value="{{ old('musteri_sikayet_konusu') }}" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900" placeholder="Şikayetin kısa bir özeti">
                                @error('musteri_sikayet_konusu') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 group mb-6">
                                <label for="musteri_sikayet_detayi" class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                    <span>Şikayet Detayı <span class="ml-1 text-red-500">*</span></span>
                                </label>
                                <textarea name="musteri_sikayet_detayi" id="musteri_sikayet_detayi" rows="5" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900 resize-y" placeholder="Şikayetin detaylı açıklamasını giriniz...">{{ old('musteri_sikayet_detayi') }}</textarea>
                                <p class="mt-2 text-xs text-gray-500">Şikayetle ilgili tüm detayları mümkün olduğunca açıklayıcı bir şekilde yazınız.</p>
                                @error('musteri_sikayet_detayi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 group mb-6">
                                <label for="dosyalar" class="flex items-start font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                    <span>Kanıtlar (Dosya Ekle)</span>
                                </label>
                                <input type="file" name="dosyalar[]" id="dosyalar" multiple accept="image/*,video/mp4,application/pdf,.doc,.docx,.xls,.xlsx"
                                       @change="updatePreviews($event)" x-ref="fileInput"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:transition-colors file:cursor-pointer border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                                <p class="mt-2 text-xs text-gray-500">Resim, PDF, Word, Video ekleyebilirsiniz (Mobil/Masaüstü). Maksimum: 10MB.</p>
                                @error('dosyalar.*') <span class="text-red-500 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror

                                <div x-show="previews.length > 0" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    <template x-for="(preview, index) in previews" :key="index">
                                        <div class="relative group bg-gray-100 rounded-lg overflow-hidden border">
                                            <template x-if="preview.url">
                                                <img :src="preview.url" class="object-cover h-24 w-full" alt="Önizleme">
                                            </template>
                                            <template x-if="!preview.url">
                                                <div class="flex flex-col items-center justify-center h-24 bg-gray-200 p-2">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0011.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <p class="text-xs text-gray-500 mt-1 truncate" x-text="preview.name"></p>
                                                </div>
                                            </template>
                                            <button @click.prevent="removePreview(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-75 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row items-center justify-between mt-8 pt-6 border-t border-gray-200 gap-4">
                            <a href="{{ route('admin.sikayetler.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                İptal
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Şikayeti Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fileUploadComponent() {
            return {
                previews: [],
                files: [],
                updatePreviews(event) {
                    let selectedFiles = Array.from(event.target.files);
                    this.files = this.files.concat(selectedFiles);
                    selectedFiles.forEach(file => {
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            let preview = {
                                url: file.type.startsWith('image/') ? e.target.result : null,
                                name: file.name
                            };
                            this.previews.push(preview);
                        };
                        reader.readAsDataURL(file);
                    });
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.fileInput.files = dataTransfer.files;
                },
                removePreview(index) {
                    this.previews.splice(index, 1);
                    this.files.splice(index, 1);
                    const dataTransfer = new DataTransfer();
                    this.files.forEach(file => dataTransfer.items.add(file));
                    this.$refs.fileInput.files = dataTransfer.files;
                }
            }
        }

        function categoryDependency(initialCategoryId, initialSubCategoryId) {
            return {
                selectedCategory: initialCategoryId,
                selectedSubCategory: initialSubCategoryId,
                subCategories: [],
                showOtherOption: false,
                otherLabel: 'Diğer Açıklama',
                isLoading: false,

                init() {
                    if (this.selectedCategory) {
                        this.fetchSubCategories(true);
                    }
                },

                fetchSubCategories(keepSelection = false) {
                    if (!this.selectedCategory) {
                        this.subCategories = [];
                        this.selectedSubCategory = '';
                        this.showOtherOption = false;
                        return;
                    }
                    this.isLoading = true;
                    var path = window.location.pathname;
                    var rootUrl = window.location.origin;
                    if (path.indexOf('/iaa/') > -1) {
                        rootUrl += '/iaa';
                    }
                    var apiUrl = rootUrl + '/api/get-alt-kategoriler/' + this.selectedCategory;
                    
                    fetch(apiUrl)
                        .then(res => {
                            if (!res.ok) throw new Error("API Hatası");
                            return res.json();
                        })
                        .then(data => {
                            this.subCategories = data.alt_kategoriler;
                            this.showOtherOption = data.diger_goster;
                            this.otherLabel = data.diger_baslik || 'Lütfen detay belirtiniz:';
                            if (!keepSelection) {
                                this.selectedSubCategory = '';
                            }
                        })
                        .catch(err => console.error("HATA:", err))
                        .finally(() => this.isLoading = false);
                }
            }
        }
    </script>
</x-app-layout>