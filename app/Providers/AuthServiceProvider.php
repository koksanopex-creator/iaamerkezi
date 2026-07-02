<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Iaa;
use App\Policies\IaaPolicy;
use App\Models\MusteriSikayeti;      // <-- YENİ EKLENDİ
use App\Policies\MusteriSikayetiPolicy; // <-- YENİ EKLENDİ
use App\Models\SikayetHatirlatma;
use App\Policies\SikayetHatirlatmaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Iaa::class => IaaPolicy::class, // MEVCUT POLİTİKAN (KALIYOR)
        MusteriSikayeti::class => MusteriSikayetiPolicy::class, 
        SikayetHatirlatma::class => SikayetHatirlatmaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Senin mevcut Superadmin kuralın burada kalıyor.
        // Bu kural, TÜM yetki kontrollerinden önce çalışır ve Superadmin'e her izni verir.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Superadmin') ? true : null;
        });

        Gate::define('viewPulse', function ($user) {
            return $user->hasRole('Superadmin');
        });
    }
}

