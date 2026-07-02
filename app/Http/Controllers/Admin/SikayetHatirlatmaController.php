<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusteriSikayeti;
use App\Models\SikayetHatirlatma;
use App\Models\Setting;
use App\Services\SikayetHatirlatmaService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SikayetHatirlatmaController extends Controller
{
    use AuthorizesRequests;
    protected $service;

    public function __construct(SikayetHatirlatmaService $service)
    {
        $this->service = $service;
    }

    /**
     * Tüm Hatırlatma Talepleri Listesi (Superadmin)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Bölüm Lider Yardımcısı ise ve yetkisi yoksa engelle
        if ($user->isDepartmentDeputy() && !$user->hasBolumAuthority('bolum.hatirlatma.gor')) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $this->authorize('viewAny', SikayetHatirlatma::class);

        $baseQuery = SikayetHatirlatma::query()
            ->whereHas('musteriSikayeti', function($q) {
                $q->whereNotIn('musteri_durum', ['Kapatıldı', 'Çözümlendi']);
            });

        // YETKİ BAZLI FİLTRELEME (rules1.md ve Ek Roller Uyumlu)
        if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
            if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                $baseQuery->whereHas('musteriSikayeti', function($sq) {
                    $sq->where('konum_tipi', 'Yurt İçi');
                });
            } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                $baseQuery->whereHas('musteriSikayeti', function($sq) {
                    $sq->where('konum_tipi', 'Yurt Dışı');
                });
            } else {
                $allowedBolumIds = $user->getAllowedBolumIds();
                
                $baseQuery->where(function($q) use ($user, $allowedBolumIds) {
                    // 1. Bölüm bazlı yetkisi olanlar (Direktör, Lider, Kalite vb.)
                    $q->whereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($allowedBolumIds) {
                        if ($allowedBolumIds !== '*') {
                            $sq->whereIn('bolum_id', $allowedBolumIds);
                        }
                    });

                    // 2. Çözüm Lideri olarak atandığı şikayetler (Spesifik atama)
                    $q->orWhereHas('musteriSikayeti.cozumTakimi', function($sq) use ($user) {
                        $sq->where('lider_user_id', $user->id);
                    });
                });
            }
        }

        $statsData = [
            'toplam' => (clone $baseQuery)->count(),
            'bekleyen' => (clone $baseQuery)->where('durum', 'bilgi_girisi_bekleniyor')->count(),
            'yanitlanan' => (clone $baseQuery)->where('durum', 'bilgi_girildi')->count(),
            'ikna_oldu' => (clone $baseQuery)->where('durum', 'musteri_ikna_oldu')->count(),
            'tekrarlanan' => (clone $baseQuery)->where('hatirlatma_sayisi', '>', 1)->count(),
        ];

        $query = SikayetHatirlatma::with(['musteriSikayeti.customer', 'gonderen'])
            ->withCount('yorumlar');

        // Apply same base query constraints to main query
        $query->mergeConstraintsFrom($baseQuery);

        // Filtreleme
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }
        if ($request->filled('search')) {
            $query->whereHas('musteriSikayeti', function ($q) use ($request) {
                $q->where('musteri_sikayet_konusu', 'like', '%' . $request->search . '%')
                  ->orWhere('musteri_adi', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('tekrarlanan')) {
            $query->where('hatirlatma_sayisi', '>', 1);
        }

        // Sıralama Mantığı
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $allowedSorts = ['customer', 'representative', 'durum', 'hatirlatma_sayisi', 'yorumlar_count', 'created_at'];

        if (in_array($sort, $allowedSorts)) {
            if ($sort === 'customer') {
                $query->join('musteri_sikayetleri', 'sikayet_hatirlatmalari.musteri_sikayeti_id', '=', 'musteri_sikayetleri.id')
                    ->join('customers', 'musteri_sikayetleri.customer_id', '=', 'customers.id')
                    ->orderBy('customers.name', $direction)
                    ->select('sikayet_hatirlatmalari.*');
            } elseif ($sort === 'representative') {
                $query->join('users', 'sikayet_hatirlatmalari.gonderen_user_id', '=', 'users.id')
                    ->orderBy('users.name', $direction)
                    ->select('sikayet_hatirlatmalari.*');
            } elseif ($sort === 'yorumlar_count') {
                $query->orderBy('yorumlar_count', $direction);
            } else {
                $query->orderBy($sort, $direction);
            }
        } else {
            $query->latest();
        }

        $hatirlatmalar = $query->paginate(20)->withQueryString();

        return view('admin.sikayet-hatirlatma.index', compact('hatirlatmalar', 'statsData'));
    }

    /**
     * Hatırlatma Detay Sayfası
     */
    public function show(SikayetHatirlatma $hatirlatma)
    {
        $user = auth()->user();

        // Bölüm Lider Yardımcısı ise ve yetkisi yoksa engelle
        if ($user->isDepartmentDeputy() && !$user->hasBolumAuthority('bolum.hatirlatma.gor')) {
            abort(403, 'Bu sayfayı görüntüleme yetkiniz bulunmamaktadır.');
        }

        // [YENİ] Müşteri Temsilcisi rolü admin URL'si yerine müşteri URL'sini kullanmalı
        if ($user->hasRole('Müşteri Temsilcisi') && !request()->has('force_admin')) {
            return redirect()->route('iaa.hatirlatmalarim.show', $hatirlatma);
        }

        $hatirlatma->load([
            'musteriSikayeti.olusturanKurulUyesi', 
            'musteriSikayeti.customer',
            'gonderen', 
            'yorumlar.user.bolum', // Bölüm bilgisini de yükle
            'bildirilenler.user'
        ]);

        return view('admin.sikayet-hatirlatma.show', compact('hatirlatma'));
    }

    /**
     * Yeni Hatırlatma Gönder (Müşteri Temsilcisi)
     */
    public function gonder(Request $request, MusteriSikayeti $sikayet)
    {
        if (Setting::get('hatirlatma_sistemi_aktif', 1) == 0) {
            return back()->with('error', 'Müşteri hatırlatma sistemi şu an kapalıdır.');
        }

        try {
            $hatirlatma = $this->service->hatirlatmaGonder($sikayet, $request->aciklama);
            return back()
                ->with('success', 'Hatırlatma başarıyla gönderildi ve ilgili birimlere bildirildi.')
                ->with('son_hatirlatma_id', $hatirlatma->id)
                ->with('son_hatirlatma_sikayet_id', $sikayet->id)
                ->with('son_hatirlatma_durum', $hatirlatma->durum);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tartışma Alanına Yorum Ekle
     */
    public function yorumEkle(Request $request, SikayetHatirlatma $hatirlatma)
    {
        $request->validate(['yorum' => 'required|string|max:2000']);

        try {
            $this->service->yorumEkle($hatirlatma, $request->yorum);
            return back()->with('success', 'Yorumunuz eklendi ve bildirim gönderildi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Yorum eklenirken bir hata oluştu.');
        }
    }

    /**
     * Yorum Güncelle
     */
    public function yorumGuncelle(Request $request, \App\Models\SikayetHatirlatmaYorumu $yorum)
    {
        // Yetki: Kendi yorumu mu?
        if ($yorum->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['yorum' => 'required|string|max:2000']);
        
        $yorum->update(['yorum' => $request->yorum]);

        return back()->with('success', 'Yorumunuz güncellendi.');
    }

    /**
     * Yorum Sil
     */
    public function yorumSil(\App\Models\SikayetHatirlatmaYorumu $yorum)
    {
        // Yetki: Kendi yorumu mu veya Superadmin mi?
        if ($yorum->user_id !== auth()->id() && !auth()->user()->hasRole('Superadmin')) {
            abort(403);
        }

        $yorum->delete();

        return back()->with('success', 'Yorum silindi.');
    }

    /**
     * Müşteri İkna Oldu Onayı
     */
    public function iknaOldu(SikayetHatirlatma $hatirlatma)
    {
        $user = auth()->user();
        $isGonderen = $user->id === $hatirlatma->gonderen_user_id;
        $isSuperadmin = $user->hasRole('Superadmin');
        
        $authorizedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
        if ($user->customer_id) {
            $authorizedCustomerIds[] = (int)$user->customer_id;
        }
        $isCustomerOwner = in_array($hatirlatma->musteriSikayeti->customer_id, $authorizedCustomerIds);

        \Log::info('Ikna Oldu Denemesi', [
            'user_id' => $user->id,
            'hatirlatma_id' => $hatirlatma->id,
            'isGonderen' => $isGonderen,
            'isSuperadmin' => $isSuperadmin,
            'isCustomerOwner' => $isCustomerOwner,
            'auth_ids' => $authorizedCustomerIds,
            'target_customer_id' => $hatirlatma->musteriSikayeti->customer_id
        ]);

        // YETKİ KONTROLÜ
        if (!$isGonderen && !$isSuperadmin && !$isCustomerOwner) {
            abort(403, 'Bu işlemi yapmaya yetkiniz bulunmamaktadır.');
        }

        try {
            $this->service->iknaOldu($hatirlatma);
            return back()->with('success', 'Müşterinin ikna olduğu onaylandı.');
        } catch (\Exception $e) {
            \Log::error('Ikna Oldu Hatası: ' . $e->getMessage());
            return back()->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Hatırlatma Ayarları Sayfası (Superadmin)
     */
    public function ayarlar()
    {
        $this->authorize('viewAny', SikayetHatirlatma::class);

        $ayarlar = [
            'aktif' => Setting::get('hatirlatma_sistemi_aktif', 1),
            'cooldown' => Setting::get('hatirlatma_cooldown_saat', 24),
            'ilk_aktif_saat' => Setting::get('hatirlatma_ilk_aktif_saat', 0),
            'sikayeti_giren' => Setting::get('hatirlatma_sikayeti_giren_bildir', 1),
            'cozum_lideri' => Setting::get('hatirlatma_cozum_lideri_bildir', 1),
            'kalite_yoneticisi' => Setting::get('hatirlatma_kalite_yoneticisi_bildir', 1),
            'bolum_lideri' => Setting::get('hatirlatma_bolum_lideri_bildir', 0),
            'direktor' => Setting::get('hatirlatma_direktor_bildir', 0),
            'yonetim' => Setting::get('hatirlatma_yonetim_bildir', 0),
            // Mesaj Şablonları
            'mail_konu' => Setting::get('hatirlatma_mail_konu', 'Müşteri Hatırlatması: {sikayet_konusu}'),
            'mail_govde' => Setting::get('hatirlatma_mail_govde', '{musteri_adi} ({firma_adi}) tarafından {sikayet_konusu} konulu şikayet için hatırlatma gönderilmiştir.'),
        ];

        return view('admin.sikayet-hatirlatma.ayarlar', compact('ayarlar'));
    }

    /**
     * Linkleri Standartlaştır (Helper metot gerekirse diye)
     */
    protected function getBaseUrls()
    {
        return [
            'customer' => '/musteri-profil/',
            'workspace' => '/proje-calisma-alani/',
            'sikayet' => '/sikayetler/'
        ];
    }

    /**
     * Ayarları Kaydet
     */
    public function ayarlariKaydet(Request $request)
    {
        $this->authorize('viewAny', SikayetHatirlatma::class);

        $keys = [
            'hatirlatma_sistemi_aktif',
            'hatirlatma_cooldown_saat',
            'hatirlatma_ilk_aktif_saat',
            'hatirlatma_sikayeti_giren_bildir',
            'hatirlatma_cozum_lideri_bildir',
            'hatirlatma_kalite_yoneticisi_bildir',
            'hatirlatma_bolum_lideri_bildir',
            'hatirlatma_direktor_bildir',
            'hatirlatma_yonetim_bildir',
            'hatirlatma_mail_konu',
            'hatirlatma_mail_govde'
        ];

        $oldCooldown = (float)Setting::get('hatirlatma_cooldown_saat', 24);

        foreach ($keys as $key) {
            $val = $request->input($key);
            // Toggle'lar için null gelirse 0 yap (Sayısal değer içerenleri hariç tut)
            if ((str_contains($key, 'aktif') && !str_contains($key, 'saat')) || str_contains($key, 'bildir')) {
                $val = $request->has($key) ? 1 : 0;
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        // [YENİ] Cooldown ayarı değiştiyse mevcut (aktif) hatırlatma sayaçlarını güncelle
        $newCooldown = (float)$request->input('hatirlatma_cooldown_saat');
        if ($oldCooldown !== $newCooldown) {
            SikayetHatirlatma::whereNotNull('son_hatirlatma_tarihi')
                ->whereNotIn('durum', ['kapatildi', 'musteri_ikna_oldu'])
                ->chunkById(100, function ($hatirlatmalar) use ($newCooldown) {
                    foreach ($hatirlatmalar as $hat) {
                        $hat->update([
                            'sonraki_hak_tarihi' => $hat->son_hatirlatma_tarihi->copy()->addMinutes($newCooldown * 60)
                        ]);
                    }
                });
        }

        return back()->with('success', 'Ayarlar başarıyla güncellendi.');
    }

    /**
     * Müşteri Tarafı: Kendi Hatırlatmalarım Listesi
     */
    public function musteriIndex(Request $request)
    {
        $user = auth()->user();
        $activeCustomerId = session('active_customer_id_' . $user->id);
        
        // Yetkili olduğu tüm firmaları al
        $userCustomers = $user->customers()->get();
        if ($userCustomers->isEmpty() && $user->customer_id) {
            $userCustomers = \App\Models\Customer::where('id', $user->customer_id)->get();
        }
        $authorizedIds = $userCustomers->pluck('id')->toArray();

        // Session'da yoksa veya yetkisi olmayan bir firma kaldıysa ilkini seç
        if (!$activeCustomerId || !in_array($activeCustomerId, $authorizedIds)) {
            $activeCustomerId = !empty($authorizedIds) ? (int)$authorizedIds[0] : null;
            if ($activeCustomerId) {
                session(['active_customer_id_' . $user->id => $activeCustomerId]);
            }
        }

        if (!$activeCustomerId) {
            return redirect()->route('dashboard')->with('error', 'Aktif bir firmaya bağlı değilsiniz.');
        }

        $query = SikayetHatirlatma::with(['musteriSikayeti.sikayetKategori', 'gonderen'])
            ->whereHas('musteriSikayeti', function($q) use ($activeCustomerId) {
                $q->where('customer_id', $activeCustomerId)
                  ->whereNotIn('musteri_durum', ['Kapatıldı', 'Çözümlendi']);
            });

        // Filtreleme (Duruma Göre)
        if ($request->get('filtre') == 'bekleyen') {
            $query->where('durum', 'bilgi_girisi_bekleniyor');
        } elseif ($request->get('filtre') == 'bilgi_girildi') {
            $query->where('durum', 'bilgi_girildi');
        }

        $hatirlatmalar = $query->latest()->paginate(15);

        return view('admin.sikayet-hatirlatma.musteri_index', compact('hatirlatmalar', 'userCustomers', 'activeCustomerId'));
    }

    /**
     * Müşteri Tarafı: Dashboard üzerinden hızlı hatırlatma gönder
     */
    public function dashboardGonder(Request $request, MusteriSikayeti $sikayet)
    {
        $user = auth()->user();
        
        // 1. Güvenlik: Bu şikayet müşterinin yetkili olduğu firmalara mı ait?
        $authorizedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
        
        // Legacy support & Merge: Hem pivot tabloyu hem de eski customer_id sütununu birleştir
        if ($user->customer_id && !in_array((int)$user->customer_id, $authorizedCustomerIds)) {
            $authorizedCustomerIds[] = (int)$user->customer_id;
        }

        if (!in_array($sikayet->customer_id, $authorizedCustomerIds) && !$user->hasAnyRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            abort(403, 'Bu şikayet için hatırlatma gönderme yetkiniz bulunmamaktadır.');
        }

        // 2. Doğrulama
        $request->validate([
            'aciklama' => 'required|string|max:2000'
        ]);

        try {
            // 3. Hatırlatmayı Gönder (Servis üzerinden)
            $service = app(\App\Services\SikayetHatirlatmaService::class);
            $service->hatirlatmaGonder($sikayet, $request->aciklama);

            return back()->with('success', 'Hatırlatma ve açıklamanız başarıyla birimlere iletildi.');
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Müşteri Tarafı: Hatırlatma Detayı
     */
    public function musteriShow(SikayetHatirlatma $hatirlatma)
    {
        $user = auth()->user();
        
        // Güvenlik: Bu hatırlatma müşterinin yetkili olduğu firmalara mı ait?
        $authorizedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
        
        // Legacy support: Eğer pivot tabloda veri yoksa eski customer_id'yi de ekle
        if ($user->customer_id) {
            $authorizedCustomerIds[] = (int)$user->customer_id;
        }

        // [YENİ] İç personel (Temsilci, Kurul, Admin) bu görünümü izleyebilir
        $isInternal = $user->hasAnyRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı', 'Müşteri Temsilcisi']);

        if (!$isInternal && !in_array($hatirlatma->musteriSikayeti->customer_id, $authorizedCustomerIds)) {
            abort(403, 'Bu hatırlatmayı görüntüleme yetkiniz bulunmamaktadır.');
        }

        // Aktif müşteri ID'sini session'da güncelle (Eğer farklıysa)
        $sessionKey = 'active_customer_id_' . $user->id;
        if (session($sessionKey) != $hatirlatma->musteriSikayeti->customer_id) {
            session([$sessionKey => $hatirlatma->musteriSikayeti->customer_id]);
        }

        $hatirlatma->load([
            'musteriSikayeti.sikayetKategori', 
            'gonderen', 
            'yorumlar.user.bolum' // Bölüm bilgisini de yükle
        ]);

        return view('admin.sikayet-hatirlatma.musteri_show', compact('hatirlatma'));
    }
}
