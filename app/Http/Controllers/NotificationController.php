<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Okunmamış bildirim listesini ve sayısını JSON olarak döndürür.
     * === GÜNCELLENDİ ===
     */
    public function index()
    {
        $user = Auth::user();
        
        // İSTEK: Sadece okunmamışları değil, en son 5 bildirimi (okunmuş/okunmamış) al
        $notifications = $user->notifications()->limit(5)->get();
        
        // Sayaç (badge) için hala SADECE okunmamışların sayısını al
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications, // Son 5 (hepsi)
            'unread_count' => $unreadCount   // Okunmamış sayısı (sadece sayaç için)
        ]);
    }

    /**
     * Sadece okunmamış bildirim sayısını döndürür (Polling için).
     * Her 30 saniyede bir bu çağrılır.
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Tüm bildirimleri okundu olarak işaretler.
     * Kullanıcı zil listesini açtığında bu çağrılır.
     */
    public function markAsRead(Request $request)
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['status' => 'success']);
    }
}