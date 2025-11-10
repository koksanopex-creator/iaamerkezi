<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bolum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

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
        // --- Veri alımı sonu ---

        // Onay Bekleyenler Sorgusu
        $onayBekleyenlerQuery = User::where('onaylandi_mi', false)
                                      ->with('bolum', 'roles')
                                      ->orderBy('name', 'asc'); // <-- DEĞİŞİKLİK (latest() yerine)

        // Aktif Kullanıcılar Sorgusu
        $aktifKullanicilarQuery = User::where('onaylandi_mi', true)
                                        ->with('bolum', 'roles')
                                        ->orderBy('name', 'asc'); // <-- DEĞİŞİKLİK (latest() yerine)

        // --- FİLTRELEME MANTIĞI ---
        $filters = $request->only(['name_filter', 'bolum_filter', 'role_filter']);

        // İsim filtresi
        if ($request->filled('name_filter')) {
            $aktifKullanicilarQuery->where('name', 'like', '%' . $request->input('name_filter') . '%');
            $onayBekleyenlerQuery->where('name', 'like', '%' . $request->input('name_filter') . '%');
        }

        // Bölüm filtresi
        if ($request->filled('bolum_filter')) {
            $aktifKullanicilarQuery->where('bolum_id', $request->input('bolum_filter'));
            $onayBekleyenlerQuery->where('bolum_id', $request->input('bolum_filter'));
        }

        // Rol filtresi
        if ($request->filled('role_filter')) {
            $aktifKullanicilarQuery->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->input('role_filter'));
            });
            $onayBekleyenlerQuery->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->input('role_filter'));
            });
        }
        // --- FİLTRELEME MANTIĞI SONU ---
        
        $onayBekleyenler = $onayBekleyenlerQuery->paginate(10, ['*'], 'onay_page')->withQueryString();
        $aktifKullanicilar = $aktifKullanicilarQuery->paginate(15, ['*'], 'aktif_page')->withQueryString();

        return view('admin.users.index', compact(
            'onayBekleyenler', 
            'aktifKullanicilar', 
            'kayitOnayiAktif',
            'bolumler',
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
     * Store a newly created resource in storage.
     * (Çoklu rol ataması eklendi)
     */
    public function store(Request $request)
    {
         // === GÜNCELLENDİ: Validation (roles array oldu) ===
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class], // lowercase eklendi
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'bolum_id' => ['nullable', 'exists:bolumler,id'],
            'roles' => ['nullable', 'array'], // Roller array olarak gelebilir
            'roles.*' => ['string', 'exists:roles,name'], // Gelen rollerin geçerli olduğundan emin ol
        ]);
        // ===============================================

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bolum_id' => $request->bolum_id,
            'onaylandi_mi' => true, // Adminin eklediği kullanıcı direkt onaylıdır.
        ]);

        // === GÜNCELLENDİ: Rol ataması syncRoles ile ===
        if ($request->has('roles')) {
            $validRoles = Role::whereIn('name', $request->roles)->pluck('name')->toArray();
            $user->syncRoles($validRoles);
        } else {
             $user->syncRoles([]); // Seçim yoksa tüm rolleri kaldır
        }
        // ==========================================

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu ve aktif edildi.');
    }

     /**
     * Display the specified resource.
     * (Sizde yoktu, eklendi - İsteğe bağlı, detay sayfası için)
     */
    public function show(User $user)
    {
        $user->load('roles', 'bolum'); // Detay sayfasında rolleri ve bölümü göster
        return view('admin.users.show', compact('user')); // admin.users.show view'i oluşturmanız gerekir
    }


    /**
     * Show the form for editing the specified resource.
     * (Kullanıcının mevcut rollerini de view'e gönderir)
     */
    public function edit(User $user)
    {
        // === GÜNCELLENDİ: pluck ve userRoles eklendi ===
        $bolumler = Bolum::orderBy('ad')->pluck('ad', 'id');
         // === DEĞİŞTİ: Rol nesnelerini al ===
        $roles = Role::orderBy('name')->get(); // pluck('name', 'name')->all();
        // ================================
        $userRoles = $user->getRoleNames()->toArray(); // Kullanıcının mevcut rollerini array olarak al
        return view('admin.users.edit', compact('user', 'bolumler', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     * (Çoklu rol güncellemesi eklendi)
     */
    public function update(Request $request, User $user)
    {
        // === GÜNCELLENDİ: Validation (roles array oldu, unique kuralı düzeltildi) ===
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id], // lowercase eklendi
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // min:8 yerine defaults kullanıldı
            'bolum_id' => ['nullable', 'exists:bolumler,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);
        // =========================================================================

        // === GÜNCELLENDİ: update() metodu yerine $updateData array'i kullanıldı ===
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'bolum_id' => $request->bolum_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);
        // =======================================================================

        // === GÜNCELLENDİ: Rol güncellemesi syncRoles ile ===
        if ($request->has('roles')) {
            $validRoles = Role::whereIn('name', $request->roles)->pluck('name')->toArray();
            $user->syncRoles($validRoles);
        } else {
             $user->syncRoles([]); // Seçim yoksa tüm rolleri kaldır
        }
        // ==============================================

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
        if ($user->id === auth()->id()){
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

