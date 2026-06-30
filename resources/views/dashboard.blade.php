@push('pageTitle')
    @if(isset($is_musteri_dashboard) && $is_musteri_dashboard)
        Müşteri Paneli | 
    @elseif(Auth::user()->hasRole('Superadmin'))
        Yönetici Paneli | 
    @else
        Panel | 
    @endif
@endpush

<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 tracking-tight flex items-center gap-3">
                    @if(isset($is_musteri_dashboard) && $is_musteri_dashboard)
                        {{ __('Müşteri Paneli') }}
                    @elseif(Auth::user()->hasRole('Superadmin') && ($activeDashboard ?? '') === 'superadmin')
                        {{ __('Yönetici Paneli') }}
                    @else
                        {{ __('Dashboard') }}

                        @php
                            $dashboardTitles = [
                                'superadmin' => 'Sistem Yönetici Paneli',
                                'yonetim' => 'Üst Yönetim Paneli',
                                'kurul' => 'Şikayet Kurulu Paneli',
                                'cozum_lideri' => 'Şikayet Çözüm Lideri Paneli',
                                'kalite' => 'Kalite Yöneticisi Paneli',
                                'bolum_lideri' => 'Bölüm Lideri Paneli',
                                'bolum_lider_yardimcisi' => 'Bölüm Lider Yardımcısı Paneli',
                                'direktor' => 'Direktör Paneli',
                                'hukuk' => 'Hukuk Paneli',
                                'disiplin_kurulu_baskani' => 'Disiplin Kurulu Başkanı Paneli',
                                'disiplin_kurulu_uyesi' => 'Disiplin Kurulu Üyesi Paneli',
                                'musteri_saha_temsilcisi' => 'Müşteri Saha Temsilcisi Paneli',
                                'standart' => 'Personel Paneli',
                            ];
                            $currentLabel = $dashboardTitles[$activeDashboard ?? 'standart'] ?? 'Personel Paneli';
                        @endphp
                        
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm animate-fade-in">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-2 animate-pulse"></span>
                            {{ $currentLabel }}
                        </span>
                    @endif
                </h2>
                <p class="text-gray-600 mt-1">
                    @if(isset($is_musteri_dashboard) && $is_musteri_dashboard)
                        Hoşgeldiniz, şikayet süreçlerinizi buradan takip edebilirsiniz.
                    @else
                        @php
                            $dashboardSubtitles = [
                                'kalite' => 'Bölümünüzün kalite süreçlerini ve onaylarınızı yönetin',
                                'bolum_lideri' => 'Departman performansınızı ve ekip verilerini izleyin',
                                'bolum_lider_yardimcisi' => 'Bölümünüzün operasyonel süreçlerini takip edin',
                                'direktor' => 'Sorumlu olduğunuz tüm bölümlerin özet verileri',
                                'cozum_lideri' => 'Atandığınız şikayet çözümlerini ve takım ilerlemesini takip edin',
                                'kurul' => 'Şikayet kurulu genel istatistikleri ve personel puanları',
                                'musteri_saha_temsilcisi' => 'Saha ziyaretlerinizi ve şikayet süreçlerini yönetin',
                                'standart' => 'Kendi performansınızı ve atanan görevlerinizi görüntüleyin',
                                'superadmin' => 'Tüm sistem verileri üzerinde tam kontrol',
                            ];
                            $currentSubtitle = $dashboardSubtitles[$activeDashboard ?? 'standart'] ?? 'Sistemdeki genel durumunuzu görüntüleyin';
                        @endphp
                        {{ $currentSubtitle }}
                    @endif
                </p>
            </div>
            
            @if(!(isset($is_musteri_dashboard) && $is_musteri_dashboard))
                <div class="hidden md:flex flex-col items-end gap-0.5">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-500">Sistem Aktif</span>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">{{ now()->format('d.m.Y') }}</p>
                        <p id="live-clock" class="text-xs font-mono font-semibold text-gray-600 tabular-nums"></p>
                    </div>
                </div>
                <script>
                    (function() {
                        function updateClock() {
                            const now = new Date();
                            const h = String(now.getHours()).padStart(2, '0');
                            const m = String(now.getMinutes()).padStart(2, '0');
                            const s = String(now.getSeconds()).padStart(2, '0');
                            const el = document.getElementById('live-clock');
                            if (el) el.textContent = h + ':' + m + ':' + s;
                        }
                        updateClock();
                        setInterval(updateClock, 1000);
                    })();
                </script>
            @endif
        </div>
    </x-slot>

    @php
        $bekleyenDavetSayisi = \App\Models\TakimDavetiyesi::where('davet_edilen_user_id', auth()->id())
            ->where('type', 'davet')
            ->where('durum', 'bekliyor')
            ->count();

        $pendingVisitCount = \App\Models\IaaZiyaretPlani::where('visitor_id', auth()->id())
            ->whereNotIn('status', ['Tamamlandı', 'Reddedildi', 'İptal Edildi'])
            ->count();
    @endphp

    @if($pendingVisitCount > 0)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6" x-data="{ show: true }" x-show="show" x-transition>
            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-md shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-indigo-800">
                            Gerçekleştirmeniz gereken <span class="font-bold">{{ $pendingVisitCount }} adet</span> ziyaret planı bulunmaktadır. Lütfen detayları kontrol ediniz.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 shrink-0 mt-2 sm:mt-0">
                    <a href="{{ route('admin.ziyaret-planlarim') }}" class="text-sm font-bold text-indigo-700 hover:text-indigo-900 underline bg-indigo-100/50 px-3 py-1 rounded-md transition-colors">
                        Ziyaret Planlarım &rarr;
                    </a>
                    <button @click="show = false" class="text-indigo-400 hover:text-indigo-600 focus:outline-none bg-transparent hover:bg-indigo-100 p-1.5 rounded-full transition-colors" title="Kapat">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($bekleyenDavetSayisi > 0)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md shadow-sm flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <span class="font-bold">{{ $bekleyenDavetSayisi }} adet</span> yeni takım davetiniz var!
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('takimlar.davetlerim') }}" class="text-sm font-medium text-yellow-700 hover:text-yellow-600 underline">
                        Davetleri İncele &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ONAY BEKLEYEN SSO BAŞVURULARI UYARISI (Superadmin ve Bölüm Lideri İçin) --}}
    @if((Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Bölüm Lideri')) && isset($pendingSsoApplicationsCount) && $pendingSsoApplicationsCount > 0)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="relative bg-gradient-to-r from-amber-500 via-orange-600 to-red-600 rounded-2xl shadow-xl overflow-hidden group transition-all duration-300 hover:shadow-orange-200/50">
                <div class="absolute inset-0 bg-grid-white/[0.1] [mask-image:linear-gradient(0deg,#fff,rgba(255,255,255,0.6))] pointer-events-none"></div>
                
                <div class="relative px-6 py-6 sm:px-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="flex-shrink-0 w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 transform transition-transform group-hover:scale-110 duration-500">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white leading-tight">Onay Bekleyen Kullanıcı Başvurusu!</h3>
                            <p class="text-amber-50 mt-1 max-w-md text-sm leading-relaxed">
                                Sisteme yeni kullanıcı başvuruları yapıldı. Şu anda onayınızı bekleyen <span class="font-extrabold text-white underline">{{ $pendingSsoApplicationsCount }} adet</span> başvuru bulunmaktadır.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                        <a href="{{ route('admin.users.onay_bekleyenler') }}" class="w-full sm:w-auto px-6 py-3 bg-white text-orange-700 font-extrabold rounded-xl shadow-lg hover:bg-orange-50 hover:scale-105 active:scale-95 transition-all text-center text-sm">
                            Onay İçin Tıklayın &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ŞİFRE DEÄİŞTİRME UYARISI (Banner) --}}
    @if(Auth::user()->require_password_change && !Auth::user()->dismissed_password_alert)
        <div id="password-change-banner" class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="relative bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 rounded-2xl shadow-xl overflow-hidden group transition-all duration-300 hover:shadow-indigo-200/50">
                <div class="absolute inset-0 bg-grid-white/[0.1] [mask-image:linear-gradient(0deg,#fff,rgba(255,255,255,0.6))] pointer-events-none"></div>
                
                <div class="relative px-6 py-8 sm:px-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="flex-shrink-0 w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 transform transition-transform group-hover:scale-110 duration-500">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white leading-tight">Şifrenizi Güncelleyerek Başlayın!</h3>
                            <p class="text-indigo-100 mt-1 max-w-md text-sm leading-relaxed">Güvenliğiniz için sistem tarafından atanan geçici şifrenizi değiştirmenizi öneririz. Bu işlem sadece bir dakikanızı alır.</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                        <a href="{{ route('profile.edit') }}" class="w-full sm:w-auto px-6 py-3 bg-white text-indigo-700 font-bold rounded-xl shadow-lg hover:bg-indigo-50 transition-all transform hover:-translate-y-0.5 text-center text-sm">
                            Hemen Değiştir
                        </a>
                        <button onclick="dismissPasswordBanner()" class="w-full sm:w-auto px-6 py-3 bg-indigo-500/30 text-white font-bold rounded-xl border border-white/20 hover:bg-indigo-500/50 transition-all text-sm backdrop-blur-sm">
                            Daha Sonra
                        </button>
                    </div>
                </div>

                {{-- Kapatma Çarpısı (Opsiyonel alternatif) --}}
                <button onclick="dismissPasswordBanner()" class="absolute top-4 right-4 text-white/50 hover:text-white transition-colors" title="Kapat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <script>
            function dismissPasswordBanner() {
                const banner = document.getElementById('password-change-banner');
                banner.classList.add('opacity-0', 'scale-95');
                setTimeout(() => banner.remove(), 300);

                fetch("{{ route('dashboard.dismiss-password-alert') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(response => response.json())
                  .then(data => console.log('Banner dismissed', data));
            }
        </script>
    @endif

    {{-- KÜÇÜK KALICI HATIRLATMAMA (Sticky/Small) --}}
    @if(Auth::user()->require_password_change)
        <div class="fixed top-24 right-6 z-[60] animate-bounce">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 bg-white dark:bg-gray-800 p-2 pr-4 rounded-full shadow-2xl border border-red-100 hover:border-red-500 transition-all group overflow-hidden">
                <div class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center animate-pulse">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="text-xs">
                    <p class="font-bold text-red-600">Şifre Uyarısı</p>
                    <p class="text-gray-400 text-[10px]">Henüz şifre değişmedi</p>
                </div>
            </a>
        </div>
    @endif

    {{-- DİSİPLİN OYLAMA UYARISI (Relevant Roles) --}}
    @include('dashboard.partials.disciplinary-voting-alert')

    {{-- GÖZLEMCİ MODUNA GEÇİŞ PANELİ --}}
    @if(!Auth::user()->isShadowing() && Auth::user()->observedUsers->isNotEmpty())
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative">
                <div class="absolute right-0 top-0 opacity-5 -mr-10 -mt-10">
                    <svg class="w-40 h-40 text-indigo-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                </div>
                <div class="flex items-center gap-4 relative">
                    <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Gözlemci Moduna Geç</h3>
                        <p class="text-sm text-gray-500">Yetkili olduğunuz yöneticilerin ekranına salt okunur modda geçiş yapabilirsiniz.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 relative">
                    @foreach(Auth::user()->observedUsers as $target)
                        <form action="{{ route('observer.start', $target->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-bold rounded-lg border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                <span class="mr-2">{{ $target->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    
    <style>
        [x-cloak] { display: none !important; }
        .gradient-bg { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); }
    </style>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- DASHBOARD SWITCHER (Çoklu Rolü Olanlar İçin) --}}
            @if(!(isset($is_musteri_dashboard) && $is_musteri_dashboard))
                @php
                    $user = Auth::user();
                    $availableDashboards = [];
                    if ($user->hasRole('Superadmin')) $availableDashboards['superadmin'] = 'Superadmin';
                    if ($user->hasRole('Yonetim')) $availableDashboards['yonetim'] = 'Yönetim';
                    if ($user->hasRole('Müşteri Şikayeti Kurulu')) $availableDashboards['kurul'] = 'Şikayet Kurulu';
                    if ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) $availableDashboards['cozum_lideri'] = 'Çözüm Lideri';
                    if ($user->hasRole('Bölüm Kalite Yöneticisi')) $availableDashboards['kalite'] = 'Kalite Yöneticisi';
                    if ($user->hasRole('Bölüm Lideri')) $availableDashboards['bolum_lideri'] = 'Bölüm Lideri';
                    if ($user->hasRole('Bölüm Lider Yardımcısı')) $availableDashboards['bolum_lider_yardimcisi'] = 'Bölüm Lider Yardımcısı';
                    if ($user->hasRole('Direktör')) $availableDashboards['direktor'] = 'Direktör';
                    if ($user->hasRole(['Hukuk Admini', 'Hukuk Yöneticisi'])) $availableDashboards['hukuk'] = 'Hukuk';
                    if ($user->hasRole('Disiplin Kurulu Başkanı')) $availableDashboards['disiplin_kurulu_baskani'] = 'Disiplin Başkanı';
                    if ($user->hasRole('Disiplin Kurulu Üyesi')) $availableDashboards['disiplin_kurulu_uyesi'] = 'Disiplin Üyesi';

                    if ($user->hasRole('Müşteri Saha Temsilcisi')) $availableDashboards['musteri_saha_temsilcisi'] = 'Müşteri Saha Temsilcisi';
                @endphp

                @if(count($availableDashboards) > 2)
                    <div class="mb-8 flex flex-wrap gap-2 items-center bg-white p-2 rounded-2xl shadow-sm border border-slate-100 ring-1 ring-slate-200/50">
                        <span class="text-[10px] font-black uppercase text-slate-400 px-3 py-1 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                            Panel Görünümü:
                        </span>
                        @foreach($availableDashboards as $key => $label)
                            <a href="{{ route('dashboard.switch', $key) }}" 
                               class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 {{ $activeDashboard === $key ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200 scale-105' : 'bg-slate-50 text-slate-500 hover:bg-slate-100 border border-transparent' }}">
                                {{ $viewLabel ?? $label }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif

            @if(isset($is_musteri_dashboard) && $is_musteri_dashboard)
                @include('dashboard.partials.musteri')
            
            @else
                {{-- Ortak Uyarılar ve Bileşenler --}}
                @include('dashboard.partials._alerts')
                @include('dashboard.partials.disciplinary-waiting')
                
                @if(Auth::user()->hasRole(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı']))
                    @include('dashboard.partials.disciplinary-board')
                @endif

                @include('dashboard.partials.disciplinary-active')

                {{-- BEKLEYEN PROJE DAVETLERİ --}}
                @if(isset($bekleyenProjeDavetleri) && $bekleyenProjeDavetleri->isNotEmpty())
                    @include('dashboard.partials.project-invitations')
                @endif

                {{-- Takım Katılma İstekleri Uyarısı --}}
                @include('dashboard.partials.waiting-requests')

                {{-- Puan Kartı --}}
                @unless(Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Yonetim') || Auth::user()->hasRole('Direktör'))
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-2xl shadow-lg text-white mb-8">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-indigo-200 uppercase tracking-wider">Toplam Puanınız</p>
                                <p class="text-4xl font-black tracking-tight">{{ number_format(Auth::user()->toplam_puan, 0) }}</p>
                            </div>
                            <a href="{{ route('puan-durumu') }}" class="text-indigo-200 hover:text-white transition-colors" title="Liderlik Tablosu">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </a>
                        </div>
                    </div>
                @endunless

                {{-- Dinamik Dashboard İçeriği --}}
                @if(isset($stats))
                    @php
                        $activeDashboard = $activeDashboard ?? 'standart';
                    @endphp

                    @if($activeDashboard === 'superadmin')
                        @include('dashboard.partials.superadmin')
                    @elseif($activeDashboard === 'yonetim')
                        @include('dashboard.partials.yonetim')
                    @elseif($activeDashboard === 'kurul')
                        @include('dashboard.partials.sikayet-kurulu')
                        @include('dashboard.partials.standart-kullanici')
                    @elseif($activeDashboard === 'hukuk')
                        @include('dashboard.partials.hukuk')
                        <div class="my-12 border-t border-slate-200"></div>
                        @include('dashboard.partials.standart-kullanici')
                    @elseif($activeDashboard === 'cozum_lideri')
                        <div x-data="{ activeTab: 'lider' }" class="space-y-6">
                            
                            <!-- Üst Başlık ve Bilgi (Direktör Stili) -->
                            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                                <div class="absolute right-0 top-0 opacity-10 -mr-16 -mt-16">
                                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <div class="relative z-10 flex flex-col xl:flex-row justify-between items-start gap-8">
                                    <div>
                                        <h3 class="text-2xl font-bold">Çözüm Lideri Genel Bakış</h3>
                                        @if(isset($stats['sorumlu_oldugu_bolumler']) && count($stats['sorumlu_oldugu_bolumler']) > 0)
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-white/90">Sorumlu Bölümler:</span>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($stats['sorumlu_oldugu_bolumler'] as $bolum)
                                                        <span class="px-3 py-1 bg-white text-purple-700 rounded-lg text-[10px] font-black border border-white shadow-sm ring-4 ring-white/10">{{ $bolum }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        <p class="text-purple-100 mt-2 text-sm leading-relaxed">Sorumlu olduğunuz şikayet süreçlerini ve bireysel performansınızı buradan yönetebilirsiniz.</p>
                                    </div>
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 w-full lg:w-auto">
                                        <a href="{{ route('admin.sikayetler.index') }}" class="text-center px-4 py-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-lg hover:bg-white/20 transition-all cursor-pointer group/card flex flex-col min-h-[160px]">
                                            <div class="mb-4">
                                                <p class="text-[9px] font-black uppercase tracking-widest opacity-80 mb-1 text-indigo-100 group-hover/card:text-white transition-colors">Toplam Şikayet</p>
                                                <p class="text-3xl font-black text-white leading-none">{{ $stats['toplam_sikayet_sayisi_all_time'] ?? 0 }}</p>
                                            </div>
                                            <div class="mt-auto pt-3 border-t border-white/10 space-y-1.5">
                                                @foreach($stats['sorumlu_oldugu_bolumler'] as $bolum)
                                                    <div class="flex justify-between items-center text-[10px] font-bold">
                                                        <span class="opacity-80 truncate mr-2 text-white">{{ $bolum }}</span>
                                                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-white min-w-[20px]">{{ $stats['breakdown']['toplam'][$bolum] ?? 0 }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-[8px] opacity-60 font-black mt-3 text-indigo-50 uppercase tracking-tighter">Tümünü Gör &rarr;</p>
                                        </a>

                                        <a href="#takim-listesi" class="text-center px-4 py-4 bg-blue-500/20 backdrop-blur-md rounded-2xl border border-blue-400/30 shadow-lg hover:bg-blue-500/30 transition-all cursor-pointer group/card flex flex-col min-h-[160px]">
                                            <div class="mb-4">
                                                <p class="text-[9px] font-black uppercase tracking-widest opacity-80 mb-1 text-blue-100 group-hover/card:text-white transition-colors">Aktif Şikayet</p>
                                                <p class="text-3xl font-black text-white leading-none">{{ $stats['aktif_sikayet_count_all_time'] ?? 0 }}</p>
                                            </div>
                                            <div class="mt-auto pt-3 border-t border-white/10 space-y-1.5">
                                                @foreach($stats['sorumlu_oldugu_bolumler'] as $bolum)
                                                    <div class="flex justify-between items-center text-[10px] font-bold">
                                                        <span class="opacity-80 truncate mr-2 text-white">{{ $bolum }}</span>
                                                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-white min-w-[20px]">{{ $stats['breakdown']['aktif'][$bolum] ?? 0 }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-[8px] opacity-60 font-black mt-3 text-blue-50 uppercase tracking-tighter">Listeye Git &rarr;</p>
                                        </a>

                                        <a href="{{ route('admin.sikayetler.index', ['tab' => 'cozulmus']) }}" class="text-center px-4 py-4 bg-emerald-500/20 backdrop-blur-md rounded-2xl border border-emerald-400/30 shadow-lg hover:bg-emerald-500/30 transition-all cursor-pointer group/card flex flex-col min-h-[160px]">
                                            <div class="mb-4">
                                                <p class="text-[9px] font-black uppercase tracking-widest opacity-80 mb-1 text-emerald-100 group-hover/card:text-white transition-colors">Tamamlanan</p>
                                                <p class="text-3xl font-black text-white leading-none">{{ $stats['cozulen_sikayetler_count_all_time'] ?? 0 }}</p>
                                            </div>
                                            <div class="mt-auto pt-3 border-t border-white/10 space-y-1.5">
                                                @foreach($stats['sorumlu_oldugu_bolumler'] as $bolum)
                                                    <div class="flex justify-between items-center text-[10px] font-bold">
                                                        <span class="opacity-80 truncate mr-2 text-white">{{ $bolum }}</span>
                                                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-white min-w-[20px]">{{ $stats['breakdown']['tamamlanan'][$bolum] ?? 0 }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-[8px] opacity-60 font-black mt-3 text-emerald-50 uppercase tracking-tighter">Çözülenleri Gör &rarr;</p>
                                        </a>

                                        <a href="#onay-bekleyenler" class="text-center px-4 py-4 bg-amber-500/20 backdrop-blur-md rounded-2xl border border-amber-400/30 shadow-lg hover:bg-amber-500/30 transition-all cursor-pointer group/card flex flex-col min-h-[160px]">
                                            <div class="mb-4">
                                                <p class="text-[9px] font-black uppercase tracking-widest opacity-80 mb-1 text-amber-100 group-hover/card:text-white transition-colors">Onay Bekleyen</p>
                                                <p class="text-3xl font-black text-white leading-none">{{ $stats['onay_bekleyen_sikayetler_count_all_time'] ?? 0 }}</p>
                                            </div>
                                            <div class="mt-auto pt-3 border-t border-white/10 space-y-1.5">
                                                @foreach($stats['sorumlu_oldugu_bolumler'] as $bolum)
                                                    <div class="flex justify-between items-center text-[10px] font-bold">
                                                        <span class="opacity-80 truncate mr-2 text-white">{{ $bolum }}</span>
                                                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-white min-w-[20px]">{{ $stats['breakdown']['onay_bekleyen'][$bolum] ?? 0 }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-[8px] opacity-60 font-black mt-3 text-amber-50 uppercase tracking-tighter">İncele &rarr;</p>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Dashboard Sekmeleri -->
                            <div class="flex p-1 bg-gray-100/50 backdrop-blur-sm rounded-xl w-full md:w-max border border-gray-200/50">
                                <button @click="activeTab = 'lider'"
                                        :class="activeTab === 'lider' ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 md:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Çözüm Lideri Paneli ({{ $stats['aktif_sikayetler_count_all_time'] ?? count($stats['aktif_sikayetler_projeler'] ?? []) }})
                                </button>
                                <button @click="activeTab = 'kisisel'"
                                        :class="activeTab === 'kisisel' ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 md:flex-none px-6 py-2.5 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Kişisel Durum
                                </button>
                            </div>

                            <div x-show="activeTab === 'lider'" x-cloak x-transition:enter="transition-opacity duration-300" class="animate-fade-in">
                                @include('dashboard.partials.cozum-lideri')
                            </div>

                            <div x-show="activeTab === 'kisisel'" x-cloak x-transition:enter="transition-opacity duration-300" class="animate-fade-in">
                                @include('dashboard.partials.standart-kullanici')
                            </div>
                        </div>
                    @elseif($activeDashboard === 'kalite')
                        @include('dashboard.partials.bolum-yoneticisi')
                    @elseif($activeDashboard === 'direktor')
                        @include('dashboard.partials.direktor')
                    @elseif($activeDashboard === 'bolum_lideri' || $activeDashboard === 'bolum_lider_yardimcisi')
                        @if(Auth::user()->bolum_id)
                            <div class="mb-6 flex justify-end">
                                <a href="{{ route('admin.bolumler.dashboard', Auth::user()->bolum_id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    Bölüm Paneline Git
                                </a>
                            </div>
                        @endif
                        @include('dashboard.partials._department-leader')
                    @elseif($activeDashboard === 'disiplin_kurulu_baskani' || $activeDashboard === 'disiplin_kurulu_uyesi')
                        {{-- Sekme Navigasyonu --}}
                        <div class="mb-8 flex border-b border-slate-200">
                            <button id="disc-btn-personal" onclick="discSwitchTab('personal')"
                                    class="px-6 py-3 border-b-2 border-indigo-600 text-indigo-600 font-bold text-sm transition-all duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Genel Bakış
                            </button>
                            <button id="disc-btn-board" onclick="discSwitchTab('board')"
                                    class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-bold text-sm transition-all duration-200 flex items-center gap-2 relative">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Disiplin Kurulu Paneli
                                @if(($stats['toplanti_bekleyen_sayisi'] ?? 0) > 0 || ($stats['onay_bekleyen_sayisi'] ?? 0) > 0)
                                    <span class="absolute top-2 right-2 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                    </span>
                                @endif
                            </button>
                        </div>

                        {{-- Sekme İçerikleri --}}
                        <div id="disc-panel-personal">
                            @include('dashboard.partials.standart-kullanici')
                        </div>

                        <div id="disc-panel-board" style="display:none">
                            @if($activeDashboard === 'disiplin_kurulu_baskani')
                                @include('dashboard.partials._disiplin_kurulu_baskani')
                            @elseif($activeDashboard === 'disiplin_kurulu_uyesi')
                                @include('dashboard.partials._disiplin_kurulu_uyesi')
                            @else
                                <div class="p-12 text-center bg-white rounded-2xl border-2 border-dashed border-slate-100">
                                    <p class="text-slate-400">Panel içeriği yüklenemedi.</p>
                                </div>
                            @endif
                        </div>

                        <script>
                        function discSwitchTab(tab) {
                            var pPersonal = document.getElementById('disc-panel-personal');
                            var pBoard    = document.getElementById('disc-panel-board');
                            var bPersonal = document.getElementById('disc-btn-personal');
                            var bBoard    = document.getElementById('disc-btn-board');
                            if (!pPersonal || !pBoard) return;

                            pPersonal.style.display = (tab === 'personal') ? '' : 'none';
                            pBoard.style.display    = (tab === 'board')    ? '' : 'none';

                            // Aktif buton stili
                            var active   = ['border-indigo-600', 'text-indigo-600'];
                            var inactive = ['border-transparent', 'text-slate-500'];
                            if (tab === 'personal') {
                                active.forEach(c => bPersonal.classList.add(c));
                                inactive.forEach(c => bPersonal.classList.remove(c));
                                inactive.forEach(c => bBoard.classList.add(c));
                                active.forEach(c => bBoard.classList.remove(c));
                            } else {
                                active.forEach(c => bBoard.classList.add(c));
                                inactive.forEach(c => bBoard.classList.remove(c));
                                inactive.forEach(c => bPersonal.classList.add(c));
                                active.forEach(c => bPersonal.classList.remove(c));
                            }
                        }
                        </script>
                    @elseif($activeDashboard === 'musteri_saha_temsilcisi')
                        @include('dashboard.partials.musteri-saha-temsilcisi')
                        <div class="my-12 border-t border-slate-200"></div>
                        @include('dashboard.partials.standart-kullanici')
                    @elseif(Auth::user()->isMaviYaka())
                        @include('dashboard.partials.mavi-yaka')
                    @else
                        @include('dashboard.partials.standart-kullanici')
                    @endif
                @endif

            @endif

        </div>
    </div>
</x-app-layout>
