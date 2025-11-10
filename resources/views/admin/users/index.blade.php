<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <span class="text-indigo-600">●</span> {{ __('Kullanıcı Yönetimi') }}
                </h2>
                <p class="text-sm text-gray-500">Kullanıcı erişimlerini, rollerini ve onay durumlarını yönetin.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white
                          bg-gradient-to-r from-indigo-600 to-indigo-500 shadow-lg shadow-indigo-500/30
                          hover:from-indigo-500 hover:to-indigo-400 active:scale-[.98] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Yeni Kullanıcı Ekle</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-br from-gray-50 via-white to-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- FLASH MESAJLAR --}}
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50/60 text-green-800 p-4 text-sm flex items-start gap-3 shadow-sm">
                    <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-1.5"></div>
                    <div class="font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50/60 text-red-800 p-4 text-sm flex items-start gap-3 shadow-sm">
                    <div class="flex-shrink-0 w-2 h-2 bg-red-500 rounded-full mt-1.5"></div>
                    <div class="font-medium">{{ session('error') }}</div>
                </div>
            @endif

            {{-- DASHBOARD MİNİ KARTLAR --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="relative overflow-hidden rounded-2xl border border-indigo-200/60 bg-white shadow-sm">
                    <div class="p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Aktif Kullanıcı</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $aktifKullanicilar->total() }}
                            </p>
                        </div>
                        <div class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5.121 17.804A1 1 0 015 17v-1a7 7 0 0114 0v1a1 1 0 01-.121.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="px-4 py-2 text-[11px] text-gray-500 bg-gradient-to-r from-indigo-50/60 to-transparent border-t border-indigo-100/60">
                        Sistemde oturumu aktif kullanıcı sayısı
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl border border-yellow-200/60 bg-white shadow-sm">
                    <div class="p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Onay Bekleyen</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $kayitOnayiAktif && $onayBekleyenler->isNotEmpty() ? $onayBekleyenler->total() : 0 }}
                            </p>
                        </div>
                        <div class="p-2 rounded-xl bg-yellow-50 text-yellow-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="px-4 py-2 text-[11px] text-gray-500 bg-gradient-to-r from-yellow-50/60 to-transparent border-t border-yellow-100/60">
                        Yönetici onayı gerektiren hesaplar
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl border border-blue-200/60 bg-white shadow-sm">
                    <div class="p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Toplam Bölüm</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $bolumler->count() }}
                            </p>
                        </div>
                        <div class="p-2 rounded-xl bg-blue-50 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 7v10a2 2 0 002 2h3m10-12V5a2 2 0 00-2-2h-3.28a2 2 0 00-1.414.586l-7.3 7.3A2 2 0 003 12.414V15a2 2 0 002 2h2m9-10v10a2 2 0 01-2 2h-5.5M10 3v4a1 1 0 001 1h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="px-4 py-2 text-[11px] text-gray-500 bg-gradient-to-r from-blue-50/60 to-transparent border-t border-blue-100/60">
                        Sistemde tanımlı üretim / destek alanları
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl border border-emerald-200/60 bg-white shadow-sm">
                    <div class="p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Rol Çeşidi</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $roller->count() }}
                            </p>
                        </div>
                        <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="px-4 py-2 text-[11px] text-gray-500 bg-gradient-to-r from-emerald-50/60 to-transparent border-t border-emerald-100/60">
                        Tanımlı yetki seviyesi sayısı
                    </div>
                </div>
            </div>

            {{-- ONAY BEKLEYENLER BLOKU --}}
            @if($kayitOnayiAktif && $onayBekleyenler->isNotEmpty())
                <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-yellow-200/50 ring-1 ring-yellow-100/70">
                    <div class="p-6 sm:p-8 border-b border-yellow-100 bg-gradient-to-r from-yellow-50 to-white flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-yellow-800 flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-yellow-500 ring-4 ring-yellow-200/60"></span>
                                Onay Bekleyen Kullanıcılar
                            </h3>
                            <p class="text-xs text-yellow-700/80 font-medium">
                                Sisteme kayıt olmuş fakat henüz aktif edilmemiş hesaplar
                            </p>
                        </div>
                        <div class="text-sm font-semibold text-yellow-800 bg-yellow-100 px-3 py-1 rounded-lg border border-yellow-300 shadow-inner w-fit">
                            Toplam: {{ $onayBekleyenler->total() }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/70">
                                <tr class="text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Ad Soyad / E-posta</th>
                                    <th class="px-6 py-3">Seçtiği Bölüm</th>
                                    <th class="px-6 py-3">Kayıt Tarihi</th>
                                    <th class="px-6 py-3 text-right">İşlemler</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($onayBekleyenler as $user)
                                    <tr class="hover:bg-yellow-50/40 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if($user->bolum_id)
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                    {{ $user->bolum->ad }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                    Bölüm Atanmamış
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $user->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        
                                        {{-- === YENİ GÜZELLEŞTİRİLMİŞ BUTONLAR === --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                <form method="POST" action="{{ route('admin.users.onayla', $user) }}">
                                                    @csrf @method('patch')
                                                    <button type="submit" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition-colors">
                                                        Onayla
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.users.edit', $user) }}" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                                                    Düzenle
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Bu kullanıcı kaydını kalıcı olarak silmek istediğinizden emin misiniz?');">
                                                    @csrf @method('delete')
                                                    <button type="submit" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                                                        Sil
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        {{-- === BUTONLAR SONU === --}}

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">
                                            Onay bekleyen kullanıcı bulunmamaktadır.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($onayBekleyenler->hasPages())
                        <div class="p-6 bg-gradient-to-r from-yellow-50/60 to-white border-t border-yellow-100/80">
                            {{ $onayBekleyenler->links() }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- AKTİF KULLANICILAR BLOKU --}}
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl rounded-2xl border border-gray-200 ring-1 ring-gray-100">
                <div class="p-6 sm:p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between w-full gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-indigo-500 ring-4 ring-indigo-200/60"></span>
                                Aktif Kullanıcılar
                            </h3>
                            <p class="text-xs text-gray-500 font-medium">
                                Sisteme erişimi olan kullanıcılar ve yetki seviyeleri
                            </p>
                        </div>

                        <div class="flex items-center gap-2 text-xs">
                            <div class="px-2.5 py-1 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold shadow-sm">
                                Toplam: {{ $aktifKullanicilar->total() }}
                            </div>
                        </div>
                    </div>

                    {{-- FİLTRE FORMU --}}
                    <form method="GET" action="{{ route('admin.users.index') }}" class="bg-white/60 border border-gray-200 rounded-xl p-4 shadow-inner">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            {{-- İsim/E-posta --}}
                            <div>
                                <label for="name_filter" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                    İsim / E-posta
                                </label>
                                <input type="text" name="name_filter" id="name_filter"
                                       value="{{ $filters['name_filter'] ?? '' }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Kullanıcı ara...">
                            </div>

                            {{-- Bölüm --}}
                            <div>
                                <label for="bolum_filter" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                    Bölüm
                                </label>
                                <select name="bolum_filter" id="bolum_filter"
                                        class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tüm Bölümler</option>
                                    @foreach($bolumler as $bolum)
                                        <option value="{{ $bolum->id }}"
                                                @if(isset($filters['bolum_filter']) && $filters['bolum_filter'] == $bolum->id) selected @endif>
                                            {{ $bolum->ad }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Rol --}}
                            <div>
                                <label for="role_filter" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                    Rol
                                </label>
                                <select name="role_filter" id="role_filter"
                                        class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tüm Roller</option>
                                    @foreach($roller as $role)
                                        <option value="{{ $role->name }}"
                                                @if(isset($filters['role_filter']) && $filters['role_filter'] == $role->name) selected @endif>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Butonlar --}}
                            <div class="flex items-end gap-2">
                                <button type="submit"
                                        class="inline-flex items-center justify-center flex-1 px-4 py-2 rounded-lg text-[11px] font-semibold text-white
                                               bg-blue-600 hover:bg-blue-500 active:scale-[.98] shadow-sm shadow-blue-400/40 transition">
                                    Filtrele
                                </button>

                                <a href="{{ route('admin.users.index') }}"
                                   class="inline-flex items-center justify-center flex-1 px-4 py-2 rounded-lg text-[11px] font-semibold text-white
                                           bg-gray-700 hover:bg-gray-600 active:scale-[.98] shadow-sm shadow-gray-500/40 transition">
                                    Temizle
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TABLO --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50/70">
                            <tr class="text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Ad Soyad / E-posta</th>
                                <th class="px-6 py-3">Bölüm</th>
                                <th class="px-6 py-3">Roller</th>
                                <th class="px-6 py-3 text-right">İşlemler</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($aktifKullanicilar as $user)
                                <tr class="hover:bg-indigo-50/40 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                        @if($user->bolum && $user->bolum->ad)
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                                {{ $user->bolum->ad }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-300">
                                                Atanmamış
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse ($user->roles as $role)
                                                <span class="
                                                    inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold ring-1 ring-inset
                                                    @if($role->name == 'Superadmin')
                                                        bg-red-50 text-red-700 ring-red-200
                                                    @elseif($role->name == 'Müşteri Şikayeti Kurulu')
                                                        bg-yellow-50 text-yellow-700 ring-yellow-200
                                                    @elseif($role->name == 'Bölüm Lideri')
                                                        bg-blue-50 text-blue-700 ring-blue-200
                                                    @else
                                                        bg-emerald-50 text-emerald-700 ring-emerald-200
                                                    @endif
                                                ">
                                                    {{ $role->name }}
                                                </span>
                                            @empty
                                                <span class="text-[11px] text-gray-500 italic bg-gray-100 px-2 py-1 rounded-md border border-gray-300">
                                                    Rol Atanmamış
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>

                                    {{-- === YENİ GÜZELLEŞTİRİLMİŞ BUTONLAR === --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                                                Düzenle
                                            </a>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">
                                                @csrf @method('delete')
                                                <button type="submit" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                                                    Sil
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    {{-- === BUTONLAR SONU === --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">
                                        @if(count($filters) > 0)
                                            Filtre kriterlerine uygun aktif kullanıcı bulunamadı.
                                        @else
                                            Aktif kullanıcı bulunmamaktadır.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($aktifKullanicilar->hasPages())
                    <div class="p-6 bg-gray-50 border-t border-gray-200">
                        {{ $aktifKullanicilar->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>