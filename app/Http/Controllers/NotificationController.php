<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Okunmamış bildirim listesini ve sayısını JSON olarak döndürür.
     * === GÜNCELLENDİ ===
     */
    public function index(Request $request)
    {
        // Eğer AJAX veya JSON isteği ise (Zil Dropdown'ı için)
        if ($request->expectsJson() || $request->ajax()) {
            $user = Auth::user();
            
            // İSTEK: SADECE okunmamış bildirimleri al (Okunanlar silinsin talebi gereği)
            $notifications = $user->unreadNotifications()->limit(5)->get();
            
            // Sayaç (badge) için hala SADECE okunmamışların sayısını al
            $unreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'notifications' => $notifications, // Son 5 (hepsi)
                'unread_count' => $unreadCount   // Okunmamış sayısı (sadece sayaç için)
            ]);
        }

        // Normal tarayıcı isteği ise (Tümünü Gör sayfası için)
        return view('notifications.index');
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
        $user = Auth::user();
        
        // Veritabanındaki tüm okunmamış bildirimleri işaretle
        $user->unreadNotifications()->update([
            'read_at' => now(),
            // Eğer ilk okumaysa first_read_at de set edelim (audit tutarlılığı için)
            'first_read_at' => DB::raw('IFNULL(first_read_at, NOW())'),
            'read_count' => DB::raw('read_count + 1')
        ]);

        return response()->json([
            'status' => 'success',
            'unread_count' => 0
        ]);
    }

    /**
     * Bildirimin okundu/okunmadı durumunu tekil olarak değiştirir.
     */
    public function toggleStatus($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        if ($notification->read_at) {
            $notification->update(['read_at' => null]);
            $status = 'unread';
        } else {
            $notification->markAsRead();
            
            // DENETİM: İlk okuma zamanını sabitle ve sayacı artır
            $updateData = [];
            if (!$notification->first_read_at) {
                $updateData['first_read_at'] = now();
            }
            $updateData['read_count'] = ($notification->read_count ?? 0) + 1;
            $notification->update($updateData);

            $status = 'read';
        }

        return response()->json([
            'status' => 'success',
            'new_status' => $status,
            'unread_count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Bildirimi okundu işaretler ve hedef URL'e yönlendirir. (Link tıklamaları için)
     */
    public function readAndRedirect($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // DENETİM: Her tıklamada sayacı artır, ilkse zamanı sabitle
        $updateData = ['read_at' => now()];
        if (!$notification->first_read_at) {
            $updateData['first_read_at'] = now();
        }
        $updateData['read_count'] = ($notification->read_count ?? 0) + 1;
        $notification->update($updateData);

        $url = $notification->data['url'] ?? 
               $notification->data['link'] ?? 
               $notification->data['action_url'] ?? 
               null;

        if (!$url && isset($notification->data['iaa_id'])) {
            $url = route('proje.workspace.show', $notification->data['iaa_id']);
        }

        return redirect($url ?? route('dashboard'));
    }
}