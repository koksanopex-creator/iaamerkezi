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

class MusteriSikayetRaporu extends Component
{
    // Filtreleme Değişkenleri
    // Filtreleme Değişkenleri
    public $startDate;
    public $endDate;
    public $activeFilter = 'toplam'; // Varsayılan filtre

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
            'projeye_donusen' => MusteriSikayeti::whereNotNull('iaa_id')
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
            ->take(50) // Filtre varken 10 az kalabilir, 50 yapalım veya pagination (User 'filtrelensin' dedi, pagination yok ama 10 az)
            ->get();

        // === 7. İADE ANALİZLERİ (YENİ) ===

        // A) İade Verileri Tablosu (Son 50 kayıt)
        $iadeVerileri = SikayetIadesi::with(['musteriSikayeti.sikayetKategori.bolum', 'user'])
            ->when($this->startDate, fn($q) => $q->whereDate('iade_tarihi', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('iade_tarihi', '<=', $this->endDate))
            ->latest('iade_tarihi')
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

        // === YENİ GRAFİKLER İÇİN VERİ HAZIRLIĞI ===

        // 1. BÖLÜM (TAKIM) - KATEGORİ DAĞILIMI (Hangi takımda hangi kategoriden kaç tane var?)
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

        // Veriyi grafik formatına dönüştürme (Takımlar X ekseni, Kategoriler Seriler)
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

        // 2. KATEGORİ - ALT KATEGORİ DAĞILIMI (Treemap Verisi)
        // 2. ALT KATEGORİ YOĞUNLUK HARİTASI
        $altKategoriData = MusteriSikayeti::select('sikayet_kategorisi_id', 'sikayet_alt_kategori_id', DB::raw('count(*) as total'))
            ->whereNotNull('sikayet_alt_kategori_id')
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->with(['sikayetKategori', 'sikayetAltKategori'])
            ->groupBy('sikayet_kategorisi_id', 'sikayet_alt_kategori_id')
            ->orderBy('total', 'desc')
            ->take(10) // En çok şikayet alan ilk 10 alt kategori
            ->get()
            ->map(function ($item) {
                // İsimlendirme: Ana Kategori > Alt Kategori
                $anaKat = $item->sikayetKategori->ad ?? 'Genel';
                $altKat = $item->sikayetAltKategori->ad ?? ($item->sikayet_alt_kategori_diger ? 'Diğer: ' . Str::limit($item->sikayet_alt_kategori_diger, 15) : 'Belirtilmemiş');

                return [
                    'x' => $anaKat . ' - ' . $altKat, // Etiket
                    'y' => $item->total
                ];
            });


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
            // --- YENİ EKLENENLER ---
            'feedbackCounts',
            'bolumMemnuniyeti',
            // --- İADE VERİLERİ ---
            'iadeVerileri',
            'toplamIadeMiktarlari',
            'iadeliSikayetSayisi',
            'iadesizSikayetSayisi',
            'bolumIadeChartData', // Birim bazlı department grafikleri
            'bolumIadeSayilariLabels',
            'bolumIadeSayilariSeries'
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
            'bolumIadeSayilariSeries' => $data['bolumIadeSayilariSeries']
        ]);

        $this->dispatch('updateSikayetRaporlari', $dispatchData);

        return view('livewire.admin.musteri-sikayet-raporu', $data);
    }
}