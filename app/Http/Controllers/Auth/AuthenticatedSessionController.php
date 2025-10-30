<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Setting; // Ekle


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View { 
        
        return view('auth.login'); }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $onayGerekli = (bool) (Setting::where('key', 'kayit_onay_sistemi')->first()?->value ?? true);

        if ($onayGerekli && !Auth::user()->onaylandi_mi) {
            $userEmail = Auth::user()->email;
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors(['email' => 'Hesabınız henüz yönetici tarafından onaylanmamıştır.'])->with('email', $userEmail);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
