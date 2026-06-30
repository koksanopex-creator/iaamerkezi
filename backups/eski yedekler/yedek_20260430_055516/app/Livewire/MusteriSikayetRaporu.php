<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use App\Models\SikayetIadesi; // YENİ
use App\Models\Takim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Str sınıfını ekledik
use Carbon\Carbon;

class MusteriSikayetRaporu extends Component
{
    // Filtreleme Değişkenleri
    // Filtreleme Değişkenleri
    public $startDate;
    public $endDate;
    public $activeFilter = 'toplam'; // Varsayılan filtre
    public $tableOpen = false; // Tablo varsayılan olarak kapalı
    public $iadeLimit = 5; // İade tablosu gösterim limiti

    public function clearFilter()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->activeFilter = 'toplam';
    }

    public function setFilter($filter)
    {
        $this->activeFilter = $filter;
    }

    public function increaseIadeLimit()
    {
        $this->iadeLimit += 5;
    }

    public function decreaseIadeLimit()
    {
        if ($this->iadeLimit > 5) {
            $this->iadeLimit -= 5;
        }
    }

    public function resetIadeLimit()
    {
        $this->iadeLimit = 5;
    }

    // Canlı dinlenecek olaylar
    protected $listeners = [
        'echo:sikayet-raporlari,SikayetOlusturuldu' => 'handleSikayetOlusturuldu',
        'echo:sikayet-raporlari,SikayetDurumuDegisti' => 'handleSikayetDurumuDegisti',
    ];

    // Olay: Yeni bir şikayet geldi
    public function handleSikayetOlusturuldu($data)
    {
        session()->flash('yeniSikayet', 'Yeni bir şikayet kaydı geldi! (ID: #' . ($data['sikayet']['id'] ?? '') . ')');
    }

    // Olay: Bir şikayetin durumu değişti (çözüldü vb.)
    public function handleSikayetDurumuDegisti()
    {
        // Sadece render'ı tetikler
    }

    /**
     * Tüm istatistikleri, listeleri ve grafikleri hesapla
     */
    private function calculateStats()
    {
        // === 1. KPI KARTLARI (Mevcut) ===
        $kpi = [
            'toplam' => MusteriSikayeti::when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                ->count(),
            'yeni' => MusteriSikayeti::where('musteri_durum', 'Yeni')
                ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                ->count(),
            'islemde' => MusteriSikayeti::where('musteri_durum', 'İşlemde')
                ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                ->count(),
            'cozuldu' => MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                ->whereDoesntHave('iaaProjesi', function ($q) {
                    $q->whereIn('durum', [
                        'hatali_bildirim_olarak_kapatildi',
                        'talep_olarak_kapatildi',
                        'talep_onayi_bekliyor_kalite',
                        'talep_onayi_bekliyor_superadmin'
                    ]);
                })
                ->whereNotIn('musteri_durum', [
                    'Talep Olarak Kapatıldı',
                    'Hatalı Bildirim Olarak Kapatıldı',
                    'talep_olarak_kapatildi',
                    'hatali_bildirim_olarak_kapatildi'
                ])
                ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                ->count(),

            'hatali_bildirim' => MusteriSikayeti::where(function ($q) {
                $q->whereIn('musteri_durum', ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])
                    ->orWhereHas('iaaProjesi', function ($sq) {
                        $sq->whereIn('durum', ['hatali_bildirim_olarak_kapatildi', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor']);
                    });
            })
                ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                ->count(),

            'talep_kapatilan' => MusteriSikayeti::where(function ($q) {
                $q->whereIn('musteri_durum', ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])
                    ->orWhereHas('iaaProjesi', function ($sq) {
                        $sq->whereIn('durum', ['talep_olarak_kapatildi', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_superadmin']);
                    });
            })
                ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                ->count(),
            'gecikmis' => MusteriSikayeti::whereNotNull('musteri_cozum_son_tarihi')
                ->where('musteri_cozum_son_tarihi', '<', now())
                ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                ->count(),
        ];

        // === 2. ORİJİNAL 4 GRAFİK VERİSİ ===
        $durumData = MusteriSikayeti::select('musteri_durum', DB::raw('count(*) as total'))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->groupBy('musteri_durum')
            ->pluck('total', 'musteri_durum');

        $kategoriData = SikayetKategori::withCount([
            'sikayetler' => function ($q) {
                $q->when($this->startDate, fn($squery) => $squery->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($squery) => $squery->whereDate('created_at', '<=', $this->endDate));
            }
        ])
            ->orderBy('sikayetler_count', 'desc')
            ->take(5)
            ->pluck('sikayetler_count', 'ad');

        $takimData = Takim::where('tur', 'sikayet')
            ->has('atananSikayetler')
            ->withCount('atananSikayetler')
            ->orderBy('atanan_sikayetler_count', 'desc')
            ->take(5)
            ->pluck('atanan_sikayetler_count', 'ad');

        $aylikTrend = MusteriSikayeti::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ay"),
            DB::raw('count(*) as total')
        )
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->when(!$this->startDate && !$this->endDate, fn($q) => $q->where('created_at', '>=', now()->subMonths(12))) // Filtre yoksa son 12 ay
            ->groupBy('ay')
            ->orderBy('ay', 'asc')
            ->pluck('total', 'ay');

        // === 3. YENİ LİSTELER ===
        $cozulenListesi = MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->with('iaaProjesi')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest('updated_at')
            ->take(5)
            ->get();

        $islemdeListesi = MusteriSikayeti::where('musteri_durum', 'İşlemde')
            ->with('iaaProjesi')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest()
            ->take(5)
            ->get();

        $yeniListesi = MusteriSikayeti::where('musteri_durum', 'Yeni')
            ->with('iaaProjesi')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest()
            ->take(5)
            ->get();

        $projeyeDonusenListesi = MusteriSikayeti::whereNotNull('iaa_id')
            ->with('iaaProjesi')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest('updated_at')
            ->take(5)
            ->get();

        // === 4. YENİ 4 DONUT GRAFİK VERİSİ ===
        $cozulenChartData = MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->select('musteri_oncelik as etiket', DB::raw('count(*) as total'))
            ->groupBy('etiket')
            ->pluck('total', 'etiket');

        $islemdeChartData = MusteriSikayeti::where('musteri_durum', 'İşlemde')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->select('musteri_oncelik as etiket', DB::raw('count(*) as total'))
            ->groupBy('etiket')
            ->pluck('total', 'etiket');

        $yeniChartData = MusteriSikayeti::where('musteri_durum', 'Yeni')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->select('musteri_oncelik as etiket', DB::raw('count(*) as total'))
            ->groupBy('etiket')
            ->pluck('total', 'etiket');

        $projeyeDonusenChartData = MusteriSikayeti::whereNotNull('iaa_id')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->select('musteri_oncelik as etiket', DB::raw('count(*) as total'))
            ->groupBy('etiket')
            ->pluck('total', 'etiket');

        // === 5. YENİ AYLIK ÇÖZÜLEN TREND VERİSİ ===
        $aylikCozulenTrend = MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->select(
                DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as ay"),
                DB::raw('count(*) as total')
            )
            ->when($this->startDate, fn($q) => $q->whereDate('updated_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('updated_at', '<=', $this->endDate))
            ->when(!$this->startDate && !$this->endDate, fn($q) => $q->where('updated_at', '>=', now()->subMonths(12))) // Filtre yoksa son 12 ay
            ->groupBy('ay')
            ->orderBy('ay', 'asc')
            ->pluck('total', 'ay');

        // ==========================================================
        // === 6. MÜŞTERİ GERİ BİLDİRİM VE MEMNUNİYET ANALİZİ ===
        // ==========================================================

        // A) Genel Geri Bildirim Dağılımı (Pasta Grafik)
        $feedbackCounts = MusteriSikayeti::whereNotNull('musteri_feedback')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->selectRaw('musteri_feedback, count(*) as total')
            ->groupBy('musteri_feedback')
            ->pluck('total', 'musteri_feedback');

        // B) Bölüm Bazlı Memnuniyet (Sütun Grafik)
        // Şikayet -> Proje (IAA) -> Bölüm ilişkisi üzerinden
        $bolumMemnuniyeti = MusteriSikayeti::whereNotNull('musteri_feedback')
            ->join('iaas', 'musteri_sikayetleri.iaa_id', '=', 'iaas.id')
            ->join('bolumler', 'iaas.bolum_id', '=', 'bolumler.id')
            ->when($this->startDate, fn($q) => $q->whereDate('musteri_sikayetleri.created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('musteri_sikayetleri.created_at', '<=', $this->endDate))
            ->selectRaw('bolumler.ad as bolum_adi, 
                         SUM(CASE WHEN musteri_feedback = "Onaylandı" THEN 1 ELSE 0 END) as onay_sayisi,
                         SUM(CASE WHEN musteri_feedback = "Reddedildi" THEN 1 ELSE 0 END) as red_sayisi,
                         SUM(CASE WHEN musteri_feedback = "Revizyon İstendi" THEN 1 ELSE 0 END) as revizyon_sayisi')
            ->groupBy('bolumler.ad')
            ->get();
        // === SON 10 ŞİKAYET TABLOSU İÇİN ===
        // === SON 10 ŞİKAYET TABLOSU İÇİN ===
        $sonSikayetlerQuery = MusteriSikayeti::with('sikayetKategori', 'dosyalar', 'olusturanKurulUyesi', 'iadeler') // iadeler ve olusturanKurulUyesi eklendi
            ->withCount(['projeYorumlari', 'musteriProjeYorumlari', 'iadeler']) // iadeler_count eklendi
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));

        // KPI KARTLARINA GÖRE FİLTRELEME
        if ($this->activeFilter === 'yeni') {
            $sonSikayetlerQuery->where('musteri_durum', 'Yeni');
        } elseif ($this->activeFilter === 'islemde') {
            $sonSikayetlerQuery->where('musteri_durum', 'İşlemde');
        } elseif ($this->activeFilter === 'cozuldu') {
            $sonSikayetlerQuery->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                ->whereDoesntHave('iaaProjesi', function ($q) {
                    $q->whereIn('durum', [
                        'hatali_bildirim_olarak_kapatildi',
                        'talep_olarak_kapatildi',
                        'talep_onayi_bekliyor_kalite',
                        'talep_onayi_bekliyor_superadmin'
                    ]);
                })
                ->whereNotIn('musteri_durum', [
                    'Talep Olarak Kapatıldı',
                    'Hatalı Bildirim Olarak Kapatıldı',
                    'talep_olarak_kapatildi',
                    'hatali_bildirim_olarak_kapatildi'
                ]);
        } elseif ($this->activeFilter === 'talep_kapatilan') {
            $sonSikayetlerQuery->where(function ($q) {
                $q->whereIn('musteri_durum', ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])
                    ->orWhereHas('iaaProjesi', function ($sq) {
                        $sq->whereIn('durum', ['talep_olarak_kapatildi', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_superadmin']);
                    });
            });
        } elseif ($this->activeFilter === 'hatali_bildirim') {
            $sonSikayetlerQuery->where(function ($q) {
                $q->whereIn('musteri_durum', ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])
                    ->orWhereHas('iaaProjesi', function ($sq) {
                        $sq->whereIn('durum', ['hatali_bildirim_olarak_kapatildi', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor']);
                    });
            });
        } elseif ($this->activeFilter === 'gecikmis') {
            $sonSikayetlerQuery->whereNotNull('musteri_cozum_son_tarihi')
                ->where('musteri_cozum_son_tarihi', '<', now())
                ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı']);
        } elseif ($this->activeFilter === 'projeye_donusen') {
            $sonSikayetlerQuery->whereNotNull('iaa_id');
        }
        // 'toplam' ise ekstra filtre yok

        $sonSikayetler = $sonSikayetlerQuery->latest()
            ->take(10) // Müşteri isteği üzerine son 10 kayıt
            ->get();

        // === 7. İADE ANALİZLERİ (YENİ) ===

        // A) İade Verileri Tablosu
        $iadeQuery = SikayetIadesi::with(['musteriSikayeti.sikayetKategori.bolum', 'user'])
            ->when($this->startDate, fn($q) => $q->whereDate('iade_tarihi', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('iade_tarihi', '<=', $this->endDate));

        $iadeVerileriCount = $iadeQuery->count();
        
        $iadeVerileri = $iadeQuery->latest('iade_tarihi')
            ->take(50)
            ->get();

        // B) Toplam İade Miktarı (Birim Bazlı) 
        // [ {birim: 'KG', total: 500}, {birim: 'Adet', total: 20} ]
        $toplamIadeMiktarlari = SikayetIadesi::select('birim', DB::raw('SUM(miktar) as total'))
            ->when($this->startDate, fn($q) => $q->whereDate('iade_tarihi', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('iade_tarihi', '<=', $this->endDate))
            ->groupBy('birim')
            ->get();

        // C) İadeli vs İadesiz Oranı (Genel - range içinde oluşturulan şikayetler)
        $iadeliSikayetSayisi = MusteriSikayeti::has('iadeler')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->count();

        $iadesizSikayetSayisi = MusteriSikayeti::doesntHave('iadeler')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->count();

        // D) Bölümlere Göre İade Miktarları (Büyükten Küçüğe)
        // D) Bölümlere Göre İade Miktarları (Birim Bazlı - Çoklu Grafik)
        $rawDeptReturns = SikayetIadesi::join('musteri_sikayetleri', 'sikayet_iadeleri.musteri_sikayeti_id', '=', 'musteri_sikayetleri.id')
            ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id') // Kategori üzerinden
            ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id') // Bölüm üzerinden
            ->select('bolumler.ad as bolum_adi', 'sikayet_iadeleri.birim', DB::raw('SUM(sikayet_iadeleri.miktar) as toplam_miktar'))
            ->when($this->startDate, fn($q) => $q->whereDate('sikayet_iadeleri.iade_tarihi', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('sikayet_iadeleri.iade_tarihi', '<=', $this->endDate))
            ->groupBy('bolumler.ad', 'sikayet_iadeleri.birim')
            ->get();

        // Grafik verisini birime göre grupla { 'Ton': {labels:[], series:[]}, 'Adet': ... }
        $bolumIadeChartData = [];
        $units = $rawDeptReturns->pluck('birim')->unique();

        foreach ($units as $u) {
            $filtered = $rawDeptReturns->where('birim', $u)->sortByDesc('toplam_miktar');
            $bolumIadeChartData[$u] = [
                'labels' => $filtered->pluck('bolum_adi')->values()->all(),
                'series' => $filtered->pluck('toplam_miktar')->values()->all()
            ];
        }
        // E) Bölüm Bazlı İadeli/İadesiz Şikayet Sayıları (Stacked Bar)
        $bolumIadeSayilari = MusteriSikayeti::join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
            ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
            ->when($this->startDate, fn($q) => $q->whereDate('musteri_sikayetleri.created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('musteri_sikayetleri.created_at', '<=', $this->endDate))
            ->select(
                'bolumler.ad as bolum_adi',
                DB::raw("SUM(CASE WHEN EXISTS(SELECT 1 FROM sikayet_iadeleri WHERE musteri_sikayeti_id = musteri_sikayetleri.id) THEN 1 ELSE 0 END) as iadeli_count"),
                DB::raw("SUM(CASE WHEN NOT EXISTS(SELECT 1 FROM sikayet_iadeleri WHERE musteri_sikayeti_id = musteri_sikayetleri.id) THEN 1 ELSE 0 END) as iadesiz_count")
            )
            ->groupBy('bolumler.ad')
            ->orderBy('iadeli_count', 'desc') // İadeli sayısı çok olan bölüm üste
            ->get();

        $bolumIadeSayilariLabels = $bolumIadeSayilari->pluck('bolum_adi');
        $bolumIadeSayilariSeries = [
            ['name' => 'İadeli Şikayet', 'data' => $bolumIadeSayilari->pluck('iadeli_count')],
            ['name' => 'İadesiz Şikayet', 'data' => $bolumIadeSayilari->pluck('iadesiz_count')]
        ];

        // === 9. YENİ GRAFİKLER İÇİN VERİ HAZIRLIĞI (RESTORED) ===

        // A) BÖLÜM (TAKIM) - KATEGORİ DAĞILIMI
        $bolumKategoriData = MusteriSikayeti::select('atanan_cozum_takimi_id', 'sikayet_kategorisi_id', DB::raw('count(*) as total'))
            ->whereNotNull('atanan_cozum_takimi_id')
            ->whereNotNull('sikayet_kategorisi_id')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->with(['cozumTakimi', 'sikayetKategori'])
            ->groupBy('atanan_cozum_takimi_id', 'sikayet_kategorisi_id')
            ->get()
            ->map(function ($item) {
                return [
                    'takim' => $item->cozumTakimi->ad ?? 'Atanmamış',
                    'kategori' => $item->sikayetKategori->ad ?? 'Diğer',
                    'total' => $item->total
                ];
            });

        $takimlar = $bolumKategoriData->pluck('takim')->unique()->values()->toArray();
        $kategoriler = $bolumKategoriData->pluck('kategori')->unique()->values()->toArray();

        $bolumKategoriSeries = [];
        foreach ($kategoriler as $kategori) {
            $data = [];
            foreach ($takimlar as $takim) {
                $record = $bolumKategoriData->where('takim', $takim)->where('kategori', $kategori)->first();
                $data[] = $record ? $record['total'] : 0;
            }
            $bolumKategoriSeries[] = ['name' => $kategori, 'data' => $data];
        }

        // B) ALT KATEGORİ YOĞUNLUK HARİTASI
        $altKategoriData = MusteriSikayeti::select('sikayet_kategorisi_id', 'sikayet_alt_kategori_id', DB::raw('count(*) as total'))
            ->whereNotNull('sikayet_alt_kategori_id')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->with(['sikayetKategori', 'sikayetAltKategori'])
            ->groupBy('sikayet_kategorisi_id', 'sikayet_alt_kategori_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $anaKat = $item->sikayetKategori->ad ?? 'Genel';
                $altKat = $item->sikayetAltKategori->ad ?? ($item->sikayet_alt_kategori_diger ? 'Diğer: ' . Str::limit($item->sikayet_alt_kategori_diger, 15) : 'Belirtilmemiş');
                return ['x' => $anaKat . ' - ' . $altKat, 'y' => $item->total];
            });

        // === C) ZİYARET ANALİZLERİ (TAKVİM API) ===
        $visitStats = [
            'total_visits' => 0,
            'reason_distribution' => [],
            'visit_rate' => 0,
            'dept_visit_rates' => [],
            'visited_count' => 0,
            'non_visited_count' => 0,
            'recent_visits' => []
        ];

        try {
            $takvimUrl = config('services.takvim.url');
            $response = \Illuminate\Support\Facades\Http::get($takvimUrl . '/api/visits/stats', [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate
            ]);

            if ($response->successful()) {
                $apiData = $response->json();
                $visitStats['total_visits'] = $apiData['total_visits'] ?? 0;
                $visitStats['reason_distribution'] = $apiData['reason_distribution'] ?? [];
                
                $visitsByComplaint = $apiData['visits_by_complaint'] ?? []; 
                $apiRecentVisits = $apiData['recent_visits'] ?? [];

                if (count($visitsByComplaint) > 0) {
                    $ids = array_keys($visitsByComplaint);
                    $complaintsWithDepts = MusteriSikayeti::whereIn('musteri_sikayetleri.id', $ids)
                        ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                        ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
                        ->select('musteri_sikayetleri.id', 'bolumler.ad as bolum_adi')
                        ->get();

                    $deptVisitation = [];
                    foreach ($complaintsWithDepts as $cs) {
                        $count = $visitsByComplaint[$cs->id] ?? 0;
                        $deptName = $cs->bolum_adi;
                        $deptVisitation[$deptName] = ($deptVisitation[$deptName] ?? 0) + $count;
                    }
                    arsort($deptVisitation);
                    $visitStats['dept_visit_rates'] = $deptVisitation;
                }

                // Son Ziyaretleri Zenginleştir
                if (count($apiRecentVisits) > 0) {
                    $visitComplaintIds = collect($apiRecentVisits)->pluck('remote_id')->unique()->toArray();
                    $visitComplaints = MusteriSikayeti::whereIn('id', $visitComplaintIds)
                        ->with(['sikayetKategori.bolum', 'iaaProjesi'])
                        ->get()
                        ->keyBy('id');

                    $enrichedVisits = [];
                    foreach ($apiRecentVisits as $v) {
                        $comp = $visitComplaints[$v['remote_id']] ?? null;
                        $enrichedVisits[] = [
                            'id' => $v['id'],
                            'visit_date' => $v['visit_date'],
                            'visit_reason' => $v['visit_reason'],
                            'remote_id' => $v['remote_id'],
                            'complaint_subject' => $comp->musteri_sikayet_konusu ?? '-',
                            'musteri_adi' => $comp->musteri_adi ?? '-',
                            'bolum_adi' => $comp->sikayetKategori->bolum->ad ?? '-',
                            'iaa_projesi_id' => $comp->iaaProjesi->id ?? null
                        ];
                    }
                    $visitStats['recent_visits'] = $enrichedVisits;
                }

                $totalComplaintCount = $kpi['toplam'] ?: 1;
                $visitedComplaintCount = count($visitsByComplaint);
                $visitStats['visit_rate'] = round(($visitedComplaintCount / $totalComplaintCount) * 100, 1);
                $visitStats['visited_count'] = $visitedComplaintCount;
                $visitStats['non_visited_count'] = max(0, $kpi['toplam'] - $visitedComplaintCount);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Takvim Visit Stats API Error: ' . $e->getMessage());
        }

        // === 10. BİRLEŞTİRİLMİŞ TREND VERİSİ (Puan Raporu Stili) ===
        $trendLabels = collect(array_unique(array_merge(array_keys($aylikTrend->toArray()), array_keys($aylikCozulenTrend->toArray()))))->sort()->values();
        $combinedTrend = [
            'labels' => $trendLabels->map(fn($ay) => Carbon::parse($ay . '-01')->format('M Y'))->toArray(),
            'datasets' => [
                [
                    'name' => 'Gelen Şikayet',
                    'data' => $trendLabels->map(fn($ay) => $aylikTrend[$ay] ?? 0)->toArray(),
                    'type' => 'area'
                ],
                [
                    'name' => 'Çözülen Şikayet',
                    'data' => $trendLabels->map(fn($ay) => $aylikCozulenTrend[$ay] ?? 0)->toArray(),
                    'type' => 'line'
                ]
            ]
        ];

        return compact(
            'kpi',
            'durumData',
            'kategoriData',
            'takimData',
            'aylikTrend',
            'cozulenListesi',
            'islemdeListesi',
            'yeniListesi',
            'projeyeDonusenListesi',
            'cozulenChartData',
            'islemdeChartData',
            'yeniChartData',
            'projeyeDonusenChartData',
            'aylikCozulenTrend',
            'sonSikayetler',
            'bolumKategoriSeries',
            'takimlar',
            'altKategoriData',
            'feedbackCounts',
            'bolumMemnuniyeti',
            'iadeVerileri',
            'iadeVerileriCount',
            'toplamIadeMiktarlari',
            'iadeliSikayetSayisi',
            'iadesizSikayetSayisi',
            'bolumIadeChartData',
            'bolumIadeSayilariLabels',
            'bolumIadeSayilariSeries',
            'visitStats',
            'combinedTrend'
        );
    }

    public function render()
    {
        $data = $this->calculateStats();

        $dispatchData = array_merge($data, [
            'bolumKategoriXaxis' => $data['takimlar'],
            'altKategoriSeries' => [['data' => $data['altKategoriData']]],
            // Yeni grafik verileri
            'feedbackCounts' => $data['feedbackCounts'],
            'bolumMemnuniyeti' => $data['bolumMemnuniyeti'],
            // İade Grafikleri
            'iadeliSikayetSayisi' => $data['iadeliSikayetSayisi'],
            'iadesizSikayetSayisi' => $data['iadesizSikayetSayisi'],
            'bolumIadeChartData' => $data['bolumIadeChartData'],
            'bolumIadeSayilariLabels' => $data['bolumIadeSayilariLabels'],
            'bolumIadeSayilariSeries' => $data['bolumIadeSayilariSeries'],
            'combinedTrend' => $data['combinedTrend']
        ]);

        $this->dispatch('updateSikayetRaporlari', $dispatchData);

        return view('livewire.admin.musteri-sikayet-raporu', $data);
    }
}