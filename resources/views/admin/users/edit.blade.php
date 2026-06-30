@push('pageTitle')
    Kullanıcı Düzenle: {{ $user->name }} | 
@endpush
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

                    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH') {{-- Güncelleme işlemi için metod PATCH olarak belirtilir --}}

                        <div class="space-y-6">

                            {{-- Avatar Yükleme --}}
                            <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100 mb-8">
                                <label class="block text-sm font-bold text-gray-800 mb-4 tracking-tight uppercase opacity-70">Profil Fotoğrafı</label>
                                <div class="flex flex-col sm:flex-row items-center gap-6">
                                    <div class="relative flex-shrink-0">
                                        @if($user->profile_photo_path)
                                            <div class="h-24 w-24 rounded-2xl overflow-hidden ring-4 ring-white shadow-xl">
                                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                            </div>
                                        @else
                                            <div class="h-24 w-24 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-3xl shadow-inner border border-indigo-200">
                                                {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 text-center sm:text-left">
                                        <div class="mb-4">
                                            <h4 class="text-gray-900 font-semibold text-lg">{{ $user->name }}</h4>
                                            <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                        </div>
                                        <input type="file" name="photo" id="photo" accept="image/*"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer transition-all">
                                        <p class="mt-2 text-[11px] text-gray-400 font-medium italic">Önerilen: Kare format (örn. 512x512px). Maksimum 2MB.</p>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                            </div>

                            {{-- İsim Soyisim ve E-posta (Merkezi API'den çekilir, yerel düzenleme kapatıldı) --}}
                            {{-- MERKEZİ SİSTEM GEÇİŞİ: 
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">İsim Soyisim <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 p-5 bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-xl border border-gray-200/60 shadow-sm relative overflow-hidden">
                                <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -mr-10 -mt-10"></div>
                                <div class="relative z-10">
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        İsim Soyisim (Merkezi Veri)
                                    </label>
                                    <div class="font-bold text-gray-900 text-lg">{{ $user->name }}</div>
                                </div>
                                <div class="relative z-10">
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        E-posta Adresi (Merkezi Veri)
                                    </label>
                                    <div class="font-bold text-gray-900">{{ $user->email }}</div>
                                </div>
                                <div class="relative z-10">
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                        TC Kimlik No (Merkezi Veri)
                                    </label>
                                    <div class="font-bold text-gray-900">{{ $user->tc_kimlik_no ?? 'Belirtilmemiş' }}</div>
                                </div>
                            </div>

                            {{-- Müşteri Yetkilisi Alanları (Koşullu) --}}
                            @if(!$user->is_personnel)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                                    <div class="sm:col-span-2">
                                        <label for="customer_id" class="block text-sm font-semibold text-blue-900">Bağlı Olduğu Firma <span class="text-red-500">*</span></label>
                                        <select name="customer_id" id="customer_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Firma seçin</option>
                                            @foreach($musteriler as $musteri)
                                                <option value="{{ $musteri->id }}" {{ old('customer_id', $user->customer_id) == $musteri->id ? 'selected' : '' }}>
                                                    {{ $musteri->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                                    </div>

                                    <div>
                                        <label for="unvan" class="block text-sm font-semibold text-blue-900">Ünvan</label>
                                        <input type="text" name="unvan" id="unvan" value="{{ old('unvan', $user->unvan) }}" placeholder="Örn: Satın Alma Müdürü" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <x-input-error :messages="$errors->get('unvan')" class="mt-2" />
                                    </div>

                                    <div>
                                        <label for="telefon" class="block text-sm font-semibold text-blue-900">Telefon</label>
                                        <input type="text" name="telefon" id="telefon" value="{{ old('telefon', $user->telefon) }}" placeholder="Örn: 05xx xxx xx xx" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
                                    </div>
                                </div>
                            @else
                                {{-- Bölüm Seçimi (Sadece Personel İçin) --}}
                                <div>
                                    <label for="bolum_id" class="block text-sm font-medium text-gray-700">Bölüm</label>
                                    <select name="bolum_id" id="bolum_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Bölüm seçilmedi</option>
                                        @foreach($bolumler as $id => $ad)
                                            <option value="{{ $id }}" {{ old('bolum_id', $user->bolum_id) == $id ? 'selected' : '' }}>{{ $ad }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('bolum_id')" class="mt-2" />
                                </div>
                            @endif

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Kullanıcı Rolü <span class="text-red-500">*</span></label>
                                <select name="roles[]" id="role" multiple size="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 custom-scrollbar">
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->name }}"
                                                @if( (is_array(old('roles')) && in_array($rol->name, old('roles'))) || (empty(old('roles')) && $user->roles->contains('name', $rol->name)) ) selected @endif>
                                            {{ $rol->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 italic">Birden fazla rol seçmek için Ctrl tuşuna basılı tutun.</p>
                                <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                            </div>

                            {{-- İşe Giriş, İşten Çıkış ve Doğum Tarihleri --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <div>
                                    <label for="dogum_tarihi" class="block text-sm font-medium text-gray-700">Doğum Tarihi</label>
                                    <input type="date" name="dogum_tarihi" id="dogum_tarihi" 
                                        value="{{ old('dogum_tarihi', $user->dogum_tarihi ? \Carbon\Carbon::parse($user->dogum_tarihi)->format('Y-m-d') : '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('dogum_tarihi')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700">İşe Giriş Tarihi</label>
                                    <input type="date" name="hire_date" id="hire_date" 
                                        value="{{ old('hire_date', $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('Y-m-d') : '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="termination_date" class="block text-sm font-medium text-gray-700">İşten Çıkış Tarihi</label>
                                    <input type="date" name="termination_date" id="termination_date" 
                                        value="{{ old('termination_date', $user->termination_date ? \Carbon\Carbon::parse($user->termination_date)->format('Y-m-d') : '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <x-input-error :messages="$errors->get('termination_date')" class="mt-2" />
                                </div>
                            </div>

                            {{-- Şifre ve Şifre Tekrar (İsteğe Bağlı) --}}
                            {{-- MERKEZİ SİSTEM GEÇİŞİ: Şifreler Merkezi Sistem'den kontrol edilir, yerel şifre güncellemesi kapatıldı.
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
                            --}}
                            <div class="pt-4 border-t flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-500">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </div>
                                <p class="text-sm text-gray-600">Kullanıcı şifreleri <span class="font-semibold text-gray-900">Merkezi API Güvenlik Sistemi</span> tarafından yönetilmektedir.</p>
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

