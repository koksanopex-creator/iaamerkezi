<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusteriSikayeti;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // <-- BU SATIRIN EKLENDİĞİNDEN EMİN OLUN
use Illuminate\Support\Facades\Log;
use App\Models\MusteriSikayetiDosyasi; // <-- BU SATIRIN EKLENDİĞİNDEN EMİN OLUN
use App\Models\SikayetKategori;
use Illuminate\Support\Facades\Auth; // <-- 1. BU SATIRI EKLEYİN


class SikayetController extends Controller
{
    use AuthorizesRequests;

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
        return view('admin.sikayetler.create', compact('kategoriler')); // <-- 'kategoriler' eklendi
    }

    public function store(Request $request)
    {
        $this->authorize('create', MusteriSikayeti::class);

        // Validasyon Kuralları
        $validated = $request->validate([
            'musteri_adi' => 'required|string|max:255',
            'musteri_iletisim' => 'nullable|string|max:255',
            'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı',
            'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id',
            // Alt Kategori: "other" metni, boş veya ID gelebilir, o yüzden 'nullable' diyoruz.
            'sikayet_alt_kategori_id' => 'nullable', 
            'sikayet_alt_kategori_diger' => 'nullable|string|max:500',
            'musteri_oncelik' => 'required|string|in:Düşük,Normal,Yüksek,Acil',
            'musteri_sikayet_konusu' => 'required|string|max:255',
            'musteri_sikayet_detayi' => 'required|string',
            'musteri_sikayet_tarihi' => 'required|date',
            'dosyalar.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4|max:10240',
        ]);

        $user = auth()->user();
        $kazanilacakPuan = 0;

        // Puan hesaplama (Superadmin değilse)
        if (!$user->hasRole('Superadmin')) {
            $kazanilacakPuan = (int)(Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0);
        }

        DB::beginTransaction();
        try {
            // --- DÜZELTİLMİŞ VERİ HAZIRLAMA ---
            $sikayetData = $validated;

            // Alt Kategori Mantığı
            if ($request->sikayet_alt_kategori_id === 'other') {
                // Kullanıcı "Diğer" seçtiyse ID null olmalı, açıklama kaydedilmeli
                $sikayetData['sikayet_alt_kategori_id'] = null;
                $sikayetData['sikayet_alt_kategori_diger'] = $request->sikayet_alt_kategori_diger;
            } else {
                // Kullanıcı listeden bir şey seçtiyse (veya boşsa)
                // Gelen değer boş string "" ise null yap, değilse ID'yi al
                $sikayetData['sikayet_alt_kategori_id'] = $request->sikayet_alt_kategori_id ?: null;
                $sikayetData['sikayet_alt_kategori_diger'] = null;
            }

            // Diğer otomatik alanlar
            $sikayetData['olusturan_kurul_uyesi_id'] = $user->id;
            $sikayetData['musteri_durum'] = 'Yeni';
            $sikayetData['kazanilan_puan'] = $kazanilacakPuan;

            // Veritabanına Kayıt
            $sikayet = MusteriSikayeti::create($sikayetData);

            // Puan Ekleme
            if ($kazanilacakPuan > 0) {
                $user->increment('toplam_puan', $kazanilacakPuan);
            }

            // Dosya Yükleme
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
            return redirect()->route('admin.sikayetler.index')->with('success', 'Şikayet başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Şikayet oluşturma hatası: ' . $e->getMessage());
            return back()->with('error', 'Şikayet kaydedilirken bir hata oluştu: ' . $e->getMessage())->withInput();
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
        if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Bölüm Kalite Yöneticisi'])) {
            $yetkiVar = true;
        }
        // 2. Atanan Takımın Üyesi ise girer
        elseif ($sikayet->atanan_cozum_takimi_id && $user->takimlar->contains($sikayet->atanan_cozum_takimi_id)) {
            $yetkiVar = true;
        }
        // 3. İAA Projesi Varsa ve Squad Üyesi ise girer (CİHANGİR BURADAN GİRECEK)
        elseif ($sikayet->iaa_id) {
             $iaa = \App\Models\Iaa::find($sikayet->iaa_id);
             if ($iaa && $iaa->projeEkibi()->where('user_id', $user->id)->wherePivot('durum', 'onaylandi')->exists()) {
                 $yetkiVar = true;
             }
        }

        if (!$yetkiVar) {
            abort(403, 'Bu şikayeti görüntüleme yetkiniz yok.');
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

        // Validasyon Kuralları
        $validated = $request->validate([
            'musteri_adi' => 'required|string|max:255',
            'musteri_iletisim' => 'nullable|string|max:255',
            'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı',
            'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id',
            // Alt Kategori
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
                                  ->latest() // En yeniler üste gelsin
                                  ->paginate(15)
                                  ->withQueryString();

        return view('admin.sikayetler.kurul', compact(
            'sikayetler', 
            'kurulUyeleri', 
            'selectedUserId',
            'stats_filtrelenmis', // Filtrelenmiş istatistikler
            'stats_kisisel'     // Kişisel istatistikler
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