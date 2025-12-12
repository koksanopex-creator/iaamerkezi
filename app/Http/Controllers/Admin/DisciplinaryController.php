<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DisciplinaryCategory;
use App\Models\DisciplinaryImpact;
use App\Models\DisciplinaryScope;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryMultiplier;
use App\Models\DisciplinaryPenaltyScale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\DisiplinTutanagiOlusturuldu;
use App\Notifications\PersonelSavunmaVerdi;
use App\Models\DisciplinaryVote; 

class DisciplinaryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. ERİŞİM KONTROLÜ
        $hasAccess = $user->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Bölüm Lideri']) 
                     || $user->can_issue_disciplinary;

        if (!$hasAccess) {
            abort(403, 'Yetkiniz yok.');
        }

        // 2. TEMEL SORGU
        $query = DisciplinaryCase::with(['user.bolum', 'behavior.category', 'reporter', 'impact', 'scope'])
            ->latest('created_at');

        $filterMessage = '';
        $filterType = 'info';

        // --- YETKİ FİLTRELEME MANTIĞI (GÜNCELLENDİ) ---

        // A. SÜPER YETKİLİLER (Admin, Hukuk, Kurul) -> HER ŞEYİ GÖRÜR
        if ($user->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])) {
            $filterMessage = 'Tam yetkili görünümü: Tüm dosyalar listeleniyor.';
            $filterType = 'success';
        } 
        
        // B. BÖLÜM YETKİLİLERİ (İSG, Güvenlik, Kapak Lideri vb.)
        else {
            // KURAL: Sadece "Raporlayanı (Tutanak Tutanı)" benim bölümümden olan dosyaları getir.
            // Yani: Barış Yalçın (İSG) veya Serkan (İSG) tutanak tuttuysa, İSG ekibi bunu görebilir.
            // Ama İK (İnsan Kaynakları) birine tutanak tuttuysa, İSG bunu GÖREMEZ (İlgili değilse).
            
            $query->whereHas('reporter', function($q) use ($user) {
                $q->where('bolum_id', $user->bolum_id);
            });

            // EKSTRA: Dosya sahibi (suçlanan) benim bölümümden ise onu da göreyim (Lidersem)
            if ($user->hasRole('Bölüm Lideri')) {
                $query->orWhereHas('user', function($q) use ($user) {
                    $q->where('bolum_id', $user->bolum_id);
                });
            }

            // Global Yetkili ise mesajı farklı verelim ama filtre aynı kalsın (Güvenlik için)
            if ($user->bolum && $user->bolum->is_disciplinary_global) {
                $filterMessage = 'Bölümünüz ('. $user->bolum->ad .') genel tutanak yetkisine sahiptir. Sadece bölümünüz tarafından oluşturulan tutanakları görüyorsunuz.';
            } else {
                $filterMessage = 'Sadece kendi bölümünüzle ilgili kayıtları görüyorsunuz.';
                $filterType = 'warning';
            }
        }

        // 4. MEVCUT FİLTRELERİ UYGULA (Search ve Durum - KORUNDU)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }

        // 5. SAYFALAMA (KORUNDU)
        $cases = $query->paginate(20)->withQueryString();

        return view('admin.disiplin.index', compact('cases', 'filterMessage', 'filterType'));
    }

    public function show($id)
    {
        // 1. VERİ YÜKLEME (Senin mevcut kodun: Yorum mantığı ve ilişkiler korunuyor)
        $case = DisciplinaryCase::with([
            'user.bolum', 
            'behavior.category', 
            'reporter', 
            'impact', 
            'scope',
            // Yorumları özel bir koşulla çekiyoruz:
            'comments' => function($q) {
                // Eğer giren kişi Superadmin veya Hukuk Admini ise SİLİNENLERİ DE GETİR
                if (Auth::user()->hasRole(['Superadmin', 'Hukuk Admini'])) {
                    $q->withTrashed(); 
                }
                $q->orderBy('created_at', 'desc'); // En yeni en üstte
            },
            'comments.user',      // Yorum sahibi bilgisi
            'comments.histories'  // Düzenleme geçmişi
        ])->findOrFail($id);

        $user = Auth::user();

        // 2. GÜVENLİK DUVARI (Hibrit Yapı)

        // A. Süper Yöneticiler ve Kurul (Her şeyi görür)
        if ($user->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])) {
            return view('admin.disiplin.show', compact('case'));
        }

        // B. Dosya Sahibi (Kendisi)
        if ($case->user_id == $user->id) {
            return view('admin.disiplin.show', compact('case'));
        }

        // C. Raporlayan (Tutanak Tutan)
        // (Serkan tutanağı tuttuysa, yetkisi alınsa bile kendi tuttuğu tutanağı görmeye devam etmeli)
        if ($case->reporter_id == $user->id) {
            return view('admin.disiplin.show', compact('case'));
        }

        // D. Bölüm Liderleri ve Yetkili Sorumlular (Yeni Eklenen Kısım)
        // Hem Liderleri hem de Delegasyon yetkisi olanları kapsar.
        $hasAuthority = $user->hasRole('Bölüm Lideri') || $user->can_issue_disciplinary;

        if ($hasAuthority) {
            // D1. Global Yetkili Bölüm mü? (İSG/Güvenlik -> Herkesi Görür)
            if ($user->bolum && $user->bolum->is_disciplinary_global) {
                return view('admin.disiplin.show', compact('case'));
            }

            // D2. Yerel Yetkili (Kapak Bölümü -> Sadece Kendi Bölümünü Görür)
            // Dosyadaki suçlu kişi, benim bölümümden mi?
            if ($case->user->bolum_id == $user->bolum_id) {
                return view('admin.disiplin.show', compact('case'));
            }
        }

        // Hiçbir kurala uymuyorsa yasakla
        abort(403, 'Bu dosyayı görüntüleme yetkiniz yok.');
    }

    /**
     * DÜZENLEME SAYFASI
     */
    public function edit($id)
    {
        $case = DisciplinaryCase::findOrFail($id);
        
        // Sadece belirli durumlarda düzenlemeye izin verelim (Örn: Karar verilmişse düzenlenemesin)
        if ($case->durum == 'Karar Verildi' || $case->durum == 'İptal Edildi') {
            return back()->with('error', 'Bu dosya kapatıldığı için düzenlenemez.');
        }

        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();
        $categories = DisciplinaryCategory::with('behaviors')->orderBy('ad')->get();
        $impacts = DisciplinaryImpact::orderBy('puan')->get();
        $scopes = DisciplinaryScope::orderBy('puan')->get();

        return view('admin.disiplin.edit', compact('case', 'users', 'categories', 'impacts', 'scopes'));
    }

    /**
     * GÜNCELLEME İŞLEMİ
     */
    public function update(Request $request, $id)
    {
        $case = DisciplinaryCase::findOrFail($id);
        
        // Validasyon (Dosya zorunlu değil bu sefer)
        $request->validate([
            'behavior_id' => 'required',
            'impact_id' => 'required',
            'scope_id' => 'required',
            'olay_aciklamasi' => 'required|min:10',
        ]);

        // 1. ESKİ DOSYALARI SİLME İŞLEMİ
        $mevcutDosyalar = $case->kanit_dosyalari ?? [];
        
        if ($request->has('silinecek_dosyalar')) {
            foreach ($request->silinecek_dosyalar as $silinecek) {
                // Array'den çıkar
                if (($key = array_search($silinecek, $mevcutDosyalar)) !== false) {
                    unset($mevcutDosyalar[$key]);
                    // Fiziksel dosyayı da silebiliriz (Opsiyonel)
                    // Storage::disk('public')->delete($silinecek); 
                }
            }
            // Indexleri düzelt (0,1,2 diye gitsin)
            $mevcutDosyalar = array_values($mevcutDosyalar);
        }

        // 2. YENİ DOSYALARI EKLEME
        if ($request->hasFile('kanit_dosyalari')) {
            foreach ($request->file('kanit_dosyalari') as $file) {
                $path = $file->store('disiplin_kanitlar', 'public');
                $mevcutDosyalar[] = $path; // Listeye ekle
            }
        }

        // Matris Yeniden Hesaplanmalı
        $calc = $this->calculateMatrixScore($case->user_id, $request->behavior_id, $request->impact_id, $request->scope_id);

        $case->update([
            'behavior_id' => $request->behavior_id,
            'impact_id' => $request->impact_id,
            'scope_id' => $request->scope_id,
            'olay_aciklamasi' => $request->olay_aciklamasi,
            'hesaplanan_puan' => $calc['toplam_puan'],
            'sistem_oneri_ceza' => $calc['oneri_ceza'],
            'kanit_dosyalari' => $mevcutDosyalar, // Güncellenmiş dosya listesi
        ]);

        return redirect()->route('admin.disiplin.show', $case->id)->with('success', 'Tutanak güncellendi.');
    }

    /**
     * SİLME YETKİSİ (Hiyerarşik Matris)
     */
    public function destroy($id)
    {
        $case = DisciplinaryCase::findOrFail($id);
        $user = Auth::user();

        // 1. SUPERADMIN: Her an, her şeyi silebilir.
        if ($user->hasRole('Superadmin')) {
            $this->performDelete($case);
            return redirect()->route('admin.disiplin.index')->with('success', 'Kayıt (Superadmin yetkisiyle) silindi.');
        }

        // 2. HUKUK ADMİNİ: Savunma girilmiş olsa bile silebilir.
        if ($user->hasRole('Hukuk Admini')) {
            $this->performDelete($case);
            return redirect()->route('admin.disiplin.index')->with('success', 'Kayıt silindi.');
        }

        // 3. HUKUK YÖNETİCİSİ: Savunma GİRİLMEDİYSE silebilir.
        if ($user->hasRole('Hukuk Yöneticisi')) {
            if ($case->durum == 'Savunma Bekleniyor' || $case->durum == 'Taslak') {
                $this->performDelete($case);
                return redirect()->route('admin.disiplin.index')->with('success', 'Kayıt silindi.');
            }
            return back()->with('error', 'Personel savunma verdiği için bu dosyayı silemezsiniz.');
        }

        // 4. BÖLÜM LİDERİ: Sadece kendi oluşturduğu ve Savunma girilmemişse silebilir.
        if ($user->hasRole('Bölüm Lideri') && $case->reporter_id == $user->id) {
            if ($case->durum == 'Savunma Bekleniyor' || $case->durum == 'Taslak') {
                $this->performDelete($case);
                return redirect()->route('admin.disiplin.index')->with('success', 'Tutanak silindi.');
            }
            return back()->with('error', 'Personel savunma verdiği için silemezsiniz.');
        }

        // 5. TUTANAK SAHİBİ (PERSONEL): Asla silemez (Savunmasını silebilir ama tutanağı değil).
        
        // 6. DİSİPLİN KURULU (BAŞKAN/ÜYE): Asla silemez.

        abort(403, 'Bu kaydı silme yetkiniz yok.');
    }

    // Ortak Silme ve Puan İade Fonksiyonu
    private function performDelete($case)
    {
        // Eğer ceza verildiyse ve puan düşüldüyse, puanı iade et.
        if ($case->durum == 'Karar Verildi' && $case->final_karar != 'Savunma Kabul Edildi (Ceza Yok)') {
            $personel = User::find($case->user_id);
            if ($personel) {
                $personel->increment('toplam_puan', $case->hesaplanan_puan);
            }
        }
        $case->delete();
    }

   
    /**
     * YENİ: Tutanak Oluşturma Formu (Akıllı Filtreleme ile)
     */
    public function create()
    {
        $currentUser = Auth::user();

        // 1. SAYFAYA ERİŞİM YETKİSİ KONTROLÜ
        // Kimler girebilir?
        // a) Superadmin, Hukuk Yöneticileri
        // b) Bölüm Liderleri
        // c) Lider tarafından yetki verilmiş "Disiplin Sorumlusu" personeller (can_issue_disciplinary = 1)
        
        $hasAccess = $currentUser->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Bölüm Lideri']) 
                     || $currentUser->can_issue_disciplinary;

        if (!$hasAccess) {
            abort(403, 'Tutanak oluşturma yetkiniz bulunmamaktadır.');
        }

        // 2. KULLANICI LİSTESİ SORGUSU (Filtreleme Başlıyor)
        $usersQuery = User::where('id', '!=', $currentUser->id) // Kendini seçemesin
            ->orderBy('name');

        // A. DOKUNULMAZLAR (Hiyerarşik Koruma)
        // Bu rollere sahip kişiler ASLA listede çıkmaz (Tutanak tutulamaz).
        $protectedRoles = ['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'];
        
        $usersQuery->whereDoesntHave('roles', function ($q) use ($protectedRoles) {
            $q->whereIn('name', $protectedRoles);
        });

        // B. YETKİ KAPSAMI (GLOBAL Mİ, YEREL Mİ?)
        
        // --- DURUM 1: SÜPER YETKİLİLER ---
        // Superadmin ve Hukukçular fabrikadaki HERKESİ görür.
        if ($currentUser->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini'])) {
            // Ekstra bir filtre uygulanmaz, herkes gelir.
        } 
        
        // --- DURUM 2: BÖLÜM LİDERLERİ VE SORUMLULAR ---
        else {
            // Kullanıcının bölümü "Global Disiplin Yetkisine" sahip mi? (Örn: İSG, Güvenlik)
            // Bu ayarı 'bolumler' tablosundaki 'is_disciplinary_global' sütunundan alıyoruz.
            $isGlobalDept = $currentUser->bolum && $currentUser->bolum->is_disciplinary_global;

            if ($isGlobalDept) {
                // >> GLOBAL YETKİ <<
                // Tüm fabrikayı görebilirler.
                // İsteğe Bağlı Kural: Kendi bölümlerindeki "Lideri" raporlayamasınlar.
                if (!$currentUser->hasRole('Bölüm Lideri')) {
                     $usersQuery->whereDoesntHave('roles', function($q){ 
                        $q->where('name', 'Bölüm Lideri'); 
                    });
                }
            } 
            else {
                // >> YEREL YETKİ (STANDART) <<
                // Sadece kendi bölümündeki personeli görebilirler.
                if ($currentUser->bolum_id) {
                    $usersQuery->where('bolum_id', $currentUser->bolum_id);
                    
                    // Hiyerarşi Kuralı: Eğer kullanıcı "Lider" değilse (yani yetkili personelse),
                    // kendi bölüm liderini şikayet edemez.
                    if (!$currentUser->hasRole('Bölüm Lideri')) {
                        $usersQuery->whereDoesntHave('roles', function($q){ 
                            $q->where('name', 'Bölüm Lideri'); 
                        });
                    }
                }
            }
        }

        // Sonuçları Getir
        $users = $usersQuery->get();
        
        // Diğer verileri çek
        $categories = DisciplinaryCategory::with('behaviors')->orderBy('ad')->get();
        $impacts = DisciplinaryImpact::orderBy('puan')->get();
        $scopes = DisciplinaryScope::orderBy('puan')->get();

        return view('admin.disiplin.create', compact('users', 'categories', 'impacts', 'scopes'));
    }

    /**
     * YENİ: Kaydetme ve Matris Hesabı
     */
    public function store(Request $request)
    {
        // dd() satırını sildik, artık işlem devam edebilir.
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'behavior_id' => 'required|exists:disciplinary_behaviors,id',
            'impact_id' => 'required|exists:disciplinary_impacts,id',
            'scope_id' => 'required|exists:disciplinary_scopes,id',
            'olay_tarihi' => 'required|date',
            'olay_aciklamasi' => 'required|string|min:10',
            'kanit_dosyalari.*' => 'nullable|file|mimes:jpg,png,pdf,mp4,mov|max:20480'
        ]);

        DB::beginTransaction();
        try {
            // 1. MATRİS HESAPLAMA (GÜNCELLENDİ)
            $calc = $this->calculateMatrixScore(
                $request->user_id, 
                $request->behavior_id, 
                $request->impact_id, 
                $request->scope_id
            );

            // 2. Dosya Yükleme
            $kanitYollari = [];
            if ($request->hasFile('kanit_dosyalari')) {
                foreach ($request->file('kanit_dosyalari') as $file) {
                    $path = $file->store('disiplin_kanitlar', 'public');
                    $kanitYollari[] = $path;
                }
            }

            // 3. Kayıt (Değişkene atayarak yapıyoruz)
            $case = DisciplinaryCase::create([ // Başına $case = ekledik
                'user_id' => $request->user_id,
                'reporter_id' => Auth::id(),
                'behavior_id' => $request->behavior_id,
                'impact_id' => $request->impact_id,
                'scope_id' => $request->scope_id,
                'olay_tarihi' => $request->olay_tarihi,
                'olay_aciklamasi' => $request->olay_aciklamasi,
                'kanit_dosyalari' => $kanitYollari, 
                
                'tekrar_sayisi' => $calc['tekrar'],
                'hesaplanan_puan' => $calc['toplam_puan'],
                'sistem_oneri_ceza' => $calc['oneri_ceza'],
                'final_karar' => $calc['oneri_ceza'],
                
                'durum' => 'Savunma Bekleniyor'
            ]);

            // --- BİLDİRİM GÖNDERİMİ ---
            
            // A. Tutanak Yiyen Personele Gönder
            try {
                $case->user->notify(new DisiplinTutanagiOlusturuldu($case));
            } catch (\Exception $e) {
                \Log::error('Personel bildirimi hatası: ' . $e->getMessage());
            }

            // B. Yöneticilere Gönder
            // Rolleri tek tek array içinde belirttik.
            try {
                $yoneticiRolleri = ['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı'];
                $yoneticiler = User::role($yoneticiRolleri)->get();
                
                // Eğer yönetici bulunamadıysa loga yazalım ki anlayalım
                if ($yoneticiler->isEmpty()) {
                    \Log::warning('Bildirim gönderilecek Hukuk/Admin yetkili bulunamadı!');
                }

                foreach ($yoneticiler as $yonetici) {
                    // 1. Kendine bildirim atmasın (Dosyayı oluşturan kişi)
                    // 2. Tutanak yiyen kişi yöneticiyse ona zaten yukarıda (A şıkkında) attık, tekrar atma.
                    if ($yonetici->id != Auth::id() && $yonetici->id != $case->user_id) {
                        $yonetici->notify(new DisiplinTutanagiOlusturuldu($case));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Yönetici bildirimi hatası: ' . $e->getMessage());
            }
            // --------------------------

            // 3. [GÜNCELLENDİ] EĞER TUTANAĞI TUTAN KİŞİ (SERKAN), BÖLÜM LİDERİ DEĞİLSE
            // O BÖLÜMÜN LİDERİNE (BARIŞ) ÖZEL BİLDİRİM GİTSİN
            if (!Auth::user()->hasRole('Bölüm Lideri')) {
                // Tutanak tutan kişinin (Serkan'ın) bölüm liderini bul
                $reporterLeader = User::where('bolum_id', Auth::user()->bolum_id)
                    ->role('Bölüm Lideri')
                    ->first();

                if ($reporterLeader) {
                    // DEĞİŞİKLİK BURADA: Standart bildirim yerine ÖZEL bildirim gönderiyoruz.
                    $reporterLeader->notify(new \App\Notifications\PersonelTutanakOlusturduBildirimi($case));
                }
            }

            // TODO: Kişiye savunma talebi maili atılacak...

            DB::commit();
            return redirect()->route('admin.disiplin.index')
                ->with('success', 'Tutanak oluşturuldu. Hesaplanan Puan: ' . $calc['toplam_puan'] . ' - Öneri: ' . $calc['oneri_ceza']);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * === YENİ MATRİS HESAPLAMA MOTORU ===
     * Formül: (Etki Puanı x Kapsam Puanı) x Tekrar Katsayısı
     */
    private function calculateMatrixScore($userId, $behaviorId, $impactId, $scopeId)
    {
        // A. Puanları Çek
        $impact = DisciplinaryImpact::find($impactId)->puan; // Örn: 5
        $scope = DisciplinaryScope::find($scopeId)->puan;   // Örn: 3

        // B. Tekrar Sayısını Bul (Aynı suçtan kaç tane kesinleşmiş ceza var?)
        $gecmisSayisi = DisciplinaryCase::where('user_id', $userId)
            ->where('behavior_id', $behaviorId)
            ->where('durum', 'Karar Verildi')
            ->count();
        
        $tekrar = $gecmisSayisi + 1; // Mevcut olay dahil

        // C. Katsayıyı Bul
        $katsayiKaydi = DisciplinaryMultiplier::where('tekrar_sayisi', $tekrar)->first();
        if (!$katsayiKaydi) {
            // Tabloda yoksa (örn: 5. tekrar) en sonuncuyu al
            $katsayiKaydi = DisciplinaryMultiplier::orderBy('tekrar_sayisi', 'desc')->first();
        }
        $katsayi = $katsayiKaydi ? $katsayiKaydi->katsayi : 1.0;

        // D. MATRİS FORMÜLÜ
        // (Etki x Kapsam) * Katsayı
        $baseScore = $impact * $scope; 
        $totalScore = $baseScore * $katsayi;

        // E. Ceza Skalasından Öneriyi Bul
        $skala = DisciplinaryPenaltyScale::where('min_puan', '<=', $totalScore)
            ->where('max_puan', '>=', $totalScore)
            ->first();
        
        $oneri = $skala ? $skala->ceza_adi : 'Kurul Değerlendirmesi';

        return [
            'tekrar' => $tekrar,
            'katsayi' => $katsayi,
            'toplam_puan' => $totalScore,
            'oneri_ceza' => $oneri
        ];
    }

    /**
     * SAVUNMA KAYDETME İŞLEMİ
     */
    public function saveDefense(Request $request, $id)
    {
        $case = DisciplinaryCase::findOrFail($id);
        $user = Auth::user();

        // 1. YETKİ KONTROLÜ (Genişletildi)
        // Kimler savunma girebilir?
        // a) Dosya sahibi (Personel)
        // b) Dosyayı Raporlayan (Bölüm Lideri) -> Personel adına
        // c) Hukuk Admini / Superadmin (Acil durumlar için)
        
        $isOwner = $case->user_id == $user->id;
        $isReporter = $case->reporter_id == $user->id;
        $isAdmin = $user->hasRole(['Superadmin', 'Hukuk Admini']);

        if (!$isOwner && !$isReporter && !$isAdmin) {
            abort(403, 'Bu dosyaya savunma girme yetkiniz yok.');
        }

        $request->validate([
            'savunma_aciklamasi' => 'required|min:5',
            'savunma_dosyalari.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:20480' 
        ]);

        DB::beginTransaction();
        try {
            $savunmaDosyalari = $case->savunma_dosyalari ?? [];
            
            // Dosya Yükleme (İsimlendirme korundu)
            if ($request->hasFile('savunma_dosyalari')) {
                foreach ($request->file('savunma_dosyalari') as $file) {
                    $userName = \Illuminate\Support\Str::slug($user->name); // İşlemi yapanın adı
                    $dateTime = now()->format('Ymd_Hi');
                    $rawName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $slugName = \Illuminate\Support\Str::slug($rawName);
                    $shortName = \Illuminate\Support\Str::limit($slugName, 25, ''); 
                    if (empty($shortName)) $shortName = 'dosya_' . \Illuminate\Support\Str::random(3);
                    $extension = $file->getClientOriginalExtension();
                    
                    $fileName = "{$userName}_{$dateTime}_{$shortName}.{$extension}";
                    $path = $file->storeAs('disiplin_savunmalar', $fileName, 'public');
                    $savunmaDosyalari[] = $path;
                }
            }

            // SAVUNMA METNİ İŞLEME
            $finalText = $request->savunma_aciklamasi;
            
            // Eğer savunmayı giren kişi dosya sahibi DEĞİLSE, altına not düş.
            if (!$isOwner) {
                $finalText .= "\n\n(Not: Bu savunma " . now()->format('d.m.Y H:i') . " tarihinde Bölüm Yöneticisi " . $user->name . " tarafından personel adına sisteme girilmiştir.)";
            }

            $case->update([
                'savunma_aciklamasi' => $finalText,
                'savunma_dosyalari' => $savunmaDosyalari,
                'savunma_tarihi' => now(),
                'durum' => 'Yönetici Değerlendirmesi' 
            ]);

            // BİLDİRİM: Hukuk ve Yönetime haber ver
            try {
                $yoneticiler = User::role(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini'])->get();
                foreach($yoneticiler as $yonetici) {
                    // İşlemi yapan kişi yöneticiyse kendine bildirim atmasın
                    if($yonetici->id != $user->id) {
                        $yonetici->notify(new \App\Notifications\PersonelSavunmaVerdi($case));
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Bildirim hatası: ' . $e->getMessage());
            }

            DB::commit();
            return back()->with('success', 'Savunma başarıyla kaydedildi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    /**
     * KARAR 1: CEZAYI ONAYLA (Puan Düşer + Not + Dosya)
     */
    public function approvePenalty(Request $request, $id)
    {
        $case = DisciplinaryCase::findOrFail($id);

        // 1. TEK VE NET YETKİ KONTROLÜ
        // Buraya "Disiplin Kurulu Başkanı"nı EKLEDİK. Artık 403 hatası almayacak.
        if (!Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı'])) {
            abort(403, 'Bu işlemi yapmaya yetkiniz yok.');
        }

        $request->validate(['karar_dosyasi' => 'nullable|file|max:10240']); // 10MB limit
       
        // Karar Dosyası Yükleme
        $dosyaYolu = null;
        if ($request->hasFile('karar_dosyasi')) {
            // YENİ DOSYA İSİMLENDİRME FORMATI: YYYYMMDD_RN.ext
            // Örn: 20251204_99.pdf
            $file = $request->file('karar_dosyasi');
            $extension = $file->getClientOriginalExtension();
            $filename = date('Ymd') . '_' . rand(10, 99) . '.' . $extension;
            
            // storeAs kullanarak özel isimle kaydet
            $dosyaYolu = $file->storeAs('disiplin_kararlar', $filename, 'public');
        }

        $imzaliNot = $request->yonetici_notu . ' (İşlemi Yapan: ' . Auth::user()->name . ')';

        $case->update([
            'durum' => 'Karar Verildi',
            'final_karar' => $case->sistem_oneri_ceza, // Örn: "Sözlü Uyarı"
            'yonetici_notu' => $imzaliNot, // Güncellendi
            'karar_dosyasi' => $dosyaYolu,
            'karar_tarihi' => now(),
        ]);

        // Puan Düşme İşlemi
        $user = User::find($case->user_id);
        if ($user) {
            $user->decrement('toplam_puan', $case->hesaplanan_puan);
        }

        return back()->with('success', 'Ceza onaylandı, puan düşüldü ve karar dosyaya işlendi.');
    }

    /**
     * KARAR 2: SAVUNMAYI KABUL ET (Puan Düşmez, Dosya Kapanır)
     */
    public function acceptDefense(Request $request, $id)
    {
        $case = DisciplinaryCase::findOrFail($id);

        // TEK VE NET YETKİ KONTROLÜ
        if (!Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı'])) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        
        // Karar Dosyası Yükleme
        $dosyaYolu = null;
        if ($request->hasFile('karar_dosyasi')) {
            // YENİ DOSYA İSİMLENDİRME FORMATI
            $file = $request->file('karar_dosyasi');
            $extension = $file->getClientOriginalExtension();
            $filename = date('Ymd') . '_' . rand(10, 99) . '.' . $extension;
            
            $dosyaYolu = $file->storeAs('disiplin_kararlar', $filename, 'public');
        }

        $imzaliNot = $request->yonetici_notu . ' (İşlemi Yapan: ' . Auth::user()->name . ')';

        $case->update([
            'durum' => 'Karar Verildi',
            'final_karar' => 'Savunma Kabul Edildi (Ceza Yok)', // Karar metni
            'yonetici_notu' => $imzaliNot, // Güncellendi
            'karar_dosyasi' => $dosyaYolu,
            'karar_tarihi' => now(),
        ]);

        // DİKKAT: Puan düşme işlemi YAPMIYORUZ.
        
        return back()->with('success', 'Savunma haklı bulundu ve kabul edildi. Personelden puan düşülmedi.');
    }

    /**
     * KARAR 3: KURULA SEVK ET
     */
    public function sendToBoard(Request $request, $id)
    {
        if (!Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini'])) abort(403);

        $case = DisciplinaryCase::findOrFail($id);

        // TARİH VE NOT
        $tarih = $request->toplanti_tarihi; // Formdan gelecek
        $not = $request->yonetici_notu;

        // İMZA EKLEME (Kurul sevki için not zorunlu değilse kontrol koyabilirsin)
        $not = $request->yonetici_notu ?? 'Kurula sevk edildi.';
        $imzaliNot = $not . ' (İşlemi Yapan: ' . Auth::user()->name . ')';
        
        // Notu kaydet, durumu güncelle
        $case->update([
            'durum' => 'Kurulda',
            'yonetici_notu' => $imzaliNot, // Güncellendi
            'toplanti_tarihi' => $tarih, // Kaydet
            'karar_tarihi' => now() // İşlem tarihini tutalım
        ]);

        return back()->with('success', 'Dosya, yönetici notuyla birlikte Disiplin Kuruluna sevk edildi.');
    }

    /**
     * KARARI GERİ AL (Revize Edildi)
     */
    public function revokeDecision($id)
    {
        // 1. GENEL YETKİ
        if (!Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı'])) {
            abort(403, 'Yetkiniz yok.');
        }

        $case = DisciplinaryCase::findOrFail($id);

        // İzin verilen durumlar: Karar Verildi VEYA Kurulda
        if (!in_array($case->durum, ['Karar Verildi', 'Kurulda'])) {
            return back()->with('error', 'Bu aşamadaki dosya geri alınamaz.');
        }

        DB::beginTransaction();
        try {
            // Eğer Ceza verildiyse ve Puan düşüldüyse -> Puanı İade Et
            // "Savunma Kabul Edildi" durumunda puan düşmediği için iadeye gerek yok.
            // "Kurulda" durumunda puan düşmediği için iadeye gerek yok.
            if ($case->durum == 'Karar Verildi' && $case->final_karar != 'Savunma Kabul Edildi (Ceza Yok)') {
                $user = User::find($case->user_id);
                if ($user) {
                    $user->increment('toplam_puan', $case->hesaplanan_puan);
                }
            }

            // Dosyayı Eski Haline ("Yönetici Değerlendirmesi") Döndür
            $case->update([
                'durum' => 'Yönetici Değerlendirmesi',
                'final_karar' => null,
                'karar_tarihi' => null,
                // yonetici_notu silinmesin, tarihçe olarak kalsın mı? 
                // Genelde geri alınınca temizlenmesi istenir ki yeni karar verilsin.
                'yonetici_notu' => null, 
                'karar_dosyasi' => null
            ]);

            DB::commit();
            return back()->with('success', 'İşlem geri alındı. Dosya tekrar yönetici ekranına düştü.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

   
    /**
     * KURUL ÜYESİ OY KULLANMA İŞLEMİ
     */
    public function saveVote(Request $request, $id)
    {
        // Sadece yetkili roller oy kullanabilir
        if (!Auth::user()->hasRole(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı', 'Superadmin', 'Hukuk Yöneticisi'])) {
            abort(403, 'Oy kullanma yetkiniz yok.');
        }

        $case = DisciplinaryCase::findOrFail($id);

        if ($case->durum != 'Kurulda') {
            return back()->with('error', 'Sadece kurul aşamasındaki dosyalara oy verilebilir.');
        }

        $request->validate([
            'oy_yonu' => 'required|in:Ceza Verilsin,Ceza Verilmesin,Ek Soruşturma,Çekimser',
            'yorum' => 'nullable|string|max:1000'
        ]);

        // UpdateOrCreate: Eğer daha önce oy kullandıysa günceller, yoksa yeni oluşturur.
        DisciplinaryVote::updateOrCreate(
            [
                'case_id' => $case->id,
                'user_id' => Auth::id()
            ],
            [
                'oy_yonu' => $request->oy_yonu,
                'yorum' => $request->yorum
            ]
        );

        return back()->with('success', 'Oyunuz ve görüşünüz başarıyla kaydedildi.');
    }

    /**
     * KURUL OYUNU SİLME (GERİ ÇEKME)
     */
    public function deleteVote($id)
    {
        $case = DisciplinaryCase::findOrFail($id);
        
        // Sadece kendi oyunu silebilir
        $vote = DisciplinaryVote::where('case_id', $case->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($vote) {
            $vote->delete();
            return back()->with('success', 'Oyunuz geri çekildi.');
        }

        return back()->with('error', 'Henüz oy kullanmamışsınız.');
    }

    /**
     * YORUM EKLEME (Bildirimli + Özel Dosya İsimlendirmeli)
     */
    /**
     * YORUM EKLEME (Orijinal İsim Korumalı)
     */
    public function storeComment(Request $request, $id)
    {
        $case = DisciplinaryCase::findOrFail($id);
        
        $request->validate([
            'yorum' => 'required|string|max:1000',
            'dosyalar.*' => 'nullable|file|max:20480' // .msg ve diğerleri için esnek kontrol
        ]);

        $dosyaYollari = [];
        if ($request->hasFile('dosyalar')) {
            foreach ($request->file('dosyalar') as $file) {
                
                // 1. KULLANICI ADI (Slug: serkan-atak)
                $userName = \Illuminate\Support\Str::slug(Auth::user()->name);
                
                // 2. TARİH SAAT (20251205_1146)
                $dateTime = now()->format('Ymd_Hi');
                
                // 3. ORİJİNAL DOSYA İSMİ (Kısaltılmış ve Güvenli)
                // Uzantısız ismi al
                $rawName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // Türkçe karakterleri temizle ve tirele (sahadan-cekilen-fotograflar)
                $slugName = \Illuminate\Support\Str::slug($rawName);
                // İlk 25 karakteri al, devamını kes (sahadan-cekilen-fotog)
                $shortName = \Illuminate\Support\Str::limit($slugName, 25, ''); 
                
                // Eğer isim tamamen sembolse ve boş kaldıysa rastgele bir şey ata
                if (empty($shortName)) {
                    $shortName = 'dosya_' . \Illuminate\Support\Str::random(3);
                }

                // 4. UZANTI
                $extension = $file->getClientOriginalExtension();

                // 5. BİRLEŞTİR: serkan-atak_20251205_1146_sahadan-cekilen.png
                $fileName = "{$userName}_{$dateTime}_{$shortName}.{$extension}";

                // 6. KAYDET
                $path = $file->storeAs('disiplin_yorumlar', $fileName, 'public');
                
                $dosyaYollari[] = $path;
            }
        }

        // Yorumu Kaydet
        $comment = \App\Models\DisciplinaryComment::create([
            'case_id' => $case->id,
            'user_id' => Auth::id(),
            'yorum' => $request->yorum,
            'dosyalar' => $dosyaYollari
        ]);

        // --- BİLDİRİM GÖNDER ---
        $recipients = collect();
        $recipients->push($case->user); // Cihangir
        $recipients->push($case->reporter); // Serkan
        $managers = User::role(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı'])->get();
        $recipients = $recipients->merge($managers);

        // Kendini çıkar
        $recipients = $recipients->unique('id')->reject(function ($user) {
            return $user->id === Auth::id();
        });

        foreach ($recipients as $recipient) {
            $recipient->notify(new \App\Notifications\YeniDisiplinYorumu($case, Auth::user()->name));
        }
        
        return back()->with('success', 'Yorumunuz eklendi.');
    }

    /**
     * YORUM GÜNCELLEME (Loglu)
     */
    public function updateComment(Request $request, $id)
    {
        $comment = \App\Models\DisciplinaryComment::findOrFail($id);

        // Yetki: Sadece yorum sahibi düzenleyebilir (Superadmin bile olsa başkasının yorumunu değiştirmemeli, silebilir ama değiştirmemeli etik olarak)
        if ($comment->user_id != Auth::id()) {
            abort(403, 'Sadece kendi yorumunuzu düzenleyebilirsiniz.');
        }

        $request->validate(['yorum' => 'required|string|max:1000']);

        // LOGLAMA (Değişiklik varsa)
        if ($comment->yorum !== $request->yorum) {
            \App\Models\DisciplinaryCommentHistory::create([
                'comment_id' => $comment->id,
                'user_id' => Auth::id(),
                'eski_yorum' => $comment->yorum // Eski metni sakla
            ]);

            // Güncelle
            $comment->update(['yorum' => $request->yorum]);
            return back()->with('success', 'Yorum düzenlendi (Eski kayıt loglandı).');
        }

        return back();
    }

    /**
     * YORUM SİLME (Soft Delete - Zaten Logludur)
     */
    public function destroyComment($id)
    {
        $comment = \App\Models\DisciplinaryComment::findOrFail($id);
        $user = Auth::user();

        // 1. Superadmin ve Hukuk Admini silebilir.
        // 2. Yorum sahibi silebilir.
        if ($user->hasRole(['Superadmin', 'Hukuk Admini']) || $comment->user_id == $user->id) {
            $comment->delete(); // Soft Delete olduğu için veritabanından silinmez, deleted_at işlenir.
            return back()->with('success', 'Yorum kaldırıldı.');
        }

        abort(403, 'Yetkiniz yok.');
    }

    
}