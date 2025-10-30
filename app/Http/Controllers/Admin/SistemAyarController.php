<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Validation\Rule;

class SistemAyarController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        $users = User::orderBy('name')->get(); // <-- Tüm kullanıcıları çek

        $logo = $settings->get('site_logo');
        $kayitOnay = $settings->get('kayit_onay_sistemi');
        $paraBirimleri = $settings->get('para_birimleri');
        $standartPuan = $settings->get('standart_puan');
        
        // Müşteri şikayeti için olan puanlar
        $musteriSikayetiPuan = $settings->get('musteri_sikayeti_standart_puan');
        $musteriSikayetiCozumCarpan = $settings->get('musteri_sikayeti_cozum_carpan'); // <-- YENİ EKLENDİ

        return view('admin.ayarlar.index', compact(
            'settings', // <-- YENİ EKLENDİ
            'users',
            'logo',
            'kayitOnay',
            'paraBirimleri',
            'standartPuan',
            'musteriSikayetiPuan',
            'musteriSikayetiCozumCarpan' // <-- YENİ EKLENDİ
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_logo' => 'nullable|image|mimes:png,jpg,svg|max:1024',
            'kayit_onay_sistemi' => 'nullable|boolean',
            'para_birimleri' => 'nullable|string',
            'standart_puan' => 'nullable|integer|min:0',
            'musteri_sikayeti_standart_puan' => 'nullable|integer|min:0',
            'musteri_sikayeti_cozum_carpan' => 'nullable|integer|min:1', // <-- YENİ EKLENDİ (min 1 olsun)
            // === Yeni E-posta Ayarları için Validation ===
            'sikayet_onay_email_subject' => 'nullable|string|max:255',
            'sikayet_onay_email_body' => 'nullable|string',
            'sikayet_cozum_email_subject' => 'nullable|string|max:255',
            'sikayet_cozum_email_body' => 'nullable|string',
            'sikayet_admin_notification_email' => 'nullable|email|max:255', // Geçerli e-posta formatı
            'sikayet_response_time_hours' => 'nullable|integer|min:1', // En az 1 saat
            // === Yeni Yönetici Bildirim Ayarları için Validation ===
            'sikayet_notify_user_ids'   => 'nullable|array', // Gelenin bir dizi olduğunu doğrula
            'sikayet_notify_user_ids.*' => 'integer|exists:users,id', // Dizideki her elemanın geçerli bir user ID olduğunu doğrula
            'sikayet_notify_manual_emails' => 'nullable|string', // Şimdilik sadece string olarak alalım, kaydederken işleriz
        ]);

        // 1. Logo Güncelleme
        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::where('key', 'site_logo')->first();
            if ($oldLogo && $oldLogo->value) {
                Storage::disk('public')->delete($oldLogo->value);
            }
            $path = $request->file('site_logo')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $path]
            );
        }

        // 2. Kayıt Onay Sistemi Güncelleme
        Setting::updateOrCreate(
            ['key' => 'kayit_onay_sistemi'],
            ['value' => $request->has('kayit_onay_sistemi') ? '1' : '0']
        );

        // 3. Para Birimleri Güncelleme
        Setting::updateOrCreate(
            ['key' => 'para_birimleri'],
            ['value' => $request->filled('para_birimleri') ? strtoupper(str_replace(' ', '', $request->para_birimleri)) : null]
        );

        // 4. Standart Puan Güncelleme
        Setting::updateOrCreate(
            ['key' => 'standart_puan'],
            ['value' => $request->filled('standart_puan') ? $request->standart_puan : 0]
        );

        // 5. Müşteri Şikayeti Giriş Puanı Güncelleme
        Setting::updateOrCreate(
            ['key' => 'musteri_sikayeti_standart_puan'],
            ['value' => $request->filled('musteri_sikayeti_standart_puan') ? $request->musteri_sikayeti_standart_puan : 0]
        );
        
        // 6. Müşteri Şikayeti Çözüm Çarpanı Güncelleme <-- YENİ EKLENDİ
        Setting::updateOrCreate(
            ['key' => 'musteri_sikayeti_cozum_carpan'],
            ['value' => $request->filled('musteri_sikayeti_cozum_carpan') ? $request->musteri_sikayeti_cozum_carpan : 10] // Varsayılan 10
        );

        // ================== YENİ AYARLARI KAYDETME ==================

        // 7. Yeni Şikayet E-posta Konusu
        Setting::updateOrCreate(
            ['key' => 'sikayet_onay_email_subject'],
            // Eğer boş gelirse view'deki varsayılanı kullanalım
            ['value' => $request->input('sikayet_onay_email_subject', 'Şikayetiniz Alınmıştır - Takip Bilgileriniz')]
        );

        // 8. Yeni Şikayet E-posta İçeriği
        Setting::updateOrCreate(
            ['key' => 'sikayet_onay_email_body'],
            // Eğer boş gelirse view'deki varsayılanı kullanalım
            ['value' => $request->input('sikayet_onay_email_body', "Sayın {musteri_adi},\n\nŞikayetiniz alınmıştır. Takip bilgileriniz aşağıdadır:\nTakip Linki: {takip_linki}\nŞifreniz: {sifre}\n\nTeşekkür ederiz.")]
        );

        // 9. Çözüm Bildirim E-posta Konusu
        Setting::updateOrCreate(
            ['key' => 'sikayet_cozum_email_subject'],
            // Eğer boş gelirse view'deki varsayılanı kullanalım
            ['value' => $request->input('sikayet_cozum_email_subject', 'Şikayetiniz Çözümlenmiştir')]
        );

        // 10. Çözüm Bildirim E-posta İçeriği
        Setting::updateOrCreate(
            ['key' => 'sikayet_cozum_email_body'],
            // Eğer boş gelirse view'deki varsayılanı kullanalım
            ['value' => $request->input('sikayet_cozum_email_body', "Sayın {musteri_adi},\n\n'{sikayet_konusu}' konulu şikayetiniz çözümlenmiştir.\nÇözüm Tarihi: {cozum_tarihi}\n\nDetayları incelemek ve geri bildirimde bulunmak için: {takip_linki}\n\nTeşekkür ederiz.")]
        );

        // 11. Yönetici Bildirim E-postası
        Setting::updateOrCreate(
            ['key' => 'sikayet_admin_notification_email'],
            // Boş bırakılabilir
            ['value' => $request->input('sikayet_admin_notification_email')]
        );

        // 12. Hedef Yanıt Süresi
        Setting::updateOrCreate(
            ['key' => 'sikayet_response_time_hours'],
            // Eğer boş gelirse veya 0'dan küçükse 72 varsayalım
            ['value' => $request->filled('sikayet_response_time_hours') && $request->sikayet_response_time_hours >= 1 ? $request->sikayet_response_time_hours : 72]
        );

        // ================== YENİ BİLDİRİM AYARLARINI KAYDETME ==================

        // 13. Bildirim Gönderilecek Kullanıcı ID'leri
        $userIdsValue = null;
        if ($request->has('sikayet_notify_user_ids')) {
            // Gelen dizi ['1', '5', '12'] ise, '1,5,12' string'ine çevir
            $userIdsValue = implode(',', $request->input('sikayet_notify_user_ids'));
        }
        Setting::updateOrCreate(
            ['key' => 'sikayet_notify_user_ids'],
            ['value' => $userIdsValue] // Null veya '1,5,12' gibi bir string
        );

        // 14. Bildirim Gönderilecek Manuel E-postalar
        $manualEmailsValue = null;
        if ($request->filled('sikayet_notify_manual_emails')) {
            // Virgül veya yeni satır ile ayrılmış e-postaları al, boşlukları temizle, boş olanları filtrele
            $emails = preg_split('/[,\n\r]+/', $request->input('sikayet_notify_manual_emails'));
            $cleanedEmails = array_filter(array_map('trim', $emails));
            // Sadece geçerli e-posta formatındakileri alalım (isteğe bağlı ama önerilir)
            $validEmails = array_filter($cleanedEmails, function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            if (!empty($validEmails)) {
                 // Tekrar virgülle birleştirerek kaydet
                 $manualEmailsValue = implode(',', $validEmails);
            }
        }
        Setting::updateOrCreate(
            ['key' => 'sikayet_notify_manual_emails'],
            ['value' => $manualEmailsValue] // Null veya 'a@b.com,c@d.com' gibi bir string
        );

        // ================== KAYDETME SONU ==================



        return back()->with('success', 'Ayarlar başarıyla güncellendi.');
    }
}