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
            $user = Auth::user();

            // Kullanıcının online durumunu önbellekte tutuyoruz (DB'yi yormamak için)
            $expiresAt = now()->addMinutes(5); // 5 dakika işlem yapmazsa offline sayılır
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);

            // Veritabanındaki son görülme zamanını güncelliyoruz
            // Her saniye DB'ye yazmamak için cache kontrolü (son 1 dakika)
            $cacheKeyLastSeen = 'user-last-seen-update-' . $user->id;
            if (!Cache::has($cacheKeyLastSeen)) {
                $user->last_seen_at = now();
                $user->save();
                Cache::put($cacheKeyLastSeen, true, now()->addMinute());
            }

            // [YENİ] Oturum Süresi Takibi: LoginActivity kaydını güncelle
            if (!session()->has('current_login_id')) {
                // Session'da ID yoksa (örn: güncelleme öncesi girenler için), son login kaydını bulmaya çalış
                // Son 2 saatteki girişlere bakıyoruz (AppServiceProvider ile uyumlu)
                $lastLogin = \App\Models\LoginActivity::where('user_id', $user->id)
                    ->where('created_at', '>', now()->subHours(2))
                    ->latest('id')
                    ->first();

                if ($lastLogin) {
                    session(['current_login_id' => $lastLogin->id]);

                    // Eğer bu kayıt daha önce hiç güncellenmemişse, şimdi başlatalım
                    if (!$lastLogin->last_activity_at) {
                        $lastLogin->update(['last_activity_at' => now()]);
                    }
                }
            }

            if (session()->has('current_login_id')) {
                $loginId = session('current_login_id');
                $cacheKeyLoginActivity = 'login-activity-update-' . $loginId;

                if (!Cache::has($cacheKeyLoginActivity)) {
                    \App\Models\LoginActivity::where('id', $loginId)->update([
                        'last_activity_at' => now()
                    ]);
                    Cache::put($cacheKeyLoginActivity, true, now()->addMinute());
                }
            }
        }
        return $next($request);
    }
}