<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg shadow">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h2 class="font-bold text-2xl text-gray-800">Yeni Çözüm Takımı Oluştur</h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-indigo-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200/50">
                <div class="p-6 sm:p-8">

                    <form action="{{ route('admin.cozum-takimlari.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">

                            {{-- Takım Adı --}}
                            <div>
                                <label for="ad" class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    Takım Adı <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" name="ad" id="ad" value="{{ old('ad') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out py-3 px-4 placeholder-gray-400"
                                       placeholder="Takım için bir isim girin (örn: Preform Şikayet Takımı)">
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
                                    <option value="" disabled selected>-- Lider Seçin --</option>
                                    {{-- === DEĞİŞTİ: $liderler kullanılıyor === --}}
                                    @forelse($liderler as $lider)
                                        <option value="{{ $lider->id }}" {{ old('lider_user_id') == $lider->id ? 'selected' : '' }}>
                                            {{ $lider->name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Uygun lider bulunamadı.</option>
                                    @endforelse
                                </select>
                                <x-input-error :messages="$errors->get('lider_user_id')" class="mt-2" />
                                @if($liderler->isEmpty())
                                 <p class="mt-2 text-xs text-orange-600">Lider olarak atanabilecek ("Müşteri Şikayeti Çözüm Lideri" rolüne sahip) kullanıcı bulunamadı. Lütfen önce kullanıcı yönetimi sayfasından rol ataması yapın.</p>
                                @endif
                            </div>

                            {{-- Takım Türü (Gizli Alan - Çözüm Takımı olarak ayarla) --}}
                            <input type="hidden" name="tur" value="sikayet">

                            {{-- Form Butonları --}}
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 mt-8">
                                <a href="{{ route('admin.cozum-takimlari.index') }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2.5 px-5 border border-gray-300 rounded-lg shadow-sm transition-colors duration-150 ease-in-out">
                                    İptal
                                </a>
                                <button type="submit" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md hover:from-indigo-700 hover:to-blue-700 transform hover:-translate-y-0.5 transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Kaydet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
