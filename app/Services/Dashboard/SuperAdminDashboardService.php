<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\Iaa;
use App\Models\Bolum;
use App\Models\Takim;
use App\Models\MusteriSikayeti;
use App\Models\ProjeYorumu;
use App\Models\ProfileComment;
use App\Models\DisciplinaryCase;
use App\Models\Customer;
use App\Models\ArabuluculukCase;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardService
{
    /**
     * Superadmin Dashboard genel istatistiklerini döndürür.
     *
     * @return array
     */
    public function getStats($bolumId = null)
    {
        // 1. GENEL İSTATİSTİKLER
        $stats = [
            'toplam_kullanici' => User::when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            'onay_bekleyen_kullanici' => User::where('onaylandi_mi', false)->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            'son_kullanicilar' => User::when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->latest()->take(10)->get(),

            // SAF IAA İSTATİSTİKLERİ
            'toplam_iaa' => Iaa::sadeceOneriler()->where('durum', 'Tamamlandı')->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            'yeni_iaa_onerileri' => Iaa::sadeceOneriler()->whereNull('atanan_takim_id')->where('durum', 'Onay Bekliyor')->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            'onay_bekleyen_tamamlanmis_iaa' => Iaa::sadeceOneriler()->whereNotNull('atanan_takim_id')->whereIn('durum', [
                'Bölüm Onayı Bekliyor',
                'Direktör Onayı Bekliyor',
                'Yönetici Onayı Bekliyor'
            ])->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),

            'atama_bekleyen_iaa' => Iaa::sadeceOneriler()->where('durum', 'Havuzda')->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->count(),
            
            'bekleyen_iaa_atama_talepleri' => DB::table('iaa_talepleri')
                ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                ->where('iaa_talepleri.durum', 'beklemede')
                ->where('iaas.durum', 'Havuzda')
                ->when($bolumId, fn($q) => $q->where('iaas.bolum_id', $bolumId))
                ->distinct('iaa_talepleri.iaa_id')
                ->count('iaa_talepleri.iaa_id'),
            
            'toplam_iaa_talep_sayisi' => DB::table('iaa_talepleri')
                ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
                ->where('iaa_talepleri.durum', 'beklemede')
                ->where('iaas.durum', 'Havuzda')
                ->when($bolumId, fn($q) => $q->where('iaas.bolum_id', $bolumId))
                ->count(),

            'son_iaalar' => Iaa::sadeceOneriler()->when($bolumId, fn($q) => $q->where('bolum_id', $bolumId))->orderBy('created_at', 'desc')->take(3)->get(),

            'toplam_bolum' => Bolum::count(),
            'son_bolumler' => Bolum::latest()->take(10)->get(),

            'toplam_takim' => Takim::when($bolumId, fn($q) => $q->whereHas('lider', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'son_takimlar' => Takim::with('lider')->withCount('uyeler')->when($bolumId, fn($q) => $q->whereHas('lider', fn($q2) => $q2->where('bolum_id', $bolumId)))->latest()->take(10)->get(),

            'toplam_sikayet' => MusteriSikayeti::whereNull('deleted_at')->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'yeni_sikayet' => MusteriSikayeti::whereNull('deleted_at')->where('musteri_durum', 'Yeni')->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'islemde_sikayet' => MusteriSikayeti::whereNull('deleted_at')->where('musteri_durum', 'İşlemde')->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'onay_bekleyen_sikayet' => MusteriSikayeti::whereNull('deleted_at')->whereHas('iaaProjesi', function ($q) {
                $q->whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']);
            })->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'son_sikayetler' => MusteriSikayeti::whereNull('deleted_at')->with('customer')->when($bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($q2) => $q2->where('bolum_id', $bolumId)))->latest()->take(3)->get(),

            // YENİ EKLEMELER
            'toplam_musteri' => Customer::count(), // Müşteri her zaman global
            'son_musteriler_listesi' => Customer::latest()->take(3)->get(),
            'toplam_disiplin' => DisciplinaryCase::when($bolumId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'aktif_disiplin' => DisciplinaryCase::whereNotIn('durum', ['Karar Verildi', 'İptal Edildi'])->when($bolumId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'toplam_arabuluculuk' => ArabuluculukCase::when($bolumId, fn($q) => $q->whereHas('calisan', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
            'aktif_arabuluculuk' => ArabuluculukCase::where('status', '!=', 'kapatildi')->when($bolumId, fn($q) => $q->whereHas('calisan', fn($q2) => $q2->where('bolum_id', $bolumId)))->count(),
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
            $iaas = Iaa::sadeceOneriler()->with(['bolum', 'atananTakim.lider'])
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
                // Sorumlu Kişi Mantığı
                $sorumlu = 'Atanmamış';
                if ($item->durum == 'Onay Bekliyor') {
                    $sa = User::role('Superadmin')->first();
                    $sorumlu = $sa ? $sa->name : 'Sistem Yöneticisi';
                } else {
                    $sorumlu = $item->atananTakim && $item->atananTakim->lider 
                        ? $item->atananTakim->lider->name 
                        : ($item->gonderen ? $item->gonderen->name : 'Atanmamış');
                }

                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'İAA',
                    'konu' => $item->baslik,
                    'personel' => $sorumlu,
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
            $sikayetler = MusteriSikayeti::with(['sikayetKategori.bolum', 'cozumTakimi.lider', 'iaaProjesi'])
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
                // Sorumlu Kişi Mantığı (Dinamik)
                $sorumlu = 'Atanmamış';
                $projeDurumu = $item->iaaProjesi ? $item->iaaProjesi->durum : $item->musteri_durum;

                if ($projeDurumu == 'Bölüm Onayı Bekliyor') {
                    $kaliteci = $item->sikayetKategori ? $item->sikayetKategori->yoneticiler()->first() : null;
                    $sorumlu = $kaliteci ? $kaliteci->name : 'Kalite Yöneticisi';
                } elseif ($projeDurumu == 'Yönetici Onayı Bekliyor') {
                    $lider = User::role('Bölüm Lideri')->where('bolum_id', ($item->sikayetKategori ? $item->sikayetKategori->bolum_id : null))->first();
                    $sorumlu = $lider ? $lider->name : 'Bölüm Lideri';
                } elseif ($projeDurumu == 'Direktör Onayı Bekliyor') {
                    $direktor = $item->sikayetKategori && $item->sikayetKategori->bolum ? $item->sikayetKategori->bolum->director : null;
                    $sorumlu = $direktor ? $direktor->name : 'Direktör';
                } else {
                    $sorumlu = $item->cozumTakimi && $item->cozumTakimi->lider 
                        ? $item->cozumTakimi->lider->name 
                        : ($item->olusturanKurulUyesi ? $item->olusturanKurulUyesi->name : 'Kurul Üyesi');
                }

                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Müşteri Şikayeti',
                    'konu' => $item->musteri_sikayet_konusu,
                    'personel' => $sorumlu,
                    'bolum' => $item->sikayetKategori && $item->sikayetKategori->bolum ? $item->sikayetKategori->bolum->ad : '-',
                    'durum' => $projeDurumu,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => $item->oncelik ?? 'Normal',
                    'link' => route('admin.sikayetler.show', $item->id)
                ]);
            });
        }

        if (!$tur || $tur == 'Arabuluculuk') {
            $arabuluculuk = ArabuluculukCase::with(['calisan.bolum'])
                ->where('status', '!=', 'kapatildi');

            if ($bolum) {
                $arabuluculuk->whereHas('calisan.bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
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

        // 4. DİSİPLİN DOSYALARI
        if (!$tur || $tur == 'Disiplin') {
            $disiplinler = DisciplinaryCase::with(['user.bolum', 'reporter', 'behavior'])
                ->whereNotIn('durum', ['Karar Verildi', 'İptal Edildi', 'Taslak']);

            if ($bolum) {
                $disiplinler->whereHas('user.bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            if ($durum) {
                $disiplinler->where('durum', $durum);
            }

            $disiplinler->get()->each(function ($item) use ($bekleyenIsler) {
                // Sorumlu Kişi Mantığı
                $sorumlu = 'Atanmamış';
                if ($item->durum == 'Savunma Bekleniyor') {
                    $sorumlu = $item->user ? $item->user->name : 'İlgili Personel';
                } elseif ($item->durum == 'Yönetici Değerlendirmesi') {
                    $hukuk = User::role('Hukuk Admini')->first();
                    $sorumlu = $hukuk ? $hukuk->name : 'Hukuk Birimi';
                } elseif (in_array($item->durum, ['Kurulda', 'Kurul İncelemesinde'])) {
                    $sorumlu = 'Disiplin Kurulu Üyeleri';
                }

                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Disiplin',
                    'konu' => ($item->behavior ? $item->behavior->ad : 'Disiplin Dosyası') . ' (' . ($item->user ? $item->user->name : '-') . ')',
                    'personel' => $sorumlu,
                    'bolum' => $item->user && $item->user->bolum ? $item->user->bolum->ad : '-',
                    'durum' => $item->durum,
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => $item->hesaplanan_puan > 50 ? 'Yüksek' : 'Normal',
                    'link' => route('admin.disiplin.show', $item->id)
                ]);
            });
        }

        // 5. KULLANICI ONAYLARI
        if (!$tur || $tur == 'Kullanıcı Kaydı') {
            $kullanicilar = User::where('onaylandi_mi', false)->with('bolum');

            if ($bolum) {
                $kullanicilar->whereHas('bolum', function ($q) use ($bolum) {
                    $q->where('ad', $bolum);
                });
            }

            $kullanicilar->get()->each(function ($item) use ($bekleyenIsler) {
                $bekleyenIsler->push([
                    'id' => $item->id,
                    'tur' => 'Kullanıcı Kaydı',
                    'konu' => 'Yeni Kayıt Onayı: ' . $item->name,
                    'personel' => 'Süperadmin',
                    'bolum' => $item->bolum ? $item->bolum->ad : '-',
                    'durum' => 'Onay Bekliyor',
                    'gun' => $item->created_at->diffInDays(now()),
                    'oncelik' => 'Normal',
                    'link' => route('admin.users.index')
                ]);
            });
        }

        // Dropdown Verileri
        $dropdowns = [
            'bolumler' => Bolum::pluck('ad')->toArray(),
            'turler' => ['İAA', 'Müşteri Şikayeti', 'Arabuluculuk', 'Disiplin', 'Kullanıcı Kaydı'],
            'durumlar' => array_unique($bekleyenIsler->pluck('durum')->toArray())
        ];

        // İstatistikler
        $stats = [
            'toplam' => $bekleyenIsler->count(),
            'onay_bekleyen' => $bekleyenIsler->whereIn('durum', ['Onay Bekliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Yeni', 'Savunma Bekleniyor', 'Yönetici Değerlendirmesi', 'Kurulda'])->count(),
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
        $ekstraTablolar['son_tamamlanan_iaa'] = Iaa::sadeceOneriler()->where('durum', 'Tamamlandı')->with('atananTakim')->latest('updated_at')->take(10)->get();

        // 4. DİSİPLİN VAKALARI (YENİ)
        $ekstraTablolar['son_disiplin_vakalari'] = DisciplinaryCase::with(['user', 'behavior'])->latest()->take(10)->get();

        // 5. DİĞERLERİ
        $ekstraTablolar['son_yorumlar'] = ProjeYorumu::with('iaa')->latest()->take(10)->get();
        $ekstraTablolar['son_profil_yorumlari'] = ProfileComment::with(['yazan', 'profilSahibi'])->latest()->take(10)->get();

        // 5. SON KAZANILAN PUANLAR (Global Karma Liste)
        // A) Projelerden (IAA + Şikayet Çözümü)
        $puanliProjeler = Iaa::where('puan', '>', 0)
            ->where('durum', 'Tamamlandı')
            ->with(['atananTakim', 'musteriSikayeti.sikayetKategori'])
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->toBase()
            ->map(function($p) {
                // Puanı kazanan kişiyi bul (Sabitlenen lider veya fallback olarak mevcut lider)
                $winnerId = $p->tamamlayan_lider_id ?? ($p->atananTakim ? $p->atananTakim->lider_user_id : null);
                $winner = $winnerId ? User::withTrashed()->find($winnerId) : null;

                return [
                    'id' => $p->id,
                    'tip' => $p->musteriSikayeti ? 'Şikayet (Proje)' : 'İAA Projesi',
                    'baslik' => $p->baslik,
                    'puan' => $p->puan,
                    'tarih' => $p->updated_at,
                    'takim' => $p->atananTakim ? $p->atananTakim->ad : '-',
                    'kategori' => $p->musteriSikayeti && $p->musteriSikayeti->sikayetKategori ? $p->musteriSikayeti->sikayetKategori->ad : 'Genel',
                    'user' => $winner,
                    'badge_color' => $p->musteriSikayeti ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800',
                    'url' => route('proje.workspace.show', $p->id)
                ];
            });

        // B) Şikayet Girişleri
        $puanliSikayetGirisleri = MusteriSikayeti::where('kazanilan_puan', '>', 0)
            ->with(['olusturanKurulUyesi', 'sikayetKategori', 'cozumTakimi'])
            ->latest('created_at')
            ->take(10)
            ->get()
            ->toBase()
            ->map(fn($s) => [
                'id' => $s->id,
                'tip' => 'Şikayet (Giriş)',
                'baslik' => $s->musteri_sikayet_konusu,
                'puan' => $s->kazanilan_puan,
                'tarih' => $s->created_at,
                'takim' => $s->cozumTakimi ? $s->cozumTakimi->ad : '-',
                'kategori' => $s->sikayetKategori ? $s->sikayetKategori->ad : 'Genel',
                'user' => $s->olusturanKurulUyesi, // Bu zaten sabit (oluşturan)
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
