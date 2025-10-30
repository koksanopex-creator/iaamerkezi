<x-guest-layout> {{-- Senin mevcut guest layout'unu kullanıyoruz --}}

    {{-- Form Başlığı ve Açıklama --}}
    <div class="mb-8 pb-6 border-b border-gray-200">
        <div class="flex items-center space-x-3 mb-2">
             <div class="p-2 bg-red-100 rounded-lg">
                 <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                 </svg>
             </div>
             <h2 class="font-bold text-2xl text-gray-800">Müşteri Şikayet Bildirimi</h2>
        </div>
        <p class="text-sm text-gray-600 pl-11">Lütfen yaşadığınız sorunla ilgili aşağıdaki bilgileri eksiksiz doldurunuz.</p>
    </div>

    {{-- Başarı/Hata Mesajları --}}
    @if (session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
         <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Hata!</strong>
            <ul class="mt-1 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Alpine.js ile dosya önizleme --}}
    <form action="{{ route('public.sikayet.store') }}" method="POST" enctype="multipart/form-data" x-data="fileUploadComponent()">
        @csrf
        {{-- === Grid Sınıfları Güncellendi === --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">

            {{-- Müşteri Adı --}}
            <div class="group col-span-1"> {{-- Explicit col-span --}}
                <label for="musteri_adi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                    Müşteri Adı <span class="ml-1 text-red-500">*</span>
                </label>
                <input type="text" name="musteri_adi" id="musteri_adi" value="{{ old('musteri_adi', auth()->user()?->name) }}" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-2.5 text-gray-900" placeholder="Adınız ve Soyadınız">
                @error('musteri_adi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Müşteri İletişim (E-posta - Zorunlu) --}}
            <div class="group col-span-1"> {{-- Explicit col-span --}}
                <label for="musteri_iletisim" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                     <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                    E-posta Adresiniz <span class="ml-1 text-red-500">*</span>
                </label>
                <input type="email" name="musteri_iletisim" id="musteri_iletisim" value="{{ old('musteri_iletisim', auth()->user()?->email) }}" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-2.5 text-gray-900" placeholder="ornek@mail.com">
                <p class="mt-1 text-xs text-gray-500">Takip linki ve şifreniz bu adrese gönderilecektir.</p>
                @error('musteri_iletisim') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Müşteri Konum --}}
            <div class="group col-span-1 md:col-span-2">
                <label class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-6.998l-6 .75m-.75-7.5l6 .75m6-.75l-6 .75M3 12h18M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                     </svg>
                    Konum Tipi <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="mt-2 flex items-center space-x-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="konum_tipi" value="Yurt İçi" class="form-radio text-red-600 focus:ring-red-500" {{ old('konum_tipi', 'Yurt İçi') == 'Yurt İçi' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Yurt İçi</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="konum_tipi" value="Yurt Dışı" class="form-radio text-red-600 focus:ring-red-500" {{ old('konum_tipi') == 'Yurt Dışı' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Yurt Dışı</span>
                    </label>
                </div>
                @error('konum_tipi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Şikayet Kategorisi --}}
            <div class="group col-span-1">
                <label for="sikayet_kategorisi_id" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                    Şikayet Kategorisi <span class="ml-1 text-red-500">*</span> {{-- Zorunlu kabul edildi --}}
                </label>
                <div class="relative">
                    <select name="sikayet_kategorisi_id" id="sikayet_kategorisi_id" required {{-- Zorunlu kabul edildi --}}
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-10 py-2.5 text-gray-900 appearance-none bg-white">
                        <option value="">-- Kategori Seçiniz --</option>
                        {{-- Controller'dan gelen $kategoriler değişkenini kullanıyoruz --}}
                        @isset($kategoriler)
                            @foreach($kategoriler as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('sikayet_kategorisi_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->ad }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></div>
                </div>
                @error('sikayet_kategorisi_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Şikayet Tarihi --}}
            <div class="group col-span-1">
                <label for="musteri_sikayet_tarihi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                     <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                    Olayın Yaşandığı Tarih <span class="ml-1 text-red-500">*</span>
                </label>
                <input type="date" name="musteri_sikayet_tarihi" id="musteri_sikayet_tarihi" value="{{ old('musteri_sikayet_tarihi', date('Y-m-d')) }}" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-2.5 text-gray-900">
                @error('musteri_sikayet_tarihi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Şikayet Konusu --}}
            <div class="col-span-1 md:col-span-2 group">
                <label for="musteri_sikayet_konusu" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    Şikayet Konusu <span class="ml-1 text-red-500">*</span>
                </label>
                <input type="text" name="musteri_sikayet_konusu" id="musteri_sikayet_konusu" value="{{ old('musteri_sikayet_konusu') }}" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-2.5 text-gray-900" placeholder="Sorunu kısaca özetleyin">
                @error('musteri_sikayet_konusu') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Şikayet Detayı --}}
            <div class="col-span-1 md:col-span-2 group">
                <label for="musteri_sikayet_detayi" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                     <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                    Şikayet Detayı <span class="ml-1 text-red-500">*</span>
                </label>
                <textarea name="musteri_sikayet_detayi" id="musteri_sikayet_detayi" rows="5" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900 resize-y" placeholder="Lütfen yaşadığınız sorunu tüm detaylarıyla açıklayınız...">{{ old('musteri_sikayet_detayi') }}</textarea>
                <p class="mt-2 text-xs text-gray-500">Şikayetle ilgili tüm detayları mümkün olduğunca açıklayıcı bir şekilde yazınız.</p>
                @error('musteri_sikayet_detayi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Dosya Yükleme --}}
            <div class="col-span-1 md:col-span-2 group">
                <label for="dosyalar" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                     <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                    Kanıtlar (Dosya Ekle)
                </label>
                <input
                    type="file"
                    name="dosyalar[]"
                    id="dosyalar"
                    multiple
                    accept="image/*,video/mp4,application/pdf,.doc,.docx,.xls,.xlsx"
                    capture="environment"
                    @change="updatePreviews($event)"
                    x-ref="fileInput"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:transition-colors file:cursor-pointer border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                <p class="mt-2 text-xs text-gray-500">Resim, PDF, Word, Video ekleyebilirsiniz (Mobil/Masaüstü). Maksimum: 10MB.</p>
                @error('dosyalar.*') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror

                {{-- Önizleme Alanı --}}
                <div x-show="previews.length > 0" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"> {{-- lg:grid-cols-5 eklendi --}}
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
                            <div class="absolute bottom-0 left-0 right-0 p-1 bg-black bg-opacity-50">
                                <p class="text-xs text-white truncate" x-text="preview.name"></p>
                            </div>
                            <button @click.prevent="removePreview(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-0.5 opacity-75 group-hover:opacity-100 transition-opacity focus:outline-none focus:ring-2 focus:ring-red-600">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
            <a href="{{ url('/') }}" class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                İptal
            </a>
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-150 ease-in-out">
                 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Şikayeti Kaydet
            </button>
        </div>
    </form>

    {{-- Alpine.js Fonksiyonu --}}
    <script>
            function fileUploadComponent() {
                return {
                    previews: [], files: [],
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
        </script>
</x-guest-layout>