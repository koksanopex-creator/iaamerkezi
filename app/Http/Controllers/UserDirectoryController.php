<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $search = $request->input('search');

        // Sorguyu Başlat
        $query = User::query();

        // === KRİTİK KURAL: GİZLİLİK ===
        // Eğer bakan kişi Superadmin DEĞİLSE, Superadminleri listeden gizle.
        if (!$currentUser->hasRole('Superadmin')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Superadmin');
            });
        }

        // Arama Filtresi (İsim, Email, Bölüm)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('bolum', function($b) use ($search) {
                      $b->where('ad', 'like', "%{$search}%");
                  });
            });
        }

        // Sıralama ve Sayfalama
        $users = $query->with('bolum', 'roles')
                       ->orderBy('name')
                       ->paginate(12) // Sayfada 12 kişi
                       ->withQueryString();

        return view('user-directory.index', compact('users', 'search'));
    }
}