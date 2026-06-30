<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class MailLogController extends Controller
{
    /**
     * Yetki kontrolü: Bu sayfaya erişim var mı?
     */
    private function authorizeAccess()
    {
        $user = Auth::user();

        // Superadmin ve Yonetim her zaman erişebilir
        if ($user->hasAnyRole(['Superadmin', 'Yonetim'])) {
            return true;
        }

        // Direktör: Yönettiği bölümler varsa erişebilir
        if ($user->hasRole('Direktör')) {
            $bolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            return count($bolumIds) > 0;
        }

        // Bölüm Lideri: Kendi bölümü varsa erişebilir
        if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
            return true;
        }

        // Yetki matrisinden kontrol
        $allowedRoles = json_decode(Setting::where('key', 'mail_log_allowed_roles')->value('value') ?? '[]', true);
        $allowedUsers = json_decode(Setting::where('key', 'mail_log_allowed_users')->value('value') ?? '[]', true);

        if (in_array($user->id, $allowedUsers)) {
            return true;
        }

        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        abort(403, 'Mail bildirim loglarını görüntüleme yetkiniz bulunmamaktadır.');
    }

    /**
     * Bölüm bazlı scoping uygula
     */
    private function applyScopeToQuery($query)
    {
        $user = Auth::user();

        // Superadmin ve Yonetim her şeyi görür
        if ($user->hasAnyRole(['Superadmin', 'Yonetim'])) {
            return $query;
        }

        // Direktör: Yönettiği bölümler
        if ($user->hasRole('Direktör')) {
            $bolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            return $query->whereIn('bolum_id', $bolumIds);
        }

        // Bölüm Lideri: Kendi bölümü
        if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
            return $query->where('bolum_id', $user->bolum_id);
        }

        // Yetki matrisinden izin verilen roller/kişiler — tüm logları görebilir
        return $query;
    }

    /**
     * Mail log listesi
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = MailLog::with('bolum', 'resolver')->latest();

        // Bölüm bazlı scoping
        $query = $this->applyScopeToQuery($query);

        // === FİLTRELER ===

        // Tarih filtresi
        if ($request->filled('tarih_baslangic')) {
            $query->whereDate('created_at', '>=', $request->tarih_baslangic);
        }
        if ($request->filled('tarih_bitis')) {
            $query->whereDate('created_at', '<=', $request->tarih_bitis);
        }

        // Durum filtresi
        if ($request->filled('durum')) {
            if ($request->durum === 'cozulmedi') {
                $query->whereNull('resolved_at');
            } elseif ($request->durum === 'cozuldu') {
                $query->whereNotNull('resolved_at');
            }
        }

        // Arama (kaynak işlem veya hata mesajı)
        if ($request->filled('arama')) {
            $arama = $request->arama;
            $query->where(function ($q) use ($arama) {
                $q->where('source_action', 'like', "%{$arama}%")
                  ->orWhere('error_message', 'like', "%{$arama}%");
            });
        }

        // İstatistikler (filtrelenmemiş, scope'lu)
        $statsQuery = MailLog::query();
        $statsQuery = $this->applyScopeToQuery($statsQuery);
        $stats = [
            'toplam' => (clone $statsQuery)->count(),
            'cozulmedi' => (clone $statsQuery)->whereNull('resolved_at')->count(),
            'cozuldu' => (clone $statsQuery)->whereNotNull('resolved_at')->count(),
        ];

        $logs = $query->paginate(10)->withQueryString();

        return view('admin.mail-logs.index', compact('logs', 'stats'));
    }

    /**
     * Mail bildirimini tekrar göndermeyi dener
     */
    public function retry($id)
    {
        $this->authorizeAccess();

        $log = MailLog::findOrFail($id);

        // Zaten çözülmüş mü?
        if ($log->isResolved()) {
            return back()->with('info', 'Bu bildirim zaten başarıyla gönderilmiş.');
        }

        // Bildirim sınıfı ve verisi var mı?
        if (!$log->notification_class || !$log->notification_data) {
            return back()->with('error', 'Bu kayıt için tekrar gönderim bilgisi bulunamadı. Manuel bildirim yapmanız gerekebilir.');
        }

        try {
            $notificationClass = $log->notification_class;
            $notificationData = $log->notification_data;

            // Bildirim sınıfı mevcut mu?
            if (!class_exists($notificationClass)) {
                return back()->with('error', 'Bildirim sınıfı bulunamadı: ' . $notificationClass);
            }

            // Alıcıları belirle (notification_data içindeki recipient_ids)
            $recipientIds = $notificationData['recipient_ids'] ?? [];
            $recipients = User::whereIn('id', $recipientIds)->get();

            if ($recipients->isEmpty()) {
                return back()->with('error', 'Gönderilecek alıcı bulunamadı.');
            }

            // Bildirimi yeniden oluştur
            $params = $notificationData['params'] ?? [];
            $notification = new $notificationClass(...array_values($params));

            // Gönder
            Notification::send($recipients, $notification);

            // Başarılı — log'u çözüldü olarak işaretle
            $log->update([
                'retry_count' => $log->retry_count + 1,
                'resolved_at' => now(),
                'resolved_by' => Auth::id(),
            ]);

            return back()->with('success', 'Bildirim başarıyla tekrar gönderildi ve log çözüldü olarak işaretlendi.');

        } catch (\Exception $e) {
            // Tekrar başarısız
            $log->increment('retry_count');
            $log->update(['error_message' => $log->error_message . "\n[Tekrar Deneme " . now()->format('d.m.Y H:i') . "] " . $e->getMessage()]);

            Log::error('Mail log tekrar gönderim hatası (ID: ' . $log->id . '): ' . $e->getMessage());
            return back()->with('error', 'Tekrar gönderim başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Log'u manuel olarak çözüldü olarak işaretle
     */
    public function markResolved($id)
    {
        $this->authorizeAccess();

        $log = MailLog::findOrFail($id);

        if ($log->isResolved()) {
            return back()->with('info', 'Bu kayıt zaten çözülmüş.');
        }

        $log->update([
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Log kaydı çözüldü olarak işaretlendi.');
    }
}
