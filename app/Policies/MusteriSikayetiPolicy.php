<?php

namespace App\Policies;

use App\Models\MusteriSikayeti;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MusteriSikayetiPolicy
{
    /**
     * Perform pre-authorization checks.
     * Herhangi bir yetki kontrolünden önce bu metot çalışır.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Eğer kullanıcı Superadmin ise, diğer kontrollere bakmadan tüm yetkileri ver.
        if ($user->hasRole('Superadmin')) {
            return true;
        }

        return null; // Superadmin değilse, normal kontrollere devam et.
    }

    /**
     * Determine whether the user can view any models.
     * Şikayet listesini (paneli) kimler görebilir?
     */
    public function viewAny(User $user): bool
    {
        // 1. Ana roller (Admin/Kurul/Lider) tüm listeyi görebilir
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri'])) {
            return true;
        }

        // 2. Normal "Kullanıcı" ise, sadece bir 'sikayet' takımına üyeyse görebilir
        // (Superadmin zaten 'before' metodunda true döndüğü için buraya hiç gelmez)
        return $user->takimlar()->where('tur', 'sikayet')->exists();
    }

    /**
     * Determine whether the user can view the model.
     * Belirli bir şikayetin detayını kimler görebilir?
     * === YENİ METOD / GÜNCELLEME ===
     */
    public function view(User $user, MusteriSikayeti $sikayet): bool
    {
        // 1. Superadmin ve Kurul her zaman görebilir
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri'])) {
            return true;
        }

        // 2. Çözüm Lideri, SADECE lideri olduğu takımlara atananları görebilir
        if ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $lideriOlduguTakimIds = $user->lideriOlduguTakimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            return $lideriOlduguTakimIds->contains($sikayet->atanan_cozum_takimi_id);
        }

        // 3. Normal Kullanıcı, SADECE üyesi olduğu takımlara atananları görebilir
        $uyesiOlduguTakimIds = $user->takimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
        return $uyesiOlduguTakimIds->contains($sikayet->atanan_cozum_takimi_id);
    }

    /**
     * Determine whether the user can create models.
     * Yeni şikayet kimler oluşturabilir?
     */
    public function create(User $user): bool
    {
        // Kurul üyesi ise oluşturabilir.
        return $user->hasRole('Müşteri Şikayeti Kurulu');
    }

    /**
     * Determine whether the user can update the model.
     * Belirli bir şikayeti kimler güncelleyebilir?
     */
    public function update(User $user, MusteriSikayeti $sikayet): bool
    {
        // Şikayeti oluşturan kişi ise güncelleyebilir.
        return $user->id === $sikayet->olusturan_kurul_uyesi_id;
    }

    /**
     * Determine whether the user can delete the model.
     * Belirli bir şikayeti kimler silebilir?
     */
    public function delete(User $user, MusteriSikayeti $sikayet): bool
    {
        // Şikayeti oluşturan kişi ise silebilir.
        return $user->id === $sikayet->olusturan_kurul_uyesi_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MusteriSikayeti $sikayet): bool
    {
        return false; // Şimdilik kullanılmıyor
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MusteriSikayeti $sikayet): bool
    {
        return false; // Şimdilik kullanılmıyor
    }
}
