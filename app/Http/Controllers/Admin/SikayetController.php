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
        $user = auth()->user();
        if (!$user->can('create', MusteriSikayeti::class)) {
            abort(403, 'SikayetController::create authorization failed. User: ' . $user->id . ' Roles: ' . $user->roles->pluck('name')->implode(', '));
        }

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

        // 1. Eğer Müşteri İse: Başkası adına kayıt giremez, kendi yetkili olduğu firmalardan birini zorla.
        if (!$user->is_personnel) {
            $validCustomerIds = $user->customers()->pluck('customers.id')->toArray();
            if ($user->customer_id) {
                $validCustomerIds[] = $user->customer_id;
            }
            $validCustomerIds = array_unique($validCustomerIds);

            if (!in_array($request->customer_id, $validCustomerIds)) {
                // Eğer hiç seçilmediyse ve sadece 1 firması varsa otomatik ata
                if (!$request->customer_id && count($validCustomerIds) === 1) {
                    $request->merge(['customer_id' => $validCustomerIds[0]]);
                } else {
                    abort(403, 'Bu firma için şikayet oluşturma yetkiniz yok.');
                }
            }
        }

        // Temizlik
        $request->merge([
            'yetkili_user_id' => $request->yetkili_user_id ?: null,
            'sikayet_alt_kategori_id' => ($request->sikayet_alt_kategori_id === 'other' || !$request->sikayet_alt_kategori_id) ? null : $request->sikayet_alt_kategori_id,
        ]);


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
            if ($request->sikayet_alt_kategori_id === null && !empty($request->sikayet_alt_kategori_diger)) {
                $sikayetData['sikayet_alt_kategori_id'] = null;
                $sikayetData['sikayet_alt_kategori_diger'] = $request->sikayet_alt_kategori_diger;
            } else {
                $sikayetData['sikayet_alt_kategori_id'] = $request->sikayet_alt_kategori_id ?: null;
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
            try {
                $this->sendNewComplaintNotification($sikayet);
                $sikayet->update(['mail_sent' => true]);
            } catch (\Exception $mailEx) {
                Log::warning('Şikayet Bildirim Maili Gönderilemedi (#'.$sikayet->id.'): ' . $mailEx->getMessage());
                $sikayet->update([
                    'mail_sent' => false,
                    'mail_error' => $mailEx->getMessage()
                ]);
                
                // Merkezi mail log tablosuna kaydet
                \App\Helpers\MailLogHelper::logFailure(
                    $sikayet,
                    '"' . $sikayet->baslik . '" başlıklı şikayetin kaydı sırasında bildirim gönderilemedi',
                    collect(), // Alıcılar sendNewComplaintNotification içinde belirleniyor
                    $mailEx->getMessage(),
                    null,
                    null,
                    $sikayet->sikayetKategori->bolum_id ?? null
                );

                // Kullanıcıya bir uyarı session'ı da ekleyebiliriz ama işlemi durdurmuyoruz
                session()->flash('warning', 'Şikayet kaydedildi ancak bildirim mailleri gönderilemedi (Limit aşılmış olabilir).');
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
                $targetCustomerId = $request->customer_id ?? $user->customer_id;
                if ($targetCustomerId) {
                    return redirect()->route('musteri.profil.show', $targetCustomerId)
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

        // 1. SÜPER YETKİLİLER (Her yeri görenler)
        if ($user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu'])) {
            $yetkiVar = true;
        }

        // 2. BÖLÜM VE KATEGORİ YETKİSİ (SADECE PERSONEL İÇİN)
        if (!$yetkiVar && $user->is_personnel) {
            $allowedBolumIds = $user->getAllowedBolumIds();

            if ($allowedBolumIds === '*' || ($sikayet->sikayetKategori && in_array($sikayet->sikayetKategori->bolum_id, $allowedBolumIds))) {
                $yetkiVar = true;
            }
        }

        // 3. GÖREV VE TAKIM YETKİSİ (SADECE PERSONEL İÇİN)
        if (!$yetkiVar && $user->is_personnel) {
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

        // 4. Müşteri Yetkilisi / Temsilcisi (Çoklu Firma Desteği)
        if (!$yetkiVar && !$user->is_personnel) {
            if ($user->customer_id == $sikayet->customer_id || $user->customers()->where('customers.id', $sikayet->customer_id)->exists()) {
                $yetkiVar = true;
            }
        }

        if (!$yetkiVar) {
            if ($user->hasRole(['Müşteri', 'Müşteri Temsilcisi']) || $user->customer_id) {
                return redirect()->route('dashboard')->with('error', 'Bu şikayeti görüntüleme yetkiniz yok.');
            }
            abort(403, 'Bu şikayeti görüntüleme yetkiniz yok.');
        }

        // İLİŞKİLERİ YÜKLE VE GÖNDER
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
            'loglar.user'
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

        $user = auth()->user();
        $tamYetki = $user->hasRole('Superadmin') || $user->id === $sikayet->olusturan_kurul_uyesi_id;

        // Kategorileri çek (Yetkiye göre filtrele)
        $allowedBolumIds = $user->getAllowedBolumIds();
        $kategoriQuery = SikayetKategori::orderBy('ad');
        if ($allowedBolumIds !== '*') {
            $kategoriQuery->whereIn('bolum_id', $allowedBolumIds);
        }
        $kategoriler = $kategoriQuery->get();

        $machines = \App\Models\Machine::where('status', 'active')->orderBy('name')->get();
        $genelHammaddeler = \App\Models\GenelHammadde::where('aktif_mi', true)->orderBy('ad')->get();
        $urunVersiyonlari = \App\Models\UrunVersiyonu::where('aktif_mi', true)->orderBy('ad')->get();

        $loglar = $sikayet->loglar()->with('user')->latest()->get();

        return view('admin.sikayetler.edit', compact('sikayet', 'kategoriler', 'machines', 'genelHammaddeler', 'urunVersiyonlari', 'loglar', 'tamYetki'));
    }

    public function update(Request $request, MusteriSikayeti $sikayet)
    {
        $this->authorize('update', $sikayet);
        $user = auth()->user();

        $request->merge([
            'customer_id' => $request->customer_id ?: null,
            'yetkili_user_id' => $request->yetkili_user_id ?: null,
            'sikayet_alt_kategori_id' => ($request->sikayet_alt_kategori_id === 'other' || !$request->sikayet_alt_kategori_id) ? null : $request->sikayet_alt_kategori_id,
        ]);

        $kategori = \App\Models\SikayetKategori::with('altKategoriler')->find($request->sikayet_kategorisi_id);
        $hariciAltKategoriZorunlu = $kategori && ($kategori->altKategoriler->count() > 0 || $kategori->diger_secenegi_goster);

        $rules = [
            'customer_id' => 'nullable|integer|exists:customers,id',
            'yetkili_user_id' => 'nullable|integer|exists:users,id',
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
            'lot_no' => 'nullable|array',
            'machine_id' => 'nullable|array',
            'genel_hammadde_id' => 'nullable|array',
            'urun_versiyonu_id' => 'nullable|array',
            'ek_yetkili_user_ids' => 'nullable|array',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $updateData = collect($validated)->except([
                'dosyalar',
                'dosyalar_sil',
                'lot_no',
                'machine_id',
                'genel_hammadde_id',
                'urun_versiyonu_id',
                'ek_yetkili_user_ids'
            ])->toArray();

            if ($request->customer_id) {
                $customer = \App\Models\Customer::find($request->customer_id);
                $updateData['musteri_adi'] = $customer->name;
                if (empty($updateData['musteri_iletisim'])) {
                    $updateData['musteri_iletisim'] = $customer->phone ?? $customer->email;
                }
            }

            $tamYetki = $user->hasRole('Superadmin') || $user->id === $sikayet->olusturan_kurul_uyesi_id;

            if (!$tamYetki) {
                $updateData = [
                    'musteri_sikayet_konusu' => $updateData['musteri_sikayet_konusu'] ?? $sikayet->musteri_sikayet_konusu,
                    'musteri_sikayet_detayi' => $updateData['musteri_sikayet_detayi'] ?? $sikayet->musteri_sikayet_detayi,
                ];
                if ($request->sikayet_alt_kategori_id === null && !empty($request->sikayet_alt_kategori_diger)) {
                    $updateData['sikayet_alt_kategori_id'] = null;
                    $updateData['sikayet_alt_kategori_diger'] = $request->sikayet_alt_kategori_diger;
                } else {
                    $updateData['sikayet_alt_kategori_id'] = $request->sikayet_alt_kategori_id ?: null;
                    $updateData['sikayet_alt_kategori_diger'] = null;
                }
            } else {
                if ($request->sikayet_alt_kategori_id === null && !empty($request->sikayet_alt_kategori_diger)) {
                    $updateData['sikayet_alt_kategori_id'] = null;
                    $updateData['sikayet_alt_kategori_diger'] = $request->sikayet_alt_kategori_diger;
                } else {
                    $updateData['sikayet_alt_kategori_id'] = $request->sikayet_alt_kategori_id ?: null;
                    $updateData['sikayet_alt_kategori_diger'] = null;
                }
            }

            $sikayet->update($updateData);

            if ($sikayet->iaa_id) {
                $iaa = \App\Models\Iaa::find($sikayet->iaa_id);
                if ($iaa && isset($updateData['musteri_sikayet_konusu'])) {
                    $iaa->update(['baslik' => $updateData['musteri_sikayet_konusu']]);
                }
            }

            if ($tamYetki && $request->has('ek_yetkili_user_ids')) {
                $sikayet->ekYetkililer()->sync($request->ek_yetkili_user_ids);
            }

            $sikayet->teknikDetaylar()->delete();
            if ($request->has('lot_no') && is_array($request->lot_no)) {
                $count = count($request->lot_no);
                for ($i = 0; $i < $count; $i++) {
                    $lot = $request->lot_no[$i] ?? null;
                    $machine = $request->machine_id[$i] ?? null;
                    $hammadde = $request->genel_hammadde_id[$i] ?? null;
                    $versiyon = $request->urun_versiyonu_id[$i] ?? null;
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

            if ($request->has('dosyalar_sil')) {
                $dosyalar = MusteriSikayetiDosyasi::where('musteri_sikayeti_id', $sikayet->id)
                    ->whereIn('id', $request->input('dosyalar_sil'))
                    ->get();
                foreach ($dosyalar as $dosya) {
                    Storage::disk('public')->delete($dosya->dosya_yolu);
                    $dosya->delete();
                }
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
            return redirect()->route('admin.sikayetler.show', $sikayet)->with('success', 'Şikayet başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Şikayet güncelleme hatası: ' . $e->getMessage());
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function destroy(MusteriSikayeti $sikayet)
    {
        $this->authorize('delete', $sikayet);
        DB::beginTransaction();
        try {
            $sikayet->delete();
            \App\Models\MusteriLog::add(
                $sikayet->customer_id,
                'Şikayet Silme',
                auth()->user()->name . ', #' . $sikayet->id . ' nolu şikayeti sildi.'
            );
            DB::commit();
            return redirect()->route('admin.sikayetler.index')->with('success', 'Şikayet silindi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hata oluştu.');
        }
    }

    public function kurulGirdileri(Request $request)
    {
        $girisYapanKullanici = Auth::user();
        $boardRoles = [
            'Superadmin', 
            'Müşteri Şikayeti Kurulu', 
            'Müşteri Şikayeti Kurulu Yöneticisi', 
            'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 
            'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı',
            'Müşteri Şikayeti Kurulu - Yurt İçi',
            'Müşteri Şikayeti Kurulu - Yurt Dışı'
        ];

        if (!$girisYapanKullanici->hasRole($boardRoles)) {
            abort(403, 'Yetkiniz yok.');
        }

        // 1. Kullanıcının Yönettiği Ekibi Bulma
        if ($girisYapanKullanici->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
            $kurulUyeleri = User::role([
                'Müşteri Şikayeti Kurulu', 
                'Müşteri Şikayeti Kurulu - Yurt İçi', 
                'Müşteri Şikayeti Kurulu - Yurt Dışı'
            ])->orderBy('name')->get();
        } elseif ($girisYapanKullanici->hasRole('Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi')) {
            $kurulUyeleri = User::role(['Müşteri Şikayeti Kurulu - Yurt İçi'])->orderBy('name')->get();
        } elseif ($girisYapanKullanici->hasRole('Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı')) {
            $kurulUyeleri = User::role(['Müşteri Şikayeti Kurulu - Yurt Dışı'])->orderBy('name')->get();
        } else {
            // Normal üye ise sadece kendisini görebilir
            $kurulUyeleri = collect([$girisYapanKullanici]);
        }

        // 2. Takım Performans Raporu (Ekip performansı DTO'su hazırlama)
        $ekipPerformansi = [];
        $yediGunOnce = now()->subDays(7);

        foreach ($kurulUyeleri as $uye) {
            $baseQuery = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $uye->id);
            
            $toplam = (clone $baseQuery)->count();
            
            $hataliBildirim = (clone $baseQuery)->whereHas('iaaProjesi', function($q) {
                $q->where('durum', 'hatali_bildirim_olarak_kapatildi');
            })->count();

            $talepKapanan = (clone $baseQuery)->whereHas('iaaProjesi', function($q) {
                $q->where('durum', 'talep_olarak_kapatildi');
            })->count();

            $son7Gun = (clone $baseQuery)->where('created_at', '>=', $yediGunOnce)->count();

            $ekipPerformansi[] = (object)[
                'id' => $uye->id,
                'name' => $uye->name,
                'toplam' => $toplam,
                'hatali_bildirim' => $hataliBildirim,
                'talep_kapanan' => $talepKapanan,
                'son_7_gun' => $son7Gun
            ];
        }

        // 3. Alt Kısımdaki Detaylı Liste İçin Mevcut Mantık
        $isManager = $girisYapanKullanici->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']);
        $selectedUserId = $request->input('kullanici_id') ?? ($isManager ? 'all' : $girisYapanKullanici->id);

        $filteredQuery = MusteriSikayeti::query();
        if ($selectedUserId !== 'all') {
            $filteredQuery->where('olusturan_kurul_uyesi_id', $selectedUserId);
        } else {
            $filteredQuery->whereIn('olusturan_kurul_uyesi_id', $kurulUyeleri->pluck('id'));
        }

        $stats_filtrelenmis = [
            'toplam' => (clone $filteredQuery)->count(),
            'islemde' => (clone $filteredQuery)->where('musteri_durum', 'İşlemde')->count(),
            'cozulen' => (clone $filteredQuery)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->count(),
            'kategoriler' => (clone $filteredQuery)
                ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
                ->select('sikayet_kategorileri.ad', DB::raw('count(musteri_sikayetleri.id) as toplam'))
                ->groupBy('sikayet_kategorileri.ad')
                ->get()
        ];

        $sikayetler = $filteredQuery->with('olusturanKurulUyesi', 'cozumTakimi', 'sikayetKategori')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats_kisisel = [
            'toplam_benim_girdiklerim' => MusteriSikayeti::where('olusturan_kurul_uyesi_id', $girisYapanKullanici->id)->count(),
            'islemde_benim_girdiklerim' => MusteriSikayeti::where('olusturan_kurul_uyesi_id', $girisYapanKullanici->id)->where('musteri_durum', 'İşlemde')->count(),
            'cozulen_benim_girdiklerim' => MusteriSikayeti::where('olusturan_kurul_uyesi_id', $girisYapanKullanici->id)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->count(),
        ];

        return view('admin.sikayetler.kurul', compact('sikayetler', 'kurulUyeleri', 'selectedUserId', 'stats_filtrelenmis', 'stats_kisisel', 'ekipPerformansi', 'isManager'));
    }

    public function restore($id)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['Superadmin', 'Super Admin', 'SUPERADMIN', 'Yonetim', 'Yönetim'])) {
            abort(403, 'Yetkiniz yok.');
        }

        $sikayet = MusteriSikayeti::withTrashed()->findOrFail($id);
        $sikayet->restore();

        \App\Models\MusteriLog::add(
            $sikayet->customer_id,
            'Şikayet Geri Alma',
            $user->name . ' şikayeti geri aldı.'
        );

        return back()->with('success', 'Geri alındı.');
    }

    public function exportExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AktifSikayetlerExport($request), 'aktif-sikayetler-' . now()->format('d-m-Y') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $query = MusteriSikayeti::query()->with(['sikayetKategori', 'customer', 'iaaProjesi']);

        if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Yonetim'])) {
            // Hepsi
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi']) && $user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $query->whereIn('konum_tipi', ['Yurt İçi', 'Yurt Dışı']);
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
            $query->where('konum_tipi', 'Yurt İçi');
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $query->where('konum_tipi', 'Yurt Dışı');
        } else {
            $allowedBolumIds = $user->getAllowedBolumIds();
            $query->whereHas('sikayetKategori', function ($q) use ($allowedBolumIds) {
                if ($allowedBolumIds !== '*') {
                    $q->whereIn('bolum_id', $allowedBolumIds);
                }
            });
        }

        // === TÜM FİLTRELER (Livewire bileşenindeki applyFilters mantığının aynısı) ===
        $r = $request;

        // Durum Filtresi
        if ($r->filled('filtreDurum')) {
            $query->whereIn('musteri_durum', (array) $r->filtreDurum);
        } elseif ($r->filled('durum')) {
            // Geriye dönük uyumluluk (eski tek durum parametresi)
            $query->where('musteri_durum', $r->durum);
        }

        // Öncelik
        if ($r->filled('filtreOncelik')) {
            $query->whereIn('musteri_oncelik', (array) $r->filtreOncelik);
        }

        // Takım
        if ($r->filled('filtreTakim')) {
            $query->whereIn('atanan_cozum_takimi_id', (array) $r->filtreTakim);
        }

        // Müşteri Adı
        if ($r->filled('filtreMusteriAdi')) {
            $query->where('musteri_adi', 'like', '%' . $r->filtreMusteriAdi . '%');
        }

        // Konu
        if ($r->filled('filtreKonu')) {
            $query->where('musteri_sikayet_konusu', 'like', '%' . $r->filtreKonu . '%');
        }

        // Ekleyen
        if ($r->filled('filtreEkleyen')) {
            $query->whereIn('olusturan_kurul_uyesi_id', (array) $r->filtreEkleyen);
        }

        // Son Tarih (Hedef Çözüm Tarihi)
        if ($r->filled('filtreSonTarihBaslangic')) {
            $query->whereDate('musteri_cozum_son_tarihi', '>=', $r->filtreSonTarihBaslangic);
        }
        if ($r->filled('filtreSonTarihBitis')) {
            $query->whereDate('musteri_cozum_son_tarihi', '<=', $r->filtreSonTarihBitis);
        }

        // Kayıt Tarihi
        if ($r->filled('filtreKayitTarihBaslangic')) {
            $query->whereDate('created_at', '>=', $r->filtreKayitTarihBaslangic);
        }
        if ($r->filled('filtreKayitTarihBitis')) {
            $query->whereDate('created_at', '<=', $r->filtreKayitTarihBitis);
        }

        // Kategori
        if ($r->filled('filtreKategori')) {
            $query->whereIn('sikayet_kategorisi_id', (array) $r->filtreKategori);
        }

        // Konum Tipi
        if ($r->filled('filtreKonumTipi')) {
            $query->whereIn('konum_tipi', (array) $r->filtreKonumTipi);
        }

        // Puan Aralığı
        if ($r->filled('filtrePuanMin')) {
            $minPuan = filter_var($r->filtrePuanMin, FILTER_VALIDATE_FLOAT);
            if ($minPuan !== false) $query->where('musteri_puan', '>=', $minPuan);
        }
        if ($r->filled('filtrePuanMax')) {
            $maxPuan = filter_var($r->filtrePuanMax, FILTER_VALIDATE_FLOAT);
            if ($maxPuan !== false) $query->where('musteri_puan', '<=', $maxPuan);
        }

        // Proje Durumu
        if ($r->filled('filtreProjeDurumu')) {
            $query->whereHas('iaaProjesi', function ($subQ) use ($r) {
                $subQ->whereIn('durum', (array) $r->filtreProjeDurumu);
            });
        }

        // Bekleme Süresi
        if ($r->filled('filtreBeklemeMin')) {
            $query->where('created_at', '<=', now()->subDays($r->filtreBeklemeMin));
        }
        if ($r->filled('filtreBeklemeMax')) {
            $query->where('created_at', '>=', now()->subDays($r->filtreBeklemeMax));
        }

        // İadeli Filtre
        if ($r->filled('filtreIadeVar') && $r->filtreIadeVar) {
            $query->whereHas('iadeler')
                  ->whereIn('musteri_durum', ['Tamamlandı', 'Çözümlendi', 'Kapatıldı']);
        }

        // Ziyaretli Filtre
        if ($r->filled('filtreZiyaretVar') && $r->filtreZiyaretVar) {
            $query->whereHas('iaaProjesi', function ($subQ) {
                $subQ->where('visit_planned', true);
            });
        }

        // Aktif Sekme Filtresi
        if ($r->filled('activeTab') && $r->activeTab !== 'tumu') {
            $tab = $r->activeTab;
            if ($tab === 'yeni') {
                $query->whereIn('musteri_durum', ['Yeni']);
            } elseif ($tab === 'islemde') {
                $islemdeDurumlar = ['İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor', 'Revize', 'Beklemede',
                    'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor',
                    'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin'];
                $query->where(function ($q) use ($islemdeDurumlar) {
                    $q->whereIn('musteri_durum', $islemdeDurumlar)
                      ->orWhereHas('iaaProjesi', fn($p) => $p->whereIn('durum', $islemdeDurumlar));
                });
            } elseif ($tab === 'cozulmus') {
                $query->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
                    ->whereDoesntHave('iaaProjesi', fn($q) => $q->whereIn('durum', ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']));
            } elseif ($tab === 'talep_kapali') {
                $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'));
            } elseif ($tab === 'hatali_bildirim') {
                $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'));
            } elseif ($tab === 'onay_bekleyenler') {
                $query->whereHas('iaaProjesi', fn($q) => $q->whereIn('durum', [
                    'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor',
                    'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin',
                    'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'
                ]));
            } elseif ($tab === 'iptal') {
                $query->whereIn('musteri_durum', ['İptal Edildi', 'Reddedildi', 'Tamamlanması Reddedildi']);
            }
        }

        $sikayetler = $query->latest()->get();
        $tarih = now()->format('d.m.Y H:i');

        // [OPTIMIZATION] Production-ready DomPDF settings
        $options = [
            'tempDir' => public_path('storage/tmp'),
            'chroot'  => public_path(),
        ];

        if (!file_exists(public_path('storage/tmp'))) {
            mkdir(public_path('storage/tmp'), 0755, true);
        }

        $viewName = 'admin.reports.pdf.aktif-sikayet-listesi';
        if (!\View::exists($viewName)) {
            Log::error("PDF View Not Found: {$viewName}");
            return back()->with('error', 'PDF şablonu bulunamadı. Lütfen sistem yöneticisine danışın.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, compact('sikayetler', 'tarih'))
            ->setPaper('a4', 'landscape')
            ->setOptions($options);

        return $pdf->download('aktif-sikayetler-' . now()->format('d-m-Y') . '.pdf');
    }
}
