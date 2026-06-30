<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bolum; // <-- BÖLÜM MODELİNİ DAHİL ET
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Kayıt formunu gösterir.
     */

    public function create(): View
    {
        // Bölüm listesini veritabanından alıyoruz
        $bolumler = Bolum::orderBy('ad')->get();
        // KVKK Metnini al
        $kvkkText = \App\Models\Setting::where('key', 'kvkk_text')->value('value');

        // ve 'bolumler' değişkeniyle birlikte view'e gönderiyoruz
        return view('auth.register', compact('bolumler', 'kvkkText'));
    }

    /**
     * Gelen kayıt isteğini işler.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'bolum_id' => ['required'], // Sadece bir seçim yapılmasını zorunlu tutuyoruz.
            'kvkk_approval' => ['required', 'accepted'], // <-- KVKK ZORUNLU
        ]);

        // "Diğer" seçeneği seçildiyse bolum_id'yi null yap, değilse gelen ID'yi al.
        $bolumId = $request->bolum_id === 'diger' ? null : $request->bolum_id;

        // Eğer geçerli bir bölüm ID'si geldiyse, veritabanında var mı diye kontrol et.
        if ($bolumId !== null) {
            Validator::make(['bolum_id' => $bolumId], [
                'bolum_id' => ['exists:bolumler,id']
            ])->validate();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bolum_id' => $bolumId, // null veya geçerli bir ID olarak kaydet
        ]);

        event(new Registered($user));

        return redirect('/login')->with('status', 'Kaydınız başarıyla oluşturuldu! Lütfen e-posta adresinize gönderilen doğrulama bağlantısına tıklayarak hesabınızı aktifleştirin. (E-posta ulaşmadıysa, yöneticinizin manuel onayını da bekleyebilirsiniz).');
    }

}