<?php
namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage; // <-- Üste ekle
use Illuminate\Support\Facades\Config; // Bunu üste ekleyin


class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {


        
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