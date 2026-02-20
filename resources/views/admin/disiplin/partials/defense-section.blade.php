<div class="mt-6">
    {{-- HATA MESAJLARI --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
            <p class="font-bold">İşlem Başarısız:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- BAŞARI MESAJI --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- DURUM A: Savunma Bekleniyor (FORM) --}}
    @if($case->durum == 'Savunma Bekleniyor')
        @if(Auth::user()->hasRole(['Yonetim', 'Yönetim']))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 shadow-sm text-center">
                <div class="inline-block p-3 bg-white rounded-full text-yellow-600 shadow-sm mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-yellow-800">Personel Savunması Bekleniyor</h3>
                <p class="text-yellow-700 mt-2 italic">Bu dosya için henüz savunma girilmemiştir. Personelin savunma yapması bekleniyor.</p>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-bold text-yellow-800 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Personel Savunması Girişi
                </h3>
                <p class="text-sm text-yellow-700 mb-4">Lütfen savunmanızı detaylı bir şekilde yazınız. Varsa kanıt dosyalarınızı ekleyiniz.</p>

                <form action="{{ route('disiplin.defense.store', $case->id) }}" method="POST" enctype="multipart/form-data"
                    x-data="{ 
                        files: [],
                        handleFileSelect(event) {
                            const fileList = event.target.files;
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            for (let i = 0; i < fileList.length; i++) { dt.items.add(fileList[i]); this.files.push(fileList[i]); }
                            this.$refs.fileInput.files = dt.files;
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            this.$refs.fileInput.files = dt.files;
                        },
                        formatSize(size) {
                            if(size > 1024*1024) return (size/(1024*1024)).toFixed(2) + ' MB';
                            return (size/1024).toFixed(2) + ' KB';
                        }
                    }">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Savunma Metni <span class="text-red-500">*</span></label>
                        <textarea name="savunma_aciklamasi" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Olayla ilgili açıklamanızı buraya yazınız..." required>{{ old('savunma_aciklamasi') }}</textarea>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Savunma Ekleri (Resim, PDF, Word)</label>
                        <div class="border-2 border-dashed border-yellow-400 bg-white rounded-lg p-6 hover:bg-yellow-50 transition relative">
                            <input x-ref="fileInput" type="file" name="savunma_dosyalari[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="handleFileSelect">
                            <div class="text-center" x-show="files.length === 0">
                                <svg class="mx-auto h-10 w-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <p class="mt-1 text-sm text-gray-600"><span class="font-medium text-yellow-600 hover:text-yellow-500">Dosya seçin</span> veya sürükleyip bırakın</p>
                                <p class="text-xs text-gray-400 mt-1">Sadece Resim, PDF, Word ve Excel (Max 20MB)</p>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4" x-show="files.length > 0">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="relative group border border-yellow-200 bg-yellow-50 p-2 rounded flex items-center gap-3 z-20">
                                        <div class="flex-shrink-0">
                                            <template x-if="file.type.startsWith('image/')">
                                                <img :src="URL.createObjectURL(file)" class="h-10 w-10 object-cover rounded">
                                            </template>
                                            <template x-if="!file.type.startsWith('image/')">
                                                <svg class="h-10 w-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18h12V6l-4-4H4v16z"/></svg>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 truncate" x-text="file.name"></p>
                                            <p class="text-[10px] text-gray-500" x-text="formatSize(file.size)"></p>
                                        </div>
                                        <button type="button" @click.prevent="removeFile(index)" class="text-red-500 hover:text-red-700 font-bold p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-yellow-700 shadow transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Savunmayı Kaydet ve Gönder
                        </button>
                    </div>
                </form>
            </div>
        @endif

    {{-- DURUM B: Savunma Verilmiş --}}
    @elseif($case->savunma_tarihi)
        <div class="bg-white border-t-4 border-blue-500 rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start border-b pb-4 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Personel Savunması</h3>
                    <p class="text-xs text-gray-500">Tarih: {{ $case->savunma_tarihi->format('d.m.Y H:i') }}</p>
                </div>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">Teslim Edildi</span>
            </div>
            
            <div class="bg-gray-50 p-4 rounded text-sm text-gray-800 italic border border-gray-200 mb-4">
                "{{ $case->savunma_aciklamasi }}"
            </div>

            @if(!empty($case->savunma_dosyalari))
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 border-b pb-1">Savunma Ekleri:</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($case->savunma_dosyalari as $dosya)
                        @php
                            $url = asset('storage/'.$dosya);
                            $ext = strtolower(pathinfo($dosya, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        <a href="{{ $url }}" target="_blank" class="group relative border rounded-lg overflow-hidden bg-white hover:shadow-md transition block">
                            <div class="h-24 w-full flex items-center justify-center bg-gray-50 overflow-hidden relative">
                                @if($isImage)
                                    <img src="{{ $url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="text-center">
                                        <svg class="w-8 h-8 text-gray-400 group-hover:text-indigo-500 transition mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 flex items-center justify-center transition duration-300">
                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 drop-shadow-md transform scale-75 group-hover:scale-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                            </div>
                            <div class="p-2 border-t bg-white">
                                <p class="text-[10px] text-gray-500 truncate font-medium" title="{{ basename($dosya) }}">{{ basename($dosya) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>