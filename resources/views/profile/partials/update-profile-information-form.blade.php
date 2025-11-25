<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profil Bilgileri') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Hesap profil bilgilerinizi ve e-posta adresinizi güncelleyin.") }}
        </p>
    </header>

    {{-- === HATA MESAJI ALANI === --}}
    @if ($errors->any())
        <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm">
            <p class="font-bold text-sm">Bir şeyler ters gitti!</p>
            <ul class="list-disc list-inside text-sm mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- ========================= --}}

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Fotoğraf Yükleme --}}
        <div>
            <x-input-label for="photo" :value="__('Profil Fotoğrafı')" />
            <input type="file" name="photo" id="photo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        {{-- İsim --}}
        <div>
            <x-input-label for="name" :value="__('Ad Soyad')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('E-posta')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('E-posta adresiniz doğrulanmamış.') }}
                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Doğrulama e-postasını tekrar göndermek için buraya tıklayın.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Yeni bir doğrulama bağlantısı e-posta adresinize gönderildi.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Telefon (Yeni) --}}
        <div>
            <x-input-label for="telefon" :value="__('Telefon Numarası')" />
            <x-text-input id="telefon" name="telefon" type="text" class="mt-1 block w-full" :value="old('telefon', $user->telefon)" placeholder="0555 555 55 55" />
            <x-input-error class="mt-2" :messages="$errors->get('telefon')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Kaydet') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Kaydedildi.') }}</p>
            @endif
        </div>
    </form>
</section>