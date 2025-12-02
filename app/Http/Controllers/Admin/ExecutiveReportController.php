<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExecutiveReportController extends Controller
{
    public function index()
    {
        // 1. SON ŞİKAYETLER (Akış İçin)
        $sonSikayetler = MusteriSikayeti::with(['sikayetKategori', 'cozumTakimi', 'dosyalar'])
            ->latest('musteri_sikayet_tarihi')
            ->take(10)
            ->get();

        // ======================================================
        // === 2. KPI SAYILARI (TÜM ZAMANLAR)
        // ======================================================
        $toplamSikayet = MusteriSikayeti::count();

        $acikSikayet = MusteriSikayeti::whereIn('musteri_durum', [
            'Yeni', 'İşlemde', 'Yeniden Açıldı', 'Revize Ediliyor'
        ])->count();

        $cozulenSikayet = MusteriSikayeti::whereIn('musteri_durum', [
            'Kapatıldı', 'Çözümlendi'
        ])->count();

        $gecikenSikayet = MusteriSikayeti::whereNotIn('musteri_durum', [
                'Kapatıldı', 'Çözümlendi'
            ])
            ->where('musteri_cozum_son_tarihi', '<', now())
            ->count();

        // Ortalama Süre (Tüm Zamanlar)
        $bitenSikayetler = MusteriSikayeti::where('musteri_durum', 'Kapatıldı')
            ->whereNotNull('kurul_onay_tarihi')
            ->get();

        $genelToplamGun = 0;
        foreach ($bitenSikayetler as $item) {
            $s = Carbon::parse($item->musteri_sikayet_tarihi);
            $e = Carbon::parse($item->kurul_onay_tarihi);
            $genelToplamGun += $s->diffInDays($e);
        }

        $genelOrtalamaSure = $bitenSikayetler->count() > 0
            ? round($genelToplamGun / $bitenSikayetler->count(), 1)
            : 0;

        // ======================================================
        // === 2.1 KPI SAYILARI — AYLIK (Bu Ay)
        // ======================================================
        $kpiMonthly = [
            'toplam' => MusteriSikayeti::whereYear('musteri_sikayet_tarihi', now()->year)
                ->whereMonth('musteri_sikayet_tarihi', now()->month)
                ->count(),

            'acik' => MusteriSikayeti::whereYear('musteri_sikayet_tarihi', now()->year)
                ->whereMonth('musteri_sikayet_tarihi', now()->month)
                ->whereIn('musteri_durum', [
                    'Yeni','İşlemde','Revize Ediliyor','Yeniden Açıldı'
                ])
                ->count(),

            'cozulen' => MusteriSikayeti::whereYear('kurul_onay_tarihi', now()->year)
                ->whereMonth('kurul_onay_tarihi', now()->month)
                ->count(),

            'geciken' => MusteriSikayeti::whereYear('musteri_cozum_son_tarihi', now()->year)
                ->whereMonth('musteri_cozum_son_tarihi', now()->month)
                ->where('musteri_cozum_son_tarihi', '<', now())
                ->count(),

            'ortalama_sure' => $this->aylikOrtSure()
        ];

        // ======================================================
// === 3. BÖLÜM PERFORMANS KARNESİ (ÇOK YILLIK DAĞILIM)
// ======================================================
$kategoriler = SikayetKategori::all();
$bolumPerformansi = [];

$yillar = range(now()->year, now()->year - 4); // Örn: 2025 - 2024 - 2023 - 2022 - 2021

foreach ($kategoriler as $kat) {

    // Tüm şikayetler (kategori bazlı)
    $katSikayetler = MusteriSikayeti::where('sikayet_kategorisi_id', $kat->id)->get();
    $katToplam = $katSikayetler->count();

    if ($katToplam > 0) {

        // GENEL VERİLER
        $katCozulen = $katSikayetler->where('musteri_durum', 'Kapatıldı')->count();
        $katAcik = $katSikayetler->whereIn('musteri_durum', ['Yeni','İşlemde'])->count();

        // ORTALAMA SÜRE
        $biten = $katSikayetler
            ->where('musteri_durum', 'Kapatıldı')
            ->whereNotNull('kurul_onay_tarihi');

        $gunToplam = 0;
        foreach ($biten as $b) {
            $gunToplam += Carbon::parse($b->musteri_sikayet_tarihi)
                ->diffInDays(Carbon::parse($b->kurul_onay_tarihi));
        }

        $katOrtSure = $biten->count() > 0
            ? round($gunToplam / $biten->count(), 1)
            : 0;

        // ✔ YILLIK AYRI AYRI DAĞILIM
        $yillikDetay = [];

        foreach ($yillar as $y) {
            $yearItems = $katSikayetler->filter(function($x) use ($y) {
                return Carbon::parse($x->musteri_sikayet_tarihi)->year == $y;
            });

            $yillikDetay[$y] = [
                'toplam' => $yearItems->count(),
                'cozulen' => $yearItems->where('musteri_durum', 'Kapatıldı')->count(),
                'acik' => $yearItems->whereIn('musteri_durum', ['Yeni','İşlemde'])->count(),
                'geciken' => $yearItems->filter(function($a){
                    return ($a->musteri_durum !== 'Kapatıldı') &&
                           Carbon::parse($a->musteri_cozum_son_tarihi) < now();
                })->count(),
            ];
        }

        // GENEL DÖNÜŞ
        $bolumPerformansi[] = [
            'ad' => $kat->ad,
            'toplam' => $katToplam,
            'cozulen' => $katCozulen,
            'acik' => $katAcik,
            'basari_orani' => round(($katCozulen / $katToplam) * 100),
            'ort_sure' => $katOrtSure,
            'yillik_detay' => $yillikDetay // ← 🔥 asıl yeni eklenen
        ];
    }
}

$bolumPerformansi = collect($bolumPerformansi)
    ->sortByDesc('toplam')
    ->values();


        // ======================================================
        // === 4. GRAFİK VERİLERİ — SON 12 AY
        // ======================================================
        $labels = [];
        $trendData = [];
        $speedData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->locale('tr')->isoFormat('MMMM');

            // A) Aylık Şikayet Sayısı
            $trendData[] = MusteriSikayeti::whereYear('musteri_sikayet_tarihi', $date->year)
                ->whereMonth('musteri_sikayet_tarihi', $date->month)
                ->count();

            // B) Ortalama hız
            $avgSpeed = MusteriSikayeti::whereYear('kurul_onay_tarihi', $date->year)
                ->whereMonth('kurul_onay_tarihi', $date->month)
                ->where('musteri_durum', 'Kapatıldı')
                ->selectRaw('AVG(DATEDIFF(kurul_onay_tarihi, musteri_sikayet_tarihi)) as avg_days')
                ->value('avg_days');

            $speedData[] = $avgSpeed ? round($avgSpeed, 1) : 0;
        }

        // ======================================================
        // === 5. PASTA GRAFİKLERİ
        // ======================================================
        $catLabels = $kategoriler->pluck('ad');
        $catData = [];

        foreach ($kategoriler as $k) {
            $catData[] = MusteriSikayeti::where('sikayet_kategorisi_id', $k->id)->count();
        }

        // ======================================================
        // === 6. DURUM GRAFİĞİ
        // ======================================================
        $statusData = [$acikSikayet, $cozulenSikayet, $gecikenSikayet];

        // ======================================================
        // === VIEW'E GÖNDERİLEN VERİLER
        // ======================================================
        return view('admin.raporlar.executive', [
            'sonSikayetler' => $sonSikayetler,
            'bolumPerformansi' => $bolumPerformansi,

            'kpi' => [
                'toplam' => $toplamSikayet,
                'acik' => $acikSikayet,
                'cozulen' => $cozulenSikayet,
                'geciken' => $gecikenSikayet,
                'ortalama_sure' => $genelOrtalamaSure
            ],

            'kpiMonthly' => $kpiMonthly,

            'charts' => [
                'labels' => $labels,
                'trend_data' => $trendData,
                'speed_data' => $speedData,
                'cat_labels' => $catLabels,
                'cat_data' => $catData,
                'status_data' => $statusData
            ]
        ]);
    }

    // ======================================================
    // === Aylık Ortalama Süre HESABI
    // ======================================================
    private function aylikOrtSure()
    {
        $biten = MusteriSikayeti::where('musteri_durum', 'Kapatıldı')
            ->whereYear('kurul_onay_tarihi', now()->year)
            ->whereMonth('kurul_onay_tarihi', now()->month)
            ->get();

        if ($biten->count() === 0) {
            return 0;
        }

        $gun = 0;
        foreach ($biten as $item) {
            $s = Carbon::parse($item->musteri_sikayet_tarihi);
            $e = Carbon::parse($item->kurul_onay_tarihi);
            $gun += $s->diffInDays($e);
        }

        return round($gun / $biten->count(), 1);
    }
}
