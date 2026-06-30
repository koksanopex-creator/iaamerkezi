@push('pageTitle')
    Yeni Kullanıcı Oluştur | 
@endpush
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Yeni Kullanıcı Ekle') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">

                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight mb-6">Yeni Kullanıcı Bilgileri</h3>

                    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">

                            {{-- Avatar Yükleme --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Profil Fotoğrafı</label>
                                <div class="mt-2 flex items-center space-x-5">
                                    <div
                                        class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center">
                                        <svg class="h-10 w-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="photo" id="photo" accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF (Max. 2MB)</p>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                            </div>

                            {{-- İsim Soyisim --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">İsim Soyisim <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- E-posta Adresi --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- TC, Sicil ve Ünvan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <div>
                                    <label for="tc_kimlik_no" class="block text-sm font-medium text-gray-700">TC Kimlik No</label>
                                    <input type="text" name="tc_kimlik_no" id="tc_kimlik_no" value="{{ old('tc_kimlik_no') }}" maxlength="11"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('tc_kimlik_no')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="sicil_no" class="block text-sm font-medium text-gray-700">Sicil No</label>
                                    <input type="text" name="sicil_no" id="sicil_no" value="{{ old('sicil_no') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('sicil_no')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="unvan" class="block text-sm font-medium text-gray-700">Ünvan</label>
                                    <input type="text" name="unvan" id="unvan" value="{{ old('unvan') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('unvan')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Bölüm Seçimi --}}
                            <div>
                                <label for="bolum_id" class="block text-sm font-medium text-gray-700">Bölüm</label>
                                <select name="bolum_id" id="bolum_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Bölüm seçilmedi</option>
                                    @foreach($bolumler as $id => $ad)
                                        <option value="{{ $id }}" {{ old('bolum_id') == $id ? 'selected' : '' }}>{{ $ad }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('bolum_id')" class="mt-2" />
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Kullanıcı Rolü <span
                                        class="text-red-500">*</span></label>
                                <select name="roles[]" id="role" multiple size="5"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 custom-scrollbar">
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->name }}" {{ (is_array(old('roles')) && in_array($rol->name, old('roles'))) || (old('roles') == $rol->name) ? 'selected' : '' }}>
                                            {{ $rol->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 italic">Birden fazla rol seçmek için Ctrl tuşuna
                                    basılı tutun.</p>
                                <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                            </div>

                            {{-- İşe Giriş, İşten Çıkış ve Doğum Tarihleri --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <div>
                                    <label for="dogum_tarihi" class="block text-sm font-medium text-gray-700">Doğum Tarihi</label>
                                    <input type="date" name="dogum_tarihi" id="dogum_tarihi" value="{{ old('dogum_tarihi') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('dogum_tarihi')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700">İşe Giriş Tarihi</label>
                                    <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="termination_date" class="block text-sm font-medium text-gray-700">İşten Çıkış Tarihi</label>
                                    <input type="date" name="termination_date" id="termination_date" value="{{ old('termination_date') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('termination_date')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Şifre ve Şifre Tekrar --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Şifre <span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="password" id="password" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700">Şifre Tekrar <span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    {{-- password_confirmation için ayrı hata göstermeye gerek yok, 'confirmed' kuralı
                                    password altında gösterir --}}
                                </div>
                            </div>

                            {{-- Form Butonları --}}
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                                <a href="{{ route('admin.users.index') }}"
                                    class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm transition-colors">İptal</a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-sm hover:from-indigo-700 hover:to-blue-600 transform hover:-translate-y-0.5 transition-all">Kullanıcıyı
                                    Kaydet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>