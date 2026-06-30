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
        if ($user->hasAnyRole(['Superadmin', 'Super Admin', 'SUPERADMIN', 'superadmin'])) {
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
        // Hukuk rolleri (eğer başka yönetici rolleri yoksa) şikayet listesini göremez
        if ($user->hasAnyRole(['Hukuk Admini', 'Hukuk Yöneticisi']) && !$user->hasAnyRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Bölüm Lideri', 'Direktör', 'Bölüm Kalite Yöneticisi'])) {
            return false;
        }

        // 1. Yönetici Rolleri
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi', 'Yonetim', 'Bölüm Lideri', 'Direktör', 'Müşteri Saha Temsilcisi'])) {
            return true;
        }

        // 1.1. Yetkili Yardımcılar (Matris İzni)
        if ($user->hasRole('Bölüm Lider Yardımcısı') && $user->hasBolumAuthority('bolum.sikayet.gor')) {
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

        // 4. Müşteri Temsilcisi (En az bir firmaya yetkisi varsa listeyi görebilir)
        if (!$user->is_personnel && ($user->customer_id || $user->customers()->exists())) {
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
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Yonetim'])) {
            return true;
        }

        // 1.1 Yurt İçi Kurul/Yönetici
        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
            if ($sikayet->konum_tipi === 'Yurt İçi') return true;
        }

        // 1.2 Yurt Dışı Kurul/Yönetici
        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            if ($sikayet->konum_tipi === 'Yurt Dışı') return true;
        }

        // 1.5. Direktör Yetkisi (Kendi bölümüne ait şikayetleri görür)
        if ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            if ($sikayet->sikayetKategori && in_array($sikayet->sikayetKategori->bolum_id, $yonetilenBolumIds)) {
                return true;
            }
        }

        // 2. Bölüm Kalite Yöneticisi (Sorumlu olduğu BÖLÜMLERDEKİ tüm şikayetleri görür)
        if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $allowedBolumIds = $user->getAllowedBolumIds();
            if ($allowedBolumIds === '*' || ($sikayet->sikayetKategori && in_array($sikayet->sikayetKategori->bolum_id, $allowedBolumIds))) {
                return true;
            }
        }

        // 2.5 Müşteri Saha Temsilcisi (Sorumlu olduğu BÖLÜMLERDEKİ şikayetleri görür)
        if ($user->hasRole(['Müşteri Saha Temsilcisi'])) {
            $allowedBolumIds = $user->musteriSahaTemsilcisiOlduguBolumler()->pluck('bolumler.id')->toArray();
            if ($sikayet->sikayetKategori && in_array($sikayet->sikayetKategori->bolum_id, $allowedBolumIds)) {
                return true;
            }
        }

        // === 3. BÖLÜM LİDERİ VE YETKİLİ YARDIMCISI (BÖLÜM BAZLI ERİŞİM) ===
        if ($user->hasRole('Bölüm Lideri') || ($user->hasRole('Bölüm Lider Yardımcısı') && $user->hasBolumAuthority('bolum.sikayet.gor'))) {
            if ($user->bolum_id) {
                // Kendi Bölümündekileri Görür
                if ($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum_id == $user->bolum_id) {
                    return true;
                }
                
                // Personeli projede olanları görür (Sadece Müdür İçin veya yetki varsa)
                if ($sikayet->iaa_id) {
                    $bolumPersonelIdleri = \App\Models\User::where('bolum_id', $user->bolum_id)->pluck('id');
                    $iaa = \App\Models\Iaa::find($sikayet->iaa_id);
                    if ($iaa && $iaa->projeEkibi()->whereIn('users.id', $bolumPersonelIdleri)->wherePivot('durum', 'onaylandi')->exists()) {
                        return true;
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

        // 7. Müşteri Yetkilisi / Temsilcisi (Şikayetin firması, temsilcinin yetkili olduğu firmalardan biri mi?)
        if (!$user->is_personnel) {
            // Doğrudan bağlı olduğu firma veya pivot tablodaki yetkili olduğu firmalar
            if ($user->customer_id == $sikayet->customer_id || $user->customers()->where('customers.id', $sikayet->customer_id)->exists()) {
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
        // Hukuk rolleri (eğer başka yetkili rolleri yoksa) şikayet oluşturamaz
        if ($user->hasAnyRole(['Hukuk Admini', 'Hukuk Yöneticisi']) && !$user->hasAnyRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Bölüm Kalite Yöneticisi'])) {
            return false;
        }

        // 1. Kurul Üyeleri ekleyebilir
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
            return true;
        }

        // [YENİ] Bölüm Kalite Yöneticisi ekleyebilir
        if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            return true;
        }

        // 2. [YENİ] Kayıtlı Müşteri Yetkilileri ekleyebilir
        // Eğer kullanıcının customer_id'si doluysa veya yetkili olduğu firmalar varsa ve personel değilse
        if (!$user->is_personnel && ($user->customer_id || $user->customers()->exists())) {
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

        // [YENİ] Bölüm Kalite Yöneticisi sadece kendi BÖLÜMÜNDEKİ şikayetleri ve durumu uygunsa düzenleyebilir
        if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $allowedStatuses = ['Yeni', 'Atandı', 'İşlemde'];
            if (in_array(trim($sikayet->musteri_durum), $allowedStatuses)) {
                $allowedBolumIds = $user->getAllowedBolumIds();
                if ($allowedBolumIds === '*' || ($sikayet->sikayetKategori && in_array($sikayet->sikayetKategori->bolum_id, $allowedBolumIds))) {
                    return true;
                }
            }
        }

        // [YENİ] Müşteri Şikayeti Çözüm Lideri kendi bölümüne ait YADA atandığı takımın lideri olduğu şikayetleri düzenleyebilir
        if ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $allowedStatuses = ['Yeni', 'Atandı', 'İşlemde'];
            if (in_array(trim($sikayet->musteri_durum), $allowedStatuses)) {
                $kendiBolumu = ($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum_id == $user->bolum_id);

                $takimLideriMi = false;
                if ($sikayet->atanan_cozum_takimi_id) {
                    $takimLideriMi = $user->lideriOlduguTakimlar->contains('id', $sikayet->atanan_cozum_takimi_id);
                }

                if ($kendiBolumu || $takimLideriMi) {
                    return true;
                }
            }
        }

        // [YENİ] Matris üzerinden 'Düzenleme' yetkisi verilmiş Yardımcılar
        if ($user->hasRole('Bölüm Lider Yardımcısı') && $user->hasBolumAuthority('bolum.sikayet.duzenle')) {
            if ($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum_id == $user->bolum_id) {
                return true;
            }
        }

        // Yurt İçi / Yurt Dışı düzenleme yetkisi
        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi']) && $sikayet->konum_tipi === 'Yurt İçi') {
            return true;
        }
        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']) && $sikayet->konum_tipi === 'Yurt Dışı') {
            return true;
        }
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
            return true;
        }

        return $user->id === $sikayet->olusturan_kurul_uyesi_id;
    }

    public function deleteAny(User $user): bool
    {
        return false; // Sadece Superadmin (before metodundan) toplu silebilir
    }

    public function delete(User $user, MusteriSikayeti $sikayet): bool
    {
        // 1. Sadece kendi eklediği şikayeti silebilir
        if ($user->id !== $sikayet->olusturan_kurul_uyesi_id) {
            return false;
        }

        // 2. Eğer şikayet durumu 'İşlemde' ise SİLEMEZ
        if ($sikayet->musteri_durum === 'İşlemde') {
            return false;
        }

        // 3. Eğer Proje/Takım Atanmışsa (atanan_cozum_takimi_id doluysa) SİLEMEZ
        // Kullanıcı 'Proje durumu Atanmadı ise silebilir' dediği için:
        if ($sikayet->atanan_cozum_takimi_id !== null) {
            return false;
        }

        return true;
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
