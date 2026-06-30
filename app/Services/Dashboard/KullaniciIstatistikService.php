<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\Iaa;
use App\Services\Dashboard\KullaniciPuanService;

class KullaniciIstatistikService
{
    protected $puanService;

    public function __construct(KullaniciPuanService $puanService)
    {
        $this->puanService = $puanService;
    }

    /**
     * Standart Kullanıcı İstatistiklerini (Puan, Bekleyen Görevler) Döndürür.
     */
    public function getStats(User $user)
    {
        // Kullanıcının "SQUAD Üyesi" olarak atandığı veya 
        // Lideri/Üyesi olduğu bir "Takıma" atanan projeler için ortak sorgu koşulu
        $userInvolvedClosure = function ($q) use ($user) {
            $q->whereHas('projeEkibi', fn($sq) => $sq->where('users.id', $user->id))
                ->orWhereHas('atananTakim', function ($sq) use ($user) {
                    $sq->where('lider_user_id', $user->id)
                        ->orWhereHas('uyeler', fn($uq) => $uq->where('users.id', $user->id));
                });
        };

        // 1. Bekleyen Görev Sayısı
        // A) Takım Lideri olduğu projelerden kendisine düşen onay vb.
        // B) Squad üyesi olduğu projeler
        // C) Üyesi olduğu takıma atanan projeler
        $bekleyenGorevler = Iaa::where($userInvolvedClosure)
            ->whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor'])
            ->count();

        // 2. Tamamlanan Görevler
        $tamamlananGorevler = Iaa::where($userInvolvedClosure)
            ->where('durum', 'Tamamlandı')
            ->count();

        // 3. Puan ve Sıralama
        $puan = $this->puanService->calculateTotalScore($user);

        // Sıralama (Basit Count)
        $siralama = 0;

        // 4. Okunmamış Mesajlar (Şimdilik devre dışı)
        $okunmamisMesaj = 0; // Message modeli bulunamadığı için 0 dönüyoruz.

        // 5. Havuzdaki Öneri Sayısı (Havuzda olanlar)
        $havuzOneriSayisi = Iaa::where('durum', 'Havuzda')->count();
        $sonHavuzOnerileri = Iaa::where('durum', 'Havuzda')->latest()->take(3)->get();

        // 6. Takımlarım
        $takimlarimSayisi = $user->takimlar()->count();
        $sonTakimlarim = $user->takimlar()->latest()->take(3)->get();

        // 7. Katılıma Açık Takımlar (Şu an tüm takımlar açık varsayalım veya bir sütun kontrolü ekleyelim. Varsayılan tüm takımlar)
        // 7. Katılıma Açık Takımlar (Sadece 'sikayet' olmayanlar)
        $acikTakimSayisi = \App\Models\Takim::where('tur', '!=', 'sikayet')->count();
        $sonAcikTakimlar = \App\Models\Takim::where('tur', '!=', 'sikayet')
            ->withCount('uyeler')
            ->latest()
            ->take(3)
            ->get();

        $iaaProjelerimQuery = clone Iaa::query()
            ->where($userInvolvedClosure)
            ->whereDoesntHave('musteriSikayeti') // Şikayet kaynaklı olmayanlar
            ->whereNotIn('durum', ['Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Talep Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi', 'talep_olarak_kapatildi']);

        $iaaProjelerimCount = $iaaProjelerimQuery->count();
        $sonIaaProjelerim = $iaaProjelerimQuery
            ->latest('created_at')
            ->take(3)
            ->get();

        // 9. Devam Eden Şikayet Projelerim (Gorevli olduğu şikayetler)
        $sikayetProjelerimQuery = clone Iaa::query()
            ->where($userInvolvedClosure)
            ->whereHas('musteriSikayeti', function ($q) {
                $q->whereNull('deleted_at');
            }) // Şikayet kaynaklı olanlar ve SİLİNMEMİŞ olanlar
            ->whereNotIn('durum', ['Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Talep Olarak Kapatıldı', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'hatali_bildirim_olarak_kapatildi', 'talep_olarak_kapatildi']); // Onay bekleyenleri ayırıyoruz

        $sikayetProjelerimCount = $sikayetProjelerimQuery->count();
        $sonSikayetProjelerim = $sikayetProjelerimQuery
            ->with('musteriSikayeti') // Eager load
            ->latest('created_at')
            ->take(3)
            ->get();

        // 10. Onay Bekleyen Şikayet Projelerim (YENİ KART)
        $onayBekleyenSikayetQuery = clone Iaa::query()
            ->where($userInvolvedClosure)
            ->whereHas('musteriSikayeti', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']);

        $onayBekleyenSikayetCount = $onayBekleyenSikayetQuery->count();
        $sonOnayBekleyenSikayetler = $onayBekleyenSikayetQuery
            ->with('musteriSikayeti')
            ->latest('updated_at')
            ->take(3)
            ->get();

        return [
            'bekleyen_gorevler' => $bekleyenGorevler,
            'tamamlanan_gorevler' => $tamamlananGorevler,
            'puan' => $puan,
            'siralama' => $siralama, // Sonra geliştirilebilir
            'okunmamis_mesaj' => $okunmamisMesaj,
            'havuz_oneri_sayisi' => $havuzOneriSayisi,
            'son_havuz_onerileri' => $sonHavuzOnerileri,
            'takimlarim_sayisi' => $takimlarimSayisi,
            'son_takimlarim' => $sonTakimlarim,
            'acik_takim_sayisi' => $acikTakimSayisi,
            'son_acik_takimlar' => $sonAcikTakimlar,
            'iaa_projelerim_count' => $iaaProjelerimCount,
            'son_iaa_projelerim' => $sonIaaProjelerim,
            'sikayet_projelerim_count' => $sikayetProjelerimCount,
            'son_sikayet_projelerim' => $sonSikayetProjelerim,
            'onay_bekleyen_sikayet_count' => $onayBekleyenSikayetCount,
            'son_onay_bekleyen_sikayetler' => $sonOnayBekleyenSikayetler
        ];
    }
}
