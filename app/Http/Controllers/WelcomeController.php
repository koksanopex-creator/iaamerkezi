<?php

namespace App\Http\Controllers;

use App\Models\Iaa; // Iaa modelini kullanacağımızı belirtiyoruz
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Welcome sayfasını gösterir ve gerekli istatistikleri hesaplar.
     */
    public function index()
    {
        // Tüm İAA'ların sayısını al
        $toplamOneri = Iaa::count();

        // "Atandı" durumundaki İAA'ların sayısını al
        $hayataGecen = Iaa::where('durum', 'Atandı')->count();

        // Veritabanındaki en son öneriyi tarihine göre bulup alıyoruz.
        $sonOneri = Iaa::latest()->first();

        // Verileri view'e gönder
        return view('welcome', compact('toplamOneri', 'hayataGecen', 'sonOneri'));
    }
}