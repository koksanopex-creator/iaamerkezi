<?php

namespace App\Policies;

use App\Models\MusteriSikayeti;
use App\Models\User;
use App\Models\Iaa; // <-- EKLENDİ (Squad kontrolü için)
use Illuminate\Auth\Access\Response;

class MusteriSikayetiPolicy
{
    /**
     * Perform pre-authorization checks.
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
     * Determine whether the user can view any models.
     * Şikayet listesini (paneli) kimler görebilir?
     */
    public function viewAny(User $user): bool
    {
        // 1. Ana roller (Kurul / Lider / Bölüm Yöneticisi)
        // 'Bölüm Kalite Yöneticisi' BURAYA EKLENDİ
        if ($user->hasRole(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi', 'Yonetim'])) {
            return true;
        }

        // 2. Normal "Kullanıcı" ise, sadece bir 'sikayet' takımına üyeyse görebilir
        if ($user->takimlar()->where('tur', 'sikayet')->exists()) {
            return true;
        }

        // 3. Squad (Geçici Görev) Üyesi ise görebilir (Cihangir için)
        // Proje ekibinde onaylanmış bir kaydı varsa listeyi görmeye hakkı vardır.
        // (Liste içinde zaten sadece kendi yetkili olduklarını görecek şekilde filtreleme yapıyoruz)
        if ($user->gorevliOlduguProjeler()->wherePivot('durum', 'onaylandi')->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     * Belirli bir şikayetin detayını kimler görebilir?
     */
    public function view(User $user, MusteriSikayeti $sikayet): bool
    {
        // 1. Kurul üyeleri her şikayeti görür
        if ($user->hasRole('Müşteri Şikayeti Kurulu', 'Yonetim')) {
            return true;
        }

        // 2. Bölüm Kalite Yöneticisi (SERKAN TÖLEK İÇİN EKLENDİ)
        // Sadece sorumlu olduğu kategorideki şikayetleri görebilir.
        if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
             $kategoriId = $sikayet->sikayet_kategorisi_id;
             // Kullanıcının sorumlu olduğu kategoriler arasında bu ID var mı?
             return $user->yonettigiSikayetKategorileri->contains($kategoriId);
        }

        // 3. Çözüm Lideri, SADECE lideri olduğu takımlara atananları görebilir
        if ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $lideriOlduguTakimIds = $user->lideriOlduguTakimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            // Kendi takımına atanmışsa GÖRÜR
            if ($lideriOlduguTakimIds->contains($sikayet->atanan_cozum_takimi_id)) {
                return true;
            }
        }

        // 4. Normal Kullanıcı, SADECE üyesi olduğu takımlara atananları görebilir
        $uyesiOlduguTakimIds = $user->takimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
        if ($uyesiOlduguTakimIds->contains($sikayet->atanan_cozum_takimi_id)) {
            return true;
        }

        // 5. SQUAD (GEÇİCİ GÖREV) ÜYESİ (CİHANGİR İÇİN EKLENDİ)
        // Eğer bu şikayet bir projeye (Iaa) dönüşmüşse ve kullanıcı o projenin ekibindeyse
        if ($sikayet->iaa_id) {
            // İaa modelini bulalım (Sikayet modelinde relation varsa $sikayet->iaaProjesi kullanılabilir)
            // Ancak ilişki adını tam bilmediğimiz için garanti olsun diye manuel sorguluyoruz:
            $iaa = Iaa::find($sikayet->iaa_id);
            if ($iaa && $iaa->projeEkibi()->where('user_id', $user->id)->wherePivot('durum', 'onaylandi')->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Kurul üyesi oluşturabilir.
        return $user->hasRole('Müşteri Şikayeti Kurulu');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MusteriSikayeti $sikayet): bool
    {
        // Şikayeti oluşturan kişi güncelleyebilir.
        return $user->id === $sikayet->olusturan_kurul_uyesi_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MusteriSikayeti $sikayet): bool
    {
        // Şikayeti oluşturan kişi silebilir.
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