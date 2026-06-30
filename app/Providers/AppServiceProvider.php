<?php
namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

// === EKLENEN: LOGİN VE LOG İŞLEMLERİ İÇİN GEREKLİ SINIFLAR ===
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Models\LoginActivity;
use App\Models\MusteriLog; // <--- YENİ EKLENDİ
// ===================================================

use App\Models\MusteriSikayeti;
use App\Observers\MusteriSikayetiObserver;
use App\Models\Iaa;
use App\Observers\IaaObserver;
use App\Models\TakimDavetiyesi;
use App\Observers\TakimDavetiyesiObserver;
use App\Models\Customer;
use App\Models\SikayetIadesi;
use App\Models\User;
use App\Observers\CustomerObserver;
use App\Observers\ContactObserver;
use App\Observers\SikayetIadesiObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // === GİRİŞ HAREKETLERİNİ KAYDET (LOGIN HISTORY & MUSTERI LOG) ===
        Event::listen(Login::class, function ($event) {
            // Sistem sağlığı simülasyonu ise loglama yapma
            if (request()->header('X-Is-Simulation')) {
                return;
            }

            $user = $event->user;

            // 1. Mevcut Sistem Logu (Genel)
            // Eğer kullanıcının çok yakın zamanda (son 2 saat) bir girişi varsa ve aktivite gösterdiyse
            // Yeni bir kayıt oluşturmak yerine mevcut oturumu devam ettiriyoruz.
            $existingActivity = LoginActivity::where('user_id', $user->id)
                ->where('created_at', '>', now()->subHours(2))
                ->latest('id')
                ->first();

            if ($existingActivity) {
                $loginActivity = $existingActivity;
            } else {
                $loginActivity = LoginActivity::create([
                    'user_id' => $user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                    'last_activity_at' => now(),
                ]);
            }

            session(['current_login_id' => $loginActivity->id]);

            // 2. YENİ: Müşteri Logu (Eğer giren kişi bir firma yetkilisi ise)
            if ($user->customer_id) {
                MusteriLog::add($user->customer_id, 'Sisteme Giriş', $user->name . ' (Yetkili) sisteme giriş yaptı.');
            }
        });

        // === YENİ: E-POSTA DOĞRULAMA OTOMATİK ONAY ===
        Event::listen(\Illuminate\Auth\Events\Verified::class, \App\Listeners\AutoApproveVerifiedUser::class);
        // ================================================================

        // === GÖZLEMCİLER (OBSERVERS) ===
        MusteriSikayeti::observe(MusteriSikayetiObserver::class);
        Iaa::observe(IaaObserver::class);
        TakimDavetiyesi::observe(TakimDavetiyesiObserver::class);
        Customer::observe(CustomerObserver::class);
        User::observe(ContactObserver::class);
        SikayetIadesi::observe(SikayetIadesiObserver::class);
        // ===============================

        // Ayarlar ve View Paylaşımları
        if (Schema::hasTable('settings')) {
            $settings = Setting::all()->keyBy('key');

            $logo = $settings->get('site_logo');
            View::share('siteLogo', $logo ? $logo->value : null);

            $kayitOnay = $settings->get('kayit_onay_sistemi');
            View::share('kayitOnaySistemiAktif', $kayitOnay ? (bool) $kayitOnay->value : true);

            $paraBirimleriSetting = $settings->get('para_birimleri');
            $paraBirimleri = $paraBirimleriSetting ? explode(',', $paraBirimleriSetting->value) : ['TL', 'USD', 'EUR'];
            View::share('paraBirimleri', $paraBirimleri);

            Carbon::setLocale(config('app.locale'));
        }

        // === GÖZLEMCİ (SHADOWING) BLADE DİREKTİFLERİ ===
        \Illuminate\Support\Facades\Blade::if('readonly', function () {
            return auth()->check() && auth()->user()->isShadowing();
        });

        \Illuminate\Support\Facades\Blade::if('notreadonly', function () {
            return auth()->check() && !auth()->user()->isShadowing();
        });

        // === MICROSOFT GRAPH MAIL DRIVER ===
        Mail::extend('microsoft-graph', function () {
            return new \App\Mail\Transports\MicrosoftGraphTransport(
                config('services.microsoft.tenant_id'),
                config('services.microsoft.client_id'),
                config('services.microsoft.client_secret'),
                config('services.microsoft.from_address'),
            );
        });
        // ===================================
    }
}