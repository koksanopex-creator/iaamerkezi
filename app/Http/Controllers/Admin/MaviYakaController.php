<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bolum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MaviYakaController extends Controller
{
    // Middleware tanımlamalarını route'ta yapıyoruz (web.php). Bölüm lideri ve Admin erişebilir.

    /**
     * Mavi yaka personel listesini getirir.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status', 'active');

        // Kullanıcının yetkili olduğu bölümleri alıyoruz
        $responsibleDeptIds = $user->getResponsibleDepartments();

        $query = User::where('is_mavi_yaka', true)
                     ->whereIn('bolum_id', $responsibleDeptIds);

        if ($status === 'resigned')
        {
            $query->onlyTrashed();
        }

        // Arama filtreleri
        if ($request->filled('search'))
        {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('tc_kimlik_no', 'like', "%{$search}%")
                    ->orWhere('sicil_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bolum'))
        {
            $query->where('bolum_id', $request->bolum);
        }

        $kullanicilar = $query->with('bolum')->orderBy('created_at', 'desc')->paginate(15);
        $bolumler = Bolum::orderBy('ad')->get();

        // İstatistikler (Yetkisel Filtreli)
        $stats = [
            'active' => User::where('is_mavi_yaka', true)->whereIn('bolum_id', $responsibleDeptIds)->count(),
            'resigned' => User::where('is_mavi_yaka', true)->whereIn('bolum_id', $responsibleDeptIds)->onlyTrashed()->count(),
        ];

        return view('admin.mavi-yaka.index', compact('kullanicilar', 'bolumler', 'status', 'stats'));
    }

    /**
     * Yeni mavi yaka ekleme formunu gösterir.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $preselectedBolumId = $request->query('bolum_id');

        // Bölüm lideri veya yetkili yardımcısıysa sadece kendi bölümüne ekleyebilir
        if (($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini']))
        {
            $bolumler = Bolum::where('id', $user->bolum_id)->get();
        }
        else
        {
            $bolumler = Bolum::orderBy('ad')->get();
        }

        return view('admin.mavi-yaka.create', compact('bolumler', 'preselectedBolumId'));
    }

    /**
     * Yeni mavi yaka personeli kaydeder.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Bölüm doğrulama kuralları (Lider veya yetkili yardımcı sadece kendi bölümüne ekleyebilir)
        $bolumIds = ($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini'])
            ? [$user->bolum_id]
            : Bolum::pluck('id')->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'tc_kimlik_no' => 'required|string|size:11|unique:users',
            'sicil_no' => 'nullable|string|max:50',
            'unvan' => 'nullable|string|max:255',
            'bolum_id' => ['required', Rule::in($bolumIds)],
            'photo' => 'nullable|image|max:2048', // Opsiyonel fotoğraf
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
        ], [
            'bolum_id.in' => 'Geçersiz departman seçimi veya bu departmana personel ekleme yetkiniz yok.',
        ]);

        $maviYaka = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tc_kimlik_no' => $validated['tc_kimlik_no'],
            'sicil_no' => $validated['sicil_no'] ?? null,
            'unvan' => $validated['unvan'] ?? 'Mavi Yaka Personel',
            'bolum_id' => $validated['bolum_id'],
            'hire_date' => $validated['hire_date'] ?? null,
            'termination_date' => $validated['termination_date'] ?? null,
            'is_mavi_yaka' => true,
            'is_personnel' => true,
            'onaylandi_mi' => true, // Otomatik onaylı gelsin
            'email_verified_at' => now(), // E-posta doğrulamasını şimdilik atlıyoruz
        ]);

        // SSO Senkronizasyonu
        try {
            app(\App\Services\CentralSsoSyncService::class)->syncUser($maviYaka, $validated['password'], 'mavi_yaka');
        } catch (\Exception $ssoEx) {
            \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for mavi yaka: ' . $ssoEx->getMessage());
        }

        // Fotoğraf yükleme (Rule 4 uyumlu)
        if ($request->hasFile('photo'))
        {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $folderName = Str::slug($maviYaka->name);
            $fileName = now()->format('d.m.Y_H.i') . '_' . Str::random(2) . '.' . $extension;

            // storage/profile_photos/mavi_yaka/{user_name}/{user_id}/tarih_random.ext
            $path = $file->storeAs("profile_photos/mavi_yaka/{$folderName}/{$maviYaka->id}", $fileName, 'public');
            $maviYaka->update(['profile_photo_path' => $path]);
        }

        // Mavi Yaka rolü ver (eğer Spatie'de tanımlı bir rol varsa ekstra atanabilir)
        // Ancak bu şimdilik bir bayrak (is_mavi_yaka) olduğu için rol ataması yapmasak da olur
        // ya da "Mavi Yaka" diye bir standart rol açıp verebiliriz.

        return redirect()->route('admin.mavi-yaka.index')
            ->with('success', 'Mavi Yaka personel başarıyla oluşturuldu.');
    }

    /**
     * Düzenleme formunu gösterir.
     */
    public function edit(User $maviYaka)
    {
        $user = Auth::user();

        // Check if user is Mavi Yaka
        if (!$maviYaka->isMaviYaka())
        {
            abort(404, 'Mavi Yaka personeli bulunamadı.');
        }

        // Bölüm lideri/yardımcısıysa ve personelin bölümü kendisinden farklıysa engel ol
        if (($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini']))
        {
            if ($maviYaka->bolum_id != $user->bolum_id)
            {
                abort(403, 'Sadece kendi departmanınızdaki personeli düzenleyebilirsiniz.');
            }
            $bolumler = Bolum::where('id', $user->bolum_id)->get();
        }
        else
        {
            $bolumler = Bolum::orderBy('ad')->get();
        }

        return view('admin.mavi-yaka.edit', compact('maviYaka', 'bolumler'));
    }

    /**
     * Düzenlenen mavi yaka personelini kaydeder.
     */
    public function update(Request $request, User $maviYaka)
    {
        $user = Auth::user();

        if (!$maviYaka->isMaviYaka())
        {
            abort(404, 'Mavi Yaka personeli bulunamadı.');
        }

        // Bölüm lideri/yardımcısıysa yetkisini kontrol et
        if (($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini']))
        {
            if ($maviYaka->bolum_id != $user->bolum_id)
            {
                abort(403, 'Sadece kendi departmanınızdaki personeli düzenleyebilirsiniz.');
            }
        }

        $bolumIds = ($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini'])
            ? [$user->bolum_id]
            : Bolum::pluck('id')->toArray();

        $rules = [
            /* MERKEZİ SİSTEM GEÇİŞİ: Ad, Email ve TC doğrulamasını kapattık.
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($maviYaka->id)],
            'tc_kimlik_no' => ['required', 'string', 'size:11', Rule::unique('users')->ignore($maviYaka->id)],
            */
            'sicil_no' => 'nullable|string|max:50',
            'unvan' => 'nullable|string|max:255',
            'bolum_id' => ['required', Rule::in($bolumIds)],
            'photo' => 'nullable|image|max:2048',
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
        ];

        /* MERKEZİ SİSTEM GEÇİŞİ: Şifre doğrulaması kapatıldı.
        // Şifre boş değilse kurallara ekle
        if ($request->filled('password'))
        {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        */

        $validated = $request->validate($rules, [
            'bolum_id.in' => 'Geçersiz departman seçimi veya bu departmana personel ekleme yetkiniz yok.',
        ]);

        // Fotoğraf güncelleme (Rule 4 uyumlu)
        if ($request->hasFile('photo'))
        {
            // Eski fotoğrafı sil
            if ($maviYaka->profile_photo_path)
            {
                Storage::disk('public')->delete($maviYaka->profile_photo_path);
            }

            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $folderName = Str::slug($validated['name']);
            $fileName = now()->format('d.m.Y_H.i') . '_' . Str::random(2) . '.' . $extension;

            $path = $file->storeAs("profile_photos/mavi_yaka/{$folderName}/{$maviYaka->id}", $fileName, 'public');
            $maviYaka->profile_photo_path = $path;
        }

        /* MERKEZİ SİSTEM GEÇİŞİ: Ad, Email, TC veri tabanı güncellemeleri kapatıldı.
        $maviYaka->name = $validated['name'];
        $maviYaka->email = $validated['email'];
        $maviYaka->tc_kimlik_no = $validated['tc_kimlik_no'];
        */
        $maviYaka->sicil_no = $validated['sicil_no'] ?? null;
        $maviYaka->unvan = $validated['unvan'] ?? null;
        $maviYaka->bolum_id = $validated['bolum_id'];
        $maviYaka->hire_date = $validated['hire_date'] ?? null;
        $maviYaka->termination_date = $validated['termination_date'] ?? null;

        /* MERKEZİ SİSTEM GEÇİŞİ: Şifreler Merkezden yönetildiği için kapatıldı.
        if ($request->filled('password'))
        {
            $maviYaka->password = Hash::make($validated['password']);
        }
        */

        $maviYaka->save();

        // SSO Senkronizasyonu (Güncelleme Merkezi'ne İletilir)
        try {
            app(\App\Services\CentralSsoSyncService::class)->syncUser($maviYaka, null, 'mavi_yaka');
        } catch (\Exception $ssoEx) {
            \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for mavi yaka update: ' . $ssoEx->getMessage());
        }

        return redirect()->route('admin.mavi-yaka.index')
            ->with('success', 'Mavi Yaka personel bilgileri güncellendi.');
    }

    /**
     * Mavi yaka personelini siler (Soft Delete).
     */
    public function destroy(User $maviYaka)
    {
        $user = Auth::user();

        if (!$maviYaka->isMaviYaka())
        {
            abort(404, 'Mavi Yaka personeli bulunamadı.');
        }

        // Yetki kontrolü (Lider/Yardımcı kısıtı)
        if (($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini']))
        {
            if ($maviYaka->bolum_id != $user->bolum_id)
            {
                abort(403, 'Sadece kendi departmanınızdaki personeli silebilirsiniz.');
            }
        }

        $maviYaka->delete();

        return redirect()->route('admin.mavi-yaka.index')
            ->with('success', 'Personel başarıyla silindi.');
    }

    /**
     * Mevcut listeyi Excel (CSV) olarak dışa aktarır.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status', 'active');
        $query = User::where('is_mavi_yaka', true);

        if ($status === 'resigned')
        {
            $query->onlyTrashed();
        }

        // Bölüm lideri/yardımcısıysa sadece kendi bölümündekileri görebilir
        if (($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini']))
        {
            $query->where('bolum_id', $user->bolum_id);
        }

        // Arama filtreleri (index ile aynı mantık)
        if ($request->filled('search'))
        {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('tc_kimlik_no', 'like', "%{$search}%")
                    ->orWhere('sicil_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bolum'))
        {
            $query->where('bolum_id', $request->bolum);
        }

        $kullanicilar = $query->with('bolum')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mavi_yaka_listesi_' . now()->format('d.m.Y_H.i') . '.csv"',
        ];

        $columns = ['sicil_no', 'name', 'bolum', 'unvan', 'tc_kimlik_no', 'hire_date', 'termination_date'];

        $callback = function () use ($columns, $kullanicilar) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel UTF-8
            fputcsv($file, $columns, ';');

            foreach ($kullanicilar as $personel)
            {
                fputcsv($file, [
                    $personel->sicil_no,
                    $personel->name,
                    $personel->bolum->ad ?? '',
                    $personel->unvan,
                    $personel->tc_kimlik_no,
                    $personel->hire_date ? $personel->hire_date->format('d.m.Y') : '',
                    $personel->termination_date ? $personel->termination_date->format('d.m.Y') : ''
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * İçe aktarma formunu gösterir.
     */
    public function importView()
    {
        return view('admin.mavi-yaka.import');
    }

    /**
     * Örnek CSV şablonu indirir.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mavi_yaka_sablon.csv"',
        ];

        $columns = ['sicil_no', 'name', 'bolum', 'unvan', 'tc_kimlik_no', 'hire_date', 'termination_date'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel UTF-8
            fputcsv($file, $columns, ';');
            // Örnek satır
            fputcsv($file, ['1234', 'Örnek Personel', 'GENEL PLANLAMA', 'PAKETLEME PERSONELİ', '11122233344', '01.01.2024', ''], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Dosyayı yükler ve önizleme sunar.
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx',
        ]);

        $file = $request->file('file');

        // Excel::toArray ile veriyi alıyoruz
        $data = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $file);
        $rows = $this->fixTurkishChars($data[0] ?? []);

        if (empty($rows))
        {
            return back()->with('error', 'Dosya boş veya okunamadı.');
        }

        // Başlıkları kontrol et (İlk satır başlık varsayıyoruz)
        $headers = array_shift($rows);

        // Sütun indekslerini bul (Esnek kolon sırası için)
        $indices = [
            'sicil_no' => array_search('sicil_no', $headers),
            'name' => array_search('name', $headers),
            'bolum' => array_search('bolum', $headers),
            'unvan' => array_search('unvan', $headers),
            'tc_kimlik_no' => array_search('tc_kimlik_no', $headers),
            'hire_date' => array_search('hire_date', $headers),
            'termination_date' => array_search('termination_date', $headers),
        ];

        if ($indices['name'] === false || $indices['tc_kimlik_no'] === false || $indices['bolum'] === false)
        {
            return back()->with('error', 'Dosyada gerekli başlıklar (name, bolum, tc_kimlik_no) bulunamadı.');
        }

        $newDepartments = [];
        $existingDepartments = [];
        $usersToSync = [];

        foreach ($rows as $row)
        {
            $deptName = trim($row[$indices['bolum']] ?? '');
            if (!$deptName)
                continue;

            if (!isset($existingDepartments[$deptName]))
            {
                $dept = Bolum::withTrashed()->where('ad', $deptName)->first();
                if ($dept)
                {
                    $existingDepartments[$deptName] = $dept->id;
                }
                else
                {
                    $newDepartments[$deptName] = true;
                }
            }

            $usersToSync[] = [
                'sicil_no' => ($indices['sicil_no'] !== false) ? ($row[$indices['sicil_no']] ?? null) : null,
                'name' => ($indices['name'] !== false) ? ($row[$indices['name']] ?? null) : null,
                'bolum' => $deptName,
                'unvan' => ($indices['unvan'] !== false) ? ($row[$indices['unvan']] ?? null) : null,
                'tc_kimlik_no' => ($indices['tc_kimlik_no'] !== false) ? ($row[$indices['tc_kimlik_no']] ?? null) : null,
                'hire_date' => ($indices['hire_date'] !== false) ? ($row[$indices['hire_date']] ?? null) : null,
                'termination_date' => ($indices['termination_date'] !== false) ? ($row[$indices['termination_date']] ?? null) : null,
            ];
        }

        session(['mavi_yaka_import_data' => $usersToSync]);
        session(['mavi_yaka_import_mode' => $request->mode]); // update or add

        return view('admin.mavi-yaka.import_preview', [
            'newDepts' => array_keys($newDepartments),
            'existingDepts' => array_keys($existingDepartments),
            'userCount' => count($usersToSync),
            'mode' => $request->mode
        ]);
    }

    /**
     * Önizleme sonrası gerçek aktarımı yapar.
     */
    public function executeImport(Request $request)
    {
        $data = session('mavi_yaka_import_data');
        $mode = session('mavi_yaka_import_mode');

        if (!$data)
        {
            return redirect()->route('admin.mavi-yaka.index')->with('error', 'Geçersiz işlem.');
        }

        $addedCount = 0;
        $updatedCount = 0;

        foreach ($data as $userData)
        {
            // Bölümü bul, pasifse/silinmişse aktifleştir, yoksa oluştur
            $deptAd = trim($userData['bolum'] ?? '');
            $dept = Bolum::withTrashed()->where('ad', $deptAd)->first();
            if ($dept) {
                if ($dept->trashed()) {
                    $dept->restore();
                }
            } else {
                $dept = Bolum::create(['ad' => $deptAd]);
            }

            // Kullanıcıyı TC No ile bul
            $user = User::where('tc_kimlik_no', $userData['tc_kimlik_no'])->first();

            if ($user)
            {
                if ($mode === 'update')
                {
                    $user->update([
                        'name' => $userData['name'],
                        'sicil_no' => $userData['sicil_no'],
                        'unvan' => $userData['unvan'],
                        'bolum_id' => $dept->id,
                        'is_mavi_yaka' => true,
                        'is_personnel' => true,
                    ]);
                    $updatedCount++;
                }
            }
            else
            {
                $maviYakaImported = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['tc_kimlik_no'] . '@koksan.com', // Geçici eposta
                    'password' => Hash::make($userData['tc_kimlik_no']), // Şifre TC No olsun
                    'tc_kimlik_no' => $userData['tc_kimlik_no'],
                    'sicil_no' => $userData['sicil_no'],
                    'unvan' => $userData['unvan'],
                    'bolum_id' => $dept->id,
                    'hire_date' => !empty($userData['hire_date']) ?\Carbon\Carbon::parse($userData['hire_date']) : null,
                    'termination_date' => !empty($userData['termination_date']) ?\Carbon\Carbon::parse($userData['termination_date']) : null,
                    'is_mavi_yaka' => true,
                    'is_personnel' => true,
                    'onaylandi_mi' => true,
                    'email_verified_at' => now(),
                ]);

                // SSO Senkronizasyonu
                try {
                    app(\App\Services\CentralSsoSyncService::class)->syncUser($maviYakaImported, $userData['tc_kimlik_no'], 'mavi_yaka');
                } catch (\Exception $ssoEx) {
                    \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for imported mavi yaka: ' . $ssoEx->getMessage());
                }

                $addedCount++;
            }
        }

        session()->forget(['mavi_yaka_import_data', 'mavi_yaka_import_mode']);

        return redirect()->route('admin.mavi-yaka.index')
            ->with('success', "İşlem tamamlandı. {$addedCount} yeni personel eklendi, {$updatedCount} personel güncellendi.");
    }

    /**
     * AJAX üzerinden paketli (chunked) aktarım yapar.
     */
    public function executeImportChunk(Request $request)
    {
        $data = session('mavi_yaka_import_data');
        $mode = session('mavi_yaka_import_mode');

        $chunkIndex = intval($request->input('chunk_index', 0));
        $chunkSize = 10; // Paket büyüklüğü

        if (!$data)
        {
            return response()->json(['error' => 'Geçersiz işlem. Oturum verisi bulunamadı.'], 400);
        }

        $totalItems = count($data);
        $start = $chunkIndex * $chunkSize;
        $chunk = array_slice($data, $start, $chunkSize);

        $added = 0;
        $updated = 0;
        $errors = [];

        foreach ($chunk as $index => $userData)
        {
            try {
                $rowNum = $start + $index + 1;
                $pName = $userData['name'] ?? 'İsimsiz';
                
                // 1. Temel Doğrulamalar
                if (empty(trim($userData['tc_kimlik_no'] ?? ''))) {
                    throw new \Exception("TC Kimlik No eksik.");
                }
                if (empty(trim($userData['bolum'] ?? ''))) {
                    throw new \Exception("Bölüm bilgisi eksik.");
                }

                $deptAd = trim($userData['bolum'] ?? '');
                $dept = Bolum::withTrashed()->where('ad', $deptAd)->first();
                if ($dept) {
                    if ($dept->trashed()) {
                        $dept->restore();
                    }
                } else {
                    $dept = Bolum::create(['ad' => $deptAd]);
                }
                
                // TC kontrolü (Silinmişler dahil)
                $user = User::withTrashed()->where('tc_kimlik_no', $userData['tc_kimlik_no'])->first();

                // Tarihleri hazırla
                $hDate = $this->parseSafeDate($userData['hire_date']);
                $tDate = $this->parseSafeDate($userData['termination_date']);

                if ($user)
                {
                    if ($mode === 'update')
                    {
                        $user->update([
                            'name' => $userData['name'],
                            'sicil_no' => $userData['sicil_no'],
                            'unvan' => $userData['unvan'],
                            'bolum_id' => $dept->id,
                            'hire_date' => $hDate,
                            'termination_date' => $tDate,
                            'is_mavi_yaka' => true,
                            'is_personnel' => true,
                        ]);
                        
                        // İşten çıkış tarihi varsa Soft Delete yap (İşten çıkanlar listesine taşır)
                        if ($tDate) {
                            $user->delete();
                        } else {
                            // Çıkış tarihi yoksa ama önceden silinmişse (tekrar işe alınmışsa) geri al
                            if ($user->trashed()) {
                                $user->restore();
                            }
                        }

                        $updated++;
                    }
                }
                else
                {
                    // Email çakışması kontrolü (Silinmişler dahil)
                    $targetEmail = $userData['tc_kimlik_no'] . '@koksan.com';
                    if (User::withTrashed()->where('email', $targetEmail)->exists()) {
                         throw new \Exception("Üretilen e-posta ({$targetEmail}) sistemde (silinmişler dahil) zaten var. Başka bir personelde bu TC kayıtlı olabilir.");
                    }

                    $newUser = User::create([
                        'name' => $userData['name'],
                        'email' => $targetEmail, 
                        'password' => Hash::make($userData['tc_kimlik_no']), 
                        'tc_kimlik_no' => $userData['tc_kimlik_no'],
                        'sicil_no' => $userData['sicil_no'],
                        'unvan' => $userData['unvan'],
                        'bolum_id' => $dept->id,
                        'hire_date' => $hDate,
                        'termination_date' => $tDate,
                        'is_mavi_yaka' => true,
                        'is_personnel' => true,
                        'onaylandi_mi' => true,
                        'email_verified_at' => now(),
                    ]);

                    // SSO Senkronizasyonu
                    try {
                        app(\App\Services\CentralSsoSyncService::class)->syncUser($newUser, $userData['tc_kimlik_no'], 'mavi_yaka');
                    } catch (\Exception $ssoEx) {
                        \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for chunk imported mavi yaka: ' . $ssoEx->getMessage());
                    }

                    // Eğer Excel'de doğrudan işten çıkmış olarak eklenmişse anında Soft Delete yap
                    if ($tDate) {
                        $newUser->delete();
                    }

                    $added++;
                }
            } catch (\Exception $e) {
                \Log::error("Aktarım Satır Hatası ({$rowNum}): " . $e->getMessage());
                $errors[] = [
                    'row' => $rowNum,
                    'name' => $userData['name'] ?? 'Bilinmiyor',
                    'message' => $e->getMessage()
                ];
            }
        }

        $isFinished = ($start + $chunkSize) >= $totalItems;
        $progress = min(100, round((($start + count($chunk)) / $totalItems) * 100));

        return response()->json([
            'success' => true,
            'added' => $added,
            'updated' => $updated,
            'errors' => $errors,
            'is_finished' => $isFinished,
            'progress' => $progress
        ]);
    }

    /**
     * Tarih metnini güvenli bir şekilde parse eder.
     */
    private function parseSafeDate($dateString)
    {
        if (empty($dateString)) return null;
        
        try {
            // Excel'den gelen farklı formatları yakalamaya çalış
            if (is_numeric($dateString)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
            }
            
            // gg.aa.yyyy formatını dene
            if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $dateString)) {
                return \Carbon\Carbon::createFromFormat('d.m.Y', $dateString);
            }

            $carbonDate = \Carbon\Carbon::parse($dateString);
            if ($carbonDate->year < 1950) return null; // 1950 öncesi bir tarih mavi yaka girişi için hatalı veridir
            return $carbonDate;
        } catch (\Exception $e) {
            return null; // Parse edilemiyorsa boş bırak, çökmesini engelle
        }
    }

    /**
     * Aktarım bittiğinde session'ı temizler ve yönlendirir.
     */
    public function finishImport(Request $request)
    {
        $added = $request->input('added', 0);
        $updated = $request->input('updated', 0);
        $newDepts = $request->input('newDepts', 0);

        session()->forget(['mavi_yaka_import_data', 'mavi_yaka_import_mode']);

        $message = "BAŞARILI: {$added} yeni personel sisteme eklendi, {$updated} personel güncellendi.";
        if ($newDepts > 0) {
            $message .= " Ayrıca {$newDepts} yeni bölüm otomatik olarak oluşturuldu.";
        }

        return redirect()->route('admin.mavi-yaka.index')->with('success', $message);
    }

    /**
     * Personeli "İşten Çıktı" (Soft Delete) yapar.
     */
    public function resign(User $maviYaka)
    {
        $user = Auth::user();

        // Yetki kontrolü (Daha önce destroy'da vardı, buraya da ekleyelim - Lider/Yardımcı kısıtı)
        if (($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini']))
        {
            if ($maviYaka->bolum_id != $user->bolum_id)
            {
                abort(403, 'Sadece kendi departmanınızdaki personeli işten çıkarabilirsiniz.');
            }
        }

        $maviYaka->termination_date = now();
        $maviYaka->save();
        $maviYaka->delete(); // Soft delete

        return redirect()->route('admin.mavi-yaka.index')
            ->with('success', "{$maviYaka->name} işten çıktı olarak işaretlendi ve listeden kaldırıldı.");
    }

    /**
     * İşten çıkarılan personeli geri alır.
     */
    public function restore($id)
    {
        $user = Auth::user();
        $maviYaka = User::onlyTrashed()->findOrFail($id);

        if (!$maviYaka->isMaviYaka())
        {
            abort(404, 'Mavi Yaka personeli bulunamadı.');
        }

        // Yetki kontrolü (Lider/Yardımcı kısıtı)
        if (($user->hasRole('Bölüm Lideri') || $user->hasBolumAuthority('bolum.mavi_yaka.yonet')) && !$user->hasAnyRole(['Superadmin', 'Hukuk Admini']))
        {
            if ($maviYaka->bolum_id != $user->bolum_id)
            {
                abort(403, 'Sadece kendi departmanınızdaki personeli geri alabilirsiniz.');
            }
        }

        $maviYaka->termination_date = null;
        $maviYaka->save();
        $maviYaka->restore();

        return redirect()->route('admin.mavi-yaka.index', ['status' => 'resigned'])
            ->with('success', "{$maviYaka->name} personeli başarıyla geri alındı.");
    }

    /**
     * Hatalı Karakter Kodlaması (Latin-1/Windows-1254) Düzeltici
     * Excel'den CSV dışa aktarırken oluşan İ->Ý, Ğ->Ð gibi bozulmaları giderir.
     */
    private function fixTurkishChars($text)
    {
        if (is_array($text)) {
            return array_map([$this, 'fixTurkishChars'], $text);
        }

        if (!is_string($text)) {
            return $text;
        }

        $map = [
            'Ý' => 'İ',
            'Ð' => 'Ğ',
            'Þ' => 'Ş',
            'ý' => 'ı',
            'ð' => 'ğ',
            'þ' => 'ş',
        ];

        return strtr($text, $map);
    }
}
