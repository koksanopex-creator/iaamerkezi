<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckReadOnlyMode
{
    /**
     * Gözlemci modunda (Shadowing) olan kullanıcıların yazma işlemlerini engeller.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kullanıcı giriş yapmış mı ve Gözlemci modunda mı?
        if (auth()->check() && auth()->user()->isShadowing()) {
            
            // 2. BEYAZ LİSTE: Gözlemci yönetim rotalarına her zaman izin ver
            // Aksi halde kullanıcı moddan çıkış (stop) yapamaz (POST olduğu için engellenir).
            if ($request->routeIs('observer.*')) {
                return $next($request);
            }

            // 3. SADECE 'GET' ve 'HEAD' isteklerine izin ver (Okuma amaçlı)
            if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
                
                // Livewire isteklerini de düşünmeliyiz. 
                // Livewire POST kullanır ancak bazen sadece 'render' yapar.
                // Fakat güvenlik için en temizi tüm POSTları engellemek.
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Gözlemci modunda işlem yapma yetkiniz bulunmamaktadır. Lütfen kendi hesabınıza geçiş yapın.'
                    ], 403);
                }

                return abort(403, 'Gözlemci modunda (Salt Okunur) işlem yapma yetkiniz bulunmamaktadır.');
            }
        }

        return $next($request);
    }
}
