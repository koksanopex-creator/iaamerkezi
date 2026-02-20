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

    public function verileriTopla(array $icerikAyarlari)
    {
        $data = [];

        // --- 1. MÜŞTERİ ŞİKAYETLERİ ---
        if (!empty($icerikAyarlari['sikayet_ozet'])) {
            $data['sikayet_genel'] = [
                'toplam_kayit' => MusteriSikayeti::count(),
                'bekleyen_yeni' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
                'islemde_olan' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
                'cozumlenen' => MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
                'iptal' => MusteriSikayeti::where('musteri_durum', 'İptal')->count(),
            ];

            // $this->... kullanarak sınıf özelliklerine erişiyoruz
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
            $data['sikayet_bolumler'] = MusteriSikayeti::join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->select(
                    'sikayet_kategorileri.ad as kategori_adi',
                    DB::raw('count(*) as toplam'),
                    DB::raw("sum(case when musteri_durum = 'Yeni' then 1 else 0 end) as yeni"),
                    DB::raw("sum(case when musteri_durum = 'İşlemde' then 1 else 0 end) as islemde"),
                    DB::raw("sum(case when musteri_durum IN ('Kapatıldı', 'Çözümlendi') then 1 else 0 end) as kapali")
                )
                ->groupBy('sikayet_kategorileri.ad')
                ->orderByDesc('toplam')
                ->get()
                ->toArray();
        }

        // --- 2. İAA SİSTEMİ ---
        if (!empty($icerikAyarlari['iaa_ozet'])) {

            // FİLTRE: Müşteri Şikayetinden gelenleri hariç tut
            $sikayetIaaIds = MusteriSikayeti::whereNotNull('iaa_id')->pluck('iaa_id')->toArray();
            $iaaQuery = Iaa::whereNotIn('id', $sikayetIaaIds);

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

            $data['iaa_durum_detay'] = Iaa::whereNotIn('id', $sikayetIaaIds)
                ->select('durum', DB::raw('count(*) as sayi'))
                ->groupBy('durum')
                ->orderByDesc('sayi')
                ->pluck('sayi', 'durum')
                ->toArray();

            // En Çok Öneri Veren Bölüm
            $enCokBolum = DB::table('iaas')
                ->whereNotIn('iaas.id', $sikayetIaaIds)
                ->whereNotNull('gonderen_user_id')
                ->join('users', 'iaas.gonderen_user_id', '=', 'users.id')
                ->join('bolumler', 'users.bolum_id', '=', 'bolumler.id')
                ->select('bolumler.ad', DB::raw('count(*) as toplam'))
                ->groupBy('bolumler.ad')
                ->orderByDesc('toplam')
                ->first();
            $data['iaa_en_cok_bolum'] = $enCokBolum ? $enCokBolum->ad . " (" . $enCokBolum->toplam . ")" : '-';

            // En Çok Çözen Takım
            $enCokTakim = DB::table('iaa_talepleri')
                ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                ->whereNotIn('iaas.id', $sikayetIaaIds)
                ->join('takimlar', 'iaa_talepleri.takim_id', '=', 'takimlar.id')
                ->where('iaa_talepleri.status', 'Tamamlandı')
                ->select('takimlar.ad', DB::raw('count(*) as toplam'))
                ->groupBy('takimlar.ad')
                ->orderByDesc('toplam')
                ->first();
            $data['iaa_en_cok_takim'] = $enCokTakim ? $enCokTakim->ad . " (" . $enCokTakim->toplam . ")" : '-';

            // Son Gelen Öneri
            $sonOneri = Iaa::whereNotIn('id', $sikayetIaaIds)->latest()->first();
            $data['iaa_son'] = $sonOneri ? [
                'tarih' => $sonOneri->created_at->format('d.m.Y'),
                'baslik' => $sonOneri->baslik,
                'tur' => $sonOneri->gonderen_user_id ? 'Personel' : 'Misafir'
            ] : null;

            // Hız
            $avgDays = Iaa::whereNotIn('id', $sikayetIaaIds)
                ->whereIn('durum', ['Tamamlandı', 'Talep Olarak Kapatıldı'])
                ->select(DB::raw('AVG(DATEDIFF(updated_at, created_at)) as gun'))
                ->value('gun');
            $data['iaa_hiz'] = $avgDays ? round($avgDays, 1) . " Gün" : '-';
        }

        // --- 3. DİSİPLİN & ARABULUCULUK ---
        if (!empty($icerikAyarlari['disiplin_ozet'])) {
            $data['disiplin'] = [
                'acik' => DisciplinaryCase::whereNotIn('durum', ['dosya_kapatildi', 'reddedildi'])->count(),
                'savunma' => DisciplinaryCase::where('durum', 'savunma_bekleniyor')->count(),
                'bu_ay' => DisciplinaryCase::where('created_at', '>=', $this->buAyBasi)->count(),
            ];
        }

        if (!empty($icerikAyarlari['arabuluculuk_ozet'])) {
            $data['arabuluculuk'] = [
                'aktif' => ArabuluculukCase::where('status', '!=', 'kapatildi')->count(),
                'odeme' => ArabuluculukCase::where('status', 'odeme_bekliyor')->count(),
                // 'toplanti' => ArabuluculukCase::where('toplanti_tarihi', '>=', now())->where('toplanti_tarihi', '<=', now()->addDays(7))->count(),
            ];
        }

        return $data;
    }

    private function getSikayetStats($start, $end)
    {
        return [
            'gelen' => MusteriSikayeti::whereBetween('created_at', [$start, $end])->count(),
            'kapanan' => MusteriSikayeti::whereIn('musteri_durum', ['Kapatıldı', 'Çözümlendi'])
                ->whereBetween('updated_at', [$start, $end])->count(),
        ];
    }

    private function getIaaStats($start, $end, $excludeIds = [])
    {
        return [
            'gelen' => Iaa::whereNotIn('id', $excludeIds)
                ->whereBetween('created_at', [$start, $end])->count(),
            'biten' => Iaa::whereNotIn('id', $excludeIds)
                ->whereIn('durum', ['Tamamlandı', 'Talep Olarak Kapatıldı'])
                ->whereBetween('updated_at', [$start, $end])->count(),
        ];
    }
}