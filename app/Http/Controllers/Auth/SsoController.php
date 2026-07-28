<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bolum; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Notifications\NewSsoApplicationNotification;
use Illuminate\Support\Facades\Notification;

class SsoController extends Controller
{
    /**
     * Merkezi API'den dönüşü karşılar.
     */
    public function login(Request $request)
    {
        $token = $request->query('token');

        $centralUrl = rtrim(env('CENTRAL_SSO_URL', 'http://127.0.0.1:8001'), '/');
        $internalHttpUrl = str_replace('localhost', '127.0.0.1', $centralUrl);

        if (!$token) {
            return redirect($centralUrl);
        }

        // Merkezi API'ye token'ı doğrulat (Windows cURL localhost IPv6 gecikmesini önlemek için 127.0.0.1 ve timeout kullanılıyor)
        try {
            $response = Http::timeout(5)->get($internalHttpUrl . '/api/auth/verify-sso-token', [
                'token' => $token
            ]);
        } catch (\Exception $e) {
            \Log::error('SSO Doğrulama isteği zaman aşımına uğradı veya başarısız oldu: ' . $e->getMessage());
            return redirect($centralUrl)->with('error', 'Merkezi oturum sunucusuna erişilemedi.');
        }

        if ($response->failed()) {
            return redirect($centralUrl)->with('error', 'Merkezi oturum doğrulanamadı.');
        }

        $centralUser = $response->json('user');

        // İAA Veritabanında Kullanıcıyı Ara
        $localUser = User::where('email', $centralUser['email'])->first();

        // --- MÜŞTERİ KONTROLÜ: Müşteriler asla başvuru ekranına gitmemeli ---
        if (!$localUser && ($centralUser['is_customer'] ?? false)) {
            // E-posta değişmiş olabilir, merkezi_user_id veya customer_id ile ara
            if (!empty($centralUser['id'])) {
                // Merkezi'deki eski e-posta ile kayıtlı müşteriyi bul (customer_id olan)
                $localUser = User::whereNotNull('customer_id')->where(function($q) use ($centralUser) {
                    // Merkezi API user id ile eşleştirme yapılamıyorsa, customer kullanıcıları arasında ismini ara
                    $q->where('email', $centralUser['email']);
                })->first();
            }
        }

        // --- SENARYO A: Kullanıcı İAA'da Kayıtlı ---
        if ($localUser) {
            // İsmini senkronize et
            $localUser->update([
                'name' => trim($centralUser['first_name'] . ' ' . $centralUser['last_name'])
            ]);

            // E-postayı da senkronize et (değişmiş olabilir)
            if ($localUser->email !== $centralUser['email']) {
                $localUser->update(['email' => $centralUser['email']]);
            }

            // Müşteri kullanıcıları her zaman doğrudan giriş yapsın
            if ($localUser->customer_id) {
                Auth::login($localUser);
                return redirect()->intended(route('dashboard'));
            }

            // İAA'daki özel aktiflik kontrolü: onaylandi_mi (sadece personel için)
            if ($localUser->onaylandi_mi) {
                Auth::login($localUser);
                return redirect()->intended(route('dashboard'));
            } else {
                // Eğer reddedilmişse tekrar başvurması için form sayfasına yönlendir
                if (!is_null($localUser->rejected_at)) {
                    $limitActive = \App\Models\Setting::where('key', 'reapply_limit_active')->value('value') !== '0';
                    $limitHours = (int) (\App\Models\Setting::where('key', 'reapply_limit_hours')->value('value') ?? 24);
                    
                    if ($limitActive) {
                        $blockedUntil = $localUser->rejected_at->copy()->addHours($limitHours);
                        if (now()->lt($blockedUntil)) {
                            $remainingHours = ceil(now()->diffInMinutes($blockedUntil) / 60);
                            return redirect()->route('sso.onay_bekliyor')->with('error', "Yeni bir başvuru yapabilmek için reddedilme anından itibaren {$limitHours} saat geçmesi gerekmektedir. Kalan süre: {$remainingHours} saat.");
                        }
                    }
                    session(['sso_user_data' => $centralUser]);
                    return redirect()->route('sso.basvuru_formu');
                }
                session(['pending_user_email' => $centralUser['email']]);
                return redirect()->route('sso.onay_bekliyor');
            }
        }

        // --- SENARYO B: İlk Defa Geliyor (Kayıt Yok) ---
        // Müşteri kullanıcılar başvuru ekranına asla gelmemeli
        if ($centralUser['is_customer'] ?? false) {
            return redirect(rtrim(env('CENTRAL_SSO_URL', 'http://localhost:8001'), '/'))->with('error', 'Müşteri hesabınız İAA sisteminde bulunamadı. Lütfen yöneticinize başvurun.');
        }

        session(['sso_user_data' => $centralUser]);
        return redirect()->route('sso.basvuru_formu');
    }

    /**
     * Yeni Kullanıcı İçin Bölüm Seçim Formu
     */
    public function basvuruFormu()
    {
        $centralUser = session('sso_user_data');
        if (!$centralUser) {
            return redirect(rtrim(env('CENTRAL_SSO_URL', 'http://localhost:8001'), '/'));
        }

        // Eğer kullanıcı veritabanında varsa ve 24 saati dolmamışsa engelle
        $localUser = User::where('email', $centralUser['email'])->first();
        if ($localUser && !$localUser->onaylandi_mi && !is_null($localUser->rejected_at)) {
            $limitActive = \App\Models\Setting::where('key', 'reapply_limit_active')->value('value') !== '0';
            $limitHours = (int) (\App\Models\Setting::where('key', 'reapply_limit_hours')->value('value') ?? 24);
            
            if ($limitActive) {
                $blockedUntil = $localUser->rejected_at->copy()->addHours($limitHours);
                if (now()->lt($blockedUntil)) {
                    $remainingHours = ceil(now()->diffInMinutes($blockedUntil) / 60);
                    return redirect()->route('sso.onay_bekliyor')->with('error', "Yeni bir başvuru yapabilmek için reddedilme anından itibaren {$limitHours} saat geçmesi gerekmektedir. Kalan süre: {$remainingHours} saat.");
                }
            }
        }

        // İAA'daki sadece aktif bölümleri listele
        $bolumler = Bolum::where('is_active', true)->orderBy('ad')->get(); 
        $liderler = \App\Models\User::role('Bölüm Lideri')->get()->groupBy('bolum_id');
        
        return view('auth.sso_basvuru', compact('centralUser', 'bolumler', 'liderler'));
    }

    /**
     * Başvuruyu İAA veritabanına kaydet
     */
    public function basvuruKaydet(Request $request)
    {
        $centralUser = session('sso_user_data');
        if (!$centralUser) {
            return redirect(rtrim(env('CENTRAL_SSO_URL', 'http://localhost:8001'), '/'));
        }

        $request->validate([
            'bolum_id' => 'required|exists:bolumler,id',
        ]);

        // 24 saat engeli kontrolü
        $user = User::where('email', $centralUser['email'])->first();
        if ($user && !$user->onaylandi_mi && !is_null($user->rejected_at)) {
            $limitActive = \App\Models\Setting::where('key', 'reapply_limit_active')->value('value') !== '0';
            $limitHours = (int) (\App\Models\Setting::where('key', 'reapply_limit_hours')->value('value') ?? 24);
            
            if ($limitActive) {
                $blockedUntil = $user->rejected_at->copy()->addHours($limitHours);
                if (now()->lt($blockedUntil)) {
                    $remainingHours = ceil(now()->diffInMinutes($blockedUntil) / 60);
                    return redirect()->route('sso.onay_bekliyor')->with('error', "Yeni bir başvuru yapabilmek için reddedilme anından itibaren {$limitHours} saat geçmesi gerekmektedir. Kalan süre: {$remainingHours} saat.");
                }
            }
        }

        if ($user) {
            $user->update([
                'name' => trim($centralUser['first_name'] . ' ' . $centralUser['last_name']),
                'bolum_id' => $request->bolum_id,
                'onaylandi_mi' => false,
                'rejected_at' => null, // 🔥 Reddedilmeyi sıfırla!
                'tc_kimlik_no' => $centralUser['tc_no'] ?? null,
                'sicil_no' => $centralUser['registration_no'] ?? null,
                'hire_date' => !empty($centralUser['hire_date']) ? \Carbon\Carbon::parse($centralUser['hire_date'])->format('Y-m-d') : null,
                'termination_date' => !empty($centralUser['termination_date']) ? \Carbon\Carbon::parse($centralUser['termination_date'])->format('Y-m-d') : null,
                'unvan' => $centralUser['job_title'] ?? null,
                'is_mavi_yaka' => $centralUser['is_mavi_yaka'] ?? false,
            ]);
        } else {
            // Kullanıcıyı "Onaysız" ve "Personel" olarak kaydet
            $user = User::create([
                'name' => trim($centralUser['first_name'] . ' ' . $centralUser['last_name']),
                'email' => $centralUser['email'],
                'password' => bcrypt(Str::random(16)), 
                'bolum_id' => $request->bolum_id,
                'onaylandi_mi' => false, // 🔥 DİKKAT: Yöneticinin onaylaması için false yapıyoruz!
                'is_personnel' => true,
                'is_mavi_yaka' => $centralUser['is_mavi_yaka'] ?? false,
                'email_verified_at' => now(), 
                'tc_kimlik_no' => $centralUser['tc_no'] ?? null,
                'sicil_no' => $centralUser['registration_no'] ?? null,
                'hire_date' => !empty($centralUser['hire_date']) ? \Carbon\Carbon::parse($centralUser['hire_date'])->format('Y-m-d') : null,
                'termination_date' => !empty($centralUser['termination_date']) ? \Carbon\Carbon::parse($centralUser['termination_date'])->format('Y-m-d') : null,
                'unvan' => $centralUser['job_title'] ?? null,
            ]);
        }

        // Webhook ile Merkezi API'yi güncelle
        $apiKey = env('CENTRAL_SSO_API_KEY', 'merkezi_api_key_buraya');
        $centralUrl = env('CENTRAL_SSO_URL') ? rtrim(env('CENTRAL_SSO_URL'), '/') : 'http://localhost:8001';

        try {
            Http::timeout(5)->withHeaders([
                'X-App-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->post($centralUrl . '/api/internal/uygulama-basvuru-durum', [
                'email' => $user->email,
                'status' => 'pending'
            ]);
        } catch (\Exception $e) {
            \Log::warning('Merkez API webhook hatası: ' . $e->getMessage());
        }

        // Superadmin'lere ve Bölüm Liderine bildirim gönder
        try {
            $superadmins = User::role('Superadmin')->get();
            $bolumLiderleri = User::role('Bölüm Lideri')->where('bolum_id', $request->bolum_id)->get();

            $allNotifiables = $superadmins->merge($bolumLiderleri)->unique('id');

            foreach ($allNotifiables as $notifiable) {
                if ($notifiable->hasRole('Bölüm Lideri') && $notifiable->bolum_id == $request->bolum_id) {
                    $title = 'Bölümünüze Yeni Başvuru';
                    $msg = $user->name . ' kullanıcısı bölümünüze kayıt olmak istiyor, lütfen onaylayın.';
                } else {
                    $bolumAdi = \App\Models\Bolum::find($request->bolum_id)->ad ?? 'Belirtilmedi';
                    $title = 'Sisteme Yeni Başvuru Yapıldı';
                    $msg = $user->name . ' isimli kullanıcı ' . $bolumAdi . ' bölümüne başvuru yaptı. Bölüm liderinin onayı bekleniyor.';
                }
                $notifiable->notify(new NewSsoApplicationNotification($user, $title, $msg));
            }
        } catch (\Exception $e) {
            \Log::error('SSO başvuru bildirimi gönderilirken hata oluştu: ' . $e->getMessage());
        }

        session()->forget('sso_user_data');
        session(['pending_user_email' => $user->email]);

        return redirect()->route('sso.onay_bekliyor');
    }

    /**
     * Onay Bekleme Ekranını Göster
     */
    public function onayBekliyor()
    {
        $pendingEmail = session('pending_user_email');
        $user = null;
        $liderler = [];
        $bolumler = \App\Models\Bolum::where('is_active', true)->orderBy('ad')->get();

        if ($pendingEmail) {
            $user = User::where('email', $pendingEmail)->with('bolum')->first();
            if ($user && $user->bolum_id) {
                // Sadece isimlerini değil, tüm nesneyi (avatar, email, phone) alalım
                $liderler = User::role('Bölüm Lideri')->where('bolum_id', $user->bolum_id)->get();
            }
        }

        return view('auth.onay_bekliyor', compact('user', 'liderler', 'bolumler'));
    }

    /**
     * Başvuru Bölümünü Güncelle
     */
    public function basvuruGuncelle(Request $request)
    {
        $pendingEmail = session('pending_user_email');
        if (!$pendingEmail) {
            return redirect()->route('sso.login')->with('error', 'Oturum süreniz dolmuş, lütfen tekrar giriş yapın.');
        }

        $user = User::where('email', $pendingEmail)->firstOrFail();

        $request->validate([
            'bolum_id' => 'required|exists:bolumler,id',
        ]);

        $eskiBolumId = $user->bolum_id;

        // Kullanıcı reddedilmişse, aynı departmanı seçse bile başvuruyu yenilemesine izin ver
        $isReapplying = !is_null($user->rejected_at);

        // Hem reddedilmemiş hem de aynı bölümü seçmişse işlem yapma
        if (!$isReapplying && $eskiBolumId == $request->bolum_id) {
            return redirect()->route('sso.onay_bekliyor')->with('success', 'Aynı departmanı seçtiğiniz için değişiklik yapılmadı.');
        }

        $user->update([
            'bolum_id' => $request->bolum_id,
            'rejected_at' => null,
            'rejection_reason' => null,
            'rejected_by' => null,
            'onaylandi_mi' => false
        ]);

        // Değişikliği Logla
        \App\Models\SsoDepartmentChangeLog::create([
            'user_id' => $user->id,
            'old_bolum_id' => $eskiBolumId,
            'new_bolum_id' => $request->bolum_id,
        ]);

        try {
            $superadmins = User::role('Superadmin')->get();
            $eskiLiderler = $eskiBolumId ? User::role('Bölüm Lideri')->where('bolum_id', $eskiBolumId)->get() : collect();
            $yeniLiderler = User::role('Bölüm Lideri')->where('bolum_id', $request->bolum_id)->get();

            // Eğer departman değiştiyse eski liderlere geri çekti bildirimi at
            if ($eskiBolumId != $request->bolum_id) {
                foreach ($eskiLiderler as $eskiLider) {
                    // Eski bildirimi sil
                    \Illuminate\Support\Facades\DB::table('notifications')
                        ->where('notifiable_id', $eskiLider->id)
                        ->where('notifiable_type', User::class)
                        ->where('type', \App\Notifications\NewSsoApplicationNotification::class)
                        ->where('data', 'like', '%"user_id":' . $user->id . '%')
                        ->delete();

                    // Geri çekti bildirimi yolla
                    $eskiLider->notify(new NewSsoApplicationNotification(
                        $user, 
                        'Başvuru Geri Çekildi', 
                        "{$user->name} kullanıcısı bölümünüze yaptığı kayıt başvurusunu geri çekerek başka bir departmana yönelmiştir."
                    ));
                }
            }

            // Yeni Liderlere ve Superadminlere bildirim at
            $allNewNotifiables = $superadmins->merge($yeniLiderler)->unique('id');

            foreach ($allNewNotifiables as $notifiable) {
                if ($notifiable->hasRole('Bölüm Lideri') && $notifiable->bolum_id == $request->bolum_id) {
                    $title = 'Bölümünüze Yeni Başvuru';
                    $msg = $eskiBolumId == $request->bolum_id 
                        ? $user->name . ' kullanıcısı reddedilen başvurusunu yenileyerek tekrar onayınızı beklemektedir.'
                        : $user->name . ' kullanıcısı başvuru departmanını güncelleyerek bölümünüze kayıt olmak istiyor.';
                } else {
                    $bolumAdi = \App\Models\Bolum::find($request->bolum_id)->ad ?? 'Belirtilmedi';
                    $title = 'Sisteme Başvuru Güncellendi';
                    $msg = $eskiBolumId == $request->bolum_id 
                        ? $user->name . ' isimli kullanıcı reddedilen başvurusunu yeniledi.'
                        : $user->name . ' isimli kullanıcı başvuru departmanını ' . $bolumAdi . ' olarak güncelledi.';
                }
                $notifiable->notify(new NewSsoApplicationNotification($user, $title, $msg));
            }

        } catch (\Exception $e) {
            \Log::error('SSO başvuru güncelleme bildirimi hatası: ' . $e->getMessage());
        }

        return redirect()->route('sso.onay_bekliyor')->with('success', 'Başvuru departmanınız başarıyla güncellendi.');
    }

    /**
     * Lidere/Superadmine Hatırlatma Gönder
     */
    public function hatirlat(Request $request)
    {
        $pendingEmail = session('pending_user_email');
        if (!$pendingEmail) {
            return redirect()->route('sso.login');
        }

        $user = User::where('email', $pendingEmail)->firstOrFail();

        // 24 saat kuralı (Cache ile kontrol edelim)
        $cacheKey = 'sso_reminder_' . $user->id;
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return redirect()->route('sso.onay_bekliyor')->with('error', 'Yakın zamanda bir hatırlatma gönderdiniz. Yeni bir hatırlatma için lütfen daha sonra tekrar deneyin (24 Saatte bir gönderilebilir).');
        }

        try {
            $superadmins = User::role('Superadmin')->get();
            $bolumLiderleri = $user->bolum_id ? User::role('Bölüm Lideri')->where('bolum_id', $user->bolum_id)->get() : collect();
            $allNotifiables = $superadmins->merge($bolumLiderleri)->unique('id');

            foreach ($allNotifiables as $notifiable) {
                $title = 'Kayıt Onay Hatırlatması';
                $msg = "Onayınızı bekleyen {$user->name} kullanıcısı sistem erişimi için onayınızın durumunu sormaktadır.";
                $notifiable->notify(new NewSsoApplicationNotification($user, $title, $msg));
            }

            // Cache'e 24 saatliğine yaz
            \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHours(24));

        } catch (\Exception $e) {
            return redirect()->route('sso.onay_bekliyor')->with('error', 'Hatırlatma gönderilirken bir hata oluştu.');
        }

        return redirect()->route('sso.onay_bekliyor')->with('success', 'Hatırlatma bildiriminiz ilgili yöneticilere başarıyla iletildi.');
    }
}
