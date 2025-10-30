<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Tekrar Hoş Geldiniz!</h1>
            <p class="text-gray-600 mt-2">Devam etmek için hesabınıza giriş yapın.</p>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
            <div class="mt-1">
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
            <div class="mt-1">
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
             <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center"><input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span class="ms-2 text-sm text-gray-700">Beni Hatırla</span></label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline" href="{{ route('password.request') }}">Şifrenizi mi unuttunuz?</a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 transition-all transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Giriş Yap</button>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Hesabınız yok mu? <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">Kayıt Olun</a></p>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <a href="{{ route('guest.iaa.create') }}" class="w-full inline-flex items-center justify-center bg-gradient-to-r from-green-500 to-emerald-500 text-white font-medium py-3 px-4 rounded-xl shadow-lg transition-transform duration-300 hover:scale-105 hover:shadow-green-500/30 text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                veya Hızlıca Öneri Bırak
            </a>
        </div>

        {{-- ================== YENİ ŞİKAYET LİNKİ ================== --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('public.sikayet.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 rounded-lg font-semibold text-white shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Müşteri Şikayeti Bildir
                    </a>
                </div>
            {{-- ================== LİNK SONU ================== --}}
            
    </form>
</x-guest-layout>