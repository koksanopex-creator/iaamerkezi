<?php

namespace App\Observers;

use App\Models\MusteriSikayeti;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\YeniMusteriSikayetiBildirimi;
use App\Notifications\SikayetTakimaAtandiBildirimi;
use Illuminate\Support\Facades\Log; // Hata ayıklama için

class MusteriSikayetiObserver
{
    /**
     * Handle the MusteriSikayeti "created" event.
     * SENARYO: Yeni Müşteri Şikayeti Girildi
     * === GÜNCELLENDİ ===
     */
    public function created(MusteriSikayeti $musteriSikayeti): void
    {
        try {
            // İsteğiniz: "Müşteri Şikayeti Çözüm Lideri", "Müşteri Şikayeti Kurulu" + "Superadmin" + "Yönetim"
            $roller = [
                "Müşteri Şikayeti Çözüm Lideri", 
                "Müşteri Şikayeti Kurulu",
                "Superadmin", // <-- EKLENDİ
                "Yönetim"     // <-- EKLENDİ (İlerde oluşturacağınız rol)
            ];
            
            $kullanicilar = User::role($roller)->get();

            if ($kullanicilar->isNotEmpty()) {
                Notification::send($kullanicilar, new YeniMusteriSikayetiBildirimi($musteriSikayeti));
            }

        } catch (\Exception $e) {
            Log::error('Yeni şikayet bildirimi gönderilemedi: ' . $e->getMessage());
        }
    }

    // ... (updated metodu 1. maddedeki gibi güncellenmiş olmalı) ...

    /**
     * Handle the MusteriSikayeti "updated" event.
     * SENARYO: Müşteri Şikayeti Bir Takıma Atandı
     */
    public function updated(MusteriSikayeti $musteriSikayeti): void
    {
        // Sadece 'atanan_cozum_takimi_id' alanı DEĞİŞTİYSE ve boş DEĞİLSE çalış
        if ($musteriSikayeti->isDirty('atanan_cozum_takimi_id') && $musteriSikayeti->atanan_cozum_takimi_id) {
            try {
                // Modelinizdeki ilişkiyi kullanıyoruz
                $takim = $musteriSikayeti->cozumTakimi;

                if ($takim) {
                    // Takımdaki tüm üyeleri al (User->takimlar ilişkisi)
                    $kullanicilar = $takim->uyeler; 
                    
                    // Ekstra güvenlik kontrolü: $kullanicilar'ın null olmadığını da kontrol edelim
                    if ($kullanicilar && $kullanicilar->isNotEmpty()) { 
                        Notification::send($kullanicilar, new SikayetTakimaAtandiBildirimi($musteriSikayeti));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Şikayet atama bildirimi gönderilemedi: ' . $e->getMessage());
            }
        }
    }
}