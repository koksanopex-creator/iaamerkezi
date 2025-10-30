<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Hesap Oluşturun</h1>
            <p class="text-gray-600 mt-2">Aramıza katılın ve fikirlerinizi paylaşmaya başlayın.</p>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">İsim Soyisim</label>
            <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
            <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        
        <div class="mt-4">
            <label for="bolum_id" class="block text-sm font-medium text-gray-700">Bölümünüz</label>
            <select name="bolum_id" id="bolum_id" required class="mt-1 block w-full px-4 py-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Lütfen bölümünüzü seçin...</option>
                @foreach($bolumler as $bolum)
                    <option value="{{ $bolum->id }}" {{ old('bolum_id') == $bolum->id ? 'selected' : '' }}>{{ $bolum->ad }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('bolum_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Şifre Tekrar</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 transition-all transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kayıt Ol
            </button>
        </div>

        <div class="mt-6 text-center text-sm text-gray-600">
            <a class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline" href="{{ route('login') }}">
                Zaten bir hesabınız var mı? Giriş Yapın
            </a>
        </div>
    </form>
</x-guest-layout>