<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kullanıcıyı Düzenle: <span class="text-indigo-600">{{ $user->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-6 sm:p-8">

                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight mb-6">Kullanıcı Bilgilerini Güncelle</h3>

                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PATCH') {{-- Güncelleme işlemi için metod PATCH olarak belirtilir --}}

                        <div class="space-y-6">

                            {{-- İsim Soyisim --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">İsim Soyisim <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- E-posta Adresi --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- Bölüm Seçimi --}}
                            <div>
                                <label for="bolum_id" class="block text-sm font-medium text-gray-700">Bölüm</label>
                                <select name="bolum_id" id="bolum_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Bölüm seçilmedi</option>
                                    {{-- === DÜZELTME: Controller'dan gelen $bolumler artık key=>value array === --}}
                                    @foreach($bolumler as $id => $ad)
                                        <option value="{{ $id }}" {{ old('bolum_id', $user->bolum_id) == $id ? 'selected' : '' }}>{{ $ad }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('bolum_id')" class="mt-2" />
                            </div>

                            {{-- Rol Seçimi (Checkbox'lar ile güncellendi) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kullanıcı Rolleri</label>
                                <div class="mt-2 space-y-2">
                                     {{-- === DÜZELTME: $roller yerine $roles kullanıldı === --}}
                                    @foreach($roles as $rol)
                                        <div class="flex items-center">
                                            <input id="role-{{ $rol->id }}"
                                                   name="roles[]"
                                                   type="checkbox"
                                                   value="{{ $rol->name }}"
                                                   {{-- Eğer kullanıcı bu role zaten sahipse veya form hatası sonrası eski seçim varsa işaretle --}}
                                                   @if( (is_array(old('roles')) && in_array($rol->name, old('roles'))) || (empty(old('roles')) && $user->hasRole($rol->name)) ) checked @endif
                                                   class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <label for="role-{{ $rol->id }}" class="ml-3 block text-sm font-medium text-gray-700">
                                                {{ $rol->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                                <x-input-error :messages="$errors->get('roles.*')" class="mt-2" /> {{-- Dizi elemanları için hata gösterimi --}}
                            </div>

                            {{-- Şifre ve Şifre Tekrar (İsteğe Bağlı) --}}
                            <div class="pt-4 border-t">
                                <p class="text-sm text-gray-500">Şifreyi değiştirmek istemiyorsanız bu alanı boş bırakın.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">Yeni Şifre</label>
                                        <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Yeni Şifre Tekrar</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>

                            {{-- Form Butonları --}}
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm transition-colors">İptal</a>
                                <button type="submit" class="inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-sm hover:from-indigo-700 hover:to-blue-600 transform hover:-translate-y-0.5 transition-all">Değişiklikleri Kaydet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

