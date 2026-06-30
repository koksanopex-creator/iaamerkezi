<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use App\Models\Bolum;

class SistemAyarController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        $users = User::where('is_personnel', true)
            ->where('is_mavi_yaka', false)
            ->orderBy('name')
            ->get();
        $roles = Role::orderBy('name')->get();
        $bolumler = Bolum::orderBy('ad')->get();

        // Arabuluculuk İzinleri
        $arabuluculukPermissions = Permission::where('name', 'like', 'arabuluculuk.%')->orderBy('name')->get();

        // Ayarlar daha dinamik
        $logo = $settings->get('site_logo');
        $kayitOnay = $settings->get('kayit_onay_sistemi');
        $paraBirimleri = $settings->get('para_birimleri');
        $standartPuan = $settings->get('standart_puan');
        $musteriSikayetiPuan = $settings->get('musteri_sikayeti_standart_puan');
        $musteriSikayetiCozumCarpan = $settings->get('musteri_sikayeti_cozum_carpan');
        $kurulDefaultPuan = $settings->get('kurul_default_puan');
        $iaaOneriPuani = $settings->get('iaa_oneri_puani');
        $kvkkText = $settings->get('kvkk_text');
        $kvkkPdf = $settings->get('kvkk_pdf');
        $notifyBolumLideri = $settings->get('sikayet_notify_bolum_lideri')?->value;
        $direktorOnayiAktif = $settings->get('sikayet_direktor_onayi_aktif')?->value;
        $superadminSikayetSayacAktif = $settings->get('superadmin_sikayet_panel_sayac_aktif')?->value;
        $newUserNotifyLider = $settings->get('new_user_notify_bolum_lideri')?->value;
        $newUserNotifyDirektor = $settings->get('new_user_notify_direktor')?->value;
        $birthdayIsActive = $settings->get('birthday_is_active')?->value ?? '1';
        $birthdayUpcomingDays = $settings->get('birthday_upcoming_days')?->value ?? '7';
        $birthdayPastDays = $settings->get('birthday_past_days')?->value ?? '3';
        $birthdayEmailSubject = $settings->get('birthday_email_subject')?->value ?? 'İyi Ki Doğdun! 🎂';
        $birthdayEmailBody = $settings->get('birthday_email_body')?->value ?? 'Sayın {personel_adi}, Doğum gününüzü kutlar, sağlıklı ve mutlu bir yıl dileriz.';
        $birthdayNotifyLeader = $settings->get('birthday_notify_leader')?->value ?? '1';
        $birthdayNotifyDirector = $settings->get('birthday_notify_director')?->value ?? '1';
        $birthdayLeaderEmailSubject = $settings->get('birthday_leader_email_subject')?->value ?? 'Ekibinizde Bir Doğum Günü! 🎂';
        $birthdayLeaderEmailBody = $settings->get('birthday_leader_email_body')?->value ?? 'Merhaba {yonetici_adi}, bugün ekibinizden {personel_adi} personelin doğum günü.';
        $birthdayNotifyColleagues = $settings->get('birthday_notify_colleagues')?->value ?? '1';
        $birthdayColleagueEmailSubject = $settings->get('birthday_colleague_email_subject')?->value ?? 'Bir Ekip Arkadaşınızın Doğum Günü! 🎂';
        $birthdayColleagueEmailBody = $settings->get('birthday_colleague_email_body')?->value ?? 'Merhaba, bugün bölüm arkadaşınız {personel_adi}\'nın doğum günü.';
        $birthdayBlockList = json_decode($settings->get('birthday_block_list')?->value ?? '[]', true);

        // Yıldönümü Ayarları
        $anniversaryIsActive = $settings->get('anniversary_is_active')?->value ?? '1';
        $anniversaryEmailSubject = $settings->get('anniversary_email_subject')?->value ?? 'Şirketimizdeki {yil}. Yılınız Kutlu Olsun! 🎊';
        $anniversaryEmailBody = $settings->get('anniversary_email_body')?->value ?? 'Sayın {personel_adi}, şirketimizdeki {yil}. yılınızı kutlar, başarılarınızın devamını dileriz.';
        $anniversaryNotifyLeader = $settings->get('anniversary_notify_leader')?->value ?? '1';
        $anniversaryNotifyDirector = $settings->get('anniversary_notify_director')?->value ?? '1';
        $anniversaryNotifyColleagues = $settings->get('anniversary_notify_colleagues')?->value ?? '1';
        $anniversaryLeaderEmailSubject = $settings->get('anniversary_leader_email_subject')?->value ?? 'Ekibinizde Bir İş Yıldönümü! 🎊';
        $anniversaryLeaderEmailBody = $settings->get('anniversary_leader_email_body')?->value ?? 'Merhaba {yonetici_adi}, bugün ekibinizden {personel_adi} personelin şirketimizdeki {yil}. yılı.';
        $anniversaryColleagueEmailSubject = $settings->get('anniversary_colleague_email_subject')?->value ?? 'Bir Ekip Arkadaşınızın İş Yıldönümü! 🎊';
        $anniversaryColleagueEmailBody = $settings->get('anniversary_colleague_email_body')?->value ?? 'Merhaba, bugün bölüm arkadaşınız {personel_adi}\'nın şirketimizdeki {yil}. yılı.';
        $anniversaryBlockList = json_decode($settings->get('anniversary_block_list')?->value ?? '[]', true);

        $allUsers = User::where('is_personnel', true)->orderBy('name')->get();

        // Hoşgeldiniz E-postası
        $newCustomerEmailSubject = $settings->get('new_customer_email_subject')?->value;
        $newCustomerEmailBody = $settings->get('new_customer_email_body')?->value;

        // Mail Log Ayarları
        $mailLogAllowedRoles = json_decode($settings->get('mail_log_allowed_roles')?->value ?? '[]', true);
        $mailLogAllowedUsers = json_decode($settings->get('mail_log_allowed_users')?->value ?? '[]', true);
        $mailLogAutoCleanupDays = $settings->get('mail_log_auto_cleanup_days')?->value ?? '0';

        return view('admin.ayarlar.index', compact(
            'settings',
            'users',
            'roles',
            'bolumler',
            'arabuluculukPermissions',
            'logo',
            'kayitOnay',
            'paraBirimleri',
            'standartPuan',
            'musteriSikayetiPuan',
            'musteriSikayetiCozumCarpan',
            'kurulDefaultPuan',
            'iaaOneriPuani',
            'kvkkText',
            'kvkkPdf',
            'notifyBolumLideri',
            'direktorOnayiAktif',
            'superadminSikayetSayacAktif',
            'newUserNotifyLider',
            'newUserNotifyDirektor',
            'newCustomerEmailSubject',
            'newCustomerEmailBody',
            'birthdayIsActive',
            'birthdayUpcomingDays',
            'birthdayPastDays',
            'birthdayEmailSubject',
            'birthdayEmailBody',
            'birthdayNotifyLeader',
            'birthdayNotifyDirector',
            'birthdayLeaderEmailSubject',
            'birthdayLeaderEmailBody',
            'birthdayNotifyColleagues',
            'birthdayColleagueEmailSubject',
            'birthdayColleagueEmailBody',
            'birthdayBlockList',
            'anniversaryIsActive',
            'anniversaryEmailSubject',
            'anniversaryEmailBody',
            'anniversaryNotifyLeader',
            'anniversaryNotifyDirector',
            'anniversaryNotifyColleagues',
            'anniversaryLeaderEmailSubject',
            'anniversaryLeaderEmailBody',
            'anniversaryColleagueEmailSubject',
            'anniversaryColleagueEmailBody',
            'anniversaryBlockList',
            'allUsers',
            'mailLogAllowedRoles',
            'mailLogAllowedUsers',
            'mailLogAutoCleanupDays'
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
            'iaa_oneri_puani' => 'nullable|integer|min:0',

            // === Müşteri E-posta Ayarları ===
            'sikayet_onay_email_subject' => 'nullable|string|max:255',
            'sikayet_onay_email_body' => 'nullable|string',
            'sikayet_cozum_email_subject' => 'nullable|string|max:255',
            'sikayet_cozum_email_body' => 'nullable|string',
            'sikayet_response_time_hours' => 'nullable|integer|min:1',
            'new_customer_email_subject' => 'nullable|string|max:255',
            'new_customer_email_body' => 'nullable|string',

            // === YENİ - Admin/İç Bildirim Ayarları ===
            'sikayet_notify_user_ids' => 'nullable|array',
            'sikayet_notify_user_ids.*' => 'integer|exists:users,id',
            'sikayet_notify_role_ids' => 'nullable|array', // <-- YENİ (Roller)
            'sikayet_notify_role_ids.*' => 'integer|exists:roles,id', // <-- YENİ (Roller)
            'sikayet_notify_manual_emails' => 'nullable|string',
            'sikayet_atama_notify_manual_emails' => 'nullable|string',
            'sikayet_notify_bolum_lideri' => 'nullable|boolean',
            'sikayet_direktor_onayi_aktif' => 'nullable|boolean', // <--- YENİ
            'superadmin_sikayet_panel_sayac_aktif' => 'nullable|boolean', // <--- YENİ
            'birthday_is_active' => 'nullable|boolean',
            'birthday_upcoming_days' => 'nullable|integer|min:1|max:31',
            'birthday_past_days' => 'nullable|integer|min:1|max:31',
            'birthday_email_subject' => 'nullable|string|max:255',
            'birthday_email_body' => 'nullable|string',
            'birthday_notify_leader' => 'nullable|boolean',
            'birthday_notify_director' => 'nullable|boolean',
            'birthday_leader_email_subject' => 'nullable|string|max:255',
            'birthday_leader_email_body' => 'nullable|string',
            'birthday_notify_colleagues' => 'nullable|boolean',
            'birthday_colleague_email_subject' => 'nullable|string|max:255',
            'birthday_colleague_email_body' => 'nullable|string',
            'birthday_block_list' => 'nullable|array',

            'anniversary_is_active' => 'nullable|boolean',
            'anniversary_email_subject' => 'nullable|string|max:255',
            'anniversary_email_body' => 'nullable|string',
            'anniversary_notify_leader' => 'nullable|boolean',
            'anniversary_notify_director' => 'nullable|boolean',
            'anniversary_notify_colleagues' => 'nullable|boolean',
            'anniversary_leader_email_subject' => 'nullable|string|max:255',
            'anniversary_leader_email_body' => 'nullable|string',
            'anniversary_colleague_email_subject' => 'nullable|string|max:255',
            'anniversary_colleague_email_body' => 'nullable|string',
            'anniversary_block_list' => 'nullable|array',

            // <--- EKLENDİ: Bölüm Yetkisi Validasyonu
            'global_disciplinary_departments' => 'nullable|array',
            'global_disciplinary_departments.*' => 'exists:bolumler,id',

            // Arabuluculuk Yetki Validasyonu (Opsiyonel ama iyi olur)
            'role_permissions' => 'nullable|array',

            // KVKK Ayarları
            'kvkk_text' => 'nullable|string',
            'kvkk_pdf' => 'nullable|file|mimes:pdf|max:5120',
            'remove_kvkk_pdf' => 'nullable|boolean',
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

        // [YENİ] 7.1 İAA Öneri Puanı Kaydetme
        Setting::updateOrCreate(
            ['key' => 'iaa_oneri_puani'],
            ['value' => $request->filled('iaa_oneri_puani') ? $request->iaa_oneri_puani : 0]
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

        // 11.1 Yeni Kayıt / Hoşgeldiniz E-posta Konusu
        Setting::updateOrCreate(
            ['key' => 'new_customer_email_subject'],
            ['value' => $request->input('new_customer_email_subject', 'Hoşgeldiniz - Sisteme Giriş Bilgileriniz')]
        );

        // 11.2 Yeni Kayıt / Hoşgeldiniz E-posta İçeriği
        Setting::updateOrCreate(
            ['key' => 'new_customer_email_body'],
            ['value' => $request->input('new_customer_email_body', "Köksan Müşteri Portalı hesabınız {sirket_adi} firması için başarıyla oluşturulmuştur. Aşağıdaki bilgileri kullanarak sisteme giriş yapabilirsiniz.")]
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

        // 18. (YENİ) Bölüm Lideri Bildirimi (Opsiyonel)
        Setting::updateOrCreate(
            ['key' => 'sikayet_notify_bolum_lideri'],
            ['value' => $request->has('sikayet_notify_bolum_lideri') ? '1' : '0']
        );

        // 19. (YENİ) Direktör Onayı Aktif mi?
        Setting::updateOrCreate(
            ['key' => 'sikayet_direktor_onayi_aktif'],
            ['value' => $request->has('sikayet_direktor_onayi_aktif') ? '1' : '0']
        );

        // 19.1 (YENİ) Superadmin Tüm Şikayet Bildirimlerini Görsün mü?
        Setting::updateOrCreate(
            ['key' => 'superadmin_sikayet_panel_sayac_aktif'],
            ['value' => $request->has('superadmin_sikayet_panel_sayac_aktif') ? '1' : '0']
        );

        // 20. Yeni Kullanıcı — Bölüm Liderine Mail
        Setting::updateOrCreate(
            ['key' => 'new_user_notify_bolum_lideri'],
            ['value' => $request->has('new_user_notify_bolum_lideri') ? '1' : '0']
        );

        // 21. Yeni Kullanıcı — Direktöre Mail
        Setting::updateOrCreate(
            ['key' => 'new_user_notify_direktor'],
            ['value' => $request->has('new_user_notify_direktor') ? '1' : '0']
        );

        // 22. Doğum Günü Ayarları
        Setting::updateOrCreate(
            ['key' => 'birthday_is_active'],
            ['value' => $request->has('birthday_is_active') ? '1' : '0']
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_upcoming_days'],
            ['value' => $request->input('birthday_upcoming_days', 7)]
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_past_days'],
            ['value' => $request->input('birthday_past_days', 3)]
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_email_subject'],
            ['value' => $request->input('birthday_email_subject', 'İyi Ki Doğdun! 🎂')]
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_email_body'],
            ['value' => $request->input('birthday_email_body')]
        );

        Setting::updateOrCreate(
            ['key' => 'birthday_notify_leader'],
            ['value' => $request->has('birthday_notify_leader') ? '1' : '0']
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_notify_director'],
            ['value' => $request->has('birthday_notify_director') ? '1' : '0']
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_leader_email_subject'],
            ['value' => $request->input('birthday_leader_email_subject')]
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_leader_email_body'],
            ['value' => $request->input('birthday_leader_email_body')]
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_notify_colleagues'],
            ['value' => $request->has('birthday_notify_colleagues') ? '1' : '0']
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_colleague_email_subject'],
            ['value' => $request->input('birthday_colleague_email_subject')]
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_colleague_email_body'],
            ['value' => $request->input('birthday_colleague_email_body')]
        );
        Setting::updateOrCreate(
            ['key' => 'birthday_block_list'],
            ['value' => json_encode($request->input('birthday_block_list', []))]
        );

        // Yıldönümü Ayarları Kaydetme
        Setting::updateOrCreate(
            ['key' => 'anniversary_is_active'],
            ['value' => $request->has('anniversary_is_active') ? '1' : '0']
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_email_subject'],
            ['value' => $request->input('anniversary_email_subject')]
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_email_body'],
            ['value' => $request->input('anniversary_email_body')]
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_notify_leader'],
            ['value' => $request->has('anniversary_notify_leader') ? '1' : '0']
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_notify_director'],
            ['value' => $request->has('anniversary_notify_director') ? '1' : '0']
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_notify_colleagues'],
            ['value' => $request->has('anniversary_notify_colleagues') ? '1' : '0']
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_leader_email_subject'],
            ['value' => $request->input('anniversary_leader_email_subject')]
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_leader_email_body'],
            ['value' => $request->input('anniversary_leader_email_body')]
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_colleague_email_subject'],
            ['value' => $request->input('anniversary_colleague_email_subject')]
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_colleague_email_body'],
            ['value' => $request->input('anniversary_colleague_email_body')]
        );
        Setting::updateOrCreate(
            ['key' => 'anniversary_block_list'],
            ['value' => json_encode($request->input('anniversary_block_list', []))]
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


        // ================== 4. ARABULUCULUK YETKİLERİ (ID BAZLI DÜZELTME) ==================

        // Arabuluculuk ile ilgili tüm izin nesnelerini al
        $arabuluculukPerms = Permission::where('name', 'like', 'arabuluculuk.%')->get();
        $allRoles = Role::where('name', '!=', 'Superadmin')->get();

        // Formdan gelen veriyi al: role_permissions[role_id][perm_id] = "on"
        $inputPermissions = $request->input('role_permissions', []);

        foreach ($allRoles as $role) {
            // Bu rol için seçilenleri al (yoksa boş dizi)
            $roleInput = $inputPermissions[$role->id] ?? [];

            foreach ($arabuluculukPerms as $perm) {
                // Eğer formda bu rol için bu İzin ID'si varsa -> Yetki Ver
                if (array_key_exists($perm->id, $roleInput)) {
                    $role->givePermissionTo($perm->name);
                }
                // Formda yok ama veritabanında varsa -> Geri Al (Revoke)
                else {
                    if ($role->hasPermissionTo($perm->name)) {
                        $role->revokePermissionTo($perm->name);
                    }
                }
            }
        }

        // Cache Temizliği
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Cache Temizliği
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ================== 5. KVKK METNİ KAYDETME (YENİ) ==================
        if ($request->has('kvkk_text')) {
            Setting::updateOrCreate(
                ['key' => 'kvkk_text'],
                ['value' => $request->input('kvkk_text')]
            );
        }

        // KVKK PDF İşlemleri
        if ($request->has('remove_kvkk_pdf')) {
            $oldPdf = Setting::where('key', 'kvkk_pdf')->first();
            if ($oldPdf && $oldPdf->value) {
                Storage::disk('public')->delete($oldPdf->value);
                $oldPdf->delete();
            }
        }

        if ($request->hasFile('kvkk_pdf')) {
            $oldPdf = Setting::where('key', 'kvkk_pdf')->first();
            if ($oldPdf && $oldPdf->value) {
                Storage::disk('public')->delete($oldPdf->value);
            }
            $path = $request->file('kvkk_pdf')->store('settings/kvkk', 'public');
            Setting::updateOrCreate(
                ['key' => 'kvkk_pdf'],
                ['value' => $path]
            );
        }
        // ===================================================================

        // ================== 6. MAIL LOG AYARLARI ==================
        Setting::updateOrCreate(
            ['key' => 'mail_log_allowed_roles'],
            ['value' => json_encode($request->input('mail_log_allowed_roles', []))]
        );
        Setting::updateOrCreate(
            ['key' => 'mail_log_allowed_users'],
            ['value' => json_encode(array_map('intval', $request->input('mail_log_allowed_users', [])))]
        );
        Setting::updateOrCreate(
            ['key' => 'mail_log_auto_cleanup_days'],
            ['value' => $request->input('mail_log_auto_cleanup_days', '0')]
        );
        // ===================================================================

        // ================== KAYDETME SONU ==================

        // 5. SEKME HATIRLAMA (GİZLİ INPUTTAN ALIYORUZ)
        // Eğer formdan 'active_tab_input' gelmediyse varsayılan 'genel' olsun.
        $tabToOpen = $request->input('active_tab_input', 'genel');

        return back()
            ->with('success', 'Ayarlar başarıyla kaydedildi.')
            ->with('activeTab', $tabToOpen);
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
        $validEmails = array_filter($cleanedEmails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        return !empty($validEmails) ? implode(',', $validEmails) : null;
    }
}
