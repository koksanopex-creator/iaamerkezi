<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusteriSikayeti;
use Illuminate\Support\Facades\DB;

class RaporController extends Controller
{
    /**
     * İAA Raporları Ana Sayfası
     */
    public function index()
    {
        if (!auth()->user()->hasRole(['Superadmin', 'Yonetim'])) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        return view('admin.raporlar.index');
    }

    /**
     * Müşteri Şikayetleri İstatistik Sayfası
     */
    public function sikayetRaporlari()
    {
        $feedbackCounts = MusteriSikayeti::whereNotNull('musteri_feedback')
            ->selectRaw('musteri_feedback, count(*) as total')
            ->groupBy('musteri_feedback')
            ->pluck('total', 'musteri_feedback');

        $bolumMemnuniyeti = MusteriSikayeti::whereNotNull('musteri_feedback')
            ->join('iaas', 'musteri_sikayetleri.iaa_id', '=', 'iaas.id')
            ->join('bolumler', 'iaas.bolum_id', '=', 'bolumler.id')
            ->selectRaw('bolumler.ad as bolum_adi, 
                         SUM(CASE WHEN musteri_feedback = "Onaylandı" THEN 1 ELSE 0 END) as onay_sayisi,
                         SUM(CASE WHEN musteri_feedback = "Reddedildi" THEN 1 ELSE 0 END) as red_sayisi,
                         SUM(CASE WHEN musteri_feedback = "Revizyon İstendi" THEN 1 ELSE 0 END) as revizyon_sayisi')
            ->groupBy('bolumler.ad')
            ->get();

        return view('admin.raporlar.sikayet-raporlari', compact('feedbackCounts', 'bolumMemnuniyeti'));
    }

    /**
     * Tüm Şikayet Listesi
     */
    public function tumSikayetListesi()
    {
        $stats = [
            'yeni' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
            'islemde' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
            'cozulen' => MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
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
                ])->count(),

            'talep_kapatilan' => MusteriSikayeti::where(function ($q) {
                $q->whereIn('musteri_durum', ['Talep Olarak Kapatıldı', 'talep_olarak_kapatildi'])
                    ->orWhereHas('iaaProjesi', function ($sq) {
                        $sq->whereIn('durum', ['talep_olarak_kapatildi', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_superadmin']);
                    });
            })->count(),

            'hatali_bildirim' => MusteriSikayeti::where(function ($q) {
                $q->whereIn('musteri_durum', ['Hatalı Bildirim Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi'])
                    ->orWhereHas('iaaProjesi', function ($sq) {
                        $sq->whereIn('durum', ['hatali_bildirim_olarak_kapatildi', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor']);
                    });
            })->count(),


            'enCokKategori' => MusteriSikayeti::join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->select('sikayet_kategorileri.ad', DB::raw('count(*) as total'))
                ->groupBy('sikayet_kategorileri.ad')
                ->orderBy('total', 'desc')
                ->first(),
        ];

        $sikayetler = MusteriSikayeti::with('sikayetKategori', 'dosyalar')
            ->withCount(['projeYorumlari', 'musteriProjeYorumlari'])
            ->latest()
            ->paginate(50);

        return view('admin.raporlar.tum-sikayet-listesi', compact('sikayetler', 'stats'));
    }

    /**
     * İAA (Öneri Sistemi) Detaylı Rapor Sayfası
     */
    public function iaaRaporlari()
    {
        // 1. Durum Dağılımı
        $durumDagilimi = \App\Models\Iaa::doesntHave('musteriSikayeti')
            ->select('durum', DB::raw('count(*) as total'))
            ->groupBy('durum')
            ->get();

        // 2. Bölüm Performansı (Toplam Öneri Sayısı)
        $bolumPerformansi = \App\Models\Iaa::doesntHave('musteriSikayeti')
            ->join('users', 'iaas.gonderen_user_id', '=', 'users.id')
            ->join('bolumler', 'users.bolum_id', '=', 'bolumler.id')
            ->select('bolumler.ad as bolum_adi', DB::raw('count(*) as toplam'))
            ->groupBy('bolumler.ad')
            ->orderByDesc('toplam')
            ->take(10)
            ->get();

        // 3. Kazanç/Tasarruf Raporu (Onaylanan projelerden)
        $kazancRaporu = \App\Models\Iaa::doesntHave('musteriSikayeti')
            ->whereNotNull('kazanc_miktar')
            ->select(
                DB::raw('SUM(kazanc_miktar) as toplam_kazanc'),
                'kazanc_birim'
            )
            ->groupBy('kazanc_birim')
            ->get();

        // 4. Son Eklenen Projeler (Liste için)
        $sonProjeler = \App\Models\Iaa::doesntHave('musteriSikayeti')
            ->with(['gonderen', 'bolum'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.raporlar.iaa-raporlari', compact('durumDagilimi', 'bolumPerformansi', 'kazancRaporu', 'sonProjeler'));
    }
}