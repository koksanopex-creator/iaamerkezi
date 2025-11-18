<?php

namespace App\Observers;

use App\Models\MusteriSikayeti;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\YeniMusteriSikayetiBildirimi;
use App\Notifications\SikayetTakimaAtandiBildirimi;
use Illuminate\Support\Facades\Log; // Hata ayıklama için

use App\Mail\SikayetAtandiMusteriBildirimi; // <-- MÜŞTERİ MAİLİ İÇİN BUNU EKLEYİN
use Illuminate\Support\Facades\Mail;      // <-- MÜŞTERİ MAİLİ İÇİN BUNU EKLEYİN
use App\Models\Takim;                      // <-- MÜŞTERİ MAİLİ İÇİN BUNU EKLEYİN (Eğer zaten yoksa)

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
     * (HEM TAKIMA BİLDİRİM HEM MÜŞTERİYE E-POSTA GÖNDERİR)
     */
    public function updated(MusteriSikayeti $musteriSikayeti): void
    {
        // Sadece 'atanan_cozum_takimi_id' alanı DEĞİŞTİYSE ve boş DEĞİLSE çalış
        if ($musteriSikayeti->isDirty('atanan_cozum_takimi_id') && $musteriSikayeti->atanan_cozum_takimi_id) {
            try {
                // Modelinizdeki ilişkiyi kullanıyoruz
                $takim = $musteriSikayeti->cozumTakimi;

                if ($takim) {
                    
                    // === 1. TAKIM İÇİ BİLDİRİM (SİZİN MEVCUT KODUNUZ) ===
                    $kullanicilar = $takim->uyeler; 
                    if ($kullanicilar && $kullanicilar->isNotEmpty()) { 
                        Notification::send($kullanicilar, new SikayetTakimaAtandiBildirimi($musteriSikayeti));
                    }
                    // === TAKIM BİLDİRİMİ SONU ===

                    
                    // === 2. MÜŞTERİYE E-POSTA BİLDİRİMİ (YENİ EKLEME) ===
                    if ($musteriSikayeti->musteri_iletisim) {
                        Mail::to($musteriSikayeti->musteri_iletisim)
                            ->queue(new SikayetAtandiMusteriBildirimi($musteriSikayeti, $takim));
                    }
                    // === MÜŞTERİ E-POSTA SONU ===

                }
            } catch (\Exception $e) {
                Log::error('Şikayet atama bildirimi/maili gönderilemedi: ' . $e->getMessage());
            }
        }
    }
}