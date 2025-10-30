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

class IaaYonetimController extends Controller
{
    /**
     * ====================================================================
     * TÜM İAA'LARI DURUMLARINA GÖRE LİSTELER
     * ====================================================================
     */

    public function index(Request $request)
    {
        // Onay bekleyenleri al (Değişiklik yok)
        $onayBekleyenKullanicilar = Iaa::where('durum', 'Onay Bekliyor')->whereNotNull('gonderen_user_id')->with('gonderen', 'bolum')->latest()->get();
        $onayBekleyenMisafirler = Iaa::where('durum', 'Onay Bekliyor')->whereNull('gonderen_user_id')->with('gonderen', 'bolum')->latest()->get();
        
        // ================== GÜNCELLEME BURADA ==================

        // TALEP ALAN: Durumu "Havuzda" olan VE en az bir talebi olanlar.
        $talepAlanOneriler = Iaa::where('durum', 'Havuzda')
                                ->has('talepEdenTakimlar')
                                ->withCount('talepEdenTakimlar')
                                ->latest()
                                ->get();

         // ATANMIŞ OLANLAR: Durumu "Atandı" VEYA "Revize Ediliyor" olanlar.
        // =============================================================
        $atanmisOlanlar = Iaa::whereIn('durum', ['Atandı', 'Revize Ediliyor'])
                            ->with('gonderen', 'bolum', 'atananTakim')
                            ->latest()
                            ->get();
        // =============================================================

        // HAVUZDAKİLER: Durumu "Havuzda" olan VE HİÇ talebi olmayanlar.
        $havuzdakiler = Iaa::where('durum', 'Havuzda')
                        ->doesntHave('talepEdenTakimlar')
                        ->with('gonderen', 'bolum', 'onaylayan')
                        ->latest()
                        ->get();

        // REDDEDİLENLER (Değişiklik yok)
        $reddedilenler = Iaa::where('durum', 'Reddedildi')->with('gonderen', 'bolum', 'onaylayan')->latest()->get();

        // YÖNETİCİ ONAYI BEKLEYEN TAMAMLANMIŞ PROJELER
        $yoneticiOnayiBekleyenler = Iaa::where('durum', 'Yönetici Onayı Bekliyor')
                                ->with('gonderen', 'bolum', 'atananTakim', 'workflow.steps') // <-- Zinciri basitleştirdik
                                ->latest()
                                ->get();

        // ===========================================
        // ===== 1. YENİ SORGUMUZU BURAYA EKLEYİN =====
        // ===========================================
        $tamamlanmasiReddedilenler = Iaa::where('durum', 'Tamamlanması Reddedildi')->with('gonderen', 'bolum', 'atananTakim')->latest()->get();
        
        // SON TAMAMLANAN PROJELER
        $sonTamamlananlar = Iaa::where('durum', 'Tamamlandı')->orderBy('onaylanma_tarihi', 'desc')->take(5)->get();

         // ================== GÜNCELLEME BURADA ==================
        // TAMAMLANAN PROJELERİN TOPLAM SAYISI (İstatistik kartı için)
        $tamamlananCount = Iaa::where('durum', 'Tamamlandı')->count();
                        

        // İSTATİSTİK KARTLARI için sayımları yeniden düzenliyoruz.
        // ========================================================
        // DOĞRU VE TAM $stats DİZİSİ
        // ========================================================
        $stats = [
            'onayBekleyen' => $onayBekleyenKullanicilar->count() + $onayBekleyenMisafirler->count(),
            'talepAlan' => $talepAlanOneriler->count(),
            'atanmis' => $atanmisOlanlar->count(), // Sadece bu değişkenin sayımını kullanıyoruz
            'havuzda' => $havuzdakiler->count(),
            'reddedilen' => $reddedilenler->count() + $tamamlanmasiReddedilenler->count(), // İsterseniz bunları ayırabilirsiniz, şimdilik öneriler ve tamamlanma reddini topladım.
            'yoneticiOnayi' => $yoneticiOnayiBekleyenler->count(),
            'tamamlanan' => Iaa::where('durum', 'Tamamlandı')->count(), // Sayıyı doğrudan burada alıyoruz
            
        ];
        // ========================================================

        return view('admin.iaa-yonetim.index', compact(
            'onayBekleyenKullanicilar', 
            'onayBekleyenMisafirler',  
            'talepAlanOneriler',
            'atanmisOlanlar',
            'havuzdakiler', 
            'reddedilenler',
            'yoneticiOnayiBekleyenler',
            'tamamlanmasiReddedilenler', // <-- 2. YENİ DEĞİŞKENİ BURAYA EKLEYİN
            'sonTamamlananlar',
            'stats' // Sadece stats dizisini göndermemiz yeterli
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

        DB::transaction(function () use ($iaa, $takim, $validated) {
            // 1. İAA'nın kendi durumunu ve atanan takım ID'sini güncelle.
            $iaa->update([
                'durum' => 'Atandı',
                'atanan_takim_id' => $takim->id,
            ]);

            // 2. Takım ve İAA arasındaki pivot kaydını Eloquent ilişkisi üzerinden güncelle.
            // Bu yöntem, kolon adlarını bizim tahmin etmemize gerek bırakmaz.
            // Laravel doğru kolon adlarını ('iaa_id', 'takim_id' veya farklı bir şey) kendisi bulur.
            $takim->talepEttigiIaalar()->updateExistingPivot($iaa->id, [
                'iaa_workflow_id' => $validated['iaa_workflow_id'],
                'start_date' => now(),
                'due_date' => $validated['due_date'],
                'status' => 'Devam Ediyor',
            ]);

            // 3. Diğer takımların taleplerini, yine ilişki üzerinden, pivot tablodan kaldır (detach).
            // Önce diğer talep eden takımların ID'lerini alalım.
            $digerTakimIdleri = $iaa->talepEdenTakimlar()->where('takim_id', '!=', $takim->id)->pluck('takim_id');
            // Sonra bu takımların bu İAA ile olan bağını koparalım.
            if ($digerTakimIdleri->isNotEmpty()) {
                $iaa->talepEdenTakimlar()->detach($digerTakimIdleri);
            }
        });

        return redirect()->route('admin.iaa-yonetim.index')
                         ->with('success', '"' . $iaa->baslik . '" projesi, ' . $takim->ad . ' takımına başarıyla atandı.');
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
        if ($iaa->durum !== 'Onay Bekliyor') {
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
        ]);

        $puan = 0; // Puanı başlangıçta 0 yapalım

        // 2. Eğer kullanıcı tüm alanları doldurduysa puanı hesapla
        if ($request->filled('risk') && $request->filled('kazanc_miktar') && $request->filled('butce_miktar')) {
            $puan = round(($validated['risk'] * $validated['kazanc_miktar']) / $validated['butce_miktar']);
        } 
        // 3. Eğer alanlar boş bırakıldıysa, sistem ayarlarından standart puanı çek
        else {
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
        try {
            $request->validate(['yonetici_notu' => 'required|string|min:10']);
        } catch (ValidationException $e) {
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
        $oncekiDurum = $iaa->durum;
        $yeniDurum = null;

        // Loglama için Auth ve Log modelini import ettiğinizden emin olun.


        DB::transaction(function () use ($iaa, $oncekiDurum, &$yeniDurum) {

            switch ($oncekiDurum) {

                case 'Tamamlandı':
                    $takim = $iaa->atananTakim;
                    if ($takim && $iaa->puan > 0) {
                        
                        // 1. TAKIMDAN PUANI DÜŞÜR (Mevcut kodun)
                        $takim->decrement('toplam_puan', $iaa->puan);

                        // ================== GÜNCELLEME BURADA ==================
                        // 2. ÜYELERDEN PUANI DÜŞÜR (Eksik olan buydu)
                        foreach ($takim->uyeler as $uye) {
                            $uye->decrement('toplam_puan', $iaa->puan);
                        }
                        // ================== GÜNCELLEME SONU ==================

                        // Puan logu tutuyorsan, burada silmen gerekir. Örn:
                        // \App\Models\PuanLog::where('iaa_id', $iaa->id)->delete();
                    }
                    
                    // Onay ve tamamlanma tarihlerini sıfırla (Mevcut kodun)
                    $iaa->onaylanma_tarihi = null; 
                    // YENİ EKLEME: Sadece onay tarihini değil, 'tamamlanma' tarihini de sıfırla.
                    // 'onayla' fonksiyonu 'tamamlanma_tarihi'ne bakacak.
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
                    if ($iaa->iaaTalebi && $iaa->iaaTalebi->status === 'Tamamlandı') {
                        $yeniDurum = 'Yönetici Onayı Bekliyor';
                    
                    // Eğer proje havuzdan yeni atandıysa,
                    // Sizin kodunuzdaki gibi havuza geri döner ve talep kaydı silinir.
                    } else {
                        $iaa->atanan_takim_id = null;
                        DB::table('iaa_talepleri')->where('iaa_id', $iaa->id)->delete();
                        $yeniDurum = 'Havuzda';
                    }
                    break;
                
                case 'Reddedildi':
                    // Sizin de belirttiğiniz gibi, reddedilen bir önerinin önceki durumu farklı olabilir.
                    // Eğer proje daha önce bir takıma atanmışsa, "Yönetici Onayı Bekliyor"a döner.
                    if ($iaa->atanan_takim_id) {
                        $yeniDurum = 'Yönetici Onayı Bekliyor';

                    // Eğer hiç atanmamışsa, ilk baştaki "Onay Bekliyor" durumuna döner ve puan vs. sıfırlanır.
                    } else {
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

            if ($yeniDurum) {
                $iaa->durum = $yeniDurum;
                $iaa->save();

                IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'İşlem Geri Alındı',
                    'aciklama' => "Proje, '{$oncekiDurum}' durumundan '{$yeniDurum}' durumuna geri alındı."
                ]);
            }
        });

        if ($yeniDurum) {
            return redirect()->route('admin.iaa-yonetim.index')->with('success', 'İşlem başarıyla geri alındı.');
        }

        return redirect()->route('admin.iaa-yonetim.index')->with('error', 'Bu durum için geri alma işlemi tanımlanmamış.');
    }

    /**
     * ====================================================================
     * BİR İAA ÖNERİSİNİ KALICI OLARAK SİLER
     * ====================================================================
     */
    public function destroy(Iaa $iaa)
    {
        foreach ($iaa->resimler as $resim) {
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
        $validated = $request->validate([
            'iaa_ids' => 'required|array',
            'iaa_ids.*' => 'exists:iaas,id',
        ]);
        $iaaIds = $validated['iaa_ids'];
        DB::transaction(function () use ($iaaIds) {
            $iaas = Iaa::with('resimler')->whereIn('id', $iaaIds)->get();
            foreach ($iaas as $iaa) {
                foreach ($iaa->resimler as $resim) {
                    Storage::disk('public')->delete($resim->dosya_yolu);
                }
                $iaa->forceDelete();
            }
        });
        return redirect()->route('admin.iaa-yonetim.index')->with('success', count($iaaIds) . ' adet öneri kalıcı olarak silindi.');
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
        if ($iaa->durum !== 'Yönetici Onayı Bekliyor') {
            return back()->with('error', 'Bu proje onaylanabilir durumda değil.');
        }

        $takim = $iaa->atananTakim;
        $projePuani = $iaa->puan ?? 0;
        $puanVerildi = false; // Puan verilip verilmediğini izlemek için bayrak
        $logAciklama = 'Yönetici tarafından proje onaylandı ve "Tamamlandı" durumuna getirildi.';
        $sikayetGuncellendi = false;

        try {
            // 2. TÜM VERİTABANI İŞLEMLERİNİ GÜVENLİ BİR TRANSACTION İÇİNE AL
            DB::transaction(function () use ($iaa, $takim, $projePuani, &$puanVerildi, &$logAciklama, &$sikayetGuncellendi) {
                
                // 3. PUANLARI DAĞIT
                // Puanları SADECE daha önce onaylanmamışsa ver ('onaylanma_tarihi' null ise)
                if (is_null($iaa->onaylanma_tarihi) && $takim && $projePuani > 0) {
                    
                    // Takım puanını artır
                    $takim->increment('toplam_puan', $projePuani);

                    // Takımdaki TÜM üyelerin puanını artır
                    // Eloquent increment() yerine DB::table() kullanarak olası model hatalarını atlıyoruz.
                    $uyeIdleri = $takim->uyeler()->pluck('users.id');
                    User::whereIn('id', $uyeIdleri)->increment('toplam_puan', $projePuani);
                    
                    $puanVerildi = true;
                    $logAciklama = 'Proje onaylandı. ' . $projePuani . ' puan, ' . $takim->ad . ' takımına ve üyelerine eklendi.';
                
                } elseif (!is_null($iaa->onaylanma_tarihi)) {
                    $logAciklama .= ' (Proje daha önce onaylandığı için tekrar puan verilmedi.)';
                }

                // 4. PROJEYİ (IAA) GÜNCELLE
                $iaa->update([
                    'durum' => 'Tamamlandı',
                    'onaylanma_tarihi' => $iaa->onaylanma_tarihi ?? now(), // Sadece ilk onay tarihini kaydet
                    'yonetici_notu' => null // Revizyon notu varsa temizle
                ]);

                // 5. BAĞLI MÜŞTERİ ŞİKAYETİNİ GÜNCELLE (EN ÖNEMLİ KISIM)
                // Bu projeye bağlı bir şikayet var mı diye kontrol et (load ile değil, DB'den)
                $bagliSikayet = MusteriSikayeti::where('iaa_id', $iaa->id)->first();
                
                if ($bagliSikayet && $bagliSikayet->musteri_durum !== 'Kapatıldı') {
                    $bagliSikayet->update([
                        'musteri_durum' => 'Kapatıldı', // veya 'Çözümlendi'
                        'musteri_cozum_notlari' => ($bagliSikayet->musteri_cozum_notlari ?? '') . "\nİlgili IAA Projesi (ID: {$iaa->id}) yönetici tarafından onaylanarak kapatıldı.",
                        'kurul_onay_tarihi' => now()
                    ]);
                    $sikayetGuncellendi = true;
                }

                // 6. LOG KAYDI OLUŞTUR
                IaaLog::create([
                    'iaa_id' => $iaa->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Proje Onaylandı',
                    'aciklama' => $logAciklama
                ]);

            }); // Transaction sonu

        } catch (\Exception $e) {
            // Hata olursa geri al ve logla
            Log::error('Proje onaylanırken hata oluştu (approveCompleted): ' . $e->getMessage());
            return back()->with('error', 'Puanlar dağıtılırken veya şikayet güncellenirken kritik bir hata oluştu.');
        }

        // 7. CANLI RAPOR SAYFASINI GÜNCELLEMEK İÇİN OLAYI TETİKLE
        if ($sikayetGuncellendi) {
            try {
                event(new \App\Events\SikayetDurumuDegisti());
            } catch (\Exception $e) {
                Log::error('SikayetDurumuDegisti broadcast olayı gönderilemedi: ' . $e->getMessage());
            }
        }

        // 8. Başarı mesajıyla geri dön
        return back()->with('success', 'Proje başarıyla onaylandı!' . ($puanVerildi ? ' Puanlar dağıtıldı.' : ''));
    }

    /**
     * Tamamlanmış bir projeyi reddeder ve gerekçesini kaydeder.
     */
    public function rejectCompleted(Request $request, Iaa $iaa)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $iaa->update([
            'durum' => 'Tamamlanması Reddedildi',
            // YENİ EKLENEN SATIR: Notu kolayca göstermek için buraya da kaydediyoruz.
            'yonetici_notu' => $validated['rejection_reason']
        ]);
        
        // Gerekçeyi 'iaa_logs' tablosuna kaydetmeye devam ediyoruz (Mevcut kodunuz).
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Tamamlanmış Projenin Reddi',
            'aciklama' => $validated['rejection_reason']
        ]);

        return back()->with('success', 'Projenin tamamlanması reddedildi.');
    }

    /**
     * Tamamlanmış bir proje için revizyon ister ve talebi kaydeder.
     */
    public function requestRevision(Request $request, Iaa $iaa)
    {
        $validated = $request->validate([
            'revision_reason' => 'required|string|min:10',
        ]);
        
        $iaa->update([
            'durum' => 'Revize Ediliyor',
            // YENİ EKLENEN SATIR: Notu kolayca göstermek için buraya da kaydediyoruz.
            'yonetici_notu' => $validated['revision_reason']
        ]);
        
        // Revizyon talebini 'iaa_logs' tablosuna kaydetmeye devam ediyoruz (Mevcut kodunuz).
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Revizyon Talep Edildi',
            'aciklama' => $validated['revision_reason']
        ]);

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
        if ($topTeamResult) {
            $takim = Takim::find($topTeamResult->atanan_takim_id);
            if ($takim) {
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
        $users = \App\Models\User::where('onaylandi_mi', true)->orderBy('name')->get();
        return view('admin.iaa-yonetim.reassign', compact('iaa', 'users'));
    }

    /**
     * İAA önerisinin sahibini (öneren kişiyi) günceller.
     */
    public function reassignUpdate(Request $request, Iaa $iaa)
    {
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
        // 1. "onayla" metodundaki validation kurallarının aynısını kullanıyoruz (artık nullable).
        $validated = $request->validate([
            'risk' => 'nullable|integer|between:1,5',
            'kazanc_miktar' => 'nullable|numeric|min:0',
            'kazanc_birim' => 'nullable|string|max:10',
            'butce_miktar' => 'nullable|numeric|gt:0',
            'butce_birim' => 'nullable|string|max:10',
        ]);

        // 2. Puanı yeniden hesapla. Alanlar boşsa standart puanı ata.
        $puan = 0;
        if ($request->filled('risk') && $request->filled('kazanc_miktar') && $request->filled('butce_miktar')) {
            $puan = round(($validated['risk'] * $validated['kazanc_miktar']) / $validated['butce_miktar']);
        } else {
            $standartPuanAyar = \App\Models\Setting::where('key', 'standart_puan')->first();
            $puan = $standartPuanAyar->value ?? 100;
        }

        // 3. İAA kaydını yeni değerlerle güncelle.
        $iaa->update([
            'risk' => $validated['risk'],
            'kazanc_miktar' => $validated['kazanc_miktar'],
            'kazanc_birim' => $request->kazanc_birim,
            'butce_miktar' => $validated['butce_miktar'],
            'butce_birim' => $request->butce_birim,
            'puan' => $puan,
        ]);

        return back()->with('success', 'Proje puanı başarıyla güncellendi.');
    }

}

   
