<?php

namespace App\Observers;

use App\Models\MusteriSikayeti;
use App\Models\Setting;
use App\Models\SikayetKategori;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\YeniMusteriSikayetiBildirimi;
use App\Notifications\SikayetTakimaAtandiBildirimi;
use Illuminate\Support\Facades\Log;

use App\Mail\SikayetAtandiMusteriBildirimi;
use Illuminate\Support\Facades\Mail;
use App\Models\Takim;

class MusteriSikayetiObserver
{
    /**
     * Handle the MusteriSikayeti "created" event.
     * SENARYO: Yeni Müşteri Şikayeti Girildi
     * === BİRLEŞTİRİLMİŞ AKILLI BİLDİRİM MANTIĞI ===
     */
    public function created(MusteriSikayeti $sikayet): void
    {
        try {
            // 1. Ayarları ve Alıcı Listesini Hazırla
            $settings = Setting::all()->keyBy('key');
            $recipients = collect();

            // 2. Rollerden Gelenler (Superadmin, Çözüm Lideri vb.)
            $roleIdsValue = $settings->get('sikayet_notify_role_ids')?->value;
            if (!empty($roleIdsValue)) {
                $roleIds = explode(',', $roleIdsValue);
                $usersFromRoles = User::whereHas('roles', function ($query) use ($roleIds) {
                    $query->whereIn('id', $roleIds);
                })->get();
                $recipients = $recipients->merge($usersFromRoles);
            }

            // 3. Özel Seçilmiş Kullanıcılar
            $userIdsValue = $settings->get('sikayet_notify_user_ids')?->value;
            if (!empty($userIdsValue)) {
                $userIds = explode(',', $userIdsValue);
                $usersFromIds = User::whereIn('id', $userIds)->get();
                $recipients = $recipients->merge($usersFromIds);
            }

            // ==========================================================
            // 4. KRİTİK KISIM: KATEGORİ -> BÖLÜM -> BÖLÜM LİDERİ
            // ==========================================================
            if ($sikayet->sikayet_kategorisi_id) {
                // AYAR KONTROLÜ: Bölüm Lideri Bildirimi Açık mı?
                $notifyBolumLideri = $settings->get('sikayet_notify_bolum_lideri')?->value;

                if ($notifyBolumLideri == '1') { // Sadece açıksa çalış
                    $kategori = SikayetKategori::find($sikayet->sikayet_kategorisi_id);

                    if ($kategori && $kategori->bolum_id) {
                        // Bölüm Liderlerini Bul
                        $bolumLiderleri = User::role('Bölüm Lideri')
                            ->where('bolum_id', $kategori->bolum_id)
                            ->get();

                        $recipients = $recipients->merge($bolumLiderleri);
                    }
                }
            }
            // ==========================================================

            // Tekilleştir ve Gönder (Zil + Mail)
            $finalRecipients = $recipients->unique('id');

            if ($finalRecipients->isNotEmpty()) {
                Notification::send($finalRecipients, new YeniMusteriSikayetiBildirimi($sikayet));
            }

            // 5. Manuel E-postalar (Sadece Mail)
            $manualEmailsValue = $settings->get('sikayet_notify_manual_emails')?->value;
            if (!empty($manualEmailsValue)) {
                $manualEmails = explode(',', $manualEmailsValue);
                foreach ($manualEmails as $email) {
                    if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                        Notification::route('mail', trim($email))
                            ->notify(new YeniMusteriSikayetiBildirimi($sikayet));
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Yeni şikayet bildirimi (Observer) gönderilemedi: ' . $e->getMessage());
        }
    }

    // ... (updated metodu 1. maddedeki gibi güncellenmiş olmalı) ...

    /**
     * Handle the MusteriSikayeti "updated" event.
     * SENARYO: Müşteri Şikayeti Bir Takıma Atandı
     * (TAKIM LİDERİ, DİREKTÖR, BÖLÜM LİDERİ, KALİTE VE OLUŞTURANA BİLDİRİM GÖNDERİR)
     */
    public function updated(MusteriSikayeti $musteriSikayeti): void
    {
        // 1. ATAMA BİLDİRİMLERİ (atanan_cozum_takimi_id değiştiyse)
        if ($musteriSikayeti->isDirty('atanan_cozum_takimi_id') && $musteriSikayeti->atanan_cozum_takimi_id) {
            try {
                $takim = $musteriSikayeti->cozumTakimi;
                if ($takim) {
                    // a) TAKIM LİDERİNE BİLDİRİM
                    if ($takim->lider) {
                        $takim->lider->notify(new SikayetTakimaAtandiBildirimi($musteriSikayeti, 'lider'));
                    }

                    // b) DİĞER TAKIM ÜYELERİNE BİLDİRİM (Sadece Zil)
                    $uyeler = $takim->uyeler->where('id', '!=', $takim->lider_user_id);
                    if ($uyeler->isNotEmpty()) {
                        Notification::send($uyeler, new SikayetTakimaAtandiBildirimi($musteriSikayeti, 'lider'));
                    }

                    // c) BÖLÜM DİREKTÖRÜNE BİLDİRİM
                    $bolum = $musteriSikayeti->sikayetKategori ? $musteriSikayeti->sikayetKategori->bolum : null;
                    if ($bolum && $bolum->director) {
                        $bolum->director->notify(new SikayetTakimaAtandiBildirimi($musteriSikayeti, 'direktor'));
                    }

                    // d) BÖLÜM LİDERİNE BİLDİRİM (Emrah Al vb.)
                    if ($bolum) {
                        $bolumLiderleri = User::role('Bölüm Lideri')->where('bolum_id', $bolum->id)->get();
                        if ($bolumLiderleri->isNotEmpty()) {
                            Notification::send($bolumLiderleri, new SikayetTakimaAtandiBildirimi($musteriSikayeti, 'bolum_lideri'));
                        }
                    }

                    // e) BÖLÜM KALİTE YÖNETİCİSİNE BİLDİRİM (Serkan Tölek vb.)
                    if ($musteriSikayeti->sikayet_kategorisi_id) {
                        $kaliteYoneticileri = User::role('Bölüm Kalite Yöneticisi')
                            ->whereHas('yonettigiSikayetKategorileri', function ($q) use ($musteriSikayeti) {
                                $q->where('sikayet_kategorileri.id', $musteriSikayeti->sikayet_kategorisi_id);
                            })->get();

                        if ($kaliteYoneticileri->isNotEmpty()) {
                            Notification::send($kaliteYoneticileri, new SikayetTakimaAtandiBildirimi($musteriSikayeti, 'kalite'));
                        }
                    }

                    // f) ŞİKAYETİ OLUŞTURAN KİŞİYE BİLDİRİM (Gülnur Nurhak vb.)
                    if ($musteriSikayeti->olusturanKurulUyesi) {
                        $musteriSikayeti->olusturanKurulUyesi->notify(new SikayetTakimaAtandiBildirimi($musteriSikayeti, 'olusturan'));
                    }

                    // g) MÜŞTERİYE E-POSTA BİLDİRİMİ
                    if ($musteriSikayeti->musteri_iletisim) {
                        Mail::to($musteriSikayeti->musteri_iletisim)
                            ->queue(new SikayetAtandiMusteriBildirimi($musteriSikayeti, $takim));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Şikayet atama bildirimi gönderilemedi: ' . $e->getMessage());
            }
        }
    }
}