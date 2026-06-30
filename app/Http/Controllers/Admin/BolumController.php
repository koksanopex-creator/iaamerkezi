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
use App\Notifications\BolumAtamaNotification;

class BolumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Yetki Kontrolü: SADECE SUPERADMIN
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        $query = Bolum::with([
            'kategori',
            'director', // Direktör bilgisi için
            'sikayetWorkflow',
            'liderler',
            'yardimcilar',
        ])->withCount([
            'machines', 
            'sikayetler', 
            'sikayetKategorileri', // Şikayet yönetimi aktif mi kontrolü için
            'users', // Toplam Personel
            'users as mavi_yaka_count' => function($q) {
                $q->where('is_mavi_yaka', true);
            },
            'users as beyaz_yaka_count' => function($q) {
                $q->where('is_mavi_yaka', false);
            }
        ]);

        // Sıralama Mantığı (Varsayılan: Alfabetik A-Z)
        $sort = $request->get('sort', 'alphabetical');
        $direction = $request->get('direction', 'asc');

        // Filtreleme: Ad
        if ($request->filled('ad'))
        {
            $query->where('ad', 'like', '%' . $request->ad . '%');
        }

        // Filtreleme: Kategori
        if ($request->filled('bolum_kategori_id'))
        {
            $query->where('bolum_kategori_id', $request->bolum_kategori_id);
        }

        // --- GELİŞMİŞ SIRALAMA ---
        $sortBy = $request->input('sort_by', 'latest'); // Varsayılan: En Yeni
        $sortOrder = $request->input('sort_order', 'desc');

        switch ($sortBy) {
            case 'ad':
                $query->orderBy('ad', $sortOrder);
                break;
            case 'machines_count':
                $query->orderBy('machines_count', $sortOrder);
                break;
            case 'users_count':
                $query->orderBy('users_count', $sortOrder);
                break;
            case 'sikayetler_count':
                $query->orderBy('sikayetler_count', $sortOrder);
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $bolumler = $query->paginate(24)->withQueryString(); // Kart görünümü için 24 (4'ün katı) daha iyi
        $kategoriler = \App\Models\BolumKategorisi::orderBy('ad')->get();

        // İstatistikler
        $totalBolumCount = Bolum::count();
        $categoryStats = BolumKategorisi::withCount('bolumler')->get();

        return view('admin.bolumler.index', compact('bolumler', 'kategoriler', 'totalBolumCount', 'categoryStats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $kategoriler = BolumKategorisi::orderBy('ad')->get();
        // Müşteri Şikayeti Şablonlarını çek
        $workflows = \App\Models\IaaWorkflow::orderBy('name')->get();

        // [YENİ] Atanabilir Personeller (Beyaz Yaka Personel)
        $atanabilirPersonel = \App\Models\User::personel()
            ->where('is_mavi_yaka', false)
            ->orderBy('name')
            ->get();

        // Mevcut Direktörler
        $directors = \App\Models\User::role('Direktör')->orderBy('name')->get();

        return view('admin.bolumler.create', compact('kategoriler', 'workflows', 'atanabilirPersonel', 'directors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:bolumler',
            'is_active' => 'required|boolean',
            'bolum_kategori_id' => 'nullable|exists:bolum_kategorileri,id',
            'sikayet_workflow_id' => 'nullable|exists:iaa_workflows,id', // <-- YENİ
            'logo_yolu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'has_machines' => 'boolean',
            'director_id' => 'nullable|exists:users,id',
            'lider_id' => 'nullable|exists:users,id',
            'lider_yardimcisi_ids' => 'nullable|array',
            'lider_yardimcisi_ids.*' => 'exists:users,id',
        ]);

        // Logo Yükleme
        $logoPath = null;
        if ($request->hasFile('logo_yolu'))
        {
            $file = $request->file('logo_yolu');
            $filename = 'bolum_' . time() . '.' . $file->getClientOriginalExtension();
            $logoPath = $file->storeAs('bolum_logos', $filename, 'public');
        }

        $bolum = Bolum::create([
            'ad' => $validated['ad'],
            'is_active' => $validated['is_active'],
            'bolum_kategori_id' => $validated['bolum_kategori_id'] ?? null,
            'sikayet_workflow_id' => $validated['sikayet_workflow_id'] ?? null, // <-- YENİ
            'director_id' => $validated['director_id'] ?? null,
            'logo_yolu' => $logoPath,
            'has_machines' => $request->has('has_machines') ? true : false,
        ]);

        // --- LİDER VE DİREKTÖR ATAMA MANTIĞI ---
        
        // 1. Direktör Rolü Güncelleme (Eğer yoksa ata)
        if ($bolum->director_id) {
            $director = \App\Models\User::find($bolum->director_id);
            if (!$director->hasRole('Direktör')) {
                $director->assignRole('Direktör');
            }
        }

        // 2. Bölüm Lideri Atama
        if ($request->lider_id) {
            $lider = \App\Models\User::find($request->lider_id);
            
            // Kullanıcının bölümünü güncelle ve rolü ata
            $lider->update(['bolum_id' => $bolum->id]);
            if (!$lider->hasRole('Bölüm Lideri')) {
                $lider->assignRole('Bölüm Lideri');
            }

            // [BİLDİRİM] Lidere kendi ataması
            $lider->notify(new BolumAtamaNotification($bolum, 'lider_atandi'));
        }

        // 2.5. Bölüm Lider Yardımcısı Atama (ÇOKLU)
        if ($request->filled('lider_yardimcisi_ids')) {
            foreach ($request->lider_yardimcisi_ids as $yardimciId) {
                $yardimci = \App\Models\User::find($yardimciId);
                if ($yardimci) {
                    $yardimci->update(['bolum_id' => $bolum->id]);
                    if (!$yardimci->hasRole('Bölüm Lider Yardımcısı')) {
                        $yardimci->assignRole('Bölüm Lider Yardımcısı');
                    }
                }
            }
        }

        // 3. Direktör Bildirimleri
        if ($bolum->director_id) {
            $director = \App\Models\User::find($bolum->director_id);
            
            // [BİLDİRİM] Direktöre kendi ataması
            $director->notify(new BolumAtamaNotification($bolum, 'direktor_atandi'));

            // [BİLDİRİM] Direktöre bağlı lider ataması (Eğer lider de atandıysa)
            if ($request->lider_id) {
                $lider = \App\Models\User::find($request->lider_id);
                $director->notify(new BolumAtamaNotification($bolum, 'direktor_bagli_lider_atandi', $lider));
            }
        }

        return redirect()->route('admin.bolumler.index')->with('success', 'Bölüm, lider ve direktör atamalarıyla birlikte başarıyla oluşturuldu!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bolum $bolum)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bölüm düzenleme yetkiniz yok.');
        }

        $kategoriler = BolumKategorisi::orderBy('ad')->get();

        // Makineleri de gönderelim (Edit sayfasındaki makine yönetimi için)
        $machines = $bolum->machines()->orderBy('created_at', 'desc')->get();

        // [YENİ] Atanabilir Personeller (Beyaz Yaka Personel)
        $atanabilirPersonel = \App\Models\User::personel()
            ->where('is_mavi_yaka', false)
            ->orderBy('name')
            ->get();

        // Mevcut Direktörler
        $directors = \App\Models\User::role('Direktör')->orderBy('name')->get();

        // Mevcut Bölüm Liderini Bul
        $mevcutLider = \App\Models\User::where('bolum_id', $bolum->id)
            ->role('Bölüm Lideri')
            ->first();

        // Mevcut Bölüm Lider Yardımcılarını Bul (ÇOKLU)
        $mevcutYardimcilar = \App\Models\User::where('bolum_id', $bolum->id)
            ->role('Bölüm Lider Yardımcısı')
            ->get();

        // Müşteri Şikayeti Şablonlarını çek
        $workflows = \App\Models\IaaWorkflow::orderBy('name')->get();

        return view('admin.bolumler.edit', compact('bolum', 'kategoriler', 'machines', 'directors', 'workflows', 'atanabilirPersonel', 'mevcutLider', 'mevcutYardimcilar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bolum $bolum)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bölüm bilgilerini düzenleme yetkiniz yok.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:bolumler,ad,' . $bolum->id,
            'is_active' => 'required|boolean',
            'bolum_kategori_id' => 'nullable|exists:bolum_kategorileri,id',
            'sikayet_workflow_id' => 'nullable|exists:iaa_workflows,id', // <-- YENİ
            'director_id' => 'nullable|exists:users,id', // <-- YENİ
            'lider_id' => 'nullable|exists:users,id', // <-- YENİ
            'lider_yardimcisi_ids' => 'nullable|array',
            'lider_yardimcisi_ids.*' => 'exists:users,id',
            'logo_yolu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'has_machines' => 'boolean',
        ]);

        $data = [
            'ad' => $validated['ad'],
            'is_active' => $validated['is_active'],
            'bolum_kategori_id' => $validated['bolum_kategori_id'] ?? null,
            'sikayet_workflow_id' => $validated['sikayet_workflow_id'] ?? null, // <-- YENİ
            'director_id' => $validated['director_id'] ?? null, // <-- YENİ
            'has_machines' => $request->has('has_machines') ? true : false,
        ];

        // Logo Yükleme
        if ($request->hasFile('logo_yolu'))
        {
            // Eski logoyu sil
            if ($bolum->logo_yolu)
            {
                Storage::disk('public')->delete($bolum->logo_yolu);
            }
            $file = $request->file('logo_yolu');
            $filename = 'bolum_' . time() . '.' . $file->getClientOriginalExtension();
            $data['logo_yolu'] = $file->storeAs('bolum_logos', $filename, 'public');
        }

        $oldDirectorId = $bolum->director_id;
        $bolum->update($data);

        // --- LİDER VE DİREKTÖR GÜNCELLEME MANTIĞI ---

        // 1. Direktör Değişimi
        if ($bolum->director_id && $bolum->director_id != $oldDirectorId) {
            $director = \App\Models\User::find($bolum->director_id);
            if (!$director->hasRole('Direktör')) {
                $director->assignRole('Direktör');
            }
        }

        // 2. Bölüm Lideri Değişimi
        $mevcutLider = \App\Models\User::where('bolum_id', $bolum->id)->role('Bölüm Lideri')->first();
        
        if ($request->lider_id) {
            // Eğer yeni lider şu anki liderden farklıysa
            if (!$mevcutLider || $mevcutLider->id != $request->lider_id) {
                
                // Eski liderin rolünü kaldır (Eğer başka bir bölümün lideri değilse - opsiyonel koruma)
                if ($mevcutLider) {
                    $mevcutLider->removeRole('Bölüm Lideri');
                }

                // Yeni lidere rolü ata ve bölümünü güncelle
                $yeniLider = \App\Models\User::find($request->lider_id);
                $yeniLider->update(['bolum_id' => $bolum->id]);
                if (!$yeniLider->hasRole('Bölüm Lideri')) {
                    $yeniLider->assignRole('Bölüm Lideri');
                }

                // [BİLDİRİM] Yeni lidere kendi ataması
                $yeniLider->notify(new BolumAtamaNotification($bolum, 'lider_atandi'));

                // [BİLDİRİM] Mevcut/Yeni Direktöre bilgi ver (Eğer direktör varsa)
                if ($bolum->director_id) {
                    $director = \App\Models\User::find($bolum->director_id);
                    $director->notify(new BolumAtamaNotification($bolum, 'direktor_bagli_lider_atandi', $yeniLider));
                }
            }
        } elseif ($mevcutLider) {
            // Lider seçimi temizlendiyse mevcut liderin rolünü kaldır
            $mevcutLider->removeRole('Bölüm Lideri');
        }

        // 2.5. Bölüm Lider Yardımcısı Değişimi (ÇOKLU SENKRONİZASYON)
        $mevcutYardimciIdleri = \App\Models\User::where('bolum_id', $bolum->id)
            ->role('Bölüm Lider Yardımcısı')
            ->pluck('id')
            ->toArray();
        
        $yeniYardimciIdleri = $request->input('lider_yardimcisi_ids', []);

        // 1. Artık yardımcı olmayanları çıkar
        $cikarilacaklar = array_diff($mevcutYardimciIdleri, $yeniYardimciIdleri);
        foreach ($cikarilacaklar as $id) {
            $u = \App\Models\User::find($id);
            if ($u) $u->removeRole('Bölüm Lider Yardımcısı');
        }

        // 2. Yeni eklenenlere rol ata
        foreach ($yeniYardimciIdleri as $id) {
            $u = \App\Models\User::find($id);
            if ($u) {
                $u->update(['bolum_id' => $bolum->id]);
                if (!$u->hasRole('Bölüm Lider Yardımcısı')) {
                    $u->assignRole('Bölüm Lider Yardımcısı');
                }
            }
        }

        // 3. Direktör Bildirimleri (Sadece direktör değiştiyse)
        if ($bolum->director_id && $bolum->director_id != $oldDirectorId) {
            $director = \App\Models\User::find($bolum->director_id);
            $director->notify(new BolumAtamaNotification($bolum, 'direktor_atandi'));

            // Yeni atanan direktöre bağlı mevcut lideri de bildir
            $aktifLider = \App\Models\User::where('bolum_id', $bolum->id)->role('Bölüm Lideri')->first();
            if ($aktifLider) {
                $director->notify(new BolumAtamaNotification($bolum, 'direktor_bagli_lider_atandi', $aktifLider));
            }
        }

        return redirect()->route('admin.bolumler.index')->with('success', 'Bölüm bilgileri ve görev atamaları başarıyla güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bolum $bolum)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
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

        // 1. Yetki Kontrolü: Superadmin, Yönetim, Bölüm Lideri (Kendi Bölümü), Direktör (Kendi Bölümü) veya Yetkili Yardımcı
        $isSuperAdmin = $user->hasRole('Superadmin');
        $isYonetim = $user->hasRole('Yonetim');
        $isLider = ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id);
        $isDirektor = ($user->hasRole('Direktör') && $bolum->director_id == $user->id);
        $isYardimci = ($user->isDepartmentDeputy() && $user->bolum_id == $bolum->id);

        if (!$isSuperAdmin && !$isYonetim && !$isLider && !$isDirektor && !$isYardimci)
        {
            abort(403, 'Bu bölümün paneline erişim yetkiniz yok.');
        }

        // Direktörler ve Yardımcılar için salt okunur modu bayrağı (Eğer yardımcıya düzenleme yetkisi verilmediyse)
        // Şimdilik yardımcıyı da readOnly başlatıyoruz, blade içinde spesifik yetkilere bakacağız
        $isReadOnly = ($isDirektor || $isYardimci) && !$isSuperAdmin && !$isYonetim && !$isLider;

        // 1. İstatistikler için yükleme
        $bolum->loadCount(['sikayetler', 'sikayetKategorileri']);

        // Ek İstatistikler — Sadece SAF İAA önerileri (şikayet kaynaklı projeler hariç)
        $iaaQuery = \App\Models\Iaa::where('bolum_id', $bolum->id)
            ->sadeceOneriler()
            ->with(['gonderen', 'atananTakim.lider'])
            ->latest();

        // İAA Tarih Filtreleme
        if ($request->filled('iaa_start_date')) {
            $iaaQuery->whereDate('created_at', '>=', $request->iaa_start_date);
        }
        if ($request->filled('iaa_end_date')) {
            $iaaQuery->whereDate('created_at', '<=', $request->iaa_end_date);
        }

        $iaaProjeleri = $iaaQuery->get();
        $iaa_count = $iaaProjeleri->count();

        // Bölüm personelinin ID'leri
        $bolumUserIds = \App\Models\User::where('bolum_id', $bolum->id)
            ->whereNull('termination_date')
            ->pluck('id');
        $disiplin_count = \App\Models\DisciplinaryCase::whereIn('user_id', $bolumUserIds)->count();

        // 2. Makine
        $machines = $bolum->machines()->with('creator')->orderBy('name', 'asc')->get();

        // 3. Personel (Gelişmiş)
        $usersRaw = \App\Models\User::where('bolum_id', $bolum->id)
            ->whereNull('termination_date') // Aktif personeller
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
            $user->total_score = $user->gorevliOlduguProjeler->map(fn($proj) => $proj->pivot->kazanilan_puan ?? 0)->sum();
            return $user;
        })->sortByDesc(function ($user) {
            // Hiyerarşik Sıralama: Direktör > Lider > Yardımcı > Diğer
            if ($user->hasRole('Direktör')) return 3;
            if ($user->hasRole('Bölüm Lideri')) return 2;
            if ($user->hasRole('Bölüm Lider Yardımcısı')) return 1;
            return 0;
        });

        // Personel Sayıları
        $beyazYakaCount = $users->where('is_mavi_yaka', false)->count();
        $maviYakaCount = $users->where('is_mavi_yaka', true)->count();
        $toplamPersonelCount = $users->count();

        // Onay Bekleyen Personel Sayısı
        $pendingUsersCount = \App\Models\User::where('bolum_id', $bolum->id)
            ->where('onaylandi_mi', false)
            ->whereNull('rejected_at')
            ->count();

        // 4. Bölüm Disiplin Dosyaları
        $disiplinDosyalari = \App\Models\DisciplinaryCase::whereIn('user_id', $bolumUserIds)
            ->with(['user', 'behavior'])
            ->latest('olay_tarihi')
            ->get();

        // 6. Makine Logları (Sadece Superadmin ve Yönetim için)
        $machineLogs = collect();
        if ($user->hasRole('Superadmin') || $user->hasRole('Yonetim'))
        {
            $machineLogs = \App\Models\MachineLog::where('bolum_id', $bolum->id)
                ->with(['user', 'machine'])
                ->latest()
                ->take(10)
                ->get();
        }

        // 7. Hammaddeler ve Versiyonlar
        $hammaddeler = $bolum->genelHammaddeler()->orderBy('ad')->get();
        $versiyonlar = $bolum->urunVersiyonlari()->orderBy('ad')->get();
        // 8. İadeler Verisi
        $iadeQuery = \App\Models\SikayetIadesi::whereHas('musteriSikayeti.sikayetKategori.bolum', fn($q) => $q->where('id', $bolum->id))
            ->with(['musteriSikayeti.sikayetKategori.bolum', 'musteriSikayeti.iaaProjesi', 'musteriSikayeti.customer'])
            ->latest('iade_tarihi');

        if ($request->filled('return_start_date')) {
            $iadeQuery->whereDate('iade_tarihi', '>=', $request->return_start_date);
        }
        if ($request->filled('return_end_date')) {
            $iadeQuery->whereDate('iade_tarihi', '<=', $request->return_end_date);
        }
        if ($request->filled('return_search')) {
            $search = $request->return_search;
            $iadeQuery->where(function ($sq) use ($search) {
                $sq->where('urun_turu', 'like', "%{$search}%")
                    ->orWhere('iade_sebebi', 'like', "%{$search}%")
                    ->orWhereHas('musteriSikayeti', function ($ssq) use ($search) {
                        $ssq->where('musteri_adi', 'like', "%{$search}%")
                            ->orWhere('musteri_sikayet_konusu', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%");
                    });
            });
        }

        $iadeVerileri = $iadeQuery->paginate(10, ['*'], 'return_page')->withQueryString();
        
        $iadeToplamlari = \App\Models\SikayetIadesi::whereHas('musteriSikayeti.sikayetKategori.bolum', fn($q) => $q->where('id', $bolum->id))
            ->select('birim', \DB::raw('SUM(miktar) as toplam_miktar'))
            ->when($request->filled('return_start_date'), fn($q) => $q->whereDate('iade_tarihi', '>=', $request->return_start_date))
            ->when($request->filled('return_end_date'), fn($q) => $q->whereDate('iade_tarihi', '<=', $request->return_end_date))
            ->groupBy('birim')->pluck('toplam_miktar', 'birim');

        return view('admin.bolumler.dashboard', compact(
            'bolum',
            'isReadOnly',
            'isYardimci',
            'iadeVerileri',
            'iadeToplamlari',
            'users',
            'machines',
            'iaa_count',
            'iaaProjeleri',
            'disiplin_count',
            'disiplinDosyalari',
            'machineLogs',
            'hammaddeler',
            'versiyonlar',
            'beyazYakaCount',
            'maviYakaCount',
            'toplamPersonelCount',
            'pendingUsersCount'
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
        // Yetki: Superadmin, Yonetim, Direktör veya Yetkili Lider/Yardımcı
        $user = Auth::user();
        $isAuthorized = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']) || 
                        ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id) ||
                        ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $bolum->id && $user->hasBolumAuthority('bolum.makine.yonet'));

        if (!$isAuthorized) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
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
     * Makine Detay Sayfası (Dashboard'a Yönlendirir ve Modal Açar)
     */
    public function showMachine(Machine $machine)
    {
        return redirect()->route('admin.bolumler.dashboard', $machine->bolum_id)
            ->with('open_machine_edit', $machine->id);
    }

    /**
     * Makine Güncelleme
     */
    public function updateMachine(Request $request, Machine $machine)
    {
        $bolum = $machine->bolum;
        $user = Auth::user();
        
        $isAuthorized = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']) || 
                        ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id) ||
                        ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $bolum->id && $user->hasBolumAuthority('bolum.makine.yonet'));

        if (!$isAuthorized) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
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
        if (!Auth::user()->hasRole('Superadmin'))
        {
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
        $user = Auth::user();
        $isAuthorized = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']) || 
                        ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id) ||
                        ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $bolum->id && $user->hasBolumAuthority('bolum.hammadde.yonet'));

        if (!$isAuthorized) {
            abort(403, 'Hammadde ekleme yetkiniz yok.');
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
        $bolum = $hammadde->bolum;
        $user = Auth::user();
        $isAuthorized = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']) || 
                        ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id) ||
                        ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $bolum->id && $user->hasBolumAuthority('bolum.hammadde.yonet'));

        if (!$isAuthorized) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
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
        $bolum = $hammadde->bolum;
        $user = Auth::user();
        $isAuthorized = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']) || 
                        ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id) ||
                        ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $bolum->id && $user->hasBolumAuthority('bolum.hammadde.yonet'));

        if (!$isAuthorized) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }
        $hammadde->delete();
        return redirect()->back()->with('success', 'Hammadde silindi.');
    }

    // =============================================================
    // VERSİYON YÖNETİMİ
    // =============================================================
    public function storeVersiyon(Request $request, Bolum $bolum)
    {
        $user = Auth::user();
        $isAuthorized = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']) || 
                        ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id) ||
                        ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $bolum->id && $user->hasBolumAuthority('bolum.hammadde.yonet'));

        if (!$isAuthorized) {
            abort(403, 'Versiyon ekleme yetkiniz yok.');
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
        $bolum = $versiyon->bolum;
        $user = Auth::user();
        $isAuthorized = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']) || 
                        ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id) ||
                        ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $bolum->id && $user->hasBolumAuthority('bolum.hammadde.yonet'));

        if (!$isAuthorized) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
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
        $bolum = $versiyon->bolum;
        $user = Auth::user();
        $isAuthorized = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Direktör']) || 
                        ($user->hasRole('Bölüm Lideri') && $user->bolum_id == $bolum->id) ||
                        ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id == $bolum->id && $user->hasBolumAuthority('bolum.hammadde.yonet'));

        if (!$isAuthorized) {
            abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }
        $versiyon->delete();
        return redirect()->back()->with('success', 'Versiyon silindi.');
    }
}
