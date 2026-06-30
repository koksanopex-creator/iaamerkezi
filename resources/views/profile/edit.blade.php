@php
    // Müşteri Kontrolü: customer_id doluysa bu kişi müşteridir.
    $isCustomer = !empty($user->customer_id);
@endphp

@push('pageTitle')
    Profilimi Düzenle | 
@endpush

<x-app-layout>
    {{-- Arka Plan --}}
    <div class="relative bg-gradient-to-r from-indigo-900 to-blue-800 pb-32 overflow-hidden shadow-xl">
        <div class="relative max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">Profilim</h1>
            <p class="mt-2 text-xl text-indigo-200">Hoşgeldiniz, {{ $user->name }}</p>
            <p class="mt-1 text-sm text-indigo-300 opacity-80">Son İşlem: {{ $user->updated_at->format('d.m.Y H:i') }}
            </p>
        </div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 pb-12">

        {{-- ÜST KART --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="sm:flex sm:items-center sm:justify-between p-6 sm:p-8">

                {{-- SOL TARAFTAKİ KULLANICI BİLGİLERİ --}}
                <div class="sm:flex sm:items-center">
                    <div class="flex-shrink-0">
                        @if($user->profile_photo_path)
                            <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg"
                                src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                        @else
                            <div
                                class="h-24 w-24 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 p-1 shadow-lg">
                                <div
                                    class="h-full w-full rounded-full bg-white flex items-center justify-center text-3xl font-bold text-indigo-600 uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6 text-center sm:text-left">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-sm font-medium text-gray-500">{{ $user->email }}</p>
                        @if($user->telefon)
                            <p class="text-sm text-gray-400 mt-1 flex items-center justify-center sm:justify-start gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $user->telefon }}
                            </p>
                        @endif
                        <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            {{-- Rol Etiketi --}}
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $user->roles->first()->name ?? 'Kullanıcı' }}
                            </span>

                            {{-- Herkese Açık Profil Linki (Sadece Personel Görsün) --}}
                            @if(!$isCustomer)
                                <a href="{{ route('profile.show', $user->id) }}"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors border border-gray-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Herkese Açık Profili Gör
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SAĞ TARAFTAKİ KART (PUAN veya FİRMA BİLGİSİ) --}}
                <div class="mt-5 sm:mt-0 text-center sm:text-right">

                    @if(!$isCustomer)
                        {{-- 1. PERSONEL İÇİN: PUAN KARTI --}}
                        <a href="{{ route('profile.puanlar', $user->id) }}"
                            class="inline-block p-4 rounded-xl bg-gradient-to-br from-yellow-50 to-amber-50 border border-amber-200 shadow-sm hover:shadow-md transition-all transform hover:scale-105 cursor-pointer">
                            <p class="text-xs font-bold text-amber-600 uppercase tracking-wide">TOPLAM PUAN</p>
                            <p class="text-3xl font-extrabold text-amber-500 mt-1">
                                {{ number_format($user->toplam_puan ?? 0, 0, ',', '.') }}</p>
                            <span class="text-[10px] text-amber-400 block mt-1">Detaylar &rarr;</span>
                        </a>

                    @elseif($user->customer)
                        {{-- 2. MÜŞTERİ İÇİN: FİRMA BİLGİ KARTI --}}
                        <div
                            class="inline-block text-left bg-white p-4 rounded-xl border border-indigo-100 shadow-sm ring-1 ring-indigo-50">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Bağlı Olduğunuz
                                Firma</p>
                            <div class="flex items-center gap-4">
                                {{-- Firma Logosu --}}
                                <div
                                    class="h-14 w-14 flex-shrink-0 bg-gray-50 rounded-lg border border-gray-100 flex items-center justify-center p-1 overflow-hidden">
                                    @if($user->customer->logo_path)
                                        <img src="{{ asset('storage/' . $user->customer->logo_path) }}"
                                            alt="{{ $user->customer->name }}" class="h-full w-full object-contain">
                                    @else
                                        <span
                                            class="text-xl font-bold text-indigo-300">{{ substr($user->customer->name, 0, 1) }}</span>
                                    @endif
                                </div>

                                {{-- Firma Adı --}}
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 leading-tight">{{ $user->customer->name }}
                                    </h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            Kurumsal Müşteri
                                        </span>
                                        @if($user->customer->location_type)
                                            <span class="text-[10px] text-gray-400 border-l border-gray-200 pl-2">
                                                {{ $user->customer->location_type }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- İSTATİSTİK BAR (SADECE PERSONELE GÖSTER) --}}
            @if(!$isCustomer)
                <div class="border-t border-gray-100 bg-gray-50 grid grid-cols-3 divide-x divide-gray-200">
                    <div class="p-4 text-center">
                        <span class="block text-xl font-bold text-gray-800">{{ $tamamlananProjeSayisi }}</span>
                        <span class="block text-xs text-gray-500 font-medium uppercase">Tamamlanan</span>
                    </div>
                    <div class="p-4 text-center">
                        <span class="block text-xl font-bold text-gray-800">{{ $aktifProjeSayisi }}</span>
                        <span class="block text-xs text-gray-500 font-medium uppercase">Aktif Görev</span>
                    </div>
                    <div class="p-4 text-center">
                        <span class="block text-xl font-bold text-gray-800">{{ $takimlar->count() }}</span>
                        <span class="block text-xs text-gray-500 font-medium uppercase">Takım</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- === MERKEZİ BİLDİRİM ALANI (BAŞARI VE HATA) === --}}
        <div class="mb-8">
            {{-- BAŞARI MESAJLARI --}}
            @if (in_array(session('status'), ['profile-updated', 'password-updated', 'istek-gonderildi', 'istek-iptal-edildi']))
                @php
                    $s = session('status');
                    $isSuccess = in_array($s, ['profile-updated', 'password-updated', 'istek-gonderildi']);
                    $title = 'İşlem Başarılı!';
                    if ($s === 'istek-iptal-edildi') $title = 'İşlem İptal Edildi';

                    $message = '';
                    if ($s === 'profile-updated') $message = 'Profil bilgileriniz başarıyla güncellendi.';
                    elseif ($s === 'password-updated') $message = 'Şifreniz başarıyla değiştirildi.';
                    elseif ($s === 'istek-gonderildi') $message = 'Değişiklik talebiniz başarıyla gönderildi ve yönetici onayına sunuldu.';
                    elseif ($s === 'istek-iptal-edildi') $message = 'Bekleyen değişiklik talebiniz iptal edildi.';

                    $containerClasses = $isSuccess ? 'bg-emerald-50 border-emerald-200 text-emerald-800 shadow-emerald-100/50' : 'bg-indigo-50 border-indigo-200 text-indigo-800 shadow-indigo-100/50';
                    $iconBg = $isSuccess ? 'bg-emerald-500' : 'bg-indigo-500';
                    $closeBtn = $isSuccess ? 'text-emerald-400 hover:text-emerald-600' : 'text-indigo-400 hover:text-indigo-600';
                @endphp
                <div 
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-4"
                    x-init="setTimeout(() => show = false, 10000)"
                    class="flex items-center gap-4 border px-6 py-4 rounded-2xl font-bold shadow-lg {{ $containerClasses }}"
                >
                    <div class="text-white p-2 rounded-xl shadow-inner {{ $iconBg }}">
                        @if($isSuccess)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-lg">{{ $title }}</p>
                        <p class="text-sm font-medium opacity-80">
                            {{ $message }}
                        </p>
                    </div>
                    <button @click="show = false" class="ml-auto transition-colors {{ $closeBtn }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @endif

            {{-- HATA MESAJLARI (Tüm Hata Gruplarını Kapsar) --}}
            @if ($errors->any() || $errors->updatePassword->any())
                <div class="p-6 bg-red-50 border border-red-200 text-red-800 rounded-2xl font-bold shadow-lg shadow-red-100/50 flex items-center gap-4 animate-pulse">
                    <div class="bg-red-500 text-white p-2 rounded-xl shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-lg">Güncelleme İşlemi Başarısız!</p>
                        <ul class="mt-1 list-disc list-inside text-sm font-medium opacity-80">
                            {{-- Ana Hatalar --}}
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            {{-- Şifre Güncelleme Hataları (Named Bag) --}}
                            @foreach ($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        {{-- TAB YAPISI --}}
        {{-- Müşteri ise varsayılan tab 'settings', değilse 'timeline'. Gelen 'tab' parametresi varsa önceliklidir. --}}
        @php
            $defaultTab = request('tab') ? request('tab') : ($isCustomer ? 'settings' : 'timeline');
        @endphp
        <div x-data="{ activeTab: '{{ $defaultTab }}' }"
            class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <nav class="space-y-2 bg-white rounded-xl shadow-sm p-2">

                    {{-- PERSONEL BUTONLARI (Müşteri Göremez) --}}
                    @if(!$isCustomer)
                        <button @click="activeTab = 'timeline'"
                            :class="activeTab === 'timeline' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                            class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                            Son Aktiviteler
                        </button>
                        <button @click="activeTab = 'projects'"
                            :class="activeTab === 'projects' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                            class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                            Projelerim ({{ $kullaniciProjeleri->count() }})
                        </button>
                        <button @click="activeTab = 'teams'"
                            :class="activeTab === 'teams' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                            class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                            Takımlarım
                        </button>
                        <button @click="activeTab = 'observers'"
                            :class="activeTab === 'observers' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                            class="group px-4 py-3 flex items-center justify-between text-sm font-bold w-full transition-all duration-200 rounded-lg">
                            <div class="flex items-center">
                                Gözlemcilerim
                            </div>
                            @if(auth()->user()->observers->count() > 0)
                                <span class="bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                                    {{ auth()->user()->observers->count() }}
                                </span>
                            @endif
                        </button>
                    @endif

                    {{-- HESAP AYARLARI (Herkes Görür) --}}
                    <button @click="activeTab = 'settings'"
                        :class="activeTab === 'settings' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                        class="group px-4 py-3 flex items-center text-sm font-bold w-full transition-all duration-200 rounded-lg">
                        Hesap Ayarları
                    </button>

                    {{-- DİSİPLİN (Müşteri Göremez) --}}
                    @if(!$isCustomer)
                        @php
                            $disiplinSayisi = \App\Models\DisciplinaryCase::where('user_id', Auth::id())->count();
                        @endphp

                        <button @click="activeTab = 'disciplinary'"
                            :class="activeTab === 'disciplinary' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                            class="group px-4 py-3 flex items-center justify-between text-sm font-bold w-full transition-all duration-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-indigo-500"
                                    :class="activeTab === 'disciplinary' ? 'text-indigo-500' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                                Disiplin Dosyalarım ({{ $disiplinSayisi }})
                            </div>
                        </button>
                    @endif
                </nav>
            </div>

            <div class="lg:col-span-2">
                
                {{-- SADECE PERSONEL İÇERİKLERİ --}}
                @if(!$isCustomer)
                    {{-- TAB: SON AKTİVİTELER --}}
                    <div x-show="activeTab === 'timeline'" x-transition
                        class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Son Aktiviteler</h3>
                        <ul class="divide-y divide-gray-100">
                            @forelse($sonAktiviteler as $log)
                                <li class="py-4">
                                    <div class="flex space-x-3">
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-800 font-medium">{{ $log->eylem }}</p>
                                            <p class="text-sm text-gray-500">{{ $log->aciklama }}</p>
                                            <span
                                                class="text-xs text-gray-400 block mt-1">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="py-4 text-center text-gray-500 text-sm">Henüz bir aktivite yok.</li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- TAB: PROJELERİM --}}
                    <div x-show="activeTab === 'projects'" x-transition style="display: none;"
                        class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Dahil Olduğum Projeler</h3>
                        <div class="space-y-4">
                            @forelse($kullaniciProjeleri as $proje)
                                <div
                                    class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div>
                                        <a href="{{ route('proje.workspace.show', $proje->id) }}"
                                            class="font-bold text-indigo-600 hover:underline">
                                            {{ $proje->baslik }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1">Takım: {{ $proje->atananTakim->ad ?? '-' }}</p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $proje->durum == 'Tamamlandı' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $proje->durum }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-center text-gray-500">Henüz bir projede yer almıyorsunuz.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB: TAKIMLARIM --}}
                    <div x-show="activeTab === 'teams'" x-transition style="display: none;"
                        class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Takımlarım</h3>
                        <div class="grid grid-cols-1 gap-4">
                            @forelse($takimlar as $takim)
                                <div class="p-4 border rounded-lg flex items-center justify-between hover:bg-gray-50">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $takim->ad }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $takim->lider ? $takim->lider->name . ' (Lider)' : '' }}</p>
                                    </div>
                                    <div
                                        class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ substr($takim->ad, 0, 1) }}</div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 text-sm">Henüz bir takıma dahil değilsiniz.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB: GÖZLEMCİLERİM --}}
                    <div x-show="activeTab === 'observers'" x-transition style="display: none;"
                        class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                        @include('profile.partials.manage-observers')
                    </div>
                @endif

                {{-- TAB: HESAP AYARLARI (Herkes Görür) --}}
                <div x-show="activeTab === 'settings'" x-transition style="display: none;">

                    {{-- Müşteri Paneline Dön Linki (Sadece Müşteriler) --}}
                    @if($isCustomer)
                        <div
                            class="mb-6 bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-indigo-900">Müşteri Paneli</h4>
                                    <p class="text-sm text-indigo-700">Şikayet süreçlerinizi takip etmek için panele dönün.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                                Panele Git &rarr;
                            </a>
                        </div>
                    @endif

                    <div class="space-y-6">
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            @include('profile.partials.update-password-form')
                        </div>
                        {{-- Hesabı silme yetkisi sadece Superadmin'de olsun --}}
                        @if(auth()->user()->hasRole('Superadmin'))
                            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                                @include('profile.partials.delete-user-form')
                            </div>
                        @endif
                    </div>
                </div>

                {{-- TAB: DİSİPLİN (Müşteri Göremez) --}}
                @if(!$isCustomer)
                    <div x-show="activeTab === 'disciplinary'" x-transition style="display: none;">
                        <div class="bg-white shadow-sm rounded-xl p-6 border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-6">Disiplin Geçmişim</h3>

                            @php
                                $hasDisciplinaryRecord = \App\Models\DisciplinaryCase::where('user_id', Auth::id())->exists();
                            @endphp

                            @if($hasDisciplinaryRecord)
                                @include('dashboard.partials.disciplinary-waiting')
                                @include('dashboard.partials.disciplinary-active')
                            @else
                                <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                    <div
                                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4 animate-bounce">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">Siciliniz Tertemiz!</h3>
                                    <p class="mt-2 text-gray-500 max-w-sm mx-auto">
                                        Adınıza kayıtlı herhangi bir disiplin süreci veya tutanak bulunmamaktadır. Örnek
                                        çalışmalarınız için teşekkür ederiz.
                                    </p>
                                    <div
                                        class="mt-6 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-bold text-sm shadow-lg shadow-green-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5">
                                            </path>
                                        </svg>
                                        Performansa Devam
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>