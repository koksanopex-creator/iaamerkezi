<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RaporVeriServisi;
use Illuminate\Support\Facades\Auth;
use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    /**
     * Günlük Müşteri Şikayetleri Raporu Sayfası
     */
    public function dailyComplaintReport(Request $request)
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['Hukuk Admini', 'Hukuk Yöneticisi']))
        {
            abort(403, 'Hukuk birimi bu raporu görüntüleme yetkisine sahip değildir.');
        }

        if (!$user->hasSikayetOrganikBagi())
        {
            abort(403, 'Müşteri şikayetleri modülüyle organik bağınız bulunmadığı için bu raporu görüntüleyemezsiniz.');
        }

        $servis = new RaporVeriServisi();
        $servis->setUser($user);

        $icerikAyarlari = [
            'sikayet_ozet' => true,
            'sikayet_detay' => true,
            'iaa_ozet' => true,
            'disiplin_ozet' => true,
            'arabuluculuk_ozet' => true
        ];

        $raporData = $servis->verileriTopla($icerikAyarlari);

        return view('admin.reports.daily-complaint-report', [
            'raporData' => $raporData,
            'tarih' => now()->translatedFormat('d F Y l')
        ]);
    }

    /**
     * Genel Raporlar İndeksi
     */
    public function index()
    {
        return view('admin.raporlar.index');
    }

    /**
     * Müşteri Şikayet Raporları Ana Sayfası
     */
    public function sikayetRaporlari()
    {
        return view('admin.raporlar.sikayet-raporlari');
    }

    /**
     * Müşteri Şikayet Analiz Raporu Sayfası
     */
    public function sikayetAnalizRaporu()
    {
        $user = Auth::user();

        // Dinamik yetki kontrolü: report_user_authorizations tablosundan (analiz_raporu)
        $authorization = \App\Models\ReportRoleAuthorization::getAuthorizationForUser($user, 'analiz_raporu');

        if (!$authorization) {
            abort(403, 'Bu analiz raporunu görüntüleme yetkiniz bulunmamaktadır.');
        }

        return view('admin.raporlar.musteri-sikayet-analiz-raporu');
    }


    /**
     * IAA Proje Raporları Sayfası
     */
    public function iaaRaporlari()
    {
        // 1. Kazanç Özeti (Birim bazlı toplamlar)
        $kazancRaporu = \App\Models\Iaa::whereNotNull('kazanc_miktar')
            ->select('kazanc_birim', DB::raw('SUM(kazanc_miktar) as toplam_kazanc'))
            ->groupBy('kazanc_birim')
            ->get();

        // 2. Son Projeler
        $sonProjeler = \App\Models\Iaa::with(['bolum', 'gonderen'])
            ->latest()
            ->take(10)
            ->get();

        // 3. Durum Dağılımı (Grafik için)
        $durumDagilimi = \App\Models\Iaa::select('durum', DB::raw('count(*) as total'))
            ->groupBy('durum')
            ->get();

        // 4. Bölüm Performansı (Grafik için)
        $bolumPerformansi = \App\Models\Iaa::join('bolumler', 'iaas.bolum_id', '=', 'bolumler.id')
            ->select('bolumler.ad as bolum_adi', DB::raw('count(*) as toplam'))
            ->groupBy('bolumler.ad')
            ->orderByDesc('toplam')
            ->take(10)
            ->get();

        return view('admin.raporlar.iaa-raporlari', compact('kazancRaporu', 'sonProjeler', 'durumDagilimi', 'bolumPerformansi'));
    }

    /**
     * İadeler Raporu Sayfası (Müşteri Şikayetleri Kaynaklı İadeler)
     */
    public function iadeRaporlari()
    {
        return view('admin.raporlar.iade_raporlari');
    }

    /**
     * Tüm Şikayetler Tablo Listesi (Filtrelemeli ve KPI'lı)
     */
    public function tumSikayetListesi(Request $request)
    {
        $user = Auth::user();
        $servis = new RaporVeriServisi();
        $servis->setUser($user);

        // 1. Kategoriler (Yetki Bazlı Filtre dropdown için)
        $allowedIds = $user->getAllowedBolumIds();
        $kategorilerQuery = SikayetKategori::query();
        if ($allowedIds !== '*') {
            $kategorilerQuery->whereIn('bolum_id', $allowedIds);
        }
        $kategoriler = $kategorilerQuery->orderBy('ad')->get();

        // 2. Ana Sorgu (Yetki kısıtlamalı)
        // RaporVeriServisi'ndeki applySikayetScope metodunu koruyarak verileri çekiyoruz
        $query = MusteriSikayeti::query();

        // Yetki Filtresi (Service içindeki mantığın aynısı)
        if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu']))
        {
            $allowedBolumIds = $user->getAllowedBolumIds();
            $query->whereHas('sikayetKategori', function ($q) use ($allowedBolumIds)
            {
                if ($allowedBolumIds !== '*')
                {
                    $q->whereIn('bolum_id', $allowedBolumIds);
                }
            });
        }

        // URL Filtreleri
        if ($request->filled('start_date'))
        {
            $query->whereDate('musteri_sikayetleri.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date'))
        {
            $query->whereDate('musteri_sikayetleri.created_at', '<=', $request->end_date);
        }
        if ($request->filled('kategori_id'))
        {
            $query->where('sikayet_kategorisi_id', $request->kategori_id);
        }
        if ($request->filled('durum'))
        {
            if ($request->durum === 'Kapatıldı') {
                $query->whereIn('musteri_durum', ['Kapatıldı', 'Çözümlendi']);
            } else {
                $query->where('musteri_durum', $request->durum);
            }
        }
        if ($request->filled('gecikmis') && $request->gecikmis == '1')
        {
            $query->whereNotNull('musteri_cozum_son_tarihi')
                ->where('musteri_cozum_son_tarihi', '<', now())
                ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı']);
        }
        if ($request->filled('iade_durumu'))
        {
            if ($request->iade_durumu == 'iadeli') {
                $query->has('iadeler');
            } elseif ($request->iade_durumu == 'iadesiz') {
                $query->doesntHave('iadeler');
            }
        }

        // 3. İstatistikleri Hesapla (Sayfalama öncesi)
        $stats = [
            'yeni' => (clone $query)->where('musteri_durum', 'Yeni')->count(),
            'islemde' => (clone $query)->where('musteri_durum', 'İşlemde')->count(),
            'cozulen' => (clone $query)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
            'talep_kapatilan' => (clone $query)->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'))->count(),
            'hatali_bildirim' => (clone $query)->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'))->count(),
            'gecikmis' => (clone $query)->whereNotNull('musteri_cozum_son_tarihi')
                ->where('musteri_cozum_son_tarihi', '<', now())
                ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                ->count(),
            'enCokKategori' => (clone $query)
                ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->select('sikayet_kategorileri.ad', DB::raw('count(*) as total'))
                ->groupBy('sikayet_kategorileri.ad')
                ->orderByDesc('total')
                ->first()
        ];

        // 4. Sıralama Mantığı
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction');
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        if ($sortField === 'gecikme_suresi') {
            $query->whereNotNull('musteri_cozum_son_tarihi')
                ->where('musteri_cozum_son_tarihi', '<', now())
                ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                ->select('*')
                ->selectRaw('DATEDIFF(NOW(), musteri_cozum_son_tarihi) as gecikme_gun')
                ->orderBy('gecikme_gun', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // 5. Verileri Çek
        $sikayetler = $query->with(['iadeler', 'sikayetKategori', 'dosyalar'])
            ->withCount(['iadeler', 'projeYorumlari', 'musteriProjeYorumlari'])
            ->paginate(20)
            ->withQueryString();

        // 5. Ziyaret Verilerini Çek (Opsiyonel)
        $visitsByComplaint = [];
        try {
            $takvimUrl = config('services.takvim.url');
            if ($takvimUrl) {
                $response = Http::get($takvimUrl . '/api/visits/stats', [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
                if ($response->successful()) {
                    $visitsByComplaint = $response->json()['visits_by_complaint'] ?? [];
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Takvim API Error (Tum Liste): ' . $e->getMessage());
        }

        return view('admin.raporlar.tum-sikayet-listesi', compact('sikayetler', 'stats', 'kategoriler', 'visitsByComplaint'));
    }

    /**
     * Şikayet Rapor Tablosu (Livewire dashboard)
     */
    public function sikayetRaporTablosu()
    {
        return view('admin.raporlar.yeni-tum-sikayet-listesi');
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SikayetlerExport($request), 'musteri-sikayetleri-' . now()->format('d-m-Y') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        // Yetki ve sorgu mantığı (tumSikayetListesi ile aynı, sadece pagination yok)
        $user = Auth::user();
        $query = MusteriSikayeti::query()->with(['sikayetKategori', 'customer']);

        if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu'])) {
            $allowedBolumIds = $user->getAllowedBolumIds();
            $query->whereHas('sikayetKategori', function ($q) use ($allowedBolumIds) {
                if ($allowedBolumIds !== '*') {
                    $q->whereIn('bolum_id', $allowedBolumIds);
                }
            });
        }

        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('created_at', '<=', $request->end_date);
        if ($request->filled('kategori_id')) $query->where('sikayet_kategorisi_id', $request->kategori_id);
        if ($request->filled('durum')) {
            if ($request->durum === 'Kapatıldı') {
                $query->whereIn('musteri_durum', ['Kapatıldı', 'Çözümlendi']);
            } else {
                $query->where('musteri_durum', $request->durum);
            }
        }

        $sikayetler = $query->latest()->get();
        $tarih = now()->format('d.m.Y H:i');

        // [OPTIMIZATION] Production-ready DomPDF settings
        $options = [
            'tempDir' => public_path('storage/tmp'),
            'chroot'  => public_path(),
        ];

        if (!file_exists(public_path('storage/tmp'))) {
            mkdir(public_path('storage/tmp'), 0755, true);
        }

        $viewName = 'admin.reports.pdf.sikayet-listesi';
        if (!\View::exists($viewName)) {
            \Illuminate\Support\Facades\Log::error("PDF View Not Found: {$viewName}");
            return back()->with('error', 'PDF şablonu bulunamadı.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, compact('sikayetler', 'tarih'))
            ->setPaper('a4', 'landscape')
            ->setOptions($options);

        return $pdf->download('musteri-sikayetleri-' . now()->format('d-m-Y') . '.pdf');
    }
}
