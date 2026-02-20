<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoginLogController extends Controller
{
    /**
     * Tüm kullanıcıların son giriş özetlerini listeler.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->with([
                'loginActivities' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->with('bolum')
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('admin.logs.login.index', compact('users', 'search'));
    }

    /**
     * Belirli bir kullanıcının geçmiş tüm girişlerini ay ve gün bazında gruplayarak döner.
     */
    public function show(User $user)
    {
        $activities = $user->loginActivities()
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($activity) {
                // Ay bazlı gruplandırma (Örn: Şubat 2026)
                return Carbon::parse($activity->created_at)->translatedFormat('F Y');
            })
            ->map(function ($monthGroup) {
                return $monthGroup->groupBy(function ($activity) {
                    // Gün bazlı gruplandırma (Örn: 13 Şubat Cuma)
                    return Carbon::parse($activity->created_at)->translatedFormat('d F l');
                });
            });

        return view('admin.logs.login.show', compact('user', 'activities'));
    }
}
