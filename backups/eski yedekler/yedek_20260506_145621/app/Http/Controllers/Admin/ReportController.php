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
     * IAA Proje Raporları Sayfası
     */
    public function iaaRaporlari()
    {
        return view('admin.raporlar.iaa-raporlari');
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

        // 1. Kategoriler (Filtre dropdown için)
        $kategoriler = SikayetKategori::orderBy('ad')->get();

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
            'enCokKategori' => (clone $query)
                ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->select('sikayet_kategorileri.ad', DB::raw('count(*) as total'))
                ->groupBy('sikayet_kategorileri.ad')
                ->orderByDesc('total')
                ->first()
        ];

        // 4. Verileri Çek
        $sikayetler = $query->with(['iadeler', 'sikayetKategori', 'dosyalar'])
            ->withCount(['iadeler', 'projeYorumlari', 'musteriProjeYorumlari'])
            ->latest()
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

    public function exportExcel()
    { /* Opsiyonel */
    }
    public function exportPdf()
    { /* Opsiyonel */
    }
}
