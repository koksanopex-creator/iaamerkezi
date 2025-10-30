<x-guest-layout> {{-- Mevcut guest layout'unu kullanıyoruz --}}
    <div class="mb-6 pb-6 border-b border-gray-200">
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Şikayet Takip Girişi</h3>
        <p class="text-sm text-gray-600">Şikayetinizi görüntülemek için lütfen e-posta adresinizi ve size gönderilen şifreyi giriniz.</p>
    </div>

    {{-- Hata Mesajları --}}
    @if (session('error'))
         <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Hata!</strong>
            <ul class="mt-1 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Giriş Formu --}}
    {{-- Form action'ı güncel rota ismini kullanıyor --}}
    <form method="POST" action="{{ route('public.sikayet.guestLogin', ['token' => $sikayet->takip_token]) }}">
        @csrf
        <div class="space-y-6">

            {{-- E-posta Adresi (Readonly) --}}
            <div class="group">
                <label for="email" class="block font-semibold text-sm text-gray-700 mb-1.5">E-posta Adresiniz</label>
                <input type="email" name="email" id="email" value="{{ old('email', $sikayet->musteri_iletisim) }}" readonly required
                       class="block w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out text-gray-700">
                 <p class="mt-1 text-xs text-gray-500">Bu e-posta adresi şikayet kaydınızdan alınmıştır.</p>
                 @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Şifre --}}
            <div class="group">
                <label for="password" class="block font-semibold text-sm text-gray-700 mb-1.5">Şifreniz</label>
                <input type="password" name="password" id="password" required
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out text-gray-900"
                       placeholder="E-postanıza gönderilen şifre">
                <p class="mt-1 text-xs text-gray-500">E-postanıza gönderilen geçici şifreyi giriniz.</p>
                 @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-150 ease-in-out">
                Giriş Yap ve Görüntüle
            </button>
        </div>
    </form>
</x-guest-layout>