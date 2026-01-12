<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArabuluculukCase;
use App\Models\ArabuluculukLog;
use App\Models\ArabuluculukFile;
use App\Models\User;
use App\Models\Arabulucu;
use App\Models\ArabuluculukAnlasmaMaddesi;
use App\Http\Requests\StoreArabuluculukRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ArabuluculukController extends Controller
{
    /**
     * LİSTELEME EKRANI
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. GENEL ERİŞİM KONTROLÜ (DİNAMİK)
        // Eğer menüyü görme yetkisi bile yoksa içeri alma.
        // (Superadmin her zaman girer)
        if (!$user->can('arabuluculuk.view_menu') && !$user->hasRole('Superadmin')) {
            abort(403, 'Bu modüle erişim yetkiniz yok.');
        }

        $query = ArabuluculukCase::with(['calisan', 'arabulucu', 'creator']);

        // 2. FİLTRELEME: Zorunlu Dosyaları Görme Yetkisi
        // Eğer kullanıcı Superadmin değilse ve 'view_zorunlu_files' yetkisi YOKSA -> Sadece İhtiyari
        // Bu sayede Personel'den "Zorunlu Gör" tikini kaldırırsan göremezler.
        if (!$user->hasRole('Superadmin') && !$user->can('arabuluculuk.view_zorunlu_files')) {
            $query->where('type', 'ihtiyari');
        }

        // 3. Dış Avukat Filtresi (Mevcut Mantık Korundu)
        if ($user->hasRole('Dış Avukat')) {
            $query->where('external_lawyer_id', $user->id);
        }

        // 4. Finans Filtresi (Opsiyonel: Sadece ödeme aşamasındakileri görsün dersen açabilirsin)
        // if ($user->hasRole('Arabuluculuk Finans')) { ... }

        $cases = $query->latest()->paginate(15);
        return view('admin.arabuluculuk.index', compact('cases'));
    }

    /**
     * DETAY EKRANI
     */
    public function show($id)
    {
        $case = ArabuluculukCase::with(['files', 'logs.user', 'payments', 'calisan', 'kurulDegerlendirmesi.user'])->findOrFail($id);
        $user = Auth::user();

        $anlasmaMaddeleri = ArabuluculukAnlasmaMaddesi::where('is_active', true)->get();

        $arabulucular = Arabulucu::where('is_active', true)->orderBy('name')->get();

        // 1. ZORUNLU DOSYA ERİŞİM KONTROLÜ (DİNAMİK)
        if ($case->type == 'zorunlu') {
            if (!$user->hasRole('Superadmin') && !$user->can('arabuluculuk.view_zorunlu_files')) {
                abort(403, 'Zorunlu arabuluculuk dosyalarına erişim yetkiniz yoktur.');
            }
        }

        // 2. Dış Avukat ve Bölüm Lideri Kısıtlamaları (Aynen Korundu)
        if ($user->hasRole('Dış Avukat') && $case->external_lawyer_id != $user->id) {
            abort(403);
        }
       

        return view('admin.arabuluculuk.show', compact('case', 'anlasmaMaddeleri', 'arabulucular'));
    }

    /**
     * YENİ DOSYA OLUŞTURMA FORMU
     */
    public function create()
    {
        $user = Auth::user();

        // 1. GENEL YETKİ KONTROLÜ
        // Herhangi bir oluşturma yetkisi yoksa (İhtiyari veya Zorunlu) -> 403
        if (!$user->hasRole('Superadmin') && !$user->canAny(['arabuluculuk.create_ihtiyari', 'arabuluculuk.create_zorunlu'])) {
            abort(403, 'Dosya açma yetkiniz yok.');
        }

        // 2. LİSTELERİ HAZIRLA
        // Çalışan listesi (Sadece personeller)
        $users = User::where('is_personnel', true)
            ->orderBy('name')
            ->get();

        $arabulucular = Arabulucu::where('is_active', true)->orderBy('name')->get();
        
        $internalLawyers = User::role(['Hukuk Admini', 'Hukuk Yöneticisi'])->orderBy('name')->get();
        $externalLawyers = User::role('Dış Avukat')->orderBy('name')->get();

        return view('admin.arabuluculuk.create', compact('users', 'arabulucular', 'internalLawyers', 'externalLawyers'));
    }

    /**
     * KAYIT İŞLEMİ
     */
    public function store(StoreArabuluculukRequest $request)
    {
        $user = Auth::user();

        // 1. ZORUNLU DOSYA KONTROLÜ (DİNAMİK)
        if ($request->type === 'zorunlu') {
            // Eğer Superadmin değilse ve 'create_zorunlu' yetkisi yoksa -> HATA
            if (!$user->hasRole('Superadmin') && !$user->can('arabuluculuk.create_zorunlu')) {
                return back()->with('error', 'Yetkisiz İşlem: Zorunlu dosya açma yetkiniz bulunmamaktadır.')->withInput();
            }
        }

        // 2. İHTİYARİ DOSYA KONTROLÜ (DİNAMİK)
        if ($request->type === 'ihtiyari') {
            if (!$user->hasRole('Superadmin') && !$user->can('arabuluculuk.create_ihtiyari')) {
                return back()->with('error', 'Yetkisiz İşlem: İhtiyari dosya açma yetkiniz bulunmamaktadır.')->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['created_by'] = $user->id;
            $data['status'] = 'taslak'; 
            
            // Owner Role Mantığı: Zorunlu ise Hukuk, İhtiyari ise Personel (Varsayılan)
            // Sistemde 'owner_role' hala kullanılıyor o yüzden bu mantığı tutuyoruz.
            if ($request->type == 'zorunlu') {
                $data['owner_role'] = 'hukuk';
                $data['board_required'] = true;
            } else {
                $data['owner_role'] = 'personel';
                $data['board_required'] = false;
            }

            $case = ArabuluculukCase::create($data);

            // LOGLAMA
            $this->logAction($case, 'OLUŞTURMA', "{$request->type} dosyası açıldı.");

            DB::commit();

            return redirect()->route('admin.arabuluculuk.show', $case->id)
                             ->with('success', 'Arabuluculuk dosyası başarıyla açıldı.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hata: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * DÜZENLEME İŞLEMİ
     */
    public function update(Request $request, $id)
    {
        $case = ArabuluculukCase::findOrFail($id);
        $user = Auth::user();

        // --- YENİ EKLENEN KİLİT KONTROLÜ ---
        // Eğer dosya 'taslak' değilse ve kullanıcı Superadmin değilse, düzenleme yapamaz.
        if ($case->status != 'taslak' && !$user->hasRole('Superadmin')) {
            return back()->with('error', 'Bu dosya onaya sunulduğu için üzerinde değişiklik yapılamaz. Değişiklik gerekliyse işlemi geri almalısınız.');
        }
        // -----------------------------------
        
        // Yetki Kontrolü
        if (!Auth::user()->can('arabuluculuk.edit') && !Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Düzenleme yetkiniz yok.');
        }

        // Validasyon
        $validated = $request->validate([
            'anlasilan_tutar' => 'nullable|numeric',
            'maddeler_secim' => 'nullable|array', // Checkbox dizisi
            'ek_notlar' => 'nullable|string',     // Manuel not alanı
            'karsi_taraf_teklif' => 'nullable|numeric',
        ]);

        // MADDELERİ BİRLEŞTİRME MANTIĞI
        $finalText = "";

        // 1. Seçilen Standart Maddeler
        if ($request->has('maddeler_secim')) {
            foreach ($request->maddeler_secim as $madde) {
                $finalText .= "- " . $madde . "\n";
            }
        }

        // 2. Ek Notlar
        if ($request->filled('ek_notlar')) {
            // Eğer yukarıda madde varsa araya boşluk koy
            if (!empty($finalText)) {
                $finalText .= "\n"; 
            }
            $finalText .= "EK NOTLAR:\n" . $request->ek_notlar;
        }

        // Veritabanına kaydet (anlasma_maddeleri sütununa)
        $case->update([
            'anlasilan_tutar' => $request->anlasilan_tutar,
            'anlasma_maddeleri' => $finalText, // Birleştirilmiş metni kaydediyoruz
            'karsi_taraf_teklif' => $request->karsi_taraf_teklif
        ]);

        $this->logAction($case, 'GÜNCELLEME', 'Anlaşma detayları güncellendi.');

        return back()->with('success', 'Bilgiler kaydedildi.');
    }

    /**
     * Hukuk ve Yönetim Karar Mekanizması
     */
    public function submitDecision(Request $request, $id)
    {
        $case = ArabuluculukCase::findOrFail($id);
        $user = Auth::user();
        $action = $request->input('action'); // hukuk_onay, hukuk_yonetim, hukuk_revize, yonetim_onay, yonetim_red
        $note = $request->input('note'); // Revizyon notu

        // --- 1. HUKUK KARARLARI ---
        if ($case->status == 'hukuk_incelemesinde') {
            
            // Yetki Kontrolü
            if (!$user->can('arabuluculuk.approve_legal') && !$user->hasRole('Superadmin')) {
                abort(403, 'Hukuk onayı verme yetkiniz yok.');
            }

            // A) YÖNETİME GÖNDER (Opsiyonel Seçenek)
            if ($action == 'send_to_board') {
                $case->update(['status' => 'yonetim_onayinda']);
                $this->logAction($case, 'HUKUK ONAYI', "Hukuk onayladı ve Yönetim onayına sundu. Not: $note");
                return back()->with('success', 'Dosya Yönetim onayına gönderildi.');
            }

            // B) DOĞRUDAN ONAYLA (İnisiyatif)
            if ($action == 'approve_direct') {
                // Yönetimi atlayıp direkt "Arabulucuda/İmza" aşamasına geçer
                $case->update(['status' => 'arabulucuda']); 
                $this->logAction($case, 'HUKUK ONAYI', "Hukuk inisiyatif kullanarak doğrudan onayladı. Not: $note");
                return back()->with('success', 'Dosya doğrudan onaylandı ve sonraki aşamaya geçildi.');
            }

            // C) REVİZYON İSTE (Personele Geri Gönder)
            if ($action == 'request_revision') {
                $request->validate(['note' => 'required|string|min:5'], ['note.required' => 'Revizyon için bir gerekçe yazmalısınız.']);
                
                $case->update([
                    'status' => 'taslak', // Düzenleme kilidini açmak için taslağa çekiyoruz
                    'mutabakat' => 'beklemede' // Personel tekrar butonlara basabilsin diye
                ]);
                
                // Personele görünmesi için notu kaydedebiliriz veya Log'dan okurlar.
                // Log yeterli olacaktır çünkü tarihçede görünüyor.
                $this->logAction($case, 'REVİZYON', "Hukuk revizyon istedi. Gerekçe: $note");
                
                return back()->with('success', 'Dosya revizyon için personele geri gönderildi.');
            }
        }

        // --- 2. YÖNETİM KARARLARI ---
        if ($case->status == 'yonetim_onayinda') {
            
            // Yetki Kontrolü
            if (!$user->can('arabuluculuk.approve_board') && !$user->hasRole('Superadmin')) {
                abort(403, 'Yönetim onayı verme yetkiniz yok.');
            }

            // A) ONAYLA
            if ($action == 'board_approve') {
                $case->update(['status' => 'arabulucuda']);
                $this->logAction($case, 'YÖNETİM ONAYI', "Yönetim onayladı. Not: $note");
                return back()->with('success', 'Yönetim onayı alındı.');
            }

            // B) REDDET / HUKUKA GERİ GÖNDER
            if ($action == 'board_reject') {
                $request->validate(['note' => 'required|string|min:5'], ['note.required' => 'Red/Revize için bir gerekçe yazmalısınız.']);

                // Dosyayı Hukuk'a geri atıyoruz
                $case->update(['status' => 'hukuk_incelemesinde']);
                
                $this->logAction($case, 'YÖNETİM REVİZE', "Yönetim dosyayı Hukuk birimine iade etti. Gerekçe: $note");
                return back()->with('success', 'Dosya tekrar incelenmek üzere Hukuk birimine gönderildi.');
            }
        }

        // --- 3. ARABULUCULUK SONUÇLANDIRMA (YENİ EKLENEN) ---
        if ($case->status == 'arabulucuda') {
            
            // Yetki: Hukuk Admini, Superadmin veya (Yetkisi varsa) Personel
            $yetkiVarMi = $user->can('arabuluculuk.approve_legal') || 
                          $user->hasRole('Superadmin') || 
                          ($user->can('arabuluculuk.assign_mediator') && $case->created_by == $user->id);

            if (!$yetkiVarMi) {
                abort(403, 'Bu işlemi yapmaya yetkiniz yok.');
            }

            // A) ANLAŞMA SAĞLANDI
            if ($action == 'mediation_agreement') {
                
                // === DOSYA KONTROLÜ (EKLENDİ) ===
                $hasFile = $case->files()->where('doc_type', 'arabuluculuk_son_tutanak')->exists();
                if (!$hasFile) {
                    return back()->with('error', 'İşlemi tamamlamak için lütfen önce "Dosyalar" sekmesinden "Arabuluculuk Son Tutanağı"nı yükleyiniz.');
                }
                // ================================

                $case->update(['status' => 'odeme_bekliyor']);
                
                $this->logAction($case, 'ARABULUCULUK SONUÇ', "Süreç ANLAŞMA ile sonuçlandı. Tutanak kontrol edildi.");
                return back()->with('success', 'Süreç anlaşma ile tamamlandı. Ödeme planı oluşturabilirsiniz.');
            }

            // B) ANLAŞMA SAĞLANAMADI
            if ($action == 'mediation_disagreement') {
                $case->update(['status' => 'anlasma_saglanamadi']);
                
                $this->logAction($case, 'ARABULUCULUK SONUÇ', "Arabuluculuk süreci ANLAŞMA SAĞLANAMADI olarak sonuçlandı. Not: $note");
                return back()->with('success', 'Dosya anlaşma sağlanamadı olarak kapatıldı.');
            }
        }

        return back()->with('error', 'Geçersiz işlem veya durum.');
    }

    /**
     * Durum Değiştirme ve Mutabakat İşlemleri
     */
    public function changeStatus(Request $request, $id)
    {
        $case = ArabuluculukCase::findOrFail($id);
        
        // --- 1. MUTABAKAT DEĞİŞİMİ (ANLAŞILDI / ANLAŞILMADI) ---
        // --- 1. MUTABAKAT DEĞİŞİMİ ---
        if ($request->has('mutabakat')) {

            // --- A) ANLAŞILDI SENARYOSU ---
            if ($request->mutabakat == 'anlasildi') {
                
                // 1. Madde ve Tutar Kontrolü
                if (empty($case->anlasma_maddeleri)) {
                    return back()->with('error', 'Lütfen önce "Düzenle" butonuna basıp Anlaşma Maddelerini seçiniz.');
                }
                if (empty($case->anlasilan_tutar)) {
                    return back()->with('error', 'Lütfen önce "Düzenle" butonuna basıp Anlaşılan Tutarı giriniz.');
                }

                // 2. Yanlış Belge Kontrolü (Anlaşma Sağlanamadı Tutanağı Yüklü mü?)
                $wrongFile = $case->files()->where('doc_type', 'anlasma_saglanamadi_tutanagi')->exists();
                if ($wrongFile) {
                    return back()->with('error', 'Sisteme "Anlaşma Sağlanamadı Tutanağı" yüklemişsiniz. Bu yüzden durumu "Anlaşıldı" yapamazsınız. Lütfen dosyalar sekmesinden yanlış dosyayı silip, "Taslak Anlaşma Belgesi" yükleyiniz.');
                }

                // 3. Doğru Belge Kontrolü
                $hasFile = $case->files()->where('doc_type', 'taslak_anlasma')->exists();
                if (!$hasFile) {
                    return back()->with('error', 'İşleme devam etmek için lütfen "Dosyalar" sekmesinden "Taslak Anlaşma Belgesi" yükleyiniz.');
                }

                // Başarılı
                $case->update([
                    'mutabakat' => 'anlasildi',
                    'status' => 'hukuk_incelemesinde'
                ]);
                $this->logAction($case, 'MUTABAKAT', "Personel anlaştı, hukuk onayına sunuldu.");
            } 
            
            // --- B) ANLAŞILMADI SENARYOSU ---
            elseif ($request->mutabakat == 'anlasilmadi') {
                
                // 1. Yanlış Belge Kontrolü (Taslak Anlaşma Yüklü mü?)
                $wrongFile = $case->files()->where('doc_type', 'taslak_anlasma')->exists();
                if ($wrongFile) {
                    return back()->with('error', 'Sisteme "Taslak Anlaşma Belgesi" yüklemişsiniz. Bu yüzden durumu "Anlaşılmadı" yapamazsınız. Lütfen dosyalar sekmesinden yanlış dosyayı silip, "Anlaşma Sağlanamadı Tutanağı" yükleyiniz.');
                }

                // 2. Doğru Belge Kontrolü
                $hasFile = $case->files()->where('doc_type', 'anlasma_saglanamadi_tutanagi')->exists();
                if (!$hasFile) {
                    return back()->with('error', 'İşleme devam etmek için lütfen "Dosyalar" sekmesinden "Anlaşma Sağlanamadı Tutanağı" yükleyiniz.');
                }

                // Başarılı
                $case->update([
                    'mutabakat' => 'anlasilmadi',
                    'status' => 'anlasma_saglanamadi'
                ]);
                $this->logAction($case, 'MUTABAKAT', "Anlaşma sağlanamadı olarak işaretlendi.");
            }
        }
        
        
        return back()->with('success', 'Durum başarıyla güncellendi.');
    }

    /**
     * İşlem Geri Alma (Revert)
     * Hem Personel (Taslağa döner) hem Hukuk (Yönetimden geri çeker) kullanabilir.
     */
    public function revertStatus($id)
    {
        $case = ArabuluculukCase::findOrFail($id);
        $user = Auth::user();

        // 1. DURUM: PERSONEL GERİ ALIYOR (Hukuk henüz bakmadıysa)
        // Sizin mevcut kodunuzdaki "anlasma_saglanamadi" kontrolünü ve log mesajını aynen koruduk.
        if (in_array($case->status, ['hukuk_incelemesinde', 'anlasma_saglanamadi']) && $case->created_by == $user->id) {
            
            $case->update([
                'status' => 'taslak',
                'mutabakat' => 'beklemede'
            ]);
            
            // Sizin istediğiniz orijinal log mesajı:
            $this->logAction($case, 'GERİ ALMA', "Personel onaya gönderimi iptal etti (Geri Aldı).");
            
            return back()->with('success', 'İşlem geri alındı. Dosya tekrar taslak modunda.');
        }

        // 2. DURUM: HUKUK GERİ ALIYOR (Yönetim Onayındaysa -> Hukuka geri çek)
        // Bu kısım yeni eklendi.
        if ($case->status == 'yonetim_onayinda' && ($user->can('arabuluculuk.approve_legal') || $user->hasRole('Superadmin'))) {
            
            $case->update(['status' => 'hukuk_incelemesinde']);
            
            $this->logAction($case, 'GERİ ALMA', "Hukuk birimi, yönetim onayındaki dosyayı geri çekti.");
            
            return back()->with('success', 'Dosya yönetimden geri çekildi, tekrar hukuk incelemesinde.');
        }

        // 3. DURUM: HUKUK GERİ ALIYOR (Arabulucuda/Son Aşamadaysa -> Hukuka geri çek)
        // Bu kısım da yeni eklendi.
        if ($case->status == 'arabulucuda' && ($user->can('arabuluculuk.approve_legal') || $user->hasRole('Superadmin'))) {
             
             $case->update(['status' => 'hukuk_incelemesinde']);
             
             $this->logAction($case, 'GERİ ALMA', "Hukuk birimi onayladığı dosyayı geri çekti.");
             
             return back()->with('success', 'Dosya onayı geri alındı, tekrar hukuk incelemesinde.');
        }

        // Hiçbir şarta uymazsa hata döndür
        return back()->with('error', 'Bu aşamada veya bu yetkiyle geri alma işlemi yapılamaz.');
    }

    public function assignMediator(Request $request, $id)
    {
        $case = ArabuluculukCase::findOrFail($id);
        $case->update(['arabulucu_id' => $request->arabulucu_id]);
        $this->logAction($case, 'ATAMA', 'Arabulucu atandı.');
        return back()->with('success', 'Arabulucu atandı.');
    }

    public function uploadFile(Request $request, $id)
    {
        $case = ArabuluculukCase::findOrFail($id);

        // === 1. GÜVENLİK: KAPALI DOSYA KONTROLÜ (YENİ) ===
        if ($case->status == 'kapatildi') {
            return back()->with('error', 'Bu dosya kapatılmıştır. Artık yeni belge yüklenemez.');
        }
        // ================================================
        
        $request->validate(['files.*' => 'required|file|max:20480', 'doc_type' => 'required']);

        if($request->hasFile('files')) {
            foreach($request->file('files') as $file) {
                DB::transaction(function () use ($case, $file, $request) {
                    
                    // --- SİZİN GELİŞMİŞ DOSYA İSİMLENDİRME MANTIĞINIZ (KORUNDU) ---
                    $datePrefix = now()->format('Ymd');
                    $caseNo = $case->dosya_no ?? 'Case'.$case->id;
                    $cleanName = \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $newFileName = "{$datePrefix}_{$caseNo}_{$cleanName}.".$file->getClientOriginalExtension();
                    
                    // --- KİLİTLEME MANTIĞINIZ (KORUNDU) ---
                    $isLocked = in_array($request->doc_type, ['imzali_belge', 'islak_imza_teslim', 'anlasma_saglanamadi_tutanagi']);

                    $path = $file->storeAs('arabuluculuk_files/' . $case->id, $newFileName, 'public');

                    // --- SİZİN GELİŞMİŞ VERİTABANI KAYDINIZ (KORUNDU) ---
                    $case->files()->create([
                        'doc_type' => $request->doc_type,
                        'dosya_yolu' => $path,
                        'orijinal_adi' => $newFileName, // Yeni isim kaydediliyor
                        'mime_tipi' => $file->getClientMimeType(),
                        'version_no' => 1,
                        'is_active' => true,
                        'locked' => $isLocked,
                        'uploaded_by' => Auth::id()
                    ]);

                    // --- DİKKAT: AŞAĞIDAKİ OTOMATİK DURUM GÜNCELLEMESİNİ SİLİYORUZ ---
                    // Sebebi: Artık durumu sadece "Karar Ver" butonları yönetecek.
                    // Dosya yükleyince durum değişirse süreç bozulur demiştik.
                    /*
                    if (in_array($request->doc_type, ['imzali_belge', 'islak_imza_teslim'])) {
                        if ($case->mutabakat == 'anlasildi') $case->update(['status' => 'odeme_bekliyor']);
                        elseif ($case->mutabakat == 'anlasilmadi') $case->update(['status' => 'kapatildi']);
                    }
                    */
                    // -----------------------------------------------------------------
                });
            }
            $this->logAction($case, 'DOSYA', count($request->file('files')) . " adet dosya yüklendi.");
        }
        
        return redirect()->route('admin.arabuluculuk.show', $case->id . '#files')
                         ->with('success', 'Dosya başarıyla yüklendi.');
    }

    public function deleteFile($fileId) // Tek parametre yeterli
    {
        // 1. Dosya Kaydını Bul
        $file = \App\Models\ArabuluculukFile::findOrFail($fileId);
        $case = $file->case; // İlişkili dosyayı bul

        // 2. Yetki Kontrolü (Sadece oluşturan veya Admin, ve sadece Taslak iken)
        $user = Auth::user();
        if (!$user->hasRole('Superadmin')) {
            // Başkası silemez
            if ($case->created_by != $user->id) { abort(403); }
            // Taslak değilse silemez
            $isIstisna = ($case->status == 'arabulucuda' && $file->doc_type == 'arabuluculuk_son_tutanak');

            if ($case->status != 'taslak' && !$isIstisna) { 
                abort(403, 'Dosya onaya sunulduğu için silinemez.'); 
            }
        }

        // 3. Fiziksel Dosyayı Sil (DÜZELTİLDİ: file_path -> dosya_yolu)
        if ($file->dosya_yolu && \Storage::disk('public')->exists($file->dosya_yolu)) {
            \Storage::disk('public')->delete($file->dosya_yolu);
        }

        // 4. Veritabanından Sil
        $file->delete();

        // Log
        $this->logAction($case, 'DOSYA SİLME', "{$file->original_name} dosyası silindi.");

        return redirect()->route('admin.arabuluculuk.show', $case->id . '#files')
            ->with('success', 'Dosya başarıyla silindi.');
    }

    public function addComment(Request $request, $id)
    {
        $case = ArabuluculukCase::findOrFail($id);
        $request->validate(['yorum' => 'required|string']);
        
        $case->kurulDegerlendirmesi()->create([
            'user_id' => Auth::id(),
            'yorum' => $request->yorum,
            'karar' => $request->karar
        ]);
        
        $this->logAction($case, 'DEĞERLENDİRME', 'Yorum eklendi.');
        return back()->with('success', 'Yorum kaydedildi.');
    }

    public function savePayment(Request $request, $id)
    {
        $case = \App\Models\ArabuluculukCase::findOrFail($id);

        // --- 1. IBAN TEMİZLİĞİ (SENİN KODUN AYNEN KALDI) ---
        if ($request->has('iban')) {
            $cleanIban = str_replace(' ', '', $request->iban); // Boşlukları sil
            $cleanIban = strtoupper($cleanIban); // Büyük harf yap
            
            // Temizlenmiş veriyi request'e geri koyuyoruz ki validasyon hatasız geçsin
            $request->merge(['iban' => $cleanIban]);
        }
        // --------------------------------

        // --- 2. VALİDASYON (SENİN KODUN AYNEN KALDI) ---
        $request->validate([
            'odenecek_kisi' => 'required|string|max:255',
            'banka_adi'     => 'required|string',
            'iban'          => ['required', 'string', 'size:26', 'starts_with:TR'],
            'son_odeme_tarihi' => 'nullable|date|after_or_equal:today',
        ], [
            'iban.size' => 'IBAN numarası (boşluklar hariç) TR dahil tam 26 haneli olmalıdır.',
            'iban.starts_with' => 'IBAN numarası "TR" ile başlamalıdır.',
        ]);

        // Banka "Diğer" seçildiyse manuel girilen değeri al (SENİN KODUN)
        $banka = $request->banka_adi == 'Diğer' ? $request->banka_adi_manuel : $request->banka_adi;

        // --- 3. KAYIT İŞLEMİ (BURASI GÜNCELLENDİ) ---
        // create() yerine updateOrCreate() kullanıyoruz.
        // Neden? Eğer daha önce reddedilmiş bir kayıt varsa üstüne yazsın, yoksa yeni açsın.
        
        $case->payments()->updateOrCreate(
            ['case_id' => $case->id], // ARAMA KRİTERİ: Bu dosyaya ait bir ödeme var mı?
            [
                // GÜNCELLENECEK / EKLENECEK VERİLER
                'odenecek_kisi'    => $request->odenecek_kisi,
                'banka_adi'        => $banka,
                'iban'             => $request->iban, // Temizlenmiş hali
                'tutar'            => $case->anlasilan_tutar,
                'odeme_durumu'     => 'bekliyor', // Durumu tekrar bekliyora çekiyoruz
                'son_odeme_tarihi' => $request->son_odeme_tarihi,
                'created_by'       => auth()->id(),
                'red_gerekcesi'    => null // KRİTİK NOKTA: Hata düzeltildiği için red notunu siliyoruz!
            ]
        );

        $this->logAction($case, 'ÖDEME PLANI', "Ödeme planı oluşturuldu/güncellendi. Ödenecek: {$request->odenecek_kisi}");

        return back()->with('success', 'Ödeme planı ve IBAN bilgileri başarıyla kaydedildi.');
    }

    public function approvePayment(Request $request, $id)
    {
        $case = ArabuluculukCase::findOrFail($id);

        // === GÜVENLİK KONTROLÜ: DEKONT VAR MI? ===
        // Dosyalarda türü 'dekont' olan bir kayıt arıyoruz.
        $dekontVarMi = $case->files()->where('doc_type', 'dekont')->exists();

        if (!$dekontVarMi) {
            // Yoksa KAPI DUVAR! İşlemi durdur, hata mesajı ver.
            return back()->with('error', 'DİKKAT: İşlemi tamamlayıp dosyayı kapatabilmek için önce "Dosyalar" sekmesinden ÖDEME DEKONTUNU yüklemeniz gerekmektedir.');
        }
        // ==========================================

        // Burası sizin mevcut kodunuz (Aynen çalışmaya devam eder)
        $payment = $case->payments()->latest()->first();
        if($payment) {
            $payment->update([
                'odeme_durumu' => 'odendi', 
                'finance_onay_by' => Auth::id(), // Auth facade'ini import ettiğinizden emin olun
                'finance_onay_at' => now()
            ]);
            
            $case->update(['status' => 'son_onay_bekliyor']);
            
            $this->logAction($case, 'FİNANS ONAYI', 'Ödeme yapıldı, dosya SON ONAY aşamasına iletildi.');
        }
        
        return back()->with('success', 'Ödeme onaylandı. Dosya son kontrol için yetkiliye gönderildi.');
    }

    // 1. PERSONELİN İŞLEMİ GERİ ALMASI
    public function revertToMediation($id) {
        $case = ArabuluculukCase::findOrFail($id);
        // Sadece ödeme bekliyorsa ve henüz plan yoksa
        if($case->status == 'odeme_bekliyor' && $case->payments->count() == 0) {
            $case->update(['status' => 'arabulucuda']);
            $this->logAction($case, 'GERİ ALMA', 'Personel süreci arabuluculuk aşamasına geri çekti.');
            return back()->with('success', 'Dosya düzenleme için geri çekildi.');
        }
        return back()->with('error', 'Bu aşamada geri alma yapılamaz.');
    }

    // 2. FİNANSIN REDDETMESİ
    public function rejectPayment(Request $request, $id) {
        $case = ArabuluculukCase::findOrFail($id);
        $request->validate(['reason' => 'required|string']);

        // Ödeme kaydını SİLMİYORUZ, bulup güncelliyoruz.
        // Böylece hukuk tarafı hatayı görüp düzeltebilir.
        $payment = $case->payments()->latest()->first();

        if ($payment) {
            $payment->update([
                'red_gerekcesi' => $request->reason,
                // Eğer enum yapın destekliyorsa durumu 'reddedildi' yapabilirsin.
                // Desteklemiyorsa 'bekliyor' kalabilir, hukuk ekranında zaten uyarı çıkacak.
                // 'odeme_durumu' => 'reddedildi' 
            ]);
        }

        // İsterseniz dosyanın genel durumunu da güncelleyebilirsiniz
        // $case->update(['status' => 'odeme_reddedildi']);
        
        $this->logAction($case, 'FİNANS RED', 'Ödeme reddedildi. Gerekçe: ' . $request->reason);
        
        return back()->with('success', 'Ödeme planı reddedildi ve Hukuk birimine geri gönderildi.');
    }

    // 3. SON ONAY VE KAPANIŞ
    public function finalClose($id) {
        // Yetki Kontrolü
        if (!auth()->user()->can('arabuluculuk.final_check') && !auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }
        
        $case = ArabuluculukCase::findOrFail($id);
        $case->update(['status' => 'kapatildi']);
        
        $this->logAction($case, 'DOSYA KAPATILDI', 'Son kontroller yapıldı ve dosya başarıyla kapatıldı.');
        return back()->with('success', 'Dosya başarıyla kapatıldı.');
    }

    private function logAction($case, $islem, $detay) {
        ArabuluculukLog::create([
            'case_id' => $case->id,
            'user_id' => Auth::id(),
            'islem' => $islem,
            'detay' => $detay
        ]);
    }
}