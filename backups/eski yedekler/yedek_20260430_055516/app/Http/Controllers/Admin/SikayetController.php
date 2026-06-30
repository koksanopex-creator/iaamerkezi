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
    use AuthorizesRequests, ComplaintNotificationTrait;

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

        // Yetkili olunan bölümleri al
        $user = auth()->user();

        // [YÖNLENDİRME] Müşteri temsilcisi admin URL'sine girerse IAA URL'sine at (URL'de admin görünmesin diye)
        if (($user->customer_id || $user->hasRole(['Müşteri', 'Müşteri Temsilcisi'])) && request()->is('admin/*')) {
            return redirect()->route('iaa.sikayetler.create', $request->all());
        }

        $allowedBolumIds = $user->getAllowedBolumIds();

        // Kategorileri çek (Yetkiye göre filtrele)
        $kategoriQuery = SikayetKategori::orderBy('ad');
        if ($allowedBolumIds !== '*') {
            $kategoriQuery->whereIn('bolum_id', $allowedBolumIds);
        }
        $kategoriler = $kategoriQuery->get();

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
        $kategori = \App\Models\SikayetKategori::with('altKategoriler')->find($request->sikayet_kategorisi_id);
        $hariciAltKategoriZorunlu = $kategori && ($kategori->altKategoriler->count() > 0 || $kategori->diger_secenegi_goster);

        $rules = [
            'customer_id' => 'required|integer|exists:customers,id',
            'yetkili_user_id' => 'nullable|integer|exists:users,id',
            'musteri_adi' => 'nullable|string|max:255',
            'musteri_iletisim' => 'nullable|string|max:255',
            'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı',
            'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id',
            'sikayet_alt_kategori_id' => $hariciAltKategoriZorunlu ? 'required_without:sikayet_alt_kategori_diger|nullable|integer' : 'nullable|integer',
            'sikayet_alt_kategori_diger' => $hariciAltKategoriZorunlu ? 'required_without:sikayet_alt_kategori_id|nullable|string|max:500' : 'nullable|string|max:500',
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
        ];

        $messages = [
            'sikayet_alt_kategori_id.required_without' => 'Lütfen bir alt kategori seçiniz veya diğer seçeneği ile açıklama belirtiniz.',
            'sikayet_alt_kategori_diger.required_without' => 'Alt kategori olarak "Diğer" seçildiğinde açıklama girmek zorunludur.',
        ];

        $validated = $request->validate($rules, $messages);

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

            // === TAKİP BİLGİLERİ OLUŞTURMA ===
            $plainPassword = \Illuminate\Support\Str::random(8);
            $sikayetData['takip_token'] = \Illuminate\Support\Str::random(12);
            $sikayetData['guest_password_hash'] = \Illuminate\Support\Facades\Hash::make($plainPassword);
            // ================================

            $sikayet = MusteriSikayeti::create($sikayetData);

            // Ek Yetkilileri Kaydet (Pivot Tablo)
            if ($request->has('ek_yetkili_user_ids')) {
                $sikayet->ekYetkililer()->sync($request->ek_yetkili_user_ids);
            }
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
                // İsimlendirme için gerekli verileri hazırla
                $kategoriAd = \App\Models\SikayetKategori::find($sikayetData['sikayet_kategorisi_id'])->ad ?? 'kategori';
                $kategoriAd = \Illuminate\Support\Str::slug($kategoriAd, '_');

                $tarihSaat = now()->format('dmY_Hi');

                $rolAd = $user->getRoleNames()->first() ?? ($user->is_personnel ? 'personel' : 'musteri');
                $rolAd = \Illuminate\Support\Str::slug($rolAd, '_');

                $musteriAd = $sikayetData['musteri_adi'] ?? 'bilinmiyor';
                $musteriAd = \Illuminate\Support\Str::slug($musteriAd, '');

                foreach ($request->file('dosyalar') as $dosya) {
                    $orijinalUzanti = $dosya->getClientOriginalExtension();

                    // Örn: kapak_07032026_1100_superadmin_anadolugida.jpg
                    $yeniDosyaAdi = "{$kategoriAd}_{$tarihSaat}_{$rolAd}_{$musteriAd}.{$orijinalUzanti}";

                    // Eğer aynı saniyede birden fazla dosya yüklenirse isim çakışmasını önlemek için
                    $sayac = 1;
                    $geciciDosyaAdi = $yeniDosyaAdi;
                    while (\Illuminate\Support\Facades\Storage::disk('public')->exists('sikayet_dosyalari/' . $geciciDosyaAdi)) {
                        $geciciDosyaAdi = "{$kategoriAd}_{$tarihSaat}_{$rolAd}_{$musteriAd}_{$sayac}.{$orijinalUzanti}";
                        $sayac++;
                    }
                    $yeniDosyaAdi = $geciciDosyaAdi;

                    $path = $dosya->storeAs('sikayet_dosyalari', $yeniDosyaAdi, 'public');

                    if ($path) {
                        $sikayet->dosyalar()->create([
                            'dosya_yolu' => $path,
                            'orijinal_adi' => $dosya->getClientOriginalName(),
                            'mime_tipi' => $dosya->getMimeType(),
                        ]);
                    }
                }
            }

            // TÜM İLGİLİLERE BİLDİRİM GÖNDER (Bölüm Lideri, Direktör, Kalite, Kurul + Firma Yetkilileri)
            // Not: sendNewComplaintNotification metodu artık tüm şikayet yetkililerine "YeniMusteriSikayetiBildirimi" gönderecek şekilde güncellenecektir.
            $this->sendNewComplaintNotification($sikayet);

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

    public function show($id)
    {
        Log::info('SikayetController@show hit with ID: ' . $id . ' by user: ' . \Illuminate\Support\Facades\Auth::id());
        $user = \Illuminate\Support\Facades\Auth::user();

        // Eğer Superadmin veya Yönetim ise silinmiş olanları da bulabilsin. Değilse sadece aktifleri.
        if ($user->hasAnyRole(['Superadmin', 'Super Admin', 'SUPERADMIN', 'Yonetim', 'Yönetim'])) {
            $sikayet = \App\Models\MusteriSikayeti::withTrashed()->with(['sikayetKategori', 'iaa'])->findOrFail($id);
        } else {
            $sikayet = \App\Models\MusteriSikayeti::with(['sikayetKategori', 'iaa'])->findOrFail($id);
        }

        $user = \Illuminate\Support\Facades\Auth::user();

        // [YÖNLENDİRME] Müşteri temsilcisi admin URL'sine girerse IAA URL'sine at (URL'de admin görünmesin diye)
        if (($user->customer_id || $user->hasRole(['Müşteri', 'Müşteri Temsilcisi'])) && request()->is('admin/*')) {
            return redirect()->route('iaa.sikayetler.show', $id);
        }

        $yetkiVar = false;

        // -------------------------------------------------------------
        // 1. SÜPER YETKİLİLER (Her yeri görenler)
        // -------------------------------------------------------------
        if ($user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu'])) {
            $yetkiVar = true;
        }

        // -------------------------------------------------------------
        // 2. BÖLÜM VE KATEGORİ YETKİSİ
        // -------------------------------------------------------------
        if (!$yetkiVar) {
            $allowedBolumIds = $user->getAllowedBolumIds();

            if ($allowedBolumIds === '*' || ($sikayet->sikayetKategori && in_array($sikayet->sikayetKategori->bolum_id, $allowedBolumIds))) {
                $yetkiVar = true;
            }
        }

        // -------------------------------------------------------------
        // 3. GÖREV VE TAKIM YETKİSİ
        // -------------------------------------------------------------
        if (!$yetkiVar) {
            // A. Atanan Çözüm Takımının Üyesi mi?
            if ($sikayet->atanan_cozum_takimi_id && $user->takimlar->contains($sikayet->atanan_cozum_takimi_id)) {
                $yetkiVar = true;
            }

            // B. İAA Projesi (Squad) Üyesi mi?
            if (!$yetkiVar && $sikayet->iaa) {
                $isSquadMember = $sikayet->iaa->users()
                    ->where('users.id', $user->id) 
                    ->whereIn('iaa_user.durum', ['onaylandi', 'bekliyor'])
                    ->exists();

                if ($isSquadMember) {
                    $yetkiVar = true;
                }
            }
        }

        // 4. Müşteri Yetkilisi
        if (!$yetkiVar && $user->customer_id && $sikayet->customer_id == $user->customer_id) {
            $yetkiVar = true;
        }

        if (!$yetkiVar) {
            if ($user->hasRole(['Müşteri', 'Müşteri Temsilcisi']) || $user->customer_id) {
                return redirect()->route('dashboard');
            }
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

        // [YENİ] Tam Yetki Kontrolü
        $user = auth()->user();
        $tamYetki = $user->hasRole('Superadmin') || $user->id === $sikayet->olusturan_kurul_uyesi_id;

        // Kategorileri çek (Yetkiye göre filtrele)
        $allowedBolumIds = $user->getAllowedBolumIds();
        $kategoriQuery = SikayetKategori::orderBy('ad');
        if ($allowedBolumIds !== '*') {
            $kategoriQuery->whereIn('bolum_id', $allowedBolumIds);
        }
        $kategoriler = $kategoriQuery->get();

        // Üretim Detayları için Verileri Çek
        $machines = \App\Models\Machine::where('status', 'active')->orderBy('name')->get();

        // Hammaddeler ve Versiyonlar Bölüme göre filtrelenebilir ama şimdilik hepsini veya genel olanları çekelim
        // İleride kategoriye göre filtreleme JS ile yapılabilir. Şimdilik aktif olanları çekiyoruz.
        $genelHammaddeler = \App\Models\GenelHammadde::where('aktif_mi', true)->orderBy('ad')->get();
        $urunVersiyonlari = \App\Models\UrunVersiyonu::where('aktif_mi', true)->orderBy('ad')->get();

        // [YENİ] Şikayete ait tarihçeyi (logları) çek
        $loglar = $sikayet->loglar()->with('user')->latest()->get();

        // [YENİ] Tam Yetki Kontrolü
        $user = auth()->user();
        $tamYetki = $user->hasRole('Superadmin') || $user->id === $sikayet->olusturan_kurul_uyesi_id;

        return view('admin.sikayetler.edit', compact('sikayet', 'kategoriler', 'machines', 'genelHammaddeler', 'urunVersiyonlari', 'loglar', 'tamYetki'));
    }

    /**
     * === UPDATE METODU - GÜNCELLENDİ ===
     * Bu metod artık 'dosyalar_sil' adında bir diziyi de işleyebiliyor.
     */
    public function update(Request $request, MusteriSikayeti $sikayet)
    {
        $this->authorize('update', $sikayet);
        $user = auth()->user();

        // === KRİTİK TEMİZLİK: Boş gelen ID'leri NULL yap ===
        $request->merge([
            'customer_id' => $request->customer_id ?: null,
            'yetkili_user_id' => $request->yetkili_user_id ?: null,
            'sikayet_alt_kategori_id' => ($request->sikayet_alt_kategori_id === 'other' || !$request->sikayet_alt_kategori_id) ? null : $request->sikayet_alt_kategori_id,
        ]);

        // Validasyon Kuralları
        $kategori = \App\Models\SikayetKategori::with('altKategoriler')->find($request->sikayet_kategorisi_id);
        $hariciAltKategoriZorunlu = $kategori && ($kategori->altKategoriler->count() > 0 || $kategori->diger_secenegi_goster);

        $rules = [
            'customer_id' => 'nullable|integer|exists:customers,id',
            'yetkili_user_id' => 'nullable|integer|exists:users,id',

            // ESKİ KAYITLAR İÇİN: customer_id yoksa musteri_adi zorunlu
            'musteri_adi' => 'required_without:customer_id|nullable|string|max:255',
            'musteri_iletisim' => 'nullable|string|max:255',
            'konum_tipi' => 'required|string|in:Yurt İçi,Yurt Dışı',
            'sikayet_kategorisi_id' => 'required|integer|exists:sikayet_kategorileri,id',
            'sikayet_alt_kategori_id' => $hariciAltKategoriZorunlu ? 'required_without:sikayet_alt_kategori_diger|nullable|integer' : 'nullable|integer',
            'sikayet_alt_kategori_diger' => $hariciAltKategoriZorunlu ? 'required_without:sikayet_alt_kategori_id|nullable|string|max:500' : 'nullable|string|max:500',
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
            'ek_yetkili_user_ids' => 'nullable|array',
            'ek_yetkili_user_ids.*' => 'integer|exists:users,id',
        ];

        $messages = [
            'sikayet_alt_kategori_id.required_without' => 'Lütfen bir alt kategori seçiniz veya diğer seçeneği ile açıklama belirtiniz.',
            'sikayet_alt_kategori_diger.required_without' => 'Alt kategori olarak "Diğer" seçildiğinde açıklama girmek zorunludur.',
        ];

        $validated = $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            // Validate edilmiş veriyi al, dosya ve teknik detay işlem dizilerini çıkar
            $updateData = collect($validated)->except([
                'dosyalar',
                'dosyalar_sil',
                'lot_no',
                'machine_id',
                'genel_hammadde_id',
                'urun_versiyonu_id',
                'ek_yetkili_user_ids'
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

            // --- TAM YETKİ KONTROLÜ (Sadece Seçili Alanları Güncelle) ---
            $tamYetki = $user->hasRole('Superadmin') || $user->id === $sikayet->olusturan_kurul_uyesi_id;

            if (!$tamYetki) {
                // Kısıtlı yetkili ise, sadece izin verilen alanları güncelle + [YENİ: Alt Kategori Güncelleme İzni]
                $updateData = [
                    'musteri_sikayet_konusu' => $updateData['musteri_sikayet_konusu'] ?? $sikayet->musteri_sikayet_konusu,
                    'musteri_sikayet_detayi' => $updateData['musteri_sikayet_detayi'] ?? $sikayet->musteri_sikayet_detayi,
                ];

                // Alt Kategori Mantığı (Kısıtlı Yetki)
                if ($request->sikayet_alt_kategori_id === 'other') {
                    $updateData['sikayet_alt_kategori_id'] = null;
                    $updateData['sikayet_alt_kategori_diger'] = $request->sikayet_alt_kategori_diger;
                } else {
                    $updateData['sikayet_alt_kategori_id'] = $request->sikayet_alt_kategori_id ?: null;
                    $updateData['sikayet_alt_kategori_diger'] = null;
                }

            } else {
                // Tam yetkiliyse tüm veriyi hazırla (zaten updateData tam)
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
            }

            // Veritabanını Güncelle
            $sikayet->update($updateData);

            // [YENİ] Varsa bağlı Iaa projesinin başlığını da senkronize et
            if ($sikayet->iaa_id) {
                $iaa = \App\Models\Iaa::find($sikayet->iaa_id);
                if ($iaa && isset($updateData['musteri_sikayet_konusu'])) {
                    $iaa->update(['baslik' => $updateData['musteri_sikayet_konusu']]);
                }
            }

            // --- EK YETKİLİLERİ GÜNCELLE VE BİLDİRİM GÖNDER ---
            if ($tamYetki && $request->has('ek_yetkili_user_ids')) {
                $oldEkIds = $sikayet->ekYetkililer->pluck('id')->toArray();
                $newEkIds = $request->ek_yetkili_user_ids;
                
                // Pivot tabloyu güncelle
                $sikayet->ekYetkililer()->sync($newEkIds);

                // Sadece yeni eklenenleri bul
                $addedIds = array_diff($newEkIds, $oldEkIds);
                
                if (!empty($addedIds)) {
                    $addedUsers = \App\Models\User::whereIn('id', $addedIds)->get();
                    
                    // Mevcut snapshot'ı al
                    $currentSnapshot = json_decode($sikayet->notified_snapshot, true) ?: [];
                    
                    foreach ($addedUsers as $u) {
                        // Bildirim Gönder
                        $u->notify(new \App\Notifications\YeniMusteriSikayetiBildirimi($sikayet));

                        // Snapshot'a ekle
                        $currentSnapshot[] = [
                            'user_id' => $u->id,
                            'name' => $u->name,
                            'email' => $u->email,
                            'phone' => $u->telefon ?? $u->phone ?? null,
                            'photo' => $u->profile_photo_path,
                            'role_label' => 'Ek İlgili (Sonradan Eklendi)',
                            'notified_at' => now()->toDateTimeString(),
                        ];
                    }

                    // Snapshot'ı güncelle
                    $sikayet->update(['notified_snapshot' => json_encode($currentSnapshot)]);
                }
            }

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
                $dosyaQuery = MusteriSikayetiDosyasi::where('musteri_sikayeti_id', $sikayet->id)
                    ->whereIn('id', $request->input('dosyalar_sil'));

                // Tam yetkili değilse, sadece KENDİ yüklediği dosyaları silebilir
                if (!$tamYetki) {
                    $dosyaQuery->where('yukleyen_id', $user->id);
                }

                $dosyalar = $dosyaQuery->get();

                foreach ($dosyalar as $dosya) {
                    /** @var MusteriSikayetiDosyasi $dosya */
                    Storage::disk('public')->delete($dosya->dosya_yolu);
                    $dosya->delete();
                }
            }

            // Yeni dosyaları ekle
            if ($request->hasFile('dosyalar')) {
                // İsimlendirme için gerekli verileri hazırla
                $kategoriAd = $sikayet->sikayetKategori->ad ?? 'kategori';
                $kategoriAd = \Illuminate\Support\Str::slug($kategoriAd, '_');

                $tarihSaat = now()->format('dmY_Hi');

                $rolAd = $user->getRoleNames()->first() ?? ($user->is_personnel ? 'personel' : 'musteri');
                $rolAd = \Illuminate\Support\Str::slug($rolAd, '_');

                $musteriAd = $sikayet->musteri_adi ?? 'bilinmiyor';
                $musteriAd = \Illuminate\Support\Str::slug($musteriAd, '');

                foreach ($request->file('dosyalar') as $dosya) {
                    $orijinalUzanti = $dosya->getClientOriginalExtension();

                    $yeniDosyaAdi = "{$kategoriAd}_{$tarihSaat}_{$rolAd}_{$musteriAd}.{$orijinalUzanti}";

                    $sayac = 1;
                    $geciciDosyaAdi = $yeniDosyaAdi;
                    while (\Illuminate\Support\Facades\Storage::disk('public')->exists('sikayet_dosyalari/' . $geciciDosyaAdi)) {
                        $geciciDosyaAdi = "{$kategoriAd}_{$tarihSaat}_{$rolAd}_{$musteriAd}_{$sayac}.{$orijinalUzanti}";
                        $sayac++;
                    }
                    $yeniDosyaAdi = $geciciDosyaAdi;

                    $path = $dosya->storeAs('sikayet_dosyalari', $yeniDosyaAdi, 'public');

                    if ($path) {
                        $sikayet->dosyalar()->create([
                            'dosya_yolu' => $path,
                            'orijinal_adi' => $dosya->getClientOriginalName(),
                            'mime_tipi' => $dosya->getMimeType(),
                        ]);
                    }
                }
            }

            // LOGLAMA (Kim tarafından güncellendi)
            $rol = $user->getRoleNames()->first();
            if (!$rol) {
                $rol = $user->is_personnel ? 'Personel' : 'Müşteri';
            }

            // 1. Firma Geneli Müşteri Logu
            \App\Models\MusteriLog::add(
                $sikayet->customer_id,
                'Şikayet Güncelleme',
                $user->name . " ($rol) tarafından #{$sikayet->id} nolu şikayet güncellendi."
            );

            // 2. [YENİ] Şikayetin Kendi Geçmişine Ekleme (Şikayet Detayındaki Tarihçe)
            \App\Models\MusteriSikayetiLog::create([
                'musteri_sikayeti_id' => $sikayet->id,
                'user_id' => $user->id,
                'eylem' => 'Şikayet Güncelleme',
                'aciklama' => $user->name . " ($rol) tarafından şikayet detayları güncellendi.",
            ]);

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

        DB::beginTransaction();
        try {
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

    public function restore($id)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['Superadmin', 'Super Admin', 'SUPERADMIN', 'Yonetim', 'Yönetim'])) {
            abort(403, 'Çöp kutusundan geri alma yetkiniz yok.');
        }

        $sikayet = MusteriSikayeti::withTrashed()->findOrFail($id);
        $sikayet->restore();

        \App\Models\MusteriLog::add(
            $sikayet->customer_id,
            'Şikayet Geri Alma (Kurtarma)',
            $user->name . ', #' . $sikayet->id . ' nolu şikayeti detay sayfasından geri aldı.'
        );

        return back()->with('success', 'Şikayet başarıyla çöp kutusundan çıkarıldı (Geri Alındı)!');
    }
}