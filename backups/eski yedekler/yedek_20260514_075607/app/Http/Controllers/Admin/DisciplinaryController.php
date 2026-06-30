<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DisciplinaryCategory;
use App\Models\DisciplinaryImpact;
use App\Models\DisciplinaryScope;
use App\Models\DisciplinaryLog;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryMultiplier;
use App\Models\DisciplinaryPenaltyScale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\DisiplinTutanagiOlusturuldu;
use App\Notifications\PersonelSavunmaVerdi;
use App\Notifications\DisiplinKararVerildi;
use App\Notifications\DisiplinKurulunaSevkEdildi;
use App\Notifications\DisiplinOylamaBaslatildi;
use App\Notifications\DisiplinToplantiBildirimi;
use App\Notifications\DisiplinPersonelKurulaSevkBildirimi;
use App\Notifications\YeniDisiplinYorumu;
use App\Models\DisciplinaryVote;
use App\Models\DisiplinKuruluToplanti;
use App\Models\DisiplinKuruluToplantiKatilimci;
use App\Models\DisciplinaryComment;
use App\Models\DisciplinaryCommentHistory;
use Illuminate\Support\Facades\Log;

class DisciplinaryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. ERİŞİM KONTROLÜ
        // Değişiklik: Eğer kullanıcının disiplin dosyası varsa da portala erişebilir (Sadece kendininkileri görmek için)
        $hasAccess = $user->hasRole(['Superadmin', 'Yonetim', 'Yönetim', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Bölüm Lideri', 'Direktör'])
            || ($user->hasRole('Bölüm Lider Yardımcısı') && $user->hasBolumAuthority('bolum.disiplin.gor'))
            || $user->can('disiplin.portal.gor') 
            || $user->can_issue_disciplinary
            || $user->disiplinDosyalari()->exists();

        if (!$hasAccess) {
            abort(403, 'Disiplin portalına erişim yetkiniz yok.');
        }

        // 2. TEMEL SORGU
        $query = DisciplinaryCase::with(['user.bolum', 'behavior.category', 'reporter', 'impact', 'scope'])
            ->latest('created_at');

        $filterMessage = '';
        $filterType = 'info';

        // --- YETKİ FİLTRELEME MANTIĞI (GÜNCELLENDİ) ---

        // A. SÜPER YETKİLİLER (Admin, Hukuk, Kurul, Yönetim) -> HER ŞEYİ GÖRÜR
        if ($user->hasRole(['Superadmin', 'Yonetim', 'Yönetim', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) || $user->can('disiplin.portal.gor')) {
            $filterMessage = 'Tam yetkili görünümü: Tüm dosyalar listeleniyor.';
            $filterType = 'success';
        }

        // B. DİREKTÖRLER -> Kendi bölümlerine ait dosyaları görür
        else if ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();

            $query->where(function ($q) use ($yonetilenBolumIds) {
                // Raporlayanı benim bölümümden olanlar
                $q->whereHas('reporter', function ($sub) use ($yonetilenBolumIds) {
                    $sub->whereIn('bolum_id', $yonetilenBolumIds);
                })
                    // VEYA suçlananı benim bölümümden olanlar
                    ->orWhereHas('user', function ($sub) use ($yonetilenBolumIds) {
                        $sub->whereIn('bolum_id', $yonetilenBolumIds);
                    });
            });

            $filterMessage = 'Sorumlu olduğunuz bölümlere ait disiplin kayıtlarını görüyorsunuz.';
            $filterType = 'success';
        }

        // C. BÖLÜM YETKİLİLERİ (Liderler, Yetkili Yardımcılar, İSG, Güvenlik vb.)
        else if ($user->hasRole('Bölüm Lideri') || ($user->hasRole('Bölüm Lider Yardımcısı') && $user->hasBolumAuthority('bolum.disiplin.gor')) || $user->can_issue_disciplinary || ($user->bolum && $user->bolum->is_disciplinary_global)) {
            // KURAL: Sadece "Raporlayanı (Tutanak Tutanı)" benim bölümümden olan dosyaları getir.
            $query->where(function ($q) use ($user) {
                $q->whereHas('reporter', function ($sub) use ($user) {
                    $sub->where('bolum_id', $user->bolum_id);
                });

                // EKSTRA: Dosya sahibi (suçlanan) benim bölümümden ise onu da göreyim (Lider veya Yetkili Yardımcıysam)
                if ($user->hasRole('Bölüm Lideri') || ($user->hasRole('Bölüm Lider Yardımcısı') && $user->hasBolumAuthority('bolum.disiplin.gor'))) {
                    $q->orWhereHas('user', function ($sub) use ($user) {
                        $sub->where('bolum_id', $user->bolum_id);
                    });
                }
            });

            if ($user->bolum && $user->bolum->is_disciplinary_global) {
                $filterMessage = 'Bölümünüz (' . $user->bolum->ad . ') genel tutanak yetkisine sahiptir. Sadece bölümünüz tarafından oluşturulan tutanakları görüyorsunuz.';
            } else {
                $filterMessage = 'Sadece kendi bölümünüzle ilgili kayıtları görüyorsunuz.';
                $filterType = 'warning';
            }
        }
        
        // D. PERSONEL ŞAHSİ GÖRÜNÜMÜ
        else {
            $query->where('user_id', $user->id);
            $filterMessage = 'Kendi disiplin dosyalarınızı görüntülüyorsunuz.';
            $filterType = 'info';
        }

        // 4. MEVCUT FİLTRELERİ UYGULA (Search ve Durum - KORUNDU)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }

        // Tarih aralığı filtresi
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Oylama Aktif Filtresi
        if ($request->has('oylama_aktif')) {
            $query->where('oylama_aktif', 1);
        }

        // === İSTATİSTİKLER ===
        $statsQuery = clone $query;
        $allForStats = $statsQuery->get();
        $stats = [
            'toplam' => $allForStats->count(),
            'savunma_bekleyen' => $allForStats->where('durum', 'Savunma Bekleniyor')->count(),
            'inceleme_bekleyen' => $allForStats->where('durum', 'Yönetici Değerlendirmesi')->count(),
            'kurulda' => $allForStats->whereIn('durum', ['Kurulda', 'Kurul İncelemesinde'])->count(),
            'oylama_baslatilanlar' => $allForStats->where('oylama_aktif', true)->count(),
            'karar_verildi' => $allForStats->where('durum', 'Karar Verildi')->count(),
            'taslak' => $allForStats->where('durum', 'Taslak')->count(),
            'iptal' => $allForStats->whereIn('durum', ['İptal Edildi', 'İptal'])->count(),
            'ortalama_puan' => $allForStats->count() > 0 ? round($allForStats->avg('hesaplanan_puan'), 1) : 0,
        ];

        // 5. SAYFALAMA (KORUNDU)
        $cases = $query->paginate(20)->withQueryString();

        return view('admin.disiplin.index', compact('cases', 'filterMessage', 'filterType', 'stats'));
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
            'comments' => function ($q) {
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
        if ($user->hasRole(['Superadmin', 'Yonetim', 'Yönetim', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi']) || $user->can('disiplin.portal.gor')) {
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

        // C.1. Direktör Yetkisi
        if ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            if (in_array($case->user->bolum_id, $yonetilenBolumIds) || ($case->reporter && in_array($case->reporter->bolum_id, $yonetilenBolumIds))) {
                return view('admin.disiplin.show', compact('case'));
            }
        }

        // D. Bölüm Liderleri ve Yetkili Sorumlular (Yeni Eklenen Kısım)
        // Hem Liderleri hem de Delegasyon yetkisi olanları kapsar.
        $hasAuthority = $user->hasRole('Bölüm Lideri') 
            || ($user->hasRole('Bölüm Lider Yardımcısı') && $user->hasBolumAuthority('bolum.disiplin.gor'))
            || $user->can_issue_disciplinary;

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
     * Disiplin Karar Tutanağı (Print/Yazdır)
     */
    public function print($id)
    {
        $case = DisciplinaryCase::with(['user.bolum', 'behavior.category', 'reporter'])->findOrFail($id);

        $puan = $case->hesaplanan_puan;
        $penaltyScale = \App\Models\DisciplinaryPenaltyScale::where('min_puan', '<=', $puan)
            ->where('max_puan', '>=', $puan)
            ->first();
        
        $cezaAdi = $penaltyScale ? $penaltyScale->ceza_adi : ($case->final_karar ?? 'Disiplin Kararı');
        
        $councilMembers = User::role(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])
            ->whereDoesntHave('roles', function($q){ $q->where('name', 'Superadmin'); })
            ->get()
            ->sortBy(function($u) { return $u->hasRole('Disiplin Kurulu Başkanı') ? 0 : 1; });

        $logo = \App\Models\Setting::get('site_logo');

        return view('admin.disiplin.print', compact('case', 'cezaAdi', 'councilMembers', 'logo'));
    }

    /**
     * Disiplin Karar Tutanağı (PDF İNDİR)
     */
    public function downloadPdf($id)
    {
        $case = DisciplinaryCase::with(['user.bolum', 'behavior.category', 'reporter'])->findOrFail($id);

        $puan = $case->hesaplanan_puan;
        $penaltyScale = \App\Models\DisciplinaryPenaltyScale::where('min_puan', '<=', $puan)
            ->where('max_puan', '>=', $puan)
            ->first();
        
        $cezaAdi = $penaltyScale ? $penaltyScale->ceza_adi : ($case->final_karar ?? 'Disiplin Kararı');
        
        $councilMembers = User::role(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])
            ->whereDoesntHave('roles', function($q){ $q->where('name', 'Superadmin'); })
            ->get()
            ->sortBy(function($u) { return $u->hasRole('Disiplin Kurulu Başkanı') ? 0 : 1; });

        $logo = \App\Models\Setting::get('site_logo');
        $logoPath = $logo ? public_path('storage/' . $logo) : null;
        $logoBase64 = null;
        if ($logoPath && file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . $logoData;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.disiplin.print', [
            'case' => $case,
            'cezaAdi' => $cezaAdi,
            'councilMembers' => $councilMembers,
            'logo' => $logo,
            'logoBase64' => $logoBase64,
            'isPdf' => true
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'tempDir' => storage_path('temp'),
                'chroot' => [public_path()]
            ]);

        return $pdf->download('disiplin_kararı_#'.$case->id.'.pdf');
    }

    /**
     * DÜZENLEME SAYFASI
     */
    public function edit($id)
    {
        $case = DisciplinaryCase::findOrFail($id);

        $isSuperAdmin = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']);
        $canEditMatrix = Auth::user()->can('disiplin.tutanak.duzenle');
        $isReporter = Auth::id() == $case->reporter_id;
        $hasEvaluation = (bool) $case->yonetici_degerlendirme_notu;

        // KURULDA durumundaki dosyalar sadece Superadmin tarafından düzenlenebilir (Manipülasyon engeli)
        if ($case->durum == 'Kurulda' && !Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Dosya kurul aşamasında olduğu için düzenleme yapılamaz. Sadece Superadmin yetkilidir.');
        }

        if (!$isSuperAdmin && !($isReporter && !$hasEvaluation) && !$canEditMatrix) {
            abort(403, 'Bu kaydı düzenleme yetkiniz yok veya değerlendirme girildiği için düzenleme kapatıldı.');
        }

        $case->load('user', 'behavior.category');

        // Sadece belirli durumlarda düzenlemeye izin verelim (Örn: Karar verilmişse düzenlenemesin)
        if ($case->durum == 'Karar Verildi' || $case->durum == 'İptal Edildi') {
            return back()->with('error', 'Bu dosya kapatıldığı için düzenlenemez.');
        }

        $gizliRoller = ['Superadmin', 'Yönetim', 'Dış Avukat', 'Hukuk Yöneticisi'];

        $users = User::personel()
            ->where('id', '!=', Auth::id()) // Kendini seçemesin
            ->whereDoesntHave('roles', function ($q) use ($gizliRoller) {
                $q->whereIn('name', $gizliRoller); // Bu rollere sahip olanları getirme
            })
            ->orderBy('name')
            ->get();
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

        // YETKİ KONTROLÜ
        $isSuperAdmin = Auth::user()->hasRole(['Superadmin', 'Hukuk Admini']);
        $canEditMatrix = Auth::user()->can('disiplin.tutanak.duzenle');
        $isReporter = Auth::id() == $case->reporter_id;
        $hasEvaluation = (bool) $case->yonetici_degerlendirme_notu;

        if (!$isSuperAdmin && !($isReporter && !$hasEvaluation) && !$canEditMatrix) {
            abort(403, 'Bu kaydı güncelleme yetkiniz yok veya değerlendirme girildiği için işlem kapatıldı.');
        }

        // Validasyon
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
                // rules1.md ve kullanıcı talebi uyumlu isimlendirme
                $userName = \Illuminate\Support\Str::slug($case->user->name);
                $folderPath = "disiplin_kanitlar/{$userName}/{$case->id}";
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileName = now()->format('d.m.Y_H.i') . "_" . \Illuminate\Support\Str::slug($originalName) . "." . $file->getClientOriginalExtension();
                
                $path = $file->storeAs($folderPath, $fileName, 'public');
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

        $isSuperAdmin = $user->hasRole('Superadmin');
        $isReporter = $user->id == $case->reporter_id;
        $hasDefense = (bool) $case->savunma_tarihi;

        // 1. SUPERADMIN: Her zaman silebilir.
        if ($isSuperAdmin) {
            $this->performDelete($case);
            return redirect()->route('admin.disiplin.index')->with('success', 'Kayıt (Superadmin yetkisiyle) silindi.');
        }

        // 2. DİĞERLERİ: SADECE kaydı oluşturan kişi VE savunma henüz girilmemişse.
        if ($isReporter && !$hasDefense) {
            $this->performDelete($case);
            return redirect()->route('admin.disiplin.index')->with('success', 'Tutanak silindi.');
        }

        if ($hasDefense) {
            return back()->with('error', 'Personel savunma verdiği için bu dosyayı silemezsiniz.');
        }

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
        // BAŞA User::personel() EKLENDİ
        $usersQuery = User::personel()
            ->where('id', '!=', $currentUser->id) // Kendini seçemesin
            ->orderBy('name');

        // A. DOKUNULMAZLAR (Hiyerarşik Koruma)
        // Bu rollere sahip kişiler ASLA listede çıkmaz (Tutanak tutulamaz).
        $protectedRoles = ['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi', 'Dış Avukat', 'Yönetim'];

        $usersQuery->whereDoesntHave('roles', function ($q) use ($protectedRoles) {
            $q->whereIn('name', $protectedRoles);
        });

        // B. YETKİ KAPSAMI (GLOBAL Mİ, YEREL Mİ?)

        // --- DURUM 1: SÜPER YETKİLİLER ---
        // Superadmin ve Hukukçular fabrikadaki HERKESİ görür.
        if ($currentUser->hasRole(['Superadmin', 'Hukuk Admini']) || $currentUser->can('disiplin.tutanak.olustur')) {
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
                    $usersQuery->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'Bölüm Lideri');
                    });
                }
            } else {
                // >> YEREL YETKİ (STANDART) <<
                // Sadece kendi bölümündeki personeli görebilirler.
                if ($currentUser->bolum_id) {
                    $usersQuery->where('bolum_id', $currentUser->bolum_id);

                    // Hiyerarşi Kuralı: Eğer kullanıcı "Lider" değilse (yani yetkili personelse),
                    // kendi bölüm liderini şikayet edemez.
                    if (!$currentUser->hasRole('Bölüm Lideri')) {
                        $usersQuery->whereDoesntHave('roles', function ($q) {
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
            // 1. MATRİS HESAPLAMA
            $calc = $this->calculateMatrixScore(
                $request->user_id,
                $request->behavior_id,
                $request->impact_id,
                $request->scope_id
            );

            // 2. Kayıt (ID almak için önce oluşturuyoruz)
            $case = DisciplinaryCase::create([
                'user_id' => $request->user_id,
                'reporter_id' => Auth::id(),
                'behavior_id' => $request->behavior_id,
                'impact_id' => $request->impact_id,
                'scope_id' => $request->scope_id,
                'olay_tarihi' => $request->olay_tarihi,
                'olay_aciklamasi' => $request->olay_aciklamasi,
                'tekrar_sayisi' => $calc['tekrar'],
                'hesaplanan_puan' => $calc['toplam_puan'],
                'sistem_oneri_ceza' => $calc['oneri_ceza'],
                'final_karar' => $calc['oneri_ceza'],
                'durum' => 'Savunma Bekleniyor'
            ]);

            // 3. Dosya Yükleme (rules1.md uyumlu)
            if ($request->hasFile('kanit_dosyalari')) {
                $kanitYollari = [];
                foreach ($request->file('kanit_dosyalari') as $file) {
                    $userName = \Illuminate\Support\Str::slug($case->user->name);
                    $folderPath = "disiplin_kanitlar/{$userName}/{$case->id}";
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $fileName = now()->format('d.m.Y_H.i') . "_" . \Illuminate\Support\Str::slug($originalName) . "." . $file->getClientOriginalExtension();
                    
                    $path = $file->storeAs($folderPath, $fileName, 'public');
                    $kanitYollari[] = $path;
                }
                $case->update(['kanit_dosyalari' => $kanitYollari]);
            }

            // --- BİLDİRİM GÖNDERİMİ (DEDUPLICATED) ---
            $recipients = collect();

            // 1. Tutanak Yiyen Personel (Ayrı Bildirim)
            try {
                $case->user->notify(new DisiplinTutanagiOlusturuldu($case));
            } catch (\Exception $e) {
                \Log::error('Personel bildirimi hatası: ' . $e->getMessage());
            }

            // 2. Diğer Alıcıları Topla (Tekilleştirilecek Grup)
            try {
                // A) Yöneticiler & Kurul Üyeleri
                $yoneticiRolleri = ['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'];
                $recipients = $recipients->merge(User::role($yoneticiRolleri)->get());

                // B) Bölüm Liderleri
                if ($case->user->bolum_id) {
                    $bolumLiderleri = User::where('bolum_id', $case->user->bolum_id)
                        ->role('Bölüm Lideri')
                        ->get();
                    $recipients = $recipients->merge($bolumLiderleri);
                }

                // C) Bölüm Direktörü
                if ($case->user->bolum && $case->user->bolum->director) {
                    $recipients->push($case->user->bolum->director);
                }

                // D) Tekilleştir, Kendini ve Vaka Sahibini Çıkar
                $uniqueRecipients = $recipients->unique('id')->filter(function ($u) use ($case) {
                    return $u->id != Auth::id() && $u->id != $case->user_id;
                });

                // E) Gönder
                foreach ($uniqueRecipients as $recipient) {
                    $recipient->notify(new DisiplinTutanagiOlusturuldu($case));
                }
            } catch (\Exception $e) {
                \Log::error('Yönetici/Amir bildirimleri hatası: ' . $e->getMessage());
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
        $canWriteDefense = $user->can('disiplin.personel.savunma.yaz');

        if (!$isOwner && !$isReporter && !$isAdmin && !$canWriteDefense) {
            abort(403, 'Bu dosyaya savunma girme yetkiniz yok.');
        }

        $request->validate([
            'savunma_aciklamasi' => 'required|min:5',
            'savunma_dosyalari.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:20480'
        ]);

        DB::beginTransaction();
        try {
            $savunmaDosyalari = $case->savunma_dosyalari ?? [];

            // Dosya Yükleme (rules1.md uyumlu)
            if ($request->hasFile('savunma_dosyalari')) {
                foreach ($request->file('savunma_dosyalari') as $file) {
                    $userName = \Illuminate\Support\Str::slug($case->user->name);
                    $folderPath = "disiplin_savunmalar/{$userName}/{$case->id}";
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $fileName = now()->format('d.m.Y_H.i') . "_" . \Illuminate\Support\Str::slug($originalName) . "." . $file->getClientOriginalExtension();
                    
                    $path = $file->storeAs($folderPath, $fileName, 'public');
                    $savunmaDosyalari[] = $path;
                }
            }

            // SAVUNMA METNİ İŞLEME
            $isUpdate = $case->savunma_tarihi ? true : false;
            $finalText = $request->savunma_aciklamasi;

            // Eğer savunmayı giren kişi dosya sahibi DEĞİLSE kuralı (Zaten vardı)
            if (!$isOwner) {
                $finalText .= "\n\n(Not: Bu savunma " . now()->format('d.m.Y H:i') . " tarihinde Bölüm Yöneticisi " . $user->name . " tarafından personel adına sisteme girilmiştir.)";
            }

            // LOGLAMA: Değişikliği kayıt altına al
            try {
                DisciplinaryLog::create([
                    'disciplinary_case_id' => $case->id,
                    'user_id' => $user->id,
                    'eylem' => $isUpdate ? 'Savunma Düzenlendi' : 'Savunma Oluşturuldu',
                    'aciklama' => $user->name . " tarafından " . ($isUpdate ? "savunma metni güncellendi." : "savunma metni girişi yapıldı."),
                    'eski_metin' => $isUpdate ? $case->savunma_aciklamasi : null
                ]);
            } catch (\Exception $e) {
                \Log::error('Savunma log hatası: ' . $e->getMessage());
            }

            $case->update([
                'savunma_aciklamasi' => $finalText,
                'savunma_dosyalari' => $savunmaDosyalari,
                'savunma_tarihi' => now(),
                'durum' => 'Yönetici Değerlendirmesi'
            ]);

            // --- BİLDİRİMLER ---

            // 1. Hukuk ve Yönetime Bildir
            try {
                $yoneticiler = User::role(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini'])->get();
                foreach ($yoneticiler as $yonetici) {
                    $yonetici->notify(new PersonelSavunmaVerdi($case));
                }
            } catch (\Exception $e) {
                \Log::error('Hukuk bildirim hatası: ' . $e->getMessage());
            }

            // 2. Bölüm Liderlerine Bildir
            try {
                $bolumLiderleri = User::where('bolum_id', $case->user->bolum_id)
                    ->role('Bölüm Lideri')
                    ->get();
                foreach ($bolumLiderleri as $lider) {
                    $lider->notify(new PersonelSavunmaVerdi($case));
                }
            } catch (\Exception $e) {
                \Log::error('Lider bildirim hatası: ' . $e->getMessage());
            }

            // 3. Bölüm Direktörüne Bildir
            try {
                if ($case->user->bolum && $case->user->bolum->director) {
                    $case->user->bolum->director->notify(new PersonelSavunmaVerdi($case));
                }
            } catch (\Exception $e) {
                \Log::error('Direktör bildirim hatası: ' . $e->getMessage());
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

        $request->validate([
            'karar_dosyalari.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:20480',
            'yonetici_notu' => 'required|min:5'
        ]);

        // Karar Dosyaları Yükleme (rules1.md uyumlu)
        $dosyaYolları = $case->karar_dosyasi ?? [];
        if ($request->hasFile('karar_dosyalari')) {
            $userName = \Illuminate\Support\Str::slug($case->user->name);
            foreach ($request->file('karar_dosyalari') as $file) {
                $folderPath = "disiplin_kararlar/{$userName}/{$case->id}";
                $fileName = now()->format('d.m.Y_H.i') . "_" . \Illuminate\Support\Str::random(2) . "." . $file->getClientOriginalExtension();
                $path = $file->storeAs($folderPath, $fileName, 'public');
                $dosyaYolları[] = $path;
            }
        }

        $not = $request->yonetici_notu ?? 'Karar verildi.';
        $imzaliNot = $not . ' (İşlemi Yapan: ' . Auth::user()->name . ' [ID:' . Auth::id() . '] - ' . now()->format('d.m.Y H:i') . ')';
        $case->update([
            'durum' => 'Karar Verildi',
            'final_karar' => $case->sistem_oneri_ceza, // Örn: "Sözlü Uyarı"
            'yonetici_notu' => $imzaliNot, // Güncellendi
            'karar_dosyasi' => $dosyaYolları,
            'karar_tarihi' => now(),
        ]);

        // Puan Düşme İşlemi
        $user = User::find($case->user_id);
        if ($user) {
            $user->decrement('toplam_puan', $case->hesaplanan_puan);
        }

        // --- İLİŞKİLİ TOPLANTI OTOMATİK TAMAMLA ---
        $this->completeRelatedMeeting($case);

        // --- BİLDİRİM: PERSONEL + AMİRLER + KURUL (DEDUPLICATED) ---
        try {
            $case->load('user.bolum');
            $personel = $case->user;

            // 1. Personele bildir (Özel mesaj şablonu)
            if ($personel) {
                $personel->notify(new \App\Notifications\DisiplinKararVerildi($case, true));
            }

            // 2. Diğer Alıcıları Topla
            $recipients = collect();

            if ($personel && $personel->bolum) {
                $bolum = $personel->bolum;
                // Bölüm Liderleri
                $recipients = $recipients->merge($bolum->users()->role('Bölüm Lideri')->get());
                // Direktör
                if ($bolum->director) {
                    $recipients->push($bolum->director);
                }
            }

            // Kurul Üyeleri
            $kurulUyeleri = User::role(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])->get();
            $recipients = $recipients->merge($kurulUyeleri);

            // Tekilleştir ve Filtrele
            $uniqueRecipients = $recipients->unique('id')->filter(function ($u) {
                return $u->id != Auth::id(); // Vaka sahibine (personel) zaten gönderdik, burada kendimizi çıkarıyoruz
            });

            // Gönder
            foreach ($uniqueRecipients as $recipient) {
                // Not: Personel bu listede olsa bile (id != Auth::id filtresinde kalırsa) 
                // DisiplinKararVerildi içindeki organizational ba kontrolü doğru mesajı seçecektir.
                // Ancak personeli (vaka sahibi) zaten yukarıda notify ettik, 
                // bu yüzden bu listeden de çıkarabiliriz.
                if ($recipient->id != $personel->id) {
                    $recipient->notify(new \App\Notifications\DisiplinKararVerildi($case, false));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Disiplin karar bildirimi hatası (approvePenalty): ' . $e->getMessage());
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


        // Karar Dosyaları Yükleme (rules1.md uyumlu)
        $dosyaYolları = $case->karar_dosyasi ?? [];
        if ($request->hasFile('karar_dosyalari')) {
            $userName = \Illuminate\Support\Str::slug($case->user->name);
            foreach ($request->file('karar_dosyalari') as $file) {
                $folderPath = "disiplin_kararlar/{$userName}/{$case->id}";
                $fileName = now()->format('d.m.Y_H.i') . "_" . \Illuminate\Support\Str::random(2) . "." . $file->getClientOriginalExtension();
                $path = $file->storeAs($folderPath, $fileName, 'public');
                $dosyaYolları[] = $path;
            }
        }

        $not = $request->yonetici_notu ?? 'Savunma kabul edildi.';
        $imzaliNot = $not . ' (İşlemi Yapan: ' . Auth::user()->name . ' [ID:' . Auth::id() . '] - ' . now()->format('d.m.Y H:i') . ')';
        $case->update([
            'durum' => 'Karar Verildi',
            'final_karar' => 'Savunma Kabul Edildi (Ceza Yok)', // Karar metni
            'yonetici_notu' => $imzaliNot, // Güncellendi
            'karar_dosyasi' => $dosyaYolları,
            'karar_tarihi' => now(),
        ]);

        // DİKKAT: Puan düşme işlemi YAPMIYORUZ.

        // --- İLİŞKİLİ TOPLANTI OTOMATİK TAMAMLA ---
        $this->completeRelatedMeeting($case);

        // --- BİLDİRİM: PERSONEL + AMİRLER + KURUL (DEDUPLICATED) ---
        try {
            $case->load('user.bolum');
            $personel = $case->user;

            // 1. Personele bildir (Özel mesaj şablonu)
            if ($personel) {
                $personel->notify(new \App\Notifications\DisiplinKararVerildi($case, true));
            }

            // 2. Diğer Alıcıları Topla
            $recipients = collect();

            if ($personel && $personel->bolum) {
                $bolum = $personel->bolum;
                // Bölüm Liderleri
                $recipients = $recipients->merge($bolum->users()->role('Bölüm Lideri')->get());
                // Direktör
                if ($bolum->director) {
                    $recipients->push($bolum->director);
                }
            }

            // Kurul Üyeleri
            $kurulUyeleri = User::role(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])->get();
            $recipients = $recipients->merge($kurulUyeleri);

            // Tekilleştir ve Filtrele
            $uniqueRecipients = $recipients->unique('id')->filter(function ($u) use ($personel) {
                return $u->id != Auth::id() && ($personel ? $u->id != $personel->id : true);
            });

            // Gönder
            foreach ($uniqueRecipients as $recipient) {
                $recipient->notify(new \App\Notifications\DisiplinKararVerildi($case, false));
            }
        } catch (\Exception $e) {
            \Log::error('Savunma kabul bildirimi hatası: ' . $e->getMessage());
        }

        return back()->with('success', 'Savunma haklı bulundu ve kabul edildi. Personelden puan düşülmedi.');
    }

    /**
     * KARAR 3: KURULA SEVK ET
     */
    public function sendToBoard(Request $request, $id)
    {
        if (!Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini'])) {
            abort(403);
        }

        $case = DisciplinaryCase::with('user')->findOrFail($id);

        $not = $request->yonetici_notu ?? 'Kurula sevk edildi.';
        $imzaliNot = $not . ' (İşlemi Yapan: ' . Auth::user()->name . ' [ID:' . Auth::id() . '] - ' . now()->format('d.m.Y H:i') . ')';

        // Karar Dosyaları Yükleme (rules1.md uyumlu)
        $dosyaYolları = $case->karar_dosyasi ?? [];
        if ($request->hasFile('karar_dosyalari')) {
            $userName = \Illuminate\Support\Str::slug($case->user->name);
            foreach ($request->file('karar_dosyalari') as $file) {
                $folderPath = "disiplin_kararlar/{$userName}/{$case->id}";
                $fileName = now()->format('d.m.Y_H.i') . "_" . \Illuminate\Support\Str::random(2) . "." . $file->getClientOriginalExtension();
                $path = $file->storeAs($folderPath, $fileName, 'public');
                $dosyaYolları[] = $path;
            }
        }

        DB::transaction(function () use ($case, $request, $imzaliNot, $dosyaYolları) {
            // 1. Dosya Durumunu Güncelle
            $case->update([
                'durum' => 'Kurulda',
                'yonetici_notu' => $imzaliNot,
                'karar_dosyasi' => $dosyaYolları,
                'toplanti_tarihi' => $request->toplanti_tarihi,
                'karar_tarihi' => now()
            ]);

            // 2. OTOMATİK TOPLANTI OLUŞTUR (Eğer zaten bir toplantıya bağlı değilse)
            $existingMeeting = $case->toplantilar()->whereIn('durum', ['planlandı', 'devam_ediyor'])->first();
            
            if (!$existingMeeting) {
                $toplanti = DisiplinKuruluToplanti::create([
                    'baslik'           => 'Disiplin Kurulu - ' . ($case->user->name ?? 'Dosya #' . $case->id),
                    'aciklama'         => $case->olay_aciklamasi,
                    'tur'              => 'karar_oturumu',
                    'baslangic_tarihi' => $request->toplanti_tarihi ?? now()->addDays(1)->setHour(10)->setMinute(0),
                    'durum'            => 'planlandı',
                    'olusturan_user_id' => Auth::id(),
                ]);
                $toplanti->disiplinDosyalari()->attach($case->id);
            } else {
                $toplanti = $existingMeeting;
                // Eğer formdan yeni bir tarih geldiyse ve mevcut toplantı planlandı aşamasındaysa tarihi güncelleyebiliriz
                if ($request->toplanti_tarihi && $toplanti->durum === 'planlandı') {
                    $toplanti->update(['baslangic_tarihi' => $request->toplanti_tarihi]);
                }
            }

            // 3. KURUL ÜYELERİNİ EKLE
            $kurulUyeleri = User::role(['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])->get();
            foreach ($kurulUyeleri as $uye) {
                DisiplinKuruluToplantiKatilimci::create([
                    'toplanti_id'    => $toplanti->id,
                    'user_id'        => $uye->id,
                    'rol'            => 'katilimci',
                    'katilim_durumu' => 'bekleniyor',
                ]);

                // Bildirim: Toplantı Daveti
                try {
                    $uye->notify(new \App\Notifications\DisiplinToplantiDavetNotification($toplanti, Auth::user()->name));
                } catch (\Exception $e) {
                    Log::error('Kurul toplantı davet bildirimi hatası: ' . $e->getMessage());
                }
            }

            // --- İDARİ PAYDAŞLARA BİLDİRİM (Hukuk Admin / Yetkili Hukuk Yöneticisi) ---
            try {
                $stakeholders = User::role(['Hukuk Admini', 'Hukuk Yöneticisi'])->get()->filter(function($u) {
                    if ($u->id === Auth::id()) return false;
                    if ($u->hasRole('Hukuk Yöneticisi') && !$u->can('disiplin.kurul.portal.gor')) return false;
                    return true;
                });

                foreach ($stakeholders as $stakeholder) {
                    $stakeholder->notify(new \App\Notifications\DisiplinKuruluToplantiNotification('planlandi', $toplanti, Auth::user()->name));
                }
            } catch (\Exception $e) {
                Log::error('IDari paydaş toplantı planlama bildirimi hatası: ' . $e->getMessage());
            }
        });

        // --- BİLDİRİM: PERSONEL + AMİRLER (DEDUPLICATED) ---
        try {
            $case->load('user.bolum');
            $personel = $case->user;

            // 1. Personele bildir (Özel bildirim sınıfı)
            if ($personel) {
                $personel->notify(new \App\Notifications\DisiplinPersonelKurulaSevkBildirimi($case, true));
            }

            // 2. Amirleri Topla
            $managers = collect();
            if ($personel && $personel->bolum) {
                $bolum = $personel->bolum;
                // Bölüm Liderleri
                $managers = $managers->merge($bolum->users()->role('Bölüm Lideri')->get());
                // Direktör
                if ($bolum->director) {
                    $managers->push($bolum->director);
                }
            }

            // Tekilleştir ve Filtrele
            $uniqueManagers = $managers->unique('id')->filter(function ($u) use ($personel) {
                return $u->id != Auth::id() && ($personel ? $u->id != $personel->id : true);
            });

            // Gönder
            foreach ($uniqueManagers as $manager) {
                $manager->notify(new \App\Notifications\DisiplinPersonelKurulaSevkBildirimi($case, false));
            }
        } catch (\Exception $e) {
            \Log::error('Kurula sevk bildirimi hatası: ' . $e->getMessage());
        }

        return back()->with('success', 'Dosya Kurul\'a sevk edildi ve otomatik toplantı kaydı oluşturuldu.');
    }

    /**
     * OYLAMAYI BAŞLAT (Sadece Disiplin Kurulu Başkanı / Superadmin)
     */
    public function startVoting(Request $request, $id)
    {
        if (!Auth::user()->hasRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini']) && !Auth::user()->can('disiplin.oylama.baslat')) {
            abort(403, 'Oylamayı sadece yetkili kurul üyeleri başlatabilir.');
        }

        $case = DisciplinaryCase::findOrFail($id);

        if ($case->durum !== 'Kurulda') {
            return back()->with('error', 'Oylama sadece "Kurulda" aşamasındaki dosyalar için başlatılabilir.');
        }

        if ($case->oylama_aktif) {
            return back()->with('info', 'Oylama zaten başlatılmış.');
        }

        $case->update([
            'oylama_aktif' => true,
            'oylama_notu' => $request->input('oylama_notu')
        ]);

        // Bildirim Gönderilecek Idari Paydaşları Topla
        try {
            $stakeholders = User::role(['Hukuk Admini', 'Hukuk Yöneticisi'])->get()->filter(function($u) {
                // Başlatan Hukuk Admini ise kendisini çıkar
                if ($u->id === Auth::id()) return false;
                
                // Hukuk Yöneticisi ise yetkisi var mı bak
                if ($u->hasRole('Hukuk Yöneticisi') && !$u->can('disiplin.kurul.portal.gor')) {
                    return false;
                }
                return true;
            });

            $kurulUyeleri = User::role(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'])->get();
            
            // Tüm alıcıları birleştir (tekilleştir)
            $allRecipients = $stakeholders->merge($kurulUyeleri)->unique('id');

            foreach ($allRecipients as $recipient) {
                $recipient->notify(new \App\Notifications\DisiplinOylamaBaslatildi($case, Auth::user(), $request->input('oylama_notu')));
            }
        } catch (\Exception $e) {
            \Log::error('Oylama başlatma bildirimi hatası: ' . $e->getMessage());
        }

        return back()->with('success', 'Oylama başarıyla başlatıldı. Kurul üyelerine bildirim gönderildi.')
            ->with('tab', 'kurul');
    }

    /**
     * KARARI GERİ AL (Revize Edildi)
     */
    public function revokeDecision($id)
    {
        // 1. GENEL YETKİ
        if (!Auth::user()->hasRole(['Superadmin', 'Hukuk Yöneticisi', 'Hukuk Admini'])) {
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
                'yonetici_notu' => null,
                'karar_dosyasi' => null,
                'toplanti_tarihi' => null // Toplantı tarihini de sıfırla
            ]);

            // --- İLGİLİ TOPLANTIYI TEMİZLE ---
            // Müdür değerlendirmesine dönüldüğü için bu vakanın toplantı ilişkisi kesilmeli
            $case->toplantilar()->detach();

            // --- KURUL OYLARINI SİL ---
            // Kurul odasındaki oylamaları da sıfırlıyoruz
            DisciplinaryVote::where('case_id', $case->id)->delete();

            // --- BİLDİRİMLERİ TEMİZLE ---
            // İlgili dosya ile ilgili TÜM bildirimleri (Karar, Davet, Oylama Başlatıldı) temizle
            $notificationTypes = [
                'App\Notifications\DisiplinKararVerildi',
                'App\Notifications\DisiplinToplantiDavetNotification',
                'App\Notifications\DisiplinOylamaBaslatildi'
            ];

            DB::table('notifications')
                ->whereIn('type', $notificationTypes)
                ->where('data', 'like', '%"case_id":' . $case->id . '%')
                ->delete();

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

        // --- OTOMATİK YOKLAMA: Toplantı varsa kullanıcıyı 'katıldı' olarak işaretle ---
        $toplanti = $case->toplantilar()
            ->whereIn('durum', ['planlandı', 'devam_ediyor'])
            ->first();

        if ($toplanti) {
            DisiplinKuruluToplantiKatilimci::where('toplanti_id', $toplanti->id)
                ->where('user_id', Auth::id())
                ->update(['katilim_durumu' => 'katildi']);
        }

        return back()->with('success', 'Oyunuz ve görüşünüz başarıyla kaydedildi.')
            ->with('tab', 'kurul');
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
            return back()->with('success', 'Oyunuz geri çekildi.')
                ->with('tab', 'kurul');
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
        $comment = DisciplinaryComment::create([
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
            $recipient->notify(new YeniDisiplinYorumu($case, Auth::user()->name));
        }

        return back()->with('success', 'Yorumunuz eklendi.');
    }

    /**
     * YORUM GÜNCELLEME (Loglu)
     */
    public function updateComment(Request $request, $id)
    {
        $comment = DisciplinaryComment::findOrFail($id);

        // Yetki: Sadece yorum sahibi düzenleyebilir (Superadmin bile olsa başkasının yorumunu değiştirmemeli, silebilir ama değiştirmemeli etik olarak)
        if ($comment->user_id != Auth::id()) {
            abort(403, 'Sadece kendi yorumunuzu düzenleyebilirsiniz.');
        }

        $request->validate(['yorum' => 'required|string|max:1000']);

        // LOGLAMA (Değişiklik varsa)
        if ($comment->yorum !== $request->yorum) {
            DisciplinaryCommentHistory::create([
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
        $comment = DisciplinaryComment::findOrFail($id);
        $user = Auth::user();

        // 1. Superadmin ve Hukuk Admini silebilir.
        // 2. Yorum sahibi silebilir.
        if ($user->hasRole(['Superadmin', 'Hukuk Admini']) || $comment->user_id == $user->id) {
            $comment->delete(); // Soft Delete olduğu için veritabanından silinmez, deleted_at işlenir.
            return back()->with('success', 'Yorum kaldırıldı.');
        }

        abort(403, 'Yetkiniz yok.');
    }

    /**
     * Disiplin dosyasına bağlı toplantıyı otomatik olarak tamamlar.
     * approvePenalty ve acceptDefense çağrıldığında kullanılır.
     */
    private function completeRelatedMeeting(DisciplinaryCase $case): void
    {
        try {
            $toplantiIds = $case->toplantilar()->whereIn('durum', ['planlandı', 'devam_ediyor'])->pluck('disiplin_kurulu_toplanti.id');
            
            if ($toplantiIds->isNotEmpty()) {
                DisiplinKuruluToplanti::whereIn('id', $toplantiIds)->update([
                    'durum'        => 'tamamlandı',
                    'bitirilme_at' => now(),
                ]);

                // --- OTOMATİK YOKLAMA: Kapanışta 'bekleniyor' olanları 'katılmadı' yap ---
                DisiplinKuruluToplantiKatilimci::whereIn('toplanti_id', $toplantiIds)
                    ->where('katilim_durumu', 'bekleniyor')
                    ->update(['katilim_durumu' => 'katilmadi']);
            }
        } catch (\Exception $e) {
            \Log::error('İlişkili toplantı tamamlama hatası: ' . $e->getMessage());
        }
    }

}