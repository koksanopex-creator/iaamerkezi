@php
    // === YETKİ KONTROLLERİ ===
    $kullaniciSikayetTakimindaMi = false;
    $disiplinYetkisi = false;
    $arabuluculukYetkisi = false;
    $hukukMenuYetkisi = false;

    if (Auth::check()) {
        $kullaniciSikayetTakimindaMi = Auth::user()->takimlar()->where('tur', 'sikayet')->exists();

        $isTeamLeader = \App\Models\Takim::where('lider_user_id', Auth::id())->exists();

        // Disiplin Yetkisi
        $disiplinYetkisi = Auth::user()->hasRole([
            'Superadmin',
            'Yonetim',
            'Yönetim',
            'Hukuk Yöneticisi',
            'Hukuk Admini',
            'Disiplin Kurulu Başkanı',
            'Disiplin Kurulu Üyesi',
            'Bölüm Lideri',
            'Direktör'
        ]) || Auth::user()->can_issue_disciplinary;

        // Arabuluculuk Yetkisi
        $arabuluculukYetkisi = Auth::user()->hasRole([
            'Superadmin',
            'Yonetim',
            'Yönetim',
            'Hukuk Yöneticisi',
            'Hukuk Admini',
            'Arabuluculuk Personel',
            'Arabuluculuk Personel Lideri',
            'Arabuluculuk Finans',
            'Direktör'
        ]);

        // Ana Hukuk Menüsü Görünsün mü?
        $hukukMenuYetkisi = $disiplinYetkisi || $arabuluculukYetkisi;
    }

    $isCustomerUser = Auth::check() && Auth::user()->customer_id != null;
@endphp

{{-- MENÜ ARKA PLANI: Pastel Grimsi/Mavimsi Ton --}}
<nav x-data="{ mobileOpen: false }"
    class="bg-slate-100 border-b border-slate-200 sticky top-0 z-50 shadow-sm h-20 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex justify-between h-full items-center">

            {{-- SOL TARA: LOGO ve MASAÜSTÜ MENÜ --}}
            <div class="flex items-center gap-6">

                {{-- LOGO ALANI --}}
                <div class="shrink-0 flex items-center h-full">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 group transition-transform hover:scale-105">
                        {{-- Logo Simgesi (Sabit Boyut - FOUC Önlemek için min-w eklendi) --}}
                        <div class="flex items-center justify-center h-14 w-auto min-w-[3.5rem]">
                            <x-application-logo class="block h-14 w-auto fill-current text-slate-700" />
                        </div>
                        {{-- PORTAL Yazısı --}}
                        <span class="font-black text-2xl text-slate-800 tracking-tight">PORTAL</span>
                    </a>
                </div>

                {{-- MASAÜSTÜ LİNKLERİ --}}
                <div class="hidden lg:flex items-center space-x-2">

                    @auth
                        {{-- 1. DASHBOARD --}}
                        @if(!Auth::user()->hasRole('Yonetim'))
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:text-indigo-600 hover:bg-white/60' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                    </path>
                                </svg>
                                Dashboard
                            </a>
                        @endif


                        {{-- 3. UYGULAMALAR --}}
                        {{-- 3. İYİLEŞTİRME (İAA) --}}
                        @if(!Auth::user()->hasRole('Yonetim') && !$isCustomerUser)
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('iaa.*') && !request()->routeIs('iaa.takimProjeleri') ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:text-indigo-600 hover:bg-white/60' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span>İyileştirme</span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="{'rotate-180': open}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition.origin.top.left
                                    class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 py-2 z-50">
                                    <x-dropdown-link :href="route('iaa.index')">İAA'larım</x-dropdown-link>
                                    <x-dropdown-link :href="route('iaa.havuz')">İyileştirme Havuzu</x-dropdown-link>

                                    @if(Auth::user()->hasRole(['Yonetim', 'Yönetim', 'Superadmin']))
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <x-dropdown-link :href="route('admin.raporlar.index')"
                                            class="text-indigo-600 font-semibold">
                                            İAA Raporları
                                        </x-dropdown-link>
                                    @endif
                                </div>
                            </div>

                            {{-- 3.5 ÇALIŞMA ALANI (Takımlar & Projeler) --}}
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('takimlar.*') || request()->routeIs('iaa.takimProjeleri') ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:text-indigo-600 hover:bg-white/60' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <span>Çalışma Alanı</span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="{'rotate-180': open}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition.origin.top.left
                                    class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 py-2 z-50">
                                    <x-dropdown-link :href="route('takimlar.index')">Takımlar</x-dropdown-link>
                                    <x-dropdown-link :href="route('iaa.takimProjeleri')">Takım Projelerim</x-dropdown-link>
                                    <x-dropdown-link :href="route('takimlar.davetlerim')">Davetlerim</x-dropdown-link>

                                    @if($isTeamLeader)
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <x-dropdown-link :href="route('takimlar.isteklerim')" class="text-indigo-600 font-semibold">
                                            Gelen İstekler
                                        </x-dropdown-link>
                                    @endif

                                    @if(!$isCustomerUser)
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <x-dropdown-link :href="route('user-directory.index')">
                                            Kullanıcı Rehberi
                                        </x-dropdown-link>
                                    @endif

                                    @if(Auth::user()->hasRole('Bölüm Lideri') && Auth::user()->bolum_id)
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <x-dropdown-link :href="route('admin.bolumler.dashboard', Auth::user()->bolum_id)"
                                            class="text-teal-600 font-semibold">
                                            Bölüm Paneli
                                        </x-dropdown-link>
                                    @endif

                                    @if(Auth::user()->hasRole('Direktör'))
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <x-dropdown-link :href="route('dashboard')" class="text-emerald-600 font-semibold">
                                            Direktör Paneli
                                        </x-dropdown-link>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- 4. HUKUK --}}
                        @if($hukukMenuYetkisi)
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.disiplin.*') || request()->routeIs('admin.arabuluculuk.*') ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:text-indigo-600 hover:bg-white/60' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3">
                                        </path>
                                    </svg>
                                    <span>Hukuk</span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="{'rotate-180': open}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition.origin.top.left
                                    class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 py-2 z-50 overflow-hidden">

                                    @if($disiplinYetkisi)
                                        <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Disiplin</span>
                                        </div>
                                        <x-dropdown-link :href="route('admin.disiplin.index')">Disiplin Dosyaları</x-dropdown-link>
                                        @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                            <x-dropdown-link :href="route('admin.disiplin.create')">Yeni Tutanak
                                                Oluştur</x-dropdown-link>
                                        @endif
                                        @if(Auth::user()->hasRole('Bölüm Lideri'))
                                            <x-dropdown-link :href="route('admin.disiplin.sorumlular.index')">Sorumlu
                                                Yönetimi</x-dropdown-link>
                                        @endif
                                    @endif

                                    @if($arabuluculukYetkisi)
                                        <div class="border-t border-slate-100"></div>
                                        <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                                            <span
                                                class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Arabuluculuk</span>
                                        </div>
                                        <x-dropdown-link :href="route('admin.arabuluculuk.index')">Dosya Listesi</x-dropdown-link>
                                        @if(Auth::user()->hasRole(['Arabuluculuk Personel', 'Hukuk Admini', 'Superadmin']))
                                            <x-dropdown-link :href="route('admin.arabuluculuk.create')">Yeni Dosya
                                                Başlat</x-dropdown-link>
                                        @endif
                                    @endif

                                    @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi']))
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <div class="px-4 py-1 text-[10px] text-slate-400 font-bold uppercase">Tanımlamalar</div>
                                        <x-dropdown-link :href="route('admin.disiplin.settings.index')">Disiplin
                                            Ayarları</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.arabulucular.index')">Arabulucu
                                            Listesi</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.dis_avukatlar.index')">Dış Avukatlar</x-dropdown-link>
                                        <x-nav-link :href="route('admin.arabuluculuk.tanim.anlasmaMaddeleri')"
                                            :active="request()->routeIs('admin.arabuluculuk.tanim.anlasmaMaddeleri')">
                                            {{ __('Anlaşma Maddeleri') }}
                                        </x-nav-link>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- 5. MÜŞTERİ --}}
                        @if(Auth::user()->hasRole(['Superadmin', 'Yonetim', 'Yönetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi', 'Direktör']) || $kullaniciSikayetTakimindaMi)
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.sikayetler.*') ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:text-indigo-600 hover:bg-white/60' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <span>Müşteri</span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="{'rotate-180': open}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition.origin.top.left
                                    class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 py-2 z-50">
                                    <x-dropdown-link :href="route('admin.sikayetler.index')">Şikayet Paneli</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.sikayet-raporlari.index')">Raporlar</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.musteriler.index')">Müşteri Listesi</x-dropdown-link>
                                    @if(Auth::user()->hasRole(['Superadmin', 'Yönetim', 'Bölüm Kalite Yöneticisi', 'Bölüm Lideri', 'Müşteri Şikayeti Kurulu']))
                                        <x-dropdown-link :href="route('admin.reports.daily_complaints')"
                                            class="text-pink-600 font-semibold">Günlük Şikayet Raporu</x-dropdown-link>
                                    @endif

                                    @role('Superadmin')
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-1 text-[10px] text-slate-400 font-bold uppercase">Tanımlar</div>
                                    <x-dropdown-link :href="route('admin.sikayet-kategorileri.index')">Şikayet
                                        Kategorileri</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.cozum-takimlari.index')">Çözüm
                                        Takımları</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.iade-ayarlari.index')">İade
                                        Parametreleri</x-dropdown-link>
                                    @endrole
                                </div>
                            </div>
                        @endif

                        {{-- 6. YÖNETİCİ MENÜSÜ --}}
                        @if(Auth::user()->hasRole(['Superadmin', 'Yonetim', 'Bölüm Kalite Yöneticisi']))
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.iaa-yonetim.*') || request()->routeIs('admin.users.*') ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:text-indigo-600 hover:bg-white/60' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>Yönetici</span>
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="{'rotate-180': open}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak x-transition.origin.top.left
                                    class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 py-2 z-50 max-h-[80vh] overflow-y-auto custom-scrollbar">
                                    @if(Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                                        <x-dropdown-link :href="route('yonetim.index')" class="font-bold text-indigo-600">Yönetim
                                            Raporu</x-dropdown-link>
                                        @role('Superadmin')
                                        <x-dropdown-link :href="route('machine-logs.index')">Makine İşlem Geçmişi</x-dropdown-link>
                                        @endrole
                                        {{-- REHBER (Yönetim İçin) --}}
                                        <x-dropdown-link :href="route('user-directory.index')">Kullanıcı Rehberi</x-dropdown-link>

                                        <div class="border-t border-slate-100 my-1"></div>
                                    @endif
                                    <div class="px-4 py-1 text-[10px] font-bold text-slate-400 uppercase mt-1">İAA Yönetimi
                                    </div>
                                    <x-dropdown-link :href="Auth::user()->hasRole(['Yonetim', 'Yönetim']) ? route('admin.raporlar.index') : route('admin.iaa-yonetim.index')">
                                        {{ Auth::user()->hasRole(['Yonetim', 'Yönetim']) ? 'İAA Raporları' : 'İAA Listesi' }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.raporlar.index')">Raporlar</x-dropdown-link>
                                    @role('Superadmin')
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-1 text-[10px] font-bold text-slate-400 uppercase">Sistem</div>
                                    <x-dropdown-link :href="route('admin.users.index')">Kullanıcılar</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.bolumler.index')">Bölümler</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.bolum-kategorileri.index')">Bölüm
                                        Kategorileri</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.takim-yonetim.index')">Takımlar</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.workflows.index')">Akış Şablonları</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.kalite-yoneticileri.index')">Kalite
                                        Yöneticileri</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.direktorler.index')">Direktör
                                        Atamaları</x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.sistem-ayarlari.index')">Sistem
                                        Ayarları</x-dropdown-link>
                                    @endrole
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- SAĞ TARA: BİLDİRİM VE PROFİL (MASAÜSTÜ & MOBİL ORTAK) --}}
            {{-- ======================================================= --}}
            @auth
                <div class="flex items-center gap-2 lg:gap-4">

                    {{-- 1. BİLDİRİM ZİLİ (Hem Mobil Hem Masaüstünde Görünür) --}}
                    <div class="relative notification-container" x-data="{ open: false }">
                        <button type="button" @click="open = !open" id="notification-bell-icon"
                            class="relative p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-200 rounded-full focus:outline-none transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span id="notification-count-badge"
                                class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] text-white shadow-sm"
                                style="display: none;">0</span>
                        </button>
                        {{-- DROPDOWN: KART GÖRÜNÜMLÜ BİLDİRİMLER (CSS ile Stile Edildi) --}}
                        <div x-show="open" x-cloak @click.away="open = false" id="notification-dropdown"
                            class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">Bildirimler</div>
                            {{-- NOT: Buradaki liste JavaScript ile dolduruluyor. --}}
                            {{-- [&_li] ile child li elementlerine KART stili veriyoruz --}}
                            <ul id="notification-list"
                                class="max-h-80 overflow-y-auto custom-scrollbar p-2 space-y-2 [&_li]:bg-slate-50 [&_li]:rounded-lg [&_li]:p-3 [&_li]:border [&_li]:border-slate-100 [&_li]:cursor-pointer [&_li]:transition-colors [&_li]:hover:bg-indigo-50">
                            </ul>
                            <div id="notification-empty" class="px-4 py-3 text-sm text-slate-500 text-center hidden">Yeni
                                bildirim yok.</div>
                            <div class="border-t border-slate-100 mt-2 pt-2 px-4 text-center">
                                <a href="#" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Tümünü Gör</a>
                            </div>
                        </div>
                    </div>

                    {{-- 2. PROFİL (Sadece Masaüstünde, Mobilde Menü İçinde) --}}
                    <div class="relative hidden lg:block" x-data="{ open: false }" @click.away="open = false">
                        <button type="button" @click="open = !open"
                            class="flex items-center gap-2 focus:outline-none group">
                            <div class="text-right">
                                <div class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-[10px] text-slate-500 uppercase">
                                    {{ Auth::user()->roles->first()->name ?? 'Kullanıcı' }}
                                </div>
                            </div>
                            @if(Auth::user()->profile_photo_path)
                                <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm group-hover:border-indigo-200 transition-colors"
                                    src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                    alt="{{ Auth::user()->name }}" />
                            @else
                                <div
                                    class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm group-hover:bg-indigo-200 transition-colors">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.right
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 py-1 z-50">
                            <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                            @if(!$isCustomerUser)
                                <x-dropdown-link :href="route('puan-durumu')">
                                    <span class="flex justify-between w-full">
                                        Liderlik <span
                                            class="text-green-600 font-bold">{{ number_format(Auth::user()->toplam_puan, 0) }}</span>
                                    </span>
                                </x-dropdown-link>

                                <div class="border-t border-slate-100 my-1"></div>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                                    Çıkış Yap
                                </x-dropdown-link>
                            </form>
                        </div>
                    </div>

                    {{-- 3. MOBİL MENÜ BUTONU --}}
                    <div class="flex items-center lg:hidden">
                        <button type="button" @click="mobileOpen = !mobileOpen"
                            class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 focus:outline-none">
                            <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen }" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen }" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MOBİL MENÜ İÇERİĞİ (EKSİKSİZ TAM LİSTE) --}}
    {{-- ======================================================= --}}
    <div x-show="mobileOpen" x-cloak
        class="lg:hidden border-t border-slate-200 bg-white max-h-[calc(100vh-80px)] overflow-y-auto shadow-inner">
        @auth
            <div class="pt-2 pb-3 space-y-1 px-2">

                <x-responsive-nav-link :href="route('dashboard')"
                    :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>


                {{-- Mobil - İyileştirme --}}
                @if(!Auth::user()->hasRole('Yonetim') && !$isCustomerUser)
                    <div class="bg-indigo-50 px-3 py-1 mt-2 text-xs font-bold text-indigo-400 uppercase">İyileştirme</div>
                    <x-responsive-nav-link :href="route('iaa.index')">İAA'larım</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('iaa.havuz')">İyileştirme Havuzu</x-responsive-nav-link>

                    @if(Auth::user()->hasRole(['Yonetim', 'Yönetim', 'Superadmin']))
                        <x-responsive-nav-link :href="route('admin.raporlar.index')"
                            :active="request()->routeIs('admin.raporlar.index')" class="text-indigo-600 font-semibold">İAA
                            Raporları</x-responsive-nav-link>
                    @endif

                    <div class="bg-indigo-50 px-3 py-1 mt-2 text-xs font-bold text-indigo-400 uppercase">Çalışma Alanı</div>
                    <x-responsive-nav-link :href="route('takimlar.index')">Takımlar</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('iaa.takimProjeleri')">Projelerim</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('takimlar.davetlerim')">Davetlerim</x-responsive-nav-link>

                    @if(!$isCustomerUser)
                        <x-responsive-nav-link :href="route('user-directory.index')">Kullanıcı Rehberi</x-responsive-nav-link>
                    @endif

                    @if($isTeamLeader)
                        <x-responsive-nav-link :href="route('takimlar.isteklerim')"
                            class="text-indigo-600 font-bold bg-indigo-50/50">
                            Gelen Katılma İstekleri
                        </x-responsive-nav-link>
                    @endif
                @endif

                {{-- Mobil Hukuk --}}
                @if($hukukMenuYetkisi)
                    <div class="bg-slate-50 px-3 py-1 mt-2 text-xs font-bold text-slate-400 uppercase">Hukuk</div>

                    @if($disiplinYetkisi)
                        <div class="px-4 py-1 text-[10px] text-slate-400 font-bold uppercase mt-1">Disiplin</div>
                        <x-responsive-nav-link :href="route('admin.disiplin.index')">Disiplin
                            Dosyaları</x-responsive-nav-link>
                        @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                            <x-responsive-nav-link :href="route('admin.disiplin.create')">Yeni Tutanak</x-responsive-nav-link>
                        @endif
                        @if(Auth::user()->hasRole('Bölüm Lideri'))
                            <x-responsive-nav-link :href="route('admin.disiplin.sorumlular.index')">Sorumlu
                                Yönetimi</x-responsive-nav-link>
                        @endif
                    @endif

                    @if($arabuluculukYetkisi)
                        <div class="px-4 py-1 text-[10px] text-slate-400 font-bold uppercase mt-1">Arabuluculuk</div>
                        <x-responsive-nav-link :href="route('admin.arabuluculuk.index')">Dosya
                            Listesi</x-responsive-nav-link>
                        @if(Auth::user()->hasRole(['Arabuluculuk Personel', 'Hukuk Admini', 'Superadmin']))
                            <x-responsive-nav-link :href="route('admin.arabuluculuk.create')">Yeni Dosya
                                Başlat</x-responsive-nav-link>
                        @endif
                    @endif

                    @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi']))
                        <div class="px-4 py-1 text-[10px] text-slate-400 font-bold uppercase mt-1">Tanımlar</div>
                        <x-responsive-nav-link :href="route('admin.disiplin.settings.index')">Disiplin
                            Ayarları</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.arabulucular.index')">Arabulucu
                            Listesi</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.dis_avukatlar.index')">Dış
                            Avukatlar</x-responsive-nav-link>
                    @endif
                @endif

                {{-- Mobil Müşteri --}}
                @if(Auth::user()->hasRole(['Superadmin', 'Yonetim', 'Yönetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Direktör']) || $kullaniciSikayetTakimindaMi)
                    <div class="bg-slate-50 px-3 py-1 mt-2 text-xs font-bold text-slate-400 uppercase">Müşteri</div>
                    <x-responsive-nav-link :href="route('admin.sikayetler.index')">Şikayet
                        Paneli</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.sikayet-raporlari.index')">Raporlar</x-responsive-nav-link>
                    @if(Auth::user()->hasRole(['Superadmin', 'Yönetim', 'Bölüm Kalite Yöneticisi', 'Bölüm Lideri', 'Müşteri Şikayeti Kurulu']))
                        <x-responsive-nav-link :href="route('admin.reports.daily_complaints')"
                            class="text-pink-600 font-semibold">Günlük Şikayet Raporu</x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('admin.musteriler.index')">Müşteri Listesi</x-responsive-nav-link>
                    @role('Superadmin')
                    <div class="px-4 py-1 text-[10px] text-slate-400 font-bold uppercase mt-1">Tanımlar</div>
                    <x-responsive-nav-link :href="route('admin.sikayet-kategorileri.index')">Şikayet
                        Kategorileri</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.cozum-takimlari.index')">Çözüm
                        Takımları</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.iade-ayarlari.index')">İade
                        Parametreleri</x-responsive-nav-link>
                    @endrole
                @endif

                {{-- Mobil Yönetim --}}
                @if(Auth::user()->hasRole(['Superadmin', 'Yonetim', 'Bölüm Kalite Yöneticisi']))
                    <div class="bg-slate-50 px-3 py-1 mt-2 text-xs font-bold text-slate-400 uppercase">Yönetim</div>

                    @if(Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                        <x-responsive-nav-link :href="route('yonetim.index')">Yönetim Raporu</x-responsive-nav-link>
                        @role('Superadmin')
                        <x-responsive-nav-link :href="route('machine-logs.index')">Makine İşlem Geçmişi</x-responsive-nav-link>
                        @endrole
                        <x-responsive-nav-link :href="route('user-directory.index')">Kullanıcı Rehberi</x-responsive-nav-link>
                    @endif

                    <x-responsive-nav-link :href="Auth::user()->hasRole(['Yonetim', 'Yönetim']) ? route('admin.raporlar.index') : route('admin.iaa-yonetim.index')">
                        {{ Auth::user()->hasRole(['Yonetim', 'Yönetim']) ? 'İAA Raporları' : 'İAA Yönetimi' }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.raporlar.index')">İAA
                        Raporları</x-responsive-nav-link>

                    @role('Superadmin')
                    <div class="px-4 py-1 text-[10px] text-slate-400 font-bold uppercase mt-1">Sistem</div>
                    <x-responsive-nav-link :href="route('admin.users.index')">Kullanıcılar</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.bolumler.index')">Bölümler</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.takim-yonetim.index')">Takımlar</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.workflows.index')">Akış
                        Şablonları</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.kalite-yoneticileri.index')">Kalite
                        Yöneticileri</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.sistem-ayarlari.index')">Sistem
                        Ayarları</x-responsive-nav-link>
                    @endrole
                @endif

            </div>

            {{-- Mobil Profil --}}
            <div class="pt-4 pb-4 border-t border-slate-200 bg-slate-50">
                <div class="px-4 flex items-center">
                    <div class="shrink-0">
                        @if(Auth::user()->profile_photo_path)
                            <img class="h-10 w-10 rounded-full object-cover"
                                src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                alt="{{ Auth::user()->name }}" />
                        @else
                            <div
                                class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-3">
                        <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1 px-2">
                    <x-responsive-nav-link :href="route('profile.edit')">Profil</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">Çıkış
                            Yap</x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>