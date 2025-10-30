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

        $validated = $request->validate([
            'musteri_adi' => 'required|string|max:255',
            'musteri_iletisim' => 'nullable|string|max:255',
            'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı', // <-- YENİ: Konum Tipi validasyonu
            'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id', // <-- YENİ: Kategori validasyonu
            'musteri_oncelik' => 'required|string|in:Düşük,Normal,Yüksek,Acil',
            'musteri_sikayet_konusu' => 'required|string|max:255',
            'musteri_sikayet_detayi' => 'required|string',
            'musteri_sikayet_tarihi' => 'required|date',
            'dosyalar.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,mp4|max:10240',
        ]);

        $user = auth()->user();
        $kazanilacakPuan = 0;

        if (!$user->hasRole('Superadmin')) {
            $kazanilacakPuan = (int)(Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0);
        }

        // === Veritabanı işlemini Transaction içine alalım (daha güvenli) ===
        DB::beginTransaction();
        try {
            $sikayet = MusteriSikayeti::create([
                'musteri_adi' => $validated['musteri_adi'],
                'musteri_iletisim' => $validated['musteri_iletisim'],
                'konum_tipi' => $validated['konum_tipi'], // <-- YENİ: Konum Tipi eklendi
                'sikayet_kategorisi_id' => $validated['sikayet_kategorisi_id'], // <-- YENİ: Kategori ID'sini ekle
                'musteri_oncelik' => $validated['musteri_oncelik'],
                'musteri_sikayet_konusu' => $validated['musteri_sikayet_konusu'],
                'musteri_sikayet_detayi' => $validated['musteri_sikayet_detayi'],
                'musteri_sikayet_tarihi' => $validated['musteri_sikayet_tarihi'],
                'olusturan_kurul_uyesi_id' => $user->id,
                'musteri_durum' => 'Yeni',
                'kazanilan_puan' => $kazanilacakPuan,
            ]);

            if ($kazanilacakPuan > 0) {
                $user->increment('toplam_puan', $kazanilacakPuan);
            }

            // Dosyaları kaydetme mantığı
            if ($request->hasFile('dosyalar')) {
                foreach ($request->file('dosyalar') as $dosya) {
                    $path = $dosya->store('sikayet_dosyalari', 'public');

                    if ($path === false) {
                        Log::error('Dosya fiziksel olarak kaydedilemedi: ' . $dosya->getClientOriginalName());
                        continue; 
                    }

                    // Veritabanı kaydı
                    $sikayet->dosyalar()->create([
                        'dosya_yolu' => $path,
                        'orijinal_adi' => $dosya->getClientOriginalName(),
                        'mime_tipi' => $dosya->getMimeType(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.sikayetler.index')->with('success', 'Şikayet ve dosyalar başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Şikayet veya dosya kaydında hata: ' . $e->getMessage());
            return back()->with('error', 'Şikayet kaydedilirken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    public function show(MusteriSikayeti $sikayet)
    {
        $this->authorize('view', $sikayet);
        // Kategori ilişkisini de yükle
        $sikayet->load('cozumTakimi', 'olusturanKurulUyesi', 'dosyalar', 'sikayetKategori'); // <-- 'sikayetKategori' eklendi
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

        $validated = $request->validate([
            'musteri_adi' => 'required|string|max:255',
            'musteri_iletisim' => 'nullable|string|max:255',
            'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı', // <-- YENİ: Konum Tipi validasyonu
            'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id', // <-- YENİ: Kategori validasyonu
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
            // Sadece validate edilmiş alanları güncelle (kategori dahil)
            $sikayet->update($validated);

            // 2. === YENİ EKLENDİ: Silinmek üzere işaretlenen dosyaları sil ===
            if ($request->has('dosyalar_sil')) {
                $dosyaIdsToSil = $request->input('dosyalar_sil');
                
                // Güvenlik: Sadece bu şikayete ait olan dosyaları sil
                $dosyalar = MusteriSikayetiDosyasi::where('musteri_sikayeti_id', $sikayet->id)
                                                  ->whereIn('id', $dosyaIdsToSil)
                                                  ->get();

                foreach ($dosyalar as $dosya) {
                    Storage::disk('public')->delete($dosya->dosya_yolu); // Fiziksel dosyayı sil
                    $dosya->delete(); // Veritabanı kaydını sil
                }
            }
            // ==============================================================

            // 3. Yeni dosyaları kaydetme mantığı (Bu kısım aynı kaldı)
            if ($request->hasFile('dosyalar')) {
                foreach ($request->file('dosyalar') as $dosya) {
                     $path = $dosya->store('sikayet_dosyalari', 'public');
                     if ($path === false) {
                         Log::error('Dosya fiziksel olarak kaydedilemedi (update): ' . $dosya->getClientOriginalName());
                         continue;
                     }
                    // Veritabanı kaydı
                    $sikayet->dosyalar()->create([
                        'dosya_yolu' => $path,
                        'orijinal_adi' => $dosya->getClientOriginalName(),
                        'mime_tipi' => $dosya->getMimeType(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.sikayetler.show', $sikayet)->with('success', 'Şikayet başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Şikayet güncelleme veya dosya silme/kaydetmede hata: ' . $e->getMessage());
            return back()->with('error', 'Şikayet güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
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

    /**
     * === BU FONKSİYON SİLİNDİ ===
     * Anında silme (AJAX) fonksiyonu olan 'destroyDosya'
     * sizin istediğiniz "kaydet'e basana kadar bekle" mantığına
     * uymadığı için TAMAMEN KALDIRILDI.
     */
    // public function destroyDosya(...) { ... }
}