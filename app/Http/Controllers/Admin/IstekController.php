<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KullaniciIstek;
use App\Models\User;
use App\Notifications\KullaniciIstegiSonucNotification;
use Illuminate\Support\Facades\Auth;

class IstekController extends Controller
{
    /**
     * Display a listing of the requests.
     */
    public function index()
    {
        $istekler = KullaniciIstek::with('user', 'admin', 'yeniBolum')
            ->latest()
            ->paginate(15);

        return view('admin.istekler.index', compact('istekler'));
    }

    /**
     * Approve the request.
     */
    public function approve(Request $request, KullaniciIstek $istek)
    {
        $request->validate([
            'admin_notu' => 'nullable|string|max:1000'
        ]);

        $istek->durum = 'onaylandi';
        $istek->admin_id = Auth::id();
        $istek->admin_notu = $request->admin_notu;
        $istek->save();

        // Kullanıcı bilgisini güncelle
        $user = $istek->user;
        if ($istek->talep_turu == 'isim_degisikligi') {
            $user->name = $istek->yeni_deger;
        } elseif ($istek->talep_turu == 'bolum_degisikligi') {
            $user->bolum_id = $istek->yeni_bolum_id;
        } elseif ($istek->talep_turu == 'email_degisikligi') {
            $user->email = $istek->yeni_deger;
            $user->email_verified_at = null; // Email değişince doğrulamayı sıfırla
        }
        $user->save();

        // Kullanıcıya sonucu bildir
        $user->notify(new KullaniciIstegiSonucNotification($istek));

        return back()->with('success', 'İstek onaylandı ve kullanıcı bilgileri güncellendi.');
    }

    /**
     * Reject the request.
     */
    public function reject(Request $request, KullaniciIstek $istek)
    {
        $request->validate([
            'admin_notu' => 'nullable|string|max:1000'
        ]);

        $istek->durum = 'reddedildi';
        $istek->admin_id = Auth::id();
        $istek->admin_notu = $request->admin_notu;
        $istek->save();

        // Kullanıcıya sonucu bildir
        $istek->user->notify(new KullaniciIstegiSonucNotification($istek));

        return back()->with('success', 'İstek reddedildi.');
    }
}
