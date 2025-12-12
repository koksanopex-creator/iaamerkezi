<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tutanak Düzenle') }} <span class="text-gray-500 text-sm">#{{ $case->id }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Hata Gösterimi --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <strong class="font-bold">Lütfen hataları düzeltin:</strong>
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                {{-- DÜZENLEME FORMU --}}
                <form action="{{ route('admin.disiplin.update', $case->id) }}" method="POST" enctype="multipart/form-data"
                    x-data="{ 
                        selectedCategory: '{{ $case->behavior->category_id }}', 
                        behaviors: {{ json_encode($categories) }},
                        files: [], // Yeni dosyalar
                        serverFiles: {{ json_encode($case->kanit_dosyalari ?? []) }}, // Eski dosyalar
                        deletedServerFiles: [], // Silinecek eski dosyalar

                        // Yeni Dosya Ekleme
                        handleFileSelect(event) {
                            const fileList = event.target.files;
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            for (let i = 0; i < fileList.length; i++) {
                                if (!this.files.some(f => f.name === fileList[i].name)) {
                                    dt.items.add(fileList[i]);
                                    this.files.push(fileList[i]);
                                }
                            }
                            this.$refs.fileInput.files = dt.files;
                        },

                        // Yeni Dosyayı Silme
                        removeFile(index) {
                            this.files.splice(index, 1);
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            this.$refs.fileInput.files = dt.files;
                        },

                        // Eski (Server) Dosyasını Silme (Görünümden kaldır ve listeye ekle)
                        removeServerFile(index, path) {
                            this.deletedServerFiles.push(path); // Backend'e gidecek
                            this.serverFiles.splice(index, 1); // Ekrandan silinecek
                        },

                        formatSize(size) {
                            if (size > 1024 * 1024) return (size / (1024 * 1024)).toFixed(2) + ' MB';
                            return (size / 1024).toFixed(2) + ' KB';
                        }
                    }">
                    @csrf
                    @method('PUT')

                    {{-- 1. BÖLÜM: PERSONEL VE TARİH --}}
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                        <h3 class="text-sm font-bold text-gray-700 uppercase mb-4 border-b pb-2">Olay Bilgileri</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Personel --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İlgili Personel</label>
                                <select disabled class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm text-gray-500 cursor-not-allowed">
                                    <option>{{ $case->user->name }}</option>
                                </select>
                                <input type="hidden" name="user_id" value="{{ $case->user_id }}">
                            </div>

                            {{-- Tarih --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Olay Tarihi ve Saati <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="olay_tarihi" value="{{ $case->olay_tarihi->format('Y-m-d\TH:i') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                        </div>
                    </div>

                    {{-- 2. BÖLÜM: SUÇ TANIMI --}}
                    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6 shadow-sm">
                        <h3 class="text-sm font-bold text-indigo-700 uppercase mb-4 border-b pb-2">İhlal / Suç Seçimi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Suç Kategorisi <span class="text-red-500">*</span></label>
                                <select x-model="selectedCategory" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Kategori Seçin --</option>
                                    <template x-for="cat in behaviors" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.ad" :selected="cat.id == selectedCategory"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Suç --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İhlal Edilen Madde <span class="text-red-500">*</span></label>
                                <select name="behavior_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Seçiniz --</option>
                                    <template x-for="cat in behaviors">
                                        <template x-if="cat.id == selectedCategory">
                                            <template x-for="b in cat.behaviors">
                                                <option :value="b.id" x-text="b.tanim" :selected="b.id == {{ $case->behavior_id }}"></option>
                                            </template>
                                        </template>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 3. BÖLÜM: MATRİS --}}
                    <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-100 mb-6">
                        <h3 class="text-sm font-bold text-indigo-900 uppercase mb-4">Ciddiyet Değerlendirmesi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Etki --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Suçun Şiddeti (Etkisi) <span class="text-red-500">*</span></label>
                                <select name="impact_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="">-- Seçiniz --</option>
                                    @foreach($impacts as $i)
                                        <option value="{{ $i->id }}" {{ $case->impact_id == $i->id ? 'selected' : '' }}>
                                            {{ $i->tanim }} (x{{ $i->puan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Kapsam --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Etki Kapsamı <span class="text-red-500">*</span></label>
                                <select name="scope_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="">-- Seçiniz --</option>
                                    @foreach($scopes as $s)
                                        <option value="{{ $s->id }}" {{ $case->scope_id == $s->id ? 'selected' : '' }}>
                                            {{ $s->tanim }} (x{{ $s->puan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 4. BÖLÜM: AÇIKLAMA --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Olayın Detaylı Açıklaması <span class="text-red-500">*</span></label>
                        <textarea name="olay_aciklamasi" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ $case->olay_aciklamasi }}</textarea>
                    </div>

                    {{-- 5. BÖLÜM: KANIT DOSYALARI --}}
                    <div class="mb-6 bg-white p-4 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-bold text-gray-700 mb-4">Kanıt Dosyaları Yönetimi</h3>

                        {{-- Silinecek dosyalar için gizli inputlar --}}
                        <template x-for="path in deletedServerFiles">
                            <input type="hidden" name="silinecek_dosyalar[]" :value="path">
                        </template>

                        {{-- Dropzone Alanı --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:bg-gray-50 transition relative">
                            <input x-ref="fileInput" type="file" name="kanit_dosyalari[]" multiple 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                @change="handleFileSelect">

                            {{-- Boş Durum --}}
                            <div class="text-center py-4" x-show="files.length === 0 && serverFiles.length === 0">
                                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <p class="text-sm text-gray-500 mt-2">Dosya yüklemek için tıklayın veya sürükleyin</p>
                            </div>

                            {{-- Grid Görünüm (Hem Eski Hem Yeni) --}}
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2" x-show="files.length > 0 || serverFiles.length > 0">
                                
                                {{-- 1. MEVCUT DOSYALAR (SERVER) --}}
                                <template x-for="(path, index) in serverFiles" :key="'server-'+index">
                                    <div class="relative group border border-blue-200 bg-blue-50 rounded-lg overflow-hidden z-20">
                                        <div class="h-20 flex items-center justify-center">
                                            <a :href="'/storage/'+path" target="_blank" class="text-blue-500 font-bold text-xs hover:underline">Görüntüle</a>
                                        </div>
                                        <div class="bg-blue-100 p-1 text-[9px] text-blue-800 text-center truncate">
                                            Kayıtlı Dosya
                                        </div>
                                        {{-- Eski Dosyayı Sil --}}
                                        <button type="button" @click.prevent="removeServerFile(index, path)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- 2. YENİ DOSYALAR (PREVIEW) --}}
                                <template x-for="(file, index) in files" :key="'new-'+index">
                                    <div class="relative group border border-green-200 bg-green-50 rounded-lg overflow-hidden z-20">
                                        <div class="h-20 flex items-center justify-center overflow-hidden">
                                            <template x-if="file.type.startsWith('image/')">
                                                <img :src="URL.createObjectURL(file)" class="h-full w-full object-cover opacity-80">
                                            </template>
                                            <template x-if="!file.type.startsWith('image/')">
                                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </template>
                                        </div>
                                        <div class="bg-green-100 p-1 text-[9px] text-green-800 text-center truncate">
                                            Yeni: <span x-text="file.name"></span>
                                        </div>
                                        {{-- Yeni Dosyayı Sil --}}
                                        <button type="button" @click.prevent="removeFile(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- + Ekle Butonu --}}
                                <div class="border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-100 min-h-[80px]" @click="$refs.fileInput.click()">
                                    <span class="text-2xl text-gray-400">+</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BUTONLAR --}}
                    <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.disiplin.show', $case->id) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md font-semibold hover:bg-gray-50 transition">
                            İptal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold hover:bg-blue-700 shadow-lg transform hover:scale-105 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Değişiklikleri Kaydet
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>