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
            $user = $event->user;

            // 1. Mevcut Sistem Logu (Genel)
            LoginActivity::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            // 2. YENİ: Müşteri Logu (Eğer giren kişi bir firma yetkilisi ise)
            if ($user->customer_id) {
                MusteriLog::add($user->customer_id, 'Sisteme Giriş', $user->name . ' (Yetkili) sisteme giriş yaptı.');
            }
        });
        // ================================================================

        // === GÖZLEMCİLER (OBSERVERS) ===
        MusteriSikayeti::observe(MusteriSikayetiObserver::class);
        Iaa::observe(IaaObserver::class);
        TakimDavetiyesi::observe(TakimDavetiyesiObserver::class);
        // ===============================

        // Ayarlar ve View Paylaşımları
        if (Schema::hasTable('settings')) {
            $settings = Setting::all()->keyBy('key');
            
            $logo = $settings->get('site_logo');
            View::share('siteLogo', $logo ? $logo->value : null);

            $kayitOnay = $settings->get('kayit_onay_sistemi');
            View::share('kayitOnaySistemiAktif', $kayitOnay ? (bool)$kayitOnay->value : true);
            
            $paraBirimleriSetting = $settings->get('para_birimleri');
            $paraBirimleri = $paraBirimleriSetting ? explode(',', $paraBirimleriSetting->value) : ['TL', 'USD', 'EUR'];
            View::share('paraBirimleri', $paraBirimleri);

            Carbon::setLocale(config('app.locale'));
        }
    }
}