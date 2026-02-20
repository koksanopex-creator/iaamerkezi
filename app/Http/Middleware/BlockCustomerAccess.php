<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BlockCustomerAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Eğer giriş yapmış kullanıcı bir Müşteri ise (customer_id doluysa)
        if (Auth::check() && Auth::user()->customer_id) {
            
            // Sadece izin verilen rotalar (Beyaz Liste)
            $allowedRoutes = [
                'dashboard', 
                'profile.edit', 
                'profile.update', 
                'profile.destroy', 
                'logout',
                'musteri.profil.show', // Kendi şirket profili
                'admin.sikayetler.create', // Şikayet ekleme
                'admin.sikayetler.store',
                'admin.sikayetler.show', // Şikayet detayı
                'public.sikayet.create' // Gerekirse public form
            ];

            // Şu anki rota ismi izin verilenlerden biri değilse
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                // Anasayfaya (Dashboard'a) geri at ve hata ver
                return redirect()->route('dashboard')->with('error', 'Bu sayfaya erişim yetkiniz yok.');
            }
        }

        return $next($request);
    }
}