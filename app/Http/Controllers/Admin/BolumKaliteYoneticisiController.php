<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SikayetKategori;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class BolumKaliteYoneticisiController extends Controller
{
    /**
     * Atama sayfasını gösterir.
     */
    public function index()
    {
        // Sadece "Bölüm Kalite Yöneticisi" rolüne sahip kullanıcıları getir
        $yoneticiler = User::role('Bölüm Kalite Yöneticisi')->with('yonettigiSikayetKategorileri')->orderBy('name')->get();
        
        // Tüm kategorileri getir
        $kategoriler = SikayetKategori::orderBy('ad')->get();

        return view('admin.bolum_kalite_yoneticileri.index', compact('yoneticiler', 'kategoriler'));
    }

    /**
     * Bir kullanıcıya sorumlu olduğu kategorileri atar/günceller.
     */
    public function update(Request $request, User $user)
    {
        // Güvenlik: Kullanıcının gerçekten bu rolde olduğundan emin olalım (Opsiyonel ama iyi olur)
        if (!$user->hasRole('Bölüm Kalite Yöneticisi')) {
            return back()->with('error', 'Bu kullanıcı "Bölüm Kalite Yöneticisi" rolüne sahip değil.');
        }

        $request->validate([
            'kategoriler' => 'array', // Hiç seçilmezse boş dizi gelir
            'kategoriler.*' => 'exists:sikayet_kategorileri,id',
        ]);

        // sync metodu, seçilenleri ekler, seçilmeyenleri siler. Tam istediğimiz şey.
        $user->yonettigiSikayetKategorileri()->sync($request->input('kategoriler', []));

        return back()->with('success', $user->name . ' için kategori sorumlulukları güncellendi.');
    }
}