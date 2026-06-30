<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Iaa;
use App\Models\Takim;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\IaaWorkflow;
use App\Models\IaaLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User; // <-- BU SATIRI EKLEYİN
use App\Models\MusteriSikayeti; // <-- BU SATIRI DA EKLEYİN (Şikayet güncellemesi için şart)
use Illuminate\Support\Facades\Notification; // <-- EKLE
use App\Notifications\ProjeDurumuDegisti; // <-- EKLE
use App\Notifications\IaaTalebiSonuclandi; // <-- EKLENDİ
use Illuminate\Support\Facades\Http;

class IaaYonetimController extends Controller
{

    /**
     * ====================================================================
     * TÜM İAA'LARI DURUMLARINA GÖRE LİSTELER (GÜNCELLENMİŞ HALİ)
     * ====================================================================
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // --- YETKİ KONTROLÜ ---
        if (!$user->hasRole(['Superadmin', 'Direktör', 'Bölüm Kalite Yöneticisi']))
        {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        // 1. Değişkenleri BOŞ KOLEKSİYON olarak başlat (Güvenlik için kritik)
        $onayBekleyenKullanicilar = collect();
        $onayBekleyenMisafirler = collect();
        $talepAlanOneriler = collect();
        $atanmisOlanlar = collect();
        $havuzdakiler = collect();
        $reddedilenler = collect();
        $bolumOnayiBekleyenler = collect();
        $bolumYoneticisiOnayladiklari = collect(); // <-- BUNU EKLEYİN (EKSİK OLAN BU)
        $superadminOnayladiklari = collect();      // <-- BUNU DA EKLEYİN (Lazım olacak)
        $yoneticiOnayiBekleyenler = collect();
        $direktorOnayiBekleyenler = collect(); // <-- EKLENDİ
        $tamamlanmasiReddedilenler = collect();
        $sonTamamlananlar = collect();

        // === SENARYO 1: SUPERADMIN (HER ŞEYİ GÖRÜR) ===
        if ($user->hasRole('Superadmin'))
        {
            $onayBekleyenKullanicilar = Iaa::sadeceOneriler()->where('durum', 'Onay Bekliyor')->whereNotNull('gonderen_user_id')->with('gonderen', 'bolum')->latest()->get();
            $onayBekleyenMisafirler = Iaa::sadeceOneriler()->where('durum', 'Onay Bekliyor')->whereNull('gonderen_user_id')->with('gonderen', 'bolum')->latest()->get();

            $talepAlanOneriler = Iaa::sadeceOneriler()->where('durum', 'Havuzda')->has('talepEdenTakimlar')->withCount('talepEdenTakimlar')->latest()->get();
            $havuzdakiler = Iaa::sadeceOneriler()->where('durum', 'Havuzda')->doesntHave('talepEdenTakimlar')->with('gonderen', 'bolum', 'onaylayan')->latest()->get();

            $atanmisOlanlar = Iaa::sadeceOneriler()->whereIn('durum', ['Atandı', 'Revize Ediliyor'])->with('gonderen', 'bolum', 'atananTakim')->latest()->get();

            $reddedilenler = Iaa::sadeceOneriler()->where('durum', 'Reddedildi')->with('gonderen', 'bolum', 'onaylayan')->latest()->get();

            $bolumOnayiBekleyenler = Iaa::sadeceOneriler()->where('durum', 'Bölüm Onayı Bekliyor')->with('gonderen', 'bolum', 'atananTakim', 'musteriSikayeti.sikayetKategori')->latest()->get();
            $direktorOnayiBekleyenler = Iaa::sadeceOneriler()->where('durum', 'Direktör Onayı Bekliyor')->with('gonderen', 'bolum', 'atananTakim', 'musteriSikayeti.sikayetKategori')->latest()->get();
            $yoneticiOnayiBekleyenler = Iaa::sadeceOneriler()->where('durum', 'Yönetici Onayı Bekliyor')->with('gonderen', 'bolum', 'atananTakim', 'workflow.steps')->latest()->get();

            $tamamlanmasiReddedilenler = Iaa::sadeceOneriler()->where('durum', 'Tamamlanması Reddedildi')->with('gonderen', 'bolum', 'atananTakim')->latest()->get();
            $sonTamamlananlar = Iaa::sadeceOneriler()->where('durum', 'Tamamlandı')->orderBy('onaylanma_tarihi', 'desc')->take(5)->get();

            // === YENİ: SUPERADMIN ONAYLADIKLARI (Final Onayı Verdikleri) ===
            $superadminOnayladiklari = Iaa::sadeceOneriler()->where('durum', 'Tamamlandı')
                ->where('onaylayan_user_id', $user->id)
                ->with('gonderen', 'bolum', 'atananTakim')
                ->latest('onaylanma_tarihi')
                ->get();
        }

        // === SENARYO 3: DİREKTÖR (KENDİ BÖLÜMLERİNİ GÖRÜR) ===
        else if ($user->hasRole('Direktör'))
        {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();

            // --- YENİ: HENÜZ ONAY BEKLEYEN (TRIYAJ) ÖNERİLER ---
            $onayBekleyenKullanicilar = Iaa::sadeceOneriler()->where('durum', 'Onay Bekliyor')
                ->whereIn('bolum_id', $yonetilenBolumIds)
                ->whereNotNull('gonderen_user_id')->with('gonderen', 'bolum')->latest()->get();

            $onayBekleyenMisafirler = Iaa::sadeceOneriler()->where('durum', 'Onay Bekliyor')
                ->whereIn('bolum_id', $yonetilenBolumIds)
                ->whereNull('gonderen_user_id')->with('gonderen', 'bolum')->latest()->get();

            // 1. Direktör Onayı Bekleyenler (ONAY VERİLEBİLİR)
            $direktorOnayiBekleyenler = Iaa::sadeceOneriler()->where('durum', 'Direktör Onayı Bekliyor')
                ->whereIn('bolum_id', $yonetilenBolumIds)
                ->with('gonderen', 'bolum', 'atananTakim')
                ->latest()
                ->get();

            // 2. Bölüm Onayı Bekleyenler (READ-ONLY TAKİP)
            $bolumOnayiBekleyenler = Iaa::sadeceOneriler()->where('durum', 'Bölüm Onayı Bekliyor')
                ->whereIn('bolum_id', $yonetilenBolumIds)
                ->with('gonderen', 'bolum', 'atananTakim', 'musteriSikayeti.sikayetKategori')
                ->latest()
                ->get();

            // 3. Yönetici (Final) Onayı Bekleyenler (READ-ONLY TAKİP)
            $yoneticiOnayiBekleyenler = Iaa::sadeceOneriler()->where('durum', 'Yönetici Onayı Bekliyor')
                ->whereIn('bolum_id', $yonetilenBolumIds)
                ->with('gonderen', 'bolum', 'atananTakim')
                ->latest()
                ->get();

            // 4. Atanmış / İşlemde Olanlar
            $atanmisOlanlar = Iaa::sadeceOneriler()->whereIn('durum', ['Atandı', 'Revize Ediliyor'])
                ->whereIn('bolum_id', $yonetilenBolumIds)
                ->with('gonderen', 'bolum', 'atananTakim')
                ->latest()
                ->get();

            // 5. Reddedilenler
            $reddedilenler = Iaa::sadeceOneriler()->whereIn('durum', ['Reddedildi', 'Tamamlanması Reddedildi'])
                ->whereIn('bolum_id', $yonetilenBolumIds)
                ->with('gonderen', 'bolum', 'onaylayan')
                ->latest()
                ->get();

            // 6. Tamamlananlar
            $sonTamamlananlar = Iaa::sadeceOneriler()->where('durum', 'Tamamlandı')
                ->whereIn('bolum_id', $yonetilenBolumIds)
                ->orderBy('onaylanma_tarihi', 'desc')
                ->take(10)
                ->get();
        }

        // === SENARYO 2: BÖLÜM KALİTE YÖNETİCİSİ (SADECE KENDİ BÖLÜMÜNÜ GÖRÜR) ===
        else
        {
            // Kullanıcının sorumlu olduğu kategori ID'lerini al
            $sorumluKategoriler = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();

            // Bölüm kalite yöneticisinin sorumlu olduğu bölümleri bul
            $sorumluBolumler = \App\Models\SikayetKategori::whereIn('id', $sorumluKategoriler)->pluck('bolum_id')->toArray();
            if ($user->bolum_id)
            {
                $sorumluBolumler[] = $user->bolum_id;
            }
            $sorumluBolumler = array_unique(array_filter($sorumluBolumler));

            // Ortak Filtre Fonksiyonu: Sadece saf İAA'lar ve ilgili bölümler
            $applyFilter = function ($query) use ($sorumluBolumler)
            {
                return $query->sadeceOneriler()->whereIn('bolum_id', $sorumluBolumler);
            };

            // 1. Bölüm Onayı Bekleyenler (GÖRMELİ)
            $bolumOnayiBekleyenler = $applyFilter(
                Iaa::where('durum', 'Bölüm Onayı Bekliyor')
                    ->with('gonderen', 'bolum', 'atananTakim', 'musteriSikayeti.sikayetKategori')
                    ->latest()
            )->get();

            // 2. Atanmış / Revize Edilenler (GÖRMELİ - Takip İçin)
            $atanmisOlanlar = $applyFilter(
                Iaa::whereIn('durum', ['Atandı', 'Revize Ediliyor'])
                    ->with('gonderen', 'bolum', 'atananTakim')
                    ->latest()
            )->get();

            // 3. Tamamlanması Reddedilenler (GÖRMELİ)
            $tamamlanmasiReddedilenler = $applyFilter(
                Iaa::where('durum', 'Tamamlanması Reddedildi')
                    ->with('gonderen', 'bolum', 'atananTakim')
                    ->latest()
            )->get();

            // 4. Tamamlananlar (GÖRMELİ)
            $sonTamamlananlar = $applyFilter(
                Iaa::where('durum', 'Tamamlandı')
                    ->orderBy('onaylanma_tarihi', 'desc')
                    ->take(5)
            )->get();

            // 5. Reddedilenler (YENİ EKLENDİ - GÖRMELİ)
            // En başta reddedilen önerileri de kendi bölümündense görsün.
            $reddedilenler = $applyFilter(
                Iaa::where('durum', 'Reddedildi')
                    ->with('gonderen', 'bolum', 'onaylayan')
                    ->latest()
            )->get();

            // === 6. YENİ: ONAYLADIKLARIM (Bölüm Yöneticisi Onaylamış, Üst Yönetici Bekliyor) ===
            // Serkan'ın onaylayıp gönderdiği projeler bunlardır.
            $bolumYoneticisiOnayladiklari = $applyFilter(
                Iaa::where('durum', 'Yönetici Onayı Bekliyor')
                    ->with('gonderen', 'bolum', 'atananTakim', 'musteriSikayeti.sikayetKategori')
                    ->latest()
            )->get();
        }

        // === ARAMA DESTEĞİ (Kullanıcı talebi: Dashboard'daki projeye tıklandığında filtreleme) ===
        if ($request->filled('search'))
        {
            $searchTerm = mb_strtolower($request->search, 'UTF-8');
            $filterBySearch = function ($collection) use ($searchTerm)
            {
                return $collection->filter(function ($item) use ($searchTerm)
                {
                    return str_contains(mb_strtolower($item->baslik, 'UTF-8'), $searchTerm);
                });
            };

            $onayBekleyenKullanicilar = $filterBySearch($onayBekleyenKullanicilar);
            $onayBekleyenMisafirler = $filterBySearch($onayBekleyenMisafirler);
            $talepAlanOneriler = $filterBySearch($talepAlanOneriler);
            $atanmisOlanlar = $filterBySearch($atanmisOlanlar);
            $havuzdakiler = $filterBySearch($havuzdakiler);
            $reddedilenler = $filterBySearch($reddedilenler);
            $bolumOnayiBekleyenler = $filterBySearch($bolumOnayiBekleyenler);
            $direktorOnayiBekleyenler = $filterBySearch($direktorOnayiBekleyenler);
            $yoneticiOnayiBekleyenler = $filterBySearch($yoneticiOnayiBekleyenler);
            $tamamlanmasiReddedilenler = $filterBySearch($tamamlanmasiReddedilenler);
            $sonTamamlananlar = $filterBySearch($sonTamamlananlar);
        }

        // 5. ONAY TOPLAM HESAPLA (Blade'deki hatayı gidermek için)
        if ($user->hasRole('Superadmin'))
        {
            $onayToplam = $bolumOnayiBekleyenler->count() + $direktorOnayiBekleyenler->count() + $onayBekleyenMisafirler->count() + $onayBekleyenKullanicilar->count() + $yoneticiOnayiBekleyenler->count();
        }
        else if ($user->hasRole('Direktör'))
        {
            $onayToplam = $direktorOnayiBekleyenler->count(); // Direktör sadece kendi onaylayacaklarını sayar
        }
        else
        {
            $onayToplam = $bolumOnayiBekleyenler->count(); // Bölüm yöneticisi sadece kendi onaylayacaklarını sayar
        }

        // İSTATİSTİK KARTLARI (GERİ YÜKLENDİ - Önceki düzenlemede yanlışlıkla silinmişti)
        $stats = [
            'onayBekleyen' => $user->hasRole('Superadmin') ? ($onayBekleyenKullanicilar->count() + $onayBekleyenMisafirler->count()) : 0,
            'talepAlan' => $user->hasRole('Superadmin') ? $talepAlanOneriler->count() : 0,
            'havuzda' => $user->hasRole('Superadmin') ? $havuzdakiler->count() : 0,
            'yoneticiOnayi' => $user->hasRole('Superadmin') ? $yoneticiOnayiBekleyenler->count() : 0,

            'bolumOnayi' => $bolumOnayiBekleyenler->count(),
            'atanmis' => $atanmisOlanlar->count(),

            'tamamlanan' => $user->hasRole('Superadmin')
                ? Iaa::sadeceOneriler()->where('durum', 'Tamamlandı')->count()
                : $sonTamamlananlar->count(),

            'reddedilen' => ($reddedilenler->count() + $tamamlanmasiReddedilenler->count()),
        ];

        return view('admin.iaa-yonetim.index', compact(
            'onayBekleyenKullanicilar',
            'onayBekleyenMisafirler',
            'talepAlanOneriler',
            'atanmisOlanlar',
            'havuzdakiler',
            'reddedilenler',
            'bolumOnayiBekleyenler',
            'yoneticiOnayiBekleyenler',
            'direktorOnayiBekleyenler',
            'tamamlanmasiReddedilenler',
            'sonTamamlananlar',
            'bolumYoneticisiOnayladiklari',
            'superadminOnayladiklari',
            'onayToplam',
            'stats'
        ));
    }

    /**
     * ====================================================================
     * YENİ: BİR İAA'YA GELEN TÜM TALEPLERİ LİSTELER
     * ====================================================================
     */
    public function talepleriGoster(Iaa $iaa)
    {
        // Talepleri, takımları, takım liderlerini VE takım üyelerini önceden yüklüyoruz.
        $iaa->load('talepEdenTakimlar.lider', 'talepEdenTakimlar.uyeler');

        return view('admin.iaa-yonetim.talepler', compact('iaa'));
    }

    /**
     * Bir İAA'yı bir takıma atamak için gerekli olan formu gösterir.
     * Bu formda akış şablonu ve bitiş tarihi seçilir.
     */
    public function atamaFormuGoster(Iaa $iaa, Takim $takim)
    {
        // Formdaki dropdown menüyü doldurmak için tüm akış şablonlarını alıyoruz.
        $workflows = IaaWorkflow::all();

        return view('admin.iaa-yonetim.atama-formu', compact('iaa', 'takim', 'workflows'));
    }

    /**
     * Formdan gelen bilgilerle İAA'yı takıma atar ve projeyi başlatır.
     */

    public function atamaYap(Request $request, Iaa $iaa, Takim $takim)
    {
        $validated = $request->validate([
            'iaa_workflow_id' => 'required|exists:iaa_workflows,id',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        DB::transaction(function () use ($iaa, $takim, $validated)
        {
            // 1. İAA'nın kendi durumunu ve atanan takım ID'sini güncelle.
            $iaa->update([
                'durum' => 'Atandı',
                'atanan_takim_id' => $takim->id,
                'atamadaki_lider_id' => $takim->lider_user_id,
            ]);

            // 2. Takım ve İAA arasındaki pivot kaydını Eloquent ilişkisi üzerinden güncelle.
            // Bu yöntem, kolon adlarını bizim tahmin etmemize gerek bırakmaz.
            // Laravel doğru kolon adlarını ('iaa_id', 'takim_id' veya farklı bir şey) kendisi bulur.
            // 2. Takım ve İAA arasındaki pivot kaydını Eloquent ilişkisi üzerinden güncelle.
            $workflow = IaaWorkflow::with('steps')->find($validated['iaa_workflow_id']);
            $stepsSnapshot = $workflow ? $workflow->steps->toArray() : null;

            $takim->talepEttigiIaalar()->updateExistingPivot($iaa->id, [
                'iaa_workflow_id' => $validated['iaa_workflow_id'],
                'workflow_snapshot' => $stepsSnapshot ? json_encode($stepsSnapshot) : null,
                'start_date' => now(),
                'due_date' => $validated['due_date'],
                'status' => 'Devam Ediyor',
            ]);

            // 3. Diğer takımların taleplerini, yine ilişki üzerinden, pivot tablodan kaldır (detach).
            // Önce diğer talep eden takımların ID'lerini alalım.
            $digerTakimIdleri = $iaa->talepEdenTakimlar()->where('takim_id', '!=', $takim->id)->pluck('takim_id');
            // Sonra bu takımların bu İAA ile olan bağını koparalım.
            if ($digerTakimIdleri->isNotEmpty())
            {
                $iaa->talepEdenTakimlar()->detach($digerTakimIdleri);
            }
        });

        // === BİLDİRİM ===
        try {
            Notification::send($takim->uyeler->merge([$takim->lider]), new IaaTalebiSonuclandi($iaa, $takim, 'onaylandi'));
        } catch (\Exception $e) {
            Log::error("İAA Atama bildirimi gönderilemedi: " . $e->getMessage());
            \App\Helpers\MailLogHelper::logFailure($iaa, "İAA Atama Bildirimi", $takim->uyeler->merge([$takim->lider]), $e->getMessage());
        }

        return redirect()->route('admin.iaa-yonetim.index')
            ->with('success', '"' . $iaa->baslik . '" projesi, ' . $takim->ad . ' takımına başarıyla atandı.');
    }

    /**
     * TALEBİ REDDETME (SİLME) METODU
     */
    public function talepReddet(Iaa $iaa, Takim $takim)
    {
        // Yetki kontrolü (Superadmin olmalı)
        if (!auth()->user()->hasRole('Superadmin'))
        {
            abort(403);
        }

        // İAA ve Takım arasındaki talebi kaldır (pivot tablodan siler)
        $iaa->talepEdenTakimlar()->detach($takim->id);

        // === BİLDİRİM ===
        try {
            Notification::send($takim->uyeler->merge([$takim->lider]), new IaaTalebiSonuclandi($iaa, $takim, 'reddedildi'));
        } catch (\Exception $e) {
            Log::error("İAA Talep Red bildirimi gönderilemedi: " . $e->getMessage());
            \App\Helpers\MailLogHelper::logFailure($iaa, "İAA Talep Red Bildirimi", $takim->uyeler->merge([$takim->lider]), $e->getMessage());
        }

        return redirect()->back()->with('success', $takim->ad . ' takımının talebi reddedildi.');
    }

    /**
     * ====================================================================
     * BİR İAA ÖNERİSİNİ ONAYLAR (YENİ VEYA TAMAMLANMIŞ)
     * ====================================================================
     * GÜNCELLENDİ: Artık hem yeni önerileri havuza ekliyor, hem de
     * tamamlanmış projeleri onaylayıp puanını takıma ekliyor.
     */
    public function onayla(Request $request, Iaa $iaa)
    {
        // --- KONTROL: Eğer proje zaten onaylanmışsa (Havuzda, Atandı vs.) işlem yapma ---
        if ($iaa->durum !== 'Onay Bekliyor')
        {
            return redirect()->route('admin.iaa-yonetim.index')
                ->with('error', 'Bu öneri zaten onaylanmış veya farklı bir statüde.');
        }
        // --- KONTROL SONU ---


        // --- MEVCUT KODUNUZ (YENİ ÖNERİLER İÇİN ÇALIŞMAYA DEVAM EDECEK) ---
        // Eğer durum "Yönetici Onayı Bekliyor" değilse, bu yeni bir öneridir.
        $validated = $request->validate([
            'risk' => 'nullable|integer|between:1,5',
            'kazanc_miktar' => 'nullable|numeric|min:0',
            'kazanc_birim' => 'nullable|string|max:10',
            'butce_miktar' => 'nullable|numeric|gt:0', // 0'a bölünme hatasını önler
            'butce_birim' => 'nullable|string|max:10',
            'yil_baz' => 'nullable|integer|min:1|max:50',
        ]);

        $puan = 0; // Puanı başlangıçta 0 yapalım
        $yilBaz = $request->input('yil_baz', 5) ?: 5;

        // 2. Eğer kullanıcı tüm alanları doldurduysa puanı hesapla
        if ($request->filled('risk') && $request->filled('kazanc_miktar') && $request->filled('butce_miktar'))
        {
            $puan = round(($validated['risk'] * $validated['kazanc_miktar'] * $yilBaz) / $validated['butce_miktar']);
        }
        // 3. Eğer alanlar boş bırakıldıysa, sistem ayarlarından standart puanı çek
        else
        {
            // Veritabanından 'standart_puan' ayarını al, bulamazsan varsayılan olarak 100 kullan.
            $standartPuanAyar = \App\Models\Setting::where('key', 'standart_puan')->first();
            $puan = $standartPuanAyar->value ?? 100;

            // Boş bırakılan değerleri veritabanında null olarak ayarlayalım.
            $validated['risk'] = null;
            $validated['kazanc_miktar'] = null;
            $validated['butce_miktar'] = null;
        }

        // 4. Veritabanını yeni değerlerle güncelle
        $iaa->durum = 'Havuzda';
        $iaa->onaylayan_user_id = auth()->id();
        $iaa->onaylanma_tarihi = now();
        $iaa->risk = $validated['risk'];
        $iaa->kazanc_miktar = $validated['kazanc_miktar'];
        $iaa->kazanc_birim = $request->kazanc_birim; // null olabilir
        $iaa->butce_miktar = $validated['butce_miktar'];
        $iaa->butce_birim = $request->butce_birim; // null olabilir
        $iaa->yil_baz = $yilBaz;
        $iaa->puan = $puan;
        $iaa->save();

        return redirect()->route('admin.iaa-yonetim.index')->with('success', 'İAA önerisi başarıyla onaylandı ve havuza eklendi.');
    }


    /**
     * ====================================================================
     * BİR İAA ÖNERİSİNİ GEREKÇEYLE REDDEDER
     * ====================================================================
     */
    public function reddet(Request $request, Iaa $iaa)
    {
        try
        {
            $request->validate(['yonetici_notu' => 'required|string|min:10']);
        }
        catch (ValidationException $e)
        {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error_modal_id', $iaa->id);
        }
        $iaa->durum = 'Reddedildi';
        $iaa->onaylayan_user_id = auth()->id();
        $iaa->yonetici_notu = $request->yonetici_notu;
        $iaa->save();
        return redirect()->route('admin.iaa-yonetim.index')->with('success', 'İAA önerisi başarıyla reddedildi.');
    }

    /**
     * ====================================================================
     * BİR İŞLEMİ GERİ ALIR (Geliştirilmiş Versiyon)
     * Projenin mevcut durumuna göre bir önceki mantıksal adıma döner.
     * ====================================================================
     */
    public function geriAl(Iaa $iaa)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $oncekiDurum = $iaa->durum;
        $yeniDurum = null;

        // Loglama için Auth ve Log modelini import ettiğinizden emin olun.


        DB::transaction(function () use ($iaa, $oncekiDurum, &$yeniDurum)
        {

            switch ($oncekiDurum)
            {

                case 'Tamamlandı':
                    $takim = $iaa->atananTakim;

                    // Puan verilmişse geri al
                    if ($takim && $iaa->puan > 0)
                    {

                        // 1. TAKIMIN KENDİ HANESİNDEN PUANI DÜŞ (Bu kalmalı, takım puanı her zaman işler)
                        $takim->decrement('toplam_puan', $iaa->puan);

                        $isSafIaa = !$iaa->musteriSikayeti;

                        // 2. KULLANICI PUANLARINI DÜŞ
                        if ($isSafIaa)
                        {
                            // Kural: Saf İAA ise daveti kabul etmiş tüm üyelerden puanı düş
                            $uyeIdleri = $takim->uyeler()->pluck('users.id');
                            User::whereIn('id', $uyeIdleri)->decrement('toplam_puan', $iaa->puan);
                        }
                        else
                        {
                            // Kural: Şikayet kaynaklı ise sadece görevli (Squad) üyelerden puanı düş
                            $squadUyeIdleri = $iaa->projeEkibi()->pluck('users.id');
                            User::whereIn('id', $squadUyeIdleri)->decrement('toplam_puan', $iaa->puan);
                        }
                    }

                    // Tarihleri sıfırla
                    $iaa->onaylanma_tarihi = null;
                    $iaa->tamamlanma_tarihi = null;

                    $yeniDurum = 'Yönetici Onayı Bekliyor';
                    break;


                // =========================
                case 'Tamamlanması Reddedildi':
                    $yeniDurum = 'Yönetici Onayı Bekliyor';
                    break;
                // =========================   

                // ===== YENİ EKLENEN CASE =====
                case 'Revize Ediliyor':
                    $yeniDurum = 'Yönetici Onayı Bekliyor';
                    break;
                // =============================

                case 'Atandı':
                    // BURASI SİZİN İSTEDİĞİNİZ AYRIMI YAPIYOR:
                    // Eğer bu proje daha önce tamamlanıp revizyona gönderildiyse, 
                    // "Yönetici Onayı Bekliyor" durumuna döner.
                    if ($iaa->iaaTalebi && $iaa->iaaTalebi->status === 'Tamamlandı')
                    {
                        $yeniDurum = 'Yönetici Onayı Bekliyor';

                        // Eğer proje havuzdan yeni atandıysa,
                        // Sizin kodunuzdaki gibi havuza geri döner ve talep kaydı silinir.
                    }
                    else
                    {
                        $iaa->atanan_takim_id = null;
                        DB::table('iaa_talepleri')->where('iaa_id', $iaa->id)->delete();
                        $yeniDurum = 'Havuzda';
                    }
                    break;

                case 'Reddedildi':
                    // Sizin de belirttiğiniz gibi, reddedilen bir önerinin önceki durumu farklı olabilir.
                    // Eğer proje daha önce bir takıma atanmışsa, "Yönetici Onayı Bekliyor"a döner.
                    if ($iaa->atanan_takim_id)
                    {
                        $yeniDurum = 'Yönetici Onayı Bekliyor';

                        // Eğer hiç atanmamışsa, ilk baştaki "Onay Bekliyor" durumuna döner ve puan vs. sıfırlanır.
                    }
                    else
                    {
                        $iaa->onaylayan_user_id = null;
                        $iaa->onaylanma_tarihi = null;
                        $iaa->yonetici_notu = null;
                        $iaa->risk = null;
                        $iaa->kazanc_miktar = null;
                        $iaa->butce_miktar = null;
                        $iaa->puan = null;
                        $yeniDurum = 'Onay Bekliyor';
                    }
                    break;

                case 'Havuzda':
                    // Sizin kodunuzdaki gibi, havuzdaki bir öneri en başa, "Onay Bekliyor"a döner ve tüm puanları sıfırlanır.
                    $iaa->onaylayan_user_id = null;
                    $iaa->onaylanma_tarihi = null;
                    $iaa->yonetici_notu = null;
                    $iaa->risk = null;
                    $iaa->kazanc_miktar = null;
                    $iaa->butce_miktar = null;
                    $iaa->puan = null;
                    $yeniDurum = 'Onay Bekliyor';
                    break;
            }

            if ($yeniDurum)
            {
                $iaa->durum = $yeniDurum;
                $iaa->save();

                IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'İşlem Geri Alındı',
                    'aciklama' => "Proje, '{$oncekiDurum}' durumundan '{$yeniDurum}' durumuna geri alındı."
                ]);

                // Takvim Ziyaret kilidini yeni duruma göre güncelle
                $readonlyStates = ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Tamamlandı', 'Talep Olarak Kapatıldı'];
                $isNowReadOnly = in_array($yeniDurum, $readonlyStates);
                $this->lockVisitInTakvim($iaa->id, $isNowReadOnly);

                // Şikayet Durumu Senkronizasyonu
                if ($iaa->musteriSikayeti) {
                    $iaa->musteriSikayeti->update(['musteri_durum' => $yeniDurum]);
                }
            }
        });

        if ($yeniDurum)
        {
            // DÜZELTME: redirect()->route(...) yerine back() kullanıyoruz.
            // Böylece admin panelinden bastıysa oraya, proje sayfasından bastıysa oraya döner.
            return back()->with('success', 'İşlem başarıyla geri alındı.');
        }

        return back()->with('error', 'Bu durum için geri alma işlemi tanımlanmamış.');
    }

    /**
     * YENİ: BÖLÜM KALİTE YÖNETİCİSİ ONAYI (ARA ONAY)
     * Bu işlem puan dağıtmaz, projeyi "Süper Yönetici" onayına (Yönetici Onayı Bekliyor) sunar.
     */
    public function bolumOnayiVer(Request $request, Iaa $iaa)
    {
        $user = Auth::user();

        // 1. Durum Kontrolü: Proje gerçekten bu aşamada mı?
        if ($iaa->durum !== 'Bölüm Onayı Bekliyor')
        {
            return back()->with('error', 'Bu proje bölüm onayı aşamasında değil.');
        }

        // 2. Yetki Kontrolü: Superadmin her şeyi onaylar. Diğerleri kontrol edilir.
        if (!$user->hasRole('Superadmin'))
        {

            // Proje bir şikayete bağlı değilse veya kategorisi yoksa hata ver (veya yetkisiz say)
            if (!$iaa->musteriSikayeti || !$iaa->musteriSikayeti->sikayet_kategorisi_id)
            {
                return back()->with('error', 'Bu proje bir şikayet kategorisine bağlı olmadığı için onaylanamaz.');
            }

            $projeKategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;

            // Kullanıcının yetkili olduğu kategori ID'lerini al
            // (User modeline eklediğimiz 'yonettigiSikayetKategorileri' ilişkisini kullanıyoruz)
            $yetkiliKategoriler = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();

            // Kontrol et
            if (!in_array($projeKategoriId, $yetkiliKategoriler))
            {
                return back()->with('error', 'Bu kategorideki projeleri onaylama yetkiniz yok.');
            }
        }

        // 3. Durumu Güncelle -> Şikayet kaynaklı projeler için Direktör varsa ona, yoksa Süper Yöneticiye
        $yeniDurum = 'Yönetici Onayı Bekliyor';
        $logMesaji = 'tarafından ara onay verildi, proje son onay için üst yönetime iletildi.';
        $bildirimMesajiSon = "Yönetici onayı bekleniyor.";

        // Direktör kullanıcı nesnesini burada tutacağız
        $direktorUser = null;

        // KURAL: Sadece Müşteri Şikayeti kaynaklı projelerde direktör onayı olacak (Kullanıcı talebi)
        if ($iaa->musteriSikayeti)
        {
            // YENİ: Sistem ayarlarından direktör onayının aktif olup olmadığını kontrol et
            $direktorOnayiAyari = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->first();
            $direktorOnayiAktif = $direktorOnayiAyari ? $direktorOnayiAyari->value == '1' : false;

            if ($direktorOnayiAktif)
            {
                // İlişkiler üzerinden bölüm ve direktörü güvenli şekilde alalım
                $bolum = null;
                // Şikayet ve Kategori ilişkisinin loaded olup olmadığını kontrol etmeden erişebiliriz çünkü model lazy load yapar
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori)
                {
                    $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum;
                }

                if ($bolum && $bolum->director_id)
                {
                    // Direktör kullanıcısının varlığını kontrol et
                    $direktorUser = $bolum->director; // BelongsTo ilişkisi

                    if ($direktorUser)
                    {
                        $yeniDurum = 'Direktör Onayı Bekliyor';
                        $logMesaji = 'tarafından ara onay verildi, proje direktör onayına sunuldu.';
                        $bildirimMesajiSon = "Direktör onayı bekleniyor.";
                    }
                }
            }
        }

        // NOT: İlgili alan validasyonu (opsiyonel olduğu için 'nullable' ama string olmalı)
        $request->validate(['not' => 'nullable|string']);

        $iaa->update([
            'durum' => $yeniDurum,
            'yonetici_notu' => $request->not, // YENİ: Yönetici notunu kaydet
            'durum_degistirme_tarihi' => now(),
        ]);

        // Şikayet Durumu Senkronizasyonu
        if ($iaa->musteriSikayeti) {
            $iaa->musteriSikayeti->update(['musteri_durum' => $yeniDurum]);
        }


        // 4. Log Kaydı
        $ekAciklama = $request->not ? " (Not: {$request->not})" : "";
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Bölüm Onayı Verildi',
            'aciklama' => $user->name . " " . $logMesaji . $ekAciklama
        ]);

        // === 5. BİLDİRİM ZİNCİRİ (GÜNCELLENDİ) ===
        try
        {
            // A) Takım Liderine Bildirim
            $takim = $iaa->atananTakim;
            if ($takim && $takim->lider)
            {
                Notification::send(
                    $takim->lider,
                    new ProjeDurumuDegisti(
                        $iaa,
                        "bölüm yöneticisi tarafından onaylandı",
                        $bildirimMesajiSon
                    )
                );
            }

            // B) EĞER DİREKTÖR ONAYINA GİTTİYSE -> DİREKTÖRE BİLDİRİM (YENİ)
            if ($yeniDurum == 'Direktör Onayı Bekliyor' && $direktorUser)
            {
                Notification::send(
                    $direktorUser,
                    new ProjeDurumuDegisti(
                        $iaa,
                        "onayınızı beklemektedir",
                        "Bölüm yöneticisi {$user->name} tarafından onaylanmıştır."
                    )
                );
            }

            // C) EĞER YÖNETİCİ ONAYINA GİTTİYSE (Direktör yoksa veya pasifse) -> SUPERADMIN'E BİLDİRİM
            if ($yeniDurum == 'Yönetici Onayı Bekliyor')
            {
                $superadmins = User::role('Superadmin')->get();
                if ($superadmins->isNotEmpty())
                {
                    Notification::send(
                        $superadmins,
                        new ProjeDurumuDegisti(
                            $iaa,
                            "onayınızı beklemektedir.",
                            "Bölüm yöneticisi {$user->name} tarafından onaylanmıştır."
                        )
                    );
                }
            }

            // D) BÖLÜM LİDERİNE BİLDİRİM (YENİ)
            $bolumId = $iaa->bolum_id;
            if (!$bolumId && $iaa->musteriSikayeti)
            {
                $bolumId = $iaa->musteriSikayeti->sikayetKategori->bolum_id ?? ($iaa->musteriSikayeti->bolum_id ?? null);
            }
            if ($bolumId)
            {
                $bolumLiderleri = User::role('Bölüm Lideri')->where('bolum_id', $bolumId)->get();
                if ($bolumLiderleri->isNotEmpty())
                {
                    $nextApproverName = ($yeniDurum == 'Direktör Onayı Bekliyor' && $direktorUser) ? $direktorUser->name : 'Üst Yönetici';
                    $blMsg = "'{$iaa->baslik}' başlıklı şikayet kalite yöneticisi {$user->name} tarafından onaylanmıştır. Bir sonraki aşama olan {$nextApproverName} tarafından onayı beklenmektedir.";
                    Notification::send($bolumLiderleri, new \App\Notifications\ProjeStakeholderBilgilendirme($iaa, $blMsg, 'info'));
                }
            }

            // E) MÜŞTERİ TEMSİLCİLERİNE BİLDİRİM (YENİ)
            if ($iaa->musteriSikayeti)
            {
                $sikayet = $iaa->musteriSikayeti;
                $temsilciler = collect();
                if ($sikayet->yetkili_user_id)
                    $temsilciler->push($sikayet->yetkili_user);
                $temsilciler = $temsilciler->merge($sikayet->ekYetkililer);
                if ($sikayet->customer)
                {
                    $firmaTemsilcileri = $sikayet->customer->users()->role('Müşteri Temsilcisi')->get();
                    $temsilciler = $temsilciler->merge($firmaTemsilcileri);
                }
                $finalTemsilciler = $temsilciler->unique('id');

                if ($finalTemsilciler->isNotEmpty())
                {
                    $nextStageName = ($yeniDurum == 'Direktör Onayı Bekliyor') ? 'Bölüm Direktörü' : 'Üst Yönetim';
                    $msMsg = "'{$iaa->baslik}' başlıklı şikayetiniz Bölüm Kalite Yöneticisi tarafından onaylanmış olup {$nextStageName} onayında beklemektedir.";
                    Notification::send($finalTemsilciler, new \App\Notifications\ProjeStakeholderBilgilendirme($iaa, $msMsg, 'info'));
                }
            }

        }
        catch (\Exception $e)
        {
            Log::error('Bölüm onayı bildirim hatası: ' . $e->getMessage());
        }
        // === BİLDİRİM SONU ===

        return back()->with('success', 'Bölüm onayı verildi. ' . ($yeniDurum == 'Direktör Onayı Bekliyor' ? 'Proje direktör onayına sunuldu.' : 'Proje son onay için yönetime iletildi.'));
    }

    /**
     * YENİ: DİREKTÖR ONAYI
     * Direktör onaylar ve projeyi "Süper Yönetici" onayına (Yönetici Onayı Bekliyor) sunar.
     */
    public function direktorOnayiVer(Request $request, Iaa $iaa)
    {
        $user = Auth::user();
        Log::info("direktorOnayiVer: Giriş yapıldı. User: {$user->id}, IAA: {$iaa->id}, Durum: {$iaa->durum}");

        try
        {
            // 1.5 VERİ ONARIMI: Eğer Iaa'nın bolum_id'si yoksa, bağlı şikayetten bulmaya çalış.
            // 1.5 VERİ ONARIMI: Eğer Iaa'nın bolum_id'si yoksa, bağlı şikayetten bulmaya çalış.
            if (is_null($iaa->bolum_id))
            {
                Log::warning("direktorOnayiVer: Projenin bolum_id değeri NULL. Şikayet üzerinden kontrol ediliyor...");
                $bolumId = null;

                // A) Şikayet Kategorisinden bulmayı dene
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum_id)
                {
                    $bolumId = $iaa->musteriSikayeti->sikayetKategori->bolum_id;
                    Log::info("direktorOnayiVer: bolum_id Şikayet Kategorisinden ({$iaa->musteriSikayeti->sikayetKategori->ad}) bulundu: {$bolumId}");
                }
                // B) Şikayet tablosunda varsa oradan dene (yedek)
                elseif ($iaa->musteriSikayeti && $iaa->musteriSikayeti->bolum_id)
                {
                    $bolumId = $iaa->musteriSikayeti->bolum_id;
                    Log::info("direktorOnayiVer: bolum_id Şikayetten bulundu: {$bolumId}");
                }

                if ($bolumId)
                {
                    $iaa->bolum_id = $bolumId;
                    $iaa->save(); // Veritabanını güncelle
                    Log::info("direktorOnayiVer: Iaa bolum_id onarıldı ve kaydedildi.");
                }
                else
                {
                    Log::error("direktorOnayiVer: HATA - Projenin bölüm bilgisi SİSTEMSEL EKSİK (Kategori Bulunamadı)!");
                    return back()->with('error', 'Bu projenin bölüm bilgisi sistemsel olarak eksik. Lütfen IT birimi ile iletişime geçin.');
                }
            }
            // 1. Durum Kontrolü
            if ($iaa->durum !== 'Direktör Onayı Bekliyor')
            {
                Log::warning("direktorOnayiVer: Hatalı durum! Beklenen: 'Direktör Onayı Bekliyor', Gelen: {$iaa->durum}");
                return back()->with('error', 'Bu proje direktör onayı aşamasında değil.');
            }

            // 2. Yetki Kontrolü
            if (!$user->hasRole('Superadmin') && !$user->hasRole('Direktör'))
            {
                Log::warning("direktorOnayiVer: Yetkisiz kullanıcı (Rol yok)");
                return back()->with('error', 'Bu işlemi yapmaya yetkiniz yok.');
            }

            if ($user->hasRole('Direktör'))
            {
                // HATA DÜZELTME: pluck('bolumler.id') yerine pluck('id')
                $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();

                if (!in_array($iaa->bolum_id, $yonetilenBolumIds))
                {
                    Log::warning("direktorOnayiVer: Bölüm yetkisi yok. Userın bölümleri: " . implode(',', $yonetilenBolumIds) . ", İstenen: {$iaa->bolum_id}");
                    return back()->with('error', 'Bu projeyi onaylama yetkiniz yok (Bölüm dışı).');
                }
            }

            // YENİ MANTIK: Direktör onayı NİHAİ onaydır. Projeyi tamamlar.
            Log::info("direktorOnayiVer: finalizeProjectApproval çağrılıyor...");
            $puanVerildi = $this->finalizeProjectApproval($iaa, $user, 'Direktör');
            Log::info("direktorOnayiVer: İşlem tamam. Puan verildi: " . ($puanVerildi ? 'Evet' : 'Hayır'));

            return back()->with('success', 'Direktör onayı verildi. Proje tamamlandı ve puanlar dağıtıldı.');

        }
        catch (\Exception $e)
        {
            Log::error("direktorOnayiVer: KRİTİK HATA: " . $e->getMessage() . " | " . $e->getFile() . ":" . $e->getLine());
            return back()->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * YENİ: DİREKTÖR ONAYINI GERİ ALMA
     * Direktör tarafından verilen "Tamamlandı" onayını geri alır.
     * Puanları siler, statüyü "Direktör Onayı Bekliyor"a döndürür.
     */
    public function direktorOnayiGeriAl(Iaa $iaa)
    {
        $user = Auth::user();
        Log::info("direktorOnayiGeriAl: Giriş. User: {$user->id}, IAA: {$iaa->id}");

        // 1. Yetki Kontrolü
        if (!$user->hasRole('Superadmin'))
        {
            // Direktör mü?
            if (!$user->hasRole('Direktör'))
            {
                return back()->with('error', 'Yetkisiz işlem (Direktör değilsiniz).');
            }
            // Kendi bölümü mü?
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();
            if (!in_array($iaa->bolum_id, $yonetilenBolumIds))
            {
                return back()->with('error', 'Bu proje sizin yetki alanınızda değil.');
            }
        }

        // 2. Durum Kontrolü
        if ($iaa->durum !== 'Tamamlandı')
        {
            return back()->with('error', 'Sadece "Tamamlandı" durumundaki projelerin onayı geri alınabilir.');
        }

        // 3. Log Kontrolü: Gerçekten Direktör mü onaylamış?
        // Son onaylayan kişinin Direktör olup olmadığına bakmak güvenli olur.
        // Ancak admin müdahalesi varsa karışabilir. Biz sadece "Direktör Onayı Verildi" logunu arayalım.
        $direktorOnayiVarMi = IaaLog::where('iaa_id', $iaa->id)
            ->where('eylem', 'Direktör Onayı Verildi')
            ->exists();

        if (!$direktorOnayiVarMi && !$user->hasRole('Superadmin'))
        {
            return back()->with('error', 'Bu proje direktör onayı ile tamamlanmamış görünüyor. Geri alamazsınız.');
        }

        try
        {
            DB::transaction(function () use ($iaa, $user)
            {
                // A) PUANLARI GERİ AL
                // Puan verilmiş miydi? (Puan > 0 ve Onay Tarihi Dolu)
                if ($iaa->puan > 0)
                {
                    $takim = $iaa->atananTakim;
                    if ($takim)
                    {
                        // 1. Takım Puanını Düş
                        $takim->decrement('toplam_puan', $iaa->puan);

                        // 2. Squad (Ekip) Puanlarını Düş
                        $uyeIdleri = $iaa->projeEkibi()->pluck('users.id');
                        User::whereIn('id', $uyeIdleri)->decrement('toplam_puan', $iaa->puan);

                        Log::info("direktorOnayiGeriAl: {$iaa->puan} puan Takım ve Squad'dan geri alındı.");
                    }
                }

                // B) PROJE DURUMUNU GERİ ÇEVİR
                $iaa->update([
                    'durum' => 'Direktör Onayı Bekliyor',
                    'onaylanma_tarihi' => null,
                    'tamamlanma_tarihi' => null, // Varsa bunu da sıfırla
                    'direktor_onay_tarihi' => null,
                    'durum_degistirme_tarihi' => now(),
                ]);

                // C) BAĞLI ŞİKAYETİ GERİ ÇEVİR
                if ($iaa->musteriSikayeti)
                {
                    $iaa->musteriSikayeti->update([
                        'musteri_durum' => 'Direktör Onayı Bekliyor',
                        'kurul_onay_tarihi' => null,
                        // Not: musteri_cozum_notlari'na "Geri Alındı" eklenebilir ama şart değil.
                    ]);
                    Log::info("direktorOnayiGeriAl: Bağlı şikayet durumu 'Direktör Onayı Bekliyor' yapıldı.");
                }

                // D) LOG OLUŞTUR
                IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => $user->id,
                    'eylem' => 'Direktör İşlemi Geri Alındı', // Özel log eylemi
                    'aciklama' => "Direktör ({$user->name}) tamamlanan projeyi geri aldı. Puanlar silindi ve statü 'Direktör Onayı Bekliyor'a döndü."
                ]);

                // Takvim Ziyaretini Kilitle (Direktör Onayı Bekliyor salt-okunurdur)
                $this->lockVisitInTakvim($iaa->id, true);
            });

            return back()->with('success', 'Onay geri alındı. Puanlar silindi ve proje tekrar onayınıza sunuldu.');

        }
        catch (\Exception $e)
        {
            Log::error("direktorOnayiGeriAl Hatası: " . $e->getMessage());
            return back()->with('error', 'İşlem sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * YENİ: DİREKTÖR REVİZYON TALEBİ
     */
    public function direktorRevizyonIste(Request $request, Iaa $iaa)
    {
        $user = Auth::user();
        $request->validate(['not' => 'required|string|min:5']);

        if ($iaa->durum !== 'Direktör Onayı Bekliyor')
        {
            return back()->with('error', 'Bu proje direktör onayı aşamasında değil.');
        }

        if (!$user->hasRole('Superadmin'))
        {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            if (!in_array($iaa->bolum_id, $yonetilenBolumIds))
            {
                return back()->with('error', 'Yetkisiz işlem.');
            }
        }

        $iaa->update([
            'durum' => 'Revize Ediliyor',
            'direktor_notu' => $request->not, // YENİ: Direktör notunu kendi sütununa yaz
            'yonetici_notu' => $request->not, // Uyumluluk için buraya da yazabiliriz veya kaldırabiliriz (şimdilik kalsın)
            'durum_degistirme_tarihi' => now(),
        ]);

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Revizyon Talep Edildi (Direktör)',
            'aciklama' => $user->name . ' (Direktör) revizyon istedi. Not: ' . $request->not
        ]);

        // Takvim Ziyaret kilidini aç (Revizyon için)
        $this->lockVisitInTakvim($iaa->id, false);

        // === BİLDİRİM GÖNDER ===
        try
        {
            $takim = $iaa->atananTakim;
            if ($takim && $takim->uyeler->isNotEmpty())
            {
                // 1. Takım Üyelerine Bildir
                Notification::send(
                    $takim->uyeler,
                    new ProjeDurumuDegisti($iaa, "direktör tarafından revizyona gönderildi", $request->not)
                );
            }
        }
        catch (\Exception $e)
        {
            Log::error('Direktör revizyon bildirim hatası: ' . $e->getMessage());
        }
        // === BİLDİRİM SONU ===

        return back()->with('success', 'Proje revizyon için takıma geri gönderildi.');
    }

    /**
     * YENİ: DİREKTÖR RED İŞLEMİ
     */
    public function direktorReddet(Request $request, Iaa $iaa)
    {
        $user = Auth::user();
        $request->validate(['not' => 'required|string|min:5']);

        if ($iaa->durum !== 'Direktör Onayı Bekliyor')
        {
            return back()->with('error', 'Hatalı durum.');
        }

        if (!$user->hasRole('Superadmin'))
        {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            if (!in_array($iaa->bolum_id, $yonetilenBolumIds))
            {
                return back()->with('error', 'Yetkisiz işlem.');
            }
        }

        $iaa->update([
            'durum' => 'Tamamlanması Reddedildi',
            'direktor_notu' => $request->not, // YENİ: Direktör notunu kaydet
            'yonetici_notu' => $request->not,
            'durum_degistirme_tarihi' => now(),
        ]);

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Proje Reddedildi (Direktör)',
            'aciklama' => $user->name . ' (Direktör) projeyi reddetti. Gerekçe: ' . $request->not
        ]);

        // Takvim Ziyaret kilidini aç (Red/Revizyon için)
        $this->lockVisitInTakvim($iaa->id, false);

        // === BİLDİRİM GÖNDER ===
        try
        {
            $takim = $iaa->atananTakim;
            if ($takim && $takim->uyeler->isNotEmpty())
            {
                // Takım Üyelerine Bildir
                Notification::send(
                    $takim->uyeler,
                    new ProjeDurumuDegisti($iaa, "direktör tarafından reddedildi", $request->not)
                );
            }
        }
        catch (\Exception $e)
        {
            Log::error('Direktör red bildirim hatası: ' . $e->getMessage());
        }
        // === BİLDİRİM SONU ===

        return back()->with('success', 'Proje direktör tarafından reddedildi.');
    }

    /**
     * BÖLÜM YÖNETİCİSİ İÇİN GENEL GERİ ALMA
     * Onay, Revizyon veya Red işlemlerini geri alır ve projeyi "Bölüm Onayı Bekliyor"a çeker.
     */
    public function bolumOnayiGeriAl(Request $request, Iaa $iaa)
    {
        $user = Auth::user();

        // 1. Durum Kontrolü: 
        // Bölüm yöneticisi şu durumlardan geri dönüş yapabilir:
        // - Yönetici Onayı Bekliyor (Onaylamıştı)
        // - Revize Ediliyor (Revizyon istemişti)
        // - Tamamlanması Reddedildi (Reddetmişti)

        $izinVerilenDurumlar = ['Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Revize Ediliyor', 'Tamamlanması Reddedildi'];

        if (!in_array($iaa->durum, $izinVerilenDurumlar))
        {
            return back()->with('error', 'Bu proje şu an geri alınabilir bir aşamada değil (Bölüm yöneticisi yetkisi dışı).');
        }

        // 2. Yetki Kontrolü
        if (!$user->hasRole('Superadmin'))
        {
            // Kategori kontrolü (Kendi tarlası mı?)
            if (!$iaa->musteriSikayeti || !$iaa->musteriSikayeti->sikayet_kategorisi_id)
            {
                return back()->with('error', 'Veri hatası.');
            }
            $yetkiliKategoriler = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            if (!in_array($iaa->musteriSikayeti->sikayet_kategorisi_id, $yetkiliKategoriler))
            {
                return back()->with('error', 'Bu işlem için yetkiniz yok.');
            }
        }

        // Eski durumu not edelim (Log için)
        $eskiDurum = $iaa->durum;

        // 3. Durumu Geri Çevir -> "Bölüm Onayı Bekliyor"
        $iaa->update([
            'durum' => 'Bölüm Onayı Bekliyor',
            'yonetici_notu' => null, // Varsa eski notu temizle
            'durum_degistirme_tarihi' => now(),
        ]);

        // Şikayet Durumu Senkronizasyonu
        if ($iaa->musteriSikayeti) {
            $iaa->musteriSikayeti->update(['musteri_durum' => 'Bölüm Onayı Bekliyor']);
        }

        // 4. Logla
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Bölüm İşlemi Geri Alındı',
            'aciklama' => $user->name . ", '{$eskiDurum}' olan kararını geri çekti ve projeyi tekrar incelemeye aldı."
        ]);

        // Takvim Ziyaretini Kilitle (Bölüm Onayı Bekliyor salt-okunurdur)
        $this->lockVisitInTakvim($iaa->id, true);

        return back()->with('success', 'İşleminiz geri alındı. Proje tekrar "Bölüm Onayı Bekliyor" durumuna döndü.');
    }

    /**
     * YENİ: BÖLÜM YÖNETİCİSİ - REVİZYON TALEBİ
     */
    public function bolumRevizyonIste(Request $request, Iaa $iaa)
    {
        $user = Auth::user();

        // Validasyon: Açıklama şart
        $request->validate(['not' => 'required|string|min:5']);

        // 1. Durum Kontrolü
        if ($iaa->durum !== 'Bölüm Onayı Bekliyor')
        {
            return back()->with('error', 'Bu proje bölüm onayı aşamasında değil.');
        }

        // 2. Yetki Kontrolü
        if (!$user->hasRole('Superadmin'))
        {
            if (!$iaa->musteriSikayeti || !$iaa->musteriSikayeti->sikayet_kategorisi_id)
            {
                return back()->with('error', 'Kategori bilgisi eksik.');
            }
            $yetkiliKategoriler = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            if (!in_array($iaa->musteriSikayeti->sikayet_kategorisi_id, $yetkiliKategoriler))
            {
                return back()->with('error', 'Bu işlem için yetkiniz yok.');
            }
        }

        // 3. Güncelleme (Revize Durumuna Çek)
        // Not: 'Revize Ediliyor' durumu sisteminizde Takım'ın tekrar düzenlemesini tetikler.
        $iaa->update([
            'durum' => 'Revize Ediliyor',
            'yonetici_notu' => $request->not, // Bölüm yöneticisinin notu
            'durum_degistirme_tarihi' => now(),
        ]);

        // 4. Logla
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Revizyon Talep Edildi (Bölüm)',
            'aciklama' => $user->name . ' (Bölüm Yöneticisi) revizyon istedi. Not: ' . $request->not
        ]);

        // Takvim Ziyaret kilidini aç (Revizyon için)
        $this->lockVisitInTakvim($iaa->id, false);

        // === 5. BİLDİRİM GÖNDER (EKLENEN KISIM) ===
        try
        {
            $takim = $iaa->atananTakim;
            if ($takim && $takim->uyeler->isNotEmpty())
            {
                // Takım üyelerine bildirim gönder
                Notification::send(
                    $takim->uyeler,
                    new ProjeDurumuDegisti($iaa, "bölüm yöneticisi tarafından revizyona gönderildi", $request->not)
                );
            }
        }
        catch (\Exception $e)
        {
            Log::error('Bölüm revizyon bildirimi hatası: ' . $e->getMessage());
        }
        // === EKLEME SONU ===

        return back()->with('success', 'Proje revizyon için takıma geri gönderildi.');
    }

    /**
     * YENİ: BÖLÜM YÖNETİCİSİ - RED İŞLEMİ
     */
    public function bolumReddet(Request $request, Iaa $iaa)
    {
        $user = Auth::user();
        $request->validate(['not' => 'required|string|min:5']);

        // 1. Durum & Yetki Kontrolü (Revizyon ile aynı)
        if ($iaa->durum !== 'Bölüm Onayı Bekliyor')
            return back()->with('error', 'Hatalı durum.');

        if (!$user->hasRole('Superadmin'))
        {
            if (!$iaa->musteriSikayeti || !$iaa->musteriSikayeti->sikayet_kategorisi_id)
                return back()->with('error', 'Kategori hatası.');
            $yetkiliKategoriler = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            if (!in_array($iaa->musteriSikayeti->sikayet_kategorisi_id, $yetkiliKategoriler))
                return back()->with('error', 'Yetkisiz işlem.');
        }

        // 2. Güncelleme (Red Durumuna Çek)
        $iaa->update([
            'durum' => 'Tamamlanması Reddedildi',
            'yonetici_notu' => $request->not,
            'durum_degistirme_tarihi' => now(),
        ]);

        // 3. Logla
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Proje Reddedildi (Bölüm)',
            'aciklama' => $user->name . ' (Bölüm Yöneticisi) projeyi reddetti. Gerekçe: ' . $request->not
        ]);

        // Takvim Ziyaret kilidini aç (Red/Revizyon için)
        $this->lockVisitInTakvim($iaa->id, false);

        // === BİLDİRİM GÖNDER (EKLENEN KISIM) ===
        try
        {
            $takim = $iaa->atananTakim;
            if ($takim && $takim->uyeler->isNotEmpty())
            {
                Notification::send(
                    $takim->uyeler,
                    new ProjeDurumuDegisti($iaa, "bölüm yöneticisi tarafından reddedildi", $request->not)
                );
            }
        }
        catch (\Exception $e)
        {
            Log::error('Bölüm red bildirimi hatası: ' . $e->getMessage());
        }
        // === EKLEME SONU ===

        return back()->with('success', 'Proje bölüm yöneticisi tarafından reddedildi.');
    }

    /**
     * ====================================================================
     * BİR İAA ÖNERİSİNİ KALICI OLARAK SİLER
     * ====================================================================
     */
    public function destroy(Iaa $iaa)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        foreach ($iaa->resimler as $resim)
        {
            Storage::disk('public')->delete($resim->dosya_yolu);
        }
        $iaa->forceDelete();
        return redirect()->route('admin.iaa-yonetim.index')->with('success', 'İAA önerisi ve ilişkili tüm veriler kalıcı olarak silindi.');
    }

    /**
     * ====================================================================
     * BİRDEN FAZLA İAA ÖNERİSİNİ TOPLU OLARAK SİLER
     * ====================================================================
     */
    public function bulkDestroy(Request $request)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $validated = $request->validate([
            'iaa_ids' => 'required|array',
            'iaa_ids.*' => 'exists:iaas,id',
        ]);
        $iaaIds = $validated['iaa_ids'];
        DB::transaction(function () use ($iaaIds)
        {
            $iaas = Iaa::whereIn('id', $iaaIds)->with('resimler')->get();
            foreach ($iaas as $iaa)
            {
                /** @var \App\Models\Iaa $iaa */
                // Resimleri sil
                foreach ($iaa->resimler as $resim)
                {
                    Storage::disk('public')->delete($resim->dosya_yolu);
                }
                // Eloquent model üzerinden sil
                $iaa->forceDelete();
            }
        });
        return redirect()->route('admin.iaa-yonetim.index')->with('success', count($iaaIds) . ' adet öneri kalıcı olarak silindi.');
    }

    /**
     * ====================================================================
     * PROJEYİ TAMAMLAR, PUANLARI DAĞITIR VE ŞİKAYETİ KAPATIR (ORTAK METOD)
     * ====================================================================
     * Hem Süper Yönetici (approveCompleted) hem de Direktör (direktorOnayiVer)
     * tarafından kullanılır.
     */
    private function finalizeProjectApproval(Iaa $iaa, User $approver, string $approverTitle)
    {
        Log::info("finalizeProjectApproval başladı. IAA ID: {$iaa->id}, Approver: {$approverTitle}, Durum: {$iaa->durum}");
        $takim = $iaa->atananTakim;
        $projePuani = $iaa->puan ?? 0;
        $puanVerildi = false;

        // Log mesajını hazırlayalım
        $logAciklama = "{$approverTitle} tarafından proje onaylandı ve \"Tamamlandı\" durumuna getirildi.";
        $sikayetGuncellendi = false;

        try
        {
            DB::transaction(function () use ($iaa, $takim, $projePuani, $approver, $approverTitle, &$puanVerildi, &$logAciklama, &$sikayetGuncellendi)
            {
                $isSikayet = str_contains($iaa->oneri ?? '', 'Müşteri şikayetinden');
                $isSafIaa = !$isSikayet;

                // 0. SENKRONİZASYON (Kritik): Şikayet projesi ise onaydan önce puanı şikayetten tazele
                if (!$isSafIaa && $iaa->musteriSikayeti) {
                    $projePuani = (float)$iaa->musteriSikayeti->musteri_puan;
                    $iaa->puan = $projePuani; // Pivot/Takım dağıtımı için güncel tut
                    $iaa->saveQuietly(); // Observer tetiklemeden DB'ye de kaydet (iaas.puan tutarlı olsun)
                }

                // 1. PUANLARI DAĞIT
                // KONTROL: Daha önce puan verilmemişse (onaylanma_tarihi null ise) dağıt.
                if (is_null($iaa->onaylanma_tarihi) && $takim && $projePuani > 0)
                {
                    // A) Takım Puanı
                    $takim->increment('toplam_puan', $projePuani);

                    // B) Kullanıcı Puanları
                    if ($isSafIaa)
                    {
                        // Kural: Saf İAA ise daveti kabul etmiş tüm üyeler puan alır
                        $uyeIdleri = $takim->uyeler()->pluck('users.id');
                        
                        // SADECE AKTİF PERSONELLER PUAN ALSIN (Ayrılma tarihi geçmiş olanlar hariç)
                        $aktifUyeIdleri = User::whereIn('id', $uyeIdleri)
                            ->where('is_personnel', true)
                            ->whereNull('deleted_at')
                            ->where(function($q) {
                                $q->whereNull('termination_date')
                                  ->orWhere('termination_date', '>=', now()->toDateString());
                            })
                            ->pluck('id');

                        User::whereIn('id', $aktifUyeIdleri)->increment('toplam_puan', $projePuani);
                        $logAciklama = "Proje {$approverTitle} tarafından onaylandı (Saf İAA). " . $projePuani . ' puan, ' . $takim->ad . ' hanesine ve aktif olan ' . $aktifUyeIdleri->count() . ' takım üyesine eklendi.';
                    }
                    else
                    {
                        // Kural: Şikayet kaynaklı ise sadece görevli (Squad) üyeler puan alır
                        $uyeIdleri = $iaa->projeEkibi()->pluck('users.id');
                        
                        // SADECE AKTİF PERSONELLER PUAN ALSIN (Ayrılma tarihi geçmiş olanlar hariç)
                        $aktifUyeIdleri = User::whereIn('id', $uyeIdleri)
                            ->where('is_personnel', true)
                            ->whereNull('deleted_at')
                            ->where(function($q) {
                                $q->whereNull('termination_date')
                                  ->orWhere('termination_date', '>=', now()->toDateString());
                            })
                            ->pluck('id');

                        User::whereIn('id', $aktifUyeIdleri)->increment('toplam_puan', $projePuani);
                        $logAciklama = "Proje {$approverTitle} tarafından onaylandı (Müşteri Şikayeti). " . $projePuani . ' puan, ' . $takim->ad . ' hanesine ve görevli ' . $aktifUyeIdleri->count() . ' proje çalışanına (Squad) eklendi.';
                    }

                    $puanVerildi = true;
                }
                elseif (!is_null($iaa->onaylanma_tarihi))
                {
                    $logAciklama .= ' (Proje daha önce onaylandığı için tekrar puan verilmedi.)';
                }

                // 2. PROJEYİ GÜNCELLE
                // tamamlayan_lider_id: Güncel AKTIF takım liderini al (işten ayrılmış eski lider atanmasın)
                $guncelLiderId = optional($iaa->atananTakim)->lider_user_id;
                $liderAktifMi = $guncelLiderId ? User::where('id', $guncelLiderId)
                    ->whereNull('deleted_at')
                    ->where(function($q) {
                        $q->whereNull('termination_date')
                          ->orWhere('termination_date', '>=', now()->toDateString());
                    })
                    ->exists() : false;

                $iaa->update([
                    'durum' => 'Tamamlandı',
                    'onaylanma_tarihi' => $iaa->onaylanma_tarihi ?? now(),
                    'yonetici_notu' => null,
                    'tamamlayan_lider_id' => $iaa->tamamlayan_lider_id ?? ($liderAktifMi ? $guncelLiderId : null), // Lideri sabitle (aktif olmalı)
                ]);

                // 3. BAĞLI MÜŞTERİ ŞİKAYETİNİ GÜNCELLE
                $bagliSikayet = MusteriSikayeti::where('iaa_id', $iaa->id)->first();
                if ($bagliSikayet && $bagliSikayet->musteri_durum !== 'Kapatıldı')
                {
                    $bagliSikayet->update([
                        'musteri_durum' => 'Kapatıldı',
                        'musteri_cozum_notlari' => ($bagliSikayet->musteri_cozum_notlari ?? '') . "\nİlgili IAA Projesi (ID: {$iaa->id}) {$approverTitle} tarafından onaylanarak kapatıldı.",
                        'kurul_onay_tarihi' => now()
                    ]);
                    $sikayetGuncellendi = true;
                }

                // 4. BİLDİRİM
                try {
                    if ($takim && $takim->uyeler->isNotEmpty())
                    {
                        Notification::send($takim->uyeler, new ProjeDurumuDegisti($iaa, "onaylandı"));
                    }
                } catch (\Exception $e) {
                    Log::error('Proje onay bildirimi (Takım) hatası: ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Proje Onay Bildirimi (Takım)", $takim->uyeler, $e->getMessage());
                }

                // --- 4.1 PAYDAŞ BİLDİRİMLERİ (YENİ) ---
                $stakeholders = collect();

                // A) Bölüm Liderleri
                $bolumId = $iaa->bolum_id;
                if (!$bolumId && $iaa->musteriSikayeti)
                {
                    $bolumId = $iaa->musteriSikayeti->sikayetKategori->bolum_id ?? ($iaa->musteriSikayeti->bolum_id ?? null);
                }
                if ($bolumId)
                {
                    $stakeholders = $stakeholders->merge(User::role('Bölüm Lideri')->where('bolum_id', $bolumId)->get());
                }

                // B) Bölüm Kalite Yöneticileri
                if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayet_kategorisi_id)
                {
                    $catId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                    $stakeholders = $stakeholders->merge(User::role('Bölüm Kalite Yöneticisi')->whereHas('yonettigiSikayetKategorileri', function ($q) use ($catId)
                    {
                        $q->where('sikayet_kategorileri.id', $catId);
                    })->get());
                }

                $finalStakeholders = $stakeholders->unique('id')->reject(fn($u) => $u->id == $approver->id);
                try {
                    if ($finalStakeholders->isNotEmpty())
                    {
                        $stMsg = "'{$iaa->baslik}' başlıklı şikayet projesi {$approverTitle} tarafından onaylanmış ve süreç tamamlanmıştır.";
                        Notification::send($finalStakeholders, new \App\Notifications\ProjeStakeholderBilgilendirme($iaa, $stMsg, 'info'));
                    }
                } catch (\Exception $e) {
                    Log::error('Proje onay bildirimi (Paydaş) hatası: ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Proje Onay Bildirimi (Paydaş)", $finalStakeholders, $e->getMessage());
                }

                // C) Müşteri Temsilcileri
                try {
                    if ($iaa->musteriSikayeti)
                    {
                        $msSikayet = $iaa->musteriSikayeti;
                        $msTemsilciler = collect();
                        if ($msSikayet->yetkili_user_id)
                            $msTemsilciler->push($msSikayet->yetkili_user);
                        $msTemsilciler = $msTemsilciler->merge($msSikayet->ekYetkililer);
                        if ($msSikayet->customer)
                        {
                            $msTemsilciler = $msTemsilciler->merge($msSikayet->customer->users()->role('Müşteri Temsilcisi')->get());
                        }
                        $finalMsTemsilciler = $msTemsilciler->unique('id')->reject(fn($u) => $u->id == $approver->id);
                        if ($finalMsTemsilciler->isNotEmpty())
                        {
                            $msFinalMsg = "'{$iaa->baslik}' başlıklı şikayet projeniz {$approverTitle} tarafından onaylanarak başarıyla sonuçlandırılmıştır.";
                            Notification::send($finalMsTemsilciler, new \App\Notifications\ProjeStakeholderBilgilendirme($iaa, $msFinalMsg, 'info'));
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Proje onay bildirimi (Müşteri Temsilcisi) hatası: ' . $e->getMessage());
                    \App\Helpers\MailLogHelper::logFailure($iaa, "Proje Onay Bildirimi (Müşteri Temsilcisi)", $finalMsTemsilciler, $e->getMessage());
                }

                // 5. LOG
                IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => $approver->id,
                    'eylem' => ($approverTitle == 'Direktör' ? 'Direktör Onayı Verildi' : 'Proje Onaylandı'),
                    'aciklama' => $logAciklama
                ]);

                // Takvim Ziyaretini Kilitle (Kesin Kilit)
                $this->lockVisitInTakvim($iaa->id, true);
            });

        }
        catch (\Exception $e)
        {
            Log::error('Proje onaylanırken hata (finalizeProjectApproval): ' . $e->getMessage());
        }

        // 6. EVENT (Transaction dışı)
        if ($sikayetGuncellendi)
        {
            try
            {
                event(new \App\Events\SikayetDurumuDegisti());
            }
            catch (\Exception $e)
            {
                Log::error('Event hatası: ' . $e->getMessage());
            }
        }

        return $puanVerildi;
    }

    /**
     * ====================================================================
     * TAMAMLANMIŞ PROJEYİ ONAYLAR (GÜNCELLENMİŞ VE TAM SÜRÜM)
     * ====================================================================
     * Puanları dağıtır ve bağlıysa Müşteri Şikayetini otomatik kapatır.
     */
    public function approveCompleted(Iaa $iaa)
    {
        // 1. Projenin 'Yönetici Onayı Bekliyor' durumunda olduğundan emin olalım.
        if ($iaa->durum !== 'Yönetici Onayı Bekliyor')
        {
            return back()->with('error', 'Bu proje onaylanabilir durumda değil.');
        }

        try
        {
            $puanVerildi = $this->finalizeProjectApproval($iaa, Auth::user(), 'Yönetici');
            return back()->with('success', 'Proje başarıyla onaylandı!' . ($puanVerildi ? ' Puanlar dağıtıldı.' : ''));
        }
        catch (\Exception $e)
        {
            return back()->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Tamamlanmış bir projeyi reddeder ve gerekçesini kaydeder.
     */
    /**
     * TAMAMLANMIŞ PROJEYİ REDDEDER (Süper Yönetici)
     */
    public function rejectCompleted(Request $request, Iaa $iaa)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $iaa->update([
            'durum' => 'Tamamlanması Reddedildi',
            'yonetici_notu' => $validated['rejection_reason']
        ]);

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Tamamlanmış Projenin Reddi',
            'aciklama' => $validated['rejection_reason']
        ]);

        // Takvim Ziyaret kilidini aç (Red/Revizyon için)
        $this->lockVisitInTakvim($iaa->id, false);

        try
        {
            $takim = $iaa->atananTakim;
            // DÜZELTME: $takim->uyeler kullanıldı
            if ($takim && $takim->uyeler->isNotEmpty())
            {
                $neden = $validated['rejection_reason'];
                Notification::send($takim->uyeler, new ProjeDurumuDegisti($iaa, "reddedildi", $neden));
            }
        }
        catch (\Exception $e)
        {
            Log::error('Proje reddedildi bildirimi gönderilemedi: ' . $e->getMessage());
        }

        return back()->with('success', 'Projenin tamamlanması reddedildi.');
    }

    /**
     * Tamamlanmış bir proje için revizyon ister ve talebi kaydeder.
     */
    /**
     * REVİZYON İSTER (Süper Yönetici)
     */
    public function requestRevision(Request $request, Iaa $iaa)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $validated = $request->validate([
            'revision_reason' => 'required|string|min:10',
        ]);

        $iaa->update([
            'durum' => 'Revize Ediliyor',
            'yonetici_notu' => $validated['revision_reason']
        ]);

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Revizyon Talep Edildi',
            'aciklama' => $validated['revision_reason']
        ]);

        // Takvim Ziyaret kilidini aç (Revizyon için)
        $this->lockVisitInTakvim($iaa->id, false);

        try
        {
            $takim = $iaa->atananTakim;
            // DÜZELTME: $takim->uyeler kullanıldı
            if ($takim && $takim->uyeler->isNotEmpty())
            {
                $neden = $validated['revision_reason'];
                Notification::send($takim->uyeler, new ProjeDurumuDegisti($iaa, "revizyona gönderildi", $neden));
            }
        }
        catch (\Exception $e)
        {
            Log::error('Proje revizyon bildirimi gönderilemedi: ' . $e->getMessage());
        }

        return back()->with('success', 'Proje için revizyon talebi takıma iletildi.');
    }

    // ================================================================
    // YENİ OLUŞTURULAN ARŞİV METODU
    // ================================================================
    public function arsiv()
    {
        $tamamlananProjeler = Iaa::where('durum', 'Tamamlandı')->orderBy('onaylanma_tarihi', 'desc')->paginate(20);

        // YENİ EKLENEN SORGULAR
        $topTeamResult = Iaa::where('durum', 'Tamamlandı')
            ->whereNotNull('atanan_takim_id')
            ->select('atanan_takim_id', DB::raw('count(*) as proje_sayisi'))
            ->groupBy('atanan_takim_id')
            ->orderByDesc('proje_sayisi')
            ->first();

        $enIyiTakim = null;
        if ($topTeamResult)
        {
            $takim = Takim::find($topTeamResult->atanan_takim_id);
            if ($takim)
            {
                $enIyiTakim = [
                    'ad' => $takim->ad,
                    'proje_sayisi' => $topTeamResult->proje_sayisi
                ];
            }
        }

        return view('admin.iaa-yonetim.arsiv', compact('tamamlananProjeler', 'enIyiTakim'));
    }


    /**
     * Bir İAA önerisini başka bir kullanıcıya atama formunu gösterir.
     */
    public function reassignForm(Iaa $iaa)
    {
        $users = User::where('onaylandi_mi', true)->orderBy('name')->get();
        return view('admin.iaa-yonetim.reassign', compact('iaa', 'users'));
    }

    /**
     * İAA önerisinin sahibini (öneren kişiyi) günceller.
     */
    public function reassignUpdate(Request $request, Iaa $iaa)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        $validated = $request->validate([
            'gonderen_user_id' => 'required|exists:users,id',
        ]);

        $iaa->gonderen_user_id = $validated['gonderen_user_id'];
        $iaa->save();

        return redirect()->route('admin.iaa-yonetim.index')->with('success', 'İAA önerisi başarıyla yeni kullanıcıya atandı.');
    }



    /**
     * Mevcut bir İAA'nın puanlama bilgilerini (risk, kazanç, bütçe) günceller.
     */
    public function updateScore(Request $request, Iaa $iaa)
    {
        if (!Auth::user()->hasRole('Superadmin'))
        {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        // 1. "onayla" metodundaki validation kurallarının aynısını kullanıyoruz (artık nullable).
        $validated = $request->validate([
            'risk' => 'nullable|integer|between:1,5',
            'kazanc_miktar' => 'nullable|numeric|min:0',
            'kazanc_birim' => 'nullable|string|max:10',
            'butce_miktar' => 'nullable|numeric|gt:0',
            'butce_birim' => 'nullable|string|max:10',
            'yil_baz' => 'nullable|integer|min:1|max:50',
        ]);

        // 2. Puanı yeniden hesapla.
        $puan = 0;
        $isSikayet = str_contains($iaa->oneri ?? '', 'Müşteri şikayetinden');
        $isSafIaa = !$isSikayet;
        $yilBaz = $request->input('yil_baz', 5) ?: 5;

        if ($request->filled('risk') && $request->filled('kazanc_miktar') && $request->filled('butce_miktar'))
        {
            $puan = round(($validated['risk'] * $validated['kazanc_miktar'] * $yilBaz) / $validated['butce_miktar']);
        }
        else
        {
            if ($isSafIaa) {
                // Saf İAA için standart puan (100)
                $standartPuanAyar = \App\Models\Setting::where('key', 'standart_puan')->first();
                $puan = $standartPuanAyar->value ?? 100;
            } else {
                // Şikayet için Triyaj Puanı
                $puan = $iaa->musteriSikayeti ? (float)$iaa->musteriSikayeti->musteri_puan : 0;
            }
        }

        // 3. İAA kaydını yeni değerlerle güncelle.
        $iaa->update([
            'risk' => $validated['risk'],
            'kazanc_miktar' => $validated['kazanc_miktar'],
            'kazanc_birim' => $request->kazanc_birim,
            'butce_miktar' => $validated['butce_miktar'],
            'butce_birim' => $request->butce_birim,
            'yil_baz' => $yilBaz,
            'puan' => $puan,
        ]);

        // 4. Puan Güncellendiğinde Senkronizasyon Yap
        // Eğer proje daha önceden onaylandıysa (puan dağıtıldıysa) tüm puanları yeniden hesaplamak en güvenlisidir.
        if ($iaa->durum === 'Tamamlandı')
        {
            try
            {
                $puanService = app(\App\Services\Dashboard\KullaniciPuanService::class);

                // Projedeki takımı güncelle
                if ($iaa->atananTakim)
                {
                    $takimData = $puanService->getTeamDetailedScoreData($iaa->atananTakim);
                    $iaa->atananTakim->update(['toplam_puan' => $takimData['hesaplananPuan'] ?? 0]);
                }

                // Projedeki personelleri güncelle
                $users = $iaa->projeEkibi;
                foreach ($users as $user)
                {
                    $userPuan = $puanService->calculateTotalScore($user);
                    $user->update(['toplam_puan' => $userPuan]);
                }
            }
            catch (\Exception $e)
            {
                Log::error('Puan güncelleme sonrası senkronizasyon hatası: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Proje puanı başarıyla güncellendi.');
    }

    /**
     * TALEBİ REDDETME (SİLME) METODU
     */

    private function lockVisitInTakvim($iaaId, $lock = true)
    {
        try
        {
            // Takvim API URL'si .env'den veya config'den alınmalı
            $baseUrl = rtrim(config('services.takvim.url', 'http://localhost:8001'), '/');

            Http::withHeaders([
                'Accept' => 'application/json',
                'X-App-Internal' => 'iaa'
            ])->post($baseUrl . '/api/visit/toggle-lock', [
                'remote_id' => $iaaId,
                'lock' => $lock
            ]);
        }
        catch (\Exception $e)
        {
            \Log::error("Takvim visit lock toggle failed for IAA $iaaId (Admin): " . $e->getMessage());
        }
    }
}



