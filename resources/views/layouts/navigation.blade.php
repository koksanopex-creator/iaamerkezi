@php
    // === GÜVENLİ KONTROL BLOKU ===
    // Bu değişkeni en başta tanımlıyoruz ki aşağıda hem masaüstü hem mobil menüde kullanabilelim.
    // Eğer kullanıcı giriş yapmışsa (Auth::check) veritabanından kontrol et,
    // Misafir ise (Müşteri gibi) direkt FALSE yap ki kod patlamasın.
    
    $kullaniciSikayetTakimindaMi = false;

    if(Auth::check()) {
        $kullaniciSikayetTakimindaMi = Auth::user()->takimlar()->where('tur', 'sikayet')->exists();
    }
@endphp

<nav x-data="{ open: false }" class="bg-gradient-to-r from-gray-800 via-gray-900 to-black shadow-2xl border-b border-gray-700/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center space-x-6">
                <div class="shrink-0 flex items-center group">
                    {{-- Misafir de ana sayfaya gidebilmeli --}}
                    <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="flex items-center space-x-3 transition-transform hover:scale-105">
                        <x-application-logo class="block h-10 w-auto fill-current text-white" />
                    </a>
                </div>

                {{-- Sadece GİRİŞ YAPMIŞ kullanıcılar menüleri görür --}}
                @auth
                <div class="hidden lg:flex items-center space-x-2">
                    <a href="{{ route('dashboard') }}" class="group relative px-4 py-2.5 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        <span class="font-semibold text-sm">Dashboard</span>
                        @if(request()->routeIs('dashboard'))
                            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                        @endif
                    </a>

                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" class="group flex items-center space-x-2 px-4 py-2.5 rounded-xl transition-all duration-300 {{ request()->routeIs('iaa.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            <span class="font-semibold text-sm">İAA Modülü</span>
                            <svg class="w-4 h-4 transition-transform text-gray-400 group-hover:text-white" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="open" x-transition x-cloak class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50 py-1">
                            <x-dropdown-link :href="route('iaa.index')">{{ __('İAA\'larım') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('iaa.havuz')">{{ __('İyileştirme Havuzu') }}</x-dropdown-link>
                        </div>
                    </div>

                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" class="group flex items-center space-x-2 px-4 py-2.5 rounded-xl transition-all duration-300 {{ request()->routeIs('takimlar.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            <span class="font-semibold text-sm">Takım Modülü</span>
                            <svg class="w-4 h-4 transition-transform text-gray-400 group-hover:text-white" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="open" x-transition x-cloak class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50 py-1">
                            <x-dropdown-link :href="route('takimlar.index')">{{ __('Takımlar') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('iaa.takimProjeleri')">{{ __('Takım Projelerim') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('takimlar.davetlerim')">{{ __('Davetlerim') }}</x-dropdown-link>
                        </div>
                    </div>

                    {{-- Hata veren PHP bloğunu buradan kaldırdık, en tepeye aldık --}}
                    
                    @if(Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri']) || $kullaniciSikayetTakimindaMi)
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" class="group flex items-center space-x-2 px-4 py-2.5 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.sikayetler.*') || request()->routeIs('admin.sikayet-kategorileri.*') || request()->routeIs('admin.cozum-takimlari.*') || request()->routeIs('admin.sikayet-raporlari.index') ? 'bg-white/10 text-white' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            <span class="font-semibold text-sm">Müşteri Şikayetleri</span>
                            <svg class="w-4 h-4 transition-transform text-gray-400 group-hover:text-white" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="open" x-transition x-cloak class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50 py-1">
                            <x-dropdown-link :href="route('admin.sikayetler.index')">{{ __('Şikayet Paneli') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.sikayet-raporlari.index')" :active="request()->routeIs('admin.sikayet-raporlari.index')">
                                {{ __('Şikayet Raporları') }}
                            </x-dropdown-link>
                            @role('Superadmin')
                                <div class="border-t border-gray-100 my-1"></div>
                                <x-dropdown-link :href="route('admin.sikayet-kategorileri.index')"> {{ __('Şikayet Kategorileri') }} </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.cozum-takimlari.index')">{{ __('Çözüm Takımları') }}</x-dropdown-link>
                            @endrole
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi']))
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" class="group flex items-center space-x-2 px-4 py-2.5 rounded-xl transition-all duration-300 {{ (request()->routeIs('admin.*') && !request()->routeIs('admin.sikayetler.*') && !request()->routeIs('admin.sikayet-kategorileri.*') && !request()->routeIs('admin.cozum-takimlari.*') && !request()->routeIs('admin.sikayet-raporlari.index')) ? 'bg-white/10 text-white' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            <span class="font-semibold text-sm">Yönetim Paneli</span>
                            <svg class="w-4 h-4 transition-transform text-gray-400 group-hover:text-white" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                        <div x-show="open" x-transition x-cloak class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50 py-1">
                            <x-dropdown-link :href="route('admin.iaa-yonetim.index')">{{ __('İAA Yönetimi') }}</x-dropdown-link>
                            @role('Superadmin')
                            <x-dropdown-link :href="route('admin.bolumler.index')">{{ __('Bölüm Yönetimi') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.takim-yonetim.index')">{{ __('Takım Yönetimi') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.workflows.index')">{{ __('Akış Şablonları') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.users.index')">{{ __('Kullanıcı Yönetimi') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.kalite-yoneticileri.index')">{{ __('Bölüm Kalite Yöneticileri') }}</x-dropdown-link>
                            <div class="border-t border-gray-100 my-1"></div>
                            <x-dropdown-link :href="route('admin.raporlar.index')">{{ __('İAA Raporları') }}</x-dropdown-link>
                            <div class="border-t border-gray-100 my-1"></div>
                            <x-dropdown-link :href="route('admin.sistem-ayarlari.index')">{{ __('Sistem Ayarları') }}</x-dropdown-link>
                            @endrole
                        </div>
                    </div>
                    @endif
                </div>
                @endauth
                </div>

            @auth
            <div class="hidden lg:flex items-center">
                
                <div class="notification-container relative mr-4"> 
                    <a href="#" id="notification-bell-icon" class="relative block p-2 text-gray-300 hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a6 6 0 00-6 6v3.586l-1.707 1.707A1 1 0 003 14h14a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                        <span id="notification-count-badge" class="notification-badge" style="display: none;">0</span>
                    </a>
                    <div id="notification-dropdown" class="notification-dropdown-menu" style="display: none;">
                        <div class="notification-header">Bildirimler</div>
                        <ul id="notification-list" class="notification-list-items">
                        </ul>
                        <div id="notification-empty" class="notification-empty-message" style="display: none;">
                            Yeni bildirim yok.
                        </div>
                    </div>
                </div>
                
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center space-x-3 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 transition-all duration-300 group">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-left hidden xl:block">
                                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400">{{ Auth::user()->roles->first()->name ?? 'Kullanıcı' }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-white transition-transform" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                         <div class="py-1">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profil') }}</x-dropdown-link>
                            <div class="border-t border-gray-200 my-1"></div>
                            <div class="block px-4 py-2 text-xs text-gray-400">Puan Durumu</div>
                            <x-dropdown-link :href="route('puan-durumu')">
                                <div class="flex justify-between items-center">
                                    <span>Liderlik Tablosu</span>
                                    <span class="text-sm font-bold text-indigo-600">{{ number_format(Auth::user()->toplam_puan, 0) }} Puan</span>
                                </div>
                            </x-dropdown-link>
                            <div class="border-t border-gray-200 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 hover:bg-red-50">
                                    {{ __('Çıkış Yap') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
            
            <div class="flex items-center lg:hidden">
                <button @click="open = !open" class="p-2 rounded-xl text-gray-300 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/><path :class="{'block': open, 'hidden': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endauth
            </div>
    </div>

    @auth
    <div x-show="open" x-transition x-cloak class="lg:hidden bg-gray-800/95 backdrop-blur-lg border-t border-gray-700/50">
        {{-- Mobil menü içeriğiniz --}}
        <div class="px-2 pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
        </div>
        
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4"><div class="font-medium text-base text-gray-400">İAA Modülü</div></div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('iaa.index')">{{ __('İAA\'larım') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('iaa.havuz')">{{ __('İyileştirme Havuzu') }}</x-responsive-nav-link>
            </div>
        </div>
        
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4"><div class="font-medium text-base text-gray-400">Takım Modülü</div></div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('takimlar.index')">{{ __('Takımlar') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('iaa.takimProjeleri')">{{ __('Takım Projelerim') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('takimlar.davetlerim')">{{ __('Davetlerim') }}</x-responsive-nav-link>
            </div>
        </div>
        
        @if(Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri']) || $kullaniciSikayetTakimindaMi)
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4"><div class="font-medium text-base text-gray-400">Müşteri Şikayetleri</div></div>
            <div class="mt-3 space-y-1">
                
                @role(['Superadmin', 'Müşteri Şikayeti Kurulu'])
                    <x-responsive-nav-link :href="route('admin.sikayetler.index')">{{ __('Şikayet Paneli (Yönetim)') }}</x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('sikayet-gorevlerim.index')">{{ __('Şikayet Görevlerim') }}</x-responsive-nav-link>
                @endrole
                
                <x-responsive-nav-link :href="route('admin.sikayet-raporlari.index')" :active="request()->routeIs('admin.sikayet-raporlari.index')">
                    {{ __('Şikayet Raporları') }}
                </x-responsive-nav-link>

                @role('Superadmin')
                    <x-responsive-nav-link :href="route('admin.sikayet-kategorileri.index')">{{ __('Şikayet Kategorileri') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.cozum-takimlari.index')">{{ __('Çözüm Takımları') }}</x-responsive-nav-link>
                @endrole
            </div>
        </div>
        @endif
        @role('Superadmin')
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4"><div class="font-medium text-base text-gray-400">Yönetim Paneli</div></div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('admin.iaa-yonetim.index')">{{ __('İAA Yönetimi') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.bolumler.index')">{{ __('Bölüm Yönetimi') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.takim-yonetim.index')">{{ __('Takım Yönetimi') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.workflows.index')">{{ __('Akış Şablonları') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')">{{ __('Kullanıcı Yönetimi') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.kalite-yoneticileri.index')">{{ __('Bölüm Kalite Yöneticileri') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.raporlar.index')">{{ __('İAA Raporları') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.sistem-ayarlari.index')">{{ __('Sistem Ayarları') }}</x-responsive-nav-link>
            </div>
        </div>
        @endrole
        
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-2">
                <div class="notification-container relative">
                    <a href="#" id="notification-bell-icon" class="relative flex items-center w-full px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a6 6 0 00-6 6v3.586l-1.707 1.707A1 1 0 003 14h14a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                        <span class="ml-3">Bildirimler</span>
                        <span id="notification-count-badge" class="notification-badge" style="display: none; position: relative; top: -10px; right: -5px;">0</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profil') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">@csrf<x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Çıkış Yap') }}</x-responsive-nav-link></form>
            </div>
        </div>
    </div>
    @endauth
</nav>