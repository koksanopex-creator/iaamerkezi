<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use App\Models\Takim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Str sınıfını ekledik

class MusteriSikayetRaporu extends Component
{
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
            'toplam' => MusteriSikayeti::count(),
            'yeni' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
            'islemde' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
            'cozuldu' => MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
            'gecikmis' => MusteriSikayeti::whereNotNull('musteri_cozum_son_tarihi')
                                ->where('musteri_cozum_son_tarihi', '<', now())
                                ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                                ->count(),
            'projeye_donusen' => MusteriSikayeti::whereNotNull('iaa_id')->count(),
        ];

        // === 2. ORİJİNAL 4 GRAFİK VERİSİ ===
        $durumData = MusteriSikayeti::select('musteri_durum', DB::raw('count(*) as total'))
            ->groupBy('musteri_durum')
            ->pluck('total', 'musteri_durum');
            
        $kategoriData = SikayetKategori::withCount('sikayetler')
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
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('ay')
            ->orderBy('ay', 'asc')
            ->pluck('total', 'ay');

        // === 3. YENİ LİSTELER ===
        $cozulenListesi = MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->with('iaaProjesi')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $islemdeListesi = MusteriSikayeti::where('musteri_durum', 'İşlemde')
            ->with('iaaProjesi')
            ->latest()
            ->take(5)
            ->get();
            
        $yeniListesi = MusteriSikayeti::where('musteri_durum', 'Yeni')
            ->with('iaaProjesi')
            ->latest()
            ->take(5)
            ->get();

        $projeyeDonusenListesi = MusteriSikayeti::whereNotNull('iaa_id')
            ->with('iaaProjesi')
            ->latest('updated_at')
            ->take(5)
            ->get();

        // === 4. YENİ 4 DONUT GRAFİK VERİSİ ===
        $cozulenChartData = MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->select('musteri_oncelik as etiket', DB::raw('count(*) as total'))
            ->groupBy('etiket')
            ->pluck('total', 'etiket');
            
        $islemdeChartData = MusteriSikayeti::where('musteri_durum', 'İşlemde')
            ->select('musteri_oncelik as etiket', DB::raw('count(*) as total'))
            ->groupBy('etiket')
            ->pluck('total', 'etiket');
            
        $yeniChartData = MusteriSikayeti::where('musteri_durum', 'Yeni')
            ->select('musteri_oncelik as etiket', DB::raw('count(*) as total'))
            ->groupBy('etiket')
            ->pluck('total', 'etiket');

        $projeyeDonusenChartData = MusteriSikayeti::whereNotNull('iaa_id')
            ->select('musteri_oncelik as etiket', DB::raw('count(*) as total'))
            ->groupBy('etiket')
            ->pluck('total', 'etiket');

        // === 5. YENİ AYLIK ÇÖZÜLEN TREND VERİSİ ===
        $aylikCozulenTrend = MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->select(
                DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as ay"),
                DB::raw('count(*) as total')
            )
            ->where('updated_at', '>=', now()->subMonths(12))
            ->groupBy('ay')
            ->orderBy('ay', 'asc')
            ->pluck('total', 'ay');

        // ==========================================================
        // === 6. MÜŞTERİ GERİ BİLDİRİM VE MEMNUNİYET ANALİZİ ===
        // ==========================================================
        
        // A) Genel Geri Bildirim Dağılımı (Pasta Grafik)
        $feedbackCounts = MusteriSikayeti::whereNotNull('musteri_feedback')
            ->selectRaw('musteri_feedback, count(*) as total')
            ->groupBy('musteri_feedback')
            ->pluck('total', 'musteri_feedback');

        // B) Bölüm Bazlı Memnuniyet (Sütun Grafik)
        // Şikayet -> Proje (IAA) -> Bölüm ilişkisi üzerinden
        $bolumMemnuniyeti = MusteriSikayeti::whereNotNull('musteri_feedback')
            ->join('iaas', 'musteri_sikayetleri.iaa_id', '=', 'iaas.id')
            ->join('bolumler', 'iaas.bolum_id', '=', 'bolumler.id')
            ->selectRaw('bolumler.ad as bolum_adi, 
                         SUM(CASE WHEN musteri_feedback = "Onaylandı" THEN 1 ELSE 0 END) as onay_sayisi,
                         SUM(CASE WHEN musteri_feedback = "Reddedildi" THEN 1 ELSE 0 END) as red_sayisi,
                         SUM(CASE WHEN musteri_feedback = "Revizyon İstendi" THEN 1 ELSE 0 END) as revizyon_sayisi')
            ->groupBy('bolumler.ad')
            ->get();
        // === SON 10 ŞİKAYET TABLOSU İÇİN ===
        $sonSikayetler = MusteriSikayeti::with('sikayetKategori', 'dosyalar')
            ->withCount(['projeYorumlari', 'musteriProjeYorumlari'])
            ->latest()
            ->take(10)
            ->get();

        // === YENİ GRAFİKLER İÇİN VERİ HAZIRLIĞI ===

        // 1. BÖLÜM (TAKIM) - KATEGORİ DAĞILIMI (Hangi takımda hangi kategoriden kaç tane var?)
        $bolumKategoriData = MusteriSikayeti::select('atanan_cozum_takimi_id', 'sikayet_kategorisi_id', DB::raw('count(*) as total'))
            ->whereNotNull('atanan_cozum_takimi_id')
            ->whereNotNull('sikayet_kategorisi_id')
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
        $altKategoriData = MusteriSikayeti::select('sikayet_kategorisi_id', 'sikayet_alt_kategori_id', 'sikayet_alt_kategori_diger', DB::raw('count(*) as total'))
            ->whereNotNull('sikayet_kategorisi_id')
            ->with(['sikayetKategori', 'sikayetAltKategori'])
            ->groupBy('sikayet_kategorisi_id', 'sikayet_alt_kategori_id', 'sikayet_alt_kategori_diger')
            ->get()
            ->map(function($item) {
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
                'durumData', 'kategoriData', 'takimData', 'aylikTrend', 
                'cozulenListesi', 'islemdeListesi', 'yeniListesi', 'projeyeDonusenListesi', 
                'cozulenChartData', 'islemdeChartData', 'yeniChartData', 'projeyeDonusenChartData', 
                'aylikCozulenTrend', 
                'sonSikayetler',
                'bolumKategoriSeries', 'takimlar', 'altKategoriData',
                // --- YENİ EKLENENLER ---
                'feedbackCounts', 'bolumMemnuniyeti'
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
            'bolumMemnuniyeti' => $data['bolumMemnuniyeti']
        ]);

        $this->dispatch('updateSikayetRaporlari', $dispatchData);

        return view('livewire.admin.musteri-sikayet-raporu', $data);
    }
}