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
use App\Traits\ComplaintNotificationTrait;
use Illuminate\Support\Facades\Validator;


class SikayetController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', MusteriSikayeti::class);
        return view('admin.sikayetler.index');
    }

    /**
     * Şikayet oluşturma formunu gösterir.
     * URL'den 'musteri_id' parametresi gelirse, o müşteriyi otomatik seçer.
     */
    public function create(Request $request)
    {
        $this->authorize('create', MusteriSikayeti::class);

        // Kategorileri çek
        $kategoriler = SikayetKategori::orderBy('ad')->get();

        // URL'den gelen 'musteri_id' parametresini al (Varsa)
        // Ancak validation hatası varsa old('customer_id') daha öncelikli olsun
        $preselectedCustomerId = old('customer_id') ?? $request->query('musteri_id');
        $preselectedRepId = old('yetkili_user_id');

        // View'e hem kategorileri hem de varsa müşteri ID'sini gönder
        return view('admin.sikayetler.create', compact('kategoriler', 'preselectedCustomerId', 'preselectedRepId'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', MusteriSikayeti::class);
        $user = auth()->user();

        // ============================================================
        // === GÜVENLİK VE OTOMATİK VERİ ATAMA ===
        // ============================================================

        // 1. Eğer Müşteri İse: Başkası adına kayıt giremez, kendi ID'sini zorla.
        if (!$user->is_personnel && $user->customer_id) {
            $request->merge(['customer_id' => $user->customer_id]);
        }

        // Temizlik
        $request->merge([
            'yetkili_user_id' => $request->yetkili_user_id ?: null,
            'sikayet_alt_kategori_id' => ($request->sikayet_alt_kategori_id === 'other' || !$request->sikayet_alt_kategori_id) ? null : $request->sikayet_alt_kategori_id,
        ]);

        // ============================================================
        // === VALIDASYON ===
        // ============================================================
        // ============================================================
        // === VALIDASYON ===
        // ============================================================
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

            // YENİ ALANLAR (Çoklu Giriş)
            'lot_no' => 'nullable|array',
            'lot_no.*' => 'nullable|string|max:255',
            'machine_id' => 'nullable|array',
            'machine_id.*' => 'nullable|exists:machines,id',
            'genel_hammadde_id' => 'nullable|array',
            'genel_hammadde_id.*' => 'nullable|exists:genel_hammaddeler,id',
            'urun_versiyonu_id' => 'nullable|array',
            'urun_versiyonu_id.*' => 'nullable|exists:urun_versiyonlari,id',
        ]);

        // ============================================================
        // === PUAN VE HAZIRLIK ===
        // ============================================================
        $kazanilacakPuan = 0;

        // KURAL: Sadece Kurul Üyesi/Personel ise puan ver. Müşteriye puan YOK.
        if (!$user->hasRole('Superadmin') && $user->is_personnel) {
            $kazanilacakPuan = (int) (\App\Models\Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0);
        }

        DB::beginTransaction();
        try {
            // Validasyon verisinden 'dosyalar' ve dizi olan teknik detayları çıkarıyoruz (Ana modelde yoklar)
            $sikayetData = collect($validated)->except([
                'dosyalar',
                'lot_no',
                'machine_id',
                'genel_hammadde_id',
                'urun_versiyonu_id'
            ])->toArray();

            // Müşteri Adı ve İletişim Bilgisi Doldurma
            if ($request->customer_id) {
                $customer = \App\Models\Customer::find($request->customer_id);
                $sikayetData['musteri_adi'] = $customer->name;

                // Müşteri kendi ekliyorsa, iletişim bilgisi olarak kendi profilini yazsın
                if (!$user->is_personnel) {
                    $sikayetData['musteri_iletisim'] = $user->name . ' (' . $user->email . ')';
                }
                // Personel ekliyorsa ve yetkili seçtiyse onu yazsın
                elseif ($request->yetkili_user_id) {
                    $yetkili = \App\Models\User::find($request->yetkili_user_id);
                    $sikayetData['musteri_iletisim'] = $yetkili->name . ' (' . $yetkili->email . ')';
                }
                // Hiçbiri değilse firmanın genel telefonunu yazsın
                else {
                    $sikayetData['musteri_iletisim'] = $customer->phone ?? 'Belirtilmedi';
                }
            }

            // Alt Kategori Temizliği
            if ($request->sikayet_alt_kategori_id === null && $request->sikayet_alt_kategori_diger) {
                $sikayetData['sikayet_alt_kategori_id'] = null;
            } else {
                $sikayetData['sikayet_alt_kategori_diger'] = null;
            }

            // Müşteri Ekliyorsa 'olusturan_kurul_uyesi_id' yine de kendi ID'si olsun (Takip için)
            // Ama puan kazanmayacak.
            $sikayetData['olusturan_kurul_uyesi_id'] = $user->id;
            $sikayetData['musteri_durum'] = 'Yeni';
            $sikayetData['kazanilan_puan'] = $kazanilacakPuan;

            $sikayet = MusteriSikayeti::create($sikayetData);

            // TEKNİK DETAYLARI KAYDET (Çoklu)
            if ($request->has('lot_no') && is_array($request->lot_no)) {
                $count = count($request->lot_no);
                for ($i = 0; $i < $count; $i++) {
                    $lot = $request->lot_no[$i] ?? null;
                    $machine = $request->machine_id[$i] ?? null;
                    $hammadde = $request->genel_hammadde_id[$i] ?? null;
                    $versiyon = $request->urun_versiyonu_id[$i] ?? null;

                    // Eğer herhangi bir veri girilmişse kaydet (Boş satırları atla)
                    if ($lot || $machine || $hammadde || $versiyon) {
                        $sikayet->teknikDetaylar()->create([
                            'lot_no' => $lot,
                            'machine_id' => $machine,
                            'genel_hammadde_id' => $hammadde,
                            'urun_versiyonu_id' => $versiyon,
                        ]);
                    }
                }
            }

            // Puan Ekleme (Sadece Personelse)
            if ($kazanilacakPuan > 0) {
                $user->increment('toplam_puan', $kazanilacakPuan);
            }

            // Dosya Kaydı
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

            // DİREKTÖR BİLDİRİMİ (Yeni)
            // Şikayetin bağlı olduğu kategorinin bölümü üzerinden direktörü bulalım
            if ($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum) {
                $bolum = $sikayet->sikayetKategori->bolum;
                // Bölümün tanımlı bir direktörü var mı?
                if ($bolum->director) {
                    $direktor = $bolum->director;
                    // Kendi eklediği şikayet için kendine bildirim gitmesin (İsteğe bağlı, genelde tercih edilir)
                    if ($direktor->id !== $user->id) {
                        $direktor->notify(new \App\Notifications\YeniMusteriSikayetiBildirimi($sikayet));
                    }
                }
            }

            DB::commit();

            // LOGLAMA
            $rol = $user->getRoleNames()->first();
            if (!$rol) {
                $rol = $user->is_personnel ? 'Personel' : 'Müşteri';
            }

            \App\Models\MusteriLog::add(
                $sikayet->customer_id,
                'Şikayet Oluşturma',
                $user->name . " ($rol) tarafından #{$sikayet->id} nolu şikayet oluşturuldu."
            );

            // YÖNLENDİRME
            if (!$user->is_personnel) {
                if ($user->customer_id) {
                    return redirect()->route('musteri.profil.show', $user->customer_id)
                        ->with('success', 'Şikayetiniz başarıyla oluşturuldu ve firma profilinize eklendi.');
                }
                return redirect()->route('dashboard')
                    ->with('success', 'Şikayetiniz başarıyla oluşturuldu ve işleme alındı.');
            }

            return redirect()->route('admin.sikayetler.index')
                ->with('success', 'Şikayet başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Şikayet hatası: ' . $e->getMessage());
            return back()->with('error', 'Hata: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id) // ID olarak alıp içeride findOrFail yapmak daha güvenli olabilir ama Model Binding de olur.
    {
        // Model Binding kullanıyorsan parametre (MusteriSikayeti $sikayet) kalabilir.
        // Ben garanti olsun diye ID üzerinden çekiyorum, ilişkileri de burada yüklüyorum.
        $sikayet = \App\Models\MusteriSikayeti::with(['sikayetKategori', 'iaa'])->findOrFail($id);

        $user = \Illuminate\Support\Facades\Auth::user();
        $yetkiVar = false;

        // -------------------------------------------------------------
        // 1. SÜPER YETKİLİLER (Her yeri görenler)
        // -------------------------------------------------------------
        // DİKKAT: Buradan 'Bölüm Kalite Yöneticisi'ni ÇIKARDIM.
        if ($user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu'])) {
            $yetkiVar = true;
        }

        // -------------------------------------------------------------
        // 2. BÖLÜM VE KATEGORİ YETKİSİ (Serkan Tölek, Serkan Atak, Hasan Ekinci)
        // -------------------------------------------------------------
        if (!$yetkiVar) {
            // User modeline eklediğimiz o akıllı fonksiyonu kullanıyoruz.
            $allowedBolumIds = $user->getAllowedBolumIds();

            // Şikayetin kategorisi var mı ve bu kategori kullanıcının yetki alanında mı?
            if ($sikayet->sikayetKategori && in_array($sikayet->sikayetKategori->bolum_id, $allowedBolumIds)) {
                $yetkiVar = true;
            }
        }

        // -------------------------------------------------------------
        // 3. GÖREV VE TAKIM YETKİSİ (Sinan Poyraz, Cihangir vb.)
        // -------------------------------------------------------------
        if (!$yetkiVar) {

            // A. Atanan Çözüm Takımının Üyesi mi?
            if ($sikayet->atanan_cozum_takimi_id && $user->takimlar->contains($sikayet->atanan_cozum_takimi_id)) {
                $yetkiVar = true;
            }

            // B. İAA Projesi (Squad) Üyesi mi?
            elseif ($sikayet->iaa) {
                // Proje ekibinde var mı? (Durumu 'onaylandi' olanlar)
                $isSquadMember = $sikayet->iaa->users()
                    ->where('users.id', $user->id)
                    // ->wherePivot('durum', 'onaylandi') // İstersen bu filtreyi açabilirsin
                    ->exists();

                if ($isSquadMember) {
                    $yetkiVar = true;
                }
            }

            // C. Bölüm Lideri Ekstra Kontrolü (Serkan Atak)
            // Kendi bölümünden (Kapak) bir personel (Sinan), başka bir bölümün (Preform) projesindeyse,
            // Lider bunu görebilmeli mi? Genelde evet.
            if ($user->hasRole('Bölüm Lideri') && $sikayet->iaa) {
                $staffIds = \App\Models\User::where('bolum_id', $user->bolum_id)->pluck('id');
                // Projede benim elemanlardan biri var mı?
                if ($sikayet->iaa->users()->whereIn('users.id', $staffIds)->exists()) {
                    $yetkiVar = true;
                }
            }
        }

        // -------------------------------------------------------------
        // 4. [YENİ] MÜŞTERİ YETKİLİSİ ERİŞİMİ
        // -------------------------------------------------------------
        // Eğer yetki hala yoksa ama kullanıcı bir Müşteri Yetkilisiyse
        // ve şikayet bu müşterinin firmasına aitse -> İZİN VER
        if (!$yetkiVar && $user->customer_id && $sikayet->customer_id == $user->customer_id) {
            $yetkiVar = true;
        }

        // -------------------------------------------------------------
        // SONUÇ: YETKİ YOKSA AT (Mevcut Kod)
        // -------------------------------------------------------------
        if (!$yetkiVar) {
            abort(403, 'Bu şikayeti görüntüleme yetkiniz yok. (Bölüm eşleşmesi veya görev bulunamadı)');
        }

        // -------------------------------------------------------------
        // İLİŞKİLERİ YÜKLE VE GÖNDER
        // -------------------------------------------------------------
        // Senin orijinal kodundaki ilişkileri yüklüyoruz
        $sikayet->load([
            'cozumTakimi',
            'olusturanKurulUyesi',
            'dosyalar',
            'sikayetKategori',
            'sikayetAltKategori',
            'customer',
            'yetkili_user',
            'iaaProjesi',
            'machine',
            'genelHammadde',
            'urunVersiyonu',
            'loglar.user' // Logları ve logu oluşturan kullanıcıyı yükle
        ]);

        // Firma İstatistikleri
        $firmaSikayetSayisi = 0;
        $kacinciSikayet = 0;

        if ($sikayet->customer_id) {
            $firmaSikayetSayisi = \App\Models\MusteriSikayeti::where('customer_id', $sikayet->customer_id)->count();
            $kacinciSikayet = \App\Models\MusteriSikayeti::where('customer_id', $sikayet->customer_id)
                ->where('id', '<=', $sikayet->id)
                ->count();
        }

        return view('admin.sikayetler.show', compact('sikayet', 'firmaSikayetSayisi', 'kacinciSikayet'));
    }

    public function edit(MusteriSikayeti $sikayet)
    {
        $this->authorize('update', $sikayet);
        $sikayet->load('dosyalar');

        // Kategorileri çek
        $kategoriler = SikayetKategori::orderBy('ad')->get();

        // Üretim Detayları için Verileri Çek
        $machines = \App\Models\Machine::where('status', 'active')->orderBy('name')->get();

        // Hammaddeler ve Versiyonlar Bölüme göre filtrelenebilir ama şimdilik hepsini veya genel olanları çekelim
        // İleride kategoriye göre filtreleme JS ile yapılabilir. Şimdilik aktif olanları çekiyoruz.
        $genelHammaddeler = \App\Models\GenelHammadde::where('aktif_mi', true)->orderBy('ad')->get();
        $urunVersiyonlari = \App\Models\UrunVersiyonu::where('aktif_mi', true)->orderBy('ad')->get();

        return view('admin.sikayetler.edit', compact('sikayet', 'kategoriler', 'machines', 'genelHammaddeler', 'urunVersiyonlari'));
    }

    /**
     * === UPDATE METODU - GÜNCELLENDİ ===
     * Bu metod artık 'dosyalar_sil' adında bir diziyi de işleyebiliyor.
     */
    public function update(Request $request, MusteriSikayeti $sikayet)
    {
        $this->authorize('update', $sikayet);

        // === KRİTİK TEMİZLİK: Boş gelen ID'leri NULL yap ===
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
            'dosyalar_sil.*' => 'integer|exists:musteri_sikayeti_dosyalari,id',

            // YENİ ALANLAR (Çoklu Giriş)
            'lot_no' => 'nullable|array',
            'lot_no.*' => 'nullable|string|max:255',
            'machine_id' => 'nullable|array',
            'machine_id.*' => 'nullable|exists:machines,id',
            'genel_hammadde_id' => 'nullable|array',
            'genel_hammadde_id.*' => 'nullable|exists:genel_hammaddeler,id',
            'urun_versiyonu_id' => 'nullable|array',
            'urun_versiyonu_id.*' => 'nullable|exists:urun_versiyonlari,id',
        ]);

        DB::beginTransaction();
        try {
            // Validate edilmiş veriyi al, dosya ve teknik detay işlem dizilerini çıkar
            $updateData = collect($validated)->except([
                'dosyalar',
                'dosyalar_sil',
                'lot_no',
                'machine_id',
                'genel_hammadde_id',
                'urun_versiyonu_id'
            ])->toArray();

            // ============================================================
            // === BURAYA EKLİYORUZ: OTOMATİK İSİM DOLDURMA MANTIĞI ===
            // ============================================================
            if ($request->customer_id) {
                $customer = \App\Models\Customer::find($request->customer_id);

                // Müşteri adını ve iletişim bilgisini otomatik doldur (Veritabanında boş kalmasın)
                $updateData['musteri_adi'] = $customer->name;

                // Eğer iletişim bilgisi boş gelmişse, firmanınkini yaz
                if (empty($updateData['musteri_iletisim'])) {
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

            // TEKNİK DETAYLARI GÜNCELLE (Strategy: Delete All & Re-create)
            // Mevcut detayları sil
            $sikayet->teknikDetaylar()->delete();

            // Yeni detayları ekle
            if ($request->has('lot_no') && is_array($request->lot_no)) {
                $count = count($request->lot_no);
                for ($i = 0; $i < $count; $i++) {
                    $lot = $request->lot_no[$i] ?? null;
                    $machine = $request->machine_id[$i] ?? null;
                    $hammadde = $request->genel_hammadde_id[$i] ?? null;
                    $versiyon = $request->urun_versiyonu_id[$i] ?? null;

                    // Eğer herhangi bir veri doluysa kaydet
                    if ($lot || $machine || $hammadde || $versiyon) {
                        $sikayet->teknikDetaylar()->create([
                            'lot_no' => $lot,
                            'machine_id' => $machine,
                            'genel_hammadde_id' => $hammadde,
                            'urun_versiyonu_id' => $versiyon,
                        ]);
                    }
                }
            }

            // Silinmek istenen dosyaları sil
            if ($request->has('dosyalar_sil')) {
                $dosyalar = MusteriSikayetiDosyasi::where('musteri_sikayeti_id', $sikayet->id)
                    ->whereIn('id', $request->input('dosyalar_sil'))->get();

                foreach ($dosyalar as $dosya) {
                    /** @var MusteriSikayetiDosyasi $dosya */
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

            // --- BURASI EKLENECEK ---
            \App\Models\MusteriLog::add(
                $sikayet->customer_id,
                'Şikayet Silme',
                auth()->user()->name . ', #' . $sikayet->id . ' nolu şikayeti sildi.'
            );
            // ------------------------

            DB::commit();
            return redirect()->route('admin.sikayetler.index')->with('success', 'Şikayet ve ilişkili dosyaları başarıyla silindi, kazanılan puan geri alındı.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Şikayet silinirken hata: ' . $e->getMessage());
            return back()->with('error', 'Şikayet silinirken bir hata oluştu.');
        }
    }

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
}