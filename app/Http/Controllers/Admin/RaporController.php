<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Iaa;
use App\Models\Takim;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ProfesyonelIaalarExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MusteriSikayeti; // <-- 1. BU SATIRI EKLEYİN


class RaporController extends Controller
{
    /**
     * Raporlama ana sayfasını gösterir.
     * KPI kartları ve YENİ GRAFİKLER için gerekli tüm istatistikleri hesaplar.
     */
    public function index()
{
    // === KPI KARTLARI İÇİN VERİLER (Mevcut Kodunuz) ===
    $stats = [
        'toplam_oneri'          => Iaa::count(),
        'onay_bekleyen_oneri'   => Iaa::where('durum', 'Onay Bekliyor')->count(),
        'havuzdaki_oneri'       => Iaa::where('durum', 'Havuzda')->count(),
        'reddedilen_oneri'      => Iaa::whereIn('durum', ['Reddedildi', 'Tamamlanması Reddedildi'])->count(),
        'atanmis_proje'         => Iaa::whereIn('durum', ['Atandı', 'Revize Ediliyor', 'Yönetici Onayı Bekliyor'])->count(),
        'tamamlanan_proje'      => Iaa::where('durum', 'Tamamlandı')->count(),
        'misafir_onerileri'     => Iaa::whereNull('gonderen_user_id')->count(),
        'kullanici_onerileri'   => Iaa::whereNotNull('gonderen_user_id')->count(),
        'toplam_kullanici'      => User::count(),
        'toplam_takim'          => Takim::count(),
    ];

    // ==========================================================
    // === YENİ APEXCHARTS GRAFİKLERİ İÇİN TÜM VERİLER ===========
    // ==========================================================
    
    // 1. Başarı Oranı Grafiği
    $oranChartData = [$stats['tamamlanan_proje'], $stats['toplam_oneri'] - $stats['tamamlanan_proje']];

    // 2. Puan Liderlik Tablosu
    $topKullanicilar = User::where('toplam_puan', '>', 0)->select('users.*')->addSelect(DB::raw('(SELECT count(*) FROM iaas WHERE iaas.durum = "Tamamlandı" AND iaas.atanan_takim_id IN (SELECT takim_id FROM takim_user WHERE takim_user.user_id = users.id)) as proje_sayisi'))->orderBy('toplam_puan', 'desc')->take(5)->get();
    $puanChartLabels = $topKullanicilar->pluck('name');
    $puanChartData = $topKullanicilar->pluck('toplam_puan');
    $projeChartData = $topKullanicilar->pluck('proje_sayisi');

    // 3. Aylık Trend Grafiği (Başlangıç verisi)
    $chartLabels = []; $chartData = [];
    for ($i = 11; $i >= 0; $i--) { $ay = now()->subMonths($i); $chartLabels[] = $ay->translatedFormat('F Y'); $chartData[] = Iaa::whereYear('created_at', $ay->year)->whereMonth('created_at', $ay->month)->count(); }

    // 4. En Çok Takıma Üye Olanlar
    $cokluUyelik = DB::table('takim_user')->join('users', 'takim_user.user_id', '=', 'users.id')->select('users.name', DB::raw('COUNT(takim_user.takim_id) as takim_sayisi'))->groupBy('users.name')->orderBy('takim_sayisi', 'desc')->limit(5)->pluck('takim_sayisi', 'name');
    $cokluUyelikData = ['labels' => $cokluUyelik->keys(), 'series' => $cokluUyelik->values()];

    // 5. En Yüksek Puanlı 5 Proje (Havuzda)
    $havuzPuan = Iaa::where('durum', 'Havuzda')->whereNotNull('puan')->orderBy('puan', 'desc')->limit(5)->pluck('puan', 'baslik');
    $havuzPuanData = ['labels' => $havuzPuan->keys(), 'series' => $havuzPuan->values()];

    // 6. En Yüksek Puanlı 5 Proje (Tamamlanan)
    $tamamlananPuan = Iaa::where('durum', 'Tamamlandı')->whereNotNull('puan')->orderBy('puan', 'desc')->limit(5)->pluck('puan', 'baslik');
    $tamamlananPuanData = ['labels' => $tamamlananPuan->keys(), 'series' => $tamamlananPuan->values()];
        
    // 7. En Kısa Sürede Biten 5 Proje (Tamamlanan)
    $hizliProjeler = Iaa::where('durum', 'Tamamlandı')->whereNotNull('onaylanma_tarihi')->select('baslik', DB::raw('DATEDIFF(updated_at, onaylanma_tarihi) as sure_gun'))->orderBy('sure_gun', 'asc')->limit(5)->pluck('sure_gun', 'baslik');
    $hizliProjeData = ['labels' => $hizliProjeler->keys(), 'series' => $hizliProjeler->values()];

    // --- TÜM VERİLERİ ANA VIEW'E GÖNDER ---
    return view('admin.raporlar.index', compact(
        'stats', 'oranChartData', 'puanChartLabels', 'puanChartData', 'projeChartData', 'chartLabels', 'chartData',
        'cokluUyelikData', 'havuzPuanData', 'tamamlananPuanData', 'hizliProjeData'
    ));
}

/**
     * Müşteri Şikayetleri için canlı raporlama sayfasını gösterir.
     */
    public function sikayetRaporlari()
    {
        // Bu view, içinde Livewire bileşenini çağıracak
        return view('admin.raporlar.sikayet-raporlari');
    }

    /**
     * YENİ METOT: Tüm şikayetleri listeleyen sayfa.
     * (GÜNCELLENDİ: Yeni KPI'lar eklendi)
     */
    public function tumSikayetListesi()
    {
        // === 1. YENİ KPI'LARI HESAPLA ===
        // Not: Bu sorgular tüm tabloyu sayar, sayfalama öncesi
        $stats = [
            'yeni' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
            'islemde' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
            'cozulen' => MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
            'enCokKategori' => MusteriSikayeti::join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->select('sikayet_kategorileri.ad', DB::raw('count(*) as total'))
                ->groupBy('sikayet_kategorileri.ad')
                ->orderBy('total', 'desc')
                ->first(),
        ];
        // === KPI HESAPLAMA SONU ===


        // === 2. SAYFALANMIŞ VERİYİ ÇEK ===
        $sikayetler = MusteriSikayeti::with('sikayetKategori', 'dosyalar')
            // Yorum sayılarını da yükle
            ->withCount(['projeYorumlari', 'musteriProjeYorumlari'])
            ->latest() // created_at'e göre en yeniden eskiye sıralar
            ->paginate(50); // Sayfa başına 50 kayıt

        // Veriyi yeni oluşturacağımız view'a gönder
        return view('admin.raporlar.tum-sikayet-listesi', compact('sikayetler', 'stats'));
    }

    /**
     * Excel dışa aktarma metodu
     */
    public function exportExcel(Request $request)
    {
        try {
            $fileName = 'İAA Raporu - ' . now()->format('d-m-Y') . '.xlsx';
            
            Log::info('📥 Excel export başlatıldı', [
                'filters' => $request->all(),
                'fileName' => $fileName
            ]);
            
            return Excel::download(new ProfesyonelIaalarExport($request->all()), $fileName);
        } catch (\Exception $e) {
            Log::error('❌ Excel export hatası: ' . $e->getMessage());
            return back()->with('error', 'Excel dışa aktarma sırasında bir hata oluştu.');
        }
    }

    /**
     * PDF dışa aktarma metodu
     */
    public function exportPdf(Request $request)
    {
        try {
            $filters = $request->all();
            $query = Iaa::query()->with(['gonderen', 'bolum', 'atananTakim'])->latest();

            // Filtreleri uygula
            $query->when($filters['search'] ?? null, function($q, $search) {
                return $q->where('baslik', 'like', '%' . $search . '%');
            });
            
            $query->when($filters['durum'] ?? null, function ($q, $durum) {
                if ($durum === 'Talep Alan') {
                    return $q->where('durum', 'Havuzda')->has('talepEdenTakimlar');
                }
                return $q->where('durum', $durum);
            });
            
            $query->when($filters['kullaniciTipi'] ?? null, function ($q, $kullaniciTipi) {
                if ($kullaniciTipi === 'kayitli') {
                    return $q->whereNotNull('gonderen_user_id');
                }
                if ($kullaniciTipi === 'misafir') {
                    return $q->whereNull('gonderen_user_id');
                }
            });
            
            $query->when($filters['baslangicTarihi'] ?? null, function($q, $tarih) {
                return $q->whereDate('created_at', '>=', $tarih);
            });
            
            $query->when($filters['bitisTarihi'] ?? null, function($q, $tarih) {
                return $q->whereDate('created_at', '<=', $tarih);
            });

            $iaas = $query->get();
            
            Log::info('📄 PDF export başlatıldı', [
                'filters' => $filters,
                'total_records' => $iaas->count()
            ]);
            
            $pdf = Pdf::loadView('admin.raporlar.partials.rapor-pdf', compact('iaas'));
            
            return $pdf->download('İAA Raporu - ' . now()->format('d-m-Y') . '.pdf');
        } catch (\Exception $e) {
            Log::error('❌ PDF export hatası: ' . $e->getMessage());
            return back()->with('error', 'PDF dışa aktarma sırasında bir hata oluştu.');
        }
    }


    public function grafikTest()
    {
        // === YENİ GRAFİK 1: En Çok Takıma Üye Olanlar ===
        $cokluUyelik = DB::table('takim_user')
            ->join('users', 'takim_user.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(takim_user.takim_id) as takim_sayisi'))
            ->groupBy('users.name')
            ->orderBy('takim_sayisi', 'desc')
            ->limit(5)
            ->pluck('takim_sayisi', 'name');
        $cokluUyelikData = ['labels' => $cokluUyelik->keys(), 'series' => $cokluUyelik->values()];

        // === YENİ GRAFİK 2: En Yüksek Puanlı 5 Proje (Havuzda) ===
        $havuzPuan = Iaa::where('durum', 'Havuzda')
            ->whereNotNull('puan')
            ->orderBy('puan', 'desc')
            ->limit(5)
            ->pluck('puan', 'baslik');
        $havuzPuanData = ['labels' => $havuzPuan->keys(), 'series' => $havuzPuan->values()];

        // === YENİ GRAFİK 3: En Yüksek Puanlı 5 Proje (Tamamlanan) ===
        $tamamlananPuan = Iaa::where('durum', 'Tamamlandı')
            ->whereNotNull('puan')
            ->orderBy('puan', 'desc')
            ->limit(5)
            ->pluck('puan', 'baslik');
        $tamamlananPuanData = ['labels' => $tamamlananPuan->keys(), 'series' => $tamamlananPuan->values()];
            
        // === YENİ GRAFİK 4: En Kısa Sürede Biten 5 Proje (Tamamlanan) ===
        $hizliProjeler = Iaa::where('durum', 'Tamamlandı')
        ->whereNotNull('onaylanma_tarihi') // Projenin bir başlangıç tarihi olmalı
        ->select('baslik', DB::raw('DATEDIFF(updated_at, onaylanma_tarihi) as sure_gun')) // DOĞRU HESAPLAMA
        ->orderBy('sure_gun', 'asc')
        ->limit(5)
        ->pluck('sure_gun', 'baslik');
        $hizliProjeData = ['labels' => $hizliProjeler->keys(), 'series' => $hizliProjeler->values()];

        // --- ESKİ GRAFİKLERİN VERİLERİ (Aynen kalacak) ---
        $tamamlananSayisi = Iaa::where('durum', 'Tamamlandı')->count();
        $oranChartData = [$tamamlananSayisi, Iaa::count() - $tamamlananSayisi];
        $topKullanicilar = User::where('toplam_puan', '>', 0)->select('users.*')->addSelect(DB::raw('(SELECT count(*) FROM iaas WHERE iaas.durum = "Tamamlandı" AND iaas.atanan_takim_id IN (SELECT takim_id FROM takim_user WHERE takim_user.user_id = users.id)) as proje_sayisi'))->orderBy('toplam_puan', 'desc')->take(5)->get();
        $puanChartLabels = $topKullanicilar->pluck('name');
        $puanChartData = $topKullanicilar->pluck('toplam_puan');
        $projeChartData = $topKullanicilar->pluck('proje_sayisi');
        $chartLabels = []; $chartData = [];
        for ($i = 11; $i >= 0; $i--) { $ay = now()->subMonths($i); $chartLabels[] = $ay->translatedFormat('F Y'); $chartData[] = Iaa::whereYear('created_at', $ay->year)->whereMonth('created_at', $ay->month)->count(); }

        // --- TÜM VERİLERİ VIEW'E GÖNDER ---
        return view('admin.raporlar.grafik-test', compact(
            'cokluUyelikData', 'havuzPuanData', 'tamamlananPuanData', 'hizliProjeData', // Yeni Veriler
            'oranChartData', 'puanChartLabels', 'puanChartData', 'projeChartData', 'chartLabels', 'chartData' // Eski Veriler
        ));
    }
}