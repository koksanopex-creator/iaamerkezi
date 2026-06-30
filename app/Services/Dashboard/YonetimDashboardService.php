<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\Iaa;
use App\Models\Bolum;
use App\Models\Takim;
use App\Models\MusteriSikayeti;
use App\Models\ProjeYorumu;
use App\Models\ProfileComment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class YonetimDashboardService
{
    /**
     * YÖNETİM PORTALI İÇİN VERİLERİ HAZIRLAR
     */
    public function getStats($bolumId = null, $startDate = null, $endDate = null)
    {
        $stats = [];

        // Baz tarih filtreleri
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        // 1. ÜST KARTLAR (Genel Durum)
        $stats['toplam_calisan'] = User::when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count();
        
        // Müşteri Şikayeti / İAA Ayrımı Netleştirme
        $stats['toplam_proje'] = Iaa::whereDoesntHave('musteriSikayeti') // Sadece saf İAA
            ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
            ->when($start, fn($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn($q) => $q->where('created_at', '<=', $end))
            ->count();

        $stats['tamamlanan_proje'] = Iaa::whereDoesntHave('musteriSikayeti')
            ->where('durum', 'Tamamlandı')
            ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
            ->when($start, fn($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn($q) => $q->where('created_at', '<=', $end))
            ->count();

        $stats['devam_eden_proje'] = Iaa::whereDoesntHave('musteriSikayeti')
            ->whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor'])
            ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
            ->count();

        $stats['toplam_sikayet'] = MusteriSikayeti::when($bolumId, function($q) use ($bolumId) {
                $q->whereHas('sikayetKategori', fn($sq) => $sq->where('bolum_id', $bolumId));
            })
            ->when($start, fn($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn($q) => $q->where('created_at', '<=', $end))
            ->count();

        $stats['cozulen_sikayet'] = MusteriSikayeti::where('musteri_durum', 'Kapatıldı')
            ->when($bolumId, function($q) use ($bolumId) {
                $q->whereHas('sikayetKategori', fn($sq) => $sq->where('bolum_id', $bolumId));
            })
            ->when($start, fn($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn($q) => $q->where('created_at', '<=', $end))
            ->count();

        // 2. DEPARTMAN PERFORMANSLARI (Bölüm Bazlı)
        $bolumler = Bolum::when($bolumId, fn($q) => $q->where('id', $bolumId))
            ->withCount(['users', 'iaas'])
            ->get();
        $bolumPerformanslari = [];

        foreach ($bolumler as $bolum) {
            // Bölümün tamamladığı projeler
            $tamamlanan = Iaa::whereHas('atananTakim.lider', function ($q) use ($bolum) {
                $q->where('bolum_id', $bolum->id);
            })->where('durum', 'Tamamlandı')
            ->when($start, fn($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn($q) => $q->where('created_at', '<=', $end))
            ->count();

            // Bölümün sorumlu olduğu aktif şikayetler
            $aktifSikayet = MusteriSikayeti::whereHas('cozumTakimi.lider', function ($q) use ($bolum) {
                $q->where('bolum_id', $bolum->id);
            })->where('musteri_durum', '!=', 'Kapatıldı')
            ->when($start, fn($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn($q) => $q->where('created_at', '<=', $end))
            ->count();

            $bolumPerformanslari[] = [
                'ad' => $bolum->ad,
                'personel_sayisi' => $bolum->users_count,
                'toplam_proje' => $bolum->iaas_count,
                'tamamlanan' => $tamamlanan,
                'aktif_sikayet' => $aktifSikayet,
                'basari_orani' => $bolum->iaas_count > 0 ? round(($tamamlanan / $bolum->iaas_count) * 100) : 0
            ];
        }
        $stats['bolum_performanslari'] = collect($bolumPerformanslari)->sortByDesc('basari_orani')->values();

        // 3. EN AKTİF PERSONELLER (Tarih Filtresi Varsa Ona Göre)
        $stats['en_aktif_personeller'] = User::when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
            ->withCount([
                'gorevliOlduguProjeler' => function ($q) use ($start, $end) {
                    $q->where('iaas.durum', 'Tamamlandı')
                      ->when($start, fn($sq) => $sq->where('iaas.created_at', '>=', $start))
                      ->when($end, fn($sq) => $sq->where('iaas.created_at', '<=', $end));
                }
            ])
            ->orderByDesc('gorevli_oldugu_projeler_count')
            ->take(5)
            ->get();

        // 4. SON 12 AYLIK PROJE GRAFİĞİ VERİLERİ (Trend genelde son 12 ay kalır ama bölüm filtresi eklenebilir)
        $aylar = [];
        $projeVerileri = [];
        $sikayetVerileri = [];

        for ($i = 11; $i >= 0; $i--) {
            $tarih = Carbon::now()->subMonths($i);
            $aylar[] = $tarih->format('M Y');

            $projeVerileri[] = Iaa::whereYear('created_at', $tarih->year)
                ->whereMonth('created_at', $tarih->month)
                ->whereDoesntHave('musteriSikayeti')
                ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
                ->count();

            $sikayetVerileri[] = MusteriSikayeti::whereYear('created_at', $tarih->year)
                ->whereMonth('created_at', $tarih->month)
                ->when($bolumId, function($q) use ($bolumId) {
                    $q->whereHas('sikayetKategori', fn($sq) => $sq->where('bolum_id', $bolumId));
                })
                ->count();
        }

        $stats['grafik'] = [
            'aylar' => $aylar,
            'projeler' => $projeVerileri,
            'sikayetler' => $sikayetVerileri
        ];

        // 5. BEKLEYEN KRİTİK İŞLER (ÖZET)
        $stats['bekleyen_onaylar'] = Iaa::whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])
            ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
            ->count();
        $stats['bekleyen_sikayetler'] = MusteriSikayeti::whereIn('musteri_durum', ['Yeni', 'İşlemde'])
            ->when($bolumId, function($q) use ($bolumId) {
                $q->whereHas('sikayetKategori', fn($sq) => $sq->where('bolum_id', $bolumId));
            })
            ->count();
        $stats['bekleyen_arabuluculuk'] = \App\Models\ArabuluculukCase::where('status', '!=', 'kapatildi')
            ->when($bolumId, function($q) use ($bolumId) {
                $q->whereHas('calisan', fn($sq) => $sq->where('bolum_id', $bolumId));
            })
            ->count();

        // 6. GÜNLÜK LOGİN İSTATİSTİKLERİ
        $stats['login_today'] = \App\Models\LoginActivity::whereDate('created_at', today())
            ->when($bolumId, fn($q) => $q->whereHas('user', fn($sq) => $sq->where('bolum_id', $bolumId)))
            ->distinct('user_id')->count();

        $stats['login_trend'] = [
            'dates' => collect(range(6, 0))->map(fn($i) => today()->subDays($i)->format('d M')),
            'counts' => collect(range(6, 0))->map(fn($i) => \App\Models\LoginActivity::whereDate('created_at', today()->subDays($i))
                ->when($bolumId, fn($q) => $q->whereHas('user', fn($sq) => $sq->where('bolum_id', $bolumId)))
                ->distinct('user_id')->count())
        ];

        // 7. AKTİF LİSTELER
        $stats['iaa'] = [
            'active_list' => Iaa::whereDoesntHave('musteriSikayeti')
                ->whereNotIn('durum', ['Tamamlandı', 'Reddedildi', 'İptal Edildi', 'Taslak'])
                ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
                ->when($start, fn($q) => $q->where('created_at', '>=', $start))
                ->when($end, fn($q) => $q->where('created_at', '<=', $end))
                ->with(['bolum', 'atananTakim.lider'])
                ->latest('updated_at')
                ->take(5)
                ->get()
        ];

        $stats['sikayetler'] = [
            'active_list' => MusteriSikayeti::where('musteri_durum', '!=', 'Kapatıldı')
                ->when($bolumId, function($q) use ($bolumId) {
                    $q->whereHas('sikayetKategori', fn($sq) => $sq->where('bolum_id', $bolumId));
                })
                ->when($start, fn($q) => $q->where('created_at', '>=', $start))
                ->when($end, fn($q) => $q->where('created_at', '<=', $end))
                ->with(['customer', 'sikayetKategori.bolum'])
                ->latest()
                ->take(5)
                ->get()
        ];

        // 7.5. DİSİPLİN VE ARABULUCULUK LİSTELERİ
        $stats['disiplin'] = [
            'active_list' => \App\Models\DisciplinaryCase::whereNotIn('durum', ['Karar Verildi', 'İptal Edildi', 'Kapandı'])
                ->when($bolumId, function($q) use ($bolumId) {
                    $q->whereHas('user', fn($sq) => $sq->where('bolum_id', $bolumId));
                })
                ->when($start, fn($q) => $q->where('created_at', '>=', $start))
                ->when($end, fn($q) => $q->where('created_at', '<=', $end))
                ->with(['user.bolum', 'behavior'])
                ->latest()
                ->take(5)
                ->get(),
            'bolum_dagilimi' => \App\Models\DisciplinaryCase::join('users', 'disciplinary_cases.user_id', '=', 'users.id')
                ->join('bolumler', 'users.bolum_id', '=', 'bolumler.id')
                ->whereNull('disciplinary_cases.deleted_at')
                ->whereNull('users.deleted_at')
                ->whereNull('bolumler.deleted_at')
                ->selectRaw('bolumler.ad as bolum_adi, count(*) as toplam')
                ->when($bolumId, fn($q) => $q->where('bolumler.id', $bolumId))
                ->groupBy('bolumler.ad')
                ->get()
        ];

        $stats['arabuluculuk'] = [
            'active_list' => \App\Models\ArabuluculukCase::whereIn('status', ['taslak', 'hukuk_incelemesinde', 'yonetim_onayinda', 'imza_asamasinda', 'odeme_bekliyor', 'arabulucuda'])
                ->when($bolumId, function($q) use ($bolumId) {
                    $q->whereHas('calisan', fn($sq) => $sq->where('bolum_id', $bolumId));
                })
                ->when($start, fn($q) => $q->where('created_at', '>=', $start))
                ->when($end, fn($q) => $q->where('created_at', '<=', $end))
                ->with(['calisan.bolum'])
                ->latest()
                ->take(5)
                ->get()
        ];

        // 8. BEKLEYEN İŞLER (ÖZET LİSTE)
        $waitingTasks = collect();
        Iaa::whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])
            ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
            ->with(['bolum', 'gonderen'])
            ->take(5)->get()->each(function ($iaa) use ($waitingTasks) {
                $waitingTasks->push([
                    'type' => 'İAA',
                    'subject' => $iaa->baslik,
                    'waiting_person' => $iaa->gonderen->name ?? 'Bilinmiyor',
                    'waiting_dept' => $iaa->bolum->ad ?? '-',
                    'status' => $iaa->durum,
                    'days' => $iaa->created_at->diffInDays(now()),
                    'link' => route('proje.workspace.show', $iaa->id)
                ]);
            });

        MusteriSikayeti::whereIn('musteri_durum', ['Yeni', 'İşlemde'])
            ->when($bolumId, function($q) use ($bolumId) {
                $q->whereHas('sikayetKategori', fn($sq) => $sq->where('bolum_id', $bolumId));
            })
            ->with(['customer', 'sikayetKategori.bolum'])
            ->take(5)->get()->each(function ($s) use ($waitingTasks) {
                $waitingTasks->push([
                    'type' => 'Müşteri Şikayeti',
                    'subject' => $s->musteri_sikayet_konusu,
                    'waiting_person' => $s->customer->name ?? 'Müşteri',
                    'waiting_dept' => $s->sikayetKategori->bolum->ad ?? '-',
                    'status' => $s->musteri_durum,
                    'days' => $s->created_at->diffInDays(now()),
                    'link' => route('admin.sikayetler.show', $s->id)
                ]);
            });
        $stats['waiting_tasks'] = $waitingTasks->sortByDesc('days');

        // 9. MÜŞTERİ VERİLERİ (İadeler vb.)
        $stats['musteriler'] = [
            'en_cok_sikayet' => \App\Models\Customer::withCount(['sikayetler' => function($q) use ($start, $end) {
                    $q->when($start, fn($sq) => $sq->where('created_at', '>=', $start))
                      ->when($end, fn($sq) => $sq->where('created_at', '<=', $end));
                }])
                ->orderByDesc('sikayetler_count')->take(5)->get(),
            'iadeler_bolum_bazli' => \App\Models\SikayetIadesi::join('musteri_sikayetleri', 'sikayet_iadeleri.musteri_sikayeti_id', '=', 'musteri_sikayetleri.id')
                ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
                ->selectRaw('bolumler.ad as bolum_adi, sikayet_iadeleri.birim, SUM(sikayet_iadeleri.miktar) as toplam_miktar')
                ->when($bolumId, fn($q) => $q->where('bolumler.id', $bolumId))
                ->when($start, fn($q) => $q->where('sikayet_iadeleri.iade_tarihi', '>=', $start))
                ->when($end, fn($q) => $q->where('sikayet_iadeleri.iade_tarihi', '<=', $end))
                ->groupBy('bolumler.ad', 'sikayet_iadeleri.birim')
                ->get()->groupBy('bolum_adi')
        ];

        $stats['online_users_list'] = User::where('last_seen_at', '>=', Carbon::now()->subMinutes(5))
            ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
            ->with(['bolum', 'loginActivities'])->orderBy('last_seen_at', 'desc')->get();

        $stats['last_active_users'] = User::where('last_seen_at', '<', Carbon::now()->subMinutes(5))
            ->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))
            ->with(['bolum', 'loginActivities'])->orderBy('last_seen_at', 'desc')->take(10)->get();

        return $stats;
    }
}
