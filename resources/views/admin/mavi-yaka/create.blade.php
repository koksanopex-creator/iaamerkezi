<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mavi Yaka Personel Ekle') }}
        </h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <div class="mb-8">
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center text-gray-700 hover:text-indigo-600 font-medium">
                                Ana Sayfa
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                                <a href="{{ route('admin.mavi-yaka.index') }}"
                                    class="ml-1 text-gray-700 hover:text-indigo-600 font-medium md:ml-2">Mavi Yaka
                                    Personel</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                                <span class="ml-1 text-gray-500 md:ml-2">Yeni Ekle</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h1 class="text-2xl font-bold text-gray-900">Mavi Yaka Personel Ekle</h1>
                    <a href="{{ route('admin.mavi-yaka.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-50 text-gray-700 rounded-xl text-xs font-bold hover:bg-gray-100 transition-all border border-gray-200 shadow-sm w-fit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Geri Dön
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">
                    {{-- MERKEZİ SİSTEM GEÇİŞİ: Mavi yaka ekleme işlemleri Merkezi API'ye taşındığı için bu form kapatıldı.
                    <form action="{{ route('admin.mavi-yaka.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Sol Kolon --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Ad Soyad <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('name') border-red-300 @enderror">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="tc_kimlik_no" class="block text-sm font-medium text-gray-700">T.C.
                                        Kimlik No
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="tc_kimlik_no" id="tc_kimlik_no"
                                        value="{{ old('tc_kimlik_no') }}" required maxlength="11"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('tc_kimlik_no') border-red-300 @enderror">
                                    @error('tc_kimlik_no') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">E-posta <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('email') border-red-300 @enderror">
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Şifre <span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="password" id="password" required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('password') border-red-300 @enderror">
                                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700">Şifre
                                        (Tekrar) <span class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                </div>
                            </div>

                            {{-- Sağ Kolon --}}
                            <div class="space-y-6">
                                <div>
                                    <label for="bolum_id" class="block text-sm font-medium text-gray-700">Bölüm <span
                                            class="text-red-500">*</span></label>
                                    <select name="bolum_id" id="bolum_id" required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('bolum_id') border-red-300 @enderror">
                                        <option value="">Bölüm Seçiniz</option>
                                        @foreach($bolumler as $bolum)
                                            <option value="{{ $bolum->id }}" {{ (old('bolum_id', $preselectedBolumId ?? '') == $bolum->id) ? 'selected' : '' }}>
                                                {{ $bolum->ad }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bolum_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="photo" class="block text-sm font-medium text-gray-700 font-semibold mb-1">
                                        {{ __('Profil Fotoğrafı') }}
                                        <span class="text-[11px] text-gray-400 font-normal">(Opsiyonel)</span>
                                    </label>
                                    <input type="file" name="photo" id="photo" accept="image/*"
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-xl file:border-0
                                                  file:text-xs file:font-semibold
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100
                                                  border border-gray-200 rounded-xl p-1 shadow-sm transition-all">
                                    @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="unvan"
                                        class="block text-sm font-medium text-gray-700">Unvan/Görevi</label>
                                    <input type="text" name="unvan" id="unvan" value="{{ old('unvan') }}"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                    @error('unvan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="sicil_no" class="block text-sm font-medium text-gray-700">Sicil No
                                        (Opsiyonel)</label>
                                    <input type="text" name="sicil_no" id="sicil_no" value="{{ old('sicil_no') }}"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                    @error('sicil_no') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="hire_date" class="block text-sm font-medium text-gray-700 font-semibold mb-1">
                                            {{ __('İşe Giriş Tarihi') }}
                                            <span class="text-[11px] text-gray-400 font-normal">(Opsiyonel)</span>
                                        </label>
                                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}"
                                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                        @error('hire_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="termination_date" class="block text-sm font-medium text-gray-700 font-semibold mb-1">
                                            {{ __('İşten Çıkış Tarihi') }}
                                            <span class="text-[11px] text-gray-400 font-normal">(Opsiyonel)</span>
                                        </label>
                                        <input type="date" name="termination_date" id="termination_date" value="{{ old('termination_date') }}"
                                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                        @error('termination_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="bg-indigo-50 rounded-xl p-4 mt-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-indigo-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1 md:flex md:justify-between">
                                            <p class="text-xs text-indigo-700">
                                                Bu personel, Mavi Yaka olarak işaretlenecektir. Şikayet ve rapor
                                                ekranlarına
                                                sadece yetkisi dahilinde erişebilir. Doğrudan İAA havuzu gibi sayfalara
                                                yönlendirilecek.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            <a href="{{ route('admin.mavi-yaka.index') }}"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                İptal
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                Kaydet
                            </button>
                        </div>
                    </form>
                    --}}
                    
                    <div class="py-12 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-300 flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-indigo-100 text-indigo-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 mb-2">Merkezi Sistem Geçişi</h2>
                        <p class="text-gray-500 text-sm max-w-md mx-auto mb-6">Mavi yaka personel ekleme işlemleri güvenlik ve senkronizasyon gereği Merkezi API sistemine taşınmıştır. İAA içerisinden manuel kullanıcı eklenemez.</p>
                        <a href="{{ route('admin.mavi-yaka.index') }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                            Listeye Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>