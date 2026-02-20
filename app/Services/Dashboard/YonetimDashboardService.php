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
    public function getStats()
    {
        $stats = [];

        // 1. ÜST KARTLAR (Genel Durum)
        $stats['toplam_calisan'] = User::count();
        $stats['aktif_calisan'] = User::doesntHave('roles', 'and', function ($q) {
            $q->where('name', 'Musteri Temsilcisi');
        })->count(); // Müşteri temsilcisi olmayanlar (Tahmini) - Rol yapısına göre düzenlenebilir.

        // Daha kesin bir aktif çalışan sayısı:
        // Şirket içi rollerden birine sahip olanlar veya is_personnel = true olanlar (Eğer users tablosunda flag varsa)
        // Şimdilik User::count() kullanılmış, biz de sadık kalalım ama role check ile iyileştirebiliriz.

        $stats['toplam_proje'] = Iaa::count();
        $stats['tamamlanan_proje'] = Iaa::where('durum', 'Tamamlandı')->count();
        $stats['devam_eden_proje'] = Iaa::whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor'])->count();

        $stats['toplam_sikayet'] = MusteriSikayeti::count();
        $stats['cozulen_sikayet'] = MusteriSikayeti::where('musteri_durum', 'Kapatıldı')->count();

        // 2. DEPARTMAN PERFORMANSLARI (Bölüm Bazlı)
        $bolumler = Bolum::withCount(['users', 'iaas'])->get();
        $bolumPerformanslari = [];

        foreach ($bolumler as $bolum) {
            // Bölümün tamamladığı projeler
            $tamamlanan = Iaa::whereHas('atananTakim.lider', function ($q) use ($bolum) {
                $q->where('bolum_id', $bolum->id);
            })->where('durum', 'Tamamlandı')->count();

            // Bölümün sorumlu olduğu aktif şikayetler
            // (Şikayet kategorisi yönetimi üzerinden veya takımlar üzerinden bakılabilir)
            // Basitçe o bölüme bağlı takımların üzerindeki şikayetler:
            $aktifSikayet = MusteriSikayeti::whereHas('cozumTakimi.lider', function ($q) use ($bolum) {
                $q->where('bolum_id', $bolum->id);
            })->where('musteri_durum', '!=', 'Kapatıldı')->count();

            $bolumPerformanslari[] = [
                'ad' => $bolum->ad,
                'personel_sayisi' => $bolum->users_count,
                'toplam_proje' => $bolum->iaas_count, // İlişki tanımlıysa
                'tamamlanan' => $tamamlanan,
                'aktif_sikayet' => $aktifSikayet,
                'basari_orani' => $bolum->iaas_count > 0 ? round(($tamamlanan / $bolum->iaas_count) * 100) : 0
            ];
        }
        $stats['bolum_performanslari'] = collect($bolumPerformanslari)->sortByDesc('basari_orani')->values();

        // 3. EN AKTİF PERSONELLER (Son 30 Gün)
        // Puanı en yüksek olanlar veya en çok iş bitirenler
        $stats['en_aktif_personeller'] = User::withCount([
            'gorevliOlduguProjeler' => function ($q) {
                $q->where('iaas.durum', 'Tamamlandı');
            }
        ])
            ->orderByDesc('gorevli_oldugu_projeler_count')
            ->take(5)
            ->get();

        // 4. SON 12 AYLIK PROJE GRAFİĞİ VERİLERİ
        $aylar = [];
        $projeVerileri = [];
        $sikayetVerileri = [];

        for ($i = 11; $i >= 0; $i--) {
            $tarih = Carbon::now()->subMonths($i);
            $aylar[] = $tarih->format('M Y');

            $projeVerileri[] = Iaa::whereYear('created_at', $tarih->year)
                ->whereMonth('created_at', $tarih->month)
                ->whereDoesntHave('musteriSikayeti')
                ->count();

            $sikayetVerileri[] = MusteriSikayeti::whereYear('created_at', $tarih->year)
                ->whereMonth('created_at', $tarih->month)
                ->count();
        }

        $stats['grafik'] = [
            'aylar' => $aylar,
            'projeler' => $projeVerileri,
            'sikayetler' => $sikayetVerileri
        ];

        // 5. BEKLEYEN KRİTİK İŞLER (ÖZET)
        $stats['bekleyen_onaylar'] = Iaa::whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])->count();
        $stats['bekleyen_sikayetler'] = MusteriSikayeti::whereIn('musteri_durum', ['Yeni', 'İşlemde'])->count();
        $stats['bekleyen_arabuluculuk'] = \App\Models\ArabuluculukCase::where('status', '!=', 'kapatildi')->count();

        // 6. GÜNLÜK LOGİN İSTATİSTİKLERİ (LoginActivity Modelinden)
        // Bugün, Dün, Bu Hafta, Bu Ay
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // Bugün giriş yapan tekil kullanıcı sayısı
        $stats['login_today'] = \App\Models\LoginActivity::whereDate('created_at', $today)->distinct('user_id')->count();

        // Dün giriş yapan tekil kullanıcı sayısı
        $stats['login_yesterday'] = \App\Models\LoginActivity::whereDate('created_at', $yesterday)->distinct('user_id')->count();

        // Bu ay (tekil)
        $stats['login_month'] = \App\Models\LoginActivity::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->distinct('user_id')
            ->count();

        // Login Trendi (Son 7 Gün)
        $loginTrendDates = [];
        $loginTrendCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i)->toDateString();
            $loginTrendDates[] = Carbon::parse($d)->format('d M');
            $loginTrendCounts[] = \App\Models\LoginActivity::whereDate('created_at', $d)->distinct('user_id')->count();
        }

        $stats['login_trend'] = [
            'dates' => $loginTrendDates,
            'counts' => $loginTrendCounts
        ];

        // 7. ONLINE VE SON AKTİF KULLANICILAR (View beklentisi için)
        $stats['online_users_list'] = User::where('last_seen_at', '>=', Carbon::now()->subMinutes(5))
            ->with(['bolum', 'loginActivities'])
            ->orderBy('last_seen_at', 'desc')
            ->get();

        $stats['last_active_users'] = User::where('last_seen_at', '<', Carbon::now()->subMinutes(5))
            ->with(['bolum', 'loginActivities'])
            ->orderBy('last_seen_at', 'desc')
            ->take(10)
            ->get();

        // 8. AKTİF LİSTELER (İAA, Şikayet, Disiplin, Arabuluculuk)
        $stats['iaa'] = [
            'active_list' => Iaa::whereNotIn('durum', ['Tamamlandı', 'Reddedildi', 'İptal Edildi', 'Taslak'])
                ->with(['bolum', 'atananTakim.lider'])
                ->latest('updated_at')
                ->take(5)
                ->get()
        ];

        $stats['sikayetler'] = [
            'active_list' => MusteriSikayeti::where('musteri_durum', '!=', 'Kapatıldı')
                ->with(['customer', 'sikayetKategori.bolum'])
                ->latest()
                ->take(5)
                ->get()
        ];

        $stats['disiplin'] = [
            'active_list' => \App\Models\DisciplinaryCase::with(['user.bolum', 'behavior'])
                ->whereNotIn('durum', ['Karar Verildi', 'İptal Edildi'])
                ->latest()
                ->take(5)
                ->get(),
            'bolum_dagilimi' => \App\Models\DisciplinaryCase::join('users', 'disciplinary_cases.user_id', '=', 'users.id')
                ->join('bolumler', 'users.bolum_id', '=', 'bolumler.id')
                ->selectRaw('bolumler.ad as bolum_adi, count(*) as toplam')
                ->whereNull('disciplinary_cases.deleted_at')
                ->groupBy('bolumler.ad')
                ->get()
        ];

        $stats['arabuluculuk'] = [
            'active_list' => \App\Models\ArabuluculukCase::with(['calisan'])
                ->where('status', '!=', 'kapatildi')
                ->latest()
                ->take(5)
                ->get()
        ];

        // 9. BEKLEYEN İŞLER (ÖZET LİSTE)
        $waitingTasks = collect();

        // İAA Bekleyenler
        Iaa::whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])
            ->with(['bolum', 'gonderen'])
            ->take(3)
            ->get()
            ->each(function ($iaa) use ($waitingTasks) {
                $waitingTasks->push([
                    'type' => 'İAA',
                    'subject' => $iaa->baslik,
                    'waiting_person' => $iaa->gonderen ? $iaa->gonderen->name : 'Bilinmiyor',
                    'waiting_dept' => $iaa->bolum ? $iaa->bolum->ad : '-',
                    'status' => $iaa->durum,
                    'days' => $iaa->created_at->diffInDays(now()),
                    'link' => route('proje.workspace.show', $iaa->id)
                ]);
            });

        // Şikayet Bekleyenler
        MusteriSikayeti::whereIn('musteri_durum', ['Yeni', 'İşlemde'])
            ->with(['customer', 'sikayetKategori.bolum'])
            ->take(3)
            ->get()
            ->each(function ($s) use ($waitingTasks) {
                $waitingTasks->push([
                    'type' => 'Müşteri Şikayeti',
                    'subject' => $s->musteri_sikayet_konusu,
                    'waiting_person' => $s->customer ? $s->customer->name : 'Müşteri',
                    'waiting_dept' => $s->sikayetKategori && $s->sikayetKategori->bolum ? $s->sikayetKategori->bolum->ad : '-',
                    'status' => $s->iaaProjesi && in_array($s->iaaProjesi->durum, ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Talep Olarak Kapatıldı']) ? $s->iaaProjesi->durum_etiketi : $s->musteri_durum,
                    'days' => $s->created_at->diffInDays(now()),
                    'link' => route('admin.sikayetler.show', $s->id)
                ]);
            });

        $stats['waiting_tasks'] = $waitingTasks->sortByDesc('days');

        // 10. MÜŞTERİ VERİLERİ
        $stats['musteriler'] = [
            'en_cok_sikayet' => \App\Models\Customer::withCount('sikayetler')
                ->orderByDesc('sikayetler_count')
                ->take(5)
                ->get(),
            'iadeler_bolum_bazli' => \App\Models\SikayetIadesi::join('musteri_sikayetleri', 'sikayet_iadeleri.musteri_sikayeti_id', '=', 'musteri_sikayetleri.id')
                ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
                ->selectRaw('bolumler.ad as bolum_adi, sikayet_iadeleri.birim, SUM(sikayet_iadeleri.miktar) as toplam_miktar')
                ->groupBy('bolumler.ad', 'sikayet_iadeleri.birim')
                ->get()
                ->groupBy('bolum_adi')
        ];

        return $stats;
    }
}
