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

class SuperAdminDashboardService
{
    /**
     * Superadmin Dashboard genel istatistiklerini döndürür.
     *
     * @return array
     */
    public function getStats()
    {
        // 1. GENEL İSTATİSTİKLER
        $stats = [
            'toplam_kullanici' => User::count(),
            'onay_bekleyen_kullanici' => User::where('onaylandi_mi', false)->count(),
            'son_kullanicilar' => User::latest()->take(10)->get(),

            // SAF IAA İSTATİSTİKLERİ (Müşteri Şikayeti Olmayanlar & Teknik Statü Hariç)
            'toplam_iaa' => Iaa::sadeceOneriler()->count(),
            'onay_bekleyen_iaa' => Iaa::sadeceOneriler()->where('durum', 'Onay Bekliyor')->count(),
            'atama_bekleyen_iaa' => Iaa::sadeceOneriler()->where('durum', 'Talep Edildi')->count(),
            'son_iaalar' => Iaa::sadeceOneriler()->latest()->take(3)->get(),

            'toplam_bolum' => Bolum::count(),
            'son_bolumler' => Bolum::latest()->take(10)->get(),

            'toplam_takim' => Takim::count(),
            'son_takimlar' => Takim::with('lider')->withCount('uyeler')->latest()->take(10)->get(),

            'toplam_sikayet' => MusteriSikayeti::count(),
            'yeni_sikayet' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
            'islemde_sikayet' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
            // YENİ: Onay Bekleyen Şikayet Sayısı
            'onay_bekleyen_sikayet' => MusteriSikayeti::whereHas('iaaProjesi', function ($q) {
                $q->whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']);
            })->count(),
            'son_sikayetler' => MusteriSikayeti::with('customer')->latest()->take(3)->get(), // Müşteri bilgisini ekledik

            // YENİ EKLEMELER
            'toplam_musteri' => \App\Models\Customer::count(),
            'son_musteriler_listesi' => \App\Models\Customer::latest()->take(3)->get(),
            'toplam_disiplin' => \App\Models\DisciplinaryCase::count(),
            'aktif_disiplin' => \App\Models\DisciplinaryCase::whereNotIn('durum', ['Karar Verildi', 'İptal Edildi'])->count(),
            'toplam_arabuluculuk' => \App\Models\ArabuluculukCase::count(),
            'aktif_arabuluculuk' => \App\Models\ArabuluculukCase::where('status', '!=', 'kapatildi')->count(),
        ];

        return $stats;
    }

    /**
     * Tüm personellerin bekleyen işlerini gösterir (Liste Tasarımı)
     */
    public function getAllPendingWorks(array $filters = [])
    {
        $tur = $filters['tur'] ?? null;
        $bolum = $filters['bolum'] ?? null;
        $durum = $filters['durum'] ?? null;

        $bekleyenIsler = collect();

        // 1. İAA PROJELERİ
        if (!$tur || $tur == 'İAA') {
            $iaas = Iaa::with(['bolum', 'atananTakim.lider'])
                ->whereNotIn('durum', ['Tamamlandı', 'Reddedildi', 'İptal Edildi', 'Taslak', 'talep_olarak_kapatildi']);

            if ($bolum) {
                $iaas->whereHas('bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            if ($durum) {
                $iaas->where('durum', $durum);
            }

            $iaas->get()->each(function ($item) use ($bekleyenIsler) {
                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'İAA',
                    'konu' => $item->baslik,
                    'personel' => $item->atananTakim && $item->atananTakim->lider ? $item->atananTakim->lider->name : ($item->gonderen ? $item->gonderen->name : 'Atanmamış'),
                    'bolum' => $item->bolum ? $item->bolum->ad : '-',
                    'durum' => $item->durum,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => $item->oncelik ?? 'Normal',
                    'link' => route('proje.workspace.show', $item->id)
                ]);
            });
        }

        // 2. MÜŞTERİ ŞİKAYETLERİ
        if (!$tur || $tur == 'Müşteri Şikayeti') {
            $sikayetler = MusteriSikayeti::with(['sikayetKategori.bolum', 'cozumTakimi.lider'])
                ->where('musteri_durum', '!=', 'Kapatıldı');

            if ($bolum) {
                $sikayetler->whereHas('sikayetKategori.bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            if ($durum) {
                $sikayetler->where('musteri_durum', $durum);
            }

            $sikayetler->get()->each(function ($item) use ($bekleyenIsler) {
                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Müşteri Şikayeti',
                    'konu' => $item->musteri_sikayet_konusu,
                    'personel' => $item->cozumTakimi && $item->cozumTakimi->lider ? $item->cozumTakimi->lider->name : ($item->olusturanKurulUyesi ? $item->olusturanKurulUyesi->name : 'Atanmamış'),
                    'bolum' => $item->sikayetKategori && $item->sikayetKategori->bolum ? $item->sikayetKategori->bolum->ad : '-',
                    'durum' => $item->musteri_durum,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => $item->oncelik ?? 'Normal',
                    'link' => route('admin.sikayetler.show', $item->id)
                ]);
            });
        }

        // 3. ARABULUCULUK VAKALARI
        if (!$tur || $tur == 'Arabuluculuk') {
            $arabuluculuk = \App\Models\ArabuluculukCase::with(['calisan.bolum'])
                ->where('status', '!=', 'kapatildi');

            if ($bolum) {
                $arabuluculuk->whereHas('calisan.bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            if ($durum) {
                $arabuluculuk->where('status', $durum);
            }

            $arabuluculuk->get()->each(function ($item) use ($bekleyenIsler) {
                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Arabuluculuk',
                    'konu' => ($item->calisan ? $item->calisan->name : 'Bilinmeyen') . ' - Arabuluculuk Dosyası',
                    'personel' => $item->calisan ? $item->calisan->name : '-',
                    'bolum' => $item->calisan && $item->calisan->bolum ? $item->calisan->bolum->ad : '-',
                    'durum' => $item->status,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => 'Yüksek',
                    'link' => route('admin.arabuluculuk.show', $item->id)
                ]);
            });
        }

        // Dropdown Verileri
        $dropdowns = [
            'bolumler' => Bolum::pluck('ad')->toArray(),
            'turler' => ['İAA', 'Müşteri Şikayeti', 'Arabuluculuk'],
            'durumlar' => array_unique($bekleyenIsler->pluck('durum')->toArray())
        ];

        // İstatistikler
        $stats = [
            'toplam' => $bekleyenIsler->count(),
            'onay_bekleyen' => $bekleyenIsler->whereIn('durum', ['Onay Bekliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Yeni'])->count(),
            'aktif_islemde' => $bekleyenIsler->whereIn('durum', ['Atandı', 'Devam Ediyor', 'İşlemde', 'Arabulucuda'])->count(),
            'arabuluculuk' => $bekleyenIsler->where('tur', 'Arabuluculuk')->count()
        ];

        // Sıralama (En çok bekleyenden en az bekleyene)
        $bekleyenIsler = $bekleyenIsler->sortByDesc('gun');

        return [
            'bekleyenIsler' => $bekleyenIsler,
            'stats' => $stats,
            'dropdowns' => $dropdowns
        ];
    }

    /**
     * Superadmin Dashboard için ekstra detay tablolarını döndürür.
     *
     * @return array
     */
    public function getExtraTables()
    {
        $ekstraTablolar = [];

        // 1. TAKIMLAR (AYRIŞTIRILMIŞ)
        $ekstraTablolar['son_iaa_takimlari'] = Takim::where('tur', '!=', 'sikayet')->with('lider')->latest()->take(10)->get();
        $ekstraTablolar['son_sikayet_takimlari'] = Takim::where('tur', 'sikayet')->with('lider')->latest()->take(10)->get();

        // 2. SON ÇÖZÜLEN ŞİKAYETLER
        $ekstraTablolar['son_cozulen_sikayetler'] = MusteriSikayeti::where('musteri_durum', 'Kapatıldı')->with(['cozumTakimi', 'sikayetKategori', 'customer', 'iaaProjesi'])->latest('updated_at')->take(10)->get();

        // 3. SON TAMAMLANAN IAA (SADECE IAA)
        $ekstraTablolar['son_tamamlanan_iaa'] = Iaa::doesntHave('musteriSikayeti')->where('durum', 'Tamamlandı')->with('atananTakim')->latest('updated_at')->take(10)->get();

        // 4. DİSİPLİN VAKALARI (YENİ)
        $ekstraTablolar['son_disiplin_vakalari'] = \App\Models\DisciplinaryCase::with(['user', 'behavior'])->latest()->take(10)->get();

        // 5. DİĞERLERİ
        $ekstraTablolar['son_yorumlar'] = ProjeYorumu::with('iaa')->latest()->take(10)->get();
        $ekstraTablolar['son_profil_yorumlari'] = ProfileComment::with(['yazan', 'profileUser'])->latest()->take(10)->get();

        // 5. SON KAZANILAN PUANLAR (Global Karma Liste)
        // A) Projelerden (IAA + Şikayet Çözümü)
        $puanliProjeler = Iaa::where('puan', '>', 0)
            ->where('durum', 'Tamamlandı')
            ->with(['atananTakim.lider', 'musteriSikayeti.sikayetKategori'])
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->toBase() // Eloquent Collection -> Base Collection (Array'lerle çalışmak için)
            ->map(fn($p) => [
                'id' => $p->id,
                'tip' => $p->musteriSikayeti ? 'Şikayet (Proje)' : 'İAA Projesi',
                'baslik' => $p->baslik,
                'puan' => $p->puan,
                'tarih' => $p->updated_at,
                'takim' => $p->atananTakim ? $p->atananTakim->ad : '-',
                'kategori' => $p->musteriSikayeti && $p->musteriSikayeti->sikayetKategori ? $p->musteriSikayeti->sikayetKategori->ad : 'Genel',
                'user' => $p->atananTakim && $p->atananTakim->lider ? $p->atananTakim->lider : null,
                'badge_color' => $p->musteriSikayeti ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800',
                'url' => route('proje.workspace.show', $p->id)
            ]);

        // B) Şikayet Girişleri
        $puanliSikayetGirisleri = MusteriSikayeti::where('kazanilan_puan', '>', 0)
            ->with(['olusturanKurulUyesi', 'sikayetKategori', 'cozumTakimi'])
            ->latest('created_at')
            ->take(10)
            ->get()
            ->toBase() // Eloquent Collection -> Base Collection
            ->map(fn($s) => [
                'id' => $s->id,
                'tip' => 'Şikayet (Giriş)',
                'baslik' => $s->musteri_sikayet_konusu,
                'puan' => $s->kazanilan_puan,
                'tarih' => $s->created_at,
                'takim' => $s->cozumTakimi ? $s->cozumTakimi->ad : '-',
                'kategori' => $s->sikayetKategori ? $s->sikayetKategori->ad : 'Genel',
                'user' => $s->olusturanKurulUyesi,
                'badge_color' => 'bg-indigo-100 text-indigo-800',
                'url' => route('admin.sikayetler.show', $s->id)
            ]);

        // Birleştir ve Sırala
        $ekstraTablolar['son_kazanilan_puanlar'] = collect($puanliProjeler->all())
            ->merge($puanliSikayetGirisleri->all())
            ->sortByDesc('tarih')
            ->take(10);

        return $ekstraTablolar;
    }
}
