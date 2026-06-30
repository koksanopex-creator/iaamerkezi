<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KullaniciIstek;
use App\Models\User;
use App\Notifications\KullaniciIstegiNotification;
use Illuminate\Support\Facades\Auth;

class KullaniciIstekController extends Controller
{
    /**
     * Store a newly created request in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'talep_turu' => 'required|in:isim_degisikligi,bolum_degisikligi,email_degisikligi',
            'yeni_deger' => 'required|string|max:255',
            'yeni_bolum_id' => 'nullable|exists:bolumler,id',
        ]);

        $user = Auth::user();

        // Check if there is already a pending request of this type
        $existingRequest = KullaniciIstek::where('user_id', $user->id)
            ->where('talep_turu', $request->talep_turu)
            ->where('durum', 'bekliyor')
            ->first();

        if ($existingRequest) {
            return back()->withInput()->withErrors(['request' => 'Bu türde zaten bekleyen bir talebiniz bulunmaktadır. Lütfen önce onu iptal edin.']);
        }

        if ($request->talep_turu == 'isim_degisikligi') {
            $eskiDeger = $user->name;
        } elseif ($request->talep_turu == 'email_degisikligi') {
            $eskiDeger = $user->email;
        } else {
            $eskiDeger = $user->bolum ? $user->bolum->ad : 'Yok';
        }

        $istek = KullaniciIstek::create([
            'user_id' => $user->id,
            'talep_turu' => $request->talep_turu,
            'eski_deger' => $eskiDeger,
            'yeni_deger' => $request->yeni_deger,
            'yeni_bolum_id' => $request->yeni_bolum_id,
            'durum' => 'bekliyor',
        ]);

        // Notify Superadmins
        $superadmins = User::role('Superadmin')->get();
        foreach ($superadmins as $admin) {
            $admin->notify(new KullaniciIstegiNotification($istek));
        }

        return back()->with('status', 'istek-gonderildi');
    }

    /**
     * Cancel the specified request.
     */
    public function cancel(Request $request, KullaniciIstek $istek)
    {
        // Yetki kontrolü (kendi isteği mi ve durumu bekliyor mu detaylı kontrol)
        if ($istek->user_id !== Auth::id() || $istek->durum !== 'bekliyor') {
            abort(403);
        }

        // Bildirimleri sil
        $superadmins = User::role('Superadmin')->get();
        foreach ($superadmins as $admin) {
            $admin->notifications()
                  ->where('type', KullaniciIstegiNotification::class)
                  ->where('data->istek_id', $istek->id)
                  ->whereNull('read_at')
                  ->delete();
        }

        $istek->delete();

        return back()->with('status', 'istek-iptal-edildi');
    }
}
