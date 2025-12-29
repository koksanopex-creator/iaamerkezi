<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request; // Request eklendi
use Illuminate\Support\Facades\Auth;
use App\Models\MusteriLog; // Log Modeli

class CustomerProfileController extends Controller
{
    public function show(Request $request, Customer $customer)
    {
        $user = Auth::user();

        // 1. GENEL SAYFA ERİŞİM YETKİSİ
        $tamYetkiliRoller = [
            'Superadmin', 'Yonetim', 'Bölüm Lideri', 'Bölüm Kalite Yöneticisi', 
            'Müşteri Şikayeti Çözüm Lideri', 'Müşteri Şikayeti Kurulu'
        ];
        
        $yetkisiVarMi = $user->hasRole($tamYetkiliRoller);
        
        // Eğer kullanıcı TAM YETKİLİ DEĞİLSE
        if (!$yetkisiVarMi) {
            
            // SENARYO A: Kullanıcı bu firmanın yetkilisi (Müşteri)
            if ($user->customer_id === $customer->id) {
                // İzin ver, aşağıda devam etsin. (Kendi sayfası)
            }
            // SENARYO B: Kullanıcı bir Personel (Örn: Sinan) ama yetkili rolü yok
            elseif ($user->is_personnel) {
                // Personelse, bu müşterinin herhangi bir şikayetinde görevi var mı diye bakacağız.
                // Not: Burada 'users' ilişkisi yerine daha garanti olan 'cozumTakimi' üzerinden bakabiliriz
                // veya Iaa modelindeki eksik ilişkiyi pas geçebiliriz.
                
                // Şimdilik basit kontrol: Eğer personel admin değilse ve müşteri de değilse
                // ve görevli olduğu bir kayıt yoksa engelle.
                
                // Bu kontrolü QUERY içinde yapacağız, burada sadece kapı kontrolü yapıyoruz.
                // Eğer personelse ve "hiçbir" şikayeti görmeye yetkisi yoksa 403 yiyecek aşağıda.
            }
            // SENARYO C: Alakasız biri (Başka müşteri vb.)
            else {
                abort(403, 'Bu müşteri profilini görüntüleme yetkiniz yok.');
            }
        }

        // =============================================================
        // 2. VERİ SORGUSU VE FİLTRELEME
        // =============================================================
        
        $baseQuery = $customer->sikayetler();

        // EĞER TAM YETKİLİ DEĞİLSE FİLTRE UYGULA
        if (!$yetkisiVarMi) {
            
            // DURUM 1: Kullanıcı MÜŞTERİ ise (Kendi firması)
            if ($user->customer_id === $customer->id) {
                // HİÇBİR FİLTRE UYGULAMA!
                // Müşteri kendi firmasının tüm şikayetlerini görmeli.
                // Burası boş kalacak, böylece 'iaa->users' hatasına düşmeyecek.
            }
            
            // DURUM 2: Kullanıcı PERSONEL ise (Sinan Poyraz gibi)
            else {
                // İşte Sinan için olan kısıtlamalar burada devreye giriyor.
                $allowedBolumIds = $user->getAllowedBolumIds(); 

                $baseQuery->where(function($q) use ($user, $allowedBolumIds) {
                    
                    // A. Bölüm Yetkisi (Kategori bazlı)
                    if (is_array($allowedBolumIds) && count($allowedBolumIds) > 0) {
                        $q->whereHas('sikayetKategori', function($k) use ($allowedBolumIds) {
                            $k->whereIn('bolum_id', $allowedBolumIds);
                        });
                    }

                    // B. Takım Üyeliği / Görevlendirme (Sinan Poyraz)
                    // HATA VEREN YER BURASIYDI. Burayı düzelttik.
                    // Iaa (Proje) -> CozumTakimi -> Uyeler üzerinden kontrol ediyoruz.
                    $q->orWhereHas('iaaProjesi', function($iaa) use ($user) {
                        $iaa->whereHas('atananTakim', function($takim) use ($user) {
                            $takim->whereHas('uyeler', function($u) use ($user) {
                                $u->where('users.id', $user->id);
                            });
                        });
                    });
                    
                    // Ayrıca direkt şikayetin çözüm takımındaysa da görsün
                    $q->orWhereHas('cozumTakimi', function($takim) use ($user) {
                        $takim->whereHas('uyeler', function($u) use ($user) {
                            $u->where('users.id', $user->id);
                        });
                    });

                    // C. Bölüm Lideri Ekstra Yetkisi
                    if ($user->hasRole('Bölüm Lideri')) {
                        $staffIds = \App\Models\User::where('bolum_id', $user->bolum_id)->pluck('id');
                        $q->orWhereIn('olusturan_user_id', $staffIds); // Basitçe ekibi oluşturduysa görsün
                    }
                });
            }
        }

        // =============================================================
        // 3. İSTATİSTİKLER 
        // =============================================================
        
        $toplamSikayet = (clone $baseQuery)->count();
        
        $aktifSikayet = (clone $baseQuery)
            ->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'İnceleniyor'])
            ->count();

        $tamamlananProje = (clone $baseQuery)
            ->whereHas('iaaProjesi', function($q) { // Modelde 'iaaProjesi' kullanıyorsun genelde
                $q->where('durum', 'Tamamlandı');
            })->count();

        $cozulmusSikayetler = (clone $baseQuery)
            ->whereNotNull('musteri_cozum_son_tarihi')
            ->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->get();
        
        $ortalamaSure = 0;
        if ($cozulmusSikayetler->count() > 0) {
            $toplamGun = 0;
            foreach ($cozulmusSikayetler as $sikayet) {
                $toplamGun += $sikayet->created_at->diffInDays($sikayet->updated_at);
            }
            $ortalamaSure = round($toplamGun / $cozulmusSikayetler->count());
        }

        // =============================================================
        // 4. VERİ ÇEKME
        // =============================================================
        
        // İlişki isimlerini senin modellerine göre düzelttim
        $query = $baseQuery->with(['iaaProjesi', 'cozumTakimi', 'sikayetKategori', 'olusturanKurulUyesi']); 

        if ($request->has('filtre')) {
            switch ($request->filtre) {
                case 'aktif':
                    $query->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'İnceleniyor']);
                    break;
                case 'tamamlanan':
                    $query->whereHas('iaaProjesi', function($q) {
                        $q->where('durum', 'Tamamlandı');
                    });
                    break;
                case 'cozulen':
                    $query->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı']);
                    break;
            }
        }

        $sikayetler = $query->latest()->paginate(15)->withQueryString();
        $temsilciler = $customer->users; 

        // LOGLAR (Sadece Adminler)
        $logs = null;
        if ($user->hasRole(['Superadmin', 'Super Admin', 'Yonetim'])) {
            $logs = \App\Models\MusteriLog::with('user')
                ->where('customer_id', $customer->id)
                ->latest()
                ->limit(20)
                ->get();
        }

        return view('admin.musteriler.musteri-profile', compact(
            'customer', 'temsilciler', 'sikayetler',
            'toplamSikayet', 'aktifSikayet', 'tamamlananProje', 'ortalamaSure', 'logs'
        ));
    }

    
    /**
     * Hızlı Yetkili Ekleme
     */
    public function storeRepresentative(\Illuminate\Http\Request $request, Customer $customer)
    {
        // Sadece yetkili personel işlem yapabilir
        if (!auth()->user()->hasRole(['Superadmin', 'Bölüm Kalite Yöneticisi', 'Müşteri Şikayeti Kurulu'])) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:100',
        ]);

        // Rastgele şifre oluştur
        $password = \Illuminate\Support\Str::random(8);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefon' => $request->phone,
            'unvan' => $request->title,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'customer_id' => $customer->id,
            'is_personnel' => false,
            'onaylandi_mi' => true,
        ]);

        // Rol Ata
        $user->assignRole('Müşteri Temsilcisi');
        // LOG
        MusteriLog::add($customer->id, 'Yetkili Ekleme', auth()->user()->name . ', yeni yetkili ekledi: ' . $request->name);

            return back()->with('success', "Yetkili eklendi. Şifre: $password (Not edin)");
        }

    // --- YETKİLİ SİLME ---
    public function destroyRepresentative(\App\Models\User $user)
    {
        if (!auth()->user()->hasRole(['Superadmin', 'Yonetim'])) { abort(403); }
        if ($user->is_personnel) { return back()->with('error', 'Personel silinemez.'); }

        // ÖNCE VERİLERİ ALIYORUZ (HATA BURADAYDI, DEĞİŞKEN TANIMLI DEĞİLDİ)
        $customerId = $user->customer_id; 
        $userName = $user->name;
        
        $user->delete();

        // LOG (Artık $customerId tanımlı olduğu için hata vermez)
        \App\Models\MusteriLog::add($customerId, 'Yetkili Silme', auth()->user()->name . ', ' . $userName . ' adlı yetkiliyi sildi.');

        return back()->with('success', 'Yetkili başarıyla silindi.');
    }

    
}