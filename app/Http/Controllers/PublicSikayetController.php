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
use App\Models\Iaa; // <-- EKLEYİN
use App\Models\IaaWorkflow; // <-- EKLEYİN
use App\Models\IaaProgressUpdate; // <-- EKLEYİN
use Illuminate\Support\Facades\Notification; // Bildirim göndermek için
use App\Notifications\MusteriGeriBildirimBildirimi; // Oluşturduğumuz bildirim sınıfı
use App\Notifications\YeniMusteriSikayetiBildirimi;
use App\Traits\ComplaintNotificationTrait;



// === YENİ EKLENENLER ===
use App\Models\User; // Yönetici/Kullanıcı e-postalarını çekmek için
use App\Mail\YeniSikayetBildirimi; // Yöneticiye giden mail sınıfı
use Spatie\Permission\Models\Role; // Rol'e göre kullanıcı çekmek için

// === YENİ EKLENENLER SONU ===

class PublicSikayetController extends Controller
{
    use ComplaintNotificationTrait;
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
            // YENİ: Alt Kategori Kuralları
            'sikayet_alt_kategori_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== 'other' && !is_null($value)) {
                        if (!\App\Models\SikayetAltKategori::where('id', $value)->exists()) {
                            $fail('Geçersiz alt kategori.');
                        }
                    }
                },
            ],
            'sikayet_alt_kategori_diger' => 'nullable|string|required_if:sikayet_alt_kategori_id,other|max:500',
            'musteri_sikayet_tarihi' => 'required|date|before_or_equal:today', // Geçmiş veya bugün olabilir
            'musteri_sikayet_konusu' => 'required|string|max:255',
            'musteri_sikayet_detayi' => 'required|string|min:20', // Minimum karakter ekleyebiliriz
            'dosyalar.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4|max:10240', // Max 10MB
        ]);

        // Transaction başlat
        DB::beginTransaction();
        try {
            $sikayetData = $validated; // Doğrulanmış veriyi al
            // YENİ: "Diğer" seçeneği mantığını uygula
            if ($request->sikayet_alt_kategori_id === 'other') {
                // "Diğer" seçildiyse ID null olmalı, açıklama kaydedilmeli
                $sikayetData['sikayet_alt_kategori_id'] = null;
            } else {
                // Normal kategori seçildiyse, açıklama null olmalı
                $sikayetData['sikayet_alt_kategori_diger'] = null;
            }
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

                // === BURAYA EKLEME YAPIYORUZ ===
                // Şifreyi log dosyasına yazdırıyoruz ki mail gitmese bile görebilesiniz.
                Log::info('=============================================');
                Log::info('YENİ MÜŞTERİ ŞİKAYETİ OLUŞTURULDU (TEST)');
                Log::info('Müşteri: ' . $request->musteri_adi);
                Log::info('Token: ' . $token);
                Log::info('OLUŞTURULAN ŞİFRE: ' . $plainPassword);
                Log::info('=============================================');
                // =================================

                $sikayetData['guest_password_hash'] = Hash::make($plainPassword);
                
                // Varsayılan Puan Ataması (Misafir için)
                $sikayetData['musteri_puan'] = (int) (Setting::where('key', 'kurul_default_puan')->value('value') ?? 0);
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

            // Dosyaları kaydetme
            if ($request->hasFile('dosyalar')) {
                // İsimlendirme verilerini hazırla
                $kategoriAd = \App\Models\SikayetKategori::find($sikayetData['sikayet_kategorisi_id'])->ad ?? 'kategori';
                $kategoriAd = \Illuminate\Support\Str::slug($kategoriAd, '_');

                $tarihSaat = now()->format('dmY_Hi');

                $rolAd = $isGuest ? 'kayitsiz_musteri' : (Auth::user()->getRoleNames()->first() ?? (Auth::user()->is_personnel ? 'personel' : 'kayitli_musteri'));
                $rolAd = \Illuminate\Support\Str::slug($rolAd, '_');

                $musteriAd = $sikayetData['musteri_adi'] ?? 'bilinmiyor';
                $musteriAd = \Illuminate\Support\Str::slug($musteriAd, '');

                foreach ($request->file('dosyalar') as $dosya) {
                    $orijinalUzanti = $dosya->getClientOriginalExtension();

                    $yeniDosyaAdi = "{$kategoriAd}_{$tarihSaat}_{$rolAd}_{$musteriAd}.{$orijinalUzanti}";

                    $sayac = 1;
                    $geciciDosyaAdi = $yeniDosyaAdi;
                    while (\Illuminate\Support\Facades\Storage::disk('public')->exists('sikayet_dosyalari/' . $geciciDosyaAdi)) {
                        $geciciDosyaAdi = "{$kategoriAd}_{$tarihSaat}_{$rolAd}_{$musteriAd}_{$sayac}.{$orijinalUzanti}";
                        $sayac++;
                    }
                    $yeniDosyaAdi = $geciciDosyaAdi;

                    $path = $dosya->storeAs('sikayet_dosyalari', $yeniDosyaAdi, 'public');

                    if ($path === false) {
                        Log::error('Public şikayet formu dosya kaydedilemedi: ' . $dosya->getClientOriginalName());
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
            
            // === BİLDİRİM GÖNDERME ===
            try {
                $this->sendNewComplaintNotification($sikayet);
            } catch (\Exception $e) {
                Log::error('Misafir şikayeti bildirim (YeniMusteriSikayetiBildirimi) gönderilemedi. Hata: ' . $e->getMessage());
            }
            // =========================

            // === YÖNETİCİ BİLDİRİMİ GÖNDERME ===
            // Bu işlem artık App\Observers\MusteriSikayetiObserver sınıfında otomatik yapılıyor.
            // Çift bildirim gitmemesi için buradaki manuel çağrıyı kaldırdık.
            // ===================================

            // === Yönlendirme ve E-posta ===
            if ($isGuest && $token && $plainPassword) {
                // Misafir ise e-posta gönder ve takip sayfasına yönlendir
                try {
                    // 1. Alıcı Listesini Belirle (Çoklu Alıcı Desteği)
                    $emails = collect([$validated['musteri_iletisim']]);

                    // E-posta adresine sahip kullanıcıyı bul ve bağlı olduğu firmayı al
                    $userWithEmail = \App\Models\User::where('email', $validated['musteri_iletisim'])->first();
                    if ($userWithEmail && $userWithEmail->customer_id) {
                        $customer = $userWithEmail->customer;
                        
                        // Firmanın genel mailini ekle
                        if ($customer->email) {
                            $emails->push($customer->email);
                        }

                        // Firmanın diğer tüm yetkililerini ekle
                        $otherReps = $customer->users()->pluck('email');
                        $emails = $emails->merge($otherReps);
                    }

                    // Tekilleştir ve geçersizleri temizle
                    $uniqueEmails = $emails->unique()->filter()->toArray();

                    // Her birine mail gönder
                    foreach ($uniqueEmails as $email) {
                        try {
                            Mail::to($email)->send(new SikayetOnayMail($sikayet, $plainPassword));
                        } catch (\Exception $e) {
                            \Log::error('Şikayet onay e-postası gönderilemedi ('.$email.'): ' . $e->getMessage());
                            \App\Helpers\MailLogHelper::logFailure(
                                $sikayet,
                                'Şikayet Onay E-postası',
                                $email,
                                $e->getMessage()
                            );
                        }
                    }

                    return redirect()->route('public.sikayet.show', ['token' => $token])
                        ->with('success', 'Şikayetiniz başarıyla alındı! Takip bilgileri ilgili tüm yetkililere iletildi.');

                } catch (\Exception $e) {
                    Log::error('Şikayet onay süreci genel hatası. Şikayet ID: ' . $sikayet->id . ' Hata: ' . $e->getMessage());
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

        // === YENİ EKLEME BAŞLANGICI ===
        $totalSteps = 0;
        $completedSteps = 0;

        // Eğer şikayet bir projeye dönüştüyse (iaa_id'si varsa)
        if ($sikayet->iaa_id) {
            // İlgili atama kaydını (iaa_talepleri) bul
            $assignment = DB::table('iaa_talepleri')
                ->where('iaa_id', $sikayet->iaa_id)
                ->first();

            // Atama kaydı ve workflow ID'si varsa devam et
            if ($assignment && $assignment->iaa_workflow_id) {
                // Toplam adım sayısını bul
                if (!empty($assignment->workflow_snapshot)) {
                    $snapshotData = json_decode($assignment->workflow_snapshot, true);
                    $totalSteps = count($snapshotData);
                } else {
                    $workflow = IaaWorkflow::find($assignment->iaa_workflow_id);
                    if ($workflow) {
                        $totalSteps = $workflow->steps()->count();
                    }
                }

                // Tamamlanan adım sayısını bul
                $completedSteps = IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
                    ->whereNotNull('completed_at')
                    ->count();
            }
        }
        // === YENİ EKLEME SONU ===

        // Yorumları çek (Bu kod sizin 'show' metodunuzda eksikti, ekliyoruz)
        $yorumlar = $sikayet->iaaProjesi // Bu, Iaa.php'deki 'hasOne' ilişkisidir
            ? $sikayet->iaaProjesi->yorumlar()->latest()->get() // 'yorumlar' ilişkisi
            : collect(); // Proje yoksa boş koleksiyon

        // b. Kullanıcıya şikayet detay sayfasını göster
        // View'e yeni değişkenleri gönder
        return view('public.sikayet.sikayet-detay', compact(
            'sikayet',
            'yorumlar',
            'totalSteps',      // <-- EKLENDİ
            'completedSteps'   // <-- EKLENDİ
        ));
    }

    /**
     * Kayıtsız müşterinin giriş denemesini işler.
     */
    public function guestLogin(Request $request, $token)
    {
        // 1. Token ile şikayeti bul
        $sikayet = MusteriSikayeti::where('takip_token', $token)->firstOrFail();

        // 2. Gelen veriyi doğrula
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $email = strtolower($credentials['email']);
        $password = $credentials['password'];

        // 3. ÖNCELİK 1: Yeni sikayet_guest_passwords tablosundan kontrol et
        $guestPasswords = \App\Models\SikayetGuestPassword::where('musteri_sikayeti_id', $sikayet->id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->get();

        foreach ($guestPasswords as $guestPw) {
            if (Hash::check($password, $guestPw->password_hash)) {
                // Eşleşme bulundu - Session'a giriş yap
                Session::put('sikayet_logged_in_' . $sikayet->takip_token, true);
                return redirect()->route('public.sikayet.show', ['token' => $sikayet->takip_token])
                    ->with('success', 'Başarıyla giriş yaptınız.');
            }
        }

        // 4. ÖNCELİK 2: Eski guest_password_hash sütununa fallback
        if ($sikayet->guest_password_hash && strtolower($sikayet->musteri_iletisim) === $email) {
            if (Hash::check($password, $sikayet->guest_password_hash)) {
                Session::put('sikayet_logged_in_' . $sikayet->takip_token, true);
                return redirect()->route('public.sikayet.show', ['token' => $sikayet->takip_token])
                    ->with('success', 'Başarıyla giriş yaptınız.');
            }
        }

        // 5. Eşleşme bulunamadı
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

        // YENİ: Mevcut seçime göre alt kategorileri doldur
        $altKategoriler = [];
        if ($sikayet->sikayet_kategorisi_id) {
            $altKategoriler = \App\Models\SikayetAltKategori::where('sikayet_kategori_id', $sikayet->sikayet_kategorisi_id)
                ->orderBy('ad')
                ->get();
        }

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

            // c. Yeni dosyaları kaydet
            if ($request->hasFile('dosyalar')) {
                // İsimlendirme verilerini hazırla (Burası public view üzerinden, auth durdurulmuş olabilir)
                $kategoriAd = $sikayet->sikayetKategori->ad ?? 'kategori';
                $kategoriAd = \Illuminate\Support\Str::slug($kategoriAd, '_');

                $tarihSaat = now()->format('dmY_Hi');

                $rolAd = Auth::check() ? (Auth::user()->getRoleNames()->first() ?? (Auth::user()->is_personnel ? 'personel' : 'kayitli_musteri')) : 'kayitsiz_musteri';
                $rolAd = \Illuminate\Support\Str::slug($rolAd, '_');

                $musteriAd = $sikayet->musteri_adi ?? 'bilinmiyor';
                $musteriAd = \Illuminate\Support\Str::slug($musteriAd, '');

                foreach ($request->file('dosyalar') as $dosya) {
                    $orijinalUzanti = $dosya->getClientOriginalExtension();

                    $yeniDosyaAdi = "{$kategoriAd}_{$tarihSaat}_{$rolAd}_{$musteriAd}.{$orijinalUzanti}";

                    $sayac = 1;
                    $geciciDosyaAdi = $yeniDosyaAdi;
                    while (\Illuminate\Support\Facades\Storage::disk('public')->exists('sikayet_dosyalari/' . $geciciDosyaAdi)) {
                        $geciciDosyaAdi = "{$kategoriAd}_{$tarihSaat}_{$rolAd}_{$musteriAd}_{$sayac}.{$orijinalUzanti}";
                        $sayac++;
                    }
                    $yeniDosyaAdi = $geciciDosyaAdi;

                    $path = $dosya->storeAs('sikayet_dosyalari', $yeniDosyaAdi, 'public');

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


        // 1. Token ile şikayeti bul
        $sikayet = MusteriSikayeti::where('takip_token', $token)->firstOrFail();

        // 2. Projenin tamamlanıp tamamlanmadığını kontrol et (Ekstra güvenlik)
        $proje = \App\Models\Iaa::find($sikayet->iaa_id);
        if (!$proje || $proje->durum != 'Tamamlandı') {
            return back()->with('error', 'Bu işlem için proje sürecinin tamamlanması gerekmektedir.');
        }

        // 3. Gelen veriyi doğrula
        $validated = $request->validate([
            'feedback' => 'required|in:Onaylandı,Reddedildi,Revizyon İstendi',
            'feedback_note' => 'nullable|string|max:1000',
        ]);

        // 4. Veritabanını güncelle
        $sikayet->update([
            'musteri_feedback' => $validated['feedback'],
            'musteri_feedback_note' => $validated['feedback_note'],
            'feedback_ip' => $request->ip(), // IP Adresi
            'feedback_user_agent' => $request->userAgent(), // Tarayıcı Bilgisi
        ]);

        // GÜVENLİK KONTROLÜ: Eğer tarayıcıda personel oturumu açıksa, ID'sini kaydet!
        if (Auth::check()) {
            $updateData['feedback_by_user_id'] = Auth::id();
        }


        // 6. Log Mesajını Oluştur
        $logMesaji = "Müşteri çözümü değerlendirdi: " . $validated['feedback'];
        if (Auth::check()) {
            $logMesaji .= " (DİKKAT: Bu işlem " . Auth::user()->name . " oturumu açıkken yapıldı!)";
        }
        $logMesaji .= " [IP: " . $request->ip() . "]";

        \App\Models\MusteriSikayetiLog::create([
            'musteri_sikayeti_id' => $sikayet->id,
            'eylem' => 'Müşteri Geri Bildirimi',
            'aciklama' => $logMesaji
        ]);

        // =================================================================
        // 6. BİLDİRİM GÖNDERME (YENİ EKLENEN KISIM)
        // =================================================================
        try {
            // A. Alıcı Listesini Oluştur
            $recipients = collect();

            // 1. Superadminler
            $recipients = $recipients->merge(User::role('Superadmin')->get());

            // 2. Bölüm Kalite Yöneticileri
            if ($sikayet->sikayet_kategorisi_id) {
                $kaliteYoneticileri = User::role('Bölüm Kalite Yöneticisi')
                    ->whereHas('yonettigiSikayetKategorileri', function ($q) use ($sikayet) {
                        $q->where('sikayet_kategorileri.id', $sikayet->sikayet_kategorisi_id);
                    })->get();

                if ($kaliteYoneticileri->isNotEmpty()) {
                    $recipients = $recipients->merge($kaliteYoneticileri);
                }
            }

            // 3. Müşteri Şikayeti Çözüm Liderleri
            $recipients = $recipients->merge(User::role('Müşteri Şikayeti Çözüm Lideri')->get());

            // 4. Çözüm Takımı Lideri (Varsa)
            if ($sikayet->cozumTakimi && $sikayet->cozumTakimi->lider) {
                $recipients->push($sikayet->cozumTakimi->lider);
            }

            // 5. Proje (İAA) Takım Lideri (Varsa)
            if ($proje && $proje->atananTakim && $proje->atananTakim->lider) {
                $recipients->push($proje->atananTakim->lider);
            }

            // Tekilleştir (Aynı kişiye 2 kere gitmesin)
            $recipients = $recipients->unique('id');

            // Bildirimi Gönder
            // Not: MusteriGeriBildirimBildirimi sınıfını oluşturduğunuzdan emin olun.
            Notification::send($recipients, new MusteriGeriBildirimBildirimi(
                $sikayet,
                $validated['feedback'],
                $validated['feedback_note']
            ));

        } catch (\Exception $e) {
            // Bildirim hatası kullanıcı deneyimini bozmasın, loglayalım ve tekrar deneme verisini kaydedelim.
            \Log::error('Müşteri geri bildirim bildirimi gönderilemedi: ' . $e->getMessage());
            \App\Helpers\MailLogHelper::logFailure(
                $sikayet,
                'Müşteri Çözüm Değerlendirme Bildirimi',
                $recipients ?? collect(),
                $e->getMessage(),
                \App\Notifications\MusteriGeriBildirimBildirimi::class,
                [
                    'recipient_ids' => ($recipients ?? collect())->pluck('id')->toArray(),
                    'params' => [
                        'sikayet' => $sikayet,
                        'feedback' => $validated['feedback'],
                        'note' => $validated['feedback_note']
                    ]
                ],
                $sikayet->sikayetKategori->bolum_id ?? null
            );
        }
        // =================================================================

        // 7. Başarı mesajı ile yönlendir
        return redirect()->route('public.sikayet.show', ['token' => $token])
            ->with('success', 'Geri bildiriminiz başarıyla kaydedildi. Teşekkür ederiz!');
    }



}