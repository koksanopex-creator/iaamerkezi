<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <h2 class="font-bold text-2xl text-gray-800">{{ __('Yeni Şikayet Kategorisi Ekle') }}</h2>
            </div>
            <a href="{{ route('admin.sikayet-kategorileri.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                &larr; Listeye Dön
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl">
                <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                
                <div class="p-8">
                    <div class="mb-8 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Kategori Bilgileri</h3>
                        <p class="text-sm text-gray-600">Yeni bir şikayet kategorisi oluşturun ve varsayılan çözüm takımını atayın.</p>
                    </div>

                    <form action="{{ route('admin.sikayet-kategorileri.store') }}" method="POST">
                        @csrf
                        <div class="space-y-8">
                            
                            {{-- Kategori Adı --}}
                            <div class="group">
                                <label for="ad" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                    Kategori Adı
                                    <span class="ml-1 text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        name="ad" 
                                        id="ad" 
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out pl-4 pr-4 py-3 text-gray-900" 
                                        placeholder="Örn: Teknik Destek, Faturalama, Genel Sorular"
                                        required>
                                </div>
                                <x-input-error :messages="$errors->get('ad')" class="mt-2" />
                            </div>

                            {{-- Varsayılan Takım --}}
                            <div class="group">
                                <label for="varsayilan_takim_id" class="flex items-center font-semibold text-sm text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                    </svg>
                                    Varsayılan Çözüm Takımı
                                    <span class="ml-2 text-xs text-gray-500 font-normal">(Opsiyonel)</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        name="varsayilan_takim_id" 
                                        id="varsayilan_takim_id" 
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out pl-4 pr-10 py-3 text-gray-900 appearance-none bg-white">
                                        <option value="">Takım Seçilmedi</option>
                                        @foreach($takimlar as $takim)
                                            <option value="{{ $takim->id }}">{{ $takim->ad }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Bu kategorideki şikayetler otomatik olarak seçilen takıma atanacaktır.</p>
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
                                            {{-- Checkbox --}}
                                            <input type="hidden" name="diger_secenegi_goster" value="0">
                                            <input 
                                                type="checkbox" 
                                                name="diger_secenegi_goster" 
                                                id="diger_secenegi_goster" 
                                                value="1"
                                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
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
                                        <label for="diger_aciklama_basligi" class="block text-sm font-medium text-gray-700">'Diğer' Açıklama Başlığı</label>
                                        <input 
                                            type="text" 
                                            name="diger_aciklama_basligi" 
                                            id="diger_aciklama_basligi"
                                            placeholder="Örn: Diğer (Lütfen açıklayın)"
                                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                                        />
                                        <x-input-error :messages="$errors->get('diger_aciklama_basligi')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                <a href="{{ route('admin.sikayet-kategorileri.index') }}" class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    İptal
                                </a>
                                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Kategori Kaydet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900 mb-1">Bilgilendirme</h4>
                        <p class="text-sm text-blue-800">Kategoriler, şikayetlerin daha iyi organize edilmesini sağlar. Her kategori için varsayılan bir takım atayabilir ve şikayetleri otomatik olarak ilgili ekibe yönlendirebilirsiniz.</p>
                        <p class="text-sm text-blue-800 mt-1">Kategori oluşturulduktan sonra düzenleme sayfasından alt kategorileri ekleyebilirsiniz.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>