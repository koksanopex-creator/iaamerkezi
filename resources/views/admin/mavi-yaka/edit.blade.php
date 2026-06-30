<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mavi Yaka Personel Düzenle') }}
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
                                <span class="ml-1 text-gray-500 md:ml-2">Personel Düzenle</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Mavi Yaka Personel Düzenle: <span
                        class="text-indigo-600">{{ $maviYaka->name }}</span></h1>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <form action="{{ route('admin.mavi-yaka.update', $maviYaka) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Sol Kolon --}}
                            <div class="space-y-6">
                                {{-- İsim, TC ve E-posta (Merkezi API'den gelir) --}}
                                {{-- MERKEZİ SİSTEM GEÇİŞİ: 
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Ad Soyad <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $maviYaka->name) }}"
                                        required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('name') border-red-300 @enderror">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="tc_kimlik_no" class="block text-sm font-medium text-gray-700">T.C.
                                        Kimlik No
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="tc_kimlik_no" id="tc_kimlik_no"
                                        value="{{ old('tc_kimlik_no', $maviYaka->tc_kimlik_no) }}" required
                                        maxlength="11"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('tc_kimlik_no') border-red-300 @enderror">
                                    @error('tc_kimlik_no') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">E-posta <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $maviYaka->email) }}" required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('email') border-red-300 @enderror">
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="pt-4 border-t border-gray-100">
                                    <h3 class="text-sm font-medium text-gray-900 mb-4">Şifre Değiştirme (Opsiyonel)</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700">Yeni
                                                Şifre</label>
                                            <input type="password" name="password" id="password"
                                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('password') border-red-300 @enderror"
                                                placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="password_confirmation"
                                                class="block text-sm font-medium text-gray-700">Yeni Şifre
                                                (Tekrar)</label>
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                        </div>
                                    </div>
                                </div>
                                --}}
                                <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-xl border border-gray-200/60 shadow-sm space-y-4">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Ad Soyad (Merkezi Veri)</label>
                                        <div class="font-bold text-gray-900 text-lg">{{ $maviYaka->name }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">T.C. Kimlik No (Merkezi Veri)</label>
                                        <div class="font-bold text-gray-900">{{ $maviYaka->tc_kimlik_no }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">E-posta (Merkezi Veri)</label>
                                        <div class="font-bold text-gray-900">{{ $maviYaka->email }}</div>
                                    </div>
                                    <div class="pt-3 border-t border-gray-200/60 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span class="text-xs text-gray-600 font-medium">Bu bilgiler ve şifre Merkezi Sistemden yönetilir.</span>
                                    </div>
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
                                            <option value="{{ $bolum->id }}" {{ old('bolum_id', $maviYaka->bolum_id) == $bolum->id ? 'selected' : '' }}>
                                                {{ $bolum->ad }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bolum_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Profil Fotoğrafı Bölümü --}}
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                    <label class="block text-sm font-bold text-gray-800 mb-3">Profil Fotoğrafı</label>
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0">
                                            @if($maviYaka->profile_photo_path)
                                                <img src="{{ asset('storage/' . $maviYaka->profile_photo_path) }}" 
                                                     alt="{{ $maviYaka->name }}" 
                                                     class="h-16 w-16 rounded-2xl object-cover ring-2 ring-white shadow-md">
                                            @else
                                                <div class="h-16 w-16 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xl shadow-inner">
                                                    {{ Str::upper(Str::substr($maviYaka->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" name="photo" id="photo" accept="image/*"
                                                   class="block w-full text-xs text-gray-500
                                                          file:mr-3 file:py-1.5 file:px-3
                                                          file:rounded-lg file:border-0
                                                          file:text-[11px] file:font-semibold
                                                          file:bg-indigo-600 file:text-white
                                                          hover:file:bg-indigo-700
                                                          cursor-pointer transition-all">
                                            <p class="mt-1.5 text-[10px] text-gray-400">Yeni bir fotoğraf seçerek güncelleyebilirsiniz.</p>
                                        </div>
                                    </div>
                                    @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="unvan"
                                        class="block text-sm font-medium text-gray-700">Unvan/Görevi</label>
                                    <input type="text" name="unvan" id="unvan"
                                        value="{{ old('unvan', $maviYaka->unvan) }}"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                    @error('unvan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="sicil_no" class="block text-sm font-medium text-gray-700">Sicil No
                                        (Opsiyonel)</label>
                                    <input type="text" name="sicil_no" id="sicil_no"
                                        value="{{ old('sicil_no', $maviYaka->sicil_no) }}"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                    @error('sicil_no') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="hire_date" class="block text-sm font-medium text-gray-700 font-semibold mb-1">
                                            {{ __('İşe Giriş Tarihi') }}
                                            <span class="text-[11px] text-gray-400 font-normal">(Opsiyonel)</span>
                                        </label>
                                        <input type="date" name="hire_date" id="hire_date" 
                                               value="{{ old('hire_date', $maviYaka->hire_date ? $maviYaka->hire_date->format('Y-m-d') : '') }}"
                                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                        @error('hire_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="termination_date" class="block text-sm font-medium text-gray-700 font-semibold mb-1">
                                            {{ __('İşten Çıkış Tarihi') }}
                                            <span class="text-[11px] text-gray-400 font-normal">(Opsiyonel)</span>
                                        </label>
                                        <input type="date" name="termination_date" id="termination_date" 
                                               value="{{ old('termination_date', $maviYaka->termination_date ? $maviYaka->termination_date->format('Y-m-d') : '') }}"
                                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors">
                                        @error('termination_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
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
                                Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>