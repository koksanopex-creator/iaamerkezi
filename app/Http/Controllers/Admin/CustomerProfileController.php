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
            'Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi',
            'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi',
            'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'
        ];
        
        $yetkisiVarMi = $user->hasRole($tamYetkiliRoller);
        
        if (!$yetkisiVarMi) {
            // Müşteri temsilcisi kendi profilini (yetkili olduğu firmaları) görebilir
            if ($user->customer_id == $customer->id || $user->customers()->where('customers.id', $customer->id)->exists()) {
                // İzin ver
            }
            // Diğer personel rolleri (Bölüm Lideri, Kalite Yöneticisi vb.) görebilir ama verisi kısıtlanacak
            elseif ($user->is_personnel) {
                // İzin ver
            }
            else {
                abort(403, 'Bu müşteri profilini görüntüleme yetkiniz yok.');
            }
        }

        // =============================================================
        // 2. VERİ SORGUSU VE FİLTRELEME
        // =============================================================
        
        $baseQuery = $customer->sikayetler();

        if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
            if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi']) && !$user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                $baseQuery->where('konum_tipi', 'Yurt İçi');
            } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']) && !$user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                $baseQuery->where('konum_tipi', 'Yurt Dışı');
            }
        }

        if ($request->has('start_date') || $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $baseQuery->where(function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->where(function ($q) use ($startDate) {
                        $q->whereDate('created_at', '>=', $startDate)
                          ->orWhereDate('updated_at', '>=', $startDate);
                    });
                }
                if ($endDate) {
                    $query->where(function ($q) use ($endDate) {
                        $q->whereDate('created_at', '<=', $endDate)
                          ->orWhereDate('updated_at', '<=', $endDate);
                    });
                }
            });
        }

        // Yetkisi kısıtlı roller (Bölüm Lideri, Direktör, Kalite vb. ve Müşteri)
        if (!$yetkisiVarMi) {
            if ($user->customer_id == $customer->id || $user->customers()->where('customers.id', $customer->id)->exists()) {
                // Müşteri sadece yetkili olduğu firmanın şikayetlerini görür (baseQuery zaten $customer->sikayetler() olduğu için ek kısıtlamaya gerek yok)
            }
            else {
                // PERSONEL VERİ KISITLAMASI (Bölüm Lideri, Direktör vb.)
                $allowedBolumIds = $user->getAllowedBolumIds(); 

                $baseQuery->where(function($q) use ($user, $allowedBolumIds) {
                    // Kriter 1: Kendi bölümüne ait şikayetler
                    if (is_array($allowedBolumIds) && count($allowedBolumIds) > 0) {
                        $q->whereHas('sikayetKategori', function($k) use ($allowedBolumIds) {
                            $k->whereIn('bolum_id', $allowedBolumIds);
                        });
                    }

                    // Kriter 2: Kendi bölümünden bir personelin dahil olduğu işler (Takım veya Proje)
                    if ($user->bolum_id && !$user->hasRole('Müşteri Saha Temsilcisi')) {
                        $q->orWhereHas('cozumTakimi.uyeler', function($u) use ($user) {
                            $u->where('users.bolum_id', $user->bolum_id);
                        })
                        ->orWhereHas('iaa.projeEkibi', function($u) use ($user) {
                            $u->where('users.bolum_id', $user->bolum_id);
                        });
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
            ->whereHas('iaaProjesi', function($q) {
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

        // LOGLAR
        $logs = null;
        if ($user->hasRole(['Superadmin', 'Super Admin', 'Yonetim'])) {
            $logs = \App\Models\MusteriLog::with('user')
                ->where('customer_id', $customer->id)
                ->latest()
                ->limit(20)
                ->get();
        }

        // =============================================================
        // 5. İADE VE MALİYET ANALİZİ (GELİŞMİŞ - YIL VE BÖLÜM BAZLI)
        // =============================================================
        
        // 1. Yıl Filtresi (Request'ten gelirse al)
        $secilenYil = $request->input('yil');

        // 2. Temel Sorgu: Bu müşteriye ait iadeleri bul
        $iadeQuery = \App\Models\SikayetIadesi::whereHas('sikayet', function($q) use ($customer, $user) {
            $q->where('customer_id', $customer->id);
            if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
                if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi']) && !$user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                    $q->where('konum_tipi', 'Yurt İçi');
                } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']) && !$user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                    $q->where('konum_tipi', 'Yurt Dışı');
                }
            }
        })
        ->with(['sikayet.sikayetKategori.bolum']); // Bölüm adını almak için ilişki zinciri

        // 3. Filtre Uygula (Eğer yıl seçildiyse)
        if ($secilenYil) {
            $iadeQuery->whereYear('created_at', $secilenYil);
        }

        $iadeler = $iadeQuery->latest()->get();

        // 4. A) Genel Birim Toplamları (Örn: Ton => 1500, Adet => 5000)
        $iadeToplamlari = $iadeler->groupBy('birim')->map(function ($row) {
            return $row->sum('miktar');
        });

        // 5. B) Bölüm Bazlı Kırılım Hesaplama
        // Yapı: ['Ton' => ['Preform' => 1000, 'Kapak' => 500], 'Adet' => [...]]
        $bolumKirilimi = [];
        foreach ($iadeler as $iade) {
            $birim = $iade->birim;
            // İlişki zincirinden bölüm adını al, yoksa 'Diğer' de
            $bolumAdi = $iade->sikayet->sikayetKategori->bolum->ad ?? 'Diğer';
            
            if (!isset($bolumKirilimi[$birim][$bolumAdi])) {
                $bolumKirilimi[$birim][$bolumAdi] = 0;
            }
            $bolumKirilimi[$birim][$bolumAdi] += $iade->miktar;
        }

        // 6. Filtre İçin Mevcut Yılları Listele (Sadece bu müşterinin yılları)
        $mevcutYillar = \App\Models\SikayetIadesi::whereHas('sikayet', function($q) use ($customer, $user) {
                $q->where('customer_id', $customer->id);
                if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
                    if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi']) && !$user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                        $q->where('konum_tipi', 'Yurt İçi');
                    } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']) && !$user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                        $q->where('konum_tipi', 'Yurt Dışı');
                    }
                }
            })
            ->selectRaw('YEAR(created_at) as yil')
            ->distinct()
            ->orderBy('yil', 'desc')
            ->pluck('yil');

        // =============================================================

        return view('admin.musteriler.musteri-profile', compact(
            'customer', 'temsilciler', 'sikayetler',
            'toplamSikayet', 'aktifSikayet', 'tamamlananProje', 'ortalamaSure', 'logs',
            'iadeler', 'iadeToplamlari', 'bolumKirilimi', 'mevcutYillar', 'secilenYil' // <--- Yeni değişkenler eklendi
        ));

        // View'a 'iadeler' ve 'iadeToplamlari' değişkenlerini de gönderiyoruz
        return view('admin.musteriler.musteri-profile', compact(
            'customer', 'temsilciler', 'sikayetler',
            'toplamSikayet', 'aktifSikayet', 'tamamlananProje', 'ortalamaSure', 'logs',
            'iadeler', 'iadeToplamlari' // <--- EKLENDİ
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

        // SSO Senkronizasyonu
        try {
            app(\App\Services\CentralSsoSyncService::class)->syncUser($user, $password, 'customer');
        } catch (\Exception $ssoEx) {
            \Illuminate\Support\Facades\Log::error('Central SSO Sync failed for new customer representative: ' . $ssoEx->getMessage());
        }

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