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

class UserController extends Controller
{

    /**
     * Onay bekleyen ve aktif kullanıcıları listeler. (FİLTRELENEBİLİR VE SIRALI)
     */
    public function index(Request $request)
    {
        $kayitOnayiAktif = env('USER_REGISTRATION_APPROVAL', false);

        // --- Filtre seçenekleri için verileri al ---
        $bolumler = Bolum::orderBy('ad')->get();
        $roller = Role::orderBy('name')->get();
        $musteriler = Customer::orderBy('name')->get(); // <-- EKLENDİ
        // --- Veri alımı sonu ---

        // Onay Bekleyenler Sorgusu
        $onayBekleyenlerQuery = User::where('onaylandi_mi', false)
            ->with('bolum', 'roles')
            ->orderBy('name', 'asc');

        // Sistem Kullanıcıları Sorgusu (Personel)
        $sistemKullanicilariQuery = User::where('onaylandi_mi', true)
            ->where('is_personnel', true)
            ->with('bolum', 'roles')
            ->orderBy('name', 'asc');

        // Müşteri Kullanıcıları Sorgusu (Yetkililer)
        $musteriKullanicilariQuery = User::where('onaylandi_mi', true) // <-- DÜZELTME: Sadece onaylıları al
            ->where('is_personnel', false)
            ->with('bolum', 'roles', 'customer') // customer ilişkisini yükle
            ->orderBy('name', 'asc');

        // --- FİLTRELEME MANTIĞI ---
        $filters = $request->only(['name_filter', 'bolum_filter', 'role_filter', 'customer_filter', 'title_filter']); // <-- title_filter eklendi

        // İsim filtresi
        if ($request->filled('name_filter')) {
            $val = '%' . $request->input('name_filter') . '%';
            $sistemKullanicilariQuery->where('name', 'like', $val);
            $musteriKullanicilariQuery->where('name', 'like', $val);
            $onayBekleyenlerQuery->where('name', 'like', $val);
        }

        // Bölüm filtresi
        if ($request->filled('bolum_filter')) {
            $val = $request->input('bolum_filter');
            $sistemKullanicilariQuery->where('bolum_id', $val);
            // Müşteri kullanıcılarının bölümü olmaz ama yine de filtrede kalsın
            $onayBekleyenlerQuery->where('bolum_id', $val);
        }

        // Müşteri (Firma) filtresi
        if ($request->filled('customer_filter')) {
            $val = $request->input('customer_filter');
            $musteriKullanicilariQuery->where('customer_id', $val);
        }

        // Ünvan filtresi (Sadece Müşteri Kullanıcısı)
        if ($request->filled('title_filter')) {
            $val = '%' . $request->input('title_filter') . '%';
            $musteriKullanicilariQuery->where('unvan', 'like', $val);
        }

        // Rol filtresi
        if ($request->filled('role_filter')) {
            $val = $request->input('role_filter');
            $sistemKullanicilariQuery->whereHas('roles', function ($q) use ($val) {
                $q->where('name', $val);
            });
            // Müşteri için rol filtresi kaldırıldı (UI'da yok, backend'de de etkisiz olsun veya kalsın)
            // $musteriKullanicilariQuery->whereHas('roles',...); // İsteğe bağlı
            $onayBekleyenlerQuery->whereHas('roles', function ($q) use ($val) {
                $q->where('name', $val);
            });
        }
        // --- FİLTRELEME MANTIĞI SONU ---

        $onayBekleyenler = $onayBekleyenlerQuery->paginate(10, ['*'], 'onay_page')->withQueryString();
        $sistemKullanicilari = $sistemKullanicilariQuery->paginate(15, ['*'], 'sistem_page')->withQueryString();
        $musteriKullanicilari = $musteriKullanicilariQuery->paginate(15, ['*'], 'musteri_page')->withQueryString();

        return view('admin.users.index', compact(
            'onayBekleyenler',
            'sistemKullanicilari',
            'musteriKullanicilari',
            'kayitOnayiAktif',
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
        if ($user->hasVerifiedEmail()) {
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
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bolum_id' => $request->bolum_id,
            'onaylandi_mi' => true,
            'email_verified_at' => now(), // Admin oluşturduğu için doğrulandı say.
        ];

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($request->name) . '_' . now()->format('Ymd_His') . '.' . $extension;
            $path = $file->storeAs('profile-photos', $filename, 'public');
            $userData['profile_photo_path'] = $path;
        }

        $user = User::create($userData);

        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu ve aktif edildi.');
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
        return view('admin.users.edit', compact('user', 'bolumler', 'roles', 'userRoles'));
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
            'roles' => ['nullable', 'array'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'bolum_id' => $request->bolum_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($request->name) . '_' . now()->format('Ymd_His') . '.' . $extension;
            $path = $file->storeAs('profile-photos', $filename, 'public');
            $updateData['profile_photo_path'] = $path;
        }

        $user->update($updateData);

        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
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
        if ($user->hasRole('Superadmin') && User::role('Superadmin')->count() === 1) {
            return redirect()->route('admin.users.index')->with('error', 'Sistemdeki son Superadmin silinemez.');
        }
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Kendinizi silemezsiniz.');
        }

        // Onaylanmamış kullanıcıyı kalıcı olarak sil
        if (!$user->onaylandi_mi) {
            $user->forceDelete(); // Kalıcı silme
            return redirect()->route('admin.users.index')->with('success', 'Onay bekleyen kullanıcı kaydı kalıcı olarak silindi.');
        }

        // ======================== SİZİN ÖZEL KONTROLLERİNİZ (Korundu) ========================
        if ($user->lideriOlduguTakimlar()->exists()) {
            return back()->with('error', 'Bu kullanıcı bir takımın lideri olduğu için silinemez. Lütfen önce takım liderliğini başka bir kullanıcıya devredin.');
        }
        if ($user->iaas()->exists()) {
            return back()->with('error', 'Bu kullanıcının göndermiş olduğu İAA önerileri bulunduğu için silinemez. Önce önerileri başka bir kullanıcıya atayın veya silin.');
        }
        if ($user->takimlar()->exists()) {
            return back()->with('error', 'Bu kullanıcı bir takıma üye olduğu için silinemez. Lütfen önce kullanıcıyı takımlarından çıkarın.');
        }
        // ===============================================================================

        // Eğer yukarıdaki tüm kontrollerden geçtiyse, onaylanmış kullanıcıyı geçici olarak sil (soft delete)
        $user->delete(); // Geçici silme (Modelinizde SoftDeletes trait'i varsa çalışır)
        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla silindi.');
    }
}

