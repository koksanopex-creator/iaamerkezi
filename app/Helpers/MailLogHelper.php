<?php

namespace App\Helpers;

use App\Models\MailLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MailLogHelper
{
    /**
     * Başarısız mail bildirimini merkezi log tablosuna kaydeder.
     *
     * @param Model|null    $loggable          İlişkili kayıt (MusteriSikayeti, DisciplinaryCase, Iaa vb.)
     * @param string        $sourceAction      İşlem açıklaması ("Yeni Şikayet Kaydı", "Tutanak Oluşturma" vb.)
     * @param array|string  $recipients        Alıcı listesi (isim veya e-posta)
     * @param string        $errorMessage      Hata mesajı
     * @param string|null   $notificationClass Bildirim sınıfı (tekrar dene için)
     * @param array|null    $notificationData  Bildirim parametreleri (tekrar dene için)
     * @param int|null      $bolumId           İlgili bölüm ID (scoping için)
     */
    public static function logFailure(
        ?Model $loggable,
        string $sourceAction,
        $recipients,
        string $errorMessage,
        ?string $notificationClass = null,
        ?array $notificationData = null,
        ?int $bolumId = null
    ): ?MailLog {
        try {
            // Alıcıları array'e çevir
            if (is_string($recipients)) {
                $recipients = [$recipients];
            }

            // Collection ise array'e çevir
            if ($recipients instanceof \Illuminate\Support\Collection) {
                $recipients = $recipients->map(function ($r) {
                    if (is_object($r) && isset($r->name)) {
                        return $r->name . ' (' . ($r->email ?? '') . ')';
                    }
                    return (string) $r;
                })->toArray();
            }

            // Eğer array içinde object varsa stringe çevir
            if (is_array($recipients)) {
                $recipients = array_map(function ($r) {
                    if (is_object($r) && isset($r->name)) {
                        return $r->name . ' (' . ($r->email ?? '') . ')';
                    }
                    return (string) $r;
                }, $recipients);
            }

            // Kaynak sayfa URL'sini al
            $sourcePage = null;
            try {
                $sourcePage = request()->fullUrl();
            } catch (\Exception $e) {
                $sourcePage = 'Bilinmiyor';
            }

            return MailLog::create([
                'loggable_type' => $loggable ? get_class($loggable) : null,
                'loggable_id' => $loggable ? $loggable->id : null,
                'source_page' => $sourcePage,
                'source_action' => $sourceAction,
                'recipients' => $recipients,
                'error_message' => $errorMessage,
                'notification_class' => $notificationClass,
                'notification_data' => $notificationData,
                'bolum_id' => $bolumId,
            ]);
        } catch (\Exception $e) {
            // Log tablosuna yazma bile başarısız olursa sadece laravel loguna yaz
            Log::error('MailLogHelper::logFailure hatası: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Çözülmemiş mail log sayısını döndürür (menü badge için).
     * Kullanıcının yetkisine göre filtreleme yapar.
     */
    public static function getUnresolvedCount(?\App\Models\User $user = null): int
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return 0;
        }

        $query = MailLog::unresolved();

        // Superadmin ve Yonetim her şeyi görür
        if ($user->hasAnyRole(['Superadmin', 'Yonetim'])) {
            return $query->count();
        }

        // Direktör: Yönettiği bölümlerin logları
        if ($user->hasRole('Direktör')) {
            $bolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();
            return $query->whereIn('bolum_id', $bolumIds)->count();
        }

        // Bölüm Lideri: Kendi bölümünün logları
        if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
            return $query->where('bolum_id', $user->bolum_id)->count();
        }

        // Diğer roller: Yetki matrisinden kontrol
        $allowedRoles = json_decode(\App\Models\Setting::where('key', 'mail_log_allowed_roles')->value('value') ?? '[]', true);
        $allowedUsers = json_decode(\App\Models\Setting::where('key', 'mail_log_allowed_users')->value('value') ?? '[]', true);

        if (in_array($user->id, $allowedUsers)) {
            return $query->count();
        }

        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return $query->count();
            }
        }

        return 0;
    }
}
