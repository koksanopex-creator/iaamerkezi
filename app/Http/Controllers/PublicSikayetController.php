<?php

namespace App\Http\Controllers;

    // Gerekli sınıfları import ediyoruz
    use App\Http\Controllers\Controller;
    use App\Models\SikayetKategori; // Formda kategori göstermek için
    use App\Models\Setting;        // Belki ayarlar için gerekir
    use App\Models\MusteriSikayetiLog; // Loglama için
    use App\Mail\SikayetOnayMail;      // E-posta gönderimi için
    use App\Mail\SikayetCozumMail;     // E-posta gönderimi için
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;     // Transaction için
    use Illuminate\Support\Facades\Log;      // Hata loglama için
    use Illuminate\Validation\Rule; // Rule'ı ekleyelim
    use Illuminate\Support\Facades\Storage; // Dosya işlemleri için
    use App\Models\MusteriSikayetiDosyasi; // Dosya kaydı için
    use App\Models\MusteriSikayeti; // <-- BU SATIR ÇOK ÖNEMLİ! Doğru yazıldığından emin ol.

    // === YENİ EKLENENLER ===
    use App\Models\User; // Yönetici/Kullanıcı e-postalarını çekmek için
    use App\Mail\YeniSikayetBildirimi; // Yöneticiye giden mail sınıfı
    use Spatie\Permission\Models\Role; // Rol'e göre kullanıcı çekmek için
    // === YENİ EKLENENLER SONU ===

    class PublicSikayetController extends Controller
    {
        /**
         * Public şikayet oluşturma formunu gösterir.
         */
        public function create()
        {
            // TODO: sikayet-form.blade.php view'ını döndür.
            // Gerekirse kategorileri vs. çekip view'e gönder.
            // Eğer kullanıcı giriş yapmışsa, bilgilerini forma önceden doldurabiliriz.

            // Örnek:
            $kategoriler = SikayetKategori::orderBy('ad')->get();
            $user = Auth::user(); // Giriş yapmış kullanıcıyı al (null olabilir)
            // return view('sikayet-form', compact('kategoriler', 'user'));

            // Şimdilik boş bırakalım veya basit bir view döndürelim
            return view('public.sikayet.sikayet-form', compact('kategoriler', 'user')); // compact güncellendi
        }

        /**
         * Public şikayet formundan gelen veriyi kaydeder.
         */
        /**
         * Public şikayet formundan gelen veriyi kaydeder.
         */
        public function store(Request $request)
        {


     // TODO: 1. Gelen veriyi doğrula (Validation).
            // TODO: 2. Eğer Auth::check() ise kayıtlı kullanıcıdır, olusturan_kurul_uyesi_id ile kaydet.
            // TODO: 3. Eğer Auth::guest() ise kayıtsız kullanıcıdır:
            // TODO:    a. Benzersiz takip_token oluştur (örn: Str::random(12), DB'de var mı kontrol et).
            // TODO:    b. Rastgele şifre oluştur (örn: Str::random(6)).
            // TODO:    c. Şifreyi hash'le (Hash::make()).
            // TODO:    d. Şikayeti DB'ye kaydet (token ve hashlenmiş şifre ile).
            // TODO:    e. Müşteriye e-posta gönder (link, hashlenmemiş şifre).
            // TODO:    f. Kullanıcıyı 'sikayet.show' rotasına yönlendir (Teşekkür mesajı için).

         

            // 1. Gelen veriyi doğrula
            $validated = $request->validate([
                'musteri_adi' => 'required|string|max:255',
                'musteri_iletisim' => 'required|email|max:255', // E-posta zorunlu ve geçerli formatta olmalı
                'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı',
                'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id', // Kategori zorunlu
                'musteri_sikayet_tarihi' => 'required|date|before_or_equal:today', // Geçmiş veya bugün olabilir
                'musteri_sikayet_konusu' => 'required|string|max:255',
                'musteri_sikayet_detayi' => 'required|string|min:20', // Minimum karakter ekleyebiliriz
                'dosyalar.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4|max:10240', // Max 10MB
            ]);

            // Transaction başlat
            DB::beginTransaction();
            try {
                $sikayetData = $validated; // Doğrulanmış veriyi al
                $sikayetData['musteri_durum'] = 'Yeni'; // Başlangıç durumu
                $sikayetData['musteri_oncelik'] = 'Normal'; // Varsayılan öncelik

                $isGuest = Auth::guest(); // Kullanıcı misafir mi?
                $plainPassword = null;    // Misafir için şifre
                $token = null;            // Misafir için token

                if ($isGuest) {
                    // Kayıtsız Kullanıcı Mantığı
                    $sikayetData['olusturan_kurul_uyesi_id'] = null;

                    // Benzersiz Token Oluşturma (12 karakter yeterli olmalı)
                    do {
                        $token = Str::random(12);
                    } while (MusteriSikayeti::where('takip_token', $token)->exists());
                    $sikayetData['takip_token'] = $token;

                    // Rastgele Şifre Oluşturma (8 karakter)
                    $plainPassword = Str::random(8);
                    $sikayetData['guest_password_hash'] = Hash::make($plainPassword);

                } else {
                    // Kayıtlı Kullanıcı Mantığı
                    $sikayetData['olusturan_kurul_uyesi_id'] = Auth::id();
                    // Kayıtlı kullanıcı için puan eklemeyi burada yapmayalım,
                    // Admin tarafındaki `store` farklı amaçla puan veriyordu (Kurul üyesi olduğu için).
                    // Public formdan giren kayıtlı kullanıcıya puan vermek gereksiz olabilir.
                    // Eğer istersen ekleyebiliriz.
                }

                // Şikayeti oluştur
                $sikayet = MusteriSikayeti::create($sikayetData);

                // Dosyaları kaydetme (Admin Controller'daki gibi)
                if ($request->hasFile('dosyalar')) {
                    foreach ($request->file('dosyalar') as $dosya) {
                        // Dosya yolunu şikayet ID'si ile ilişkilendirmek daha düzenli olabilir
                        // Örn: 'sikayet_dosyalari/' . $sikayet->id . '/' . uniqid() . '.' . $dosya->extension()
                        // Şimdilik admin controller'daki gibi tutalım:
                        $path = $dosya->store('sikayet_dosyalari', 'public');

                        if ($path === false) {
                            Log::error('Public şikayet formu dosya kaydedilemedi: ' . $dosya->getClientOriginalName());
                            // Hata durumunda işlemi geri alıp hata mesajı verebiliriz ama şimdilik devam etsin
                            continue;
                        }

                        $sikayet->dosyalar()->create([
                            'dosya_yolu' => $path,
                            'orijinal_adi' => $dosya->getClientOriginalName(),
                            'mime_tipi' => $dosya->getMimeType(),
                        ]);
                    }
                }

                // Her şey yolundaysa transaction'ı onayla
                DB::commit();

                // === YENİ OLAYI BURADA TETİKLE ===
                try {
                    event(new \App\Events\SikayetOlusturuldu($sikayet));
                } catch (\Exception $e) {
                    Log::error('Broadcast olayı gönderilemedi: ' . $e->getMessage());
                }
                // === TETİKLEME SONU ===

                // === YÖNETİCİ BİLDİRİMİ GÖNDERME (YENİ EKLENEN KISIM) ===
                try {
                    // Ayarlara göre ilgili yöneticilere/rollere mail gönder
                    $this->notifyAdminsAboutNewComplaint($sikayet);
                } catch (\Exception $e) {
                    Log::error('Yönetici bildirim maili gönderilemedi. Şikayet ID: ' . $sikayet->id . ' Hata: '. $e->getMessage());
                    // Bu hatanın kullanıcıyı durdurmasına gerek yok, sadece loglansın.
                }
                // === YÖNETİCİ BİLDİRİMİ SONU ===

                // === Yönlendirme ve E-posta ===
                if ($isGuest && $token && $plainPassword) {
                    // Misafir ise e-posta gönder ve takip sayfasına yönlendir
                    try {
                        Mail::to($validated['musteri_iletisim'])->queue(new SikayetOnayMail($sikayet, $plainPassword)); // <-- DÜZELTİLDİ
                        return redirect()->route('public.sikayet.show', ['token' => $token])
                                        ->with('success', 'Şikayetiniz başarıyla alındı! ...');
            
                    } catch (\Exception $e) {
                        Log::error('Şikayet onay e-postası gönderilemedi. Şikayet ID: ' . $sikayet->id . ' Hata: ' . $e->getMessage());
                        // E-posta gitmese bile şikayet kaydedildi, takip sayfasına yönlendir ama uyarı ver
                        return redirect()->route('public.sikayet.show', ['token' => $token])
                                        ->with('warning', 'Şikayetiniz alındı ancak takip bilgileri e-postanıza gönderilirken bir sorun oluştu. Lütfen takip kodunuzu not alın: ' . $token);
                    }
                } else {
                    // Kayıtlı kullanıcı ise basit bir başarı mesajı ile forma geri yönlendir (veya başka bir sayfaya)
                    return redirect()->route('public.sikayet.create')
                                    ->with('success', 'Şikayetiniz başarıyla alındı!');
                    // Veya kayıtlı kullanıcı için ayrı bir "Şikayetlerim" sayfası varsa oraya yönlendirilebilir:
                    // return redirect()->route('user.sikayetlerim')->with('success', 'Şikayetiniz başarıyla alındı!');
                }

            } catch (\Exception $e) {
                // Hata olursa transaction'ı geri al
                DB::rollBack();
                Log::error('Public şikayet kaydında hata: ' . $e->getMessage());
                // Hata mesajıyla forma geri dön
                return back()->with('error', 'Şikayetiniz kaydedilirken beklenmedik bir hata oluştu. Lütfen tekrar deneyin veya yönetici ile iletişime geçin.')->withInput();
            }
        }
       

        /**
         * Verilen token'a ait şikayet detayını veya giriş formunu gösterir.
         */
        public function show($token)
        {
                // TODO: 1. Token ile şikayeti bul (firstOrFail).
                // TODO: 2. Eğer guest_password_hash varsa VE Session'da 'sikayet_logged_in_' . $token anahtarı yoksa:
                // TODO:    a. sikayet-login.blade.php view'ını döndür.
                // TODO: 3. Eğer Session'da varsa VEYA şikayet kayıtlı kullanıcıya aitse ve o kullanıcı giriş yapmışsa:
                // TODO:    a. sikayet-detay.blade.php view'ını döndür (şikayet verisiyle).
                // TODO:    b. View içinde düzenleme kilidini (edit_locked_at) kontrol et.
                // TODO:    c. View içinde durumu ('Kapatıldı') kontrol edip feedback butonlarını göster/gizle.
                // TODO:    d. View içinde proje linkini (atanan_takim_id varsa) oluştur.

                // Şimdilik boş bırakalım veya basit bir view döndürelim
                    /**
                 * Verilen token'a ait şikayet detayını veya giriş formunu gösterir.
                 * GÜNCELLENDİ: Session kontrolü eklendi.
                 */
            
                // 1. Token ile şikayeti bul (firstOrFail 404 hatası verir)
                $sikayet = MusteriSikayeti::where('takip_token', $token)->firstOrFail();

                // 2. Bu şikayet için özel session anahtarı belirle
                $sessionKey = 'sikayet_logged_in_' . $sikayet->takip_token;

                // 3. Giriş Gerekli mi?
                // Şartlar: Şifre (guest_password_hash) varsa (yani misafir şikayeti ise)
                // VE kullanıcı bu şikayet için session'da giriş yapmamışsa
                if ($sikayet->guest_password_hash && !Session::has($sessionKey)) {

                    // a. Kullanıcıya giriş formunu göster
                    return view('public.sikayet.sikayet-login', compact('sikayet'));
                }

                // 4. Giriş Gerekli Değilse (ya session var ya da kayıtlı kullanıcıya ait)
                // TODO: Kayıtlı kullanıcıya aitse ve giriş yapan ID'si uyuşmuyorsa ne yapacağız?
                // Şimdilik token'ı bilenin (veya giriş yapanın) görebileceğini varsayıyoruz.
                // İlgili ilişkileri yükle (Model'de 'dosyalar' ve 'sikayetKategori' ilişkileri olmalı)
                $sikayet->load('dosyalar', 'sikayetKategori', 'cozumTakimi', 'loglar.user');

                // b. Kullanıcıya şikayet detay sayfasını göster
                return view('public.sikayet.sikayet-detay', compact('sikayet'));
            }

        /**
         * Kayıtsız müşterinin giriş denemesini işler.
         */
        public function guestLogin(Request $request, $token)
        {
            // TODO: 1. Token ile şikayeti bul (firstOrFail).
            // TODO: 2. Gelen e-posta ve şifreyi doğrula (Validation).
            // TODO: 3. Şikayetin musteri_iletisim'i ile gelen e-postanın eşleştiğini kontrol et.
            // TODO: 4. Gelen şifre ile DB'deki guest_password_hash'i Hash::check() ile kontrol et.
            // TODO: 5. Eşleşme varsa:
            // TODO:    a. Session::put('sikayet_logged_in_' . $token, true); // Veya benzeri bir işaretçi
            // TODO:    b. 'sikayet.show' rotasına yönlendir.
            // TODO: 6. Eşleşme yoksa:
            // TODO:    a. Hata mesajıyla login formuna geri yönlendir.

            // Şimdilik boş bırakalım
            // 1. Token ile şikayeti bul
            $sikayet = MusteriSikayeti::where('takip_token', $token)->firstOrFail();

            // 2. Gelen veriyi doğrula
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6', // Şifremiz 8 karakterdi ama min:6 diyelim
            ]);

            // 3. E-posta ve Şifreyi Kontrol Et
            // a. E-posta adresi, şikayetteki e-posta ile eşleşiyor mu?
            //    (Büyük/küçük harf duyarlılığını önlemek için strtolower kullanabiliriz)
            if (strtolower($sikayet->musteri_iletisim) !== strtolower($credentials['email'])) {
                // E-posta eşleşmiyorsa hata ver
                return back()->with('error', 'E-posta adresi veya şifre hatalı.');
            }

            // b. Gelen şifre, hash'lenmiş şifre ile eşleşiyor mu?
            if (Hash::check($credentials['password'], $sikayet->guest_password_hash)) {
                // 5. Eşleşme Varsa:
                // a. Session'a bu şikayet için giriş yaptığını işaretle
                Session::put('sikayet_logged_in_' . $sikayet->takip_token, true);

                // b. 'sikayet.show' rotasına (detay sayfasına) yönlendir
                return redirect()->route('public.sikayet.show', ['token' => $sikayet->takip_token])
                                ->with('success', 'Başarıyla giriş yaptınız.');
            }

            // 6. Eşleşme Yoksa:
            // a. Hata mesajıyla login formuna geri yönlendir
            return back()->with('error', 'E-posta adresi veya şifre hatalı.');
        }


            /**
             * Müşterinin (kilitlenmemiş) şikayetini düzenleme formunu gösterir.
             */

            public function edit($token)
            {
                $sikayet = MusteriSikayeti::where('takip_token', $token)->firstOrFail();
                $sessionKey = 'sikayet_logged_in_' . $sikayet->takip_token;

                // Yetki Kontrolü...
                if ($sikayet->guest_password_hash && !Session::has($sessionKey)) {
                    return redirect()->route('public.sikayet.show', ['token' => $token])->with('error', 'Düzenleme yapmak için lütfen giriş yapın.');
                }

                // Kilit Kontrolü...
                if (!is_null($sikayet->edit_locked_at) || $sikayet->musteri_durum != 'Yeni') {
                    return redirect()->route('public.sikayet.show', ['token' => $token])->with('error', 'Bu şikayet işleme alındığı için artık düzenlenemez.');
                }

                // === GÜNCELLEME BURADA ===
                // Şikayete ait dosyaları ve kategorileri de yükle
                $sikayet->load('dosyalar');
                $kategoriler = SikayetKategori::orderBy('ad')->get();
                // === GÜNCELLEME SONU ===

                // Düzenleme view'ını göster
                return view('public.sikayet.sikayet-edit', compact('sikayet', 'kategoriler'));
            }

        /**
         * Müşterinin (kilitlenmemiş) şikayetini güncellemesini sağlar.
         */
        public function update(Request $request, $token)
        {
            // TODO: 1. Token ile şikayeti bul (firstOrFail).
            // TODO: 2. Yetki kontrolü: Kullanıcı giriş yapmış mı (Session veya Auth)? Şikayet kilitlenmiş mi (edit_locked_at)?
            // TODO: 3. Gelen veriyi doğrula.
            // TODO: 4. Şikayeti güncelle.
            // TODO: 5. Log at? (Opsiyonel)
            // TODO: 6. Başarı mesajıyla 'sikayet.show' rotasına yönlendir.

            // 1. Token ile şikayeti bul
            $sikayet = MusteriSikayeti::where('takip_token', $token)->firstOrFail();
            $sessionKey = 'sikayet_logged_in_' . $sikayet->takip_token;

            // 2. Yetki Kontrolü: Kullanıcı bu şikayet için giriş yapmış mı?
            if ($sikayet->guest_password_hash && !Session::has($sessionKey)) {
                // (Kayıtlı kullanıcıysa Auth::check() de eklenebilir)
                return redirect()->route('public.sikayet.show', ['token' => $token])->with('error', 'Düzenleme yapmak için lütfen giriş yapın.');
            }

            // 3. Kilit Kontrolü: Şikayet kilitlenmiş mi veya durumu 'Yeni' değil mi?
            if (!is_null($sikayet->edit_locked_at) || $sikayet->musteri_durum != 'Yeni') {
                return redirect()->route('public.sikayet.show', ['token' => $token])->with('error', 'Bu şikayet işleme alındığı için artık düzenlenemez.');
            }

            // 4. Gelen veriyi doğrula (store metoduna benzer)
            $validated = $request->validate([
                'musteri_adi' => 'required|string|max:255',
                'musteri_iletisim' => 'required|email|max:255',
                'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı',
                'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id',
                'musteri_sikayet_tarihi' => 'required|date|before_or_equal:today',
                'musteri_sikayet_konusu' => 'required|string|max:255',
                'musteri_sikayet_detayi' => 'required|string|min:20',
                // Dosya validasyonları (admin/edit formuyla aynı)
                'dosyalar_sil' => 'nullable|array',
                'dosyalar_sil.*' => 'integer|exists:musteri_sikayeti_dosyalari,id',
                'dosyalar' => 'nullable|array', // Yeni dosyalar
                'dosyalar.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4|max:10240',
            ]);

            // 5. Veritabanı işlemini Transaction içine al
            DB::beginTransaction();
            try {
                // a. Metin alanlarını güncelle
                // (Validated dizisinden dosya anahtarlarını çıkarıp kalanıyla update yap)
                $updateData = $request->except(['_token', '_method', 'dosyalar', 'dosyalar_sil']);
                $sikayet->update($updateData);

                // b. Seçili dosyaları sil (Admin/SikayetController@update ile aynı)
                if ($request->has('dosyalar_sil')) {
                    $dosyaIdsToSil = $request->input('dosyalar_sil');

                    // Güvenlik: Sadece bu şikayete ait olan dosyaları sil
                    $dosyalar = MusteriSikayetiDosyasi::where('musteri_sikayeti_id', $sikayet->id)
                                                    ->whereIn('id', $dosyaIdsToSil)
                                                    ->get();

                    foreach ($dosyalar as $dosya) {
                        Storage::disk('public')->delete($dosya->dosya_yolu); // Fiziksel dosyayı sil
                        $dosya->delete(); // Veritabanı kaydını sil
                    }
                }

                // c. Yeni dosyaları kaydet (store metoduyla aynı)
                if ($request->hasFile('dosyalar')) {
                    foreach ($request->file('dosyalar') as $dosya) {
                        $path = $dosya->store('sikayet_dosyalari', 'public');
                        if ($path === false) {
                            Log::error('Public şikayet GÜNCELLEME dosya kaydedilemedi: ' . $dosya->getClientOriginalName());
                            continue;
                        }
                        $sikayet->dosyalar()->create([
                            'dosya_yolu' => $path,
                            'orijinal_adi' => $dosya->getClientOriginalName(),
                            'mime_tipi' => $dosya->getMimeType(),
                        ]);
                    }
                }

                DB::commit();

                // 6. Başarı mesajıyla 'sikayet.show' rotasına yönlendir
                return redirect()->route('public.sikayet.show', ['token' => $token])
                                ->with('success', 'Şikayetiniz başarıyla güncellendi.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Public şikayet güncellemede hata: ' . $e->getMessage());
                return back()->with('error', 'Şikayet güncellenirken bir hata oluştu.')->withInput();
            }
        }

        /**
         * Müşterinin çözümle ilgili geri bildirimini kaydeder.
         */
        public function storeFeedback(Request $request, $token)
        {
            // TODO: 1. Token ile şikayeti bul (firstOrFail).
            // TODO: 2. Yetki kontrolü: Kullanıcı giriş yapmış mı? Şikayet durumu 'Kapatıldı' mı?
            // TODO: 3. Gelen geri bildirimi doğrula (feedback türü, not gerekli mi?).
            // TODO: 4. Şikayetin musteri_feedback ve musteri_feedback_note alanlarını güncelle.
            // TODO: 5. MusteriSikayetiLog'a kayıt ekle.
            // TODO: 6. Başarı mesajıyla 'sikayet.show' rotasına yönlendir.

            // Şimdilik boş bırakalım
            return redirect()->route('public.sikayet.show', ['token' => $token])->with('success', 'Geri bildiriminiz (geçici olarak) alındı!');
        }

        // ======================================================================
        // === YENİ EKLENEN YARDIMCI METOT (YÖNETİCİ BİLDİRİMİ İÇİN) ===
        // ======================================================================

        /**
         * Ayarlara göre ilgili kişilere yeni şikayet bildirimini gönderir.
         */
        private function notifyAdminsAboutNewComplaint(MusteriSikayeti $sikayet)
        {
            // Ayarları veritabanından çek (performans için cache'lenebilir)
            $settings = Setting::all()->keyBy('key');
            $recipientEmails = collect();

            // 1. Rollerden gelen kullanıcı e-postaları
            $roleIdsValue = $settings->get('sikayet_notify_role_ids')?->value;
            if (!empty($roleIdsValue)) {
                $roleIds = explode(',', $roleIdsValue);
                $usersFromRoles = User::whereHas('roles', function ($query) use ($roleIds) {
                    $query->whereIn('id', $roleIds);
                })->pluck('email');
                $recipientEmails = $recipientEmails->merge($usersFromRoles);
            }

            // 2. Kullanıcılardan gelen e-postalar
            $userIdsValue = $settings->get('sikayet_notify_user_ids')?->value;
            if (!empty($userIdsValue)) {
                $userIds = explode(',', $userIdsValue);
                $usersFromIds = User::whereIn('id', $userIds)->pluck('email');
                $recipientEmails = $recipientEmails->merge($usersFromIds);
            }

            // 3. Manuel e-postalar
            $manualEmailsValue = $settings->get('sikayet_notify_manual_emails')?->value;
            if (!empty($manualEmailsValue)) {
                $manualEmails = explode(',', $manualEmailsValue);
                $recipientEmails = $recipientEmails->merge($manualEmails);
            }
            
            // 4. (Eski sistemden kalan) sikayet_admin_notification_email
            //    Bu ayarı da listeye ekleyelim, belki hala kullanılıyordur.
            $legacyAdminEmail = $settings->get('sikayet_admin_notification_email')?->value;
            if (!empty($legacyAdminEmail)) {
                $recipientEmails->push($legacyAdminEmail);
            }


            // Tekilleştir (aynı kişiye 5 mail gitmesin) ve boş olanları filtrele
            $finalRecipients = $recipientEmails->filter()->unique();

            if ($finalRecipients->isNotEmpty()) {
                foreach ($finalRecipients as $recipient) {
                    if(filter_var(trim($recipient), FILTER_VALIDATE_EMAIL)) {
                        Mail::to(trim($recipient))->queue(new YeniSikayetBildirimi($sikayet)); // <-- DÜZELTİLDİ
                    }
                }
            }
        }

    }