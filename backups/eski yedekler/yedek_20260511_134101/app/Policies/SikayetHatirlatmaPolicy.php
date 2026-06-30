<?php

namespace App\Policies;

use App\Models\SikayetHatirlatma;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SikayetHatirlatmaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'Superadmin', 
            'Yonetim', 
            'Müşteri Şikayeti Kurulu', 
            'Müşteri Temsilcisi', 
            'Müşteri Şikayeti Çözüm Lideri', 
            'Bölüm Kalite Yöneticisi', 
            'Direktör', 
            'Bölüm Lideri'
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SikayetHatirlatma $sikayetHatirlatma): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Temsilcisi']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SikayetHatirlatma $sikayetHatirlatma): bool
    {
        return $user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Temsilcisi']) || $user->id === $sikayetHatirlatma->gonderen_user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SikayetHatirlatma $sikayetHatirlatma): bool
    {
        return $user->hasRole(['Superadmin', 'Yonetim']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SikayetHatirlatma $sikayetHatirlatma): bool
    {
        return $user->hasRole(['Superadmin', 'Yonetim']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SikayetHatirlatma $sikayetHatirlatma): bool
    {
        return $user->hasRole(['Superadmin']);
    }
}
