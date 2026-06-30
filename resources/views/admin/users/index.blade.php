@push('pageTitle')
    Kullanıcı Yönetimi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <span class="text-indigo-600">●</span> {{ __('Kullanıcı Yönetimi') }}
                </h2>
                <p class="text-sm text-gray-500">Kullanıcı erişimlerini, rollerini ve onay durumlarını yönetin.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.users.export') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-emerald-700
                          bg-emerald-50 border border-emerald-200 shadow-sm
                          hover:bg-emerald-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Dışa Aktar</span>
                </a>
                
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-blue-700
                          bg-blue-50 border border-blue-200 shadow-sm
                          hover:bg-blue-100 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>İçe Aktar</span>
                </button>

                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white
                          bg-gradient-to-r from-indigo-600 to-indigo-500 shadow-lg shadow-indigo-500/30
                          hover:from-indigo-500 hover:to-indigo-400 active:scale-[.98] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Yeni Kullanıcı Ekle (Merkezi Senkron)</span>
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
                <a href="{{ route('admin.users.index', ['activeTab' => 'sistem']) }}" class="block group relative overflow-hidden rounded-2xl border border-indigo-200/60 bg-white shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                    <div class="p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide group-hover:text-indigo-600 transition-colors">Ofis / Beyaz Yaka</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $sistemKullanicilari->total() }}
                            </p>
                        </div>
                        <div class="p-2 rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="px-4 py-2 text-[11px] text-gray-500 bg-gradient-to-r from-indigo-50/60 to-transparent border-t border-indigo-100/60">
                        Ofis personeli sayısı
                    </div>
                </a>

                <a href="{{ route('admin.users.index', ['activeTab' => 'mavi']) }}" class="block group relative overflow-hidden rounded-2xl border border-emerald-200/60 bg-white shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                    <div class="p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide group-hover:text-emerald-600 transition-colors">Mavi Yaka</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $maviYakaKullanicilari->total() }}
                            </p>
                        </div>
                        <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="px-4 py-2 text-[11px] text-gray-500 bg-gradient-to-r from-emerald-50/60 to-transparent border-t border-emerald-100/60">
                        Saha personeli sayısı
                    </div>
                </a>

                <a href="{{ route('admin.users.index', ['activeTab' => 'musteri']) }}" class="block group relative overflow-hidden rounded-2xl border border-purple-200/60 bg-white shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                    <div class="p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide group-hover:text-purple-600 transition-colors">Müşteri Yetkilisi</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $musteriKullanicilari->total() }}
                            </p>
                        </div>
                        <div class="p-2 rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="px-4 py-2 text-[11px] text-gray-500 bg-gradient-to-r from-purple-50/60 to-transparent border-t border-purple-100/60">
                        Tanımlı müşteri temsilcisi sayısı
                    </div>
                </a>

                <div class="relative overflow-hidden rounded-2xl border border-yellow-200/60 bg-white shadow-sm">
                    <div class="p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Onay Bekleyen</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ $onayBekleyenler->total() }}
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
            </div>

            {{-- ONAY BEKLEYENLER BLOKU --}}
            @if($onayBekleyenler->isNotEmpty())
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
                                            <div class="flex items-center gap-2">
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                @if($user->is_mavi_yaka)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-tighter">
                                                        Mavi Yaka
                                                    </span>
                                                @endif
                                            </div>
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
                                            @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                            <div class="flex items-center justify-end gap-2">
                                                @if(!$user->hasVerifiedEmail())
                                                    <form method="POST" action="{{ route('admin.users.verifyEmail', $user) }}">
                                                        @csrf
                                                        <button type="submit" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors" title="E-postayı Doğrula">
                                                            E-posta Doğrula
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.users.onayla', $user) }}">
                                                    @csrf @method('patch')
                                                    <button type="button" 
                                                            class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition-colors swal-confirm"
                                                            data-swal-title="Kullanıcı Onaylanacak"
                                                            data-swal-text="Bu kullanıcının sisteme erişimine izin vermek istediğinizden emin misiniz?"
                                                            data-swal-icon="question"
                                                            data-swal-type="success">
                                                        Onayla
                                                    </button>
                                                </form>
                                                <a href="{{ $user->is_mavi_yaka ? route('admin.mavi-yaka.edit', $user->id) : route('admin.users.edit', $user) }}" class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition-colors">
                                                    Düzenle
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block">
                                                    @csrf @method('delete')
                                                    <button type="button" 
                                                            class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition-colors swal-confirm"
                                                            data-swal-title="Kayıt Silinecek!"
                                                            data-swal-text="Bu kullanıcı kaydını kalıcı olarak silmek istediğinizden emin misiniz?"
                                                            data-swal-icon="warning"
                                                            data-swal-type="danger">
                                                        Sil
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
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

            {{-- TABLAT KULLANICI LİSTESİ --}}
            <div x-data="{ activeTab: '{{ request('activeTab', 'sistem') }}' }" class="bg-white/80 backdrop-blur-sm shadow-xl rounded-xl border border-gray-200 ring-1 ring-gray-100 overflow-hidden">
                
                {{-- TAB BAŞLIKLARI --}}
                <div class="border-b border-gray-200 bg-gray-50 flex items-center px-4">
                    <button @click="activeTab = 'sistem'" 
                            :class="activeTab === 'sistem' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out focus:outline-none">
                        Sistem Kullanıcıları ({{ $sistemKullanicilari->total() }})
                    </button>
                    <button @click="activeTab = 'mavi'" 
                            :class="activeTab === 'mavi' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out focus:outline-none">
                        Mavi Yaka ({{ $maviYakaKullanicilari->total() }})
                    </button>
                    <button @click="activeTab = 'musteri'" 
                            :class="activeTab === 'musteri' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out focus:outline-none">
                        Müşteri Yetkilileri ({{ $musteriKullanicilari->total() }})
                    </button>
                    <button @click="activeTab = 'resigned'" 
                            :class="activeTab === 'resigned' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out focus:outline-none">
                        İşten Çıkanlar ({{ $resignedUsers->total() }})
                    </button>
                </div>

                {{-- FİLTRE FORMU (Her iki tab için ortak) --}}
                <div class="p-4 border-b border-gray-100 bg-white">
                     <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <input type="hidden" name="activeTab" :value="activeTab"> {{-- TAB DURUMUNU KORU --}}
                        
                        {{-- İsim/E-posta --}}
                        <div>
                            <label for="name_filter" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                İsim veya E-posta
                            </label>
                            <input type="text" name="name_filter" id="name_filter" value="{{ $filters['name_filter'] ?? '' }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Ara...">
                        </div>

                        {{-- Bölüm (Sadece Sistem Kullanıcıları için) --}}
                        <div x-show="activeTab === 'sistem'">
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

                        {{-- Firma (Sadece Müşteri Kullanıcıları için) --}}
                        <div x-show="activeTab === 'musteri'" style="display: none;">
                            <label for="customer_filter" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                Firma (Müşteri)
                            </label>
                            <select name="customer_filter" id="customer_filter"
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tüm Firmalar</option>
                                @foreach($musteriler as $musteri)
                                    <option value="{{ $musteri->id }}"
                                            @if(isset($filters['customer_filter']) && $filters['customer_filter'] == $musteri->id) selected @endif>
                                        {{ $musteri->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rol (Sadece Sistem Kullanıcıları için) --}}
                        <div x-show="activeTab === 'sistem'">
                            <label for="role_filter" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                Rol
                            </label>
                            <select name="role_filter" id="role_filter" class="mt-1 block w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Tüm Roller</option>
                                @foreach($roller as $role)
                                    <option value="{{ $role->name }}" @if(isset($filters['role_filter']) && $filters['role_filter'] == $role->name) selected @endif>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ünvan (Sadece Müşteri Kullanıcıları için) --}}
                        <div x-show="activeTab === 'musteri'" style="display: none;">
                            <label for="title_filter" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wide">
                                Ünvan
                            </label>
                            <input type="text" name="title_filter" id="title_filter" value="{{ $filters['title_filter'] ?? '' }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Ünvan Ara...">
                        </div>
                        {{-- Butonlar --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 bg-gray-800 text-white px-3 py-2 rounded-lg text-xs font-semibold hover:bg-gray-700 transition">Filtrele</button>
                            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-xs font-semibold hover:bg-gray-300 transition">Temizle</a>
                        </div>
                    </form>
                </div>

                {{-- TAB İÇERİKLERİ --}}
                
                {{-- 1. SİSTEM KULLANICILARI TABLOSU --}}
                <div x-show="activeTab === 'sistem'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/70">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bölüm</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roller</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kayıt Tarihi</th>
                                    <th class="px-6 py-3 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($sistemKullanicilari as $user)
                                    <tr class="hover:bg-indigo-50/50 transition">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                 @if($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-8 w-8 rounded-full object-cover border border-gray-200">
                                                 @else
                                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                                    </div>
                                                 @endif
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                                        @if($user->is_mavi_yaka)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-tighter">
                                                                Mavi Yaka
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            @if($user->bolum)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                                    {{ $user->bolum->ad }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($user->roles as $role)
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-gray-500">
                                            {{ $user->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                            <div class="flex items-center justify-end gap-2">
                                                @if(!$user->hasVerifiedEmail())
                                                    <form method="POST" action="{{ route('admin.users.verifyEmail', $user) }}" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="p-1.5 rounded-full text-blue-600 hover:bg-blue-50 transition-colors" title="E-postayı Doğrula">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ $user->is_mavi_yaka ? route('admin.mavi-yaka.edit', $user->id) : route('admin.users.edit', $user) }}" class="p-1.5 rounded-full text-indigo-600 hover:bg-indigo-50 transition-colors" title="Düzenle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.resign', $user) }}" class="inline-block">
                                                    @csrf @method('patch')
                                                    <button type="button" class="p-1.5 rounded-full text-orange-600 hover:bg-orange-50 transition-colors relative z-10 swal-confirm" 
                                                            title="İşten Çıkar (Pasif Yap)"
                                                            data-swal-title="Personel İşten Çıkarılacak"
                                                            data-swal-text="Bu kullanıcıyı işten çıkarmak (pasif yapmak) istediğinizden emin misiniz?"
                                                            data-swal-icon="warning"
                                                            data-swal-type="warning">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block">
                                                    @csrf @method('delete')
                                                    <button type="button" class="p-1.5 rounded-full text-red-600 hover:bg-red-50 transition-colors relative z-10 swal-confirm" 
                                                            title="Sil"
                                                            data-swal-title="DİKKAT: Kalıcı Silme"
                                                            data-swal-text="Bu kullanıcıyı SİSTEMDEN KALICI OLARAK silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!"
                                                            data-swal-icon="error"
                                                            data-swal-type="danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 text-xs">Sistem kullanıcısı bulunamadı.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($sistemKullanicilari->hasPages())
                        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                            {{ $sistemKullanicilari->appends(['activeTab' => 'sistem'])->links() }}
                        </div>
                    @endif
                </div>

                {{-- 2. MAVİ YAKA KULLANICILARI TABLOSU --}}
                <div x-show="activeTab === 'mavi'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/70">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bölüm</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ünvan/Sicil</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kayıt Tarihi</th>
                                    <th class="px-6 py-3 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($maviYakaKullanicilari as $user)
                                    <tr class="hover:bg-emerald-50/50 transition">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                 @if($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-8 w-8 rounded-full object-cover border border-gray-200">
                                                 @else
                                                    <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                                        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                                    </div>
                                                 @endif
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                                        @if($user->is_mavi_yaka)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-tighter">
                                                                Mavi Yaka
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            @if($user->bolum)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                                    {{ $user->bolum->ad }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="text-xs text-gray-900">{{ $user->unvan ?? '-' }}</div>
                                            <div class="text-[10px] text-gray-500">Sicil: {{ $user->sicil_no ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-gray-500">
                                            {{ $user->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.mavi-yaka.edit', $user->id) }}" class="p-1.5 rounded-full text-indigo-600 hover:bg-indigo-50 transition-colors" title="Düzenle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.resign', $user) }}" class="inline-block">
                                                    @csrf @method('patch')
                                                    <button type="button" class="p-1.5 rounded-full text-orange-600 hover:bg-orange-50 transition-colors relative z-10 swal-confirm" 
                                                            title="İşten Çıkar (Pasif Yap)"
                                                            data-swal-title="Personel İşten Çıkarılacak"
                                                            data-swal-text="Bu personeli işten çıkarmak (pasif yapmak) istediğinizden emin misiniz?"
                                                            data-swal-icon="warning"
                                                            data-swal-type="warning">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block">
                                                    @csrf @method('delete')
                                                    <button type="button" class="p-1.5 rounded-full text-red-600 hover:bg-red-50 transition-colors relative z-10 swal-confirm" 
                                                            title="Sil"
                                                            data-swal-title="DİKKAT: Kalıcı Silme"
                                                            data-swal-text="Bu personeli silmek istediğinizden emin misiniz?"
                                                            data-swal-icon="error"
                                                            data-swal-type="danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 text-xs">Mavi yaka personeli bulunamadı.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($maviYakaKullanicilari->hasPages())
                        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                            {{ $maviYakaKullanicilari->appends(['activeTab' => 'mavi'])->links() }}
                        </div>
                    @endif
                </div>

                {{-- 3. MÜŞTERİ YETKİLİLERİ TABLOSU --}}
                <div x-show="activeTab === 'musteri'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50/70">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yetkili Kişi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bağlı Olduğu Firma</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ünvan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eklenme Tarihi</th>
                                    <th class="px-6 py-3 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($musteriKullanicilari as $user)
                                    <tr class="hover:bg-purple-50/50 transition">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                 @if($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-8 w-8 rounded-full object-cover border border-gray-200">
                                                 @else
                                                    <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-xs">
                                                        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                                    </div>
                                                 @endif
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                                        @if($user->is_mavi_yaka)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-tighter">
                                                                Mavi Yaka
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            @if($user->customer)
                                                <a href="{{ route('musteri.profil.show', $user->customer->id) }}" class="text-indigo-600 hover:underline font-medium text-xs">
                                                    {{ $user->customer->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">Firma Bağı Yok</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-xs text-gray-700">
                                            {{ $user->unvan ?? '-' }}
                                        </td>
                                        <td class="px-6 py-3">
                                            {{-- TOOLTIP MEKANİZMASI --}}
                                            @php
                                                // Eğer accessor null ise hata vermemesi için kontrol
                                                $creatorName = null;
                                                try {
                                                    $creator = $user->creator;
                                                    if($creator) $creatorName = $creator->name;
                                                } catch(\Exception $e) {}
                                            @endphp

                                            <div class="group relative inline-block cursor-help">
                                                <span class="text-xs text-gray-500 border-b border-dotted border-gray-400">
                                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                                </span>
                                                {{-- Tooltip Content --}}
                                                <div class="opacity-0 invisible group-hover:opacity-100 group-hover:visible absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg whitespace-nowrap z-50 transition-all duration-200 pointer-events-none">
                                                    @if($creatorName)
                                                        <div class="font-semibold">Ekleyen:</div>
                                                        <div>{{ $creatorName }}</div>
                                                    @else
                                                        Ekleyen bilgisi bulunamadı
                                                    @endif
                                                    {{-- Ok Kısmı --}}
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                            <div class="flex items-center justify-end gap-2">
                                                @if(!$user->hasVerifiedEmail())
                                                    <form method="POST" action="{{ route('admin.users.verifyEmail', $user) }}" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="p-1.5 rounded-full text-blue-600 hover:bg-blue-50 transition-colors" title="E-postayı Doğrula">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ $user->is_mavi_yaka ? route('admin.mavi-yaka.edit', $user->id) : route('admin.users.edit', $user) }}" class="p-1.5 rounded-full text-indigo-600 hover:bg-indigo-50 transition-colors" title="Düzenle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.resign', $user) }}" class="inline-block">
                                                    @csrf @method('patch')
                                                    <button type="button" class="p-1.5 rounded-full text-orange-600 hover:bg-orange-50 transition-colors relative z-10 swal-confirm" 
                                                            title="İşten Çıkar (Pasif Yap)"
                                                            data-swal-title="Personel İşten Çıkarılacak"
                                                            data-swal-text="Bu kullanıcıyı işten çıkarmak (pasif yapmak) istediğinizden emin misiniz?"
                                                            data-swal-icon="warning"
                                                            data-swal-type="warning">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block">
                                                    @csrf @method('delete')
                                                    <button type="button" class="p-1.5 rounded-full text-red-600 hover:bg-red-50 transition-colors relative z-10 swal-confirm" 
                                                            title="Sil"
                                                            data-swal-title="DİKKAT: Kalıcı Silme"
                                                            data-swal-text="Bu kullanıcıyı silmek istediğinizden emin misiniz?"
                                                            data-swal-icon="error"
                                                            data-swal-type="danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 text-xs">Müşteri yetkilisi bulunamadı.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($musteriKullanicilari->hasPages())
                        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                            {{ $musteriKullanicilari->appends(['activeTab' => 'musteri'])->links() }}
                        </div>
                    @endif
                </div>

                {{-- 4. İŞTEN ÇIKANLAR LİSTESİ --}}
                <div x-show="activeTab === 'resigned'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-red-50/70">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Kullanıcı</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Bölüm</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">İşten Çıkış</th>
                                    <th class="px-6 py-3 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($resignedUsers as $user)
                                    <tr class="hover:bg-red-50/30 transition opacity-75">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                 <div class="grayscale">
                                                     @if($user->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-8 w-8 rounded-full object-cover">
                                                     @else
                                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs">
                                                            {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                                        </div>
                                                     @endif
                                                 </div>
                                                <div>
                                                    <div class="font-semibold text-gray-700">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            @if($user->bolum)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                    {{ $user->bolum->ad }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-xs text-red-600 font-medium">
                                            {{ $user->deleted_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                            <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="inline-block">
                                                @csrf @method('patch')
                                                <button type="button" 
                                                        class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-[10px] font-bold rounded-lg hover:bg-green-700 transition shadow-sm swal-confirm"
                                                        data-swal-title="Personel Geri Alınacak"
                                                        data-swal-text="Bu kullanıcıyı tekrar aktif hale getirmek istediğinizden emin misiniz?"
                                                        data-swal-icon="question"
                                                        data-swal-type="success">
                                                    GERİ AL (AKTİF YAP)
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm italic">
                                            İşten çıkarılan kullanıcı bulunmamaktadır.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($resignedUsers->hasPages())
                        <div class="p-6 bg-gray-50 border-t border-gray-100">
                            {{ $resignedUsers->appends(['activeTab' => 'resigned'])->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Excel'den Kullanıcı İçe Aktar</h3>
                        <div class="mt-4">
                            <form action="{{ route('admin.users.import_preview') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Excel Dosyası (.xlsx, .xls, .csv)</label>
                                    <input type="file" name="excel_file" required accept=".xlsx,.xls,.csv" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Önizle ve Devam Et
                                    </button>
                                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                                        İptal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // SweetAlert2 Global Confirm Handler
            document.querySelectorAll('.swal-confirm').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const title = this.getAttribute('data-swal-title') || 'Emin misiniz?';
                    const text = this.getAttribute('data-swal-text') || 'Bu işlem geri alınamaz!';
                    const icon = this.getAttribute('data-swal-icon') || 'warning';
                    const confirmButtonText = this.getAttribute('data-swal-confirm-text') || 'Evet, Devam Et';
                    const type = this.getAttribute('data-swal-type') || 'warning';

                    let confirmButtonColor = '#4f46e5'; // Indigo default
                    if (type === 'danger') confirmButtonColor = '#ef4444'; // Red-500
                    if (type === 'warning') confirmButtonColor = '#f97316'; // Orange-500
                    if (type === 'success') confirmButtonColor = '#22c55e'; // Green-500

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: confirmButtonColor,
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: confirmButtonText,
                        cancelButtonText: 'Vazgeç',
                        reverseButtons: true,
                        padding: '2rem',
                        borderRadius: '20px',
                        // MODERN BACKDROP (BLUR + DARK)
                        backdrop: `rgba(15, 23, 42, 0.75)`,
                        customClass: {
                            container: 'swal2-modern-container',
                            popup: 'rounded-3xl shadow-2xl border border-gray-100',
                            title: 'text-xl font-bold text-gray-900',
                            htmlContainer: 'text-sm text-gray-600',
                            confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold',
                            cancelButton: 'rounded-xl px-6 py-2.5 text-sm font-bold'
                        },
                        showClass: {
                            popup: 'animate__animated animate__zoomIn animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut animate__faster'
                        },
                        didOpen: () => {
                            // Ekranı kaplayan blur efekti için body'ye class ekleyebiliriz veya backdrop üzerinden hallederiz
                            document.querySelector('.swal2-container').style.backdropFilter = 'blur(8px)';
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Premium Loading State
                            Swal.fire({
                                title: 'İşlem Yapılıyor',
                                html: `
                                    <div class="flex flex-col items-center gap-4 py-4">
                                        <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                                        <p class="text-gray-500 text-xs font-medium uppercase tracking-widest">Lütfen Bekleyin...</p>
                                    </div>
                                `,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                borderRadius: '20px',
                                customClass: {
                                    popup: 'rounded-3xl shadow-xl'
                                }
                            });
                            
                            // Formu gönder
                            setTimeout(() => form.submit(), 300);
                        }
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>