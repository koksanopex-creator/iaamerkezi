<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Kategoriyi Düzenle: <span class="text-indigo-600">{{ $sikayetKategori->ad }}</span>
            </h2>
            <a href="{{ route('admin.sikayet-kategorileri.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                &larr; Listeye Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            {{-- Başarı Mesajı --}}
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Başarılı!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- SOL KOLON: Ana Kategori Düzenleme Formu --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100 h-fit">
                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Kategori Bilgileri</h3>
                        </div>

                        <form action="{{ route('admin.sikayet-kategorileri.update', $sikayetKategori) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="space-y-6">
                                
                                {{-- Kategori Adı --}}
                                <div class="group">
                                    <x-input-label for="ad" :value="__('Kategori Adı')" class="text-sm font-medium text-gray-700" />
                                    <x-text-input 
                                        id="ad" 
                                        name="ad" 
                                        type="text" 
                                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all duration-200" 
                                        :value="old('ad', $sikayetKategori->ad)" 
                                        required 
                                    />
                                    <x-input-error :messages="$errors->get('ad')" class="mt-2" />
                                </div>

                                {{-- Varsayılan Takım --}}
                                <div class="group">
                                    <x-input-label for="varsayilan_takim_id" :value="__('Varsayılan Çözüm Takımı')" class="text-sm font-medium text-gray-700" />
                                    <select 
                                        name="varsayilan_takim_id" 
                                        id="varsayilan_takim_id" 
                                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                                    >
                                        <option value="">Takım Seçilmedi</option>
                                        @foreach($takimlar as $takim)
                                            <option value="{{ $takim->id }}" {{ old('varsayilan_takim_id', $sikayetKategori->varsayilan_takim_id) == $takim->id ? 'selected' : '' }}>
                                                {{ $takim->ad }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1.5 text-xs text-gray-500">Opsiyonel: Bu kategori için otomatik atanacak takım</p>
                                    <x-input-error :messages="$errors->get('varsayilan_takim_id')" class="mt-2" />
                                </div>

                                {{-- Diğer Seçeneği Ayarları --}}
                                <div class="mt-6 p-6 bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl shadow-sm">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="p-1.5 bg-white rounded-lg shadow-sm">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-base font-semibold text-gray-800">"Diğer" Seçeneği Ayarları</h4>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                {{-- Checkbox value gönderimi için hidden input --}}
                                                <input type="hidden" name="diger_secenegi_goster" value="0">
                                                <input 
                                                    type="checkbox" 
                                                    name="diger_secenegi_goster" 
                                                    id="diger_secenegi_goster" 
                                                    value="1"
                                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                                                    @if(old('diger_secenegi_goster', $sikayetKategori->diger_secenegi_goster)) checked @endif
                                                >
                                            </div>
                                            <div class="ml-3">
                                                <label for="diger_secenegi_goster" class="text-sm font-medium text-gray-700 cursor-pointer">
                                                    Formlarda "Diğer" seçeneğini göster
                                                </label>
                                                <p class="text-xs text-gray-500 mt-0.5">Kullanıcılar listede olmayan seçenekleri metin olarak girebilir</p>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <x-input-label for="diger_aciklama_basligi" :value="__('\'Diğer\' Açıklama Başlığı')" class="text-sm font-medium text-gray-700" />
                                            <x-text-input 
                                                type="text" 
                                                name="diger_aciklama_basligi" 
                                                id="diger_aciklama_basligi"
                                                value="{{ old('diger_aciklama_basligi', $sikayetKategori->diger_aciklama_basligi) }}"
                                                placeholder="Örn: Diğer (Lütfen açıklayın)"
                                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                                            />
                                            <x-input-error :messages="$errors->get('diger_aciklama_basligi')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                {{-- Kaydet Butonu --}}
                                <div class="flex justify-end pt-4 border-t border-gray-200">
                                    <x-primary-button class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Değişiklikleri Kaydet
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                {{-- SAĞ KOLON: Alt Kategoriler Yönetimi --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Alt Kategoriler</h3>
                                <p class="text-sm text-gray-500 mt-0.5">Bu kategori seçildiğinde görünecek seçenekler</p>
                            </div>
                        </div>
                        
                        {{-- Yeni Alt Kategori Ekleme Formu --}}
                        <form action="{{ route('admin.sikayet-kategorileri.alt-kategori.store', $sikayetKategori) }}" method="POST" class="mt-6 p-5 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                            @csrf
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <label for="alt_kategori_ad" class="sr-only">Alt Kategori Adı</label>
                                    <x-text-input 
                                        type="text" 
                                        name="ad" 
                                        id="alt_kategori_ad" 
                                        placeholder="Yeni alt kategori adı (örn: Leke, Bombe, Renk Hatası)" 
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 transition-all duration-200" 
                                        required 
                                    />
                                </div>
                                <x-primary-button class="px-6 py-2.5 !bg-green-600 hover:!bg-green-700 focus:ring-4 focus:ring-green-200 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 whitespace-nowrap">
                                    <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Ekle
                                </x-primary-button>
                            </div>
                            {{-- Hata mesajı 'ad' alanı için --}}
                            @error('ad')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </form>

                        {{-- Alt Kategoriler Listesi (GÜNCELLENMİŞ HALİ) --}}
                        <div class="mt-6">
                            <ul role="list" class="space-y-2">
                                @forelse($sikayetKategori->altKategoriler as $altKategori)
                                    {{-- x-data ile bu satırın düzenleme modunda olup olmadığını takip ediyoruz --}}
                                    <li class="group p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all duration-200 border border-gray-200 hover:border-gray-300" 
                                        x-data="{ editing: false, newName: '{{ $altKategori->ad }}' }">
                                        
                                        {{-- GÖRÜNTÜLEME MODU (editing: false) --}}
                                        <div class="flex items-center justify-between" x-show="!editing">
                                            <div class="flex items-center gap-3">
                                                <div class="p-1.5 bg-white rounded-md shadow-sm">
                                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ $altKategori->ad }}</span>
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                {{-- Düzenle Butonu --}}
                                                <button @click="editing = true" type="button" class="text-blue-600 hover:text-blue-800 p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>

                                                {{-- Sil Butonu --}}
                                                <form action="{{ route('admin.sikayet-alt-kategori.destroy', $altKategori) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- DÜZENLEME MODU (editing: true) --}}
                                        <div class="flex items-center justify-between gap-2" x-show="editing" style="display: none;">
                                            <form action="{{ route('admin.sikayet-alt-kategori.update', $altKategori) }}" method="POST" class="flex-1 flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="ad" x-model="newName" class="flex-1 text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1">
                                                
                                                <button type="submit" class="text-green-600 hover:text-green-800 p-1" title="Kaydet">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>
                                            <button @click="editing = false; newName = '{{ $altKategori->ad }}'" type="button" class="text-gray-500 hover:text-gray-700 p-1" title="İptal">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                    </li>
                                @empty
                                    <li class="p-8 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-3">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium">Bu kategori için henüz alt kategori eklenmemiş</p>
                                        <p class="text-xs text-gray-400 mt-1">Yukarıdaki formu kullanarak yeni alt kategori ekleyebilirsiniz</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>