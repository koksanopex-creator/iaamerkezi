<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Yeni İyileştirmeye Açık Alan Öner') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Kullanıcının bölümü atanmamışsa uyarı --}}
                    @if (!Auth::user()->bolum_id)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">İAA önerebilmek için bir bölüme atanmış olmalısınız. Lütfen yöneticinizle iletişime geçin.</span>
                        </div>
                    @endif

                    {{-- Hata Mesajları --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- DÜZENLEME: Forma Alpine.js eklenerek para birimi yönetimi sağlandı --}}
                    <form x-data="{ currency: 'TL' }" action="{{ route('iaa.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Başlık --}}
                        <div class="mb-4">
                            <label for="baslik" class="block text-gray-700 text-sm font-bold mb-2">Konu / Başlık:</label>
                            <input type="text" name="baslik" id="baslik" value="{{ old('baslik') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                        </div>
                        
                        {{-- Mevcut Durum --}}
                        <div class="mb-4">
                            <label for="mevcut_durum" class="block text-gray-700 text-sm font-bold mb-2">Mevcut Durum / Problem Tanımı:</label>
                            <textarea name="mevcut_durum" id="mevcut_durum" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>{{ old('mevcut_durum') }}</textarea>
                        </div>

                        {{-- Öneri --}}
                        <div class="mb-4">
                            <label for="oneri" class="block text-gray-700 text-sm font-bold mb-2">İyileştirme Önerisi:</label>
                            <textarea name="oneri" id="oneri" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>{{ old('oneri') }}</textarea>
                        </div>

                        {{-- ================= YENİ FİNANSAL BİLGİLER BÖLÜMÜ BAŞLANGICI ================= --}}
                        <hr class="my-6 border-t">
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Tahmini Finansal Etki (Opsiyonel)</h3>
                        <p class="text-sm text-gray-500 mb-4">Eğer bir öngörünüz varsa, lütfen tahmini kazanç ve bütçe bilgilerini giriniz. Bu, yöneticinin önerinizi daha iyi değerlendirmesine yardımcı olacaktır.</p>
                        
                        <div class="mb-6">
                            <label for="para_birimi" class="block text-gray-700 text-sm font-bold mb-2">Kullanılacak Para Birimi</label>
                                <select name="para_birimi" x-model="currency" id="para_birimi" class="shadow border rounded w-full md:w-1/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @foreach($paraBirimleri as $birim)
                                        <option value="{{ $birim }}">{{ $birim }}</option>
                                    @endforeach
                                </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Tahmini Kazanç --}}
                            <div>
                                <label for="oneren_kazanc_miktar" class="block text-gray-700 text-sm font-bold mb-2">Tahmini Yıllık Kazanç (<span x-text="currency"></span>)</label>
                                <input type="number" step="0.01" name="oneren_kazanc_miktar" id="oneren_kazanc_miktar" value="{{ old('oneren_kazanc_miktar') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                            </div>
                            
                            {{-- Tahmini Bütçe --}}
                            <div>
                                <label for="oneren_butce_miktar" class="block text-gray-700 text-sm font-bold mb-2">Tahmini Bütçe (<span x-text="currency"></span>)</label>
                                <input type="number" step="0.01" name="oneren_butce_miktar" id="oneren_butce_miktar" value="{{ old('oneren_butce_miktar') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <hr class="my-6 border-t">
                        {{-- ================= YENİ FİNANSAL BİLGİLER BÖLÜMÜ SONU ================= --}}

                        {{-- Resimler --}}
                        <div class="mb-4">
                            <label for="resimler" class="block text-gray-700 text-sm font-bold mb-2">Resimler (Opsiyonel, Birden Fazla Seçebilirsiniz):</label>
                                <input type="file" name="resimler[]" id="resimler" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" multiple accept="image/*" capture="environment" {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                        </div>
                        
                        {{-- Butonlar --}}
                        <div class="flex items-center justify-end">
                            <a href="{{ route('iaa.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-4">
                                İptal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" {{ !Auth::user()->bolum_id ? 'disabled' : '' }}>
                                Öneriyi Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>