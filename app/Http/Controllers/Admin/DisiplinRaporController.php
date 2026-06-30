<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryPenaltyScale;
use App\Models\DisciplinaryBehavior;
use App\Models\DisciplinaryImpact;
use App\Models\DisciplinaryScope;
use App\Models\DisiplinKuruluToplanti;
use App\Models\Bolum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DisiplinRaporController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // =============================================
        // YETKİ KONTROLÜ VE KAPSAM BELİRLEME
        // =============================================
        $fullAccessRoles = ['Superadmin', 'Yonetim', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'];
        $hasFullAccess = $user->hasAnyRole($fullAccessRoles);
        
        // Hukuk Yöneticisi - sadece yetki verilmişse tam erişim
        if (!$hasFullAccess && $user->hasRole('Hukuk Yöneticisi')) {
            if ($user->can('disiplin.rapor.gor') || $user->can('disiplin.kurul.portal.gor')) {
                $hasFullAccess = true;
            }
        }
        
        // Direktör - sadece kendi bölümleri
        $isDirector = $user->hasRole('Direktör') && !$hasFullAccess;
        $directorBolumIds = [];
        if ($isDirector) {
            $directorBolumIds = Bolum::where('director_id', $user->id)->pluck('id')->toArray();
        }
        
        // Bölüm Lideri - sadece kendi bölümü
        $isBolumLideri = ($user->hasRole('Bölüm Lideri') || $user->hasRole('Bölüm Lider Yardımcısı')) && !$hasFullAccess && !$isDirector;
        
        // Erişim yoksa 403
        if (!$hasFullAccess && !$isDirector && !$isBolumLideri) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        // =============================================
        // BASE QUERY
        // =============================================
        $query = DisciplinaryCase::with(['user.bolum', 'behavior.category', 'impact', 'scope', 'reporter']);

        // Kapsam kısıtlaması
        if ($isBolumLideri) {
            $query->whereHas('user', fn($q) => $q->where('bolum_id', $user->bolum_id));
        } elseif ($isDirector) {
            $query->whereHas('user', fn($q) => $q->whereIn('bolum_id', $directorBolumIds));
        }

        // Tarih Filtresi
        if ($request->filled('start_date')) {
            $query->whereDate('disciplinary_cases.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('disciplinary_cases.created_at', '<=', $request->end_date);
        }
        // Bölüm Filtresi
        if ($request->filled('bolum_id')) {
            $query->whereHas('user', fn($q) => $q->where('bolum_id', $request->bolum_id));
        }
        // Personel Filtresi
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // =============================================
        // İSTATİSTİKLER
        // =============================================
        $allCases = (clone $query)->get();
        $totalCases = $allCases->count();

        // 1. DURUM BAZLI
        $kararVerilenler = $allCases->where('durum', 'Karar Verildi');
        $cezaVerilenler = $kararVerilenler->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)')->count();
        $cezaVerilmeyenler = $kararVerilenler->where('final_karar', 'Savunma Kabul Edildi (Ceza Yok)')->count();
        $kuruldakiler = $allCases->where('durum', 'Kurulda')->count();
        $savunmaBekleyenler = $allCases->where('durum', 'Savunma Bekleniyor')->count();
        $yoneticiDegerlendirmesinde = $allCases->whereIn('durum', ['Yönetici Değerlendirmesi', 'Yöneticide'])->count();

        // 2. ERTELENEN DOSYALAR
        $ertelenenDosyalar = $allCases->where('rediscussion_count', '>', 0)->count();

        // 3. TOPLANTI SAYISI
        $toplantiQuery = DisiplinKuruluToplanti::query();
        if ($request->filled('start_date')) {
            $toplantiQuery->whereDate('baslangic_tarihi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $toplantiQuery->whereDate('baslangic_tarihi', '<=', $request->end_date);
        }
        
        // Kapsam kısıtlaması toplantı sayısına da uygulanır
        if (!$hasFullAccess) {
            $toplantiQuery->whereHas('disiplinDosyalari', function($q) use ($isBolumLideri, $isDirector, $user, $directorBolumIds) {
                if ($isBolumLideri) {
                    $q->whereHas('user', fn($sq) => $sq->where('bolum_id', $user->bolum_id));
                } elseif ($isDirector) {
                    $q->whereHas('user', fn($sq) => $sq->whereIn('bolum_id', $directorBolumIds));
                }
            });
        }
        
        $toplamToplanti = $toplantiQuery->count();

        // 4. BÖLÜM BAZLI DOSYA SAYISI
        $bolumBazli = (clone $query)
            ->join('users', 'disciplinary_cases.user_id', '=', 'users.id')
            ->join('bolumler', 'users.bolum_id', '=', 'bolumler.id')
            ->select('bolumler.ad as label', DB::raw('count(*) as count'))
            ->groupBy('bolumler.id', 'bolumler.ad')
            ->orderBy('count', 'desc')
            ->get();

        // 5. CEZA PUAN SKALASI BAZLI
        $penaltyScales = DisciplinaryPenaltyScale::orderBy('min_puan')->get();
        $skalaBazli = [];
        foreach ($penaltyScales as $scale) {
            $count = $kararVerilenler->filter(function($case) use ($scale) {
                $finalPenalty = $case->manual_penalty_name ?? $case->sistem_oneri_ceza;
                return $finalPenalty === $scale->ceza_adi;
            })->count();
            $skalaBazli[] = [
                'ceza_adi' => $scale->ceza_adi,
                'count' => $count,
            ];
        }

        // 6. EN ÇOK DİSİPLİN DOSYASI OLAN PERSONELLER (Top 10)
        $topPersoneller = $allCases->groupBy('user_id')
            ->map(function ($cases) {
                $user = $cases->first()->user;
                return ['user' => $user, 'count' => $cases->count(), 'bolum' => $user->bolum->ad ?? '-'];
            })->sortByDesc('count')->take(10)->values();

        // 7. AYLIK TREND
        $trendData = (clone $query)
            ->select(DB::raw("DATE_FORMAT(disciplinary_cases.created_at, '%Y-%m') as ay"), DB::raw('count(*) as count'))
            ->groupBy('ay')->orderBy('ay')->get();

        // 8. EN ÇOK SEÇİLEN İHLAL MADDELERİ
        $behaviorStats = $allCases->groupBy('behavior_id')
            ->map(function ($cases) {
                $behavior = $cases->first()->behavior;
                return ['tanim' => $behavior ? $behavior->tanim : 'Belirtilmemiş', 'count' => $cases->count()];
            })->filter(fn($item) => $item['count'] > 0)->sortByDesc('count')->values();

        // 9. DURUM DAĞILIMI (Pie)
        $durumDagilimi = $allCases->groupBy('durum')
            ->map(fn($cases, $durum) => ['durum' => $durum, 'count' => $cases->count()])->values();

        // 10. CEZA VERİLEN / VERİLMEYEN / BEKLEYEN KARŞILAŞTIRMA
        $bekleyenDosyalar = $allCases->whereIn('durum', ['Kurulda', 'Savunma Bekleniyor', 'Yönetici Değerlendirmesi', 'Yöneticide'])->count();
        $kararKarsilastirma = [
            ['label' => 'Ceza Verilen', 'count' => $cezaVerilenler],
            ['label' => 'Ceza Verilmeyen', 'count' => $cezaVerilmeyenler],
            ['label' => 'Görüşülmeyi Bekleyen', 'count' => $bekleyenDosyalar],
        ];

        // 11. CİDDİYET DEĞERLENDİRMESİ - SUÇUN ŞİDDETİ (Impact)
        $impactStats = $allCases->groupBy('impact_id')
            ->map(function ($cases) {
                $impact = $cases->first()->impact;
                return ['tanim' => $impact ? $impact->tanim : 'Belirtilmemiş', 'count' => $cases->count()];
            })->filter(fn($i) => $i['count'] > 0)->sortByDesc('count')->values();

        // 12. CİDDİYET DEĞERLENDİRMESİ - ETKİ KAPSAMI (Scope)
        $scopeStats = $allCases->groupBy('scope_id')
            ->map(function ($cases) {
                $scope = $cases->first()->scope;
                return ['tanim' => $scope ? $scope->tanim : 'Belirtilmemiş', 'count' => $cases->count()];
            })->filter(fn($i) => $i['count'] > 0)->sortByDesc('count')->values();

        // 13. TEKRARLI DOSYALAR (Aynı kullanıcı + aynı behavior_id)
        $tekrarliDosyalar = $allCases->groupBy(function ($case) {
            return $case->user_id . '_' . $case->behavior_id;
        })->filter(fn($group) => $group->count() > 1)
        ->map(function ($cases) {
            $first = $cases->first();
            return [
                'user' => $first->user,
                'behavior' => $first->behavior ? $first->behavior->tanim : '-',
                'count' => $cases->count(),
                'bolum' => $first->user->bolum->ad ?? '-',
            ];
        })->sortByDesc('count')->values();

        // 14. EN ÇOK TUTANAK TUTAN KULLANICILAR (reporter_id bazlı)
        $topReporters = $allCases->whereNotNull('reporter_id')->groupBy('reporter_id')
            ->map(function ($cases) {
                $reporter = $cases->first()->reporter;
                return ['user' => $reporter, 'count' => $cases->count(), 'bolum' => $reporter->bolum->ad ?? '-'];
            })->sortByDesc('count')->take(10)->values();

        // Filtre Seçenekleri
        if ($isBolumLideri) {
            $bolumler = Bolum::where('id', $user->bolum_id)->orderBy('ad')->get();
        } elseif ($isDirector) {
            $bolumler = Bolum::whereIn('id', $directorBolumIds)->orderBy('ad')->get();
        } else {
            $bolumler = Bolum::orderBy('ad')->get();
        }
        if ($isBolumLideri) {
            $users = User::personel()->where('bolum_id', $user->bolum_id)->select('id', 'name')->orderBy('name')->get();
        } elseif ($isDirector) {
            $users = User::personel()->whereIn('bolum_id', $directorBolumIds)->select('id', 'name')->orderBy('name')->get();
        } else {
            $users = User::personel()->select('id', 'name')->orderBy('name')->get();
        }
        // Aktif Filtre Bilgileri
        $activeFilters = [];
        if ($request->filled('start_date')) {
            $activeFilters[] = ['type' => 'Başlangıç', 'value' => Carbon::parse($request->start_date)->format('d.m.Y')];
        }
        if ($request->filled('end_date')) {
            $activeFilters[] = ['type' => 'Bitiş', 'value' => Carbon::parse($request->end_date)->format('d.m.Y')];
        }
        if ($request->filled('bolum_id')) {
            $filtreliBolum = $bolumler->firstWhere('id', $request->bolum_id);
            $activeFilters[] = ['type' => 'Bölüm', 'value' => $filtreliBolum ? $filtreliBolum->ad : '-'];
        }
        if ($request->filled('user_id')) {
            $filtreliUser = $users->firstWhere('id', $request->user_id);
            $activeFilters[] = ['type' => 'Personel', 'value' => $filtreliUser ? $filtreliUser->name : '-'];
        }

        // Rapor meta (export için)
        $reportMeta = [
            'generated_by' => $user->name,
            'generated_at' => now()->format('d.m.Y H:i'),
            'date_range' => ($request->filled('start_date') && $request->filled('end_date'))
                ? Carbon::parse($request->start_date)->format('d.m.Y') . ' - ' . Carbon::parse($request->end_date)->format('d.m.Y') . ' arası rapor'
                : (($request->filled('start_date'))
                    ? Carbon::parse($request->start_date)->format('d.m.Y') . ' tarihinden itibaren'
                    : (($request->filled('end_date'))
                        ? Carbon::parse($request->end_date)->format('d.m.Y') . ' tarihine kadar'
                        : 'Tüm zamanlar')),
        ];

        return view('admin.disiplin.report', compact(
            'totalCases', 'cezaVerilenler', 'cezaVerilmeyenler', 'kuruldakiler',
            'savunmaBekleyenler', 'yoneticiDegerlendirmesinde', 'ertelenenDosyalar',
            'toplamToplanti', 'bolumBazli', 'skalaBazli', 'topPersoneller',
            'trendData', 'behaviorStats', 'durumDagilimi', 'bolumler', 'users',
            'penaltyScales', 'kararKarsilastirma', 'impactStats', 'scopeStats',
            'tekrarliDosyalar', 'topReporters', 'bekleyenDosyalar',
            'activeFilters', 'reportMeta'
        ));
    }
}
