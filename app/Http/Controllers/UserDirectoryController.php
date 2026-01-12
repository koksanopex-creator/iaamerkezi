<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserDirectoryController extends Controller
{
    /**
     * KULLANICI LİSTESİ (Rehber Sayfası)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $search = $request->input('search');

        // Sorguyu Başlat
        $query = User::query();

        // === GÜVENLİK FİLTRESİ: LİSTELEME ===
        // Eğer bakan kişi Superadmin DEĞİLSE filtreleri uygula
        if (!$currentUser->hasRole('Superadmin')) {
            
            // 1. ADIM: Müşterileri Gizle (Sadece Personel Gelsin)
            // User modelinde scopePersonel varsa onu da kullanabilirdik ama garanti olsun diye manuel yazdım.
            $query->where('is_personnel', true);

            // 2. ADIM: Özel Rolleri Gizle
            // Bu roller sadece Superadmin'e görünür, diğer herkes için gizlidir.
            $gizliRoller = [
                'Superadmin', 
                'Yonetim', 
                'Dış Avukat', 
                'Arabuluculuk Finans', 
                'Hukuk Yöneticisi',
                'Hukuk Admini' // İstersen bunu da ekleyebilirsin
            ];

            $query->whereDoesntHave('roles', function ($q) use ($gizliRoller) {
                $q->whereIn('name', $gizliRoller);
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
                        ->paginate(12)
                        ->withQueryString();

        return view('user-directory.index', compact('users', 'search'));
    }

    /**
     * PROFİL DETAY GÖRÜNTÜLEME (URL Koruması İçin)
     * Örn: /kullanici-profil/{id} linkine gidildiğinde çalışır.
     */
    public function show($id)
    {
        $targetUser = User::findOrFail($id); // Aranan kullanıcı
        $currentUser = Auth::user();         // Giriş yapan kullanıcı

        // === GÜVENLİK DUVARI: ERİŞİM ENGELLEME ===

        // 1. KURAL: Superadmin herkesi görebilir.
        if ($currentUser->hasRole('Superadmin')) {
            // View dosyanın adı neyse onu yazmalısın. Genelde 'profile.show' veya 'user-directory.show' olur.
            // Laravel Jetstream kullanıyorsan ve oraya yönlendiriyorsan bu metodun dışına çıkabilir.
            // Ancak kendi özel sayfan varsa burası çalışır.
            return view('profile.show', ['user' => $targetUser]); 
        }

        // 2. KURAL: Kişi KENDİ profilini her zaman görebilir.
        if ($currentUser->id == $targetUser->id) {
            return view('profile.show', ['user' => $targetUser]);
        }

        // 3. KURAL: Hedef kişi Müşteri ise (is_personnel = false) GÖSTERME
        if ($targetUser->is_personnel == false) {
            abort(404); // Sanki böyle biri yokmuş gibi davran
        }

        // 4. KURAL: Hedef kişi "Yasaklı Roller"den birine sahipse GÖSTERME
        $yasakliRoller = [
            'Superadmin', 
            'Yonetim', 
            'Dış Avukat', 
            'Arabuluculuk Finans', 
            'Hukuk Yöneticisi'
        ];

        if ($targetUser->hasRole($yasakliRoller)) {
            abort(403, 'Bu kullanıcının profili gizlidir.');
        }

        // Her şey temizse profili göster
        return view('profile.show', ['user' => $targetUser]);
    }
}