<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\Iaa;
use App\Models\MusteriSikayeti;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class KullaniciPuanService
{
    /**
     * Tek bir kullanıcının toplam puanını hesaplar.
     * (Sinan'ın 50 Puanı ve Gülnur'un Çift Puan Sorunu Düzeltildi)
     */
    public function calculateTotalScore(User $user)
    {
        $toplamPuan = 0;

        // =================================================================
        // 1. PROJELERDEN GELEN PUANLAR (LİDER + SQUAD + PASİF ÜYE)
        // =================================================================

        // A. İlişkileri Hazırla
        $takimIdleri = $user->takimlar()->pluck('takim_id'); // Üye olduğu takımlar

        // Lider olduğu takımları çekiyoruz
        $liderOlduguTakimIds = \App\Models\Takim::where('lider_user_id', $user->id)->pluck('id');

        // SQUAD: Bizzat görevli olduğu projeler
        $squadProjeIds = $user->gorevliOlduguProjeler()->pluck('iaas.id');

        // B. Puanları Topla
        $projePuani = Iaa::where('durum', 'Tamamlandı')
            ->where('puan', '>', 0)
            ->where(function ($query) use ($takimIdleri, $liderOlduguTakimIds, $squadProjeIds) {

                // KURAL A: TAKIM LİDERİ (Her türlü puan alır)
                $query->whereIn('atanan_takim_id', $liderOlduguTakimIds)

                    // KURAL B: SQUAD / GÖREVLİ (Her türlü puan alır)
                    ->orWhereIn('id', $squadProjeIds)

                    // KURAL C: NORMAL ÜYE / PASİF (Filtreli Puan)
                    // Şikayetse ve yukarıdaki Squad listesinde yoksa PUAN ALAMAZ.
                    ->orWhere(function ($sub) use ($takimIdleri) {
                    $sub->whereIn('atanan_takim_id', $takimIdleri)
                        ->doesntHave('musteriSikayeti');
                });
            })
            ->sum('puan');

        $toplamPuan += $projePuani;

        // =================================================================
        // 2. ŞİKAYET OLUŞTURMA PUANI (SADECE GİRİŞ PUANI)
        // =================================================================

        $sikayetGirisPuani = Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0;

        if ($sikayetGirisPuani > 0) {
            // Şikayet girişlerini sayıyoruz (Talep ve Hatalı Bildirim olarak kapananlar/bekleyenler hariç)
            $girilenSikayetSayisi = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $user->id)
                ->where('musteri_durum', '!=', 'Talep')
                ->whereDoesntHave('iaaProjesi', function ($q) {
                    $q->whereIn('durum', [
                        'talep_olarak_kapatildi',
                        'hatali_bildirim_olarak_kapatildi'
                    ]);
                })
                ->count();

            $toplamPuan += ($girilenSikayetSayisi * $sikayetGirisPuani);
        }

        // =================================================================
        // 3. ONAYLANAN ÖNERİLER
        // =================================================================
        $iaaOneriPuani = Setting::where('key', 'iaa_oneri_puani')->value('value') ?? 0;

        if ($iaaOneriPuani > 0) {
            $oneriSayisi = Iaa::where('gonderen_user_id', $user->id)
                ->whereNotIn('durum', ['Taslak', 'Onay Bekliyor', 'Reddedildi'])
                ->count();

            $toplamPuan += ($oneriSayisi * $iaaOneriPuani);
        }

        // =================================================================
        // 4. DİSİPLİN CEZALARI
        // =================================================================
        $disiplinCezalari = \App\Models\DisciplinaryCase::where('user_id', $user->id)
            ->where('durum', 'Karar Verildi')
            ->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)')
            ->sum('hesaplanan_puan');

        $toplamPuan -= $disiplinCezalari;

        if ($toplamPuan < 0) {
            $toplamPuan = 0;
        }

        return $toplamPuan;
    }

    /**
     * Kullanıcının detaylı puan verilerini ve geçmişini döndürür.
     * Tarih filtresi eklendi.
     */
    public function getDetailedScoreData(User $user, $startDate = null, $endDate = null)
    {
        // Tarih filtresi fonksiyonu (Reusable)
        $applyDateFilter = function ($query, $column = 'updated_at') use ($startDate, $endDate) {
            if ($startDate) {
                $query->whereDate($column, '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate($column, '<=', $endDate);
            }
        };

        // 1. PROJELER (Kazanılan Puanlar)
        // A. Lider Olduğu Projeler
        $liderProjelerQuery = Iaa::whereHas('atananTakim', function ($q) use ($user) {
            $q->where('lider_user_id', $user->id);
        })
            ->where('durum', 'Tamamlandı')
            ->where('puan', '>', 0);

        $applyDateFilter($liderProjelerQuery, 'onaylanma_tarihi'); // Onay tarihine göre filtrele

        $liderProjeler = $liderProjelerQuery->get()->map(function ($p) {
            $p->kazanma_sebebi = 'Takım Lideri';
            return $p;
        });

        // B. Squad (Görevli) Olduğu Projeler
        $squadProjelerQuery = $user->gorevliOlduguProjeler()
            ->where('iaas.durum', 'Tamamlandı')
            ->where('iaas.puan', '>', 0);

        $applyDateFilter($squadProjelerQuery, 'onaylanma_tarihi');

        $squadProjeler = $squadProjelerQuery->get()->map(function ($p) {
            $p->kazanma_sebebi = 'Proje Ekibi (Squad)';
            return $p;
        });

        // C. Pasif Üye Olduğu Projeler (Şikayet Olmayanlar)
        $pasifProjeIds = $user->takimlar()->pluck('takim_id');
        $pasifProjelerQuery = Iaa::whereIn('atanan_takim_id', $pasifProjeIds)
            ->doesntHave('musteriSikayeti') // Şikayetlerden pasif puan alınmaz
            ->where('durum', 'Tamamlandı')
            ->where('puan', '>', 0);

        $applyDateFilter($pasifProjelerQuery, 'onaylanma_tarihi');

        $pasifProjeler = $pasifProjelerQuery->get()->map(function ($p) {
            $p->kazanma_sebebi = 'Takım Üyesi'; // "Pasif" kelimesi kaldırıldı
            return $p;
        });

        // Hepsini birleştir ve ID'ye göre unique yap
        $tumProjeler = $liderProjeler->merge($squadProjeler)->merge($pasifProjeler)->unique('id')->sortByDesc('onaylanma_tarihi');

        // 2. ŞİKAYET GİRİŞLERİ
        $sikayetGirisleriQuery = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $user->id)
            ->where('musteri_durum', '!=', 'Talep')
            ->whereDoesntHave('iaaProjesi', function ($q) {
                $q->whereIn('durum', [
                    'talep_olarak_kapatildi',
                    'hatali_bildirim_olarak_kapatildi'
                ]);
            });

        $applyDateFilter($sikayetGirisleriQuery, 'created_at'); // Oluşturulma tarihine göre

        $sikayetGirisleri = $sikayetGirisleriQuery->get();

        $sikayetGirisPuani = Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0;

        // 3. ÖNERİLER
        $onerilerQuery = Iaa::where('gonderen_user_id', $user->id)
            ->whereNotIn('durum', ['Taslak', 'Onay Bekliyor', 'Reddedildi']);

        $applyDateFilter($onerilerQuery, 'created_at'); // Öneri tarihine göre

        $oneriler = $onerilerQuery->get();

        $oneriPuani = Setting::where('key', 'iaa_oneri_puani')->value('value') ?? 0;

        // 4. DİSİPLİN CEZALARI
        $cezalarQuery = \App\Models\DisciplinaryCase::where('user_id', $user->id)
            ->where('durum', 'Karar Verildi')
            ->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)');

        $applyDateFilter($cezalarQuery, 'updated_at'); // Karar tarihine göre

        $cezalar = $cezalarQuery->get();

        // 5. TOPLAM PUAN HESABI (Filtreli veya Genel)
        // Eğer tarih filtresi varsa, yukarıdaki filtrelenmiş verilerden topla.
        // Yoksa genel toplamı hesapla.

        $hesaplananToplam = 0;

        // Projelerden
        foreach ($tumProjeler as $p) {
            $hesaplananToplam += $p->puan;
        }

        // Şikayetlerden
        $hesaplananToplam += ($sikayetGirisleri->count() * $sikayetGirisPuani);

        // Önerilerden
        $hesaplananToplam += ($oneriler->count() * $oneriPuani);

        // Cezalar (Düş)
        $hesaplananToplam -= $cezalar->sum('hesaplanan_puan');

        // NOT: Filtreli görünümde (örn: bu hafta) sadece ceza varsa, toplamın EKSİ çıkması isteniyor.
        // Bu yüzden alttaki kontrolü kaldırıyoruz.
        // if ($hesaplananToplam < 0)
        //    $hesaplananToplam = 0;

        return [
            'tum_projeler' => $tumProjeler,
            'sikayet_girisleri' => $sikayetGirisleri,
            'sikayet_giris_puani' => $sikayetGirisPuani,
            'oneriler' => $oneriler,
            'oneri_puani' => $oneriPuani,
            'cezalar' => $cezalar,
            'toplam_puan' => $hesaplananToplam // Filtreli toplam
        ];
    }


    public function getTeamDetailedScoreData(\App\Models\Takim $takim)
    {
        // 1. Takım Üyeleri
        $uyeler = $takim->uyeler;

        // 2. Tamamlanan Projeler (Bu takıma atanmış ve bitmiş)
        $tamamlananProjeler = Iaa::where('atanan_takim_id', $takim->id)
            ->where('durum', 'Tamamlandı')
            ->where('puan', '>', 0)
            ->doesntHave('musteriSikayeti') // SADECE SAF İAA PROJELERİ GÖZÜKSÜN (Şikayetler şikayet listesinde)
            ->orderByDesc('onaylanma_tarihi')
            ->get();

        // 3. Şikayet Çözümleri (Eğer Şikayet Takımıysa)
        $cozulenSikayetler = [];
        if ($takim->tur == 'sikayet') {
            $cozulenSikayetler = MusteriSikayeti::where('atanan_cozum_takimi_id', $takim->id)
                ->where('musteri_durum', 'Kapatıldı')
                ->orderByDesc('updated_at')
                ->with('iaaProjesi') // Puanı çekmek için gerekli
                ->get();
        }

        // Puan Hesaplama (DB Score Should Match Calculated)
        $hesaplananPuan = $tamamlananProjeler->sum('puan');

        // Şikayetlerden gelen puanları ekle (Proje puanı varsa onu, yoksa şikayet puanını)
        if (!empty($cozulenSikayetler)) {
            foreach ($cozulenSikayetler as $sikayet) {
                if ($sikayet->iaa_id && $sikayet->iaaProjesi) {
                    $hesaplananPuan += $sikayet->iaaProjesi->puan;
                } else {
                    $hesaplananPuan += $sikayet->kazanilan_puan;
                }
            }
        }

        return [
            'uyeler' => $uyeler,
            'tamamlananProjeler' => $tamamlananProjeler,
            'cozulenSikayetler' => $cozulenSikayetler,
            'hesaplananPuan' => $hesaplananPuan
        ];
    }
}
