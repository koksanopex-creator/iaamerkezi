@php
    // === YETKİ KONTROLLERİ ===
    $kullaniciSikayetTakimindaMi = false;
    $disiplinYetkisi = false;
    $arabuluculukYetkisi = false;
    $hukukMenuYetkisi = false;
    $availableDashboards = [];
    $activeDashboard = null;

    if (Auth::check()) {
        $kullaniciSikayetTakimindaMi = Auth::user()->takimlar()->where('tur', 'sikayet')->exists();

        $isTeamLeader = \App\Models\Takim::where('lider_user_id', Auth::id())->exists();

        // Disiplin Yetkisi (Portal Yönetimi için)
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
        ]) || Auth::user()->can_issue_disciplinary || Auth::user()->hasBolumAuthority('bolum.disiplin.gor') || Auth::user()->hasBolumAuthority('bolum.disiplin.sorumlu_yonet');

        // Kendi disiplin dosyası var mı? (Herkes için)
        $hasDisciplineFiles = Auth::user()->disiplinDosyalari()->exists();

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

        // === DASHBOARD GEÇİŞ HESAPLAMASI ===
        $dashboardLabels = [
            'superadmin' => ['label' => 'Sistem Genel Bakış', 'icon' => '🛡️'],
            'yonetim' => ['label' => 'Yönetim Paneli', 'icon' => '📊'],
            'kurul' => ['label' => 'Şikayet Kurulu', 'icon' => '📋'],
            'cozum_lideri' => ['label' => 'Çözüm Lideri', 'icon' => '⚡'],
            'kalite' => ['label' => 'Kalite Yönetimi', 'icon' => '🔍'],
            'bolum_lideri' => ['label' => 'Bölüm Lideri', 'icon' => '🏢'],
            'bolum_lider_yardimcisi' => ['label' => 'Bölüm Lider Yardımcısı', 'icon' => '🛡️'],
            'direktor' => ['label' => 'Direktör Paneli', 'icon' => '👔'],
            'hukuk' => ['label' => 'Hukuk Paneli', 'icon' => '⚖️'],
            'musteri_saha_temsilcisi' => ['label' => 'Saha Temsilcisi', 'icon' => '🌍'],
            'standart' => ['label' => 'Personel Paneli', 'icon' => '👤'],
        ];
        $roleToKey = [
            'Superadmin' => 'superadmin',
            'Yonetim' => 'yonetim',
            'Müşteri Şikayeti Kurulu' => 'kurul',
            'Müşteri Şikayeti Çözüm Lideri' => 'cozum_lideri',
            'Bölüm Kalite Yöneticisi' => 'kalite',
            'Bölüm Lideri' => 'bolum_lideri',
            'Bölüm Lider Yardımcısı' => 'bolum_lider_yardimcisi',
            'Direktör' => 'direktor',
            'Müşteri Saha Temsilcisi' => 'musteri_saha_temsilcisi',
        ];
        foreach ($roleToKey as $role => $key) {
            if (Auth::user()->hasRole($role)) {
                $availableDashboards[$key] = $dashboardLabels[$key];
            }
        }
        if (Auth::user()->hasRole(['Hukuk Admini', 'Hukuk Yöneticisi'])) {
            $availableDashboards['hukuk'] = $dashboardLabels['hukuk'];
        }
        if (empty($availableDashboards)) {
            $availableDashboards['standart'] = $dashboardLabels['standart'];
        }
        $activeDashboard = session('active_dashboard_' . Auth::id(), array_key_first($availableDashboards));
    }
    
    if (Auth::check()) {
        // === BEKLEYEN DAVETLER (Takım + Proje) VE İSTEKLER ===
        $takimInviteCount = \App\Models\TakimDavetiyesi::where('davet_edilen_user_id', Auth::id())
            ->where('type', 'davet')
            ->where('durum', 'bekliyor')
            ->count();
            
        $projeInviteCount = Auth::user()->gorevliOlduguProjeler()
            ->wherePivot('durum', 'bekliyor')
            ->count();

        // Takım Liderlerine Gelen Katılma İstekleri (Yeni)
        $pendingRequestsCount = \App\Models\TakimDavetiyesi::where('davet_edilen_user_id', Auth::id())
            ->where('type', 'istek')
            ->where('durum', 'bekliyor')
            ->count();
            
        // Savunma bekleyen disiplin dosyalarım
        $myDefenseWaitingCount = Auth::user()->disiplinDosyalari()->where('durum', 'Savunma Bekleniyor')->count();
            
        $pendingInvitesCount = $takimInviteCount + $projeInviteCount + $pendingRequestsCount + $myDefenseWaitingCount;

        // === ONAL/DEĞERLENDİRME BEKLEYENLER ===
        $user = Auth::user();
        
        // 1. Hukuk (Disiplin)
        $discReviewCount = 0;
        $kuruldaBekleyenSayisi = 0;
        if ($user->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])) {
            $discReviewCount = \App\Models\DisciplinaryCase::where('durum', 'Yönetici Değerlendirmesi')->count();
            
            $kuruldaBekleyenSayisi = \App\Models\DisciplinaryCase::where('durum', 'Kurulda')->count();

            // Eğer Kurul Portalı yetkisi varsa VEYA Kurul Üyesi/Başkanı ise, Kurulda bekleyenleri de sayaca ekle
            if ($user->can('disiplin.kurul.portal.gor') || $user->hasRole(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])) {
                $discReviewCount += $kuruldaBekleyenSayisi;
            }
        }

        // 2. Müşteri (Şikayetler)
        $sikayetApprovalCount = 0;
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            $sikayetApprovalCount += \App\Models\MusteriSikayeti::where('musteri_durum', 'Yönetici Onayı Bekliyor')->count();
        }
        if ($user->hasRole('Bölüm Lideri')) {
            $sikayetApprovalCount += \App\Models\MusteriSikayeti::where('musteri_durum', 'Bölüm Onayı Bekliyor')
                ->whereHas('sikayetKategori', function($q) use ($user) {
                    $q->where('bolum_id', $user->bolum_id);
                })->count();
        }
        if ($user->hasRole('Direktör')) {
            $sikayetApprovalCount += \App\Models\MusteriSikayeti::where('musteri_durum', 'Direktör Onayı Bekliyor')
                ->whereHas('sikayetKategori', function($q) use ($user) {
                    $q->whereIn('bolum_id', \App\Models\Bolum::where('director_id', $user->id)->pluck('id'));
                })->count();
        }
        if ($user->hasRole(['Hukuk Admini', 'Hukuk Yöneticisi'])) {
            $sikayetApprovalCount += \App\Models\MusteriSikayeti::where('musteri_durum', 'Hukuk Onayı Bekliyor')->count();
        }

        // 2.2 Müşteri (Aktif Görevler - Atanan İşler)
        $pendingSikayetGorevCount = 0;
        $activeSikayetStatuses = ['Yeni', 'Atandı', 'İşlemde', 'İnceleniyor', 'Devam Ediyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Hukuk Onayı Bekliyor'];

        // Superadmin tümünü görme ayarı
        $superadminSikayetSayacAktif = \App\Models\Setting::get('superadmin_sikayet_panel_sayac_aktif', '0');

        if ($user->hasRole('Superadmin') && $superadminSikayetSayacAktif == '1') {
            $pendingSikayetGorevCount = \App\Models\MusteriSikayeti::whereIn('musteri_durum', $activeSikayetStatuses)->count();
        } else {
            $pendingSikayetGorevCount = \App\Models\MusteriSikayeti::whereIn('musteri_durum', $activeSikayetStatuses)
                ->where(function($q) use ($user) {
                    // Varsayılan olarak hiçbir şeyi getirme (0), sadece aşağıdaki orWhere koşulları eşleşirse getir.
                    $q->whereRaw('1=0');

                    // Direktör: Sorumlu olduğu bölümler
                    $managedBolumIds = \App\Models\Bolum::where('director_id', $user->id)->pluck('id');
                    if ($managedBolumIds->isNotEmpty()) {
                        $q->orWhereHas('sikayetKategori', function($k) use ($managedBolumIds) {
                            $k->whereIn('bolum_id', $managedBolumIds);
                        });
                    }
                    // Bölüm Lideri: Kendi bölümü
                    if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
                        $q->orWhereHas('sikayetKategori', function($k) use ($user) {
                            $k->where('bolum_id', $user->bolum_id);
                        });
                    }
                    // Takım Üyesi/Lideri: Atanan takım
                    $myTeamIds = $user->takimlar()->pluck('takimlar.id');
                    if ($myTeamIds->isNotEmpty()) {
                        $q->orWhereIn('atanan_cozum_takimi_id', $myTeamIds);
                    }
                    // Kalite Yöneticisi: Sorumlu olduğu kategoriler
                    $managedCatIds = $user->yonettigiSikayetKategorileri()->pluck('sikayet_kategorileri.id');
                    if ($managedCatIds->isNotEmpty()) {
                        $q->orWhereIn('sikayet_kategorisi_id', $managedCatIds);
                    }
                })->count();
        }

        if ($user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Müşteri Şikayeti Çözüm Lideri'])) {
            $q = \App\Models\MusteriSikayeti::where('musteri_durum', 'Yeni');
            if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
                if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                    $q->where('konum_tipi', 'Yurt İçi');
                } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                    $q->where('konum_tipi', 'Yurt Dışı');
                }
            }
            $unassignedSikayetCount = $q->count();
        } else {
            $unassignedSikayetCount = 0;
        }

        // 3. İyileştirme (İAA) - Onay Bekleyenler
        $iaaApprovalCount = 0;
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            $iaaApprovalCount = \App\Models\Iaa::where('durum', 'Yönetici Onayı Bekliyor')->count();
        } elseif ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $iaaApprovalCount = \App\Models\Iaa::where('durum', 'Bölüm Onayı Bekliyor')
                ->where('bolum_id', $user->bolum_id)->count();
        }
        if ($user->hasRole('Direktör')) {
            $iaaApprovalCount += \App\Models\Iaa::where('durum', 'Direktör Onayı Bekliyor')
                ->whereIn('bolum_id', \App\Models\Bolum::where('director_id', $user->id)->pluck('id'))
                ->count();
        }

        // İAA Yönetim Paneli için Toplam Sayaç (Onay Bekleyenler + Havuz/Talepler)
        $iaaYonetimToplamSayac = 0;
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            $triyajCount = \App\Models\Iaa::sadeceOneriler()->where('durum', 'Onay Bekliyor')->count();
            $talepCount = \App\Models\Iaa::sadeceOneriler()->where('durum', 'Havuzda')->has('talepEdenTakimlar')->count();
            $iaaYonetimToplamSayac = $triyajCount + $talepCount;
        } elseif ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = \App\Models\Bolum::where('director_id', $user->id)->pluck('id')->toArray();
            $triyajCount = \App\Models\Iaa::sadeceOneriler()->where('durum', 'Onay Bekliyor')->whereIn('bolum_id', $yonetilenBolumIds)->count();
            $iaaYonetimToplamSayac = $triyajCount;
        }

        // 4. Sistem (Onay Bekleyen Kullanıcılar ve İstekler)
        $pendingUserCount = 0;
        $istekCount = 0;
        if ($user->hasRole('Superadmin')) {
            $pendingUserCount = \App\Models\User::where('onaylandi_mi', 0)->whereNull('rejected_at')->count();
            $istekCount = \App\Models\KullaniciIstek::where('durum', 'bekliyor')->count();
        } elseif ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
            $pendingUserCount = \App\Models\User::where('onaylandi_mi', 0)->whereNull('rejected_at')->where('bolum_id', $user->bolum_id)->count();
        }

        // 5. Mail Bildirim Log Sayacı
        $mailLogUnresolvedCount = \App\Helpers\MailLogHelper::getUnresolvedCount($user);

        // 6. Ziyaret Planlarım Sayacı (Ziyaretçiler İçin)
        $pendingVisitCount = \App\Models\IaaZiyaretPlani::where('visitor_id', Auth::id())
            ->whereNotIn('status', ['Tamamlandı', 'Reddedildi', 'İptal Edildi'])
            ->count();

        // 7. Yönetici Ziyaret Onay Sayacı (Müşteri Ziyaretleri İçin)
        $managerPendingVisitCount = 0;
        if ($user->hasRole(['Bölüm Kalite Yöneticisi', 'Direktör', 'Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $allowedBolumler = $user->getAllowedBolumIds();
            $query = \App\Models\IaaZiyaretPlani::where('status', 'Beklemede');
            if ($allowedBolumler !== '*') {
                $query->whereHas('iaa', function ($q) use ($allowedBolumler) {
                    $q->whereIn('bolum_id', $allowedBolumler)
                        ->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($allowedBolumler) {
                            $sq->whereIn('bolum_id', $allowedBolumler);
                        });
                });
            } else {
                if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
                    if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                        $query->whereHas('iaa.musteriSikayeti', function($sq) { $sq->where('konum_tipi', 'Yurt İçi'); });
                    } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                        $query->whereHas('iaa.musteriSikayeti', function($sq) { $sq->where('konum_tipi', 'Yurt Dışı'); });
                    }
                }
            }
            $managerPendingVisitCount = $query->count();
        }

        // 8. Müşteri Ziyaretleri - Tamamlanmayanlar (Yeni Sayaç)
        $notCompletedVisitCount = \App\Models\IaaZiyaretPlani::getNotCompletedCountForUser($user);
    }

    $isCustomerUser = Auth::check() && Auth::user()->customer_id != null;
@endphp

{{-- İKİ KATMANLI NAVİGASYON TASARIMI --}}
<nav x-data="{ mobileOpen: false }"
    class="sticky top-0 z-50 shadow-md font-sans">
    
    {{-- ÜST KATMAN: MARKA + KULLANICI (KOYU TEMA - bg-slate-900) --}}
    <div class="bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex justify-between items-center text-white">
            
            {{-- Sol: Logo ve Marka --}}
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 transition-transform hover:scale-105">
                    <div class="flex items-center justify-center h-10 w-auto">
                        <x-application-logo class="block h-10 w-auto fill-current text-white" />
                    </div>
                    <span class="font-black text-xl text-white tracking-tight uppercase">Portal</span>
                </a>
            </div>

            {{-- Sağ: Bildirim ve Profil --}}
            <div class="flex items-center gap-2 lg:gap-4">
                
                {{-- Merkezi Yönetim'e Dön Butonu --}}
                <a href="{{ rtrim(env('CENTRAL_SSO_URL', 'http://localhost:8001'), '/') }}/dashboard"
                   class="hidden sm:flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-lg text-sm font-bold transition-all shadow-sm border border-indigo-500 group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Merkezi Yönetim
                </a>

                {{-- 1. BİLDİRİM ZİLİ (Masaüstü & Mobil Ortak) --}}
                @auth
                <div class="relative notification-container">
                    <a href="#" id="notification-bell-icon"
                        class="relative p-2 text-slate-300 hover:text-white hover:bg-slate-800 rounded-full focus:outline-none transition-colors group">
                        <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <span id="notification-count-badge" class="notification-badge" style="display: none;">0</span>
                    </a>
                    <div id="notification-dropdown" class="notification-dropdown-menu" style="display: none;">
                        <div id="notification-header-container"></div>
                        <ul id="notification-list" class="notification-list-items"></ul>
                        <div id="notification-empty" class="notification-empty-container" style="display: none;">
                            <svg class="notification-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            <p class="notification-empty-text">Yeni bildiriminiz yok.</p>
                        </div>
                        <div id="notification-footer-container"></div>
                    </div>
                </div>

                {{-- 2. PROFİL (Sadece Masaüstü - Mobil versiyon Drawer içinde) --}}
                <div class="relative hidden lg:block" x-data="{ open: false }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="flex items-center gap-3 focus:outline-none group">
                        <div class="text-right leading-tight hidden lg:block">
                            <div class="text-sm font-bold text-slate-100 group-hover:text-indigo-400 transition-colors">
                                @if(Auth::user()->isShadowing())
                                    {{ Auth::user()->getRealAttribute('name') }} <span class="text-[11px] text-amber-400 font-black tracking-tight">(👉 {{ Auth::user()->name }})</span>
                                @else
                                    {{ Auth::user()->name }}
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                                @if(Auth::user()->hasRole('Bölüm Lider Yardımcısı'))
                                    BÖLÜM LİDER YARDIMCISI
                                @elseif(Auth::user()->hasRole('Bölüm Lideri'))
                                    BÖLÜM LİDERİ
                                @else
                                    {{ Auth::user()->roles->first()->name ?? 'Personel' }}
                                @endif
                            </div>
                        </div>
                        <div class="relative">
                            @if(Auth::user()->profile_photo_path)
                                <img class="h-9 w-9 rounded-full object-cover border-2 border-slate-700 group-hover:border-indigo-500 transition-all shadow-md"
                                    src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                    alt="{{ Auth::user()->name }}" />
                            @else
                                <div class="h-9 w-9 rounded-full bg-slate-800 flex items-center justify-center text-indigo-400 font-bold text-xs border-2 border-slate-700 group-hover:border-indigo-500 transition-all shadow-md">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right
                        class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-10 py-2 z-50 text-slate-700">
                        <x-dropdown-link :href="route('profile.edit')">Profil Bilgilerim</x-dropdown-link>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" 
                                class="text-rose-600 hover:bg-rose-50 font-semibold">
                                Güvenli Çıkış
                            </x-dropdown-link>
                        </form>
                    </div>
                </div>
                @endauth

                {{-- 3. MOBİL MENÜ BUTONU --}}
                <div class="flex items-center lg:hidden">
                    <button type="button" @click="mobileOpen = !mobileOpen"
                        class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none transition-all">
                        <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            {{-- Hamburger (Menü Kapalıyken) --}}
                            <path class="inline-flex" :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen }" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            {{-- X İkonu (Menü Açıkken) --}}
                            <path class="hidden" x-cloak :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen }" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border-b border-slate-200 hidden lg:block">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 h-12 flex items-center justify-between">
            
            @auth
                {{-- 1. DASHBOARD GEÇİŞİ --}}
                @if(!Auth::user()->hasRole('Yonetim'))
                    <div class="flex items-center h-full">
                        @if(count($availableDashboards) > 1)
                            <div class="relative h-full flex items-center" x-data="{ open: false }" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                    <span>{{ $availableDashboards[$activeDashboard]['icon'] }} {{ $availableDashboards[$activeDashboard]['label'] }}</span>
                                    <svg class="w-3 h-3 opacity-50 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" x-cloak x-transition.origin.top
                                    class="absolute left-1/2 -translate-x-1/2 top-full mt-1 w-56 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-10 py-2 z-50">
                                    @foreach($availableDashboards as $key => $info)
                                        <a href="{{ route('dashboard.switch', $key) }}"
                                            class="flex items-center gap-2.5 px-4 py-2 text-sm transition-colors {{ $activeDashboard === $key ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-indigo-600' }}">
                                            <span class="text-base">{{ $info['icon'] }}</span>
                                            <span>{{ $info['label'] }}</span>
                                            @if($activeDashboard === $key)
                                                <svg class="ml-auto w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ route('dashboard') }}"
                                class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                Dashboard
                            </a>
                        @endif
                    </div>
                @endif


                {{-- 2. İYİLEŞTİRME --}}
                @if(!Auth::user()->hasRole('Yonetim') && !$isCustomerUser)
                    <div class="flex items-center h-full">
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @click.away="open = false">
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-bold transition-all {{ request()->routeIs('iaa.*') && !request()->routeIs('iaa.takimProjeleri') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>İyileştirme</span>
                                <svg class="w-3 h-3 opacity-50 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition.origin.top
                                class="absolute left-1/2 -translate-x-1/2 top-full mt-1 w-52 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-10 py-2 z-50">
                                
                                {{-- G1: OPERASYON --}}
                                <div class="px-4 py-2.5 bg-indigo-700 border-b border-indigo-800 text-[13px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                    <span class="w-1.5 h-4 bg-white rounded-full shadow-sm"></span>
                                    İYİLEŞTİRME SÜREÇLERİ
                                </div>
                                <x-dropdown-link :href="route('iaa.index')">İyileştirmelerim (İAA)</x-dropdown-link>
                                <x-dropdown-link :href="route('iaa.havuz')">İyileştirme Havuzu</x-dropdown-link>
                                
                                {{-- G2: ANALİZ --}}
                                @if(Auth::user()->hasRole(['Yonetim', 'Yönetim', 'Superadmin']))
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-2 bg-indigo-50 text-[11px] font-black text-indigo-600 uppercase tracking-widest">RAPORLAMA</div>
                                    <x-dropdown-link :href="route('admin.raporlar.index')" class="text-indigo-700 font-bold">İyileştirme Raporları</x-dropdown-link>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 3. ÇALIŞMA ALANI --}}
                @if(!$isCustomerUser)
                    <div class="flex items-center h-full">
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @click.away="open = false">
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-bold transition-all {{ request()->routeIs('takimlar.*') || request()->routeIs('iaa.takimProjeleri') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="relative">
                                    Çalışma Alanı
                                    @if($pendingInvitesCount > 0)
                                        <span class="absolute -top-1.5 -right-2.5 flex h-2.5 w-2.5 focus:outline-none">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                                        </span>
                                    @endif
                                </span>
                                <svg class="w-3 h-3 opacity-50 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition.origin.top
                                class="absolute left-1/2 -translate-x-1/2 top-full mt-1 w-64 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-10 py-2 z-50">
                                
                                {{-- G1: TAKIMLAR --}}
                                @if(!Auth::user()->hasRole('Yonetim'))
                                    <div class="px-4 py-2.5 bg-emerald-700 border-b border-emerald-800 text-[13px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-4 bg-white rounded-full shadow-sm"></span>
                                        TAKIM VE PROJELER
                                    </div>
                                    <x-dropdown-link :href="route('takimlar.index')">Takımlarım</x-dropdown-link>
                                    <x-dropdown-link :href="route('iaa.takimProjeleri')">Takım Projelerim</x-dropdown-link>
                                @endif
                                
                                @if($hasDisciplineFiles)
                                    <x-dropdown-link :href="route('admin.disiplin.index')" class="flex items-center justify-between group">
                                        <div class="flex items-center gap-2 text-rose-600 font-bold">
                                            <svg class="w-4 h-4 text-rose-500 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            <span>Disiplin Dosyalarım</span>
                                        </div>
                                        @if($myDefenseWaitingCount > 0)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full shadow-sm animate-pulse">{{ $myDefenseWaitingCount }}</span>
                                        @endif
                                    </x-dropdown-link>
                                @endif
                                
                                {{-- G2: İSTEKLER --}}
                                @if(!Auth::user()->hasRole('Yonetim'))
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-1 text-[11px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50">DAVET VE İSTEKLER</div>
                                    <x-dropdown-link :href="route('takimlar.davetlerim')" class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span>Takım Davetlerim</span>
                                        </div>
                                        @if(($takimInviteCount + $projeInviteCount) > 0)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">{{ $takimInviteCount + $projeInviteCount }}</span>
                                        @endif
                                    </x-dropdown-link>

                                    {{-- Gelen Katılma İstekleri (Mithat Ekin vb.) --}}
                                    @if($isTeamLeader || Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                                        <x-dropdown-link :href="route('takimlar.isteklerim')" class="flex items-center justify-between group">
                                            <div class="flex items-center gap-2 text-indigo-600 font-bold group-hover:text-indigo-700">
                                                <span>📩 Gelen Katılma İstekleri</span>
                                            </div>
                                            @if($pendingRequestsCount > 0)
                                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full shadow-sm">{{ $pendingRequestsCount }}</span>
                                            @endif
                                        </x-dropdown-link>
                                    @endif
                                @endif

                                {{-- G3: BİLGİ VE ANALİTİK --}}
                                <div class="border-t border-slate-100 my-1"></div>
                                <div class="px-4 py-1 text-[11px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50">BİLGİ VE ANALİTİK</div>
                                <x-dropdown-link :href="route('user-directory.index')" class="text-slate-500 italic flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.432.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    <span>Kullanıcı Rehberi</span>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('puan-durumu')" class="flex justify-between items-center group">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        <span class="text-xs font-bold text-slate-600">Liderlik Puanım</span>
                                    </div>
                                    <span class="text-emerald-600 font-black px-1.5 py-0.5 rounded-md text-[10px] bg-emerald-50">
                                        {{ number_format(Auth::user()->toplam_puan, 0) }}
                                    </span>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('tum-bolum-puanlari')" class="text-indigo-700 font-bold group flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                                    <span>Bölüm Puanları Analizi</span>
                                </x-dropdown-link>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 4. HUKUK --}}
                @if($hukukMenuYetkisi)
                    <div class="flex items-center h-full">
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @click.away="open = false">
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-bold transition-all {{ request()->routeIs('admin.disiplin.*') || request()->routeIs('admin.arabuluculuk.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                <span class="relative">
                                    Hukuk
                                    @if($discReviewCount > 0)
                                        <span class="absolute -top-1.5 -right-2.5 flex h-2.5 w-2.5 focus:outline-none">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                                        </span>
                                    @endif
                                </span>
                                <svg class="w-3 h-3 opacity-50 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition.origin.top.right
                                class="absolute right-0 top-full mt-1 w-64 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-10 py-2 z-50 max-h-[80vh] overflow-y-auto custom-scrollbar">
                                @if($disiplinYetkisi)
                                    <div class="px-4 py-2.5 bg-indigo-700 border-b border-indigo-800 text-[15px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-5 bg-white rounded-full shadow-sm"></span>
                                        DİSİPLİN
                                    </div>
                                    @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                                        <x-dropdown-link :href="route('admin.disiplin.create')" class="bg-indigo-50/50 font-bold text-indigo-700">Yeni Tutanak Oluştur</x-dropdown-link>
                                    @endif
                                    <x-dropdown-link :href="route('admin.disiplin.index')" class="flex items-center justify-between">
                                        <span>Disiplin Dosyaları</span>
                                        @if($discReviewCount - $kuruldaBekleyenSayisi > 0)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">{{ $discReviewCount - $kuruldaBekleyenSayisi }}</span>
                                        @endif
                                    </x-dropdown-link>

                                    @if(Auth::user()->hasRole('Bölüm Lideri') || Auth::user()->hasBolumAuthority('bolum.disiplin.sorumlu_yonet'))
                                        <x-dropdown-link :href="route('admin.disiplin.sorumlular.index')" class="text-indigo-600 font-bold border-l-2 border-indigo-500">
                                            🛡️ Sorumlu Yönetimi
                                        </x-dropdown-link>
                                    @endif
                                    @if(Auth::user()->can('disiplin.kurul.portal.gor') || Auth::user()->hasRole(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Yonetim']))
                                        <x-dropdown-link :href="route('admin.disiplin.kurul.index')" class="text-indigo-700 font-black bg-indigo-50 border-y border-indigo-100/50 flex items-center justify-between group">
                                            <div class="flex items-center gap-2">
                                                <span>⚖️ Disiplin Kurulu Portalı</span>
                                                @if($kuruldaBekleyenSayisi > 0)
                                                    <span class="text-[11px] text-rose-600 font-black animate-pulse">({{ $kuruldaBekleyenSayisi }})</span>
                                                @endif
                                            </div>
                                            @if($kuruldaBekleyenSayisi > 0)
                                                <span class="flex h-2 w-2 relative">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-600"></span>
                                                </span>
                                            @endif
                                        </x-dropdown-link>
                                    @endif
                                    {{-- Disiplin Raporu --}}
                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Bölüm Lideri', 'Direktör']) || Auth::user()->can('disiplin.rapor.gor'))
                                        <x-dropdown-link :href="route('admin.disiplin.report')" class="text-indigo-600 font-bold flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Disiplin Raporu
                                        </x-dropdown-link>
                                    @endif
                                @endif

                                @if($arabuluculukYetkisi)
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-2.5 bg-emerald-700 border-b border-emerald-800 text-[15px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-5 bg-white rounded-full shadow-sm"></span>
                                        ARABULUCULUK
                                    </div>
                                    @if(Auth::user()->hasRole(['Arabuluculuk Personel', 'Hukuk Admini', 'Superadmin']))
                                        <x-dropdown-link :href="route('admin.arabuluculuk.create')" class="bg-emerald-50/50 font-bold text-emerald-700">Yeni Dosya Başlat</x-dropdown-link>
                                    @endif
                                    <x-dropdown-link :href="route('admin.arabuluculuk.index')">Dosya Listesi</x-dropdown-link>
                                    @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Yonetim']))
                                        <x-dropdown-link :href="route('admin.arabulucular.index')">Arabulucu Listesi</x-dropdown-link>
                                    @endif
                                @endif

                                @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Yonetim']))
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-2.5 bg-slate-700 border-b border-slate-800 text-[15px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-5 bg-white rounded-full shadow-sm"></span>
                                        AYARLAR
                                    </div>
                                    @if(Auth::user()->can('disiplin.ayarlar.gor'))
                                        <x-dropdown-link :href="route('admin.disiplin.settings.index')">Disiplin Ayarları</x-dropdown-link>
                                    @endif
                                    @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']))
                                        <x-dropdown-link :href="route('admin.disiplin.hukuk-matrisi.index')" class="text-indigo-600 font-bold border-l-2 border-indigo-500">🛡️ Hukuk Yetki Matrisi</x-dropdown-link>
                                    @endif
                                    @if(Auth::user()->can('arabuluculuk.tanimlar.gor') || Auth::user()->hasRole('Yonetim'))
                                        <x-dropdown-link :href="route('admin.arabuluculuk.tanim.anlasmaMaddeleri')">Anlaşma Maddeleri</x-dropdown-link>
                                    @endif
                                    @if(Auth::user()->can('dis-avukatlar.gor') || Auth::user()->hasRole('Yonetim'))
                                        <x-dropdown-link :href="route('admin.dis_avukatlar.index')">Dış Avukatlar</x-dropdown-link>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 5. MÜŞTERİ (Organik Bağ Kontrolü Eklendi) --}}
                @if(Auth::user()->hasSikayetOrganikBagi())
                    <div class="flex items-center h-full">
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @click.away="open = false">
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-bold transition-all {{ request()->routeIs('admin.sikayetler.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="relative">
                                    Müşteri
                                    @if($sikayetApprovalCount > 0 || $pendingSikayetGorevCount > 0 || $unassignedSikayetCount > 0 || $pendingVisitCount > 0 || $managerPendingVisitCount > 0 || $notCompletedVisitCount > 0)
                                        <span class="absolute -top-1.5 -right-2.5 flex h-2.5 w-2.5 focus:outline-none">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ ($unassignedSikayetCount > 0 || $pendingVisitCount > 0 || $managerPendingVisitCount > 0 || $notCompletedVisitCount > 0) ? 'bg-rose-500' : 'bg-rose-400' }} opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ ($unassignedSikayetCount > 0 || $pendingVisitCount > 0 || $managerPendingVisitCount > 0 || $notCompletedVisitCount > 0) ? 'bg-rose-600' : 'bg-rose-500' }}"></span>
                                        </span>
                                    @endif
                                </span>
                                <svg class="w-3 h-3 opacity-50 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition.origin.top.right
                                class="absolute right-0 top-full mt-1 w-56 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-10 py-2 z-50 max-h-[80vh] overflow-y-auto custom-scrollbar">
                                
                                {{-- 1. ŞİKAYET VE ANALİZ --}}
                                @if(Auth::user()->canSeeMusteriOperasyonlari() || $kullaniciSikayetTakimindaMi)
                                    <div class="px-4 py-2.5 bg-rose-700 border-b border-rose-800 text-[13px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-4 bg-white rounded-full shadow-sm"></span>
                                        ŞİKAYET VE ANALİZ
                                    </div>
                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Bölüm Kalite Yöneticisi', 'Müşteri Şikayeti Çözüm Lideri', 'Direktör', 'Bölüm Lideri', 'Müşteri Saha Temsilcisi']) || Auth::user()->hasBolumAuthority('bolum.sikayet.gor'))
                                    <x-dropdown-link :href="route('admin.sikayetler.index')" class="flex items-center justify-between font-bold {{ $unassignedSikayetCount > 0 ? 'text-rose-700 bg-rose-50' : '' }}">
                                        <span>Şikayet Paneli</span>
                                        @if($pendingSikayetGorevCount > 0 || $unassignedSikayetCount > 0)
                                            <div class="flex items-center gap-1">
                                                @if($unassignedSikayetCount > 0)
                                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-black leading-none text-white bg-rose-600 rounded-full animate-bounce shadow-sm ring-1 ring-white" title="Atama Bekleyen Yeni Şikayet">
                                                        {{ $unassignedSikayetCount }} YENİ
                                                    </span>
                                                @endif
                                                @if($pendingSikayetGorevCount > 0)
                                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-slate-500 rounded-full">
                                                        {{ $pendingSikayetGorevCount }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.sikayet-raporlari.index')">Şikayet Raporları</x-dropdown-link>
                                    @if(\App\Models\ReportRoleAuthorization::getAuthorizationForUser(Auth::user(), 'analiz_raporu'))
                                        <x-dropdown-link :href="route('admin.musteri-sikayet-analiz-raporu')" class="text-indigo-600 font-bold bg-indigo-50/20">📊 Şikayet Analiz Raporu</x-dropdown-link>
                                    @endif
                                    @if(\App\Models\ReportRoleAuthorization::getAuthorizationForUser(Auth::user(), 'karsilastirma_raporu'))
                                        <x-dropdown-link :href="route('admin.musteri-sikayet-karsilastirma')" class="text-indigo-600 font-bold bg-indigo-50/20">📈 Kıyaslama Raporu</x-dropdown-link>
                                    @endif
                                    @endif
                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Bölüm Kalite Yöneticisi', 'Müşteri Şikayeti Çözüm Lideri', 'Direktör', 'Bölüm Lideri', 'Müşteri Saha Temsilcisi']) || Auth::user()->hasBolumAuthority('bolum.iade.gor'))
                                    <x-dropdown-link :href="route('admin.sikayet-iade-raporlari.index')" class="text-rose-600 font-bold bg-rose-50/20">♻️ İadeler Raporu</x-dropdown-link>
                                    @endif
                                    
                                    {{-- MÜŞTERİ HATIRLATMALARI (Modül A & C) --}}
                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Müşteri Şikayeti Çözüm Lideri', 'Direktör', 'Bölüm Lideri', 'Müşteri Saha Temsilcisi']) || Auth::user()->hasBolumAuthority('bolum.sikayet.bildirim'))
                                        <x-dropdown-link :href="route('admin.sikayet-hatirlatma.index')" class="text-indigo-600 font-bold bg-indigo-50/50 flex justify-between items-center group">
                                            <span>Müşteri Hatırlatmaları</span>
                                            <div class="flex items-center gap-1">
                                                @php
                                                    $user = auth()->user();
                                                    $navPendingCount = \App\Models\SikayetHatirlatma::where('durum', 'bilgi_girisi_bekleniyor')
                                                        ->whereHas('musteriSikayeti', function($q) {
                                                            $q->whereNotIn('musteri_durum', ['Kapatıldı', 'Çözümlendi']);
                                                        });
                                                    if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
                                                        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                                                            $navPendingCount->whereHas('musteriSikayeti', function($sq) { $sq->where('konum_tipi', 'Yurt İçi'); });
                                                        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                                                            $navPendingCount->whereHas('musteriSikayeti', function($sq) { $sq->where('konum_tipi', 'Yurt Dışı'); });
                                                        } else {
                                                            $allowedBolumIds = $user->getAllowedBolumIds();
                                                            $navPendingCount->where(function($q) use ($user, $allowedBolumIds) {
                                                                $q->whereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($allowedBolumIds) {
                                                                    if ($allowedBolumIds !== '*') { $sq->whereIn('bolum_id', $allowedBolumIds); }
                                                                })->orWhereHas('musteriSikayeti.cozumTakimi', function($sq) use ($user) {
                                                                    $sq->where('lider_user_id', $user->id);
                                                                });
                                                            });
                                                        }
                                                    }
                                                    $navPendingCount = $navPendingCount->count();
                                                @endphp
                                                @if($navPendingCount > 0)
                                                    <span class="px-1.5 py-0.5 bg-rose-500 text-white text-[10px] rounded-full animate-pulse">{{ $navPendingCount }}</span>
                                                @endif
                                                <span class="group-hover:animate-bounce">🔔</span>
                                            </div>
                                        </x-dropdown-link>
                                    @endif
                                    <x-dropdown-link :href="route('admin.reports.daily_complaints')" class="text-rose-600 font-bold bg-rose-50/30">📅 GÜNLÜK ŞİKAYET RAPORU</x-dropdown-link>
                                @endif

                                {{-- 2. MÜŞTERİ VE ZİYARET YÖNETİMİ --}}
                                <div class="border-t border-slate-100 my-1"></div>
                                <div class="px-4 py-2.5 bg-slate-800 border-b border-slate-900 text-[13px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                    <span class="w-1.5 h-4 bg-indigo-500 rounded-full shadow-sm"></span>
                                    MÜŞTERİ VE ZİYARET
                                </div>
                                @if(Auth::user()->canSeeMusteriOperasyonlari())
                                    <x-dropdown-link :href="route('admin.musteriler.index')">Müşteri Listesi</x-dropdown-link>
                                @endif
                                
                                @if(Auth::user()->canViewZiyaretlerPage())
                                    <x-dropdown-link :href="route('admin.ziyaretler')" class="flex items-center justify-between group">
                                        <span>Müşteri Ziyaretleri</span>
                                        <div class="flex items-center gap-1">
                                            @if(isset($managerPendingVisitCount) && $managerPendingVisitCount > 0)
                                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-black leading-none text-white bg-amber-500 rounded-full group-hover:bg-amber-600 transition shadow-sm" title="Onay Bekleyen Ziyaretler">{{ $managerPendingVisitCount }}</span>
                                            @endif
                                            @if(isset($notCompletedVisitCount) && $notCompletedVisitCount > 0)
                                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-black leading-none text-white bg-rose-500 rounded-full animate-pulse group-hover:bg-rose-600 transition shadow-sm" title="Tamamlanmayan Ziyaretler">{{ $notCompletedVisitCount }}</span>
                                            @endif
                                        </div>
                                    </x-dropdown-link>
                                @endif
                                
                                @if(Auth::user()->hasZiyaretGorevi())
                                    <x-dropdown-link :href="route('admin.ziyaret-planlarim')" class="flex items-center justify-between">
                                        <span>Ziyaret Planlarım</span>
                                        @if($pendingVisitCount > 0)
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full shadow-sm animate-pulse">{{ $pendingVisitCount }}</span>
                                        @endif
                                    </x-dropdown-link>
                                @endif


                                {{-- 3. SİSTEM TANIMLARI (Sadece Superadmin) --}}
                                @role('Superadmin')
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-2.5 bg-slate-500 border-b border-slate-600 text-[13px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-4 bg-slate-100 rounded-full shadow-sm"></span>
                                        SİSTEM TANIMLARI
                                    </div>
                                    <x-dropdown-link :href="route('admin.sikayet-kategorileri.index')">Şikayet Kategorileri</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.cozum-takimlari.index')">Çözüm Takımları</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.iade-ayarlari.index')">İade Parametreleri</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.sikayet-hatirlatma.ayarlar')" class="text-indigo-600 font-bold bg-indigo-50/20">⚙️ Hatırlatma Ayarları</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.sikayet-hatirlaticilar.index')" class="text-emerald-600 font-bold">🕒 Otomatik Hatırlatıcılar</x-dropdown-link>
                                @endrole
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 5.5 İŞ & SÜREÇ TAKİBİ --}}
                @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Bölüm Lider Yardımcısı', 'Bölüm Kalite Yöneticisi', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Direktör', 'Hukuk Yöneticisi', 'Hukuk Admini']))
                    <div class="flex items-center h-full">
                        <a href="{{ route('admin.tum-bekleyen-isler') }}" class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-bold transition-all {{ request()->routeIs('admin.tum-bekleyen-isler') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>İş & Süreç Takibi</span>
                        </a>
                    </div>
                @endif

                {{-- 6. YÖNETİCİ --}}
                @if(Auth::user()->hasRole(['Superadmin', 'Yonetim', 'Bölüm Kalite Yöneticisi', 'Bölüm Lideri', 'Direktör']) || Auth::user()->hasBolumAuthority('bolum.mavi_yaka.yonet'))
                    <div class="flex items-center h-full">
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @click.away="open = false">
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-bold transition-all {{ request()->routeIs('admin.iaa-yonetim.*') || request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                                <span class="relative">
                                    Yönetici
                                    @if($iaaApprovalCount > 0 || $pendingUserCount > 0 || $istekCount > 0 || $iaaYonetimToplamSayac > 0)
                                        <span class="absolute -top-1.5 -right-2.5 flex h-2.5 w-2.5 focus:outline-none">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                                        </span>
                                    @endif
                                </span>
                                <svg class="w-3 h-3 opacity-50 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition.origin.top.right
                                class="absolute right-0 top-full mt-1 w-64 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-10 py-2 z-50 max-h-[85vh] overflow-y-auto custom-scrollbar">
                                
                                {{-- 1. PERSONEL VE ORGANİZASYON --}}
                                @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör', 'Hukuk Admini']) || Auth::user()->hasBolumAuthority('bolum.mavi_yaka.yonet'))
                                    <div class="px-4 py-2.5 bg-slate-800 border-b border-slate-900 text-[13px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-4 bg-indigo-500 rounded-full shadow-sm"></span>
                                        PERSONEL VE ORGANİZASYON
                                    </div>
                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim']))
                                        <x-dropdown-link :href="route('admin.users.index')" class="flex items-center justify-between font-bold">
                                            <span>Kullanıcı Yönetimi</span>
                                            @if($pendingUserCount > 0)
                                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">{{ $pendingUserCount }}</span>
                                            @endif
                                        </x-dropdown-link>
                                    @endif
                                    @role('Superadmin')
                                        <x-dropdown-link :href="route('admin.istekler.index')" class="flex items-center justify-between font-bold text-indigo-700">
                                            <span>Kullanıcı İstekleri</span>
                                            @if(isset($istekCount) && $istekCount > 0)
                                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">{{ $istekCount }}</span>
                                            @endif
                                        </x-dropdown-link>
{{-- YENİ: Kullanıcı Başvuruları (SSO Onay Bekleyenler) --}}
@if(Auth::user()->hasRole(['Superadmin', 'Bölüm Lideri']))
    <x-dropdown-link :href="route('admin.users.onay_bekleyenler')" class="flex items-center justify-between font-bold text-amber-600 hover:text-amber-700 hover:bg-amber-50">
        <div class="flex items-center gap-2">
            <span>📋 Kullanıcı Başvuruları</span>
        </div>
        @if($pendingUserCount > 0)
            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-amber-500 rounded-full animate-pulse shadow-sm">
                {{ $pendingUserCount }}
            </span>
        @endif
    </x-dropdown-link>
@endif
                                        <x-dropdown-link :href="route('admin.bolumler.index')">Bölüm Yönetimi</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.bolum-kategorileri.index')" class="text-slate-500 text-[11px]">Bölüm Kategorileri</x-dropdown-link>
                                    @endrole

                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör', 'Hukuk Admini']) || Auth::user()->hasBolumAuthority('bolum.mavi_yaka.yonet'))
                                        <x-dropdown-link :href="route('admin.mavi-yaka.index')" class="font-medium">Mavi Yaka Personel</x-dropdown-link>
                                    @endif
                                    
                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri']))
                                        <x-dropdown-link :href="route('admin.bolum-yonetim.index')" class="text-indigo-600 font-bold border-l-2 border-indigo-500">🛡️ Bölüm Yetki Matrisi</x-dropdown-link>
                                    @endif

                                    @role('Superadmin')
                                        <x-dropdown-link :href="route('admin.direktorler.index')">Direktör Atamaları</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.kalite-yoneticileri.index')">Kalite Yöneticileri</x-dropdown-link>
                                    @endrole
                                    @role('Superadmin')
                                        <x-dropdown-link :href="route('admin.musteri-saha-temsilcileri.index')">Müşteri Saha Temsilcileri</x-dropdown-link>
                                    @endrole
                                @endif

                                {{-- 2. OPERASYON VE İYİLEŞTİRME --}}
                                @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']))
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-2.5 bg-indigo-700 border-b border-indigo-800 text-[13px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-4 bg-white rounded-full shadow-sm"></span>
                                        OPERASYON VE İYİLEŞTİRME
                                    </div>

                                    <x-dropdown-link :href="Auth::user()->hasRole(['Yonetim', 'Yönetim']) ? route('admin.raporlar.index') : route('admin.iaa-yonetim.index')" class="flex items-center justify-between font-bold">
                                        <span>İAA Paneli</span>
                                        @if($iaaYonetimToplamSayac > 0)
                                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">{{ $iaaYonetimToplamSayac }}</span>
                                        @endif
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.raporlar.index')">İyileştirme Raporları</x-dropdown-link>
                                    
                                    @role('Superadmin')
                                        <x-dropdown-link :href="route('admin.takim-yonetim.index')">Takım Yönetimi</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.workflows.index')">Akış Şablonları (Workflows)</x-dropdown-link>
                                    @endrole
                                @endif

                                {{-- 3. DENETİM VE SİSTEM AYARLARI --}}
                                @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör', 'Bölüm Kalite Yöneticisi']))
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <div class="px-4 py-2.5 bg-slate-600 border-b border-slate-700 text-[13px] font-black text-white uppercase tracking-tighter flex items-center gap-2 shadow-inner">
                                        <span class="w-1.5 h-4 bg-emerald-400 rounded-full shadow-sm"></span>
                                        DENETİM VE AYARLAR
                                    </div>
                                    <x-dropdown-link :href="route('admin.notifications.audit')" class="text-indigo-600 font-bold">Bildirim Denetimi</x-dropdown-link>
                                    @if($mailLogUnresolvedCount > 0 || Auth::user()->hasAnyRole(['Superadmin', 'Yonetim']))
                                        <x-dropdown-link :href="route('admin.mail-logs.index')" class="text-rose-600 font-bold bg-rose-50/30 flex items-center justify-between">
                                            <span>📧 Mail Bildirim Logları</span>
                                            @if($mailLogUnresolvedCount > 0)
                                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full animate-pulse">{{ $mailLogUnresolvedCount }}</span>
                                            @endif
                                        </x-dropdown-link>
                                    @endif
                                    @role('Superadmin')
                                        <x-dropdown-link :href="route('admin.profil-yorum-denetimi.index')" class="text-emerald-600 font-bold">Profil Yorum Denetimi</x-dropdown-link>
                                    @endrole
                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim']))
                                        <x-dropdown-link :href="route('yonetim.index')" class="font-bold text-indigo-700">Yönetim Raporu</x-dropdown-link>
                                    @endif
                                    @role('Superadmin')
                                        <x-dropdown-link :href="route('admin.takvim-eslestirme.index')" class="bg-indigo-50/30 font-bold border-y border-indigo-50/50">Takvim Eşleştirme</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.sistem-ayarlari.index')">Sistem Ayarları</x-dropdown-link>
                                        <x-dropdown-link href="/pulse" target="_blank" class="text-indigo-600 font-bold bg-indigo-50/20">⚡ Performans Paneli (Pulse)</x-dropdown-link>
                                    @endrole

                                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']))
                                        <x-dropdown-link :href="route('logs.login.index')" class="text-indigo-600 font-bold">Giriş Logları</x-dropdown-link>
                                        <x-dropdown-link :href="route('machine-logs.index')" class="text-indigo-500 font-medium italic">Makine İşlem Geçmişi</x-dropdown-link>
                                    @endif
                                @endif

                                {{-- 4. SİSTEM SAĞLIĞI --}}
                                @role('Superadmin')
                                    <div class="border-t border-rose-100 my-1"></div>
                                    <x-dropdown-link :href="route('admin.health.index')" class="text-rose-600 font-black uppercase text-[11px] bg-rose-50/50 hover:bg-rose-100 transition-all flex items-center justify-between">
                                        <span>SİSTEM SAĞLIĞI</span>
                                        <span class="flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-rose-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                        </span>
                                    </x-dropdown-link>
                                @endrole
                @endif
            @endauth
        </div>
    </div>

    {{-- MOBİL MENÜ İÇERİĞİ (TELEPORT İLE BODY SONUNA TAŞINARAK CSS CLIPPING ENGELLENDİ) --}}
    <template x-teleport="body">
        <div class="lg:hidden">
            <!-- Ekran Karartma Katmanı (Backdrop) -->
            <div x-show="mobileOpen" x-cloak
                 class="fixed inset-0 bg-slate-900/60 z-[90] backdrop-blur-sm"
                 @click="mobileOpen = false"
                 aria-hidden="true">
            </div>

            <!-- Menü Çekmecesi (Sağdan Kayarak Açılır) -->
            <div x-show="mobileOpen" x-cloak
                 class="fixed inset-y-0 right-0 w-[85%] max-w-sm bg-slate-50 flex flex-col shadow-2xl z-[100]"
                 @click.away="mobileOpen = false">

        {{-- Mobil Profil Başlığı (YENİ) --}}
        @auth
            <div class="px-5 py-6 bg-slate-800 text-white flex items-center justify-between shadow-md shrink-0 border-b border-slate-700 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/30 to-purple-600/30"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="shrink-0">
                        @if(Auth::user()->profile_photo_path)
                            <img class="h-11 w-11 rounded-full object-cover border-2 border-white/20 shadow-lg"
                                src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                alt="{{ Auth::user()->name }}" />
                        @else
                            <div class="h-11 w-11 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold border-2 border-white/20 text-lg shadow-lg">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <div class="font-bold text-[14px]">
                            @if(Auth::user()->isShadowing())
                                {{ Auth::user()->getRealAttribute('name') }}
                            @else
                                {{ Auth::user()->name }}
                            @endif
                        </div>
                        <div class="font-medium text-[11px] text-slate-300">
                            @if(Auth::user()->isShadowing())
                                {{ Auth::user()->getRealAttribute('email') }}
                            @else
                                {{ Auth::user()->email }}
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Kapat Tuşu --}}
                <button @click="mobileOpen = false" type="button" class="relative z-10 bg-slate-700/50 hover:bg-slate-600 text-slate-200 p-2 rounded-full transition-colors focus:outline-none ring-1 ring-white/10 shadow hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endauth

        {{-- İçerik (Kaydırılabilir Menü Linkleri) --}}
        <div class="flex-1 overflow-y-auto px-2 py-3 overscroll-contain">
            @auth
            <div class="pt-2 pb-3 space-y-1 px-2">
                
                {{-- Merkezi Yönetim'e Dön Butonu (Mobil) --}}
                <x-responsive-nav-link :href="rtrim(env('CENTRAL_SSO_URL', 'http://localhost:8001'), '/') . '/dashboard'" class="text-indigo-700 font-black flex items-center gap-2 bg-indigo-100 border border-indigo-200 rounded-md mb-4 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Merkezi Yönetim'e Dön
                </x-responsive-nav-link>

                {{-- MOBİL: Dashboard Seçici --}}
                @if(!Auth::user()->hasRole('Yonetim'))
                    @if(count($availableDashboards) > 1)
                        <div class="bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-400 uppercase">Dashboard</div>
                        @foreach($availableDashboards as $key => $info)
                            <a href="{{ route('dashboard.switch', $key) }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm {{ $activeDashboard === $key ? 'text-indigo-700 font-bold bg-indigo-50' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span>{{ $info['icon'] }} {{ $info['label'] }}</span>
                                @if($activeDashboard === $key)
                                    <svg class="ml-auto w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    @else
                        <x-responsive-nav-link :href="route('dashboard')"
                            :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
                    @endif
                @endif


                {{-- Mobil - İyileştirme --}}
                @if(!Auth::user()->hasRole('Yonetim') && !$isCustomerUser)
                    {{-- 1. İYİLEŞTİRME SÜREÇLERİ --}}
                    <div class="bg-indigo-700 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-indigo-800 shadow-sm">
                        <span class="w-1 h-3 bg-white rounded-full shadow-sm"></span>
                        <span>İYİLEŞTİRME SÜREÇLERİ</span>
                    </div>
                    <x-responsive-nav-link :href="route('iaa.index')">İyileştirmelerim (İAA)</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('iaa.havuz')">İyileştirme Havuzu</x-responsive-nav-link>

                    @if(Auth::user()->hasRole(['Yonetim', 'Yönetim', 'Superadmin']))
                        <x-responsive-nav-link :href="route('admin.raporlar.index')"
                            class="text-indigo-600 font-bold bg-indigo-50/50">İyileştirme Raporları</x-responsive-nav-link>
                    @endif

                    {{-- 2. TAKIM VE PROJE YÖNETİMİ --}}
                    <div class="bg-emerald-700 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-emerald-800 shadow-sm">
                        <span class="w-1 h-3 bg-white rounded-full shadow-sm"></span>
                        <span>TAKIM VE PROJELER</span>
                    </div>
                    <x-responsive-nav-link :href="route('takimlar.index')">Takımlarım</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('iaa.takimProjeleri')">Takım Projelerim</x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('takimlar.davetlerim')" class="flex items-center justify-between">
                        <span>Takım Davetlerim</span>
                        @if(($takimInviteCount + $projeInviteCount) > 0)
                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">
                                {{ $takimInviteCount + $projeInviteCount }}
                            </span>
                        @endif
                    </x-responsive-nav-link>

                    @if($isTeamLeader || Auth::user()->hasRole(['Superadmin', 'Yonetim']))
                        <x-responsive-nav-link :href="route('takimlar.isteklerim')"
                            class="flex items-center justify-between text-indigo-600 font-bold border-l-4 border-indigo-500 bg-indigo-50/30">
                            <span>📩 Gelen Katılma İstekleri</span>
                            @if($pendingRequestsCount > 0)
                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full shadow-sm">
                                    {{ $pendingRequestsCount }}
                                </span>
                            @endif
                        </x-responsive-nav-link>
                    @endif

                    @if(!$isCustomerUser)
                        <div class="bg-slate-800 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-slate-900 shadow-sm">
                            <span class="w-1 h-3 bg-indigo-500 rounded-full"></span>
                            <span>BİLGİ VE ANALİTİK</span>
                        </div>
                        <x-responsive-nav-link :href="route('user-directory.index')" class="text-slate-500 italic">
                            📖 Kullanıcı Rehberi
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('puan-durumu')" class="flex justify-between items-center bg-emerald-50/50">
                            <span class="text-emerald-700 font-bold">⭐ Liderlik Puanım</span>
                            <span class="text-emerald-600 font-black px-1.5 py-0.5 rounded-md text-[10px] bg-white border border-emerald-100">
                                {{ number_format(Auth::user()->toplam_puan, 0) }}
                            </span>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('tum-bolum-puanlari')" class="text-indigo-600 font-bold">
                            📊 Bölüm Puanları Analizi
                        </x-responsive-nav-link>
                    @endif

                    @if($isTeamLeader || Auth::user()->hasRole(['Direktör', 'Superadmin', 'Hukuk Admini']))
                        <div class="bg-blue-50 px-3 py-1 mt-2 text-xs font-bold text-blue-500 uppercase">👷 Mavi Yaka</div>
                        <x-responsive-nav-link :href="route('admin.mavi-yaka.index')" class="text-blue-600 font-bold">
                            Mavi Yaka Personel Listesi
                        </x-responsive-nav-link>
                        @if(!Auth::user()->hasRole(['Direktör', 'Yonetim']))
                            <x-responsive-nav-link :href="route('admin.mavi-yaka.create')" class="text-blue-500">
                                + Yeni Mavi Yaka Ekle
                            </x-responsive-nav-link>
                        @endif
                    @endif
                @endif

                {{-- Mobil Hukuk --}}
                @if($hukukMenuYetkisi)
                    <div class="bg-slate-50 px-3 py-1 mt-2 text-xs font-bold text-slate-400 uppercase flex items-center justify-between">
                        <span>Hukuk</span>
                        @if($discReviewCount > 0)
                            <span class="flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                        @endif
                    </div>

                    @if($disiplinYetkisi)
                        <div class="bg-indigo-700 text-white px-3 py-2.5 mt-2 text-sm font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-indigo-800">
                            <span class="w-1.5 h-4 bg-white rounded-full"></span>
                            <span>DİSİPLİN</span>
                        </div>
                        @if(!Auth::user()->hasRole(['Yonetim', 'Yönetim']))
                            <x-responsive-nav-link :href="route('admin.disiplin.create')" class="text-indigo-600 font-black">
                                + YENİ TUTANAK OLUŞTUR
                            </x-responsive-nav-link>
                        @endif
                        <x-responsive-nav-link :href="route('admin.disiplin.index')" class="flex items-center justify-between">
                            <span>Disiplin Dosyaları</span>
                            @if($discReviewCount > 0)
                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">
                                    {{ $discReviewCount }}
                                </span>
                            @endif
                        </x-responsive-nav-link>
                        
                        {{-- Disiplin Kurulu Portalı (Mobil) --}}
                        @if(Auth::user()->can('disiplin.kurul.portal.gor') || Auth::user()->hasRole(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Yonetim']))
                            <x-responsive-nav-link :href="route('admin.disiplin.kurul.index')" class="text-indigo-700 font-black bg-indigo-50 border-y border-indigo-100">
                                ⚖️ Disiplin Kurulu Portalı
                            </x-responsive-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör', 'Hukuk Admini', 'Hukuk Yöneticisi', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) || Auth::user()->can('disiplin.rapor.gor'))
                            <x-responsive-nav-link :href="route('admin.disiplin.report')" class="text-indigo-600 font-bold flex items-center gap-2">
                                📊 Disiplin Raporu
                            </x-responsive-nav-link>
                        @endif

                        @if(Auth::user()->hasRole('Bölüm Lideri'))
                            <x-responsive-nav-link :href="route('admin.disiplin.sorumlular.index')">Sorumlu Yönetimi</x-responsive-nav-link>
                        @endif
                    @endif

                    @if($arabuluculukYetkisi)
                        <div class="bg-slate-900 text-white px-3 py-2.5 mt-2 text-sm font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-slate-800">
                            <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                            <span>ARABULUCULUK</span>
                        </div>
                        @if(Auth::user()->hasRole(['Arabuluculuk Personel', 'Hukuk Admini', 'Superadmin']))
                            <x-responsive-nav-link :href="route('admin.arabuluculuk.create')" class="text-emerald-600 font-black">
                                + YENİ DOSYA BAŞLAT
                            </x-responsive-nav-link>
                        @endif
                        <x-responsive-nav-link :href="route('admin.arabuluculuk.index')">Dosya Listesi</x-responsive-nav-link>
                        @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi']))
                            <x-responsive-nav-link :href="route('admin.arabulucular.index')">Arabulucu Listesi</x-responsive-nav-link>
                        @endif
                    @endif

                    @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini', 'Hukuk Yöneticisi']))
                        <div class="bg-slate-50 px-3 py-1 mt-2 text-xs font-bold text-slate-400 uppercase">AYARLAR VE DENETİM</div>
                        @if(Auth::user()->can('disiplin.ayarlar.gor'))
                            <x-responsive-nav-link :href="route('admin.disiplin.settings.index')">Disiplin Ayarları</x-responsive-nav-link>
                        @endif
                        @if(Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']))
                            <x-responsive-nav-link :href="route('admin.disiplin.hukuk-matrisi.index')" class="text-indigo-700 font-black bg-indigo-50 border-y border-indigo-100">
                                🛡️ Hukuk Yetki Matrisi
                            </x-responsive-nav-link>
                        @endif
                        @if(Auth::user()->can('arabuluculuk.tanimlar.gor'))
                            <x-responsive-nav-link :href="route('admin.arabuluculuk.tanim.anlasmaMaddeleri')">Anlaşma Maddeleri</x-responsive-nav-link>
                        @endif
                        @if(Auth::user()->can('dis-avukatlar.gor'))
                            <x-responsive-nav-link :href="route('admin.dis_avukatlar.index')">Dış Avukatlar</x-responsive-nav-link>
                        @endif
                    @endif
                @endif

                @if(Auth::user()->hasSikayetOrganikBagi())
                    {{-- 1. ŞİKAYET VE ANALİZ --}}
                    @if(Auth::user()->canSeeMusteriOperasyonlari() || $kullaniciSikayetTakimindaMi)
                        <div class="bg-rose-700 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-rose-800 shadow-sm">
                            <span class="w-1 h-3 bg-white rounded-full shadow-sm"></span>
                            <span class="relative">
                                ŞİKAYET VE ANALİZ
                                @if($pendingSikayetGorevCount > 0)
                                    <span class="absolute -top-1 -right-4 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                    </span>
                                @endif
                            </span>
                        </div>
                        <x-responsive-nav-link :href="route('admin.sikayetler.index')" class="flex items-center justify-between font-bold">
                            <span>Şikayet Paneli</span>
                            @if($pendingSikayetGorevCount > 0)
                                <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">
                                    {{ $pendingSikayetGorevCount }}
                                </span>
                            @endif
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.sikayet-raporlari.index')">Raporlar</x-responsive-nav-link>
                        @if(\App\Models\ReportRoleAuthorization::getAuthorizationForUser(Auth::user(), 'analiz_raporu'))
                            <x-responsive-nav-link :href="route('admin.musteri-sikayet-analiz-raporu')" class="text-indigo-600 font-bold bg-indigo-50/50">📊 Şikayet Analiz Raporu</x-responsive-nav-link>
                        @endif
                        @if(\App\Models\ReportRoleAuthorization::getAuthorizationForUser(Auth::user(), 'karsilastirma_raporu'))
                            <x-responsive-nav-link :href="route('admin.musteri-sikayet-karsilastirma')" class="text-indigo-600 font-bold bg-indigo-50/50">📈 Kıyaslama Raporu</x-responsive-nav-link>
                        @endif
                        <x-responsive-nav-link :href="route('admin.sikayet-iade-raporlari.index')" class="text-rose-600 font-bold bg-rose-50/20">♻️ İadeler Raporu</x-responsive-nav-link>
                        
                        <x-responsive-nav-link :href="route('admin.reports.daily_complaints')"
                             class="text-rose-600 font-bold bg-rose-50/50">📅 GÜNLÜK ŞİKAYET RAPORU</x-responsive-nav-link>
                    @endif

                    {{-- 2. MÜŞTERİ VE ZİYARET YÖNETİMİ --}}
                    <div class="bg-slate-800 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-slate-900 shadow-sm">
                        <span class="w-1 h-3 bg-indigo-500 rounded-full"></span>
                        <span>MÜŞTERİ VE ZİYARET</span>
                    </div>
                    @if(Auth::user()->canSeeMusteriOperasyonlari())
                        <x-responsive-nav-link :href="route('admin.musteriler.index')">Müşteri Listesi</x-responsive-nav-link>
                        @if(Auth::user()->canViewZiyaretlerPage())
                            <x-responsive-nav-link :href="route('admin.ziyaretler')">Müşteri Ziyaretleri</x-responsive-nav-link>
                        @endif
                    @endif
                    
                    @if(Auth::user()->hasZiyaretGorevi())
                        <x-responsive-nav-link :href="route('admin.ziyaret-planlarim')">Ziyaret Planlarım</x-responsive-nav-link>
                    @endif

                    {{-- 3. SİSTEM TANIMLARI (Sadece Superadmin) --}}
                    @role('Superadmin')
                        <div class="bg-slate-500 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-slate-600 shadow-sm">
                            <span class="w-1 h-3 bg-white rounded-full opacity-50"></span>
                            <span>SİSTEM TANIMLARI</span>
                        </div>
                        <x-responsive-nav-link :href="route('admin.sikayet-kategorileri.index')">Şikayet Kategorileri</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.cozum-takimlari.index')">Çözüm Takımları</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.iade-ayarlari.index')">İade Parametreleri</x-responsive-nav-link>
                    @endrole
                @endif


                {{-- Mobil Yönetim --}}
                @if(Auth::user()->hasRole(['Superadmin', 'Yonetim', 'Bölüm Kalite Yöneticisi', 'Bölüm Lideri', 'Direktör']))
                    {{-- 1. PERSONEL VE ORGANİZASYON --}}
                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör', 'Hukuk Admini']))
                        <div class="bg-slate-800 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-slate-900">
                            <span class="w-1 h-3 bg-indigo-500 rounded-full"></span>
                            <span>PERSONEL VE ORGANİZASYON</span>
                        </div>
                        @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim']))
                            <x-responsive-nav-link :href="route('admin.users.index')" class="flex items-center justify-between font-bold">
                                <span>Kullanıcı Yönetimi</span>
                                @if($pendingUserCount > 0)
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">{{ $pendingUserCount }}</span>
                                @endif
                            </x-responsive-nav-link>
                        @endif
                        @role('Superadmin')
                            <x-responsive-nav-link :href="route('admin.bolumler.index')">Bölüm Yönetimi</x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('admin.bolum-kategorileri.index')" class="text-slate-400 text-[11px]">Bölüm Kategorileri</x-responsive-nav-link>
                        @endrole

                        <x-responsive-nav-link :href="route('admin.mavi-yaka.index')">Mavi Yaka Personel</x-responsive-nav-link>
                        
                        @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri']))
                            <x-responsive-nav-link :href="route('admin.bolum-yonetim.index')" class="text-indigo-600 font-bold border-l-4 border-indigo-500 bg-indigo-50/30">🛡️ Bölüm Yetki Matrisi</x-responsive-nav-link>
                        @endif

                        @role('Superadmin')
                            <x-responsive-nav-link :href="route('admin.direktorler.index')">Direktör Atamaları</x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('admin.kalite-yoneticileri.index')">Kalite Yöneticileri</x-responsive-nav-link>
                        @endrole
                        @role('Superadmin')
                            <x-responsive-nav-link :href="route('admin.musteri-saha-temsilcileri.index')">Müşteri Saha Temsilcileri</x-responsive-nav-link>
                        @endrole
                    @endif

                    {{-- 2. OPERASYON VE İYİLEŞTİRME --}}
                        @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']))
                            <div class="bg-indigo-700 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-indigo-800">
                                <span class="w-1 h-3 bg-white rounded-full"></span>
                                <span>OPERASYON VE İYİLEŞTİRME</span>
                            </div>
                            @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim']))
                                <x-responsive-nav-link :href="route('admin.tum-bekleyen-isler')" class="text-indigo-700 font-black bg-indigo-50">⚡ TÜM BEKLEYEN İŞLER</x-responsive-nav-link>
                            @endif
                            <x-responsive-nav-link :href="Auth::user()->hasRole(['Yonetim', 'Yönetim']) ? route('admin.raporlar.index') : route('admin.iaa-yonetim.index')" class="flex items-center justify-between font-bold">
                                <span>İAA Paneli</span>
                                @if($iaaYonetimToplamSayac > 0)
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full">{{ $iaaYonetimToplamSayac }}</span>
                                @endif
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('admin.raporlar.index')">İyileştirme Raporları</x-responsive-nav-link>
                            
                            @role('Superadmin')
                                <x-responsive-nav-link :href="route('admin.takim-yonetim.index')">Takım Yönetimi</x-responsive-nav-link>
                                <x-responsive-nav-link :href="route('admin.workflows.index')">Akış Şablonları</x-responsive-nav-link>
                            @endrole
                        @endif

                    {{-- 3. DENETİM VE AYARLAR --}}
                    @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör', 'Bölüm Kalite Yöneticisi']))
                        <div class="bg-slate-600 text-white px-3 py-2 mt-4 text-xs font-black uppercase tracking-tighter flex items-center gap-2 rounded-t-lg mx-2 border-b border-slate-700">
                            <span class="w-1 h-3 bg-emerald-400 rounded-full"></span>
                            <span>DENETİM VE AYARLAR</span>
                        </div>
                        <x-responsive-nav-link :href="route('admin.notifications.audit')" class="text-indigo-600 font-bold">Bildirim Denetimi</x-responsive-nav-link>
                        @if($mailLogUnresolvedCount > 0 || Auth::user()->hasAnyRole(['Superadmin', 'Yonetim']))
                            <x-responsive-nav-link :href="route('admin.mail-logs.index')" class="text-rose-600 font-bold flex items-center justify-between bg-rose-50/30">
                                <span>📧 Mail Bildirim Logları</span>
                                @if($mailLogUnresolvedCount > 0)
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full animate-pulse">{{ $mailLogUnresolvedCount }}</span>
                                @endif
                            </x-responsive-nav-link>
                        @endif
                        @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim']))
                            <x-responsive-nav-link :href="route('yonetim.index')" class="font-bold text-indigo-600">Yönetim Raporu</x-responsive-nav-link>
                        @endif

                        @if(Auth::user()->hasAnyRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör']))
                            <x-responsive-nav-link :href="route('logs.login.index')" class="text-indigo-600 font-bold">Giriş Logları</x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('machine-logs.index')" class="text-slate-500 italic">Makine İşlem Geçmişi</x-responsive-nav-link>
                        @endif
                        
                        @role('Superadmin')
                            <x-responsive-nav-link :href="route('admin.takvim-eslestirme.index')" class="text-indigo-600 font-bold bg-indigo-50/30">Takvim Eşleştirme</x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('admin.sistem-ayarlari.index')">Sistem Ayarları</x-responsive-nav-link>
                            <x-responsive-nav-link href="/pulse" target="_blank" class="text-indigo-600 font-bold bg-indigo-50/20">⚡ Performans Paneli (Pulse)</x-responsive-nav-link>
                        @endrole
                    @endif

                    {{-- 4. SİSTEM SAĞLIĞI --}}
                    @role('Superadmin')
                        <x-responsive-nav-link :href="route('admin.health.index')" class="mt-4 text-rose-600 font-black bg-rose-50/50 border-y border-rose-100 flex items-center justify-between">
                            <span>🚨 SİSTEM SAĞLIĞI</span>
                            <span class="flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                        </x-responsive-nav-link>
                    @endrole
                @endif

        </div>
        </div>


        {{-- Drawer Alt Kısım: Çıkış ve Profil Ayarları (Drawer Footer) --}}
        <div class="border-t border-slate-200 bg-white px-4 py-4 mt-auto shrink-0 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] relative">
            <div class="flex items-center justify-between gap-2">
                <a href="{{ route('profile.edit') }}" class="flex-1 flex flex-col items-center justify-center py-2.5 rounded-lg bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-colors">
                    <svg class="w-5 h-5 mb-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Ayarlar
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1 flex">
                    @csrf
                    <button type="submit" class="w-full flex flex-col items-center justify-center py-2.5 rounded-lg bg-rose-50 text-rose-700 font-bold text-sm hover:bg-rose-100 border border-rose-100 transition-colors">
                        <svg class="w-5 h-5 mb-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Çıkış Yap
                    </button>
                </form>
            </div>
        </div>
                @endauth
            </div>
        </div>
    </template>
</nav>