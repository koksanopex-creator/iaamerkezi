<?php

namespace App\Policies;

use App\Models\MusteriSikayeti;
use App\Models\User;
use App\Models\Iaa;
use Illuminate\Auth\Access\Response;

class MusteriSikayetiPolicy
{
    /**
     * Superadmin her kapıyı açar.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole(['Superadmin'])) {
            return true;
        }
        return null;
    }

    /**
     * Şikayet LİSTESİNE (Index) kimler erişebilir?
     * URL: /admin/sikayetler
     */
    public function viewAny(User $user): bool
    {
        // 1. Yönetici Rolleri
        // 'Bölüm Lideri' buraya eklendiği için Emrah Al artık listeye girebilir (403 almaz).
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi', 'Yonetim', 'Bölüm Lideri', 'Direktör'])) {
            return true;
        }

        // 2. Şikayet Çözüm Takımı Üyeleri
        if ($user->takimlar()->where('tur', 'sikayet')->exists()) {
            return true;
        }

        // 3. Proje (Squad) Üyeleri
        if ($user->gorevliOlduguProjeler()->wherePivot('durum', 'onaylandi')->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Şikayet DETAYINI (Show) kimler görebilir?
     * URL: /admin/sikayetler/{id}
     */
    public function view(User $user, MusteriSikayeti $sikayet): bool
    {
        // 1. Üst Yönetim ve Kurul (Her şeyi görür)
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Yonetim'])) {
            return true;
        }

        // 1.5. Direktör Yetkisi (Kendi bölümüne ait şikayetleri görür)
        if ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            if ($sikayet->sikayetKategori && in_array($sikayet->sikayetKategori->bolum_id, $yonetilenBolumIds)) {
                return true;
            }
        }

        // 2. Bölüm Kalite Yöneticisi (Sorumlu olduğu kategoriyi görür)
        if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $kategoriId = $sikayet->sikayet_kategorisi_id;
            return $user->yonettigiSikayetKategorileri->contains($kategoriId);
        }

        // === 3. BÖLÜM LİDERİ (EMRAH AL İÇİN ÖZEL KURAL) ===
        if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {

            // SENARYO A: Şikayet Kendi Bölümüne mi Ait? (Örn: Preform Şikayeti)
            if ($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum_id == $user->bolum_id) {
                return true; // Evet, görebilir.
            }

            // SENARYO B: Şikayet Başka Bölümün (Örn: Kapak) AMA Personeli Ekipte mi?
            if ($sikayet->iaa_id) {
                // Emrah'ın bölümündeki tüm personellerin ID'lerini bul
                $bolumPersonelIdleri = \App\Models\User::where('bolum_id', $user->bolum_id)->pluck('id');

                // İAA Projesini kontrol et
                $iaa = Iaa::find($sikayet->iaa_id);

                if ($iaa) {
                    // Proje ekibinde, Emrah'ın bölümünden (onaylı) bir personel var mı?
                    $personelVarMi = $iaa->projeEkibi()
                        ->whereIn('users.id', $bolumPersonelIdleri)
                        ->wherePivot('durum', 'onaylandi')
                        ->exists();

                    if ($personelVarMi) {
                        return true; // Evet, personeli işin içinde olduğu için İzleyici olarak görebilir.
                    }
                }
            }
        }
        // === BÖLÜM LİDERİ SONU ===

        // 4. Çözüm Lideri (Kendi takımına atanmışsa)
        if ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $lideriOlduguTakimIds = $user->lideriOlduguTakimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($lideriOlduguTakimIds->contains($sikayet->atanan_cozum_takimi_id)) {
                return true;
            }
        }

        // 5. Normal Takım Üyesi (Kendi takımına atanmışsa)
        $uyesiOlduguTakimIds = $user->takimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
        if ($uyesiOlduguTakimIds->contains($sikayet->atanan_cozum_takimi_id)) {
            return true;
        }

        // 6. SQUAD (Geçici Görev) Üyesi (Projeye atanmışsa)
        if ($sikayet->iaa_id) {
            $iaa = Iaa::find($sikayet->iaa_id);
            if ($iaa && $iaa->projeEkibi()->where('user_id', $user->id)->wherePivot('durum', 'onaylandi')->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Şikayet OLUŞTURMA (Create) yetkisi
     */
    public function create(User $user): bool
    {
        // 1. Kurul Üyeleri ekleyebilir
        if ($user->hasRole('Müşteri Şikayeti Kurulu')) {
            return true;
        }

        // 2. [YENİ] Kayıtlı Müşteri Yetkilileri ekleyebilir
        // Eğer kullanıcının customer_id'si doluysa ve personel değilse
        if ($user->customer_id && !$user->is_personnel) {
            return true;
        }

        return false;
    }

    public function update(User $user, MusteriSikayeti $sikayet): bool
    {
        // Kapatılmış şikayetleri kimse (Superadmin hariç) düzenleyemez
        if (trim($sikayet->musteri_durum) === 'Kapatıldı') {
            return false;
        }

        return $user->id === $sikayet->olusturan_kurul_uyesi_id;
    }

    public function delete(User $user, MusteriSikayeti $sikayet): bool
    {
        return $user->id === $sikayet->olusturan_kurul_uyesi_id;
    }

    public function restore(User $user, MusteriSikayeti $sikayet): bool
    {
        return false;
    }

    public function forceDelete(User $user, MusteriSikayeti $sikayet): bool
    {
        return false;
    }
}