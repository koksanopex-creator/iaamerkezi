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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
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
            'name' => $request->name,
            'email' => $request->email,
            'bolum_id' => $request->bolum_id,
            'customer_id' => $request->customer_id, // <--- EKLENDİ
            'unvan' => $request->unvan, // <--- EKLENDİ
            'telefon' => $request->telefon, // <--- EKLENDİ
            'hire_date' => $request->hire_date,
            'termination_date' => $request->termination_date,
            'dogum_tarihi' => $request->dogum_tarihi,
        ];

        if ($request->filled('password'))
        {
            $updateData['password'] = Hash::make($request->password);
        }

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

        return back()->with('success', $user->name . ' adlı personel işten çıkarıldı ve pasif duruma getirildi.');
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

        return back()->with('success', $user->name . ' adlı personel tekrar aktif hale getirildi.');
    }
}
