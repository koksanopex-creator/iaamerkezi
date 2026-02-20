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

        // Kural 2: Eğer öneri "Havuzda" veya daha ileri bir aşamadaysa (TAMAMLANDI DAHİL), herkes görebilir.
        // === GÜNCELLEME BURADA ===
        if (in_array($iaa->durum, ['Havuzda', 'Talep Edildi', 'Atandı', 'Yönetici Onayı Bekliyor', 'Revize Ediliyor', 'Tamamlandı'])) {
            return true;
        }
        // =========================

        // Bu koşullar sağlanmazsa (örneğin başkasının "Onay Bekleyen" önerisi), göremez.
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
        // Sadece öneriyi gönderen kişi ve sadece "Onay Bekliyor" durumundayken güncelleyebilir.
        return $user->id === $iaa->gonderen_user_id && $iaa->durum === 'Onay Bekliyor';
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