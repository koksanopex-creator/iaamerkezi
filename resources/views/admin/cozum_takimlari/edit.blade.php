<x-app-layout>
    <x-slot name="header">
         <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg shadow">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
             <h2 class="font-bold text-2xl text-gray-800">
                 Çözüm Takımını Düzenle: <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ $takim->ad }}</span>
             </h2>
         </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-indigo-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200/50">
                <div class="p-6 sm:p-8">
                    {{-- GÜNCELLEME FORMU --}}
                    {{-- === DÜZELTİLDİ: Doğru route parametresi 'cozumTakimi' === --}}
                    <form id="updateForm" action="{{ route('admin.cozum-takimlari.update', ['cozumTakimi' => $takim->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">

                            {{-- Takım Adı --}}
                            <div>
                                <label for="ad" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                     <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                     Takım Adı <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" name="ad" id="ad" value="{{ old('ad', $takim->ad) }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out py-3 px-4 placeholder-gray-400">
                                <x-input-error :messages="$errors->get('ad')" class="mt-2" />
                            </div>

                             {{-- Takım Lideri --}}
                            <div>
                                <label for="lider_user_id" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                     <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                     Takım Lideri <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select name="lider_user_id" id="lider_user_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out py-3 px-4 appearance-none bg-white">
                                    <option value="" disabled {{ !$takim->lider_user_id ? 'selected' : '' }}>-- Lider Seçin --</option>
                                    @forelse($liderler as $lider)
                                        <option value="{{ $lider->id }}" {{ old('lider_user_id', $takim->lider_user_id) == $lider->id ? 'selected' : '' }}>
                                            {{ $lider->name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Uygun lider bulunamadı.</option>
                                    @endforelse
                                </select>
                                <x-input-error :messages="$errors->get('lider_user_id')" class="mt-2" />
                                @if($liderler->isEmpty() && !$takim->lider)
                                 <p class="mt-2 text-xs text-orange-600">Lider olarak atanabilecek ("Müşteri Şikayeti Çözüm Lideri" rolüne sahip) kullanıcı bulunamadı. Lütfen önce kullanıcı yönetimi sayfasından rol ataması yapın.</p>
                                @elseif($liderler->isEmpty() && $takim->lider && !$takim->lider->hasRole('Müşteri Şikayeti Çözüm Lideri'))
                                 <p class="mt-2 text-xs text-orange-600">Mevcut liderin ("{{ $takim->lider->name }}") "Müşteri Şikayeti Çözüm Lideri" rolü bulunmuyor veya uygun başka lider yok. Lütfen rol ataması yapın veya farklı bir lider seçin.</p>
                                @endif
                            </div>

                             {{-- Takım Türü (Gizli Alan - Değiştirilemez) --}}
                            <input type="hidden" name="tur" value="{{ $takim->tur }}">


                             {{-- Form Butonları --}}
                            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                                 {{-- Silme Formu ve Butonu --}}
                                <div>
                                     {{-- === DÜZELTİLDİ: Doğru route parametresi 'cozumTakimi' === --}}
                                     <form id="deleteForm" action="{{ route('admin.cozum-takimlari.destroy', ['cozumTakimi' => $takim->id]) }}" method="POST" onsubmit="return confirm('Bu takımı silmek istediğinizden emin misiniz? Bu takıma atanmış şikayetler varsa silme işlemi başarısız olacaktır.');">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-5 border border-transparent rounded-lg shadow-sm transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                             Takımı Sil
                                         </button>
                                     </form>
                                </div>
                                 {{-- =================================== --}}

                                 <div class="flex items-center space-x-4">
                                     <a href="{{ route('admin.cozum-takimlari.index') }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2.5 px-5 border border-gray-300 rounded-lg shadow-sm transition-colors duration-150 ease-in-out">
                                         İptal
                                     </a>
                                     <button type="submit" form="updateForm" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md hover:from-indigo-700 hover:to-blue-700 transform hover:-translate-y-0.5 transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Değişiklikleri Kaydet
                                     </button>
                                 </div>
                            </div>
                        </div>
                    </form> {{-- Güncelleme Formu burada bitiyor --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>