<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusteriSikayeti;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Log;
use App\Models\MusteriSikayetiDosyasi; 
use App\Models\SikayetKategori;
use Illuminate\Support\Facades\Auth;
use App\Traits\ComplaintNotificationTrait; // <-- Add this line
use Illuminate\Support\Facades\Validator;


class SikayetController extends Controller
{
    use AuthorizesRequests;
    use ComplaintNotificationTrait; // <-- Add this line

    public function index()
    {
        $this->authorize('viewAny', MusteriSikayeti::class);
        return view('admin.sikayetler.index');
    }

    public function create()
    {
        $this->authorize('create', MusteriSikayeti::class);
        // Kategorileri çek ve view'e gönder
        $kategoriler = SikayetKategori::orderBy('ad')->get();
        return view('admin.sikayetler.create', compact('kategoriler')); 
    }

    public function store(Request $request)
    {
        $this->authorize('create', MusteriSikayeti::class);

        // === 1. TEMİZLİK ===
        // Boş stringleri NULL yapıyoruz ki veritabanı kızmasın
        $request->merge([
            'yetkili_user_id' => $request->yetkili_user_id ?: null,
            'sikayet_alt_kategori_id' => ($request->sikayet_alt_kategori_id === 'other' || !$request->sikayet_alt_kategori_id) ? null : $request->sikayet_alt_kategori_id,
        ]);

        // === 2. VALIDASYON ===
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'yetkili_user_id' => 'nullable|integer|exists:users,id',
            'musteri_adi' => 'nullable|string|max:255',
            'musteri_iletisim' => 'nullable|string|max:255',
            'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı',
            'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id',
            'sikayet_alt_kategori_id' => 'nullable|integer',
            'sikayet_alt_kategori_diger' => 'nullable|string|max:500',
            'musteri_oncelik' => 'required|string|in:Düşük,Normal,Yüksek,Acil',
            'musteri_sikayet_konusu' => 'required|string|max:255',
            'musteri_sikayet_detayi' => 'required|string',
            'musteri_sikayet_tarihi' => 'required|date',
            'dosyalar.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4|max:10240',
        ]);

        $user = auth()->user();
        $kazanilacakPuan = 0;

        if (!$user->hasRole('Superadmin')) {
            $kazanilacakPuan = (int)(\App\Models\Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0);
        }

        DB::beginTransaction();
        try {
            $sikayetData = $validated;

            // === 3. OTOMATİK VERİ DOLDURMA ===
            if ($request->customer_id) {
                $customer = \App\Models\Customer::find($request->customer_id);
                $sikayetData['musteri_adi'] = $customer->name;
                
                if ($request->yetkili_user_id) {
                    $yetkili = \App\Models\User::find($request->yetkili_user_id);
                    $sikayetData['musteri_iletisim'] = $yetkili->name . ' (' . $yetkili->email . ')';
                } else {
                    $sikayetData['musteri_iletisim'] = $customer->phone ?? 'Belirtilmedi';
                }
            }

            if ($request->sikayet_alt_kategori_id === null && $request->sikayet_alt_kategori_diger) {
                $sikayetData['sikayet_alt_kategori_id'] = null;
            } else {
                $sikayetData['sikayet_alt_kategori_diger'] = null;
            }

            $sikayetData['olusturan_kurul_uyesi_id'] = $user->id;
            $sikayetData['musteri_durum'] = 'Yeni';
            $sikayetData['kazanilan_puan'] = $kazanilacakPuan;

            $sikayet = MusteriSikayeti::create($sikayetData);

            if ($kazanilacakPuan > 0) {
                $user->increment('toplam_puan', $kazanilacakPuan);
            }

            if ($request->hasFile('dosyalar')) {
                foreach ($request->file('dosyalar') as $dosya) {
                    $path = $dosya->store('sikayet_dosyalari', 'public');
                    if ($path) {
                        $sikayet->dosyalar()->create([
                            'dosya_yolu' => $path,
                            'orijinal_adi' => $dosya->getClientOriginalName(),
                            'mime_tipi' => $dosya->getMimeType(),
                        ]);
                    }
                }
            }

            DB::commit();
            
            if (method_exists($this, 'sendNewComplaintNotification')) {
                $this->sendNewComplaintNotification($sikayet);
            }

            return redirect()->route('admin.sikayetler.index')->with('success', 'Şikayet başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Şikayet hatası: ' . $e->getMessage());
            return back()->with('error', 'Hata: ' . $e->getMessage())->withInput();
        }
    }

    public function show(MusteriSikayeti $sikayet)
    {
        $user = Auth::user();
        
        // === YENİ HİBRİT YETKİ KONTROLÜ ===
        // Standart Policy ($this->authorize) yerine manuel kontrol yapıyoruz
        // çünkü Policy dosyası squad mantığını henüz bilmiyor olabilir.
        
        $yetkiVar = false;

        // 1. Admin veya Kurul ise girer
        if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Bölüm Kalite Yöneticisi', 'Yonetim'])) {
            $yetkiVar = true;
        }
        // 2. [YENİ] BÖLÜM LİDERİ KONTROLÜ (Emrah Al buraya takılacak)
        elseif ($user->hasRole('Bölüm Lideri')) {
            // Şikayetin kategorisi var mı ve bu kategori kullanıcının bölümüne mi ait?
            if ($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum_id == $user->bolum_id) {
                $yetkiVar = true;
            }
            // VEYA: Kullanıcı "Bölüm Kalite Yöneticisi" ise ve kategorisi uyuyorsa
            elseif ($user->hasRole('Bölüm Kalite Yöneticisi') && $user->yonettigiSikayetKategorileri->contains($sikayet->sikayet_kategorisi_id)) {
                $yetkiVar = true;
            }
        }
        // 3. Atanan Takımın Üyesi ise girer
        elseif ($sikayet->atanan_cozum_takimi_id && $user->takimlar->contains($sikayet->atanan_cozum_takimi_id)) {
            $yetkiVar = true;
        }
        // 4. İAA Projesi Varsa ve Squad Üyesi ise girer (CİHANGİR BURADAN GİRECEK)
        elseif ($sikayet->iaa_id) {
             $iaa = \App\Models\Iaa::find($sikayet->iaa_id);
             if ($iaa && $iaa->projeEkibi()->where('user_id', $user->id)->wherePivot('durum', 'onaylandi')->exists()) {
                 $yetkiVar = true;
             }
        }

        if (!$yetkiVar) {
            abort(403, 'Bu şikayeti görüntüleme yetkiniz yok. (Bölüm eşleşmesi veya yetki bulunamadı)');
        }
        // === KONTROL SONU ===

        // İlişkileri yükle
        $sikayet->load('cozumTakimi', 'olusturanKurulUyesi', 'dosyalar', 'sikayetKategori');
        
        return view('admin.sikayetler.show', compact('sikayet'));
    }

    public function edit(MusteriSikayeti $sikayet)
    {
        $this->authorize('update', $sikayet);
        $sikayet->load('dosyalar');
        // Kategorileri çek ve view'e gönder
        $kategoriler = SikayetKategori::orderBy('ad')->get();
        return view('admin.sikayetler.edit', compact('sikayet', 'kategoriler')); // <-- 'kategoriler' eklendi
    }

    /**
     * === UPDATE METODU - GÜNCELLENDİ ===
     * Bu metod artık 'dosyalar_sil' adında bir diziyi de işleyebiliyor.
     */
    public function update(Request $request, MusteriSikayeti $sikayet)
    {
        $this->authorize('update', $sikayet);

        // === KRİTİK TEMİZLİK: Boş gelen ID'leri NULL yap ===
        // Bu sayede "seçiniz" değeri (boş string) veritabanına NULL olarak gider
        // ve validasyondaki 'integer' kuralına takılmaz.
        $request->merge([
            'customer_id' => $request->customer_id ?: null,
            'yetkili_user_id' => $request->yetkili_user_id ?: null,
            'sikayet_alt_kategori_id' => ($request->sikayet_alt_kategori_id === 'other' || !$request->sikayet_alt_kategori_id) ? null : $request->sikayet_alt_kategori_id,
        ]);

        // Validasyon Kuralları
        $validated = $request->validate([
            'customer_id' => 'nullable|integer|exists:customers,id',
            'yetkili_user_id' => 'nullable|integer|exists:users,id',
            
            // ESKİ KAYITLAR İÇİN: customer_id yoksa musteri_adi zorunlu
            'musteri_adi' => 'required_without:customer_id|nullable|string|max:255',
            'musteri_iletisim' => 'nullable|string|max:255',
            'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı',
            'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id',
            'sikayet_alt_kategori_id' => 'nullable', 
            'sikayet_alt_kategori_diger' => 'nullable|string|max:500',
            'musteri_oncelik' => 'required|string|in:Düşük,Normal,Yüksek,Acil',
            'musteri_sikayet_konusu' => 'required|string|max:255',
            'musteri_sikayet_detayi' => 'required|string',
            'musteri_sikayet_tarihi' => 'required|date',
            'dosyalar.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4|max:10240',
            'dosyalar_sil' => 'nullable|array',
            'dosyalar_sil.*' => 'integer|exists:musteri_sikayeti_dosyalari,id'
        ]);

        DB::beginTransaction();
        try {
            // Validate edilmiş veriyi al, dosya işlemlerini (dizi oldukları için) çıkar
            $updateData = collect($validated)->except(['dosyalar', 'dosyalar_sil'])->toArray();

            // ============================================================
            // === BURAYA EKLİYORUZ: OTOMATİK İSİM DOLDURMA MANTIĞI ===
            // ============================================================
            if ($request->customer_id) {
                $customer = \App\Models\Customer::find($request->customer_id);
                
                // Müşteri adını ve iletişim bilgisini otomatik doldur (Veritabanında boş kalmasın)
                $updateData['musteri_adi'] = $customer->name;
                
                // Eğer iletişim bilgisi boş gelmişse, firmanınkini yaz
                if(empty($updateData['musteri_iletisim'])) {
                    $updateData['musteri_iletisim'] = $customer->phone ?? $customer->email;
                }
           }
           // ============================================================

            // --- DÜZELTİLMİŞ VERİ HAZIRLAMA ---
            // Alt Kategori Mantığı
            if ($request->sikayet_alt_kategori_id === 'other') {
                // "Diğer" seçildiyse ID'yi temizle, açıklamayı ekle
                $updateData['sikayet_alt_kategori_id'] = null;
                $updateData['sikayet_alt_kategori_diger'] = $request->sikayet_alt_kategori_diger;
            } else {
                // Normal seçim yapıldıysa ID'yi al, açıklamayı temizle
                $updateData['sikayet_alt_kategori_id'] = $request->sikayet_alt_kategori_id ?: null;
                $updateData['sikayet_alt_kategori_diger'] = null;
            }

            // Veritabanını Güncelle
            $sikayet->update($updateData);

            // Silinmek istenen dosyaları sil
            if ($request->has('dosyalar_sil')) {
                $dosyalar = MusteriSikayetiDosyasi::where('musteri_sikayeti_id', $sikayet->id)
                    ->whereIn('id', $request->input('dosyalar_sil'))->get();
                
                foreach ($dosyalar as $dosya) {
                    Storage::disk('public')->delete($dosya->dosya_yolu);
                    $dosya->delete();
                }
            }

            // Yeni dosyaları ekle
            if ($request->hasFile('dosyalar')) {
                foreach ($request->file('dosyalar') as $dosya) {
                    $path = $dosya->store('sikayet_dosyalari', 'public');
                    if ($path) {
                        $sikayet->dosyalar()->create([
                            'dosya_yolu' => $path,
                            'orijinal_adi' => $dosya->getClientOriginalName(),
                            'mime_tipi' => $dosya->getMimeType(),
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.sikayetler.show', $sikayet)->with('success', 'Şikayet başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Şikayet güncelleme hatası: ' . $e->getMessage());
            return back()->with('error', 'Şikayet güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function destroy(MusteriSikayeti $sikayet)
    {
        $this->authorize('delete', $sikayet);

        $puan = $sikayet->kazanilan_puan;
        $olusturanId = $sikayet->olusturan_kurul_uyesi_id;

        DB::beginTransaction(); 
        try {
            if ($puan > 0 && $olusturanId) {
                User::where('id', $olusturanId)->decrement('toplam_puan', $puan);
            }

            foreach ($sikayet->dosyalar as $dosya) {
                Storage::disk('public')->delete($dosya->dosya_yolu);
            }
            $sikayet->delete();

            DB::commit();
            return redirect()->route('admin.sikayetler.index')->with('success', 'Şikayet ve ilişkili dosyaları başarıyla silindi, kazanılan puan geri alındı.');

        } catch (\Exception $e) {
             DB::rollBack();
             Log::error('Şikayet silinirken hata: ' . $e->getMessage());
             return back()->with('error', 'Şikayet silinirken bir hata oluştu.');
        }
    }

    // === 2. YENİ METODU BURAYA EKLEYİN ===
    /**
     * Sadece Kurul üyelerinin girdiği şikayetleri filtreleyerek gösterir.
     */
    public function kurulGirdileri(Request $request)
    {
        $girisYapanKullanici = Auth::user();

        // Yetkilendirme
        if (!$girisYapanKullanici->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu'])) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        $kurulUyeleri = User::role('Müşteri Şikayeti Kurulu')->orderBy('name')->get();

        // Filtrelemeyi ayarla
        $selectedUserId = $request->input('kullanici_id');
        if (is_null($selectedUserId)) {
            $selectedUserId = $girisYapanKullanici->hasRole('Superadmin') ? 'all' : $girisYapanKullanici->id;
        }

        // === 1. KİŞİSEL İSTATİSTİKLER (HER ZAMAN GİRİŞ YAPAN KULLANICIYI HESAPLAR) ===
        $kisisel_query = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $girisYapanKullanici->id);
        $stats_kisisel = [
            'toplam_benim_girdiklerim' => (clone $kisisel_query)->count(),
            'islemde_benim_girdiklerim' => (clone $kisisel_query)->where('musteri_durum', 'İşlemde')->count(),
            'cozulen_benim_girdiklerim' => (clone $kisisel_query)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
        ];
        // === KİŞİSEL İSTATİSTİK SONU ===


        // === 2. FİLTRELENMİŞ İSTATİSTİKLER (FİLTREYE GÖRE DEĞİŞİR) ===
        $filteredQuery = MusteriSikayeti::query();

        if ($selectedUserId == 'all') {
            $filteredQuery->whereIn('olusturan_kurul_uyesi_id', $kurulUyeleri->pluck('id'));
        } else {
            $filteredQuery->where('olusturan_kurul_uyesi_id', $selectedUserId);
        }
        
        $stats_filtrelenmis = [
            'toplam' => (clone $filteredQuery)->count(),
            'islemde' => (clone $filteredQuery)->where('musteri_durum', 'İşlemde')->count(),
            'cozulen' => (clone $filteredQuery)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(),
            'kategoriler' => (clone $filteredQuery)
                                ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                                ->select('sikayet_kategorileri.ad', DB::raw('count(musteri_sikayetleri.id) as toplam'))
                                ->groupBy('sikayet_kategorileri.ad')
                                ->orderBy('toplam', 'desc')
                                ->get()
        ];
        // === FİLTRELENMİŞ İSTATİSTİK SONU ===
        

        // Ana veriyi al ve view'e gönder
        $sikayetler = $filteredQuery->with('olusturanKurulUyesi', 'cozumTakimi', 'sikayetKategori')
                                  ->latest()
                                  ->paginate(15)
                                  ->withQueryString();

        return view('admin.sikayetler.kurul', compact(
            'sikayetler', 
            'kurulUyeleri', 
            'selectedUserId',
            'stats_filtrelenmis',
            'stats_kisisel' 
        ));
    }
    // === YENİ METOD SONU ===

    /**
     * === BU FONKSİYON SİLİNDİ ===
     * Anında silme (AJAX) fonksiyonu olan 'destroyDosya'
     * sizin istediğiniz "kaydet'e basana kadar bekle" mantığına
     * uymadığı için TAMAMEN KALDIRILDI.
     */
    // public function destroyDosya(...) { ... }
}