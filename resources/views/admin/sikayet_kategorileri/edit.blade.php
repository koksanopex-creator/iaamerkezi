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

                                {{-- === YENİ EKLENEN: BÖLÜM SEÇİMİ === --}}
                            <div class="group mt-6">
                                <label for="bolum_id" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    İlgili Bölüm
                                    <span class="ml-2 text-xs text-gray-500 font-normal">(Opsiyonel)</span>
                                </label>
                                <div class="relative">
                                    <select name="bolum_id" id="bolum_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white">
                                        <option value="">Bölüm Seçilmedi</option>
                                        @foreach($bolumler as $bolum)
                                            <option value="{{ $bolum->id }}" {{ old('bolum_id', $sikayetKategori->bolum_id ?? '') == $bolum->id ? 'selected' : '' }}>
                                                {{ $bolum->ad }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Bu kategoride şikayet gelirse, seçilen bölümün liderine de bildirim gider.</p>
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
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 h-full flex flex-col">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Alt Kategoriler</h3>
                <p class="text-xs text-gray-500">Bu kategori seçildiğinde görünecek seçenekler</p>
            </div>
        </div>
        <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full">{{ $sikayetKategori->altKategoriler->count() }} Adet</span>
    </div>

    {{-- YENİ EKLEME FORMU --}}
    <form action="{{ route('admin.sikayet-kategorileri.alt-kategori.store', $sikayetKategori) }}" method="POST" class="mb-4">
        @csrf
        <div class="flex gap-2">
            <input type="text" name="ad" placeholder="Yeni alt kategori adı (Örn: Leke)" class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                EKLE
            </button>
        </div>
    </form>

    {{-- ARAMA KUTUSU (Javascript ile çalışır) --}}
    <div class="relative mb-2">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input type="text" id="altKategoriSearch" placeholder="Listede ara..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out">
    </div>

    {{-- LİSTE ALANI (Scrollable) --}}
    {{-- max-h-[500px] değeri listenin maksimum yüksekliğini belirler, taşarsa scroll çıkar --}}
    <div class="flex-1 overflow-y-auto pr-2 max-h-[500px] space-y-2 custom-scrollbar" id="altKategoriList">
    {{-- sortBy('ad') ekleyerek isme göre A-Z sıralıyoruz --}}
    @forelse($sikayetKategori->altKategoriler->sortBy('ad', SORT_NATURAL|SORT_FLAG_CASE) as $altKategori)
            <div class="group flex items-center justify-between p-3 bg-gray-50 hover:bg-indigo-50 rounded-lg border border-gray-100 hover:border-indigo-200 transition-all search-item">
                
                {{-- Görüntüleme Modu --}}
                <span class="text-sm text-gray-700 font-medium flex-1 search-text">{{ $altKategori->ad }}</span>
                
                {{-- Düzenleme Formu (Gizli) --}}
                <form action="{{ route('admin.sikayet-alt-kategori.update', $altKategori) }}" method="POST" class="hidden flex-1 mr-2 edit-form">
                    @csrf
                    @method('PUT')
                    <div class="flex gap-2">
                        <input type="text" name="ad" value="{{ $altKategori->ad }}" class="w-full rounded border-gray-300 text-sm py-1 px-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="text-green-600 hover:text-green-800 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></button>
                        <button type="button" onclick="toggleEdit(this)" class="text-gray-500 hover:text-gray-700 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                </form>

                {{-- İşlem Butonları --}}
                <div class="flex items-center gap-2 action-buttons">
                    <button type="button" onclick="toggleEdit(this)" class="text-blue-400 hover:text-blue-600 transition-colors p-1 rounded-md hover:bg-blue-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    
                    <form action="{{ route('admin.sikayet-alt-kategori.destroy', $altKategori) }}" method="POST" onsubmit="return confirm('Bu alt kategoriyi silmek istediğinize emin misiniz?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors p-1 rounded-md hover:bg-red-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                <p>Henüz alt kategori eklenmemiş.</p>
            </div>
        @endforelse
        
        {{-- Arama sonucu bulunamadı mesajı (JS ile görünür olur) --}}
        <div id="noResult" class="hidden text-center py-4 text-gray-500 text-sm">
            Aradığınız kriterde bir alt kategori bulunamadı.
        </div>
    </div>
</div>

{{-- Gerekli Javascript ve CSS --}}
<script>
    // Arama Fonksiyonu
    document.getElementById('altKategoriSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let listItems = document.querySelectorAll('.search-item');
        let hasResult = false;

        listItems.forEach(function(item) {
            let text = item.querySelector('.search-text').textContent.toLowerCase();
            if (text.includes(filter)) {
                item.style.display = "";
                hasResult = true;
            } else {
                item.style.display = "none";
            }
        });

        // Sonuç yoksa mesaj göster
        document.getElementById('noResult').style.display = hasResult ? 'none' : 'block';
    });

    // Düzenleme Modunu Aç/Kapat
    function toggleEdit(btn) {
        const container = btn.closest('.search-item');
        const textSpan = container.querySelector('.search-text');
        const editForm = container.querySelector('.edit-form');
        const actionButtons = container.querySelector('.action-buttons');

        if (editForm.classList.contains('hidden')) {
            // Düzenleme modunu aç
            textSpan.classList.add('hidden');
            actionButtons.classList.add('hidden');
            editForm.classList.remove('hidden');
            editForm.querySelector('input').focus();
        } else {
            // Düzenleme modunu kapat
            textSpan.classList.remove('hidden');
            actionButtons.classList.remove('hidden');
            editForm.classList.add('hidden');
        }
    }
</script>

<style>
    /* İnce Scrollbar Tasarımı */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
            </div>

        </div>
    </div>
</x-app-layout>