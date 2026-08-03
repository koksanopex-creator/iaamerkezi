<?php

namespace App\Services;

use App\Models\MusteriSikayeti;
use App\Models\Iaa;
use App\Models\DisciplinaryCase;
use App\Models\ArabuluculukCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RaporVeriServisi
{
    protected $user = null;
    protected $disiplinKategoriFiltresi = [];

    // Tarih değişkenlerini sınıf özelliği olarak tanımlıyoruz (Scope hatasını önler)
    protected $bugun;
    protected $buHaftaBasi;
    protected $gecenHaftaBasi;
    protected $gecenHaftaSonu;
    protected $buAyBasi;
    protected $gecenAyBasi;
    protected $gecenAySonu;
    protected $buYilBasi;
    protected $gecenYilBasi;
    protected $gecenYilSonu;

    public function __construct()
    {
        // Tarihleri bir kez hesapla
        $this->bugun = Carbon::today();
        $this->buHaftaBasi = Carbon::now()->startOfWeek();
        $this->gecenHaftaBasi = Carbon::now()->subWeek()->startOfWeek();
        $this->gecenHaftaSonu = Carbon::now()->subWeek()->endOfWeek();

        $this->buAyBasi = Carbon::now()->startOfMonth();
        $this->gecenAyBasi = Carbon::now()->subMonth()->startOfMonth();
        $this->gecenAySonu = Carbon::now()->subMonth()->endOfMonth();

        $this->buYilBasi = Carbon::now()->startOfYear();
        $this->gecenYilBasi = Carbon::now()->subYear()->startOfYear();
        $this->gecenYilSonu = Carbon::now()->subYear()->endOfYear();
    }

    /**
     * Filtreleme için kullanıcıyı set eder.
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Disiplin raporu için suç kategorisi (DisciplinaryCategory id) filtresini set eder.
     */
    public function setDisiplinKategoriFiltresi(array $kategoriler)
    {
        $this->disiplinKategoriFiltresi = $kategoriler;
        return $this;
    }

    /**
     * Kullanıcının kısıtlamaya tabi olup olmadığını kontrol eder.
     */
    protected function shouldFilter()
    {
        return $this->user && !$this->user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu']);
    }

    /**
     * MusteriSikayeti sorgusuna yetki bazlı filtre ekler.
     */
    protected function applySikayetScope($query)
    {
        if ($this->shouldFilter()) {
            $allowedBolumIds = $this->user->getAllowedBolumIds();
            $query->where(function($q) use ($allowedBolumIds) {
                // 1. Şikayet kategorisi kendi bölümüne ait
                if ($allowedBolumIds !== '*') {
                    $q->whereHas('sikayetKategori', fn($k) => $k->whereIn('bolum_id', $allowedBolumIds));
                }
                // 2. Kendi bölümünden bir personel takımda/projede
                if ($this->user->bolum_id) {
                    $q->orWhereHas('cozumTakimi.uyeler', fn($u) => $u->where('users.bolum_id', $this->user->bolum_id))
                      ->orWhereHas('iaa.projeEkibi', fn($u) => $u->where('users.bolum_id', $this->user->bolum_id));
                }
            });
        }
        return $query;
    }

    /**
     * Iaa sorgusuna yetki bazlı filtre ekler.
     */
    protected function applyIaaScope($query)
    {
        if ($this->shouldFilter()) {
            $query->where(function($q) {
                // Kendi bölümünün projesi
                if ($this->user->bolum_id) {
                    $q->where('bolum_id', $this->user->bolum_id)
                      // Veya personelinin dahil olduğu proje
                      ->orWhereHas('projeEkibi', fn($u) => $u->where('users.bolum_id', $this->user->bolum_id));
                }
            });
        }
        return $query;
    }

    public function verileriTopla(array $icerikAyarlari)
    {
        $data = [];

        // --- 1. MÜŞTERİ ŞİKAYETLERİ ---
        if (!empty($icerikAyarlari['sikayet_ozet'])) {
            $baseSikayetQuery = $this->applySikayetScope(MusteriSikayeti::query());

            $data['sikayet_genel'] = [
                'toplam_kayit' => (clone $baseSikayetQuery)->count(),
                'bekleyen_yeni' => (clone $baseSikayetQuery)->where('musteri_durum', 'Yeni')->count(),
                'islemde_olan' => (clone $baseSikayetQuery)->where('musteri_durum', 'İşlemde')->count(),
                'cozumlenen' => (clone $baseSikayetQuery)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
                'iptal' => (clone $baseSikayetQuery)->where('musteri_durum', 'İptal')->count(),
            ];

            $data['sikayet_zaman'] = [
                'bugun' => $this->getSikayetStats(Carbon::today(), Carbon::now()),
                'bu_hafta' => $this->getSikayetStats($this->buHaftaBasi, Carbon::now()),
                'gecen_hafta' => $this->getSikayetStats($this->gecenHaftaBasi, $this->gecenHaftaSonu),
                'bu_ay' => $this->getSikayetStats($this->buAyBasi, Carbon::now()),
                'gecen_ay' => $this->getSikayetStats($this->gecenAyBasi, $this->gecenAySonu),
                'bu_yil' => $this->getSikayetStats($this->buYilBasi, Carbon::now()),
                'gecen_yil' => $this->getSikayetStats($this->gecenYilBasi, $this->gecenYilSonu),
            ];

            $data['sikayet_ceyrekler'] = [
                'Q1' => $this->getSikayetStats(Carbon::create(null, 1, 1), Carbon::create(null, 3, 31)),
                'Q2' => $this->getSikayetStats(Carbon::create(null, 4, 1), Carbon::create(null, 6, 30)),
                'Q3' => $this->getSikayetStats(Carbon::create(null, 7, 1), Carbon::create(null, 9, 30)),
                'Q4' => $this->getSikayetStats(Carbon::create(null, 10, 1), Carbon::create(null, 12, 31)),
            ];
        }

        if (!empty($icerikAyarlari['sikayet_detay'])) {
            $detayQuery = $this->applySikayetScope(MusteriSikayeti::query())
                ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->select(
                    'sikayet_kategorileri.bolum_id',
                    'sikayet_kategorileri.ad as kategori_adi',
                    DB::raw('count(*) as toplam'),
                    DB::raw("sum(case when musteri_durum = 'Yeni' then 1 else 0 end) as yeni"),
                    DB::raw("sum(case when musteri_durum = 'İşlemde' then 1 else 0 end) as islemde"),
                    DB::raw("sum(case when musteri_durum IN ('Kapatıldı', 'Çözümlendi') then 1 else 0 end) as kapali")
                )
                ->groupBy('sikayet_kategorileri.bolum_id', 'sikayet_kategorileri.ad')
                ->orderByDesc('toplam');

            $data['sikayet_bolumler'] = $detayQuery->get()->toArray();
        }

        // --- 2. İAA SİSTEMİ ---
        if (!empty($icerikAyarlari['iaa_ozet'])) {

            // FİLTRE: Müşteri Şikayetinden gelenleri hariç tut
            $sikayetIaaIds = MusteriSikayeti::whereNotNull('iaa_id')->pluck('iaa_id')->toArray();
            $iaaQuery = $this->applyIaaScope(Iaa::whereNotIn('id', $sikayetIaaIds));

            $data['iaa_ozet'] = [
                'toplam' => (clone $iaaQuery)->count(),
                'havuz' => (clone $iaaQuery)->where('durum', 'Havuzda')->count(),
                'devam' => (clone $iaaQuery)->whereNotIn('durum', ['Havuzda', 'Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Talep Olarak Kapatıldı'])->count(),
                'genel_tamamlanan' => (clone $iaaQuery)->whereIn('durum', ['Tamamlandı', 'Talep Olarak Kapatıldı'])->count(),

                'bu_yil_biten' => (clone $iaaQuery)->whereIn('durum', ['Tamamlandı', 'Talep Olarak Kapatıldı'])
                    ->where('updated_at', '>=', $this->buYilBasi)->count(),

                'bu_ay_biten' => (clone $iaaQuery)->whereIn('durum', ['Tamamlandı', 'Talep Olarak Kapatıldı'])
                    ->where('updated_at', '>=', $this->buAyBasi)->count(),
            ];

            $data['iaa_zaman'] = [
                'bu_hafta' => $this->getIaaStats($this->buHaftaBasi, Carbon::now(), $sikayetIaaIds),
                'gecen_hafta' => $this->getIaaStats($this->gecenHaftaBasi, $this->gecenHaftaSonu, $sikayetIaaIds),
                'bu_ay' => $this->getIaaStats($this->buAyBasi, Carbon::now(), $sikayetIaaIds),
                'gecen_ay' => $this->getIaaStats($this->gecenAyBasi, $this->gecenAySonu, $sikayetIaaIds),
                'bu_yil' => $this->getIaaStats($this->buYilBasi, Carbon::now(), $sikayetIaaIds),
                'gecen_yil' => $this->getIaaStats($this->gecenYilBasi, $this->gecenYilSonu, $sikayetIaaIds),
            ];

            $data['iaa_durum_detay'] = (clone $iaaQuery)
                ->select('durum', DB::raw('count(*) as sayi'))
                ->groupBy('durum')
                ->orderByDesc('sayi')
                ->pluck('sayi', 'durum')
                ->toArray();

            // En Çok Öneri Veren Bölüm (Scope buraya da uygulanmalı)
            $enCokBolumQuery = DB::table('iaas')
                ->whereNotIn('iaas.id', $sikayetIaaIds)
                ->whereNotNull('gonderen_user_id')
                ->join('users', 'iaas.gonderen_user_id', '=', 'users.id')
                ->join('bolumler', 'users.bolum_id', '=', 'bolumler.id');

            if ($this->shouldFilter()) {
                $enCokBolumQuery->where('iaas.bolum_id', $this->user->bolum_id);
            }

            $enCokBolum = $enCokBolumQuery->select('bolumler.ad', DB::raw('count(*) as toplam'))
                ->groupBy('bolumler.ad')
                ->orderByDesc('toplam')
                ->first();
            $data['iaa_en_cok_bolum'] = $enCokBolum ? $enCokBolum->ad . " (" . $enCokBolum->toplam . ")" : '-';

            // En Çok Çözen Takım
            $enCokTakimQuery = DB::table('iaa_talepleri')
                ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                ->whereNotIn('iaas.id', $sikayetIaaIds)
                ->join('takimlar', 'iaa_talepleri.takim_id', '=', 'takimlar.id')
                ->where('iaa_talepleri.status', 'Tamamlandı');

            if ($this->shouldFilter()) {
                $enCokTakimQuery->where('iaas.bolum_id', $this->user->bolum_id);
            }

            $enCokTakim = $enCokTakimQuery->select('takimlar.ad', DB::raw('count(*) as toplam'))
                ->groupBy('takimlar.ad')
                ->orderByDesc('toplam')
                ->first();
            $data['iaa_en_cok_takim'] = $enCokTakim ? $enCokTakim->ad . " (" . $enCokTakim->toplam . ")" : '-';

            // Son Gelen Öneri
            $sonOneri = (clone $iaaQuery)->latest()->first();
            $data['iaa_son'] = $sonOneri ? [
                'tarih' => $sonOneri->created_at->format('d.m.Y'),
                'baslik' => $sonOneri->baslik,
                'tur' => $sonOneri->gonderen_user_id ? 'Personel' : 'Misafir'
            ] : null;

            // Hız
            $avgDays = (clone $iaaQuery)
                ->whereIn('durum', ['Tamamlandı', 'Talep Olarak Kapatıldı'])
                ->select(DB::raw('AVG(DATEDIFF(updated_at, created_at)) as gun'))
                ->value('gun');
            $data['iaa_hiz'] = $avgDays ? round($avgDays, 1) . " Gün" : '-';
        }

        // --- 3. DİSİPLİN & ARABULUCULUK ---
        if (!empty($icerikAyarlari['disiplin_ozet'])) {
            $disiplinQuery = DisciplinaryCase::query()->with(['user.bolum', 'behavior.category']);
            if ($this->shouldFilter()) {
                $allowedBolumIds = $this->user->getAllowedBolumIds();
                if ($allowedBolumIds !== '*') {
                    if (empty($allowedBolumIds)) {
                        $disiplinQuery->whereRaw('1 = 0'); // No access to any department
                    } else {
                        $disiplinQuery->whereHas('user', fn($u) => $u->whereIn('bolum_id', $allowedBolumIds));
                    }
                }
            }
            
            if (!empty($this->disiplinKategoriFiltresi)) {
                $disiplinQuery->whereHas('behavior', function($q) {
                    $q->whereIn('category_id', $this->disiplinKategoriFiltresi);
                });
            }

            $cases = $disiplinQuery->get();

            $data['disiplin'] = [
                'genel' => [
                    'tum' => $this->getDisiplinStats($cases),
                    'bu_yil' => $this->getDisiplinStats($cases, $this->buYilBasi),
                    'bu_ay' => $this->getDisiplinStats($cases, $this->buAyBasi),
                    'bu_hafta' => $this->getDisiplinStats($cases, $this->buHaftaBasi),
                ],
                'ceyrekler' => [
                    'Q1' => $this->getDisiplinStats($cases, Carbon::now()->startOfYear(), Carbon::create(null, 3, 31)->endOfDay()),
                    'Q2' => $this->getDisiplinStats($cases, Carbon::create(null, 4, 1)->startOfDay(), Carbon::create(null, 6, 30)->endOfDay()),
                    'Q3' => $this->getDisiplinStats($cases, Carbon::create(null, 7, 1)->startOfDay(), Carbon::create(null, 9, 30)->endOfDay()),
                    'Q4' => $this->getDisiplinStats($cases, Carbon::create(null, 10, 1)->startOfDay(), Carbon::now()->endOfYear()),
                ],
                'bolumler' => [],
                'kategoriler' => [],
                'yakalar' => [],
            ];

            // Bölümlere göre gruplama
            $bolumler = $cases->groupBy(function ($case) {
                return optional(optional($case->user)->bolum)->ad ?? 'Belirtilmemiş';
            });
            foreach ($bolumler as $bolumAd => $bCases) {
                $data['disiplin']['bolumler'][] = [
                    'ad' => $bolumAd,
                    'tum' => $this->getDisiplinStats($bCases),
                    'bu_yil' => $this->getDisiplinStats($bCases, $this->buYilBasi),
                    'bu_ay' => $this->getDisiplinStats($bCases, $this->buAyBasi),
                    'bu_hafta' => $this->getDisiplinStats($bCases, $this->buHaftaBasi),
                ];
            }
            // En çok dosya olana göre sırala
            usort($data['disiplin']['bolumler'], fn($a, $b) => $b['tum']['toplam'] <=> $a['tum']['toplam']);

            // Kategorilere göre gruplama
            $kategoriler = $cases->groupBy(function ($case) {
                return $case->settings_snapshot['category_ad'] ?? optional(optional($case->behavior)->category)->ad ?? 'Diğer';
            });
            foreach ($kategoriler as $katAd => $kCases) {
                $data['disiplin']['kategoriler'][] = [
                    'ad' => $katAd,
                    'tum' => $this->getDisiplinStats($kCases),
                    'bu_yil' => $this->getDisiplinStats($kCases, $this->buYilBasi),
                    'bu_ay' => $this->getDisiplinStats($kCases, $this->buAyBasi),
                    'bu_hafta' => $this->getDisiplinStats($kCases, $this->buHaftaBasi),
                ];
            }
            usort($data['disiplin']['kategoriler'], fn($a, $b) => $b['tum']['toplam'] <=> $a['tum']['toplam']);

            // Yakaya göre gruplama (Mavi/Beyaz)
            $yakalar = $cases->groupBy(function ($case) {
                if (!$case->user) return 'Bilinmiyor';
                return $case->user->is_mavi_yaka ? 'Mavi Yaka' : 'Beyaz Yaka';
            });
            foreach ($yakalar as $yakaAd => $yCases) {
                $data['disiplin']['yakalar'][] = [
                    'ad' => $yakaAd,
                    'tum' => $this->getDisiplinStats($yCases),
                    'bu_yil' => $this->getDisiplinStats($yCases, $this->buYilBasi),
                    'bu_ay' => $this->getDisiplinStats($yCases, $this->buAyBasi),
                    'bu_hafta' => $this->getDisiplinStats($yCases, $this->buHaftaBasi),
                ];
            }
        }

        if (!empty($icerikAyarlari['arabuluculuk_ozet'])) {
            $arabuluculukQuery = ArabuluculukCase::query();
            if ($this->shouldFilter()) {
                $allowedBolumIds = $this->user->getAllowedBolumIds();
                if ($allowedBolumIds !== '*') {
                    if (empty($allowedBolumIds)) {
                        $arabuluculukQuery->whereRaw('1 = 0');
                    } else {
                        $arabuluculukQuery->whereHas('calisan', fn($u) => $u->whereIn('bolum_id', $allowedBolumIds));
                    }
                }
            }

            $data['arabuluculuk'] = [
                'aktif' => (clone $arabuluculukQuery)->where('status', '!=', 'kapatildi')->count(),
                'odeme' => (clone $arabuluculukQuery)->where('status', 'odeme_bekliyor')->count(),
            ];
        }

        return $data;
    }

    private function getSikayetStats($start, $end)
    {
        $queryGelen = $this->applySikayetScope(MusteriSikayeti::whereBetween('created_at', [$start, $end]));
        $queryKapanan = $this->applySikayetScope(MusteriSikayeti::whereIn('musteri_durum', ['Kapatıldı', 'Çözümlendi'])
                ->whereBetween('updated_at', [$start, $end]));

        return [
            'gelen' => $queryGelen->count(),
            'kapanan' => $queryKapanan->count(),
        ];
    }

    private function getIaaStats($start, $end, $excludeIds = [])
    {
        $queryGelen = $this->applyIaaScope(Iaa::whereNotIn('id', $excludeIds)->whereBetween('created_at', [$start, $end]));
        $queryBiten = $this->applyIaaScope(Iaa::whereNotIn('id', $excludeIds)->whereIn('durum', ['Tamamlandı', 'Talep Olarak Kapatıldı'])->whereBetween('updated_at', [$start, $end]));

        return [
            'gelen' => $queryGelen->count(),
            'biten' => $queryBiten->count(),
        ];
    }

    private function getDisiplinStats($cases, $startDate = null, $endDate = null)
    {
        $filtered = $cases;
        if ($startDate) {
            $filtered = $filtered->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $filtered = $filtered->where('created_at', '<=', $endDate);
        }
        
        return [
            'toplam' => $filtered->where('durum', '!=', 'Taslak')->count(),
            'acik' => $filtered->whereNotIn('durum', ['Karar Verildi', 'İptal', 'Taslak'])->count(),
            'savunma' => $filtered->where('durum', 'Savunma Bekleniyor')->count(),
            'yonetici' => $filtered->where('durum', 'Yönetici Değerlendirmesi')->count(),
            'kurul' => $filtered->where('durum', 'Kurulda')->count(),
            'kapali' => $filtered->where('durum', 'Karar Verildi')->count(),
        ];
    }
}