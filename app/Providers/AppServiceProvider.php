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

// === BU MODELLERİ VE OBSERVER'LARI EKLEYİN ===
use App\Models\MusteriSikayeti;
use App\Observers\MusteriSikayetiObserver;
use App\Models\Iaa;
use App\Observers\IaaObserver;
use App\Models\TakimDavetiyesi;
use App\Observers\TakimDavetiyesiObserver;
// === EKLEME SONU ===

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
        // === GÖZLEMCİLERİ (OBSERVERS) BURAYA KAYDEDİN ===
        MusteriSikayeti::observe(MusteriSikayetiObserver::class);
        Iaa::observe(IaaObserver::class);
        TakimDavetiyesi::observe(TakimDavetiyesiObserver::class);
        // === KAYIT SONU ===

        // Mevcut kodunuz buradan devam ediyor
        if (Schema::hasTable('settings')) {
            // Ayarları tek seferde al
            $settings = Setting::all()->keyBy('key');
            
            // Logoyu paylaş
            $logo = $settings->get('site_logo');
            View::share('siteLogo', $logo ? $logo->value : null);

            // Kayıt onay ayarını paylaş
            $kayitOnay = $settings->get('kayit_onay_sistemi');
            View::share('kayitOnaySistemiAktif', $kayitOnay ? (bool)$kayitOnay->value : true);
            
            // YENİ: Para birimlerini bir dizi olarak paylaş
            $paraBirimleriSetting = $settings->get('para_birimleri');
            $paraBirimleri = $paraBirimleriSetting ? explode(',', $paraBirimleriSetting->value) : ['TL', 'USD', 'EUR'];
            View::share('paraBirimleri', $paraBirimleri);

            Carbon::setLocale(config('app.locale'));
        }
    }
}