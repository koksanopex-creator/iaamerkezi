<?php

namespace App\Http\Controllers;

use App\Models\IaaResim;
use App\Models\Bolum;
use App\Models\Iaa;
use App\Models\Takim; // Yeni metodlar için eklendi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Setting;
use App\Models\IaaLog;
use App\Models\User; 
use App\Traits\NotifiesManager;
use Illuminate\Support\Facades\Notification;
use App\Notifications\IaaProjesineTalepGeldi;
use App\Notifications\YeniIaaOnerisi;

class IaaController extends Controller
{
    use AuthorizesRequests;
    use NotifiesManager;

    /**
     * ====================================================================
     * GİRİŞ YAPMIŞ KULLANICININ KENDİ ÖNERİLERİNİ LİSTELER
     * ====================================================================
     * Bu metod, sadece o an giriş yapmış kullanıcının gönderdiği İAA'ları
     * veritabanından çeker ve 'iaa.index' view'ine gönderir.
     */
    public function index()
    {
        $iaas = Iaa::where('gonderen_user_id', Auth::id())
                    ->with('bolum', 'gonderen')
                    ->latest()
                    ->get();
        return view('iaa.index', compact('iaas'));
    }

    /**
     * ====================================================================
     * YENİ İAA ÖNERİSİ OLUŞTURMA FORMUNU GÖSTERİR
     * ====================================================================
     */
    public function create()
    {
        if (!Auth::user()->bolum_id) {
            return redirect()->route('iaa.index')->with('error', 'İAA önerebilmek için bir bölüme atanmış olmalısınız. Lütfen yöneticinizle iletişime geçin.');
        }

        // Tüm bölümleri alıp sayfaya gönderiyoruz
    $bolumler = Bolum::orderBy('ad')->get();

        return view('iaa.create', compact('bolumler'));
    }

    /**
     * ====================================================================
     * YENİ İAA ÖNERİSİNİ VERİTABANINA KAYDEDER
     * ====================================================================
     * Formdan gelen veriyi doğrular, İAA kaydını ve varsa resimlerini
     * bir transaction içinde güvenli bir şekilde veritabanına ekler.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->bolum_id) {
            return redirect()->route('iaa.index')->with('error', 'İAA önerebilmek için bir bölüme atanmış olmalısınız.');
        }

        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'mevcut_durum' => 'required|string',
            'oneri' => 'required|string',
            'bolum_id' => 'required|exists:bolumler,id', // Zorunlu yaptık
            'konum_text' => 'required|string|max:100', // YENİ ALAN (Konum/Alan)
            'resimler' => 'nullable|array',
            'resimler.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
            'para_birimi' => 'nullable|string|max:10',
            'oneren_kazanc_miktar' => 'nullable|numeric|min:0',
            'oneren_butce_miktar' => 'nullable|numeric|min:0'
        ]);
        
        $iaaRecord = null;

        DB::transaction(function () use ($validated, $request, &$iaaRecord) {

            $birlestirilmisMevcutDurum = "📍 Lokasyon/Alan: " . $validated['konum_text'] . "\n\n" . $validated['mevcut_durum'];
            
            $iaaData = [
                'gonderen_user_id' => Auth::id(),
                'bolum_id' => $validated['bolum_id'], // Kullanıcının seçtiği sorumlu bölüm
                'baslik' => $validated['baslik'],
                'mevcut_durum' => $birlestirilmisMevcutDurum, // Birleştirilmiş metin
                'oneri' => $validated['oneri'],
                'oneren_kazanc_miktar' => $validated['oneren_kazanc_miktar'] ?? null,
                'oneren_butce_miktar' => $validated['oneren_butce_miktar'] ?? null,
                'oneren_kazanc_birim' => $validated['para_birimi'] ?? null,
                'oneren_butce_birim' => $validated['para_birimi'] ?? null,
            ];

            $iaaRecord = Iaa::create($iaaData);

            if ($request->hasFile('resimler')) {
                foreach ($request->file('resimler') as $file) {
                    // 1. Tarih, saat ve mikrosaniye içeren benzersiz bir dosya adı oluşturuyoruz.
                    $filename = 'iaa_' . now()->format('Ymd_Hisu') . '.' . $file->getClientOriginalExtension();
            
                    // 2. storeAs() ile dosyayı kendi belirlediğimiz adla kaydediyoruz.
                    $path = $file->storeAs('iaa_resimleri', $filename, 'public'); 
            
                    $iaaRecord->resimler()->create(['dosya_yolu' => $path]);
                }
            }
        });

        // === BİLDİRİM: Transaction DIŞINDA — mail hatası kaydı bozmaz ===
        if ($iaaRecord) {
            try {
                $superadmins = User::role('Superadmin')->get();
                if ($superadmins->isNotEmpty()) {
                    Notification::send($superadmins, new YeniIaaOnerisi($iaaRecord, Auth::user()));
                }
            } catch (\Exception $e) {
                \App\Helpers\MailLogHelper::logFailure(
                    $iaaRecord,
                    'Personel İAA Önerisi Bildirimi',
                    User::role('Superadmin')->get(),
                    $e->getMessage(),
                    null,
                    null,
                    $iaaRecord->bolum_id
                );
            }
        }

        return redirect()->route('iaa.index')->with('success', 'İAA öneriniz başarıyla gönderildi.');
    }

    /**
     * ====================================================================
     * BİR İAA ÖNERİSİNİN DETAY SAYFASINI GÖSTERİR
     * ====================================================================
     * 'IaaPolicy'deki yetki kurallarını kontrol eder. Eğer kullanıcı
     * yetkiliyse, önerinin detaylarını 'iaa.show' view'ine gönderir.
     */
    public function show(Iaa $iaa)
    {
        // Yetki kontrolü (Policy'den)
        $this->authorize('view', $iaa);

        // ================== GÜNCELLEME BAŞLANGICI ==================
        $kullanici = auth()->user();
        
        // Buton mantığı için kullanıcının lideri olduğu takımları alıyoruz
        $liderOlduguTakimlar = $kullanici->lideriOlduguTakimlar;
        
        // Bu takımların daha önce talep ettiği İAA'ların ID'lerini alıyoruz
        $talepEdilenIaaIdleri = DB::table('iaa_talepleri')
                                    ->whereIn('takim_id', $liderOlduguTakimlar->pluck('id'))
                                    ->pluck('iaa_id');
        // ================== GÜNCELLEME SONU ==================

        // ================== YENİ EKLENEN KISIM ==================
        // Sistem ayarlarından para birimlerini alıyoruz.
        $paraBirimleriAyar = Setting::where('key', 'para_birimleri')->first();
        $paraBirimleri = explode(',', $paraBirimleriAyar->value ?? 'TL,USD,EUR');
        // ==========================================================

        // Gerekli ilişkili verileri yüklüyoruz
        $iaa->load('resimler', 'gonderen', 'bolum', 'atananTakim');
        
        // Tüm verileri view'e gönderiyoruz
        return view('iaa.show', compact(
            'iaa', 
            'liderOlduguTakimlar', 
            'talepEdilenIaaIdleri',
            'paraBirimleri'
        ));
    }

    /**
     * ====================================================================
     * BİR İAA ÖNERİSİNİ DÜZENLEME FORMUNU GÖSTERİR
     * ====================================================================
     */
    /**
     * ====================================================================
     * BİR İAA ÖNERİSİNİ DÜZENLEME FORMUNU GÖSTERİR
     * ====================================================================
     */
    public function edit(Iaa $iaa)
    {
        // 1. Senin MEVCUT Güvenlik Kodun (AYNEN KALIYOR)
        $this->authorize('update', $iaa); 

        // 2. View için Gerekli Ek Veriler (BUNLARI EKLİYORUZ)
        // Bölümleri çekmezsek select box boş gelir, hata verir.
        $bolumler = Bolum::orderBy('ad')->get(); 
        $paraBirimleri = ['TL', 'USD', 'EUR', 'GBP'];

        // 3. Verileri View'a Gönderiyoruz
        return view('iaa.edit', compact('iaa', 'bolumler', 'paraBirimleri'));
    }

    /**
     * ====================================================================
     * DÜZENLENEN İAA ÖNERİSİNİ GÜNCELLER
     * ====================================================================
     */
    /**
     * ====================================================================
     * İAA GÜNCELLEME İŞLEMİNİ YAPAR (VERİTABANINA YAZAR)
     * ====================================================================
     */
    public function update(Request $request, Iaa $iaa)
    {
        // 1. Yetki Kontrolü (Senin yapın)
        $this->authorize('update', $iaa);

        // 2. Gelen Verileri Doğrula
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            
            // DİKKAT: Bölüm ve Lokasyon (ilgili_alan) burada doğrulanıp kaydediliyor
            'bolum_id' => 'required|exists:bolumler,id', 
            'ilgili_alan' => 'nullable|string|max:255', 
            
            'mevcut_durum' => 'required|string',
            'oneri' => 'required|string',

            // Finansal Alanlar
            'oneren_kazanc_miktar' => 'nullable|numeric',
            'oneren_butce_miktar' => 'nullable|numeric',
            'para_birimi' => 'required|string',

            // Resim Kontrolleri
            'yeni_resimler.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'silinecek_resimler.*' => 'nullable|integer|exists:iaa_resimler,id',
        ]);

        // 3. Para Birimini Veritabanındaki Sütunlara Eşle
        // Formdan tek bir 'para_birimi' geliyor ama veritabanında iki ayrı alan var.
        $validated['oneren_kazanc_birim'] = $request->para_birimi;
        $validated['oneren_butce_birim'] = $request->para_birimi;

        // 4. Verileri Güncelle (Update komutu burada çalışır)
        $iaa->update($validated);

        // 5. Resim Silme İşlemleri
        if ($request->has('silinecek_resimler')) {
            foreach ($request->silinecek_resimler as $resimId) {
                $resim = $iaa->resimler()->find($resimId);
                if ($resim) {
                    \Storage::disk('public')->delete($resim->dosya_yolu);
                    $resim->delete();
                }
            }
        }

        // 6. Yeni Resim Yükleme İşlemleri
        if ($request->hasFile('yeni_resimler')) {
            foreach ($request->file('yeni_resimler') as $file) {
                $path = $file->store('iaa_resimler', 'public');
                $iaa->resimler()->create(['dosya_yolu' => $path]);
            }
        }

        // 7. Yönlendirme
        return redirect()->route('iaa.show', $iaa)->with('success', 'Öneri başarıyla güncellendi.');
    }
    
    /**
     * ====================================================================
     * TÜM KULLANICILAR İÇİN İYİLEŞTİRME HAVUZUNU LİSTELER
     * ====================================================================
     * Havuzdaki önerileri, kullanıcının lideri olduğu takımları ve bu takımların
     * daha önce talep ettiği önerileri view'e gönderir.
     */
    public function havuz()
    {
        $kullanici = auth()->user();
        $liderOlduguTakimlar = $kullanici->lideriOlduguTakimlar;
        
        $talepEdilenIaaIdleri = DB::table('iaa_talepleri')
                                    ->whereIn('takim_id', $liderOlduguTakimlar->pluck('id'))
                                    ->pluck('iaa_id');

        $havuzdakiler = Iaa::where('durum', 'Havuzda')->with('gonderen', 'bolum')->latest()->get();

        return view('iaa.havuz', compact('havuzdakiler', 'liderOlduguTakimlar', 'talepEdilenIaaIdleri'));
    }

    /**
     * MANUEL KONTROLLÜ TALEP METODU
     */
    // DİKKAT: İkinci parametredeki 'Iaa $iaa' kısmını '$id' yaptık.
    public function takimlaTalepEt(Request $request, $id) 
    {
        // 1. Önce ID'nin doğru gelip gelmediğini test edelim.
        // Eğer bu satır ekrana basılırsa ROTA ÇALIŞIYOR demektir.
        // dd("Rota çalıştı! Gelen ID: " . $id); // Test için yorumu açabilirsiniz

        // 2. Kaydı manuel bulalım (Silinmişler dahil baksın diye withTrashed ekleyebiliriz ama şimdilik normal bakalım)
        $iaa = Iaa::find($id);

        if (!$iaa) {
            // Eğer kayıt yoksa 404 vermek yerine hatayı biz söyleyelim
            return back()->with('error', 'HATA: ID numarası ' . $id . ' olan öneri veritabanında bulunamadı! (Silinmiş olabilir)');
        }

        // --- Buradan sonrası eski kodun aynısı ---
        
        $kullanici = auth()->user();
        
        // Takım Lideri Kontrolü
        if ($kullanici->lideriOlduguTakimlar->isEmpty()) {
             return back()->with('error', 'Bu işlem için bir takımın lideri olmalısınız.');
        }

        $validated = $request->validate([
            'takim_id' => [
                'required',
                'exists:takimlar,id',
                Rule::in($kullanici->lideriOlduguTakimlar->pluck('id')),
            ],
        ]);
        
        $takim_id = $validated['takim_id'];

        $mevcutTalep = DB::table('iaa_talepleri')
            ->where('iaa_id', $iaa->id)
            ->where('takim_id', $takim_id)
            ->exists();

        if ($mevcutTalep) {
            return back()->with('error', 'Bu takımla zaten bu öneriye talepte bulunulmuş.');
        }

        DB::table('iaa_talepleri')->insert([
            'iaa_id' => $iaa->id,
            'takim_id' => $takim_id,
            'talep_eden_user_id' => $kullanici->id,
            'durum' => 'beklemede',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // === BİLDİRİM SİSTEMİ (try-catch ile korunuyor) ===
        try {
            $takim = Takim::with('uyeler', 'lider')->find($takim_id);
            
            // 1. Superadmin'lere Gönder
            $superadmins = User::role('Superadmin')->get();
            Notification::send($superadmins, new IaaProjesineTalepGeldi($iaa, $takim, $kullanici, 'superadmin'));

            // 2. Takım Üyelerinin Bölüm Liderleri ve Direktörleri
            $uyeler = $takim->uyeler->merge([$takim->lider]);
            $bolumLideriIds = [];
            $direktorIds = [];

            foreach ($uyeler as $uye) {
                if ($uye->bolum_id) {
                    // Bölüm Liderini Bul (Bölüm Lideri rolüne sahip ve o bölüme atanmış olanlar)
                    $liderler = User::role('Bölüm Lideri')->where('bolum_id', $uye->bolum_id)->get();
                    foreach($liderler as $l) {
                        $bolumLideriIds[] = $l->id;
                    }

                    // Direktörü Bul
                    $bolum = Bolum::with('director')->find($uye->bolum_id);
                    if ($bolum && $bolum->director_id) {
                        $direktorIds[] = $bolum->director_id;
                    }
                }
            }

            // Tekilleştir ve Gönder
            $bolumLiderleri = User::whereIn('id', array_unique($bolumLideriIds))->get();
            if ($bolumLiderleri->isNotEmpty()) {
                Notification::send($bolumLiderleri, new IaaProjesineTalepGeldi($iaa, $takim, $kullanici, 'bolum_lideri'));
            }

            $direktorler = User::whereIn('id', array_unique($direktorIds))->get();
            if ($direktorler->isNotEmpty()) {
                Notification::send($direktorler, new IaaProjesineTalepGeldi($iaa, $takim, $kullanici, 'direktor'));
            }
        } catch (\Exception $e) {
            \App\Helpers\MailLogHelper::logFailure(
                $iaa,
                'İAA Takım Talebi Bildirimi',
                User::role('Superadmin')->get()->merge($bolumLiderleri ?? collect())->merge($direktorler ?? collect()),
                $e->getMessage(),
                null,
                null,
                $iaa->bolum_id
            );
        }

        return redirect()->route('iaa.havuz')->with('success', 'Takımınızın talebi başarıyla yönetici onayına gönderildi.');
    }

    /**
     * TALEBİ GERİ ÇEKME METODU
     */
    public function talebiGeriCek(Iaa $iaa)
    {
        $kullanici = auth()->user();
        
        // Kullanıcının lider olduğu takımları bul
        $liderOlduguTakimlar = $kullanici->lideriOlduguTakimlar;
        
        if ($liderOlduguTakimlar->isEmpty()) {
            return back()->with('error', 'Bu işlem için bir takımın lideri olmalısınız.');
        }

        // Bu İAA için bu kullanıcının takımlarının yaptığı VE DURUMU 'beklemede' OLAN talepleri sil
        $silinen = DB::table('iaa_talepleri')
            ->where('iaa_id', $iaa->id)
            ->whereIn('takim_id', $liderOlduguTakimlar->pluck('id'))
            ->where('durum', 'beklemede')
            ->delete();

        if ($silinen) {
            return redirect()->route('iaa.havuz')->with('success', 'Talebiniz başarıyla geri çekildi.');
        }

        return back()->with('error', 'Geri çekilecek aktif bir talep bulunamadı.');
    }
    /**
     * ====================================================================
     * YENİ: KULLANICININ TAKIMLARININ PROJELERİNİ VE TALEPLERİNİ LİSTELER
     * ====================================================================
     * Eski 'taleplerim' metodunun yerine geçer. Artık takımların
     * onay bekleyen taleplerini ve atanmış projelerini listeler.
     */

     public function takimProjeleri()
    {
        $user = Auth::user();
        
        // 1. KULLANICININ ÜYE OLDUĞU TAKIMLAR (Kartlar için gerekli)
        $katildigimTakimlar = $user->takimlar()
            ->with('lider')
            ->withCount(['uyeler', 'atananProjeler'])
            ->latest('pivot_created_at')
            ->get();
            
        // [DÜZELTME] Değişken ismi hatası giderildi
        $takimIdleri = $katildigimTakimlar->pluck('id');

        // === 2. SQUAD (GEÇİCİ) ÜYELİKLER ===
        $squadProjeIdleri = $user->gorevliOlduguProjeler()
                                 ->wherePivot('durum', 'onaylandi')
                                 ->pluck('iaas.id')
                                 ->toArray();

        // 3. VERİLERİ ÇEKME

        // A) Bekleyen Talepler (Havuz)
        $bekleyenTalepler = DB::table('iaa_talepleri')
            ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
            ->join('takimlar', 'iaa_talepleri.takim_id', '=', 'takimlar.id')
            ->whereIn('iaa_talepleri.takim_id', $takimIdleri)
            ->where('iaa_talepleri.durum', 'beklemede')
            ->where('iaas.durum', 'Havuzda') 
            ->select('iaas.id as iaa_id', 'iaas.baslik', 'takimlar.ad as takim_adi', 'iaa_talepleri.created_at', 'iaa_talepleri.durum as talep_durumu')
            ->latest('iaa_talepleri.created_at') 
            ->get();

        // B) Aktif Projeler
        $atanmisProjeler = Iaa::with(['atananTakim', 'musteriSikayeti'])
            ->where(function($query) use ($takimIdleri, $squadProjeIdleri) {
                $query->whereIn('atanan_takim_id', $takimIdleri)
                      ->orWhereIn('id', $squadProjeIdleri);
            })
            ->whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']) 
            ->latest('updated_at')
            ->get();

        // C) Onay Bekleyen Tamamlanmış
        $onayBekleyenTamamlanmisProjeler = Iaa::with([
                'atananTakim', 
                'musteriSikayeti',
                'logs' => function ($query) {
                    $query->whereIn('eylem', ['Revizyon Talep Edildi', 'Proje Tamamlandı (İadesiz)', 'İade Girildi/Güncellendi'])->latest('created_at');
                }
            ])
            ->where(function($query) use ($takimIdleri, $squadProjeIdleri) {
                $query->whereIn('atanan_takim_id', $takimIdleri)
                      ->orWhereIn('id', $squadProjeIdleri);
            })
            ->whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])
            ->orderByDesc('updated_at')
            ->get();

        // D) Tamamlananlar
        $tamamlananProjeler = Iaa::with(['atananTakim', 'musteriSikayeti'])
            ->where(function($query) use ($takimIdleri, $squadProjeIdleri) {
                $query->whereIn('atanan_takim_id', $takimIdleri)
                      ->orWhereIn('id', $squadProjeIdleri);
            })
            ->where('durum', 'Tamamlandı')
            ->orderByDesc('onaylanma_tarihi')
            ->take(10)
            ->get();

        // E) Kişisel Görevler
        $banaAtananAdimlar = DB::table('iaa_step_assignments')
            ->join('iaas', 'iaa_step_assignments.iaa_id', '=', 'iaas.id')
            ->join('iaa_talepleri', 'iaas.id', '=', 'iaa_talepleri.iaa_id') 
            ->join('iaa_workflow_steps', 'iaa_step_assignments.iaa_workflow_step_id', '=', 'iaa_workflow_steps.id')
            ->leftJoin('iaa_progress_updates', function($join) {
                $join->on('iaa_talepleri.id', '=', 'iaa_progress_updates.iaa_talep_id')
                     ->on('iaa_step_assignments.iaa_workflow_step_id', '=', 'iaa_progress_updates.iaa_workflow_step_id');
            })
            ->where('iaa_step_assignments.user_id', $user->id)
            ->where('iaas.durum', '!=', 'Tamamlandı') 
            ->whereNull('iaa_progress_updates.completed_at') 
            ->select('iaas.id as iaa_id', 'iaas.baslik', 'iaa_workflow_steps.name as adim_adi', 'iaa_step_assignments.created_at as atama_tarihi')
            ->get();

        // İstatistikler
        $stats = [
            'aktif' => $atanmisProjeler->count(),
            'talep' => $bekleyenTalepler->count(),
            'onay_bekleyen_tamamlanmis' => $onayBekleyenTamamlanmisProjeler->count(),
            'tamamlanan' => $tamamlananProjeler->count(),
            'toplam_puan' => $user->toplam_puan
        ];

        return view('iaa.takim-projeleri', compact(
            'katildigimTakimlar', 
            'bekleyenTalepler',
            'atanmisProjeler',
            'tamamlananProjeler',
            'onayBekleyenTamamlanmisProjeler',
            'stats',
            'banaAtananAdimlar'
        ));
    }

     /**
     * Kullanıcının proje davetine verdiği yanıtı işler.
     */
    public function davetYanitla(Request $request, Iaa $iaa)
    {
        $user = Auth::user();
        $yanit = $request->input('yanit'); // 'kabul' veya 'red'

        // Pivot tablosundaki kaydı bul
        $pivotKayit = DB::table('iaa_user')
                        ->where('iaa_id', $iaa->id)
                        ->where('user_id', $user->id)
                        ->first();

        if (!$pivotKayit) {
            return back()->with('error', 'Bu proje için geçerli bir davet bulunamadı.');
        }

        // Durumu belirle
        $yeniDurum = ($yanit === 'kabul') ? 'onaylandi' : 'reddedildi';
        
        // 1. Veritabanını Güncelle (Pivot Tablo)
        $iaa->projeEkibi()->updateExistingPivot($user->id, ['durum' => $yeniDurum]);

        // 2. BİLDİRİM GÖNDERME KISMI ===
        try {
            $bildirim = new \App\Notifications\ProjeDavetYaniti($iaa, $user, $yanit);
            $alicilar = collect();

            // A) Proje Liderini Ekle (Erhan Cesur)
            if ($iaa->atananTakim && $iaa->atananTakim->lider_user_id) {
                $lider = User::find($iaa->atananTakim->lider_user_id);
                if ($lider) {
                    $alicilar->push($lider);
                }
            }

            // B) [YENİ] Personelin Bölüm Liderini Ekle (Emrah Al)
            // Eğer personel bir bölüme bağlıysa, o bölümün liderlerini bul
            if ($user->bolum_id) {
                $mudurler = User::role('Bölüm Lideri')
                                ->where('bolum_id', $user->bolum_id)
                                ->where('id', '!=', $user->id) // Eğer personel zaten müdürse kendine atmasın
                                ->get();
                
                if ($mudurler->isNotEmpty()) {
                    $alicilar = $alicilar->merge($mudurler);
                }
            }

            // C) Tekilleştir ve Gönder
            $alicilar = $alicilar->unique('id');
            
            if ($alicilar->isNotEmpty()) {
                Notification::send($alicilar, $bildirim);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Davet yanıtı bildirimi hatası: ' . $e->getMessage());
        }
        // ===================================

        // 3. Yönlendirme
        if ($yanit === 'kabul') {
            return back()->with('success', 'Daveti kabul ettiniz.');
        } else {
            // Eğer detay sayfasındaysa ve reddedince yetkisi kalmıyorsa davetlerim'e yönlendir
            $service = app(\App\Services\ProjectWorkspace\ProjeCalismaAlaniService::class);
            if (!$service->authorizeUser($iaa)) {
                return redirect()->route('takimlar.davetlerim')
                                ->with('info', 'Proje davetini reddettiniz.');
            }
            return back()->with('info', 'Proje davetini reddettiniz.');
        }
    }
}