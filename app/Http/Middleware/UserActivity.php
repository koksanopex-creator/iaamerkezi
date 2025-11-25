<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class UserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Kullanıcının online durumunu önbellekte tutuyoruz (DB'yi yormamak için)
            $expiresAt = now()->addMinutes(5); // 5 dakika işlem yapmazsa offline sayılır
            Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);

            // Veritabanındaki son görülme zamanını güncelliyoruz
            // Her saniye DB'ye yazmamak için session kontrolü yapabiliriz ama
            // şimdilik basit olması için direkt güncelliyoruz.
            $user = User::find(Auth::user()->id);
            $user->last_seen_at = now();
            $user->save();
        }
        return $next($request);
    }
}