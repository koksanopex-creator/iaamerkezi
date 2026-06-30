<x-guest-layout>

    {{-- Alpine Component Context for Modal --}}
    <div x-data="{ showKvkk: false }">

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Aramıza Katılın</h1>
                <p class="text-gray-500 mt-2 text-sm">Fabrika süreçlerine hemen dahil olmak için hesap oluşturun.</p>
            </div>

            {{-- 1. İsim Soyisim --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Ad Soyad</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input id="name" name="name" type="text" required autofocus autocomplete="name"
                        value="{{ old('name') }}"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 bg-gray-50/50 hover:bg-white"
                        placeholder="Adınız ve Soyadınız">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- 2. E-Posta --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">E-posta Adresi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input id="email" name="email" type="email" required autocomplete="username"
                        value="{{ old('email') }}"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-200 bg-gray-50/50 hover:bg-white"
                        placeholder="ad.soyad@koksan.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- 3. Bölüm Seçimi --}}
            <div>
                <label for="bolum_id" class="block text-sm font-semibold text-gray-700 mb-1">Bağlı Olduğunuz Bölüm /
                    Birim</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <select id="bolum_id" name="bolum_id" required
                        class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm appearance-none bg-gray-50/50 hover:bg-white transition-all cursor-pointer">
                        <option value="" disabled selected>Lütfen Bölüm Seçiniz</option>
                        @foreach($bolumler as $bolum)
                            <option value="{{ $bolum->id }}" {{ old('bolum_id') == $bolum->id ? 'selected' : '' }}>
                                {{ $bolum->ad }}
                            </option>
                        @endforeach
                        <option value="" disabled>──────────</option>
                        <option value="other">Listede Yok / Diğer</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('bolum_id')" class="mt-2" />
            </div>

            {{-- 4. Şifre --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Şifre Oluşturun</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-50/50 hover:bg-white transition-all"
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- 5. Şifre Onay --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Şifre
                    Tekrarı</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        autocomplete="new-password"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-50/50 hover:bg-white transition-all"
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- KVKK Onayı --}}
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="kvkk_approval" name="kvkk_approval" type="checkbox" required
                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded cursor-pointer">
                </div>
                <div class="ml-3 text-sm">
                    <label for="kvkk_approval" class="font-medium text-gray-700 cursor-pointer select-none">
                        <span class="text-blue-600 hover:text-blue-800 underline cursor-pointer"
                            @click.prevent="showKvkk = true">Kişisel Verilerin Korunması Kanunu (KVKK) Metnini</span>
                        okudum ve onaylıyorum.
                    </label>
                    <x-input-error :messages="$errors->get('kvkk_approval')" class="mt-1" />
                </div>
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-500/30 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:-translate-y-0.5">
                    Kayıt Ol
                </button>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}"
                    class="text-sm font-medium text-gray-600 hover:text-blue-500 transition-colors">
                    Zaten bir hesabınız var mı? <span class="text-blue-600 underline">Giriş Yapın</span>
                </a>
            </div>
        </form>

        {{-- KVKK MODAL --}}
        <div x-show="showKvkk" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog"
            aria-modal="true">
            {{-- Backdrop --}}
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showKvkk" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showKvkk = false"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Panel --}}
                <div x-show="showKvkk" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    KVKK Aydınlatma Metni
                                </h3>

                                @php
                                    $kvkkPdfVal = \App\Models\Setting::where('key', 'kvkk_pdf')->value('value');
                                    $kvkkTextVal = \App\Models\Setting::where('key', 'kvkk_text')->value('value');
                                @endphp

                                @if($kvkkPdfVal)
                                    <div class="mt-4 w-full h-[60vh] rounded-lg overflow-hidden border border-gray-100">
                                        <embed src="{{ asset('storage/' . $kvkkPdfVal) }}#toolbar=0" type="application/pdf"
                                            class="w-full h-full">
                                    </div>
                                @else
                                    <div
                                        class="mt-4 max-h-96 overflow-y-auto bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <div class="prose prose-sm text-gray-600">
                                            {!! !empty($kvkkTextVal) ? $kvkkTextVal : 'KVKK metni henüz sistem yöneticisi tarafından eklenmemiştir.' !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="showKvkk = false; document.getElementById('kvkk_approval').checked = true;">
                            Okudum, Anladım
                        </button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="showKvkk = false">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-guest-layout>