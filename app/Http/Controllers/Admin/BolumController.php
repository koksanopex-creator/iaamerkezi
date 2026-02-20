<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bolum;
use App\Models\BolumKategorisi;
use App\Models\Machine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BolumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Yetki Kontrolü: SADECE SUPERADMIN
        if (!Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        $query = Bolum::with([
            'kategori',
            'users' => function ($q) {
                $q->whereHas('roles', function ($r) {
                    // Burada rol ismini tam olarak veritabanındaki gibi yazmalıyız.
                    // Genelde 'Bölüm Lideri' veya 'Bolum Lideri' olabilir. 
                    // RolesSeeder'ı kontrol etmiştik, 'Bölüm Lideri' idi.
                    $r->where('name', 'Bölüm Lideri');
                });
            }
        ])->withCount('machines');

        // Filtreleme: Ad
        if ($request->filled('ad')) {
            $query->where('ad', 'like', '%' . $request->ad . '%');
        }

        // Filtreleme: Kategori
        if ($request->filled('bolum_kategori_id')) {
            $query->where('bolum_kategori_id', $request->bolum_kategori_id);
        }

        // Sıralama: Makine Sayısı
        if ($request->filled('sort_machines')) {
            $query->orderBy('machines_count', $request->sort_machines == 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest(); // Varsayılan sıralama
        }

        $bolumler = $query->paginate(20)->withQueryString();
        $kategoriler = \App\Models\BolumKategorisi::orderBy('ad')->get();

        // İstatistikler
        $totalBolumCount = Bolum::count();
        $categoryStats = BolumKategorisi::withCount('bolumler')->get();

        // 2. Query Güncellemesi: Şikayet Sayısı ve Makine Sayısı
        // withCount 'sikayetler' ilişkisini (Bolum modeline eklediğimiz) kullanır.
        $bolumler = $query->withCount(['machines', 'sikayetler'])
            ->paginate(20)
            ->withQueryString();

        return view('admin.bolumler.index', compact('bolumler', 'kategoriler', 'totalBolumCount', 'categoryStats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $kategoriler = BolumKategorisi::orderBy('ad')->get();
        return view('admin.bolumler.create', compact('kategoriler'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:bolumler',
            'is_active' => 'required|boolean',
            'bolum_kategori_id' => 'nullable|exists:bolum_kategorileri,id',
            'logo_yolu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'has_machines' => 'boolean',
        ]);

        // Logo Yükleme
        $logoPath = null;
        if ($request->hasFile('logo_yolu')) {
            $file = $request->file('logo_yolu');
            $filename = 'bolum_' . time() . '.' . $file->getClientOriginalExtension();
            $logoPath = $file->storeAs('bolum_logos', $filename, 'public');
        }

        Bolum::create([
            'ad' => $validated['ad'],
            'is_active' => $validated['is_active'],
            'bolum_kategori_id' => $validated['bolum_kategori_id'] ?? null,
            'logo_yolu' => $logoPath,
            'has_machines' => $request->has('has_machines') ? true : false,
        ]);

        return redirect()->route('admin.bolumler.index')->with('success', 'Bölüm başarıyla oluşturuldu!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bolum $bolum)
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Bölüm düzenleme yetkiniz yok.');
        }

        $kategoriler = BolumKategorisi::orderBy('ad')->get();

        // Makineleri de gönderelim (Edit sayfasındaki makine yönetimi için)
        $machines = $bolum->machines()->orderBy('created_at', 'desc')->get();

        // Direktörleri çek
        $directors = \App\Models\User::role('Direktör')->orderBy('name')->get();

        return view('admin.bolumler.edit', compact('bolum', 'kategoriler', 'machines', 'directors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bolum $bolum)
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Bölüm bilgilerini düzenleme yetkiniz yok.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:bolumler,ad,' . $bolum->id,
            'is_active' => 'required|boolean',
            'bolum_kategori_id' => 'nullable|exists:bolum_kategorileri,id',
            'director_id' => 'nullable|exists:users,id', // <-- YENİ
            'logo_yolu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'has_machines' => 'boolean',
        ]);

        $data = [
            'ad' => $validated['ad'],
            'is_active' => $validated['is_active'],
            'bolum_kategori_id' => $validated['bolum_kategori_id'] ?? null,
            'director_id' => $validated['director_id'] ?? null, // <-- YENİ
            'has_machines' => $request->has('has_machines') ? true : false,
        ];

        // Logo Yükleme
        if ($request->hasFile('logo_yolu')) {
            // Eski logoyu sil
            if ($bolum->logo_yolu) {
                Storage::disk('public')->delete($bolum->logo_yolu);
            }
            $file = $request->file('logo_yolu');
            $filename = 'bolum_' . time() . '.' . $file->getClientOriginalExtension();
            $data['logo_yolu'] = $file->storeAs('bolum_logos', $filename, 'public');
        }

        $bolum->update($data);

        return redirect()->route('admin.bolumler.index')->with('success', 'Bölüm başarıyla güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bolum $bolum)
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Yetkiniz yok.');
        }
        $bolum->delete();
        return redirect()->route('admin.bolumler.index')->with('success', 'Bölüm başarıyla silindi!');
    }

    // =============================================================
    // BÖLÜM DASHBOARD (BÖLÜM LİDERİ VE ADMİN İÇİN)
    // =============================================================
    public function dashboard(Request $request, Bolum $bolum)
    {
        $user = Auth::user();

        // 1. Yetki Kontrolü: Superadmin, Bölüm Lideri (Kendi Bölümü) veya Yönetim
        if (!$user->hasRole('Superadmin') && !$user->hasRole('Yonetim')) {
            if ($user->bolum_id != $bolum->id) {
                // Eğer kullanıcı bu bölümün lideri değilse, erişim engellensin.
                if (!$user->hasRole('Bölüm Lideri') || $user->bolum_id != $bolum->id) {
                    abort(403, 'Bu bölümün paneline erişim yetkiniz yok.');
                }
            }
        }

        // 1. İstatistikler için yükleme
        $bolum->loadCount(['sikayetler', 'sikayetKategorileri']);

        // Ek İstatistikler
        $iaa_count = \App\Models\Iaa::where('bolum_id', $bolum->id)->count();

        // Bölüm personelinin ID'leri
        $bolumUserIds = \App\Models\User::where('bolum_id', $bolum->id)->pluck('id');
        $disiplin_count = \App\Models\DisciplinaryCase::whereIn('user_id', $bolumUserIds)->count();

        // 2. Makine
        $machines = $bolum->machines()->with('creator')->orderBy('status', 'asc')->get();

        // 3. Personel (Gelişmiş)
        $usersRaw = \App\Models\User::where('bolum_id', $bolum->id)
            ->with(['roles'])
            ->withCount(['iaas', 'gorevliOlduguProjeler', 'disiplinDosyalari'])
            ->with([
                'gorevliOlduguProjeler' => function ($q) {
                    // Puan hesabı için pivot verisine ihtiyacımız var
                }
            ])
            ->orderBy('name', 'asc')
            ->get();

        // Puan Hesabı ve Sıralama (Lider en üste)
        $users = $usersRaw->map(function (\App\Models\User $user) {
            // Toplam Puan: Görevli olduğu projelerden kazandığı puanların toplamı
            $user->total_score = $user->gorevliOlduguProjeler->sum(fn($proj) => $proj->pivot->kazanilan_puan ?? 0);
            return $user;
        })->sortByDesc(function ($user) {
            // Bölüm Lideri en üste (1: Lider, 0: Diğer)
            return $user->hasRole('Bölüm Lideri') ? 1 : 0;
        });

        // 4. Bölüm Disiplin Dosyaları
        $disiplinDosyalari = \App\Models\DisciplinaryCase::whereIn('user_id', $bolumUserIds)
            ->with(['user', 'behavior'])
            ->latest('olay_tarihi')
            ->get();

        // 5. Şikayet Listesi ve Filtreleme
        $sikayetQuery = $bolum->sikayetler()
            ->with(['customer', 'iaaProjesi', 'cozumTakimi.users', 'sikayetKategori', 'sikayetAltKategori'])
            ->latest('musteri_sikayet_tarihi');

        // Filtreleme
        if ($request->filled('start_date')) {
            $sikayetQuery->whereDate('musteri_sikayet_tarihi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $sikayetQuery->whereDate('musteri_sikayet_tarihi', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $sikayetQuery->where('musteri_sikayet_durumu', $request->status);
        }
        if ($request->filled('customer_id')) {
            $sikayetQuery->where('customer_id', $request->customer_id);
        }

        $sikayetler = $sikayetQuery->paginate(10)->withQueryString();

        // Filtreler için veriler
        $relatedCustomerIds = $bolum->sikayetler()->select('customer_id')->distinct()->pluck('customer_id');
        $customers = \App\Models\Customer::whereIn('id', $relatedCustomerIds)->orderBy('name')->get();

        // Statü listesi - Controller'da tanımlıydı, aynen koruyoruz veya modelden çekiyoruz
        // Önceki kodda manuel array vardı, onu koruyalım.
        $statuses = [
            'Yeni',
            'İşlemde',
            'İnceleniyor',
            'Atandı',
            'Devam Ediyor',
            'Çözümlendi',
            'Kapatıldı',
            'Tamamlandı',
            'İptal Edildi',
            'Reddedildi',
            'Revize'
        ];

        // 6. Makine Logları (Sadece Superadmin ve Yönetim için)
        $machineLogs = collect();
        if ($user->hasRole('Superadmin') || $user->hasRole('Yonetim')) {
            $machineLogs = \App\Models\MachineLog::where('bolum_id', $bolum->id)
                ->with(['user', 'machine'])
                ->latest()
                ->take(10)
                ->get();
        }

        // 7. Hammaddeler ve Versiyonlar
        $hammaddeler = $bolum->genelHammaddeler()->orderBy('ad')->get();
        $versiyonlar = $bolum->urunVersiyonlari()->orderBy('ad')->get();

        return view('admin.bolumler.dashboard', compact(
            'bolum',
            'users',
            'machines',
            'sikayetler',
            'customers',
            'statuses',
            'iaa_count',
            'disiplin_count',
            'disiplinDosyalari',
            'machineLogs',
            'hammaddeler',
            'versiyonlar'
        ));
    }

    // =============================================================
    // MAKİNE YÖNETİMİ METOTLARI
    // =============================================================

    /**
     * Yeni Makine Ekleme
     */
    /**
     * Yeni Makine Ekleme
     */
    /**
     * Yeni Makine Ekleme
     */
    public function storeMachine(Request $request, Bolum $bolum)
    {
        // Yetki: Superadmin veya Kendi Bölümü Olan Lider
        if (!Auth::user()->hasRole('Superadmin')) {
            if (Auth::user()->bolum_id != $bolum->id) {
                abort(403, 'Yetkisiz işlem.');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'installation_date' => 'nullable|date',
            'status' => 'required|string|in:active,maintenance,repair,broken,inactive',
        ]);

        $machine = Machine::create([
            'bolum_id' => $bolum->id,
            'name' => $validated['name'],
            'installation_date' => $validated['installation_date'],
            'status' => $validated['status'],
            'created_by' => Auth::id(),
        ]);

        \App\Models\MachineLog::create([
            'machine_id' => $machine->id,
            'user_id' => Auth::id(),
            'bolum_id' => $bolum->id,
            'action' => 'Ekleme',
            'details' => [
                'name' => $machine->name,
                'status' => $machine->status,
                'installation_date' => $machine->installation_date
            ]
        ]);

        return redirect()->back()->with('success', 'Makine başarıyla eklendi.');
    }

    /**
     * Makine Güncelleme
     */
    public function updateMachine(Request $request, Machine $machine)
    {
        // Yetki: Superadmin veya Kendi Bölümü Olan Lider
        if (!Auth::user()->hasRole('Superadmin')) {
            if (Auth::user()->bolum_id != $machine->bolum_id) {
                abort(403, 'Yetkisiz işlem.');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'installation_date' => 'nullable|date',
            'status' => 'required|string|in:active,maintenance,repair,broken,inactive',
        ]);

        $oldData = $machine->toArray();

        $machine->update([
            'name' => $validated['name'],
            'installation_date' => $validated['installation_date'],
            'status' => $validated['status'],
            'updated_by' => Auth::id(),
        ]);

        \App\Models\MachineLog::create([
            'machine_id' => $machine->id,
            'user_id' => Auth::id(),
            'bolum_id' => $machine->bolum_id,
            'action' => 'Güncelleme',
            'details' => [
                'old' => $oldData,
                'new' => $machine->toArray()
            ]
        ]);

        return redirect()->back()->with('success', 'Makine bilgileri güncellendi.');
    }

    /**
     * Makine Silme (SADECE SUPERADMIN)
     */
    public function deleteMachine(Machine $machine)
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Makine silme yetkisi sadece sistem yöneticisindedir.');
        }

        \App\Models\MachineLog::create([
            'machine_id' => $machine->id,
            'user_id' => Auth::id(),
            'bolum_id' => $machine->bolum_id,
            'action' => 'Silme',
            'details' => [
                'deleted_machine_name' => $machine->name
            ]
        ]);

        $machine->delete();

        return redirect()->back()->with('success', 'Makine silindi.');
    }

    // =============================================================
    // HAMMADDE YÖNETİMİ
    // =============================================================
    public function storeHammadde(Request $request, Bolum $bolum)
    {
        if (!Auth::user()->hasRole('Superadmin') && Auth::user()->bolum_id != $bolum->id) {
            abort(403, 'Yetkisiz işlem.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
        ]);

        \App\Models\GenelHammadde::create([
            'bolum_id' => $bolum->id,
            'ad' => $validated['ad'],
            'aktif_mi' => $validated['aktif_mi'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Hammadde eklendi.');
    }

    public function updateHammadde(Request $request, \App\Models\GenelHammadde $hammadde)
    {
        if (!Auth::user()->hasRole('Superadmin') && Auth::user()->bolum_id != $hammadde->bolum_id) {
            abort(403, 'Yetkisiz işlem.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
        ]);

        $hammadde->update([
            'ad' => $validated['ad'],
            'aktif_mi' => $request->has('aktif_mi'),
        ]);

        return redirect()->back()->with('success', 'Hammadde güncellendi.');
    }

    public function deleteHammadde(\App\Models\GenelHammadde $hammadde)
    {
        if (!Auth::user()->hasRole('Superadmin') && Auth::user()->bolum_id != $hammadde->bolum_id) {
            abort(403, 'Yetkisiz işlem.');
        }
        $hammadde->delete();
        return redirect()->back()->with('success', 'Hammadde silindi.');
    }

    // =============================================================
    // VERSİYON YÖNETİMİ
    // =============================================================
    public function storeVersiyon(Request $request, Bolum $bolum)
    {
        if (!Auth::user()->hasRole('Superadmin') && Auth::user()->bolum_id != $bolum->id) {
            abort(403, 'Yetkisiz işlem.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
        ]);

        \App\Models\UrunVersiyonu::create([
            'bolum_id' => $bolum->id,
            'ad' => $validated['ad'],
            'aktif_mi' => $validated['aktif_mi'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Versiyon eklendi.');
    }

    public function updateVersiyon(Request $request, \App\Models\UrunVersiyonu $versiyon)
    {
        if (!Auth::user()->hasRole('Superadmin') && Auth::user()->bolum_id != $versiyon->bolum_id) {
            abort(403, 'Yetkisiz işlem.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
        ]);

        $versiyon->update([
            'ad' => $validated['ad'],
            'aktif_mi' => $request->has('aktif_mi'),
        ]);

        return redirect()->back()->with('success', 'Versiyon güncellendi.');
    }

    public function deleteVersiyon(\App\Models\UrunVersiyonu $versiyon)
    {
        if (!Auth::user()->hasRole('Superadmin') && Auth::user()->bolum_id != $versiyon->bolum_id) {
            abort(403, 'Yetkisiz işlem.');
        }
        $versiyon->delete();
        return redirect()->back()->with('success', 'Versiyon silindi.');
    }
}
