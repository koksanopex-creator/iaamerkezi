<?php

namespace App\Policies;

use App\Models\Iaa;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IaaPolicy
{
    /**
     * Bu metod, diğer tüm kurallardan önce çalışır.
     * Eğer kullanıcı 'Superadmin' rolüne sahipse, diğer kurallara bakmadan
     * tüm işlemlere otomatik olarak izin verir.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Superadmin')) {
            return true;
        }

        return null; // Diğer kuralları kontrol etmeye devam et
    }

    /**
     * Kullanıcının İAA listelerini (örneğin /havuz sayfası) görüp göremeyeceğini belirler.
     */
    public function viewAny(User $user): bool
    {
        // Giriş yapmış her kullanıcı listeleri görebilir.
        return true;
    }

    /**
     * Kullanıcının belirli bir İAA önerisini görüp göremeyeceğini belirler.
     * BU, SİZİN 403 HATANIZI ÇÖZECEK OLAN KURALDIR.
     */
    public function view(User $user, Iaa $iaa): bool
    {
        // Kural 1: Eğer kullanıcı öneriyi gönderen kişiyse, görebilir.
        if ($user->id === $iaa->gonderen_user_id) {
            return true;
        }

        // Kural 1.5: Direktör Yetkisi (Maksimum Kapsamlı Erişim)
        if ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();

            // 1. Doğrudan proje bölümü üzerinden
            if (in_array($iaa->bolum_id, $yonetilenBolumIds)) {
                return true;
            }

            // 2. Şikayet kategorisi departmanı üzerinden
            if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori && in_array($iaa->musteriSikayeti->sikayetKategori->bolum_id, $yonetilenBolumIds)) {
                return true;
            }

            // 3. Proje gönderen kişi üzerinden
            if ($iaa->gonderen && in_array($iaa->gonderen->bolum_id, $yonetilenBolumIds)) {
                return true;
            }

            // 4. Proje ekibi (Squad) üzerinden
            $teamMembersDeptIds = $iaa->projeEkibi()->pluck('users.bolum_id')->unique()->toArray();
            if (count(array_intersect($teamMembersDeptIds, $yonetilenBolumIds)) > 0) {
                return true;
            }

            // 5. Takım lideri üzerinden
            if ($iaa->atananTakim && $iaa->atananTakim->lider && in_array($iaa->atananTakim->lider->bolum_id, $yonetilenBolumIds)) {
                return true;
            }
        }

        // --- rules1.md: İAA vs ŞİKAYET AYRIMI (Pattern Matching) ---
        $isSikayet = str_contains($iaa->oneri ?? '', 'Müşteri şikayetinden');
        $isIaa = !$isSikayet;

        // Kural 1.6: Bölüm Lideri / Yetkili Yardımcı Yetkisi (Kendi bölümüne ait projeler)
        $isLeader = $user->hasRole('Bölüm Lideri');
        $isAuthorizedDeputy = $user->hasRole('Bölüm Lider Yardımcısı') && $user->hasBolumAuthority('bolum.iaa.gor');

        if (($isLeader || $isAuthorizedDeputy) && $user->bolum_id && $iaa->bolum_id == $user->bolum_id) {
            return true;
        }

        // Kural 1.7: Şikayet Projeleri İçin Ek Roller (Kategori bazlı)
        if ($isSikayet) {
            // Bölüm Kalite Yöneticisi (Sadece şikayetlerde vardır)
            if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                if ($iaa->musteriSikayeti) {
                    $kategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                    if ($kategoriId && $user->yonettigiSikayetKategorileri->contains($kategoriId)) {
                        return true;
                    }
                    if ($user->bolum_id && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum_id == $user->bolum_id) {
                        return true;
                    }
                }
            }

            // Kural 1.8: Müşteri Şikayeti Kurulu ve Yöneticileri (Bölgesel veya Global)
            if ($iaa->musteriSikayeti) {
                $sikayetKonum = $iaa->musteriSikayeti->konum_tipi;
                if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
                    return true;
                }
                if ($sikayetKonum === 'Yurt İçi' && $user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                    return true;
                }
                if ($sikayetKonum === 'Yurt Dışı' && $user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                    return true;
                }
            }
        }

        // Kural 1.9: Eğer bu kişi herhangi bir adımda widget ile seçilmişse (ve bildirim gittiyse), görebilir.
        // Veya herhangi bir adıma "Adım Sorumlusu" olarak atanmışsa görebilir.
        $isAssignedToStep = \Illuminate\Support\Facades\DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa->id)
            ->where('user_id', $user->id)
            ->exists();
            
        if ($isAssignedToStep) {
            return true;
        }

        $isWidgetSelected = \App\Models\IaaProgressUpdate::join('iaa_talepleri', 'iaa_talepleri.id', '=', 'iaa_progress_updates.iaa_talep_id')
            ->where('iaa_talepleri.iaa_id', $iaa->id)
            ->where('iaa_progress_updates.content', 'LIKE', '%"'.$user->id.'"%')
            ->exists();
            
        if ($isWidgetSelected) {
            return true;
        }

        // Kural 2: Durum Bazlı Görünürlük (Yalnızca Personel İçin)
        if ($isIaa && $user->is_personnel) {
            // Saf İAA'lar Havuzda veya sonrasında tüm personeller tarafından görülebilir
            if (in_array($iaa->durum, ['Havuzda', 'Talep Edildi', 'Atandı', 'Yönetici Onayı Bekliyor', 'Revize Ediliyor', 'Tamamlandı'])) {
                return true;
            }
        } else {
            // Şikayet Projeleri: Sadece terminal ve atama durumlarında görünür (Gizlilik gereği onay süreçleri kısıtlı kalır)
            if (in_array($iaa->durum, ['Atandı', 'Tamamlandı'])) {
                return true;
            }
            // Not: Onay sürecindeki (Bölüm Onayı Bekliyor vb.) şikayetleri 
            // sadece yukarıdaki Kural 1, 1.5, 1.6'daki yetkililer görebilir.
        }

        // Bu koşullar sağlanmazsa göremez.
        return false;
    }

    /**
     * Kullanıcının yeni bir İAA önerisi oluşturup oluşturamayacağını belirler.
     */
    public function create(User $user): bool
    {
        // Giriş yapmış her kullanıcı yeni öneri oluşturabilir.
        return true;
    }

    /**
     * Kullanıcının bir İAA önerisini güncelleyip güncelleyemeyeceğini belirler.
     */
    public function update(User $user, Iaa $iaa): bool
    {
        // 1. Sadece öneriyi gönderen kişi ve sadece "Onay Bekliyor" durumundayken güncelleyebilir. (Saf İAA Akışı)
        if ($user->id === $iaa->gonderen_user_id && $iaa->durum === 'Onay Bekliyor') {
            return true;
        }

        // 2. Müdahale Yetkili Kalite Yöneticisi (Şikayet Projeleri Akışı)
        if ($user->hasRole('Bölüm Kalite Yöneticisi') && $user->can_intervene_quality) {
            if ($iaa->musteriSikayeti) {
                $kategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                if ($kategoriId && $user->yonettigiSikayetKategorileri->contains($kategoriId)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Kullanıcının projeye idari müdahale (adım geri alma, atama vb.) yapıp yapamayacağını belirler.
     */
    public function intervene(User $user, Iaa $iaa): bool
    {
        // 1. Takım Lideri
        if ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $user->id) {
            return true;
        }

        // 2. Müdahale Yetkili Kalite Yöneticisi
        if ($user->hasRole('Bölüm Kalite Yöneticisi') && $user->can_intervene_quality) {
            if ($iaa->musteriSikayeti) {
                $kategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                if ($kategoriId && $user->yonettigiSikayetKategorileri->contains($kategoriId)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Kullanıcının bir İAA önerisini silip silemeyeceğini belirler.
     */
    public function delete(User $user, Iaa $iaa): bool
    {
        // Sadece öneriyi gönderen kişi silebilir.
        return $user->id === $iaa->gonderen_user_id;
    }

    /**
     * Silinmiş bir öneriyi geri getirip getiremeyeceğini belirler.
     * (Sadece Superadmin yapabilsin diye false bırakıyoruz, before metodu halledecek)
     */
    public function restore(User $user, Iaa $iaa): bool
    {
        return false;
    }

    /**
     * Bir öneriyi kalıcı olarak silip silemeyeceğini belirler.
     * (Sadece Superadmin yapabilsin diye false bırakıyoruz, before metodu halledecek)
     */
    public function forceDelete(User $user, Iaa $iaa): bool
    {
        return false;
    }
}