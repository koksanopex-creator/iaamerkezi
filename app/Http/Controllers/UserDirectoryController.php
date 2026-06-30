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
        $search = $request->input('search');
        $activeTab = $request->input('tab', 'personel');

        // Müşteri listesini kimler görebilir?
        $canSeeCustomers = Auth::user()->hasRole([
            'Superadmin', 
            'Yonetim', 
            'Müşteri Şikayeti Kurulu', 
            'Bölüm Kalite Yöneticisi', 
            'Bölüm Lideri', 
            'Müşteri Şikayeti Çözüm Lideri', 
            'Direktör', 
            'Hukuk Admini', 
            'Hukuk Yöneticisi'
        ]);

        $isMaviYaka = $request->boolean('mavi_yaka');
        $bolumId = $request->input('bolum_id');

        $query = User::with(['bolum', 'roles']);

        // SEKMEYE GÖRE FİLTRELE
        if ($activeTab === 'musteri' && $canSeeCustomers) {
            $query->musteriler();
        } else {
            $activeTab = 'personel'; // Yetkisi yoksa veya personel seçiliyse
            $query->personel();
            
            // Mavi yaka filtresi durumu (Aktifse sadece mavi yaka, değilse sadece beyaz yaka)
            $query->where('is_mavi_yaka', $isMaviYaka);
        }

        // Bölüm Filtresi
        if ($bolumId) {
            $query->where('bolum_id', $bolumId);
        }

        // Personel sekmesindeyken bazı rolleri gizle (Yetkisizler için)
        if ($activeTab === 'personel' && !Auth::user()->hasRole('Superadmin')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['Superadmin']);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('bolum', function ($bq) use ($search) {
                        $bq->where('ad', 'LIKE', "%{$search}%");
                    });
            });
        }

        $users = $query->orderBy('name')->paginate(12)->withQueryString();
        $totalUserCount = $users->total();
        
        // Bölümleri getir (Filtre için)
        $bolumler = \App\Models\Bolum::orderBy('ad')->get();

        return view('user-directory.index', compact('users', 'search', 'totalUserCount', 'activeTab', 'canSeeCustomers', 'isMaviYaka', 'bolumId', 'bolumler'));
    }
}