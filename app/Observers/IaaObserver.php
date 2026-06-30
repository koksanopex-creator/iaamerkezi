<?php

namespace App\Observers;

use App\Models\Iaa;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\IaaHavuzaEklendi;
use Illuminate\Support\Facades\Log;

class IaaObserver
{
    /**
     * Handle the Iaa "updated" event.
     * SENARYO: IAA Projesi Havuza Eklendiğinde
     */
    public function updated(Iaa $iaa): void
    {
        // Sadece 'durum' alanı DEĞİŞTİYSE ve yeni durum 'Havuzda' ise çalış
        // Bu, IaaYonetimController@onayla metodundan tetiklenecek
        if ($iaa->isDirty('durum') && $iaa->durum === 'Havuzda') {
            try {
                // İsteğiniz: "tüm kullanıcılara" (Müşteriler hariç, sadece personel)
                $kullanicilar = User::personel()->where('onaylandi_mi', true)->get();

                if ($kullanicilar->isNotEmpty()) {
                    Notification::send($kullanicilar, new IaaHavuzaEklendi($iaa));
                }

            } catch (\Exception $e) {
                Log::error('IAA havuza eklendi bildirimi gönderilemedi: ' . $e->getMessage());
                \App\Helpers\MailLogHelper::logFailure(
                    $iaa,
                    'İAA Havuza Eklendi Bildirimi',
                    User::personel()->where('onaylandi_mi', true)->get(),
                    $e->getMessage(),
                    null,
                    null,
                    $iaa->bolum_id
                );
            }
        }
    }
}