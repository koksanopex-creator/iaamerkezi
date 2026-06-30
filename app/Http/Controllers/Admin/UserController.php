<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bolum;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use App\Notifications\NewUserAddedNotification;
use Carbon\Carbon;
use App\Exports\Admin\UserExport;
use App\Imports\Admin\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\BolumKategorisi;
use App\Models\Nationality;

class UserController extends Controller
{

    /**
     * Onay bekleyen ve aktif kullanıcıları listeler. (FİLTRELENEBİLİR VE SIRALI)
     */
    public function index(Request $request)
    {
        // --- Filtre seçenekleri için verileri al ---
        $bolumler = Bolum::orderBy('ad')->get();
        $roller = Role::orderBy('name')->get();
        $musteriler = Customer::orderBy('name')->get(); // <-- EKLENDİ
        // --- Veri alımı sonu ---

        // Onay Bekleyenler Sorgusu
        $onayBekleyenlerQuery = User::where('onaylandi_mi', false)
            ->with('bolum', 'roles')
            ->orderBy('name', 'asc');

        // Sistem Kullanıcıları Sorgusu (Ofis / Beyaz Yaka)
        $sistemKullanicilariQuery = User::where('onaylandi_mi', true)
            ->where('is_personnel', true)
            ->where('is_mavi_yaka', false) // Mavi yaka olmayanlar
            ->with('bolum', 'roles')
            ->orderBy('name', 'asc');

        // Mavi Yaka Kullanıcıları Sorgusu
        $maviYakaKullanicilariQuery = User::where('onaylandi_mi', true)
            ->where('is_mavi_yaka', true)
            ->with('bolum', 'roles')
            ->orderBy('name', 'asc');

        // Müşteri Kullanıcıları Sorgusu (Yetkililer)
        $musteriKullanicilariQuery = User::where('onaylandi_mi', true) // <-- DÜZELTME: Sadece onaylıları al
            ->where('is_personnel', false)
            ->with('bolum', 'roles', 'customer') // customer ilişkisini yükle
            ->orderBy('name', 'asc');

        // İşten Çıkarılanlar Sorgusu (Pasif Kullanıcılar)
        $resignedUsersQuery = User::onlyTrashed()
            ->with('bolum', 'roles')
            ->orderBy('deleted_at', 'desc');

        // --- FİLTRELEME MANTIĞI ---
        $filters = $request->only(['name_filter', 'bolum_filter', 'role_filter', 'customer_filter', 'title_filter']); // <-- title_filter eklendi

        // İsim veya E-posta filtresi
        if ($request->filled('name_filter'))
        {
            $val = '%' . $request->input('name_filter') . '%';
            $sistemKullanicilariQuery->where(function($q) use ($val) {
                $q->where('name', 'like', $val)->orWhere('email', 'like', $val);
            });
            $maviYakaKullanicilariQuery->where(function($q) use ($val) {
                $q->where('name', 'like', $val)->orWhere('email', 'like', $val);
            });
            $musteriKullanicilariQuery->where(function($q) use ($val) {
                $q->where('name', 'like', $val)->orWhere('email', 'like', $val);
            });
            $onayBekleyenlerQuery->where(function($q) use ($val) {
                $q->where('name', 'like', $val)->orWhere('email', 'like', $val);
            });
            $resignedUsersQuery->where(function($q) use ($val) {
                $q->where('name', 'like', $val)->orWhere('email', 'like', $val);
            });
        }

        // Bölüm filtresi
        if ($request->filled('bolum_filter'))
        {
            $val = $request->input('bolum_filter');
            $sistemKullanicilariQuery->where('bolum_id', $val);
            $maviYakaKullanicilariQuery->where('bolum_id', $val); // Mavi yaka eklendi
            $onayBekleyenlerQuery->where('bolum_id', $val);
            $resignedUsersQuery->where('bolum_id', $val);
        }

        // Müşteri (Firma) filtresi
        if ($request->filled('customer_filter'))
        {
            $val = $request->input('customer_filter');
            $musteriKullanicilariQuery->where('customer_id', $val);
        }

        // Ünvan filtresi (Sadece Müşteri Kullanıcısı)
        if ($request->filled('title_filter'))
        {
            $val = '%' . $request->input('title_filter') . '%';
            $musteriKullanicilariQuery->where('unvan', 'like', $val);
        }

        // Rol filtresi
        if ($request->filled('role_filter'))
        {
            $val = $request->input('role_filter');
            $sistemKullanicilariQuery->whereHas('roles', function ($q) use ($val) {
                $q->where('name', $val);
            });
            $maviYakaKullanicilariQuery->whereHas('roles', function ($q) use ($val) { // Mavi yaka eklendi
                $q->where('name', $val);
            });
            $onayBekleyenlerQuery->whereHas('roles', function ($q) use ($val) {
                $q->where('name', $val);
            });
            $resignedUsersQuery->whereHas('roles', function ($q) use ($val) {
                $q->where('name', $val);
            });
        }
        // --- FİLTRELEME MANTIĞI SONU ---

        $onayBekleyenler = $onayBekleyenlerQuery->paginate(10, ['*'], 'onay_page')->withQueryString();
        $sistemKullanicilari = $sistemKullanicilariQuery->paginate(15, ['*'], 'sistem_page')->withQueryString();
        $maviYakaKullanicilari = $maviYakaKullanicilariQuery->paginate(15, ['*'], 'mavi_page')->withQueryString();
        $musteriKullanicilari = $musteriKullanicilariQuery->paginate(15, ['*'], 'musteri_page')->withQueryString();
        $resignedUsers = $resignedUsersQuery->paginate(15, ['*'], 'resigned_page')->withQueryString();

        return view('admin.users.index', compact(
            'onayBekleyenler',
            'sistemKullanicilari',
            'maviYakaKullanicilari',
            'musteriKullanicilari',
            'resignedUsers',
            'bolumler',
            'musteriler', // <-- View'a gönder
            'roller',
            'filters'
        ));
    }

    /**
     * Belirtilen kullanıcıyı onaylar (aktif hale getirir).
     * (Bu metot sizde vardı, korundu)
     */
    public function onayla(User $user)
    {
        $user->onaylandi_mi = true;
        $user->save();

        return back()->with('success', $user->name . ' adlı kullanıcının hesabı başarıyla aktive edildi.');
    }

    /**
     * Kullanıcının e-posta adresini manuel olarak doğrular (Admin yetkisiyle).
     */
    public function verifyEmail(User $user)
    {
        if ($user->hasVerifiedEmail())
        {
            return back()->with('info', 'Bu kullanıcının e-postası zaten doğrulanmış.');
        }

        $user->markEmailAsVerified();

        return back()->with('success', $user->name . ' kullanıcısının e-postası başarıyla doğrulandı.');
    }

    /**
     * Show the form for creating a new resource.
     * (Rolleri ve Bölümleri dinamik olarak alır)
     */
    public function create()
    {
        // === GÜNCELLENDİ: pluck ile isim/id alınıyor ===
        $bolumler = Bolum::orderBy('ad')->pluck('ad', 'id');
        // === DEĞİŞTİ: Rol nesnelerini al ===
        $roles = Role::orderBy('name')->get(); // pluck('name', 'name')->all();
        // ================================

        // === HATA AYIKLAMA KODU KALDIRILDI ===
        // dd($roles);
        // ===================================

        return view('admin.users.create', compact('bolumler', 'roles'));
    }

    /**
     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'bolum_id' => ['nullable', 'exists:bolumler,id'],
            'roles' => ['nullable', 'array'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'hire_date' => ['nullable', 'date'],
            'termination_date' => ['nullable', 'date'],
            'dogum_tarihi' => ['nullable', 'date'],
            'tc_kimlik_no' => ['nullable', 'string', 'max:11'],
            'sicil_no' => ['nullable', 'string', 'max:50'],
            'unvan' => ['nullable', 'string', 'max:255'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bolum_id' => $request->bolum_id,
            'onaylandi_mi' => true,
            'email_verified_at' => now(), // Admin oluşturduğu için doğrulandı say.
            'hire_date' => $request->hire_date,
            'termination_date' => $request->termination_date,
            'dogum_tarihi' => $request->dogum_tarihi,
            'tc_kimlik_no' => $request->tc_kimlik_no,
            'sicil_no' => $request->sicil_no,
            'unvan' => $request->unvan,
        ];

        if ($request->hasFile('photo'))
        {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($request->name) . '_' . now()->format('Ymd_His') . '.' . $extension;
            $path = $file->storeAs('profile-photos', $filename, 'public');
            $userData['profile_photo_path'] = $path;
        }

        $user = User::create($userData);

        if ($request->filled('roles'))
        {
            $user->syncRoles($request->roles);
        }

        // ============================================================
        // === MERKEZİ SSO SENKRONİZASYONU (HIZLI EKLEME) ===
        // ============================================================
        try {
            app(\App\Services\CentralSsoSyncService::class)->syncUser($user, $request->password, 'personnel');
        } catch (\Exception $ssoEx) {
            \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for new admin-created user: ' . $ssoEx->getMessage());
        }
        // ============================================================

        // ============================================================
        // === YENİ KULLANICI BİLDİRİMLERİ (anlık / senkron) ===
        // ============================================================
        try
        {
            // 1. Yeni kullanıcıya hoşgeldin maili
            if ($user->customer_id || $user->hasRole(['Müşteri', 'Müşteri Temsilcisi'])) {
                // Müşteri odaklı şablon (Sistem Ayarları'ndan çeker)
                Mail::to($user)->queue(new \App\Mail\NewCustomerUserCreated($user, $request->password));
            } else {
                // Standart personel şablonu
                Mail::to($user)->queue(new WelcomeUserMail($user, $request->password));
            }

            // 2. Yöneticilere bildirim (Bölüm Lideri ve Direktör)
            if ($user->bolum_id)
            {
                $bolum = Bolum::with('director')->find($user->bolum_id);
                if ($bolum)
                {

                    // Lider mail ayarını kontrol et
                    $liderMailAktif = \App\Models\Setting::where('key', 'new_user_notify_bolum_lideri')->value('value');
                    // Direktör mail ayarını kontrol et
                    $direktorMailAktif = \App\Models\Setting::where('key', 'new_user_notify_direktor')->value('value');

                    // A) Bölüm Liderlerini bul ve bildir
                    $bolumLiderleri = User::role('Bölüm Lideri')->where('bolum_id', $bolum->id)->get();
                    foreach ($bolumLiderleri as $lider)
                    {
                        $lider->notify(
                            new NewUserAddedNotification(
                                $user,
                                "Bölümünüze **{$user->name}** isimli kullanıcı sistem tarafından eklenmiştir.",
                                'Yeni Kullanıcı Eklendi',
                                $liderMailAktif !== '0' // null veya '1' ise mail gönder
                            )
                        );
                    }

                    // B) Direktörü bul ve bildir
                    if ($bolum->director)
                    {
                        $bolum->director->notify(
                            new NewUserAddedNotification(
                                $user,
                                "Direktörlüğünüze bağlı **{$bolum->ad}** bölümüne **{$user->name}** isimli kullanıcı eklenmiştir.",
                                'Yeni Kullanıcı Eklendi',
                                $direktorMailAktif !== '0' // null veya '1' ise mail gönder
                            )
                        );
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            \Illuminate\Support\Facades\Log::error('Yeni kullanıcı bildirim hatası: ' . $e->getMessage());
        }
        // ============================================================

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu, aktif edildi ve bilgilendirme mailleri gönderildi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles', 'bolum');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $bolumler = Bolum::orderBy('ad')->pluck('ad', 'id');
        $roles = Role::orderBy('name')->get();
        $userRoles = $user->getRoleNames()->toArray();
        $musteriler = Customer::orderBy('name')->get(); // <--- EKLENDİ

        return view('admin.users.edit', compact('user', 'bolumler', 'roles', 'userRoles', 'musteriler'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            /* MERKEZİ SİSTEM GEÇİŞİ: Ad, E-posta ve Şifre Merkezi API'den geldiği için yerel form doğrulaması kapatıldı.
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            */
            'bolum_id' => ['nullable', 'exists:bolumler,id'],
            'customer_id' => ['nullable', 'exists:customers,id'], // <--- EKLENDİ
            'unvan' => ['nullable', 'string', 'max:255'], // <--- EKLENDİ
            'telefon' => ['nullable', 'string', 'max:255'], // <--- EKLENDİ
            'roles' => ['nullable', 'array'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'hire_date' => ['nullable', 'date'],
            'termination_date' => ['nullable', 'date'],
            'dogum_tarihi' => ['nullable', 'date'],
        ]);

        $updateData = [
            /* MERKEZİ SİSTEM GEÇİŞİ: Ad ve E-posta Merkezi API'den geldiği için yerel veritabanı güncellemesi kapatıldı.
            'name' => $request->name,
            'email' => $request->email,
            */
            'bolum_id' => $request->bolum_id,
            'customer_id' => $request->customer_id, // <--- EKLENDİ
            'unvan' => $request->unvan, // <--- EKLENDİ
            'telefon' => $request->telefon, // <--- EKLENDİ
            'hire_date' => $request->hire_date,
            'termination_date' => $request->termination_date,
            'dogum_tarihi' => $request->dogum_tarihi,
        ];

        /* MERKEZİ SİSTEM GEÇİŞİ: Şifreler Merkezi API'den yönetildiği için yerel şifre güncellemesi kapatıldı.
        if ($request->filled('password'))
        {
            $updateData['password'] = Hash::make($request->password);
        }
        */

        if ($request->hasFile('photo'))
        {
            if ($user->profile_photo_path)
            {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();

            // Rules1.md uyumlu dinamik yol belirleme
            $folderType = $user->is_personnel ? ($user->is_mavi_yaka ? 'mavi_yaka' : 'sistem_kullanicisi') : 'musteri_yetkilisi';
            $folderName = Str::slug($request->name);
            $fileName = now()->format('d.m.Y_H.i') . '_' . Str::random(2) . '.' . $extension;

            // path: storage/profile_photos/{tip}/{isim}/{id}/{dosya}
            $path = $file->storeAs("profile_photos/{$folderType}/{$folderName}/{$user->id}", $fileName, 'public');
            $updateData['profile_photo_path'] = $path;
        }

        $user->update($updateData);

        // === OTOMATİK PASİFLEŞTİRME: termination_date dolu ise soft-delete yap ===
        if ($request->filled('termination_date') && !$user->trashed())
        {
            $user->delete(); // Soft delete → İşten Çıkanlar sekmesine taşınır
        }
        // === OTOMATİK AKTİFLEŞTİRME: termination_date temizlendiyse restore yap ===
        elseif (!$request->filled('termination_date') && $user->trashed())
        {
            $user->restore(); // Aktif listeye geri döner
        }

        if ($request->filled('roles'))
        {
            // Rol değişiklik bildirimi için eski rolleri al
            $eskiRoller = $user->getRoleNames()->toArray();
            $yeniRoller = $request->roles;

            $user->syncRoles($yeniRoller);

            // Eklenen ve kaldırılan rolleri tespit et
            $eklenenRoller = array_diff($yeniRoller, $eskiRoller);
            $kaldirilanRoller = array_diff($eskiRoller, $yeniRoller);
            $atayanAdi = auth()->user()->name;

            try
            {
                foreach ($eklenenRoller as $rol)
                {
                    $user->notify(new \App\Notifications\RolAtandiNotification($rol, true, $atayanAdi));
                }
                foreach ($kaldirilanRoller as $rol)
                {
                    $user->notify(new \App\Notifications\RolAtandiNotification($rol, false, $atayanAdi));
                }
            }
            catch (\Exception $e)
            {
                \Log::error('Rol atama bildirimi hatası: ' . $e->getMessage());
            }
        }
        else
        {
            $eskiRoller = $user->getRoleNames()->toArray();
            $user->syncRoles([]);
            $atayanAdi = auth()->user()->name;
            try
            {
                foreach ($eskiRoller as $rol)
                {
                    $user->notify(new \App\Notifications\RolAtandiNotification($rol, false, $atayanAdi));
                }
            }
            catch (\Exception $e)
            {
                \Log::error('Rol kaldırma bildirimi hatası: ' . $e->getMessage());
            }
        }

        // ============================================================
        // === MERKEZİ SSO SENKRONİZASYONU (GÜNCELLEME İÇİN) ===
        // ============================================================
        try {
            $profession = $user->is_personnel ? ($user->is_mavi_yaka ? 'mavi_yaka' : 'personnel') : 'customer';
            app(\App\Services\CentralSsoSyncService::class)->syncUser($user, null, $profession);
        } catch (\Exception $ssoEx) {
            \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for user update: ' . $ssoEx->getMessage());
        }
        // ============================================================

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla güncellendi!');
    }

    /**
     * Bir kullanıcıyı siler. Onaylanmamışsa kalıcı, onaylanmışsa ilişkileri kontrol edilerek geçici olarak siler.
     * (Sizin özel kontrolleriniz korundu)
     */
    public function destroy(User $user)
    {
        // MEVCUT KORUMALARINIZ (Bunlar harika, olduğu gibi kalıyor)
        if ($user->hasRole('Superadmin') && User::role('Superadmin')->count() === 1)
        {
            return redirect()->route('admin.users.index')->with('error', 'Sistemdeki son Superadmin silinemez.');
        }
        if ($user->id === auth()->id())
        {
            return redirect()->route('admin.users.index')->with('error', 'Kendinizi silemezsiniz.');
        }

        // Onaylanmamış kullanıcıyı kalıcı olarak sil
        if (!$user->onaylandi_mi)
        {
            $user->forceDelete(); // Kalıcı silme
            return redirect()->route('admin.users.index')->with('success', 'Onay bekleyen kullanıcı kaydı kalıcı olarak silindi.');
        }

        // ======================== SİZİN ÖZEL KONTROLLERİNİZ (Korundu) ========================
        if ($user->lideriOlduguTakimlar()->exists())
        {
            return back()->with('error', 'Bu kullanıcı bir takımın lideri olduğu için silinemez. Lütfen önce takım liderliğini başka bir kullanıcıya devredin.');
        }
        if ($user->iaas()->exists())
        {
            return back()->with('error', 'Bu kullanıcının göndermiş olduğu İAA önerileri bulunduğu için silinemez. Önce önerileri başka bir kullanıcıya atayın veya silin.');
        }
        if ($user->takimlar()->exists())
        {
            return back()->with('error', 'Bu kullanıcı bir takıma üye olduğu için silinemez. Lütfen önce kullanıcıyı takımlarından çıkarın.');
        }
        // ===============================================================================

        // Eğer yukarıdaki tüm kontrollerden geçtiyse, onaylanmış kullanıcıyı geçici olarak sil (soft delete)
        $user->delete(); // Geçici silme (Modelinizde SoftDeletes trait'i varsa çalışır)
        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla silindi.');
    }

    /**
     * Kullanıcıyı işten çıkar (Pasif yap).
     */
    public function resign(User $user)
    {
        // Yetki: Superadmin

        $user->update([
            'termination_date' => now(),
        ]);

        $user->delete(); // Soft delete

        // Merkezi API'yi bilgilendir (Erişimi Kapat)
        $apiKey = env('CENTRAL_SSO_API_KEY', 'merkezi_api_key_buraya'); 
        $centralUrl = env('CENTRAL_SSO_URL') ? rtrim(env('CENTRAL_SSO_URL'), '/') : 'http://localhost:8001';

        try {
            \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'X-App-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->post($centralUrl . '/api/internal/uygulama-basvuru-durum', [
                'email' => $user->email,
                'status' => 'rejected' 
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Merkez API webhook hatası (Resign): ' . $e->getMessage());
        }

        return back()->with('success', $user->name . ' adlı personel işten çıkarıldı, pasif duruma getirildi ve Merkezi API erişimi kapatıldı.');
    }

    /**
     * İşten çıkarılan kullanıcıyı geri al (Aktif yap).
     */
    public function restore(int $id)
    {
        // Yetki: Superadmin

        $user = User::withTrashed()->findOrFail($id);

        $user->restore();

        $user->update([
            'termination_date' => null,
        ]);

        // Merkezi API'yi bilgilendir (Erişimi Aç)
        $apiKey = env('CENTRAL_SSO_API_KEY', 'merkezi_api_key_buraya'); 
        $centralUrl = env('CENTRAL_SSO_URL') ? rtrim(env('CENTRAL_SSO_URL'), '/') : 'http://localhost:8001';

        try {
            \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'X-App-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->post($centralUrl . '/api/internal/uygulama-basvuru-durum', [
                'email' => $user->email,
                'status' => 'approved' 
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Merkez API webhook hatası (Restore): ' . $e->getMessage());
        }

        return back()->with('success', $user->name . ' adlı personel tekrar aktif hale getirildi ve Merkezi API erişimi açıldı.');
    }

    // ==========================================================
    // --- SSO ONAY VE YETKİLENDİRME MEKANİZMASI ---
    // ==========================================================

    /**
     * ONAY BEKLEYENLER EKRANI
     */
    public function onayBekleyenler()
    {
        $user = auth()->user();
        
        $query = \App\Models\User::with('bolum')
            ->where('onaylandi_mi', false)
            ->whereNull('rejected_at');

        $reddedilenQuery = \App\Models\User::with('bolum')
            ->where('onaylandi_mi', false)
            ->whereNotNull('rejected_at');

        // Sadece Bölüm Lideri ise ve Superadmin değilse (Sadece kendi bölümünün başvurularını görsün)
        $onaylananlarQuery = \App\Models\User::with('bolum')->where('onaylandi_mi', true);
        
        if (!$user->hasRole('Superadmin') && $user->hasRole('Bölüm Lideri') && $user->bolum_id) {
            $query->where('bolum_id', $user->bolum_id);
            $reddedilenQuery->where('bolum_id', $user->bolum_id);
            $onaylananlarQuery->where('bolum_id', $user->bolum_id);
        }

        $bekleyenler = $query->latest()->get();
        $reddedilenler = $reddedilenQuery->latest()->get();
        $onaylananlar = $onaylananlarQuery->latest()->get();

        $merkezBekleyenler = collect();

        // Eğer Superadmin ise Merkezi API'den "İAA yetkisi olan ama giriş yapmamış" kullanıcıları çek
        if ($user->hasRole('Superadmin')) {
            $apiKey = env('CENTRAL_SSO_API_KEY', 'merkezi_api_key_buraya');
            $centralUrl = env('CENTRAL_SSO_URL') ? rtrim(env('CENTRAL_SSO_URL'), '/') : 'http://localhost:8001';

            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                    'X-App-Key' => $apiKey,
                    'Accept' => 'application/json'
                ])->get($centralUrl . '/api/internal/uygulama-bekleyen-kullanicilar');

                if ($response->successful()) {
                    $centralUsers = collect($response->json('users'));
                    // İAA veritabanında zaten olanları (e-postası eşleşenleri) filtrele
                    $existingEmails = \App\Models\User::pluck('email')->toArray();
                    
                    $merkezBekleyenler = $centralUsers->reject(function ($cUser) use ($existingEmails) {
                        return in_array($cUser['email'], $existingEmails);
                    });
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Merkezden bekleyen kullanıcıları çekerken hata: ' . $e->getMessage());
            }
        }

        $bolumler = \App\Models\Bolum::where('is_active', true)->orderBy('ad')->get();
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'Superadmin')->orderBy('name')->get();
        $customers = \App\Models\Customer::orderBy('name')->get();

        $departmentLogs = collect();
        $actionLogs = collect();
        if ($user->hasRole('Superadmin')) {
            $departmentLogs = \App\Models\SsoDepartmentChangeLog::with(['user', 'oldBolum', 'newBolum'])->latest()->get();
            $actionLogs = \App\Models\SsoActionLog::with(['user', 'actionBy'])->latest()->get();
        }

        return view('admin.users.bekleyen_basvurular', compact('bekleyenler', 'reddedilenler', 'onaylananlar', 'merkezBekleyenler', 'bolumler', 'roles', 'customers', 'departmentLogs', 'actionLogs'));
    }

    /**
     * ONAYI GERİ AL (Bekleyen duruma düşür)
     */
    public function basvuruGeriAl(\Illuminate\Http\Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        // Zaman sınırı kontrolü: 3 günden eskiyse geri alınamaz
        if ($user->updated_at && $user->updated_at->diffInDays(now()) > 3) {
            return redirect()->back()->with('error', 'Bu başvurunun onayı üzerinden 3 günden fazla süre geçtiği için geri alınamaz.');
        }

        $user->onaylandi_mi = false;
        $user->rejected_at = null; // Reddedilmişse onu da sıfırla
        $user->save();

        // Aksiyon Logu Oluştur
        \App\Models\SsoActionLog::create([
            'user_id' => $user->id,
            'action' => 'revoked',
            'action_by' => auth()->id(),
            'details' => 'Kullanıcının onayı geri alındı ve bekleyenler listesine taşındı.'
        ]);

        // Kullanıcının mevcut oturumlarını kapat (Sistemden at)
        \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();

        // Merkezi API'yi bilgilendir (Tekrar Bekleyen / Pending Durumuna Al)
        $apiKey = env('CENTRAL_SSO_API_KEY', 'merkezi_api_key_buraya'); 
        $centralUrl = env('CENTRAL_SSO_URL') ? rtrim(env('CENTRAL_SSO_URL'), '/') : 'http://localhost:8001';

        try {
            \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'X-App-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->post($centralUrl . '/api/internal/uygulama-basvuru-durum', [
                'email' => $user->email,
                'status' => 'pending' 
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Merkez API geri alma webhook hatası: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', $user->name . ' kullanıcısının onayı geri alındı. Kullanıcı tekrar "Onay Bekleyenler" listesine taşındı.');
    }

    /**
     * BAŞVURUYU ONAYLA VE MERKEZE BİLDİR
     */
    public function basvuruOnayla(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'string',
            'bolum_id' => 'nullable|exists:bolumler,id',
            'account_type' => 'required|in:personel,mavi_yaka,musteri',
            'customer_id' => 'nullable|exists:customers,id',
            'sicil_no' => 'nullable|string|max:50',
            'unvan' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date'
        ]);

        $user = \App\Models\User::findOrFail($id);
        
        // İAA Tarafında Kullanıcıyı Aktif Et
        $user->onaylandi_mi = true;
        $user->rejected_at = null; // Reddedilme durumunu temizle

        // Hesap Türüne Göre Ayarlamalar
        if ($request->account_type === 'musteri') {
            $user->is_personnel = false;
            $user->is_mavi_yaka = false;
            $user->customer_id = $request->customer_id;
            $user->bolum_id = null;
        } elseif ($request->account_type === 'mavi_yaka') {
            $user->is_personnel = true;
            $user->is_mavi_yaka = true;
            $user->customer_id = null;
            if ($request->filled('bolum_id')) {
                $user->bolum_id = $request->bolum_id;
            }
        } else {
            // Normal Personel
            $user->is_personnel = true;
            $user->is_mavi_yaka = false;
            $user->customer_id = null;
            if ($request->filled('bolum_id')) {
                $user->bolum_id = $request->bolum_id;
            }
        }
        
        if (in_array($request->account_type, ['personel', 'mavi_yaka'])) {
            $user->sicil_no = $request->sicil_no;
            $user->unvan = $request->unvan;
            if ($request->filled('hire_date')) {
                $user->hire_date = \Carbon\Carbon::parse($request->hire_date);
            }
        }

        $user->save();

        // Aksiyon Logu Oluştur
        \App\Models\SsoActionLog::create([
            'user_id' => $user->id,
            'action' => 'approved',
            'action_by' => auth()->id(),
            'details' => 'Başvuru onaylandı. Rol: ' . ($request->account_type ?? 'personel') . ', Bölüm: ' . ($user->bolum ? $user->bolum->ad : 'Belirtilmedi')
        ]);

        // Eğer onaylayan kişi Superadmin ise ve onaylanan kişi bir bölüme atandıysa,
        // Bölüm liderine "Superadmin tarafından onaylandı" diye bildirim at.
        if (auth()->user()->hasRole('Superadmin') && $user->bolum_id) {
            $bolumLiderleri = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $user->bolum_id)->get();
            foreach ($bolumLiderleri as $lider) {
                if ($lider->id !== auth()->id()) {
                    $lider->notify(new \App\Notifications\NewSsoApplicationNotification(
                        $user, 
                        'Başvuru Superadmin Tarafından Onaylandı', 
                        "Onayınızı bekleyen {$user->name} kullanıcısının başvurusu Superadmin tarafından onaylanmıştır."
                    ));
                }
            }
        }

        // Kullanıcıya girişinin onaylandığına dair bildirim (mail/zil) at
        $user->notify(new \App\Notifications\UserApplicationApprovedNotification());

        // Roller seçildiyse ata, seçilmediyse veya gizliyse varsayılan "Kullanıcı" rolünü ata
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        } else {
            // Eğer daha önceden bir rolü yoksa varsayılan "Kullanıcı" rolünü ata
            if ($user->roles->count() === 0) {
                $user->assignRole('Kullanıcı');
            }
        }

        // 🔥 MERKEZİ API'YI BİLGİLENDİR (Webhook)
        $apiKey = env('CENTRAL_SSO_API_KEY', 'merkezi_api_key_buraya'); 
        $centralUrl = env('CENTRAL_SSO_URL') ? rtrim(env('CENTRAL_SSO_URL'), '/') : 'http://localhost:8001';

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'X-App-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->post($centralUrl . '/api/internal/uygulama-basvuru-durum', [
                'email' => $user->email,
                'status' => 'approved' 
            ]);

            if ($response->failed()) {
                $hataDetayi = $response->json('message') ?? $response->body();
                return redirect()->back()->with('error', 'Kullanıcı İAA\'da onaylandı FAKAT Merkez API güncellenemedi! Sebep: ' . $hataDetayi);
            }

            // Sync User to Central SSO directly if it's personnel or mavi_yaka
            if (in_array($request->account_type, ['personel', 'mavi_yaka'])) {
                try {
                    $profession = $request->account_type === 'mavi_yaka' ? 'mavi_yaka' : 'personnel';
                    app(\App\Services\CentralSsoSyncService::class)->syncUser($user, null, $profession);
                } catch (\Exception $ssoEx) {
                    \Illuminate\Support\Facades\Log::error('Central SSO Sync failed in basvuruOnayla: ' . $ssoEx->getMessage());
                }
            }
        } catch (\Exception $e) {
            // Merkez API'ye bağlanılamazsa yine de onaylamış ol, sadece uyar
            \Illuminate\Support\Facades\Log::warning('Merkez API webhook hatası: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', $user->name . ' başarıyla onaylandı!');
    }

    /**
     * BAŞVURUYU REDDET
     */
    public function basvuruReddet(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $user = \App\Models\User::findOrFail($id);
        $userName = $user->name;
        $userEmail = $user->email;

        // Kullanıcı onaylı değilse reddet (veritabanından silmek yerine reddedildi olarak işaretle)
        if (!$user->onaylandi_mi) {
            $user->rejected_at = now();
            $user->rejection_reason = $request->rejection_reason;
            $user->rejected_by = auth()->id();
            $user->save();

            // Aksiyon Logu Oluştur
            \App\Models\SsoActionLog::create([
                'user_id' => $user->id,
                'action' => 'rejected',
                'action_by' => auth()->id(),
                'details' => 'Başvuru reddedildi. Sebep: ' . $request->rejection_reason
            ]);

            // Kullanıcıya red bildirimi ve e-posta gönder
            $user->notify(new \App\Notifications\UserApplicationRejectedNotification($request->rejection_reason));

            // Kullanıcının mevcut oturumlarını kapat (Sistemden at)
            \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();
        } else {
            return redirect()->back()->with('error', 'Onaylanmış kullanıcı bu sayfadan reddedilemez.');
        }

        // Merkezi API'yi bilgilendir (opsiyonel)
        $apiKey = env('CENTRAL_SSO_API_KEY', 'merkezi_api_key_buraya');
        $centralUrl = env('CENTRAL_SSO_URL') ? rtrim(env('CENTRAL_SSO_URL'), '/') : 'http://localhost:8001';

        try {
            \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'X-App-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->post($centralUrl . '/api/internal/uygulama-basvuru-durum', [
                'email' => $userEmail,
                'status' => 'rejected'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Merkez API reddet webhook hatası: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "{$userName} ({$userEmail}) adlı kullanıcının başvurusu reddedildi.");
    }

    /**
     * MERKEZDEN ÇEK VE ONAYLA (Superadmin)
     */
    public function merkezdenCek(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'tc_no' => 'nullable|string',
            'account_type' => 'required|in:personel,mavi_yaka',
            'bolum_id' => 'required|exists:bolumler,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string'
        ]);

        // Kullanıcıyı oluştur
        $user = \App\Models\User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                'email_verified_at' => now(),
                'tc_kimlik_no' => $request->tc_no
            ]
        );

        $user->onaylandi_mi = true;
        $user->rejected_at = null;
        $user->bolum_id = $request->bolum_id;

        if ($request->account_type === 'mavi_yaka') {
            $user->is_personnel = true;
            $user->is_mavi_yaka = true;
        } else {
            $user->is_personnel = true;
            $user->is_mavi_yaka = false;
        }

        $user->save();

        // Roller: Sadece Superadmin/Yönetim atayabilir
        if (auth()->user()->hasRole(['Superadmin', 'Yonetim'])) {
            if ($request->has('roles') && !empty($request->roles)) {
                $user->syncRoles($request->roles);
            } else {
                // Hiçbir rol seçilmediyse varsayılan "Kullanıcı" rolü
                $user->syncRoles(['Kullanıcı']);
            }
        } else {
            if ($user->roles->isEmpty()) {
                $user->assignRole('Kullanıcı');
            }
        }

        // İlgili bölüm liderine bildirim gönder
        $bolumLiderleri = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $user->bolum_id)->get();
        foreach ($bolumLiderleri as $lider) {
            $lider->notify(new \App\Notifications\NewSsoApplicationNotification(
                $user, 
                'Sisteme Yeni Kullanıcı Dahil Edildi', 
                "{$user->name} kullanıcısı Superadmin tarafından doğrudan merkeze çekilerek bölümünüze atanmıştır."
            ));
        }

        return redirect()->back()->with('success', "{$user->name} başarıyla merkeze çekildi ve bölümüne atandı.");
    }

    public function exportExcel()
    {
        return Excel::download(new UserExport, 'kullanicilar.xlsx');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('excel_file');
        
        try {
            $data = Excel::toArray(new UserImport, $file)[0] ?? [];
            if(empty($data)) {
                return redirect()->back()->with('error', 'Yüklenen dosya boş.');
            }
            
            // Generate a unique cache key to store the data for confirmation
            $cacheKey = 'user_import_' . auth()->id() . '_' . Str::random(10);
            cache()->put($cacheKey, $data, now()->addMinutes(30));

            return view('admin.users.import-preview', compact('data', 'cacheKey'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Excel okuma hatası: ' . $e->getMessage());
        }
    }

    public function importConfirm(Request $request)
    {
        $request->validate([
            'cache_key' => 'required|string',
        ]);

        $data = cache()->get($request->cache_key);
        if(!$data) {
            return redirect()->route('admin.users.index')->with('error', 'İçe aktarma oturumu zaman aşımına uğradı. Lütfen tekrar deneyin.');
        }

        $imported = 0;
        $errors = [];

        foreach($data as $index => $row) {
            // Row number for display
            $rowNum = $index + 2;

            try {
                // Determine names
                $adSoyad = $row['adi_soyadi'] ?? $row['ad_soyad'] ?? $row['name'] ?? null;
                $email = $row['email'] ?? null;
                
                if(!$adSoyad || !$email) {
                    $errors[] = "Satır $rowNum: Ad Soyad veya Email boş olamaz.";
                    continue;
                }

                // Check if user exists
                if(User::where('email', $email)->exists()) {
                    $errors[] = "Satır $rowNum: Bu email adresi zaten kayıtlı ($email).";
                    continue;
                }

                // Process Directorate and Department
                $direktorlukAd = $row['direktorluk'] ?? null;
                $bolumAd = $row['bolum'] ?? null;
                $bolumId = null;

                if ($bolumAd) {
                    $kategoriId = null;
                    if ($direktorlukAd) {
                        $kategori = BolumKategorisi::firstOrCreate(['ad' => $direktorlukAd]);
                        $kategoriId = $kategori->id;
                    }
                    $bolum = Bolum::firstOrCreate(['ad' => $bolumAd], ['bolum_kategori_id' => $kategoriId]);
                    $bolumId = $bolum->id;
                }

                // Process Nationality
                $uyrukAd = $row['uyruk'] ?? null;
                $uyrukId = null;
                if ($uyrukAd) {
                    $nationality = Nationality::firstOrCreate(['name' => $uyrukAd]);
                    $uyrukId = $nationality->id;
                }

                // Process password
                $rawPassword = $row['sifre'] ?? '12345678';

                // Setup UserType flags
                $userType = $row['kullanici_tipi'] ?? 'personnel';
                $isPersonnel = in_array($userType, ['personnel', 'blue_collar']) ? 1 : 0;
                $isMaviYaka = $userType === 'blue_collar' ? 1 : 0;

                $user = User::create([
                    'name' => $adSoyad,
                    'email' => $email,
                    'password' => Hash::make($rawPassword),
                    'telefon' => $row['telefon'] ?? null,
                    'tc_kimlik_no' => $row['tc_kimlik_no'] ?? null,
                    'sicil_no' => $row['sicil_no'] ?? null,
                    'unvan' => $row['unvan'] ?? null,
                    'bolum_id' => $bolumId,
                    'hire_date' => $row['ise_giris_tarihi'] ? Carbon::parse($row['ise_giris_tarihi'])->format('Y-m-d') : null,
                    'termination_date' => $row['isten_cikis_tarihi'] ? Carbon::parse($row['isten_cikis_tarihi'])->format('Y-m-d') : null,
                    'nationality_id' => $uyrukId,
                    'is_personnel' => $isPersonnel,
                    'is_mavi_yaka' => $isMaviYaka,
                    'onaylandi_mi' => 1,
                    'email_verified_at' => now(),
                    'created_by_id' => auth()->id()
                ]);

                // Assign Roles
                $rolesStr = $row['rol'] ?? null;
                if ($rolesStr) {
                    $roleNames = array_map('trim', explode(',', $rolesStr));
                    foreach ($roleNames as $rName) {
                        $role = Role::where('name', $rName)->first();
                        if ($role) {
                            $user->assignRole($role);
                        }
                    }
                }

                $imported++;

            } catch (\Exception $e) {
                $errors[] = "Satır $rowNum: Beklenmeyen bir hata oluştu - " . $e->getMessage();
            }
        }

        // Clear cache
        cache()->forget($request->cache_key);

        $msg = "$imported kullanıcı başarıyla aktarıldı.";
        if (count($errors) > 0) {
            return redirect()->route('admin.users.index')->with('warning', $msg . ' Ancak bazı hatalar oluştu: ' . implode('<br>', $errors));
        }

        return redirect()->route('admin.users.index')->with('success', $msg);
    }
}
