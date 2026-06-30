<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use App\Models\Takim;
use App\Models\Bolum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExecutiveReport extends Component
{
    // Filtreler
    public $startDate;
    public $endDate;
    public $selectedBolumId;

    // URL Parametreleri
    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'selectedBolumId' => ['except' => '']
    ];

    public function mount()
    {
        // Varsayılan değerler boş
    }

    public function updated($propertyName)
    {
        // Filtre güncellendiğinde otomatik render çalışır
    }

    public function clearFilters()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->selectedBolumId = null;
    }

    // Ortak Filtre Uygulayıcı
    private function applyFilters($query, $dateField = 'musteri_sikayet_tarihi')
    {
        return $query->when($this->startDate, fn($q) => $q->whereDate($dateField, '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate($dateField, '<=', $this->endDate))
            ->when($this->selectedBolumId, function ($q) {
                // Şikayetin Çözüm Takımı -> Bölüm veya Kategori -> Bölüm
                // Öncelik: Kategori -> Bölüm
                $q->whereHas('sikayetKategori.bolum', function ($sq) {
                    $sq->where('id', $this->selectedBolumId);
                });
            });
    }

    public function render()
    {
        // 1. SON ŞİKAYETLER (MARQUEE İÇİN - Limitli)
        $sonSikayetler = MusteriSikayeti::with(['sikayetKategori.bolum', 'sikayetAltKategori', 'cozumTakimi', 'dosyalar', 'olusturanKurulUyesi'])
            ->tap(fn($q) => $this->applyFilters($q))
            ->latest('musteri_sikayet_tarihi')
            ->take(20)
            ->get();

        // 2. LISTELER İÇİN TAM VERİ (Filtreli)
        // KPI tabloları için limit olmadan filtreye uyan tüm kayıtları alıyoruz.
        // Performans için sadece gerekli alanları select edebiliriz ama şimdilik with ile alalım.
        $listSikayetler = MusteriSikayeti::with(['cozumTakimi'])
            ->tap(fn($q) => $this->applyFilters($q))
            ->latest('musteri_sikayet_tarihi')
            ->get();

        // 2. KPI - GENEL (Filtreye Duyarlı)
        $baseQuery = MusteriSikayeti::query();
        $this->applyFilters($baseQuery);

        // Hız için count querylerini clone ile alıyoruz
        $toplamSikayet = (clone $baseQuery)->count();

        $acikSikayet = (clone $baseQuery)->whereIn('musteri_durum', [
            'Yeni',
            'İşlemde',
            'Yeniden Açıldı',
            'Revize Ediliyor'
        ])->count();

        $cozulenSikayet = (clone $baseQuery)->whereIn('musteri_durum', [
            'Kapatıldı',
            'Çözümlendi'
        ])->count();

        $gecikenSikayet = (clone $baseQuery)->whereNotIn('musteri_durum', [
            'Kapatıldı',
            'Çözümlendi'
        ])
            ->where('musteri_cozum_son_tarihi', '<', now())
            ->count();

        // Ortalama Süre (Filtreli)
        $bitenSikayetler = (clone $baseQuery)->where('musteri_durum', 'Kapatıldı')
            ->whereNotNull('kurul_onay_tarihi')
            ->get();

        $genelToplamGun = 0;
        foreach ($bitenSikayetler as $item) {
            $s = Carbon::parse($item->musteri_sikayet_tarihi);
            $e = Carbon::parse($item->kurul_onay_tarihi);
            $genelToplamGun += $s->diffInDays($e);
        }
        $genelOrtalamaSure = $bitenSikayetler->count() > 0 ? round($genelToplamGun / $bitenSikayetler->count(), 1) : 0;

        // KPI MONTHLY (Bu Ay) - Filtreden BAĞIMSIZ (Tarih açısından) ama Bölüm filtresine duyarlı
        $filteredByMonth = MusteriSikayeti::whereYear('musteri_sikayet_tarihi', now()->year)
            ->whereMonth('musteri_sikayet_tarihi', now()->month)
            ->when($this->selectedBolumId, function ($q) {
                $q->whereHas('sikayetKategori.bolum', function ($sq) {
                    $sq->where('id', $this->selectedBolumId);
                });
            });

        $kpiMonthly = [
            'toplam' => (clone $filteredByMonth)->count(),
            'acik' => (clone $filteredByMonth)->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'Revize Ediliyor', 'Yeniden Açıldı'])->count(),
            'cozulen' => (clone $filteredByMonth)->whereIn('musteri_durum', ['Kapatıldı', 'Çözümlendi'])->count(), // Şikayet tarihine göre "bu ay açılıp çözülenler" bakılıyor genelde ama dashboard mantığında "Bu ay ÇÖZÜLENLER" (kurul onay tarihi bu ay olanlar) farklıdır.
            // Orijinal Controller mantığına bakalım:
            // 'cozulen' => whereYear('kurul_onay_tarihi', now()) ... idi.
            // O zaman filteredByMonth sadece şikayet tarihi filtreli. Onu kullanmayalım her biri için ayrı query atalım daha doğru olur.
        ];

        // GÜNCEL AYIN VERİLERİ (Bölüm Filtresine Duyarlı, Tarih Filtresi Ignore)
        $monthQuery = function () {
            return MusteriSikayeti::query()->when($this->selectedBolumId, function ($q) {
                $q->whereHas('sikayetKategori.bolum', function ($sq) {
                    $sq->where('id', $this->selectedBolumId);
                });
            });
        };

        $kpiMonthly = [
            'toplam' => $monthQuery()->whereYear('musteri_sikayet_tarihi', now()->year)->whereMonth('musteri_sikayet_tarihi', now()->month)->count(),

            'acik' => $monthQuery()->whereYear('musteri_sikayet_tarihi', now()->year)->whereMonth('musteri_sikayet_tarihi', now()->month)
                ->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'Revize Ediliyor', 'Yeniden Açıldı'])->count(),

            'cozulen' => $monthQuery()->whereYear('kurul_onay_tarihi', now()->year)->whereMonth('kurul_onay_tarihi', now()->month)
                ->count(),

            'geciken' => $monthQuery()->whereYear('musteri_cozum_son_tarihi', now()->year)->whereMonth('musteri_cozum_son_tarihi', now()->month)
                ->where('musteri_cozum_son_tarihi', '<', now())->count(),

            'ortalama_sure' => $this->aylikOrtSure($this->selectedBolumId) // Metodu güncellemek gerekecek
        ];

        // 3. MEMNUNİYET
        $feedbackCounts = (clone $baseQuery)->whereNotNull('musteri_feedback')
            ->selectRaw('musteri_feedback, count(*) as total')
            ->groupBy('musteri_feedback')
            ->pluck('total', 'musteri_feedback');

        // 4. BÖLÜM MEMNUNİYETİ
        $bolumMemnuniyeti = (clone $baseQuery)
            ->whereNotNull('musteri_feedback')
            ->leftJoin('takimlar', 'musteri_sikayetleri.atanan_cozum_takimi_id', '=', 'takimlar.id')
            ->leftJoin('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
            ->selectRaw('
                COALESCE(takimlar.ad, sikayet_kategorileri.ad, "Genel / Atanmamış") as bolum_adi,
                SUM(CASE WHEN musteri_feedback LIKE "%Onay%" THEN 1 ELSE 0 END) as onay_sayisi,
                SUM(CASE WHEN musteri_feedback LIKE "%Red%" THEN 1 ELSE 0 END) as red_sayisi,
                SUM(CASE WHEN musteri_feedback LIKE "%Revizyon%" THEN 1 ELSE 0 END) as revizyon_sayisi
            ')
            ->groupBy('bolum_adi')
            ->get();

        // 5. BÖLÜM PERFORMANS KARNESİ
        $kategoriler = SikayetKategori::with('bolum')->get();
        // Eğer Bölüm seçiliyse sadece o bölümün kategorilerini getir
        if ($this->selectedBolumId) {
            $kategoriler = $kategoriler->where('bolum_id', $this->selectedBolumId);
        }

        $bolumPerformansi = [];
        // Yıl bazlı detay yerine Seçilen Tarih Aralığı (veya son 12 ay) gösterimi daha mantıklı grafikte.
        // Tabloda ise "Yıllık Detay"ı kaldırıp genel özet gösterebiliriz filtre varsa.
        // Ancak kodun karmaşasını önlemek için mevcut yapıyı filtreli hale getirelim.

        foreach ($kategoriler as $kat) {
            $katSikayetQuery = MusteriSikayeti::where('sikayet_kategorisi_id', $kat->id);
            $this->applyFilters($katSikayetQuery); // Ana filtreleri uygula
            $katSikayetler = $katSikayetQuery->get();

            $katToplam = $katSikayetler->count();

            if ($katToplam > 0) {
                $katCozulen = $katSikayetler->whereIn('musteri_durum', ['Kapatıldı', 'Çözümlendi'])->count();
                $katAcik = $katSikayetler->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'Revize Ediliyor', 'Yeniden Açıldı'])->count();

                // Ort Süre
                $biten = $katSikayetler->where('musteri_durum', 'Kapatıldı')->whereNotNull('kurul_onay_tarihi');
                $gunToplam = 0;
                foreach ($biten as $b) {
                    $gunToplam += Carbon::parse($b->musteri_sikayet_tarihi)->diffInDays(Carbon::parse($b->kurul_onay_tarihi));
                }
                $katOrtSure = $biten->count() > 0 ? round($gunToplam / $biten->count(), 1) : 0;

                // Yıllık detay yerine "Seçilen Dönem" tek satır basabiliriz ya da boş geçebiliriz.
                // View tarafında bu array kullanıldığı için boş array bırakıyorum, view'da düzenleyeceğim.
                $yillikDetay = [];

                $bolumPerformansi[] = [
                    'ad' => $kat->ad,
                    'toplam' => $katToplam,
                    'cozulen' => $katCozulen,
                    'acik' => $katAcik,
                    'basari_orani' => round(($katCozulen / $katToplam) * 100),
                    'ort_sure' => $katOrtSure,
                    'yillik_detay' => $yillikDetay
                ];
            }
        }
        $bolumPerformansi = collect($bolumPerformansi)->sortByDesc('toplam')->values();

        // 6. GRAFİK VERİLERİ (Trend - Son 12 Ay veya Filtre Aralığı)
        // Eğer tarih filtresi varsa aralığı aylara böl, yoksa son 12 ay.
        $labels = [];
        $trendData = [];
        $speedData = [];

        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            // Aylık döngü
            // Eğer aralık 1 aydan kısaysa gün bazlı, değilse ay bazlı -> Basitlik için Ay bazlı devam edelim
            // ya da direkt DB:raw query ile gruplayalım.
            // Loop ile yapalım (daha kontrollü)
            $period = \Carbon\CarbonPeriod::create($start, '1 month', $end->copy()->addMonth());

            foreach ($period as $dt) {
                if ($dt > $end)
                    break;
                $labels[] = $dt->locale('tr')->isoFormat('MMM YYYY');

                // O ayki veriler (Filtreler zaten tarih aralığını kısıtlıyor ama burada spesifik aya bakıyoruz)
                // Bölüm filtresi hala geçerli olmalı
                $trendData[] = MusteriSikayeti::whereYear('musteri_sikayet_tarihi', $dt->year)
                    ->whereMonth('musteri_sikayet_tarihi', $dt->month)
                    ->when($this->selectedBolumId, function ($q) {
                        $q->whereHas('sikayetKategori.bolum', function ($sq) {
                            $sq->where('id', $this->selectedBolumId);
                        });
                    })
                    ->count();

                $avgSpeed = MusteriSikayeti::whereYear('kurul_onay_tarihi', $dt->year)
                    ->whereMonth('kurul_onay_tarihi', $dt->month)
                    ->where('musteri_durum', 'Kapatıldı')
                    ->when($this->selectedBolumId, function ($q) { // Hız hesabında da bölüm filtresi
                        $q->whereHas('sikayetKategori.bolum', function ($sq) {
                            $sq->where('id', $this->selectedBolumId);
                        });
                    })
                    ->selectRaw('AVG(DATEDIFF(kurul_onay_tarihi, musteri_sikayet_tarihi)) as avg_days')
                    ->value('avg_days');
                $speedData[] = $avgSpeed ? round($avgSpeed, 1) : 0;
            }
        } else {
            // Varsayılan: Son 12 Ay
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->locale('tr')->isoFormat('MMMM');

                $trendData[] = MusteriSikayeti::whereYear('musteri_sikayet_tarihi', $date->year)
                    ->whereMonth('musteri_sikayet_tarihi', $date->month)
                    ->count();

                $avgSpeed = MusteriSikayeti::whereYear('kurul_onay_tarihi', $date->year)
                    ->whereMonth('kurul_onay_tarihi', $date->month)
                    ->where('musteri_durum', 'Kapatıldı')
                    ->selectRaw('AVG(DATEDIFF(kurul_onay_tarihi, musteri_sikayet_tarihi)) as avg_days')
                    ->value('avg_days');

                $speedData[] = $avgSpeed ? round($avgSpeed, 1) : 0;
            }
        }

        // 7. KATEGORİ PASTA
        $catLabels = $kategoriler->pluck('ad');
        $catData = [];
        foreach ($kategoriler as $k) {
            // Kategori sayısını da tarih filtresine göre (Eğer varsa)
            $cQuery = MusteriSikayeti::where('sikayet_kategorisi_id', $k->id);
            $this->applyFilters($cQuery);
            $catData[] = $cQuery->count();
        }

        // 8. DURUM GRAFİĞİ
        $statusData = [$acikSikayet, $cozulenSikayet, $gecikenSikayet];

        // 9. MÜŞTERİ KARAR İSTATİSTİKLERİ
        $musteriKararIstatistikleri = [
            'onay_orani' => (clone $baseQuery)->where('musteri_feedback', 'Onaylandı')->count(),
            'red_orani' => (clone $baseQuery)->where('musteri_feedback', 'Reddedildi')->count(),
            'revizyon' => (clone $baseQuery)->where('musteri_feedback', 'Revizyon İstendi')->count(),
        ];

        // View Parametreleri
        return view('livewire.executive-report', [
            'sonSikayetler' => $sonSikayetler,
            'listSikayetler' => $listSikayetler,
            'bolumPerformansi' => $bolumPerformansi,
            'feedbackCounts' => $feedbackCounts,
            'bolumMemnuniyeti' => $bolumMemnuniyeti,
            'musteriKararIstatistikleri' => $musteriKararIstatistikleri,
            'kpi' => [
                'toplam' => $toplamSikayet,
                'acik' => $acikSikayet,
                'cozulen' => $cozulenSikayet,
                'geciken' => $gecikenSikayet,
                'ortalama_sure' => $genelOrtalamaSure
            ],
            'kpiMonthly' => $kpiMonthly, // Eklendi
            'charts' => [
                'labels' => $labels,
                'trend_data' => $trendData,
                'speed_data' => $speedData,
                'cat_labels' => $catLabels,
                'cat_data' => $catData,
                'status_data' => $statusData
            ],
            // Sadece Şikayet Kategorisi Tanımlanmış (Yani şikayet süreci işletilen) Bölümleri getir
            'bolumler' => Bolum::whereHas('sikayetKategorileri')->orderBy('ad')->get(),
        ])->layout('layouts.app');
    }

    private function aylikOrtSure($bolumId = null)
    {
        $biten = MusteriSikayeti::where('musteri_durum', 'Kapatıldı')
            ->whereYear('kurul_onay_tarihi', now()->year)
            ->whereMonth('kurul_onay_tarihi', now()->month)
            ->when($bolumId, function ($q) use ($bolumId) {
                $q->whereHas('sikayetKategori.bolum', function ($sq) use ($bolumId) {
                    $sq->where('id', $bolumId);
                });
            })
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
