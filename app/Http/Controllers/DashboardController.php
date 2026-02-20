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

    public function __construct(
        \App\Services\Dashboard\SuperAdminDashboardService $superAdminService,
        \App\Services\Dashboard\YonetimDashboardService $yonetimService,
        \App\Services\Dashboard\KullaniciPuanService $puanService,
        \App\Services\Dashboard\KullaniciIstatistikService $istatistikService,
        \App\Services\Dashboard\BolumDashboardService $bolumService,
        \App\Services\Dashboard\SikayetDashboardService $sikayetService,
        \App\Services\Dashboard\HukukDashboardService $hukukService,
        \App\Services\Dashboard\MusteriDashboardService $musteriService
    ) {
        $this->superAdminService = $superAdminService;
        $this->yonetimService = $yonetimService;
        $this->puanService = $puanService;
        $this->istatistikService = $istatistikService;
        $this->bolumService = $bolumService;
        $this->sikayetService = $sikayetService;
        $this->hukukService = $hukukService;
        $this->musteriService = $musteriService;
    }

    /**
     * Dashboard ana sayfasını gösterir.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. PUAN SENKRONİZASYONU (Personel İse)
        if ($user->is_personnel) {
            $gercekPuan = $this->puanService->calculateTotalScore($user);
            if ($user->toplam_puan != $gercekPuan) {
                $user->toplam_puan = $gercekPuan;
                $user->save();
            }
        }

        // 2. MÜŞTERİ DASHBOARD (Personel Değilse)
        if (!$user->is_personnel) {
            $stats = $this->musteriService->getStats($user);
            return view('dashboard', compact('user', 'stats'))->with('is_musteri_dashboard', true);
        }

        // 3. PERSONEL DASHBOARD (Rol Bazlı)
        $stats = [];
        $ekstraTablolar = [];
        $bolumOnayiBekleyenSayisi = 0;
        $iadeVerileri = null; // Default null
        $iadeToplamlari = []; // Default empty

        // A) SUPERADMIN
        if ($user->hasRole('Superadmin')) {
            $stats = $this->superAdminService->getStats();
            $ekstraTablolar = $this->superAdminService->getExtraTables();
            $bolumOnayiBekleyenSayisi = Iaa::where('durum', 'Bölüm Onayı Bekliyor')->count();

            // İADE TABLOSU VERİLERİ (V5.11)
            $iadeVerileri = \App\Models\SikayetIadesi::with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.iaaProjesi', 'musteriSikayeti.customer'])
                ->when(request('return_start_date'), function ($q) {
                    $q->whereDate('iade_tarihi', '>=', request('return_start_date'));
                })
                ->when(request('return_end_date'), function ($q) {
                    $q->whereDate('iade_tarihi', '<=', request('return_end_date'));
                })
                ->latest('iade_tarihi')
                ->paginate(5, ['*'], 'return_page');

            // İADE TOPLAMLARI (Birim Bazlı) - V5.12
            $iadeToplamlari = \App\Models\SikayetIadesi::select('birim', \DB::raw('SUM(miktar) as toplam_miktar'))
                ->when(request('return_start_date'), function ($q) {
                    $q->whereDate('iade_tarihi', '>=', request('return_start_date'));
                })
                ->when(request('return_end_date'), function ($q) {
                    $q->whereDate('iade_tarihi', '<=', request('return_end_date'));
                })
                ->groupBy('birim')
                ->pluck('toplam_miktar', 'birim'); // ['Adet' => 150, 'KG' => 5000]

            // B) YÖNETİM (YENİ PORTAL)
        } elseif ($user->hasRole('Yonetim')) {
            $stats = $this->yonetimService->getStats();

            // C) MÜŞTERİ ŞİKAYETİ KURULU
        } elseif ($user->hasRole('Müşteri Şikayeti Kurulu')) {
            $kurul_stats = $this->sikayetService->getBoardStats();
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($kurul_stats, $user_stats);

            // D) ÇÖZÜM LİDERİ
        } elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $leaderStats = $this->sikayetService->getLeaderStats($user);
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($leaderStats, $user_stats);

            // E) BÖLÜM KALİTE YÖNETİCİSİ
        } elseif ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $qualityStats = $this->bolumService->getQualityStats($user);
            $bolumOnayiBekleyenSayisi = $qualityStats['bolum_onay_sayisi'];
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($qualityStats, $user_stats);

            // F) BÖLÜM LİDERİ
        } elseif ($user->hasRole('Bölüm Lideri')) {
            $leaderStats = $this->bolumService->getLeaderStats($user, request()->all());

            // Personel puanlarını hesapla (Servis üzerinden)
            if (isset($leaderStats['tum_personel_listesi'])) {
                foreach ($leaderStats['tum_personel_listesi'] as $p) {
                    $p->cached_total_score = $this->puanService->calculateTotalScore($p);
                }
            }
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($leaderStats, $user_stats);

            // İADE TABLOSU VERİLERİ (Bölümüne Ait)
            if ($user->bolum_id) {
                $iadeVerileri = \App\Models\SikayetIadesi::whereHas('musteriSikayeti.sikayetKategori.bolum', function ($q) use ($user) {
                    $q->where('id', $user->bolum_id);
                })
                    ->with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.iaaProjesi'])
                    ->when(request('return_start_date'), function ($q) {
                        $q->whereDate('iade_tarihi', '>=', request('return_start_date'));
                    })
                    ->when(request('return_end_date'), function ($q) {
                        $q->whereDate('iade_tarihi', '<=', request('return_end_date'));
                    })
                    ->latest('iade_tarihi')
                    ->paginate(5, ['*'], 'return_page');
            }

            // G) DİREKTÖR DASHBOARD (YENİ)
        } elseif ($user->hasRole('Direktör')) {
            $direktorBolumleri = $user->yonetilenBolumler()->with(['machines', 'iaas'])->get();
            $bolumVerileri = [];
            $bolumIds = $direktorBolumleri->pluck('id')->toArray();

            // Agrega Verileri Servis Üzerinden Al (V5.20)
            $aggregateStats = $this->bolumService->getDirectorAggregateStats($bolumIds, request()->all());

            // --- SEKME SIRALAMASI (YENİ - V5.32) ---
            $tabOrderKey = "user_pref_" . $user->id . "_direktor_tabs";
            $savedOrder = Setting::where('key', $tabOrderKey)->first();

            if ($savedOrder && !empty($savedOrder->value)) {
                $orderIds = json_decode($savedOrder->value, true);
                if (is_array($orderIds)) {
                    $direktorBolumleri = $direktorBolumleri->sortBy(function ($bolum) use ($orderIds) {
                        $index = array_search($bolum->id, $orderIds);
                        return $index !== false ? $index : 999;
                    });
                }
            }

            foreach ($direktorBolumleri as $bolum) {
                $bolumVerileri[$bolum->id] = $this->bolumService->getLeaderStatsByBolum($bolum, request()->all());
            }

            $stats = [
                'direktor_bolumleri' => $direktorBolumleri,
                'bolum_verileri' => $bolumVerileri,
                'direktor_genel_toplam' => $aggregateStats
            ];
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($stats, $user_stats);

            // G) HUKUK DASHBOARD
        } elseif ($user->hasRole(['Hukuk Admini', 'Hukuk Yöneticisi'])) {
            $hukukStats = $this->hukukService->getStats($user, request()->all());
            $user_stats = $this->istatistikService->getStats($user);
            $stats = array_merge($hukukStats, $user_stats);

            // H) STANDART KULLANICI
        } else {
            $stats = $this->istatistikService->getStats($user);
        }

        // --- ORTAK VERİLER (Online Users, Bekleyen İşler) ---

        $onlineKullanicilar = User::where('last_seen_at', '>=', \Carbon\Carbon::now()->subMinutes(5))
            ->orderBy('last_seen_at', 'desc')
            ->take(10)
            ->get();

        // Son Görülenler (Son 5 dakikadan daha önce)
        $sonAktifKullanicilar = User::where('last_seen_at', '<', \Carbon\Carbon::now()->subMinutes(5))
            ->orderBy('last_seen_at', 'desc')
            ->take(10)
            ->get();

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
        $bekleyenAdimGorevleri = Iaa::whereHas('stepAssignments', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->whereNotIn('durum', ['Tamamlandı', 'İptal Edildi', 'Reddedildi']) // Sadece aktif projeler
            ->with(['aktifAdim.sorumlular'])
            ->get()
            ->filter(function ($iaa) use ($user) {
                // Projenin aktif bir adımı var mı ve bu adımda kullanıcı sorumlu mu?
                return $iaa->aktifAdim && $iaa->aktifAdim->sorumlular->contains('id', $user->id);
            })
            ->map(function ($iaa) use ($user) {
                // Kullanıcının pivot verisine eriş (Assignment tarihi için)
                $sorumlu = $iaa->aktifAdim->sorumlular->where('id', $user->id)->first();
                $createdAt = $sorumlu->pivot->created_at ?? null;

                return (object) [
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
            ->whereHas('atananTakim', function ($q) use ($user) {
                $q->where('lider_user_id', $user->id); // DÜZELTME: lider_id -> lider_user_id
            })
            ->get()
            ->map(function ($iaa) use ($user) {
                $updatedAt = $iaa->updated_at; // Son işlem tarihi
    
                return (object) [
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
            'iadeToplamlari'
        ));
    }

    /**
     * Kullanıcının Puan Detay Sayfası
     */
    /**
     * Kullanıcının Puan Detay Sayfası
     */
    public function kullaniciPuanlari(Request $request, User $user)
    {
        // Yetki Kontrolü
        $authUser = Auth::user();

        // 1. Müşteriler (is_personnel = 0) başkasının profilini göremez.
        if (!$authUser->is_personnel) {
            abort(403, 'Bu sayfayı görüntüleme yetkiniz yok.');
        }

        // 2. Personel olan herkes, diğer personellerin puanlarını görebilir.
        // Bu yüzden eski kısıtlamayı kaldırıyoruz.
        // if ($authUser->id !== $user->id && !$authUser->hasRole(['Superadmin', 'Yonetim', 'Bölüm Lideri'])) { ... }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = $this->puanService->getDetailedScoreData($user, $startDate, $endDate);

        // === PUAN SENKRONİZASYONU (Eğer tarih filtresi yoksa) ===
        if (!$startDate && !$endDate && $user->is_personnel) {
            // getDetailedScoreData zaten 'toplam_puan' verisini döndürüyor.
            $guncelPuan = $data['toplam_puan'];

            if ($user->toplam_puan != $guncelPuan) {
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
        if (!$user->hasRole(['Superadmin', 'Yonetim'])) {
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
        // 1. Personel Filtreleme & Liste
        $userQuery = User::where('is_personnel', true);

        if ($request->filled('user_name')) {
            $userQuery->where('name', 'like', '%' . $request->user_name . '%');
        }
        if ($request->filled('user_bolum')) {
            $userQuery->whereHas('bolum', function ($q) use ($request) {
                $q->where('ad', 'like', '%' . $request->user_bolum . '%');
            });
        }
        if ($request->filled('user_min_score')) {
            $userQuery->where('toplam_puan', '>=', $request->user_min_score);
        }
        if ($request->filled('user_max_score')) {
            $userQuery->where('toplam_puan', '<=', $request->user_max_score);
        }

        $topUsers = $userQuery->orderByDesc('toplam_puan')->take(10)->get();

        // 2. İAA Takımları Filtreleme (Tur != sikayet)
        $iaaTakimQuery = Takim::where('tur', '!=', 'sikayet');

        if ($request->filled('iaa_team_name')) {
            $iaaTakimQuery->where('ad', 'like', '%' . $request->iaa_team_name . '%');
        }
        if ($request->filled('iaa_team_leader')) {
            $iaaTakimQuery->whereHas('lider', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->iaa_team_leader . '%');
            });
        }
        if ($request->filled('iaa_min_score')) {
            $iaaTakimQuery->where('toplam_puan', '>=', $request->iaa_min_score);
        }
        if ($request->filled('iaa_max_score')) {
            $iaaTakimQuery->where('toplam_puan', '<=', $request->iaa_max_score);
        }

        $iaaTakimlari = $iaaTakimQuery->orderByDesc('toplam_puan')->take(10)->get();

        // 3. Şikayet Takımları Filtreleme (Tur == sikayet)
        $sikayetTakimQuery = Takim::where('tur', 'sikayet');

        if ($request->filled('sikayet_team_name')) {
            $sikayetTakimQuery->where('ad', 'like', '%' . $request->sikayet_team_name . '%');
        }
        if ($request->filled('sikayet_team_leader')) {
            $sikayetTakimQuery->whereHas('lider', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->sikayet_team_leader . '%');
            });
        }
        if ($request->filled('sikayet_min_score')) {
            $sikayetTakimQuery->where('toplam_puan', '>=', $request->sikayet_min_score);
        }
        if ($request->filled('sikayet_max_score')) {
            $sikayetTakimQuery->where('toplam_puan', '<=', $request->sikayet_max_score);
        }

        $sikayetTakimlari = $sikayetTakimQuery->orderByDesc('toplam_puan')->take(10)->get();

        return view('dashboard.puan-durumu', compact('topUsers', 'iaaTakimlari', 'sikayetTakimlari'));
    }

    /**
     * Tüm Personel Listesi (Filtreli ve Sayfalı)
     */
    public function tumPersonel(Request $request)
    {
        $query = User::where('is_personnel', true);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('bolum')) {
            $query->whereHas('bolum', function ($q) use ($request) {
                $q->where('ad', 'like', '%' . $request->bolum . '%');
            });
        }
        if ($request->filled('min_score')) {
            $query->where('toplam_puan', '>=', $request->min_score);
        }
        if ($request->filled('max_score')) {
            $query->where('toplam_puan', '<=', $request->max_score);
        }

        $users = $query->orderByDesc('toplam_puan')->paginate(20)->withQueryString();

        return view('dashboard.tum-personel', compact('users'));
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
        $users = User::where('is_personnel', true)->get();
        foreach ($users as $user) {
            /** @var \App\Models\User $user */
            $score = $this->puanService->calculateTotalScore($user);
            $user->toplam_puan = $score;
            $user->save();
        }

        // 2. Takım Puanlarını Senkronize Et
        $takimlar = Takim::all();
        foreach ($takimlar as $takim) {
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

        if (!is_array($order)) {
            return response()->json(['success' => false, 'message' => 'Geçersiz veri.'], 400);
        }

        $tabOrderKey = "user_pref_" . $user->id . "_direktor_tabs";

        Setting::updateOrCreate(
            ['key' => $tabOrderKey],
            ['value' => json_encode($order)]
        );

        return response()->json(['success' => true]);
    }
}