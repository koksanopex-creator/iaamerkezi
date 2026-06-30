@push('pageTitle')
    Yeni İAA Önerisi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Yeni İyileştirme Önerisi') }}
        </h2>
    </x-slot>

    {{-- CDN Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Uyarı Alanı (Bölüm Atanmamışsa) --}}
            @if (!Auth::user()->bolum_id)
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 shadow-sm rounded-r-lg" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-bold">
                                İAA önerebilmek için bir bölüme atanmış olmalısınız.
                            </p>
                            <p class="text-sm text-red-600">Lütfen yöneticinizle iletişime geçin.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Hata Mesajları --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 shadow-sm">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <h3 class="font-bold text-red-800">Lütfen formdaki hataları düzeltin:</h3>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700 ml-2">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- Ana Form Kartı --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                
                {{-- Kart Başlığı --}}
                <div class="bg-indigo-50/50 px-6 py-4 border-b border-indigo-100 flex items-center justify-between">
                    <div class="text-indigo-900 font-medium text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-red-500 font-bold mx-1">*</span> ile işaretli alanlar zorunludur.
                    </div>
                </div>

                <div class="p-6 sm:p-10">
                    <form 
                        action="{{ route('iaa.store') }}" 
                        method="POST" 
                        enctype="multipart/form-data"
                        x-data="{ 
                            currency: 'TL',
                            files: [],
                            errorMessage: '',
                            
                            handleFileSelect(e) {
                                const fileList = e.target.files;
                                this.errorMessage = '';
                                const newFiles = Array.from(fileList).filter(file => file.type.startsWith('image/'));
                                
                                if (newFiles.length === 0 && fileList.length > 0) {
                                    this.errorMessage = 'Lütfen sadece resim dosyası seçin.'; return;
                                }
                                if (this.files.length + newFiles.length > 5) {
                                    this.errorMessage = 'En fazla 5 resim yükleyebilirsiniz.'; return;
                                }

                                newFiles.forEach(file => {
                                    if (file.size > 4 * 1024 * 1024) { 
                                        this.errorMessage = `'${file.name}' çok büyük (Max 4MB).`; return;
                                    }
                                    let reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.files.push({ preview: e.target.result, fileObject: file });
                                        this.updateInput(); 
                                    };
                                    reader.readAsDataURL(file);
                                });
                            },
                            removeFile(index) {
                                this.files.splice(index, 1);
                                this.updateInput();
                            },
                            updateInput() {
                                const dt = new DataTransfer();
                                this.files.forEach(f => dt.items.add(f.fileObject));
                                $refs.fileInput.files = dt.files;
                            },
                            formatSize(size) {
                                return size > 1024 * 1024 ? (size / 1048576).toFixed(1) + ' MB' : (size / 1024).toFixed(1) + ' KB';
                            }
                        }"
                    >
                        @csrf
                        
                        <div class="space-y-8">
                            
                            {{-- Bölüm 1: Detaylar --}}
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">1</div>
                                    Öneri Detayları
                                </h3>
                                
                                <div class="space-y-6 pl-11">
                                    
                                    {{-- YENİ DÜZEN: 3'lü Grid --}}
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        {{-- 1. Kendi Bölümü --}}
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Sizin Bölümünüz</label>
                                            <input type="text" value="{{ Auth::user()->bolum->ad ?? 'Tanımsız' }}" disabled class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm py-3 px-4 font-semibold">
                                        </div>

                                        {{-- 2. Hedef Bölüm (Zorunlu) --}}
                                        <div>
                                            <label for="bolum_id" class="block text-sm font-bold text-gray-700 mb-2">Sorumlu Bölüm <span class="text-red-500">*</span></label>
                                            <select name="bolum_id" id="bolum_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-sm py-3 px-4 bg-white">
                                                <option value="">Seçiniz...</option>
                                                @foreach($bolumler as $bolum)
                                                    <option value="{{ $bolum->id }}" {{ (old('bolum_id') == $bolum->id || Auth::user()->bolum_id == $bolum->id) ? 'selected' : '' }}>{{ $bolum->ad }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-[10px] text-gray-400 mt-1">Bu sorunu kim çözmeli?</p>
                                        </div>

                                        {{-- 3. Spesifik Alan (YENİ EKLENEN) --}}
                                        <div>
                                            <label for="konum_text" class="block text-sm font-bold text-gray-700 mb-2">Tam Konum / Alan <span class="text-red-500">*</span></label>
                                            <input type="text" name="konum_text" id="konum_text" value="{{ old('konum_text') }}" required 
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-sm py-3 px-4 placeholder-gray-400" 
                                                placeholder="Örn: Kantar, Palet Alanı...">
                                            <p class="text-[10px] text-gray-400 mt-1">Sorun tam olarak nerede?</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="baslik" class="block text-sm font-bold text-gray-700 mb-2">Konu / Başlık <span class="text-red-500">*</span></label>
                                        <input type="text" name="baslik" id="baslik" value="{{ old('baslik') }}" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-sm py-3 px-4" placeholder="Örn: Paketleme Hattı Ergonomi İyileştirmesi">
                                    </div>

                                    <div>
                                        <label for="mevcut_durum" class="block text-sm font-bold text-gray-700 mb-2">Mevcut Durum / Problem <span class="text-red-500">*</span></label>
                                        <textarea name="mevcut_durum" id="mevcut_durum" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-sm py-3 px-4 resize-none" placeholder="Sorunu detaylıca açıklayın...">{{ old('mevcut_durum') }}</textarea>
                                    </div>

                                    <div>
                                        <label for="oneri" class="block text-sm font-bold text-gray-700 mb-2">Çözüm Öneriniz <span class="text-red-500">*</span></label>
                                        <textarea name="oneri" id="oneri" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-sm py-3 px-4 resize-none" placeholder="Çözüm fikriniz nedir?">{{ old('oneri') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Bölüm 2: Finansal --}}
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 text-sm">2</div>
                                    Tahmini Finansal Etki (Opsiyonel)
                                </h3>
                                
                                <div class="pl-11">
                                    <div class="bg-green-50/50 rounded-xl p-6 border border-green-100">
                                        <div class="mb-6">
                                            <label for="para_birimi" class="block text-sm font-bold text-gray-700 mb-2">Para Birimi</label>
                                            <select name="para_birimi" x-model="currency" id="para_birimi" class="rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 w-full md:w-1/3 shadow-sm bg-white">
                                                @foreach($paraBirimleri as $birim)
                                                    <option value="{{ $birim }}">{{ $birim }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="oneren_kazanc_miktar" class="block text-sm font-bold text-gray-700 mb-2">Yıllık Tahmini Kazanç</label>
                                                <div class="relative rounded-md shadow-sm">
                                                    <input type="number" step="0.01" name="oneren_kazanc_miktar" id="oneren_kazanc_miktar" value="{{ old('oneren_kazanc_miktar') }}" 
                                                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 pr-12" 
                                                        placeholder="0.00" {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm font-bold" x-text="currency"></span>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">Bu iyileştirme ile sağlanacak tasarruf veya gelir.</p>
                                            </div>
                                            
                                            <div>
                                                <label for="oneren_butce_miktar" class="block text-sm font-bold text-gray-700 mb-2">Tahmini Bütçe (Maliyet)</label>
                                                <div class="relative rounded-md shadow-sm">
                                                    <input type="number" step="0.01" name="oneren_butce_miktar" id="oneren_butce_miktar" value="{{ old('oneren_butce_miktar') }}" 
                                                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 pr-12" 
                                                        placeholder="0.00" {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm font-bold" x-text="currency"></span>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">Bu işin yapılması için gereken harcama.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Bölüm 3: Dosya Yükleme (MODERN) --}}
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 text-sm">3</div>
                                        Görsel Yükle (Opsiyonel)
                                    </h3>
                                    <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full border border-indigo-200" x-show="files.length > 0">
                                        <span x-text="files.length"></span> / 5 Resim
                                    </span>
                                </div>

                                <div class="pl-11">
                                    {{-- Hata Mesajı --}}
                                    <div x-show="errorMessage" x-cloak class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold border border-red-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        <span x-text="errorMessage"></span>
                                    </div>

                                    {{-- Önizleme --}}
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-4" x-show="files.length > 0" x-cloak>
                                        <template x-for="(file, index) in files" :key="index">
                                            <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-gray-50">
                                                <img :src="file.preview" class="w-full h-full object-cover">
                                                <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md transition-colors">&times;</button>
                                                <div class="absolute bottom-0 inset-x-0 bg-white/90 backdrop-blur-sm text-[10px] text-center py-1 font-semibold border-t border-gray-100" x-text="formatSize(file.fileObject.size)"></div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Yükleme Kutusu --}}
                                    <div x-show="files.length < 5">
                                        <label class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-400 transition-all bg-gray-50">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                <p class="text-sm text-gray-500 font-medium">Resim yüklemek için <span class="text-indigo-600 font-bold">tıklayın</span> veya sürükleyin</p>
                                                <p class="text-xs text-gray-400 mt-1">PNG, JPG (Max 4MB)</p>
                                            </div>
                                            <input type="file" x-ref="fileInput" name="resimler[]" multiple accept="image/*" @change="handleFileSelect" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Aksiyon Butonları --}}
                            <div class="flex items-center justify-end gap-4 pt-4">
                                <a href="{{ route('iaa.index') }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm transition-colors">
                                    İptal
                                </a>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                                    Öneriyi Kaydet
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>