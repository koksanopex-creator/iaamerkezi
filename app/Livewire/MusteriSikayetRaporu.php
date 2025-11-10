<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use App\Models\Takim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        // === 2. ORİJİNAL 4 GRAFİK VERİSİ (image_c80e84.png) ===
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

        // === 3. YENİ LİSTELER (image_c87723.png) ===
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

        // === 4. YENİ 4 DONUT GRAFİK VERİSİ (image_c87723.png) ===
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

        // === 5. YENİ AYLIK ÇÖZÜLEN TREND VERİSİ (image_c87723.png) ===
        $aylikCozulenTrend = MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->select(
                DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as ay"),
                DB::raw('count(*) as total')
            )
            ->where('updated_at', '>=', now()->subMonths(12))
            ->groupBy('ay')
            ->orderBy('ay', 'asc')
            ->pluck('total', 'ay');

            // === YENİ EKLENDİ (SON 10 ŞİKAYET TABLOSU İÇİN) ===
        $sonSikayetler = MusteriSikayeti::with('sikayetKategori', 'dosyalar')
        ->latest() // En son ekleneni (created_at) en üste alır
        ->take(10) // Sadece 10 tane alır
        ->get();
    // === YENİ EKLEME SONU ===

        return compact(
            'kpi', 
            'durumData', 'kategoriData', 'takimData', 'aylikTrend', // Orijinal 4 grafik
            'cozulenListesi', 'islemdeListesi', 'yeniListesi', 'projeyeDonusenListesi', // Yeni 4 liste
            'cozulenChartData', 'islemdeChartData', 'yeniChartData', 'projeyeDonusenChartData', // Yeni 4 donut
            'aylikCozulenTrend', // Yeni 1 line
            'sonSikayetler' // <-- YENİ EKLENDİ
        );
    }

    public function render()
    {
        $data = $this->calculateStats();
        
        // Tek bir olay (event) ile TÜM verileri JavaScript'e gönder
        $this->dispatch('updateSikayetRaporlari', $data);

        return view('livewire.admin.musteri-sikayet-raporu', $data);
    }
}