<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Iaa;
use App\Models\Bolum;
use App\Models\Takim;
use App\Models\MusteriSikayeti;
use Carbon\Carbon;
use App\Models\ProjeYorumu;
use App\Models\ProfileComment;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\SikayetKategori;
use App\Exports\BolumAnalizExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    // --- SERVİS TANIMLARI ---
    protected $superAdminService;
    protected $yonetimService;
    protected $puanService; // KullaniciPuanService
    protected $istatistikService; // KullaniciIstatistikService
    protected $bolumService;
    protected $sikayetService;
    protected $hukukService;
    protected $musteriService; // MusteriDashboardService
    protected $disiplinKuruluService;

    public function __construct(
        \App\Services\Dashboard\SuperAdminDashboardService $superAdminService,
        \App\Services\Dashboard\YonetimDashboardService $yonetimService,
        \App\Services\Dashboard\KullaniciPuanService $puanService,
        \App\Services\Dashboard\KullaniciIstatistikService $istatistikService,
        \App\Services\Dashboard\BolumDashboardService $bolumService,
        \App\Services\Dashboard\SikayetDashboardService $sikayetService,
        \App\Services\Dashboard\HukukDashboardService $hukukService,
        \App\Services\Dashboard\MusteriDashboardService $musteriService,
        \App\Services\Dashboard\DisiplinKuruluDashboardService $disiplinKuruluService
    ) {
        $this->superAdminService = $superAdminService;
        $this->yonetimService = $yonetimService;
        $this->puanService = $puanService;
        $this->istatistikService = $istatistikService;
        $this->bolumService = $bolumService;
        $this->sikayetService = $sikayetService;
        $this->hukukService = $hukukService;
        $this->musteriService = $musteriService;
        $this->disiplinKuruluService = $disiplinKuruluService;
    }

    /**
     * Dashboard ana sayfasını gösterir.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. PUAN SENKRONÄ°ZASYONU (Personel Ä°se)
        if ($user->is_personnel)
        {
            $gercekPuan = $this->puanService->calculateTotalScore($user);
            if ($user->toplam_puan != $gercekPuan)
            {
                $user->toplam_puan = $gercekPuan;
                $user->save();
            }
        }

        // 2. MÃœÅTERÄ° DASHBOARD (Personel DeÄŸilse)
        if (!$user->is_personnel)
        {
            $startDate = request('start_date');
            $endDate = request('end_date');

            $activeCustomerId = session('active_customer_id_' . $user->id);
            if ($activeCustomerId) {
                $activeCustomerId = (int)$activeCustomerId;
            }
            
            // Eğer session'da yoksa veya geçersizse, yetkili olduğu firmalardan ilkini seç
            $userCustomers = $user->customers()->get();
            
            // Legacy support: Eğer pivot tabloda veri yoksa eski customer_id'yi kullan
            if ($userCustomers->isEmpty() && $user->customer_id) {
                $userCustomers = \App\Models\Customer::where('id', $user->customer_id)->get();
            }

            $authorizedIds = $userCustomers->pluck('id')->toArray();

            if (!$activeCustomerId || !in_array($activeCustomerId, $authorizedIds)) {
                $activeCustomerId = !empty($authorizedIds) ? (int)$authorizedIds[0] : null;
                if ($activeCustomerId) {
                    session(['active_customer_id_' . $user->id => $activeCustomerId]);
                }
            }

            $stats = $this->musteriService->getStats($user, $startDate, $endDate, $activeCustomerId);
            
            return view('dashboard', compact('user', 'stats', 'userCustomers', 'activeCustomerId'))->with('is_musteri_dashboard', true);
        }

        // 3. PERSONEL DASHBOARD (Rol BazlÄ±)
        $stats = [];
        $ekstraTablolar = [];
        $bolumOnayiBekleyenSayisi = 0;
        $iadeVerileri = null;
        $iadeToplamlari = [];
        $yonetilenKategoriler = collect();
        $yonetilenKategoriIds = [];

        // --- Dashboard Geçiş Oturumu Kontrolü ---
        // Kullanıcının seçtiği dashboard varsa ve hâlâ o role sahipse onu kullan.
        $activeDashboard = session('active_dashboard_' . $user->id);
        $sessionValid = false;
        if ($activeDashboard)
        {
            $roleMap = [
                'superadmin' => 'Superadmin',
                'yonetim' => 'Yonetim',
                'kurul' => 'Müşteri Şikayeti Kurulu',
                'cozum_lideri' => 'Müşteri Şikayeti Çözüm Lideri',
                'kalite' => 'Bölüm Kalite Yöneticisi',
                'bolum_lideri' => 'Bölüm Lideri',
                'bolum_lider_yardimcisi' => 'Bölüm Lider Yardımcısı',
                'direktor' => 'Direktör',
                'hukuk' => ['Hukuk Admini', 'Hukuk Yöneticisi'],
                'disiplin_kurulu_baskani' => 'Disiplin Kurulu Başkanı',
                'disiplin_kurulu_uyesi' => 'Disiplin Kurulu Üyesi',
            ];
            if (isset($roleMap[$activeDashboard]))
            {
                $requiredRole = $roleMap[$activeDashboard];
                $sessionValid = is_array($requiredRole)
                    ? $user->hasRole($requiredRole)
                    : $user->hasRole($requiredRole);
            }
        }

        // Geçerli oturum yoksa default önceliği belirle
        if (!$sessionValid)
        {
            if ($user->hasRole('Superadmin'))
                $activeDashboard = 'superadmin';
            elseif ($user->hasRole('Yonetim'))
                $activeDashboard = 'yonetim';
            elseif ($user->hasRole('Müşteri Şikayeti Kurulu'))
                $activeDashboard = 'kurul';
            elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri'))
                $activeDashboard = 'cozum_lideri';
            elseif ($user->hasRole('Bölüm Kalite Yöneticisi'))
                $activeDashboard = 'kalite';
            elseif ($user->hasRole('Bölüm Lideri'))
                $activeDashboard = 'bolum_lideri';
            elseif ($user->hasRole('Bölüm Lider Yardımcısı'))
                $activeDashboard = 'bolum_lider_yardimcisi';
            elseif ($user->hasRole('Direktör'))
                $activeDashboard = 'direktor';
            elseif ($user->hasRole(['Hukuk Admini', 'Hukuk Yöneticisi']))
                $activeDashboard = 'hukuk';
            elseif ($user->hasRole('Disiplin Kurulu Başkanı'))
                $activeDashboard = 'disiplin_kurulu_baskani';
            elseif ($user->hasRole('Disiplin Kurulu Üyesi'))
                $activeDashboard = 'disiplin_kurulu_uyesi';
            else
                $activeDashboard = 'standart';
            session(['active_dashboard_' . $user->id => $activeDashboard]);
        }

        // --- Dashboard Verisi Yükle ---
        if ($activeDashboard === 'superadmin')
        {
            $bolumId = request('bolum_id');
            $stats = $this->superAdminService->getStats($bolumId);
            $ekstraTablolar = $this->superAdminService->getExtraTables();
            $bolumOnayiBekleyenSayisi = Iaa::where('durum', 'Bölüm Onayı Bekliyor')->count();
            $iadeVerileri = \App\Models\SikayetIadesi::with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.iaaProjesi', 'musteriSikayeti.customer'])
                ->when(request('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', request('return_start_date')))
                ->when(request('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', request('return_end_date')))
                ->when(request('return_search'), function ($q)
                {
                    $search = request('return_search');
                    $q->where(function ($sq) use ($search)
                    {
                        $sq->where('urun_turu', 'like', "%{$search}%")
                            ->orWhere('iade_sebebi', 'like', "%{$search}%")
                            ->orWhereHas('musteriSikayeti', function ($ssq) use ($search)
                            {
                                $ssq->where('musteri_adi', 'like', "%{$search}%")
                                    ->orWhere('musteri_sikayet_konusu', 'like', "%{$search}%")
                                    ->orWhere('id', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest('iade_tarihi')->paginate(5, ['*'], 'return_page');
            $iadeToplamlari = \App\Models\SikayetIadesi::select('birim', \DB::raw('SUM(miktar) as toplam_miktar'))
                ->when(request('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', request('return_start_date')))
                ->when(request('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', request('return_end_date')))
                ->when(request('return_search'), function ($q)
                {
                    $search = request('return_search');
                    $q->where(function ($sq) use ($search)
                    {
                        $sq->where('urun_turu', 'like', "%{$search}%")
                            ->orWhere('iade_sebebi', 'like', "%{$search}%")
                            ->orWhereHas('musteriSikayeti', function ($ssq) use ($search)
                            {
                                $ssq->where('musteri_adi', 'like', "%{$search}%")
                                    ->orWhere('musteri_sikayet_konusu', 'like', "%{$search}%")
                                    ->orWhere('id', 'like', "%{$search}%");
                            });
                    });
                })
                ->groupBy('birim')->pluck('toplam_miktar', 'birim');

            // YENİ: Havuzdaki talep almış İAA sayısı
            $stats['bekleyenIaaTalepleri'] = Iaa::where('durum', 'Havuzda')
                ->whereHas('talepEdenTakimlar')
                ->count();

            // YENİ: Tüm bölümler (filtreleme barı için)
            $tumBolumler = Bolum::with('kategori')->orderBy('ad')->get();
            $seciliBolumId = $bolumId;


        }
        elseif ($activeDashboard === 'yonetim')
        {
            $bolumId = request('bolum_id');
            $startDate = request('start_date');
            $endDate = request('end_date');

            $stats = $this->yonetimService->getStats($bolumId, $startDate, $endDate);
            $tumBolumler = Bolum::with('kategori')->orderBy('ad')->get();
            $seciliBolumId = $bolumId;
            $seciliStartDate = $startDate;
            $seciliEndDate = $endDate;

        }
        elseif ($activeDashboard === 'kurul')
        {
            $kurul_stats = $this->sikayetService->getBoardStats();
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($kurul_stats, $user_stats);

            // Müşteri Şikayet Kurulu için iade verileri (Tüm iadeleri görsünler)
            $iadeVerileri = \App\Models\SikayetIadesi::with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.iaaProjesi', 'musteriSikayeti.customer'])
                ->when(request('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', request('return_start_date')))
                ->when(request('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', request('return_end_date')))
                ->when(request('return_search'), function ($q)
                {
                    $search = request('return_search');
                    $q->where(function ($sq) use ($search)
                    {
                        $sq->where('urun_turu', 'like', "%{$search}%")
                            ->orWhere('iade_sebebi', 'like', "%{$search}%")
                            ->orWhereHas('musteriSikayeti', function ($ssq) use ($search)
                            {
                                $ssq->where('musteri_adi', 'like', "%{$search}%")
                                    ->orWhere('musteri_sikayet_konusu', 'like', "%{$search}%")
                                    ->orWhere('id', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest('iade_tarihi')->paginate(10, ['*'], 'return_page');

            $iadeToplamlari = \App\Models\SikayetIadesi::select('birim', \DB::raw('SUM(miktar) as toplam_miktar'))
                ->when(request('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', request('return_start_date')))
                ->when(request('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', request('return_end_date')))
                ->groupBy('birim')->pluck('toplam_miktar', 'birim');

            // Müşteri listesi tablosu için tüm müşteriler (Son eklenenlere göre ve istatistiklerle)
            $stats['sorumlu_musteriler'] = \App\Models\Customer::with('users')
                ->withCount([
                    'complaints as toplam_sikayet',
                    'complaints as cozulen_sikayet' => function($query) {
                        $query->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı']);
                    },
                    'complaints as bekleyen_sikayet' => function($query) {
                        $query->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı', 'İptal Edildi', 'Reddedildi']);
                    }
                ])
                ->latest()
                ->get();
        }
        elseif ($activeDashboard === 'cozum_lideri')
        {
            $leaderStats = $this->sikayetService->getLeaderStats($user, request()->all());
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($leaderStats, $user_stats);

        }
        elseif ($activeDashboard === 'kalite')
        {
            $qualityStats = $this->bolumService->getQualityStats($user, request()->all());
            $bolumOnayiBekleyenSayisi = $qualityStats['bolum_onay_sayisi'];
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($qualityStats, $user_stats);

            // Kalite Yöneticisi için kategori verileri
            $yonetilenKategoriler = $user->yonettigiSikayetKategorileri;
            $yonetilenKategoriIds = $yonetilenKategoriler->pluck('id')->toArray();

            // Kalite Yöneticisi için iade verileri
            $sorumluKategoriler = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            $iadeVerileri = \App\Models\SikayetIadesi::whereHas('musteriSikayeti', function ($q) use ($sorumluKategoriler)
            {
                $q->whereIn('sikayet_kategorisi_id', $sorumluKategoriler);
            })
                ->with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.iaaProjesi', 'musteriSikayeti.customer'])
                ->when(request('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', request('return_start_date')))
                ->when(request('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', request('return_end_date')))
                ->when(request('return_search'), function ($q)
                {
                    $search = request('return_search');
                    $q->where(function ($sq) use ($search)
                    {
                        $sq->where('urun_turu', 'like', "%{$search}%")
                            ->orWhere('iade_sebebi', 'like', "%{$search}%")
                            ->orWhereHas('musteriSikayeti', function ($ssq) use ($search)
                            {
                                $ssq->where('musteri_adi', 'like', "%{$search}%")
                                    ->orWhere('musteri_sikayet_konusu', 'like', "%{$search}%")
                                    ->orWhere('id', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest('iade_tarihi')->paginate(5, ['*'], 'return_page');

            $iadeToplamlari = \App\Models\SikayetIadesi::whereHas('musteriSikayeti', function ($q) use ($sorumluKategoriler)
            {
                $q->whereIn('sikayet_kategorisi_id', $sorumluKategoriler);
            })
                ->select('birim', \DB::raw('SUM(miktar) as toplam_miktar'))
                ->when(request('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', request('return_start_date')))
                ->when(request('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', request('return_end_date')))
                ->when(request('return_search'), function ($q)
                {
                    $search = request('return_search');
                    $q->where(function ($sq) use ($search)
                    {
                        $sq->where('urun_turu', 'like', "%{$search}%")
                            ->orWhere('iade_sebebi', 'like', "%{$search}%")
                            ->orWhereHas('musteriSikayeti', function ($ssq) use ($search)
                            {
                                $ssq->where('musteri_adi', 'like', "%{$search}%")
                                    ->orWhere('musteri_sikayet_konusu', 'like', "%{$search}%")
                                    ->orWhere('id', 'like', "%{$search}%");
                            });
                    });
                })
                ->groupBy('birim')->pluck('toplam_miktar', 'birim');

        }
        elseif ($activeDashboard === 'bolum_lideri' || $activeDashboard === 'bolum_lider_yardimcisi')
        {
            $leaderStats = $this->bolumService->getLeaderStats($user, request()->all());
            if (isset($leaderStats['tum_personel_listesi']))
            {
                foreach ($leaderStats['tum_personel_listesi'] as $p)
                {
                    $p->cached_total_score = $this->puanService->calculateTotalScore($p);
                }
            }
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($leaderStats, $user_stats);
            if ($user->bolum_id)
            {
                $iadeVerileri = \App\Models\SikayetIadesi::whereHas('musteriSikayeti.sikayetKategori.bolum', fn($q) => $q->where('id', $user->bolum_id))
                    ->with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.iaaProjesi'])
                    ->when(request('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', request('return_start_date')))
                    ->when(request('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', request('return_end_date')))
                    ->when(request('return_search'), function ($q)
                    {
                        $search = request('return_search');
                        $q->where(function ($sq) use ($search)
                        {
                            $sq->where('urun_turu', 'like', "%{$search}%")
                                ->orWhere('iade_sebebi', 'like', "%{$search}%")
                                ->orWhereHas('musteriSikayeti', function ($ssq) use ($search)
                                {
                                    $ssq->where('musteri_adi', 'like', "%{$search}%")
                                        ->orWhere('musteri_sikayet_konusu', 'like', "%{$search}%")
                                        ->orWhere('id', 'like', "%{$search}%");
                                });
                        });
                    })
                    ->latest('iade_tarihi')->paginate(5, ['*'], 'return_page');
            }

        }
        elseif ($activeDashboard === 'direktor')
        {
            $direktorBolumleri = $user->yonetilenBolumler()->with(['machines', 'iaas'])->get();
            $bolumVerileri = [];
            $bolumIds = $direktorBolumleri->pluck('id')->toArray();
            $aggregateStats = $this->bolumService->getDirectorAggregateStats($bolumIds, request()->all());
            $tabOrderKey = "user_pref_" . $user->id . "_direktor_tabs";
            $savedOrder = Setting::where('key', $tabOrderKey)->first();
            if ($savedOrder && !empty($savedOrder->value))
            {
                $orderIds = json_decode($savedOrder->value, true);
                if (is_array($orderIds))
                {
                    $direktorBolumleri = $direktorBolumleri->sortBy(fn($bolum) => ($i = array_search($bolum->id, $orderIds)) !== false ? $i : 999);
                }
            }
            foreach ($direktorBolumleri as $bolum)
            {
                $bolumVerileri[$bolum->id] = $this->bolumService->getLeaderStatsByBolum($bolum, request()->all());
                // Her bölüm için iade verilerini çekiyoruz
                $bolumVerileri[$bolum->id]['iadeVerileri'] = \App\Models\SikayetIadesi::whereHas('musteriSikayeti.sikayetKategori.bolum', fn($q) => $q->where('id', $bolum->id))
                    ->with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.iaaProjesi'])
                    ->when(request('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', request('return_start_date')))
                    ->when(request('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', request('return_end_date')))
                    ->when(request('return_search'), function ($q)
                    {
                        $search = request('return_search');
                        $q->where(function ($sq) use ($search)
                        {
                            $sq->where('urun_turu', 'like', "%{$search}%")
                                ->orWhere('iade_sebebi', 'like', "%{$search}%")
                                ->orWhereHas('musteriSikayeti', function ($ssq) use ($search)
                                {
                                    $ssq->where('musteri_adi', 'like', "%{$search}%")
                                        ->orWhere('musteri_sikayet_konusu', 'like', "%{$search}%")
                                        ->orWhere('id', 'like', "%{$search}%");
                                });
                        });
                    })
                    ->latest('iade_tarihi')->paginate(10, ['*'], 'return_page_' . $bolum->id);
            }
            $stats = ['direktor_bolumleri' => $direktorBolumleri, 'bolum_verileri' => $bolumVerileri, 'direktor_genel_toplam' => $aggregateStats];
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($stats, $user_stats);

        }
        elseif ($activeDashboard === 'hukuk')
        {
            $hukukStats = $this->hukukService->getStats($user, request()->all());
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($hukukStats, $user_stats);

        }
        elseif ($activeDashboard === 'disiplin_kurulu_baskani')
        {
            $kurulStats = $this->disiplinKuruluService->getChairmanStats($user, request()->all());
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($user_stats, $kurulStats); // Kurul stats should override for these roles

        }
        elseif ($activeDashboard === 'disiplin_kurulu_uyesi')
        {
            $kurulStats = $this->disiplinKuruluService->getMemberStats($user, request()->all());
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($user_stats, $kurulStats); // Kurul stats should override for these roles

        }
        else
        {
            $stats = $this->istatistikService->getStats($user);
        }

        // --- ORTAK VERİLER (Online Users, Bekleyen İşler) ---

        $onlineQuery = User::where('last_seen_at', '>=', \Carbon\Carbon::now()->subMinutes(5))
            ->with(['bolum', 'loginActivities']);

        $lastSeenQuery = User::where('last_seen_at', '<', \Carbon\Carbon::now()->subMinutes(5))
            ->with(['bolum', 'loginActivities']);

        // Yetki Bazlı Filtreleme
        if (($activeDashboard === 'bolum_lideri' || $activeDashboard === 'bolum_lider_yardimcisi') && $user->bolum_id)
        {
            $onlineQuery->where('bolum_id', $user->bolum_id);
            $lastSeenQuery->where('bolum_id', $user->bolum_id);
        }
        elseif ($activeDashboard === 'direktor')
        {
            $managedBolumIds = (clone $user->yonetilenBolumler)->pluck('id')->toArray();
            $onlineQuery->whereIn('bolum_id', $managedBolumIds);
            $lastSeenQuery->whereIn('bolum_id', $managedBolumIds);
        }

        $onlineKullanicilar = $onlineQuery->orderBy('last_seen_at', 'desc')->take(10)->get();
        $sonAktifKullanicilar = $lastSeenQuery->orderBy('last_seen_at', 'desc')->take(10)->get();

        $bekleyenProjeDavetleri = $user->gorevliOlduguProjeler()
            ->wherePivot('durum', 'bekliyor')
            ->with('atananTakim.lider')
            ->get();

        // SQL SORGU DÜZELTMESİ (DB Table Names Check)
        // iaa_step_assignments -> iaa_workflow_steps -> iaas (via IaaStepAssignment model logic)
        // IaaStepAssignment table columns: id, iaa_id, iaa_workflow_step_id, user_id, status...
        // 'iaa_steps' diye bir tablo yok, 'iaa_workflow_steps' var.

        // 2026-02-12 Fix: iaa_step_assignments tablosunda 'status' yok.
        // Aktif adım mantığı Iaa modelindeki 'aktifAdim' ilişkisi üzerinden kurulmalı.
        $bekleyenAdimGorevleri = Iaa::whereHas('stepAssignments', function ($q) use ($user)
        {
            $q->where('user_id', $user->id);
        })
            ->whereDoesntHave('musteriSikayeti', function ($q)
            {
                $q->onlyTrashed();
            })
            ->whereNotIn('durum', ['Tamamlandı', 'İptal Edildi', 'Reddedildi']) // Sadece aktif projeler
            ->with(['aktifAdim.sorumlular'])
            ->get()
            ->filter(function ($iaa) use ($user)
            {
                // Projenin aktif bir adımı var mı ve bu adımda kullanıcı sorumlu mu?
                return $iaa->aktifAdim && $iaa->aktifAdim->sorumlular->contains('id', $user->id);
            })
            ->map(function ($iaa) use ($user)
            {
                // Kullanıcının pivot verisine eriş (Assignment tarihi için)
                $sorumlu = $iaa->aktifAdim->sorumlular->where('id', $user->id)->first();
                $createdAt = $sorumlu->pivot->created_at ?? null;

                return (object)[
                    'id' => $sorumlu->pivot->id ?? null, // assignment id
                    'proje_baslik' => $iaa->baslik, // View uyumluluğu (proje_basligi -> proje_baslik)
                    'adim_basligi' => $iaa->aktifAdim->name,
                    'adim_adi' => $iaa->aktifAdim->name, // View uyumluluğu
                    'proje_id' => $iaa->id,
                    'iaa_id' => $iaa->id, // View uyumluluğu
                    'created_at' => $createdAt,
                    'atama_tarihi' => $createdAt, // View uyumluluğu
                    'user_id' => $user->id,
                    'is_revision' => false // Normal görev
                ];
            })
            ->toBase(); // Support\Collection'a kesin dönüşüm (stdClass hatasını önler)

        // === YENİ: REVİZE EDİLEN PROJELERİ EKLE (Sadece Takım Lideri Görür) ===
        $revizeGorevleri = Iaa::where('durum', 'Revize Ediliyor')
            ->whereHas('atananTakim', function ($q) use ($user)
            {
                $q->where('lider_user_id', $user->id); // DÜZELTME: lider_id -> lider_user_id
            })
            ->whereDoesntHave('musteriSikayeti', function ($q)
            {
                $q->onlyTrashed();
            })
            ->get()
            ->map(function ($iaa) use ($user)
            {
                $updatedAt = $iaa->updated_at; // Son işlem tarihi
    
                return (object)[
                    'id' => 'rev-' . $iaa->id, // Benzersiz ID
                    'proje_baslik' => $iaa->baslik . ' (REVİZE EDİLİYOR)', // View uyumluluğu (proje_basligi -> proje_baslik)
                    'adim_basligi' => 'Revizyon Talebi',
                    'adim_adi' => 'Revizyon Talebi', // View uyumluluğu
                    'proje_id' => $iaa->id,
                    'iaa_id' => $iaa->id, // View uyumluluğu
                    'created_at' => $updatedAt,
                    'atama_tarihi' => $updatedAt, // View uyumluluğu
                    'user_id' => $user->id,
                    'is_revision' => true // Revizyon olduğunu belirt
                ];
            });

        // Koleksiyonları birleştir
        $bekleyenAdimGorevleri = $bekleyenAdimGorevleri->merge($revizeGorevleri);

        // === YENİ: BEKLEYEN TAKIM İSTEKLERİ VE DAVETLERİ (ZİL VE DASHBOARD İÇİN) ===
        $bekleyenTakimIstekleri = \App\Models\TakimDavetiyesi::where('davet_edilen_user_id', $user->id)
            ->where('type', 'istek')
            ->where('durum', 'bekliyor')
            ->count();

        $banaGelenDavetler = \App\Models\TakimDavetiyesi::where('davet_edilen_user_id', $user->id)
            ->where('type', 'davet')
            ->where('durum', 'bekliyor')
            ->count();

        // === TOP 5 PERFORMANS VERİSİ (Dönemlik veya Genel) ===
        $start_date = request('start_date');
        $end_date = request('end_date');
        $excludeRoles = ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat'];

        $topPerformersBolumId = (($activeDashboard === 'bolum_lideri' || $activeDashboard === 'bolum_lider_yardimcisi') && $user->bolum_id) ? $user->bolum_id : null;
        $topPerformers = $this->puanService->getRankings($start_date, $end_date, $topPerformersBolumId, 5, $excludeRoles);


        // --- DOĞUM GÜNÜ HATIRLATICI (YENİ) ---
        $birthdayIsActive = Setting::where('key', 'birthday_is_active')->first()?->value ?? '1';
        $upcomingRange = (int)(Setting::where('key', 'birthday_upcoming_days')->first()?->value ?? 7);
        $pastRange = (int)(Setting::where('key', 'birthday_past_days')->first()?->value ?? 3);

        $dogumGunuBugun = collect();
        $dogumGunuYaklasan = collect();
        $dogumGunuGecmis = collect();

        if ($user->is_personnel && $birthdayIsActive == '1')
        {
            $today = now()->startOfDay();
            $nextWeek = $today->copy()->addDays($upcomingRange);
            $lastDays = $today->copy()->subDays($pastRange);

            $internalUsersQuery = User::where('is_personnel', true)
                ->whereNotNull('dogum_tarihi')
                ->whereDoesntHave('roles', function ($q)
                {
                    $q->whereIn('name', ['Müşteri Temsilcisi', 'Müşteri']);
                });

            if (($activeDashboard === 'bolum_lideri' || $activeDashboard === 'bolum_lider_yardimcisi') && $user->bolum_id) {
                $internalUsersQuery->where('bolum_id', $user->bolum_id);
            }

            $internalUsers = $internalUsersQuery->get();

            foreach ($internalUsers as $u)
            {
                // Bu yılki doğum günü
                $bday = $u->dogum_tarihi->copy()->year($today->year)->startOfDay();

                if ($bday->isToday())
                {
                    $dogumGunuBugun->push($u);
                }
                // Yaklaşanlar
                elseif ($bday->isAfter($today) && $bday->isBefore($nextWeek->copy()->addDay()))
                {
                    $dogumGunuYaklasan->push($u);
                }
                // Geçmiştekiler
                elseif ($bday->isBefore($today) && $bday->isAfter($lastDays->copy()->subDay()))
                {
                    $dogumGunuGecmis->push($u);
                }
                // Yıl sonu geçişi
                else
                {
                    $nextYearBday = $bday->copy()->addYear();
                    if ($nextYearBday->isAfter($today) && $nextYearBday->isBefore($nextWeek->copy()->addDay()))
                    {
                        $dogumGunuYaklasan->push($u);
                    }
                }
            }

            $dogumGunuYaklasan = $dogumGunuYaklasan->sortBy(function ($u) use ($today)
            {
                $bday = $u->dogum_tarihi->copy()->year($today->year);
                if ($bday->isBefore($today))
                    $bday->addYear();
                return $bday->timestamp;
            })->take(10);

            $dogumGunuGecmis = $dogumGunuGecmis->sortByDesc(function ($u) use ($today)
            {
                $bday = $u->dogum_tarihi->copy()->year($today->year);
                if ($bday->isAfter($today))
                    $bday->subYear();
                return $bday->timestamp;
            })->take(10);
        }
        // --- DOĞUM GÜNÜ SON ---

        // --- İŞ YILDÖNÜMÜ HATIRLATICI (YENİ) ---
        $anniversaryIsActive = Setting::where('key', 'anniversary_is_active')->first()?->value ?? '1';
        $yildonumuBugun = collect();
        $yildonumuYaklasan = collect();
        $yildonumuGecmis = collect();

        if ($user->is_personnel && $anniversaryIsActive == '1')
        {
            $today = now()->startOfDay();
            // Aynı menzilleri kullanalım (Ayrı ayar isterseniz eklenebilir, şu an birthday menzillerini referans alıyor)
            $uRange = (int)(Setting::where('key', 'birthday_upcoming_days')->first()?->value ?? 7);
            $pRange = (int)(Setting::where('key', 'birthday_past_days')->first()?->value ?? 3);

            $nextWeek = $today->copy()->addDays($uRange);
            $lastDays = $today->copy()->subDays($pRange);

            $internalUsersQuery = User::where('is_personnel', true)
                ->whereNotNull('hire_date')
                ->whereDoesntHave('roles', function ($q)
                {
                    $q->whereIn('name', ['Müşteri Temsilcisi', 'Müşteri']);
                });

            if (($activeDashboard === 'bolum_lideri' || $activeDashboard === 'bolum_lider_yardimcisi') && $user->bolum_id) {
                $internalUsersQuery->where('bolum_id', $user->bolum_id);
            }

            $internalUsers = $internalUsersQuery->get();

            foreach ($internalUsers as $u)
            {
                $annivThisYear = $u->hire_date->copy()->year($today->year)->startOfDay();
                $years = $today->year - $u->hire_date->year;
                if ($years <= 0 && !$annivThisYear->isToday())
                    continue; // Bugün girmişse 0. yıl kutlanabilir belki ama genellikle 1. yıldan başlar.

                $u->is_anniversary_today = false;
                $u->anniversary_years = $years;

                if ($annivThisYear->isToday())
                {
                    $u->is_anniversary_today = true;
                    $yildonumuBugun->push($u);
                }
                elseif ($annivThisYear->isAfter($today) && $annivThisYear->isBefore($nextWeek->copy()->addDay()))
                {
                    $yildonumuYaklasan->push($u);
                }
                elseif ($annivThisYear->isBefore($today) && $annivThisYear->isAfter($lastDays->copy()->subDay()))
                {
                    $yildonumuGecmis->push($u);
                }
                else
                {
                    $nextYearAnniv = $annivThisYear->copy()->addYear();
                    if ($nextYearAnniv->isAfter($today) && $nextYearAnniv->isBefore($nextWeek->copy()->addDay()))
                    {
                        $u->anniversary_years = $years + 1;
                        $yildonumuYaklasan->push($u);
                    }
                }
            }

            $yildonumuYaklasan = $yildonumuYaklasan->sortBy(function ($u) use ($today)
            {
                $anniv = $u->hire_date->copy()->year($today->year);
                if ($anniv->isBefore($today))
                    $anniv->addYear();
                return $anniv->timestamp;
            })->take(10);

            $yildonumuGecmis = $yildonumuGecmis->sortByDesc(function ($u) use ($today)
            {
                $anniv = $u->hire_date->copy()->year($today->year);
                if ($anniv->isAfter($today))
                    $anniv->subYear();
                return $anniv->timestamp;
            })->take(10);
        }
        // --- İŞ YILDÖNÜMÜ SON ---

        // --- YENİ: Aktif Oylama Uyarıları (Relevant Roles) ---
        $activeVotingCases = \App\Models\DisciplinaryCase::where('oylama_aktif', true)
            ->whereNotIn('durum', ['Karar Verildi', 'İptal Edildi', 'İptal'])
            ->with(['user', 'behavior'])
            ->get();

        // SSO Başvurusu Onay Bekleyenler Sayısı (Sadece Superadmin için)
        $pendingSsoApplicationsCount = 0;
        if ($user->hasRole('Superadmin')) {
            $pendingSsoApplicationsCount = User::where('onaylandi_mi', false)->count();
        }

        // View Return
        return view('dashboard', compact(
            'stats',
            'onlineKullanicilar',
            'sonAktifKullanicilar',
            'ekstraTablolar',
            'bolumOnayiBekleyenSayisi',
            'bekleyenProjeDavetleri',
            'bekleyenAdimGorevleri',
            'iadeVerileri',
            'iadeToplamlari',
            'bekleyenTakimIstekleri',
            'banaGelenDavetler',
            'topPerformers',
            'activeVotingCases',
            'yonetilenKategoriler',
            'yonetilenKategoriIds',
            'dogumGunuBugun',
            'dogumGunuYaklasan',
            'dogumGunuGecmis',
            'yildonumuBugun',
            'yildonumuYaklasan',
            'yildonumuGecmis',
            'pendingSsoApplicationsCount'
        ))->with('tumBolumler', $tumBolumler ?? collect())
          ->with('seciliBolumId', $seciliBolumId ?? null)
          ->with('activeDashboard', $activeDashboard);
    }

    /**
     * Kullanıcının aktif dashboard'ını değiştirir ve session'a kaydeder.
     */
    public function switchDashboard(string $view)
    {
        $user = Auth::user();

        $allowedMap = [
            'superadmin' => 'Superadmin',
            'yonetim' => 'Yonetim',
            'kurul' => 'Müşteri Şikayeti Kurulu',
            'cozum_lideri' => 'Müşteri Şikayeti Çözüm Lideri',
            'kalite' => 'Bölüm Kalite Yöneticisi',
            'bolum_lideri' => 'Bölüm Lideri',
            'bolum_lider_yardimcisi' => 'Bölüm Lider Yardımcısı',
            'direktor' => 'Direktör',
            'hukuk' => ['Hukuk Admini', 'Hukuk Yöneticisi'],
            'disiplin_kurulu_baskani' => 'Disiplin Kurulu Başkanı',
            'disiplin_kurulu_uyesi' => 'Disiplin Kurulu Üyesi',
            'standart' => null, // Herkes erişebilir
        ];

        if (!array_key_exists($view, $allowedMap))
        {
            abort(404);
        }

        $requiredRole = $allowedMap[$view];
        if ($requiredRole !== null)
        {
            $hasRole = is_array($requiredRole)
                ? $user->hasRole($requiredRole)
                : $user->hasRole($requiredRole);
            if (!$hasRole)
            {
                abort(403, 'Bu dashboard için yetkiniz yok.');
            }
        }

        session(['active_dashboard_' . $user->id => $view]);

        return redirect()->route('dashboard');
    }

    /**
     * Kullanıcının Puan Detay Sayfası
     */
    public function kullaniciPuanlari(Request $request, User $user)
    {
        // Yetki Kontrolü
        $authUser = Auth::user();

        // 1. Müşteriler (is_personnel = 0) başkasının profilini göremez.
        if (!$authUser->is_personnel)
        {
            abort(403, 'Bu sayfayı görüntüleme yetkiniz yok.');
        }

        // 2. Personel olan herkes, diğer personellerin puanlarını görebilir.
        // Bu yüzden eski kısıtlamayı kaldırıyoruz.
        // if ($authUser->id !== $user->id && !$authUser->hasRole(['Superadmin', 'Yonetim', 'Bölüm Lideri'])) { ... }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = $this->puanService->getDetailedScoreData($user, $startDate, $endDate);

        // === PUAN SENKRONİZASYONU (Eğer tarih filtresi yoksa) ===
        if (!$startDate && !$endDate && $user->is_personnel)
        {
            // getDetailedScoreData zaten 'toplam_puan' verisini döndürüyor.
            $guncelPuan = $data['toplam_puan'];

            if ($user->toplam_puan != $guncelPuan)
            {
                $user->toplam_puan = $guncelPuan;
                $user->save();
            }
        }

        return view('profile.puanlar', array_merge(['user' => $user, 'startDate' => $startDate, 'endDate' => $endDate], $data));
    }

    /**
     * Tüm personellerin bekleyen işlerini gösterir (Yönetim & Superadmin)
     */
    public function tumBekleyenIsler(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['Superadmin', 'Yonetim']))
        {
            abort(403, 'Bu sayfayı görüntüleme yetkiniz yok.');
        }

        $data = $this->superAdminService->getAllPendingWorks($request->all());

        return view('admin.tum-bekleyen-isler', [
            'bekleyenIsler' => $data['bekleyenIsler'],
            'stats' => $data['stats'],
            'bolumler' => $data['dropdowns']['bolumler'],
            'turler' => $data['dropdowns']['turler'],
            'durumlarListesi' => $data['dropdowns']['durumlar']
        ]);
    }

    /**
     * Puan Durumu Sayfası
     */
    /**
     * Puan Durumu Sayfası
     */
    public function puanDurumu(Request $request)
    {
        $userQuery = User::withTrashed()
            ->where('is_personnel', true)
            ->whereDoesntHave('roles', function ($q)
            {
                $q->whereIn('name', ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat']);
            });

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $excludeRoles = ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat'];

        // 1. Personel SıralamasÄ± (Top 10)
        if ($start_date || $end_date)
        {
            // Tarih filtresi varsa servis Ã¼zerinden hesapla
            $topUsers = $this->puanService->getRankings($start_date, $end_date, null, null, $excludeRoles);

            // --- KOLEKSÄ°YON FİLTRELERİ (Tarih varken SQL yetmez Ã§Ã¼nkÃ¼ puan deÄŸiÅŸkendir) ---
            if ($request->filled('user_name'))
            {
                $name = $request->user_name;
                $topUsers = $topUsers->filter(fn($u) => mb_stripos($u->name, $name) !== false);
            }
            if ($request->filled('user_bolum'))
            {
                $bolum = $request->user_bolum;
                $topUsers = $topUsers->filter(fn($u) => $u->bolum && mb_stripos($u->bolum->ad, $bolum) !== false);
            }
            if ($request->filled('user_min_score'))
            {
                $topUsers = $topUsers->filter(fn($u) => $u->period_puan >= $request->user_min_score);
            }
            if ($request->filled('user_max_score'))
            {
                $topUsers = $topUsers->filter(fn($u) => $u->period_puan <= $request->user_max_score);
            }
        }
        else
        {
            // Tarih filtresi yoksa SQL bazlı (HIZLI)
            if ($request->filled('user_name'))
            {
                $userQuery->where('name', 'like', '%' . $request->user_name . '%');
            }
            if ($request->filled('user_bolum'))
            {
                $userQuery->whereHas('bolum', function ($q) use ($request)
                {
                    $q->where('ad', 'like', '%' . $request->user_bolum . '%');
                });
            }
            if ($request->filled('user_min_score'))
            {
                $userQuery->where('toplam_puan', '>=', $request->user_min_score);
            }
            if ($request->filled('user_max_score'))
            {
                $userQuery->where('toplam_puan', '<=', $request->user_max_score);
            }

            $topUsers = $userQuery->orderByDesc('toplam_puan')->get();
            foreach ($topUsers as $u)
                $u->period_puan = $u->toplam_puan;
        }

        // SıralamayÄ± yap ve ilk 10'u al
        $allRankedUsers = $topUsers; // Bölüm hesaplamasÄ± iÃ§in tÃ¼m listeyi sakla
        $topUsers = $topUsers->sortByDesc(fn($u) => $u->period_puan ?? 0)->take(10);

        // BÃ¼tÃ¼n Bölüm Liderlerini bir kerede Ã§ekelim (Performans iÃ§in)
        $allLeaders = User::role('Bölüm Lideri')->get()->groupBy('bolum_id');

        // 4. Bölüm PuanlarÄ± SıralamasÄ±
        $bolumPuanListesi = $allRankedUsers->groupBy('bolum_id')
            ->map(function ($group) use ($allLeaders)
            {
                $bolum = $group->first()->bolum;
                if (!$bolum)
                    return null;

                // Bölüm Lideri (Bu bÃ¶lÃ¼me atanmÄ±ÅŸ lider)
                $lider = $allLeaders->get($bolum->id)?->first();

                // Bölüm Birincisi (Bu grubun iÃ§indeki en yÃ¼ksek puanlÄ± kiÅŸi)
                $birinci = $group->sortByDesc('period_puan')->first();
                $bolum_total = $group->sum('period_puan');
                $birinci_katki_orani = $bolum_total > 0
                    ? round(($birinci->period_puan / $bolum_total) * 100, 1)
                    : 0;

                return (object)[
                    'id' => $bolum->id,
                    'ad' => $bolum->ad,
                    'logo_yolu' => $bolum->logo_yolu,
                    'total_score' => $bolum_total,
                    'lider' => $lider,
                    'birinci' => $birinci,
                    'birinci_katki_orani' => $birinci_katki_orani
                ];
            })
            ->filter()
            ->sortByDesc('total_score')
            ->values();

        // Kendi Puanımı Bul (Vurgu iÃ§in)
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->is_personnel)
        {
            $currentUser->period_puan = ($start_date || $end_date)
                ? $this->puanService->calculateScoreInRange($currentUser, $start_date, $end_date)
                : $currentUser->toplam_puan;
        }


        // 2. Ä°AA TakımlarÄ± Filtreleme (Tur != sikayet)
        $iaaTakimQuery = Takim::where('tur', '!=', 'sikayet');

        if ($request->filled('iaa_team_name'))
        {
            $iaaTakimQuery->where('ad', 'like', '%' . $request->iaa_team_name . '%');
        }
        if ($request->filled('iaa_team_leader'))
        {
            $iaaTakimQuery->whereHas('lider', function ($q) use ($request)
            {
                $q->where('name', 'like', '%' . $request->iaa_team_leader . '%');
            });
        }
        if ($request->filled('iaa_min_score'))
        {
            $iaaTakimQuery->where('toplam_puan', '>=', $request->iaa_min_score);
        }
        if ($request->filled('iaa_max_score'))
        {
            $iaaTakimQuery->where('toplam_puan', '<=', $request->iaa_max_score);
        }

        $iaaTakimlari = $iaaTakimQuery->orderByDesc('toplam_puan')->take(10)->get();

        // 3. Åikayet TakımlarÄ± Filtreleme (Tur == sikayet)
        $sikayetTakimQuery = Takim::where('tur', 'sikayet');

        if ($request->filled('sikayet_team_name'))
        {
            $sikayetTakimQuery->where('ad', 'like', '%' . $request->sikayet_team_name . '%');
        }
        if ($request->filled('sikayet_team_leader'))
        {
            $sikayetTakimQuery->whereHas('lider', function ($q) use ($request)
            {
                $q->where('name', 'like', '%' . $request->sikayet_team_leader . '%');
            });
        }
        if ($request->filled('sikayet_min_score'))
        {
            $sikayetTakimQuery->where('toplam_puan', '>=', $request->sikayet_min_score);
        }
        if ($request->filled('sikayet_max_score'))
        {
            $sikayetTakimQuery->where('toplam_puan', '<=', $request->sikayet_max_score);
        }

        $sikayetTakimlari = $sikayetTakimQuery->orderByDesc('toplam_puan')->take(10)->get();

        return view('dashboard.puan-durumu', compact('topUsers', 'iaaTakimlari', 'sikayetTakimlari', 'currentUser', 'bolumPuanListesi'));
    }

    /**
     * Tüm Bölümler Analiz Sayfası
     */
    public function tumBolumler(Request $request)
    {
        $data = $this->getBolumData($request);

        // Kullanıcıya Özel Özet Verileri (Opsiyonel Ek Mantık)
        $currentUser = auth()->user();
        $myDeptStats = null;
        $myContribution = ['puan' => 0, 'percentage' => 0];

        if ($currentUser->bolum_id)
        {
            $myDeptStats = $data['bolumPuanListesi']->where('id', $currentUser->bolum_id)->first();
            if ($myDeptStats)
            {
                $myPuan = $data['allUsers']->where('id', $currentUser->id)->first()?->period_puan ?? 0;
                $myContribution = [
                    'puan' => $myPuan,
                    'percentage' => $myDeptStats->total_score > 0 ? round(($myPuan / $myDeptStats->total_score) * 100, 1) : 0
                ];
            }
        }

        return view('dashboard.tum-bolumler', array_merge([
            'myDeptStats' => $myDeptStats,
            'myContribution' => $myContribution,
        ], $data));
    }

    /**
     * Bölüm Puan Detay Sayfası
     */
    public function bolumPuanlari(Request $request, Bolum $bolum)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $excludeRoles = ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat'];

        $users = User::where('bolum_id', $bolum->id)
            ->where('is_personnel', true)
            ->whereDoesntHave('roles', function ($q) use ($excludeRoles)
            {
                $q->whereIn('name', $excludeRoles);
            })
            ->get();

        foreach ($users as $user)
        {
            $user->period_puan = ($start_date || $end_date)
                ? $this->puanService->calculateScoreInRange($user, $start_date, $end_date)
                : $user->toplam_puan;
        }

        $users = $users->sortByDesc('period_puan')->values(); // Sıralama ve index sıfırlama
        $totalBolumPuan = $users->sum('period_puan');

        // Yaka Ayrımı İstatistikleri
        $whiteCollar = $users->where('is_mavi_yaka', false);
        $blueCollar = $users->where('is_mavi_yaka', true);

        $stats = (object)[
            'total_puan' => $totalBolumPuan,
            'white_puan' => $whiteCollar->sum('period_puan'),
            'blue_puan' => $blueCollar->sum('period_puan'),
            'white_count' => $whiteCollar->count(),
            'blue_count' => $blueCollar->count(),
            'white_percentage' => $totalBolumPuan > 0
                ? round(($whiteCollar->sum('period_puan') / $totalBolumPuan) * 100, 1)
                : 0,
            'blue_percentage' => $totalBolumPuan > 0
                ? round(($blueCollar->sum('period_puan') / $totalBolumPuan) * 100, 1)
                : 0,
        ];

        // Kişisel Katkı Yüzdeleri
        foreach ($users as $user)
        {
            $user->contribution_percentage = $totalBolumPuan > 0
                ? round(($user->period_puan / $totalBolumPuan) * 100, 1)
                : 0;
        }

        // --- KATEGORİ BAZLI DÖKÜM (YENİ) ---
        $categoryBreakdown = $this->calculateCategoryBreakdown($users, $start_date, $end_date);

        return view('dashboard.bolum-puanlar', compact('bolum', 'users', 'totalBolumPuan', 'start_date', 'end_date', 'stats', 'categoryBreakdown'));
    }

    /**
     * Bölüm Detay Analizi Excel Dışa Aktar
     */
    public function exportBolumDetayExcel(Request $request, Bolum $bolum)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $excludeRoles = ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat'];

        $users = User::where('bolum_id', $bolum->id)
            ->where('is_personnel', true)
            ->whereDoesntHave('roles', function ($q) use ($excludeRoles)
            {
                $q->whereIn('name', $excludeRoles);
            })
            ->get();

        foreach ($users as $user)
        {
            $user->period_puan = ($start_date || $end_date)
                ? $this->puanService->calculateScoreInRange($user, $start_date, $end_date)
                : $user->toplam_puan;
        }

        $users = $users->sortByDesc('period_puan')->values();
        $totalBolumPuan = $users->sum('period_puan');
        $breakdown = $this->calculateCategoryBreakdown($users, $start_date, $end_date);

        $grossTotal = $breakdown['iaa_success']['score'] +
                      $breakdown['iaa_suggest']['score'] +
                      $breakdown['complaint_resolution']['score'] +
                      $breakdown['complaint_entry']['score'];

        $penaltyTotal = $breakdown['discipline']['score'];
        $netTotal = $grossTotal + $penaltyTotal;

        // Önemli: Ranking'deki toplam puan ile özetteki net puanı eşitleyelim
        $totalBolumPuanForPercentage = max(1, $netTotal);

        return \Excel::download(new \App\Exports\BolumDetayExport(
            $bolum,
            $users,
            $netTotal,
            $breakdown,
            $start_date,
            $end_date,
            $grossTotal,
            $penaltyTotal,
            $netTotal
        ), $bolum->ad . '_Analiz.xlsx');
    }

    /**
     * Bölüm Detay Analizi PDF Dışa Aktar
     */
    public function exportBolumDetayPdf(Request $request, Bolum $bolum)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $excludeRoles = ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat'];

        $users = User::where('bolum_id', $bolum->id)
            ->where('is_personnel', true)
            ->whereDoesntHave('roles', function ($q) use ($excludeRoles)
            {
                $q->whereIn('name', $excludeRoles);
            })
            ->get();

        foreach ($users as $user)
        {
            $user->period_puan = ($start_date || $end_date)
                ? $this->puanService->calculateScoreInRange($user, $start_date, $end_date)
                : $user->toplam_puan;
        }

        $users = $users->sortByDesc('period_puan')->values();
        $totalBolumPuan = $users->sum('period_puan');
        $breakdown = $this->calculateCategoryBreakdown($users, $start_date, $end_date);

        $grossTotal = $breakdown['iaa_success']['score'] +
                      $breakdown['iaa_suggest']['score'] +
                      $breakdown['complaint_resolution']['score'] +
                      $breakdown['complaint_entry']['score'];

        $penaltyTotal = $breakdown['discipline']['score'];
        $netTotal = $grossTotal + $penaltyTotal;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.exports.bolum-detay-pdf', [
            'bolum' => $bolum,
            'users' => $users,
            'totalBolumPuan' => $netTotal,
            'breakdown' => $breakdown,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'grossTotal' => $grossTotal,
            'penaltyTotal' => abs($penaltyTotal),
            'netTotal' => $netTotal
        ])->setPaper('a4', 'landscape');

        return $pdf->download($bolum->ad . '_Analiz.pdf');
    }

    /**
     * Tüm Personel Listesi (Filtreli ve Sayfalı)
     */
    public function tumPersonel(Request $request)
    {
        $query = User::withTrashed()
            ->where('is_personnel', true)
            ->whereDoesntHave('roles', function ($q)
            {
                $q->whereIn('name', ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat']);
            });

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $excludeRoles = ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat'];

        if ($start_date || $end_date)
        {
            // Tarih seçiliyse sayfalama koleksiyon bazlı olacak
            $allUsers = $this->puanService->getRankings($start_date, $end_date, null, null, $excludeRoles);

            // --- KOLEKSÄ°YON FİLTRELERİ ---
            if ($request->filled('name'))
            {
                $name = $request->name;
                $allUsers = $allUsers->filter(fn($u) => mb_stripos($u->name, $name) !== false);
            }
            if ($request->filled('bolum'))
            {
                $bolum = $request->bolum;
                $allUsers = $allUsers->filter(fn($u) => $u->bolum && mb_stripos($u->bolum->ad, $bolum) !== false);
            }
        }
        else
        {
            // Tarih seÃ§ili değilse SQL bazlı
            if ($request->filled('name'))
            {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            if ($request->filled('bolum'))
            {
                $query->whereHas('bolum', function ($q) use ($request)
                {
                    $q->where('ad', 'like', '%' . $request->bolum . '%');
                });
            }
            $allUsers = $query->orderByDesc('toplam_puan')->get();
            foreach ($allUsers as $u)
                $u->period_puan = $u->toplam_puan;
        }

        // Sıralama
        $allUsers = $allUsers->sortByDesc(fn($u) => $u->period_puan ?? 0);

        // Kendi Puanımı Bul
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->is_personnel)
        {
            $currentUser->period_puan = ($start_date || $end_date)
                ? $this->puanService->calculateScoreInRange($currentUser, $start_date, $end_date)
                : $currentUser->toplam_puan;
        }

        // Sayfalama
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 20;
        $items = $allUsers->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allUsers->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );


        return view('dashboard.tum-personel', compact('users', 'currentUser'));
    }

    /**
     * Takım Puan Detay Sayfası
     */
    public function takimPuanlari(Takim $takim)
    {
        $data = $this->puanService->getTeamDetailedScoreData($takim);
        return view('dashboard.takim-puanlari', array_merge(['takim' => $takim], $data));
    }

    /**
     * Puan Senkronizasyonu (Manual Tetikleme)
     */
    public function syncAllUserPoints()
    {
        // 1. Kullanıcı Puanlarını Senkronize Et
        $users = User::withTrashed()->where('is_personnel', true)->get();
        foreach ($users as $user)
        {
            /** @var \App\Models\User $user */
            $score = $this->puanService->calculateTotalScore($user);
            $user->toplam_puan = $score;
            $user->save();
        }

        // 2. Takım Puanlarını Senkronize Et
        $takimlar = Takim::all();
        foreach ($takimlar as $takim)
        {
            $data = $this->puanService->getTeamDetailedScoreData($takim);
            $takim->toplam_puan = $data['hesaplananPuan'] ?? 0;
            $takim->save();
        }

        return redirect()->route('admin.sistem-ayarlari.index')
            ->with('success', 'Tüm kullanıcı ve takım puanları başarıyla senkronize edildi.')
            ->with('activeTab', 'finans');
    }

    /**
     * Direktör dashboard sekme sıralamasını kaydeder.
     */
    public function saveTabOrder(Request $request)
    {
        $user = Auth::user();
        $order = $request->input('order');

        if (!is_array($order))
        {
            return response()->json(['success' => false, 'message' => 'Geçersiz veri.'], 400);
        }

        $tabOrderKey = "user_pref_" . $user->id . "_direktor_tabs";

        Setting::updateOrCreate(
            ['key' => $tabOrderKey],
            ['value' => json_encode($order)]
        );

        return response()->json(['success' => true]);
    }

    /**
     * TÃ¼m Bölümler Analizi Excel Dışa Aktar
     */
    public function exportBolumAnalizExcel(Request $request)
    {
        $data = $this->getBolumData($request);
        return Excel::download(new BolumAnalizExport($data), 'bolum_analizi_' . now()->format('d_m_Y') . '.xlsx');
    }

    /**
     * TÃ¼m Bölümler Analizi PDF Dışa Aktar
     */
    public function exportBolumAnalizPdf(Request $request)
    {
        $data = $this->getBolumData($request);

        $pdf = Pdf::loadView('dashboard.exports.bolum-analiz-pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('bolum_analizi_' . now()->format('d_m_Y') . '.pdf');
    }

    /**
     * Ortak Veri Çekme Metodu
     */
    private function getBolumData(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $excludeRoles = ['Superadmin', 'Yonetim', 'Müşteri Temsilcisi', 'Müşteri', 'Dış Avukat'];

        if ($start_date || $end_date)
        {
            // Parametre sırası: $startDate, $endDate, $bolumId, $limit, $excludeRoles
            $allUsers = $this->puanService->getRankings($start_date, $end_date, null, null, $excludeRoles);
        }
        else
        {
            $allUsers = User::withTrashed()
                ->where('is_personnel', true)
                ->whereDoesntHave('roles', function ($q) use ($excludeRoles)
                {
                    $q->whereIn('name', $excludeRoles);
                })
                ->get();
            foreach ($allUsers as $u)
            {
                // Filtreli tarihler yoksa toplam puan gelsin
                $u->period_puan = $u->toplam_puan;
            }
        }

        // --- GLOBAL ÖZET HESAPLAMA (Tüm Bölümler İçin) ---
        $userIds = $allUsers->pluck('id')->toArray();
        $globalBreakdown = $this->calculateCategoryBreakdown($allUsers, $start_date, $end_date);

        // Net Sonuç: Tüm personellerin dönem içindeki puanlarının toplamıdır (Bölümsüzler dahil en doğru veri)
        $netTotal = $allUsers->sum('period_puan');

        // Disiplin: Kesintileri ayrıca çekelim
        $penaltyQuery = \App\Models\DisciplinaryCase::whereIn('user_id', $userIds)
             ->where('durum', 'Karar Verildi')
             ->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)');
        $this->applyDateFilter($penaltyQuery, $start_date, $end_date, 'updated_at');
        $penaltyTotal = abs($penaltyQuery->sum('hesaplanan_puan'));

        // Brüt Başarı: Net sonuçtan kesintileri çıkararak (ekleyerek) bulalım
        $grossTotal = $netTotal + $penaltyTotal;

        $allLeaders = User::role('Bölüm Lideri')->get()->groupBy('bolum_id');

        $bolumPuanListesi = $allUsers->groupBy(function ($u)
        {
            return $u->bolum_id && $u->bolum ? $u->bolum_id : 0;
        })
            ->map(function ($group) use ($allLeaders)
            {
                $firstInGroup = $group->first();
                $bolum = $firstInGroup->bolum;

                $ad = $bolum ? $bolum->ad : 'Bölüm Dışı / Genel';
                $logo = $bolum ? $bolum->logo_yolu : null;
                $id = $bolum ? $bolum->id : 0;

                $birinci = $group->sortByDesc('period_puan')->first();
                $bolum_total = $group->sum('period_puan');

                return (object)[
                    'id' => $id,
                    'ad' => $ad,
                    'logo_yolu' => $logo,
                    'total_score' => $bolum_total,
                    'lider' => $bolum ? $bolum->lider : null,
                    'birinci' => $birinci,
                    'birinci_katki_orani' => $bolum_total > 0 ? round(($birinci->period_puan / $bolum_total) * 100, 1) : 0
                ];
            })
            ->filter()
            ->sortBy('ad')
            ->sortByDesc('total_score');

        if ($request->filled('dept_name'))
        {
            $bolumPuanListesi = $bolumPuanListesi->filter(fn($b) => mb_stripos($b->ad, $request->dept_name) !== false);
        }
        if ($request->filled('dept_leader'))
        {
            $bolumPuanListesi = $bolumPuanListesi->filter(fn($b) => $b->lider && mb_stripos($b->lider->name, $request->dept_leader) !== false);
        }

        $bolumPuanListesi = $bolumPuanListesi->values();

        // Dominans Analizi Hesapla (Bölüm Puan Listesi üzerinden)
        $dominanceStats = [
            'top1' => ['percent' => 0, 'name' => '-', 'score' => 0],
            'top2' => ['percent' => 0, 'name' => '-', 'score' => 0],
            'top3' => ['percent' => 0, 'name' => '-', 'score' => 0],
        ];
        $dominanceDenominator = $grossTotal > 0 ? $grossTotal : 1;
        foreach ([0, 1, 2] as $i)
        {
            if (isset($bolumPuanListesi[$i]))
            {
                $item = $bolumPuanListesi[$i];
                $dominanceStats['top' . ($i + 1)] = [
                    'percent' => round(($item->total_score / $dominanceDenominator) * 100, 1),
                    'name' => $item->ad,
                    'score' => $item->total_score
                ];
            }
        }

        return [
            'allUsers' => $allUsers,
            'bolumPuanListesi' => $bolumPuanListesi,
            'grossTotal' => $grossTotal,
            'penaltyTotal' => abs($penaltyTotal),
            'netTotal' => $netTotal,
            'categoryBreakdown' => $globalBreakdown,
            'breakdown' => $globalBreakdown, // Exportlar için eklendi
            'dominanceStats' => $dominanceStats,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }

    /**
     * Departman bazlı kategori dökümünü hesaplar.
     */
    private function calculateCategoryBreakdown($users, $startDate = null, $endDate = null)
    {
        $userIds = collect($users)->pluck('id')->toArray();

        $breakdown = [
            'iaa_success' => ['label' => 'IAA Başarısı', 'score' => 0, 'icon' => 'iaa'],
            'complaint_resolution' => ['label' => 'Müşteri Şikayeti Çözümü', 'score' => 0, 'icon' => 'resol'],
            'iaa_suggest' => ['label' => 'İAA Önerileri', 'score' => 0, 'icon' => 'suggest'],
            'complaint_entry' => ['label' => 'Müşteri Şikayeti Girişi', 'score' => 0, 'icon' => 'entry'],
            'discipline' => ['label' => 'Disiplin', 'score' => 0, 'icon' => 'disc'],
        ];

        // 1 & 2. IAA Projeleri (Başarı ve Şikayet Çözümü Ayrımı)
        $projects = Iaa::where('durum', 'Tamamlandı')
            ->where('puan', '>', 0)
            ->where(function ($q) use ($userIds)
            {
                // Lider, Squad veya Gönderen olan projeler
                $q->whereIn('atanan_takim_id', Takim::whereIn('lider_user_id', $userIds)->pluck('id'))
                  ->orWhereHas('projeEkibi', fn($sq) => $sq->whereIn('user_id', $userIds))
                  ->orWhereIn('atanan_takim_id', DB::table('takim_user')->whereIn('user_id', $userIds)->pluck('takim_id'))
                  ->orWhereIn('gonderen_user_id', $userIds);
            });

        $this->applyDateFilter($projects, $startDate, $endDate, 'updated_at');
        $projectList = $projects->get();

        foreach ($projectList as $p)
        {
            $isComplaint = $p->musteriSikayeti()->exists();
            if ($isComplaint)
            {
                $breakdown['complaint_resolution']['score'] += $p->puan;
            }
            else
            {
                $breakdown['iaa_success']['score'] += $p->puan;
            }
        }

        // 3. İAA Önerileri
        $oneriPuani = Setting::where('key', 'iaa_oneri_puani')->value('value') ?? 0;
        $onerilerQuery = Iaa::whereIn('gonderen_user_id', $userIds)
            ->whereNotIn('durum', ['Taslak', 'Onay Bekliyor', 'Reddedildi']);
        $this->applyDateFilter($onerilerQuery, $startDate, $endDate, 'created_at');
        $breakdown['iaa_suggest']['score'] = $onerilerQuery->count() * $oneriPuani;

        // 4. Müşteri Şikayeti Girişi
        $sikayetGirisPuani = Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0;
        $sikayetQuery = MusteriSikayeti::whereIn('olusturan_kurul_uyesi_id', $userIds)
            ->whereHas('olusturanKurulUyesi', function ($q)
            {
                $q->whereDoesntHave('roles', function ($rq)
                {
                    $rq->whereIn('name', ['Superadmin', 'Müşteri']);
                });
            })
            ->where('musteri_durum', '!=', 'Talep')
            ->whereDoesntHave('iaaProjesi', function ($q)
            {
                $q->whereIn('durum', ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']);
            });
        $this->applyDateFilter($sikayetQuery, $startDate, $endDate, 'created_at');
        $breakdown['complaint_entry']['score'] = $sikayetQuery->count() * $sikayetGirisPuani;

        // 5. Disiplin
        $cezalarQuery = \App\Models\DisciplinaryCase::whereIn('user_id', $userIds)
            ->where('durum', 'Karar Verildi')
            ->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)');
        $this->applyDateFilter($cezalarQuery, $startDate, $endDate, 'updated_at');
        $breakdown['discipline']['score'] = -$cezalarQuery->sum('hesaplanan_puan');

        // Toplam Departman Puanı (Katkı payı için)
        $totalDeptPuan = array_sum(array_column($breakdown, 'score'));

        foreach ($breakdown as $key => $val)
        {
            $breakdown[$key]['percentage'] = $totalDeptPuan > 0
                ? round(($val['score'] / $totalDeptPuan) * 100, 1)
                : 0;
        }

        // Büyükten küçüğe sırala
        uasort($breakdown, fn($a, $b) => $b['score'] <=> $a['score']);

        return $breakdown;
    }

    /**
     * Tarih filtresini sorguya uygular.
     */
    private function applyDateFilter($query, $startDate, $endDate, $column = 'updated_at')
    {
        if ($startDate)
            $query->whereDate($column, '>=', $startDate);
        if ($endDate)
            $query->whereDate($column, '<=', $endDate);
        return $query;
    }

    /**
     * Şifre değiştirme uyarısını (banner) kalıcı olarak kapatır.
     */
    /**
     * Müşteri Temsilcileri için aktif firma değiştirme işlemi.
     */
    public function switchCustomer(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id'
        ]);

        $user = Auth::user();
        $targetId = (int)$request->customer_id;
        
        \Log::info("Switching customer for user {$user->id} to {$targetId}");

        // Yetki kontrolü: Kullanıcı bu firmaya yetkili mi?
        $hasAccess = ($user->customer_id == $targetId) || 
                     $user->customers()->where('customers.id', $targetId)->exists();

        if (!$hasAccess) {
            \Log::warning("Access denied for user {$user->id} to customer {$targetId}");
            return back()->with('error', 'Bu firmaya erişim yetkiniz bulunmamaktadır.');
        }

        // Seçimi session'da sakla
        session(['active_customer_id_' . $user->id => $targetId]);
        
        \Log::info("Session updated for user {$user->id}: active_customer_id = " . session('active_customer_id_' . $user->id));

        return redirect()->route('dashboard')->with('success', 'Aktif firma başarıyla değiştirildi.');
    }

    public function dismissPasswordAlert()
    {
        $user = Auth::user();
        if ($user)
        {
            $user->update(['dismissed_password_alert' => true]);
        }
        return response()->json(['success' => true]);
    }
}
