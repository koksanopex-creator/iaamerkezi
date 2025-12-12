<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Yeni Disiplin Tutanağı Oluştur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- GENEL SİSTEM HATALARI --}}
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <p class="font-bold">Bir Hata Oluştu:</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- VALIDASYON HATALARI (Liste Halinde) --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <strong class="font-bold block mb-1">Kayıt Başarısız! Lütfen hataları düzeltin:</strong>
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                {{-- TEK VE MERKEZİ X-DATA YAPISI --}}
                <form action="{{ route('admin.disiplin.store') }}" method="POST" enctype="multipart/form-data"
                    x-data="{ 
                        selectedCategory: '', 
                        behaviors: {{ json_encode($categories) }},
                        files: [],
                        
                        // Dosya Seçildiğinde Tetiklenir
                        handleFileSelect(event) {
                            const fileList = event.target.files;
                            // Mevcut dosyalara yenilerini ekle (DataTransfer ile input'u güncellemek için)
                            const dt = new DataTransfer();
                            
                            // Önceki dosyaları ekle
                            this.files.forEach(file => dt.items.add(file));
                            
                            // Yeni dosyaları ekle
                            for (let i = 0; i < fileList.length; i++) {
                                const file = fileList[i];
                                // Aynı dosya tekrar eklenmesin kontrolü (opsiyonel)
                                if (!this.files.some(f => f.name === file.name && f.size === file.size)) {
                                    dt.items.add(file);
                                    this.files.push(file);
                                }
                            }
                            
                            // Input değerini güncelle
                            this.$refs.fileInput.files = dt.files;
                        },

                        // Dosyayı Listeden Kaldır
                        removeFile(index) {
                            this.files.splice(index, 1);
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            this.$refs.fileInput.files = dt.files;
                        },

                        // Dosya Boyutunu Formatla
                        formatSize(size) {
                            if (size > 1024 * 1024) return (size / (1024 * 1024)).toFixed(2) + ' MB';
                            return (size / 1024).toFixed(2) + ' KB';
                        }
                    }">
                    @csrf

                    {{-- 1. BÖLÜM: PERSONEL VE TARİH --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h3 class="text-sm font-bold text-gray-700 uppercase mb-4 border-b pb-2">Olay Bilgileri</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İlgili Personel <span class="text-red-500">*</span></label>
                                <select name="user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Seçiniz --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->bolum->ad ?? 'Bölümsüz' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Olay Tarihi ve Saati <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="olay_tarihi" value="{{ old('olay_tarihi') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                        </div>
                    </div>

                    {{-- 2. BÖLÜM: SUÇ TANIMI --}}
                    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6 shadow-sm">
                        <h3 class="text-sm font-bold text-indigo-700 uppercase mb-4 border-b pb-2">İhlal / Suç Seçimi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Suç Kategorisi <span class="text-red-500">*</span></label>
                                <select x-model="selectedCategory" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Önce Kategori Seçin --</option>
                                    <template x-for="cat in behaviors" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.ad"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İhlal Edilen Madde <span class="text-red-500">*</span></label>
                                <select name="behavior_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :disabled="!selectedCategory" required>
                                    <option value="">-- Seçiniz --</option>
                                    <template x-for="cat in behaviors">
                                        <template x-if="cat.id == selectedCategory">
                                            <template x-for="b in cat.behaviors">
                                                <option :value="b.id" x-text="b.tanim"></option>
                                            </template>
                                        </template>
                                    </template>
                                </select>
                                <p x-show="!selectedCategory" class="text-xs text-gray-400 mt-1 italic">Lütfen önce sol taraftan kategori seçiniz.</p>
                            </div>
                        </div>
                    </div>

                    {{-- 3. BÖLÜM: MATRİS --}}
                    <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-100 mb-6">
                        <h3 class="text-sm font-bold text-indigo-900 uppercase mb-4">Ciddiyet Değerlendirmesi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Suçun Şiddeti (Etkisi) <span class="text-red-500">*</span></label>
                                <select name="impact_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="">-- Seçiniz --</option>
                                    @foreach($impacts as $i)
                                        <option value="{{ $i->id }}" {{ old('impact_id') == $i->id ? 'selected' : '' }}>{{ $i->tanim }} (x{{ $i->puan }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Etki Kapsamı <span class="text-red-500">*</span></label>
                                <select name="scope_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="">-- Seçiniz --</option>
                                    @foreach($scopes as $s)
                                        <option value="{{ $s->id }}" {{ old('scope_id') == $s->id ? 'selected' : '' }}>{{ $s->tanim }} (x{{ $s->puan }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 4. BÖLÜM: AÇIKLAMA --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Olayın Detaylı Açıklaması <span class="text-red-500">*</span></label>
                        <textarea name="olay_aciklamasi" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('olay_aciklamasi') }}</textarea>
                    </div>

                    {{-- 5. BÖLÜM: DOSYA YÜKLEME (ÖNİZLEMELİ & SİLİNEBİLİR) --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kanıt Dosyaları (Resim, PDF, Video)</label>
                        
                        {{-- Dropzone Alanı --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:bg-gray-50 transition relative bg-white">
                            
                            <input x-ref="fileInput" 
                                type="file" 
                                name="kanit_dosyalari[]" 
                                multiple 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                @change="handleFileSelect">

                            <div class="text-center" x-show="files.length === 0">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-1 text-sm text-gray-600">
                                    <span class="font-medium text-indigo-600 hover:text-indigo-500">Dosya seçin</span> 
                                    veya sürükleyip bırakın
                                </p>
                                <p class="text-xs text-gray-500">PNG, JPG, PDF, MP4 (Max 20MB)</p>
                            </div>

                            {{-- ÖNİZLEME LİSTESİ (Dosya seçilince burası görünür) --}}
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2" x-show="files.length > 0">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="relative group border rounded-lg overflow-hidden bg-gray-50 shadow-sm z-20"> {{-- z-20 önemli --}}
                                        
                                        {{-- Resimse Önizle, Değilse İkon --}}
                                        <div class="h-24 w-full flex items-center justify-center bg-gray-200 overflow-hidden">
                                            <template x-if="file.type.startsWith('image/')">
                                                <img :src="URL.createObjectURL(file)" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!file.type.startsWith('image/')">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </template>
                                        </div>

                                        {{-- Dosya Bilgisi --}}
                                        <div class="p-2">
                                            <p class="text-[10px] text-gray-500 truncate" x-text="file.name"></p>
                                            <p class="text-[9px] text-gray-400" x-text="formatSize(file.size)"></p>
                                        </div>

                                        {{-- SİL BUTONU (ÇARPI) --}}
                                        <button type="button" @click.prevent="removeFile(index)" 
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition shadow-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                                
                                {{-- + Ekle Butonu (Mini) --}}
                                <div class="border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:bg-gray-50 min-h-[100px]"
                                    @click="$refs.fileInput.click()">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span class="text-xs text-gray-500 mt-1">Daha Fazla Ekle</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BUTONLAR --}}
                    <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.disiplin.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md font-semibold hover:bg-gray-50 transition">İptal</a>
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md font-bold hover:bg-red-700 shadow-lg transform hover:scale-105 transition flex items-center gap-2">
                            Tutanak Oluştur ve Gönder
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>