<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Spatie\Permission\Models\Role; // <-- ROL MODELİNİ EKLE
use Illuminate\Validation\Rule;
use App\Models\Bolum;

class SistemAyarController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->get(); // <-- ROLLERİ ÇEK
        $bolumler = Bolum::orderBy('ad')->get(); // <--- EKLENDİ: Bölümleri Çek

        // Ayarları daha dinamik alalım
        $logo = $settings->get('site_logo');
        $kayitOnay = $settings->get('kayit_onay_sistemi');
        $paraBirimleri = $settings->get('para_birimleri');
        $standartPuan = $settings->get('standart_puan');
        $musteriSikayetiPuan = $settings->get('musteri_sikayeti_standart_puan');
        $musteriSikayetiCozumCarpan = $settings->get('musteri_sikayeti_cozum_carpan');
        $kurulDefaultPuan = $settings->get('kurul_default_puan');

        return view('admin.ayarlar.index', compact(
            'settings',
            'users',
            'roles', // <-- ROLLERİ VIEW'A GÖNDER
            'bolumler', // <--- EKLENDİ: View'a gönder
            'logo',
            'kayitOnay',
            'paraBirimleri',
            'standartPuan',
            'musteriSikayetiPuan',
            'musteriSikayetiCozumCarpan',
            'kurulDefaultPuan'
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
            'musteri_sikayeti_cozum_carpan' => 'nullable|integer|min:1',
            'kurul_default_puan' => 'nullable|integer|min:0',

            // === Müşteri E-posta Ayarları ===
            'sikayet_onay_email_subject' => 'nullable|string|max:255',
            'sikayet_onay_email_body' => 'nullable|string',
            'sikayet_cozum_email_subject' => 'nullable|string|max:255',
            'sikayet_cozum_email_body' => 'nullable|string',
            'sikayet_response_time_hours' => 'nullable|integer|min:1',

            // === YENİ - Admin/İç Bildirim Ayarları ===
            'sikayet_notify_user_ids' => 'nullable|array',
            'sikayet_notify_user_ids.*' => 'integer|exists:users,id',
            'sikayet_notify_role_ids' => 'nullable|array', // <-- YENİ (Roller)
            'sikayet_notify_role_ids.*' => 'integer|exists:roles,id', // <-- YENİ (Roller)
            'sikayet_notify_manual_emails' => 'nullable|string',
            'sikayet_atama_notify_manual_emails' => 'nullable|string', // <-- YENİ (Atama Bildirimi)

            // <--- EKLENDİ: Bölüm Yetkisi Validasyonu
            'global_disciplinary_departments' => 'nullable|array',
            'global_disciplinary_departments.*' => 'exists:bolumler,id',
        ]);

        // 1-7 arası (Mevcut ayarlarınız) ...
        // ... (Logo, Kayıt, Para Birimi, Puanlar vs. kodlarınız burada)
        // ... (Kısalık olması için eklemedim, sizde mevcut)
        
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
        
        // 6. Müşteri Şikayeti Çözüm Çarpanı Güncelleme
        Setting::updateOrCreate(
            ['key' => 'musteri_sikayeti_cozum_carpan'],
            ['value' => $request->filled('musteri_sikayeti_cozum_carpan') ? $request->musteri_sikayeti_cozum_carpan : 10] // Varsayılan 10
        );

        // 7. Kurulun Atadığı Default Puan
        Setting::updateOrCreate(
            ['key' => 'kurul_default_puan'],
            ['value' => $request->filled('kurul_default_puan') ? $request->kurul_default_puan : 0] // Varsayılan 0
        );


        // 8-13 arası (Müşteri E-posta Ayarları) ...
        // ... (Sikayet Onay, Çözüm Subject/Body vs. kodlarınız burada)
        // ... (Kısalık olması için eklemedim, sizde mevcut)

        // 8. Yeni Şikayet E-posta Konusu
        Setting::updateOrCreate(
            ['key' => 'sikayet_onay_email_subject'],
            ['value' => $request->input('sikayet_onay_email_subject', 'Şikayetiniz Alınmıştır - Takip Bilgileriniz')]
        );

        // 9. Yeni Şikayet E-posta İçeriği
        Setting::updateOrCreate(
            ['key' => 'sikayet_onay_email_body'],
            ['value' => $request->input('sikayet_onay_email_body', "Sayın {musteri_adi},\n\nŞikayetiniz alınmıştır. Takip bilgileriniz aşağıdadır:\nTakip Linki: {takip_linki}\nŞifreniz: {sifre}\n\nTeşekkür ederiz.")]
        );

        // 10. Çözüm Bildirim E-posta Konusu
        Setting::updateOrCreate(
            ['key' => 'sikayet_cozum_email_subject'],
            ['value' => $request->input('sikayet_cozum_email_subject', 'Şikayetiniz Çözümlenmiştir')]
        );

        // 11. Çözüm Bildirim E-posta İçeriği
        Setting::updateOrCreate(
            ['key' => 'sikayet_cozum_email_body'],
            ['value' => $request->input('sikayet_cozum_email_body', "Sayın {musteri_adi},\n\n'{sikayet_konusu}' konulu şikayetiniz çözümlenmiştir.\nÇözüm Tarihi: {cozum_tarihi}\n\nDetayları incelemek ve geri bildirimde bulunmak için: {takip_linki}\n\nTeşekkür ederiz.")]
        );

        // 12. Yönetici Bildirim E-postası (Bu ayarı 'sikayet_notify_manual_emails' ile birleştirebiliriz, ama mevcutsa kalsın)
        // Not: Bu 'sikayet_admin_notification_email' alanı sizde vardı ama yeni sistemde 'sikayet_notify_manual_emails' daha esnek.
        // Şimdilik sizdeki kodu koruyorum:
        Setting::updateOrCreate(
            ['key' => 'sikayet_admin_notification_email'],
            ['value' => $request->input('sikayet_admin_notification_email')]
        );

        // 13. Hedef Yanıt Süresi
        Setting::updateOrCreate(
            ['key' => 'sikayet_response_time_hours'],
            ['value' => $request->filled('sikayet_response_time_hours') && $request->sikayet_response_time_hours >= 1 ? $request->sikayet_response_time_hours : 72]
        );


        // ================== YENİ İÇ BİLDİRİM AYARLARINI KAYDETME ==================

        // 14. (Mevcut) Bildirim Gönderilecek Kullanıcı ID'leri
        $userIdsValue = null;
        if ($request->has('sikayet_notify_user_ids')) {
            $userIdsValue = implode(',', $request->input('sikayet_notify_user_ids'));
        }
        Setting::updateOrCreate(
            ['key' => 'sikayet_notify_user_ids'],
            ['value' => $userIdsValue]
        );

        // 15. (Mevcut) Bildirim Gönderilecek Manuel E-postalar (Yeni Şikayet)
        $manualEmailsValue = $this->processManualEmails($request->input('sikayet_notify_manual_emails'));
        Setting::updateOrCreate(
            ['key' => 'sikayet_notify_manual_emails'],
            ['value' => $manualEmailsValue]
        );

        // 16. (YENİ) Bildirim Gönderilecek Roller
        $roleIdsValue = null;
        if ($request->has('sikayet_notify_role_ids')) {
            $roleIdsValue = implode(',', $request->input('sikayet_notify_role_ids'));
        }
        Setting::updateOrCreate(
            ['key' => 'sikayet_notify_role_ids'],
            ['value' => $roleIdsValue]
        );

        // 17. (YENİ) Bildirim Gönderilecek Manuel E-postalar (Şikayet Atama)
        $atamaManualEmailsValue = $this->processManualEmails($request->input('sikayet_atama_notify_manual_emails'));
        Setting::updateOrCreate(
            ['key' => 'sikayet_atama_notify_manual_emails'],
            ['value' => $atamaManualEmailsValue]
        );

        // ================== KAYDETME SONU ==================

       // ================== GÜNCELLENMİŞ: DİSİPLİN YETKİ AYARLARI ==================
        
        // 1. Önce temizlik (Reset)
        Bolum::query()->update([
            'is_disciplinary_global' => 0,
            'disciplinary_target_depts' => null
        ]);

        // 2. Formdan gelen verileri işle
        if ($request->has('disciplinary_auth')) {
            foreach ($request->input('disciplinary_auth') as $bolumId => $data) {
                $bolum = Bolum::find($bolumId);
                if ($bolum) {
                    // Global Yetki (Tüm Fabrika) İşaretli mi?
                    $isGlobal = isset($data['global']) && $data['global'] == '1';
                    
                    $bolum->is_disciplinary_global = $isGlobal;
                    
                    // Eğer Global değilse ama spesifik bölümler seçildiyse onları kaydet
                    // (Şimdilik global ise 'all' yapalım, değilse null bırakalım, mantığı basitleştirelim)
                    // İleride buraya "Sadece Üretim ve Depo'ya tutabilsin" gibi detay ekleyebiliriz.
                    
                    $bolum->save();
                }
            }
        }
        
        // ================== KAYDETME SONU ==================

        return back()->with('success', 'Ayarlar ve Bölüm Yetkileri başarıyla güncellendi.');
    }
    /**
     * E-posta adreslerini temizler ve virgülle ayrılmış string'e dönüştürür.
     *
     * @param string|null $emailsInput
     * @return string|null
     */
    private function processManualEmails(?string $emailsInput): ?string
    {
        if (empty($emailsInput)) {
            return null;
        }

        $emails = preg_split('/[,\n\r]+/', $emailsInput);
        $cleanedEmails = array_filter(array_map('trim', $emails));
        $validEmails = array_filter($cleanedEmails, function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        return !empty($validEmails) ? implode(',', $validEmails) : null;
    }
}