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

class IaaController extends Controller
{
    use AuthorizesRequests;

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
        return view('iaa.create');
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
            'resimler' => 'nullable|array',
            'resimler.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
            'para_birimi' => 'nullable|string|max:10',
            'oneren_kazanc_miktar' => 'nullable|numeric|min:0',
            'oneren_butce_miktar' => 'nullable|numeric|min:0'
        ]);
        
        DB::transaction(function () use ($validated, $request) {
            $iaaData = [
                'gonderen_user_id' => Auth::id(),
                'bolum_id' => Auth::user()->bolum_id,
                'baslik' => $validated['baslik'],
                'mevcut_durum' => $validated['mevcut_durum'],
                'oneri' => $validated['oneri'],
                'oneren_kazanc_miktar' => $validated['oneren_kazanc_miktar'] ?? null,
                'oneren_butce_miktar' => $validated['oneren_butce_miktar'] ?? null,
                'oneren_kazanc_birim' => $validated['para_birimi'] ?? null,
                'oneren_butce_birim' => $validated['para_birimi'] ?? null,
            ];

            $iaa = Iaa::create($iaaData);

            if ($request->hasFile('resimler')) {
                foreach ($request->file('resimler') as $file) {
                    // 1. Tarih, saat ve mikrosaniye içeren benzersiz bir dosya adı oluşturuyoruz.
                    $filename = 'iaa_' . now()->format('Ymd_Hisu') . '.' . $file->getClientOriginalExtension();
            
                    // 2. storeAs() ile dosyayı kendi belirlediğimiz adla kaydediyoruz.
                    $path = $file->storeAs('iaa_resimleri', $filename, 'public'); 
            
                    $iaa->resimler()->create(['dosya_yolu' => $path]);
                }
            }
        });

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
    public function edit(Iaa $iaa)
    {
        $this->authorize('update', $iaa); // Policy'deki 'update' kuralını kullanır.

        return view('iaa.edit', compact('iaa'));
    }

    /**
     * ====================================================================
     * DÜZENLENEN İAA ÖNERİSİNİ GÜNCELLER
     * ====================================================================
     */
    public function update(Request $request, Iaa $iaa)
    {
        $this->authorize('update', $iaa);

        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'mevcut_durum' => 'required|string',
            'oneri' => 'required|string',
            'silinecek_resimler' => 'nullable|array',
            'silinecek_resimler.*' => 'exists:iaa_resimler,id',
            'yeni_resimler' => 'nullable|array',
            'yeni_resimler.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($request, $iaa, $validated) {
            $iaa->update([
                'baslik' => $validated['baslik'],
                'mevcut_durum' => $validated['mevcut_durum'],
                'oneri' => $validated['oneri'],
            ]);

            if (isset($validated['silinecek_resimler'])) {
                foreach ($validated['silinecek_resimler'] as $resimId) {
                    $resim = IaaResim::find($resimId);
                    if ($resim && $resim->iaa_id === $iaa->id) {
                        $resim->delete();
                    }
                }
            }

            if ($request->hasFile('yeni_resimler')) {
                foreach ($request->file('yeni_resimler') as $file) {
                    // 1. Aynı şekilde benzersiz ve anlamlı bir dosya adı oluşturuyoruz.
                    $filename = 'iaa_' . now()->format('Ymd_Hisu') . '.' . $file->getClientOriginalExtension();
            
                    // 2. storeAs() ile dosyayı kendi belirlediğimiz adla kaydediyoruz.
                    $path = $file->storeAs('iaa_resimleri', $filename, 'public'); 
            
                    $iaa->resimler()->create(['dosya_yolu' => $path]);
                }
            }
        });

        return redirect()->route('iaa.index')->with('success', 'İAA önerisi ve resimler başarıyla güncellendi!');
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
     * ====================================================================
     * BİR TAKIM LİDERİNİN TALEP GÖNDERME İŞLEMİNİ YAPAR
     * ====================================================================
     * Gelen isteği doğrular ve yeni talebi 'iaa_talepleri' tablosuna kaydeder.
     */
    public function takimlaTalepEt(Request $request, Iaa $iaa)
    {
        $kullanici = auth()->user();
        
        $validated = $request->validate([
            'takim_id' => [
                'required',
                'exists:takimlar,id',
                Rule::in($kullanici->lideriOlduguTakimlar->pluck('id')),
            ],
        ]);
        
        $takim_id = $validated['takim_id'];

        $mevcutTalep = DB::table('iaa_talepleri')->where('iaa_id', $iaa->id)->where('takim_id', $takim_id)->exists();

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

        return redirect()->route('iaa.havuz')->with('success', 'Takımınızın talebi başarıyla yönetici onayına gönderildi.');
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
         $kullanici = auth()->user();
         
         // 1. Kullanıcının "Ana Takım" (Kalıcı) üyelikleri
         $takimIdleri = $kullanici->takimlar->pluck('id'); 
 
         // === YENİ EKLENEN KISIM: SQUAD (GEÇİCİ) ÜYELİKLER ===
         // Kullanıcının "iaa_user" tablosunda ekli olduğu ve "onaylandi" durumundaki proje ID'leri
         $squadProjeIdleri = $kullanici->gorevliOlduguProjeler()
                                       ->wherePivot('durum', 'onaylandi')
                                       ->pluck('iaas.id')
                                       ->toArray();
         // =====================================================
 
         // ---------------------------------------------------------------------
         // 1. Onay Bekleyen Talepler (Havuz)
         // Not: Havuzdaki işler genelde takıma aittir, squad henüz kurulmamıştır.
         // Bu kısmı değiştirmemize gerek yok, sadece ana takım üyeleri görür.
         // ---------------------------------------------------------------------
         $bekleyenTalepler = DB::table('iaa_talepleri')
             ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
             ->join('takimlar', 'iaa_talepleri.takim_id', '=', 'takimlar.id')
             ->whereIn('iaa_talepleri.takim_id', $takimIdleri)
             ->where('iaa_talepleri.durum', 'beklemede')
             ->where('iaas.durum', 'Havuzda') 
             ->select(
                 'iaas.id as iaa_id',
                 'iaas.baslik',
                 'takimlar.ad as takim_adi', 
                 'iaa_talepleri.created_at',
                 'iaa_talepleri.durum as talep_durumu'
             )
             ->latest('iaa_talepleri.created_at') 
             ->get();
      
         // 2. Atanmış (Aktif) Projeler
        // GÜNCELLEME: 'Bölüm Onayı Bekliyor' ve diğer ara statüler eklendi.
        $atanmisProjeler = \App\Models\Iaa::with('atananTakim')
        ->where(function($query) use ($takimIdleri, $squadProjeIdleri) {
            $query->whereIn('atanan_takim_id', $takimIdleri)
                  ->orWhereIn('id', $squadProjeIdleri);
        })
        ->whereIn('durum', [
            'Atandı', 
            'Revize Ediliyor', 
            'Tamamlanması Reddedildi', 
            'Bölüm Onayı Bekliyor',    // <-- EKLENDİ (Cihangir artık görecek)
            'Yönetici Onayı Bekliyor'  // <-- EKLENDİ
        ]) 
        ->latest('updated_at') 
        ->get();
                              
         // ---------------------------------------------------------------------
         // 3. Tamamlanan Projeler - GÜNCELLENDİ
         // Aynı mantık buraya da uygulandı
         // ---------------------------------------------------------------------
         $tamamlananProjeler = \App\Models\Iaa::with('atananTakim')
             ->where(function($query) use ($takimIdleri, $squadProjeIdleri) {
                 $query->whereIn('atanan_takim_id', $takimIdleri)
                       ->orWhereIn('id', $squadProjeIdleri);
             })
             ->where('durum', 'Tamamlandı')
             ->where('puan', '>', 0) 
             ->orderByDesc('onaylanma_tarihi') 
             ->get();
      
         // ---------------------------------------------------------------------
         // 4. Onay Bekleyen TAMAMLANMIŞ Projeler - GÜNCELLENDİ
         // Aynı mantık buraya da uygulandı
         // ---------------------------------------------------------------------
         $onayBekleyenTamamlanmisProjeler = \App\Models\Iaa::with([
                 'atananTakim', 
                 'logs' => function ($query) {
                     $query->where('eylem', 'Revizyon Talep Edildi')
                           ->with('user')
                           ->latest('created_at');
                 }
             ])
             ->where(function($query) use ($takimIdleri, $squadProjeIdleri) {
                 $query->whereIn('atanan_takim_id', $takimIdleri)
                       ->orWhereIn('id', $squadProjeIdleri);
             })
             ->where('durum', 'Yönetici Onayı Bekliyor')
             ->orderByDesc('updated_at')
             ->get();
      
         // İstatistikler
         $stats = [
             'aktif' => $atanmisProjeler->count(),
             'talep' => $bekleyenTalepler->count(),
             'onay_bekleyen_tamamlanmis' => $onayBekleyenTamamlanmisProjeler->count(),
             'tamamlanan' => $tamamlananProjeler->count(),
             'toplam_puan' => $tamamlananProjeler->sum('puan')
         ];
        
         return view('iaa.takim-projeleri', compact(
             'bekleyenTalepler',
             'atanmisProjeler',
             'tamamlananProjeler',
             'onayBekleyenTamamlanmisProjeler',
             'stats'
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

        // 2. Lidere Bildirim Gönder
        try {
            $liderId = $iaa->atananTakim->lider_user_id;
            if ($liderId) {
                $lider = User::find($liderId);
                if ($lider) {
                    // Yanıt bildirimini gönder
                    \Illuminate\Support\Facades\Notification::send(
                        $lider, 
                        new \App\Notifications\ProjeDavetYaniti($iaa, $user, $yanit)
                    );
                }
            }
        } catch (\Exception $e) {
            // Mail/Bildirim hatası işlemi durdurmasın
            \Illuminate\Support\Facades\Log::error('Davet yanıtı bildirimi hatası: ' . $e->getMessage());
        }

        // 3. Yönlendirme
        if ($yanit === 'kabul') {
            return redirect()->route('proje.workspace.show', $iaa->id)
                             ->with('success', 'Daveti kabul ettiniz, proje alanına yönlendirildiniz.');
        } else {
            return redirect()->route('dashboard')
                             ->with('info', 'Proje davetini reddettiniz.');
        }
    }
}