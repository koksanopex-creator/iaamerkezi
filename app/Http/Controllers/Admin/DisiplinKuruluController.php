<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisiplinKuruluToplanti;
use App\Models\DisiplinKuruluToplantiKatilimci;
use App\Models\DisiplinKuruluUyelik;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryVote;
use App\Models\User;
use App\Notifications\DisiplinKuruluUyelikNotification;
use App\Notifications\DisiplinKuruluToplantiNotification;
use App\Notifications\DisiplinToplantiDavetNotification;
use App\Notifications\DisiplinToplantiRevizyonNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class DisiplinKuruluController extends Controller
{
    /**
     * Yetki kontrolü: Sadece bu roller erişebilir.
     */
    private function yetkiKontrol(): void
    {
        $user = Auth::user();

        // Superadmin ve Yonetim her zaman girebilir
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            return;
        }

        // Hukuk Admini, Kurul Başkanı ve Üyeleri de girebilir
        if ($user->hasRole(['Hukuk Admini', 'Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'])) {
            return;
        }

        // Hukuk Yöneticisi ise 'disiplin.kurul.portal.gor' izni olmalı
        if ($user->hasRole('Hukuk Yöneticisi') && $user->can('disiplin.kurul.portal.gor')) {
            return;
        }

        abort(403, 'Bu sayfaya erişim yetkiniz yok.');
    }

    /**
     * Üye yönetim yetkisi: Başkan + Hukuk Admini + Superadmin
     */
    private function uyeYonetimiYetkisi()
    {
        $user = Auth::user();
        
        // Superadmin ve Hukuk Admini tam yetkili
        if ($user->hasRole(['Superadmin', 'Hukuk Admini'])) {
            return true;
        }

        // Kurul Başkanı tam yetkili
        if ($user->hasRole('Disiplin Kurulu Başkanı')) {
            return true;
        }

        // Hukuk Yöneticisi ise matristeki iznine bakılır
        if ($user->hasRole('Hukuk Yöneticisi') && $user->can('disiplin.kurul.uye.yonet')) {
            return true;
        }

        return false;
    }

    /**
     * Kurul ile ilgili bildirim gitmesi gereken idari paydaşları döner.
     * (Hukuk Admini, Hukuk Yöneticisi ve Kurul Üyeleri)
     */
    private function getKurulStakeholders($excludeUserId = null)
    {
        $stakeholders = User::role([
            'Disiplin Kurulu Başkanı', 
            'Disiplin Kurulu Üyesi', 
            'Hukuk Admini', 
            'Hukuk Yöneticisi'
        ])->get();

        if ($excludeUserId) {
            $stakeholders = $stakeholders->where('id', '!=', $excludeUserId);
        }

        return $stakeholders->unique('id');
    }

    // =========================================================
    // ANA PORTAL SAYFASI
    // =========================================================
    public function index(Request $request)
    {
        $this->yetkiKontrol();

        // Filtreler (Varsayılan bitiş tarihini yıl sonuna çekiyoruz ki gelecek toplantılar da görünsün)
        $baslangic = $request->input('baslangic', Carbon::now()->startOfYear()->format('Y-m-d'));
        $bitis     = $request->input('bitis', Carbon::now()->endOfYear()->format('Y-m-d'));
        $bolumId   = $request->input('bolum_id');

        // Kurul üyeleri (rollere göre)
        $baskanlar = User::role('Disiplin Kurulu Başkanı')->with('bolum')->get();
        $uyeler    = User::role('Disiplin Kurulu Üyesi')->with('bolum')->get();

        $tumUyeler = $baskanlar->merge($uyeler);

        // Toplam toplantı sayısı (oylaması olan benzersiz davalar)
        $toplamToplanti = DisciplinaryCase::whereBetween('toplanti_tarihi', [$baslangic, $bitis])
            ->whereNotNull('toplanti_tarihi')
            ->when($bolumId, fn($q) => $q->whereHas('user', fn($qu) => $qu->where('bolum_id', $bolumId)))
            ->count();

        // Her üye için istatistikler
        $uyeStats = $tumUyeler->map(function ($uye) use ($baslangic, $bitis, $bolumId, $toplamToplanti, $baskanlar) {
            // Resmi Disiplin Dosya Oyları (disciplinary_votes tablosu) - Sadece aktif dosyalar
            $resmiOylar = \App\Models\DisciplinaryVote::where('user_id', $uye->id)
                ->whereHas('disciplinaryCase')
                ->get();
            
            // Canlı Toplantı Oyları (toplanti_oylari tablosu) - Sadece mevcut toplantılar
            $canliOylar = \App\Models\ToplantiOy::where('user_id', $uye->id)
                ->whereHas('oylama.toplanti')
                ->get();

            // Katılım Oranı (Hem 'katildi' hem 'katıldı' kontrolü + Sadece mevcut toplantılar)
            $toplamDavet = \App\Models\DisiplinKuruluToplantiKatilimci::where('user_id', $uye->id)
                ->whereHas('toplanti')
                ->count();
            $gercekKatilim = \App\Models\DisiplinKuruluToplantiKatilimci::where('user_id', $uye->id)
                ->whereHas('toplanti')
                ->whereIn('katilim_durumu', ['katıldı', 'katildi'])
                ->count();
            
            $katilimOrani = $toplamDavet > 0 ? round(($gercekKatilim / $toplamDavet) * 100) : 0;

            // --- OYLAMA İSTATİSTİKLERİ (Tekil Dosya Bazlı) ---
            
            // Tüm benzersiz disiplin dosyası ID'lerini topla
            $resmiCaseIds = $resmiOylar->pluck('case_id')->filter()->unique();
            $canliCaseIds = $canliOylar->flatMap(fn($oy) => $oy->oylama?->toplanti?->disiplinDosyalari->pluck('id'))->filter()->unique();
            $tumDavaIdleri = $resmiCaseIds->merge($canliCaseIds)->unique();

            $lehOy = 0; $aleyhOy = 0; $cekimserOy = 0;

            foreach ($tumDavaIdleri as $caseId) {
                $finalOy = null;

                // 1. Önce resmi oya bak (En kesin karar)
                $resmiOy = $resmiOylar->where('case_id', $caseId)->first();
                if ($resmiOy) {
                    $finalOy = $resmiOy->oy_yonu === 'Ceza Verilmesin' ? 'lehte' 
                             : ($resmiOy->oy_yonu === 'Ceza Verilsin' ? 'aleyhte' : 'cekimser');
                } else {
                    // 2. Resmi oy yoksa, en son yapılan canlı toplantı oylamasına bak
                    $sonCanliOy = $canliOylar->filter(function($oy) use ($caseId) {
                        return $oy->oylama?->toplanti?->disiplinDosyalari->contains('id', $caseId);
                    })->sortByDesc('created_at')->first();

                    if ($sonCanliOy) {
                        $finalOy = in_array($sonCanliOy->oy, ['lehte', 'evet', 'ceza_verilmesin']) ? 'lehte'
                                 : (in_array($sonCanliOy->oy, ['aleyhte', 'hayir', 'ceza_verilsin']) ? 'aleyhte' : 'cekimser');
                    }
                }

                // Sayacı artır
                if ($finalOy === 'lehte') $lehOy++;
                elseif ($finalOy === 'aleyhte') $aleyhOy++;
                elseif ($finalOy === 'cekimser') $cekimserOy++;
            }

            $toplamTekilDosya = $tumDavaIdleri->count();

            // --- Üyelik Geçmişi ---
            $uyelikKaydi = DisiplinKuruluUyelik::where('user_id', $uye->id)
                ->orderBy('katilim_tarihi', 'desc')
                ->first();

            return [
                'user'             => $uye,
                'rol'              => $baskanlar->contains($uye) ? 'Başkan' : 'Üye',
                'oy_kullanilanSayisi' => $toplamTekilDosya,
                'katilim_orani'    => $katilimOrani,
                'leh_oy'           => $lehOy,
                'aleyh_oy'         => $aleyhOy,
                'cekimser_oy'      => $cekimserOy,
                'katilim_tarihi'   => $uyelikKaydi?->katilim_tarihi,
                'notlar'           => $uyelikKaydi?->notlar,
            ];
        });

        // Genel istatistikler
        $toplamDosya = DisciplinaryCase::whereBetween('created_at', [$baslangic, $bitis])
            ->when($bolumId, fn($q) => $q->whereHas('user', fn($qu) => $qu->where('bolum_id', $bolumId)))
            ->count();

        $kararVerilenDosya = DisciplinaryCase::whereBetween('created_at', [$baslangic, $bitis])
            ->where('durum', 'Karar Verildi')
            ->when($bolumId, fn($q) => $q->whereHas('user', fn($qu) => $qu->where('bolum_id', $bolumId)))
            ->count();

        // [YENİ] Arşiv sekmesi için tüm dosyalar
        $arsivdekiDosyalar = DisciplinaryCase::whereBetween('created_at', [$baslangic, $bitis])
            ->when($bolumId, fn($q) => $q->whereHas('user', fn($qu) => $qu->where('bolum_id', $bolumId)))
            ->with(['user.bolum', 'behavior'])
            ->orderBy('created_at', 'desc')
            ->get();

        $toplamOy = DisciplinaryVote::whereBetween('created_at', [$baslangic, $bitis])->count();
        $ortalamKatilim = $toplamToplanti > 0 && $tumUyeler->count() > 0
            ? round($uyeStats->avg('katilim_orani'))
            : 0;

        // Kurulda olan dosyalar (Henüz toplantısı planlanmamış olanlar)
        $kuruldakiDosyalar = DisciplinaryCase::where('durum', 'Kurulda')
            ->whereDoesntHave('toplantilar') // Toplantısı olmayanlar
            ->with(['user', 'behavior'])
            ->orderBy('created_at', 'desc')
            ->get();

        // [YENİ] Toplantı planlarken seçilebilecek dosyalar ("Karar Verildi" dışındakiler)
        $secilebilirDosyalar = DisciplinaryCase::where('durum', '!=', 'Karar Verildi')
            ->with(['user', 'behavior'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Yaklaşan ve Devam Eden Toplantılar (Filtre kısıtlamasını gelecek toplantılar için esnetiyoruz)
        // Başlangıç tarihinden bağımsız olarak, 'planlandı' veya 'devam_ediyor' olan tüm toplantılar görünmeli
        $yaklasanToplantılar = DisiplinKuruluToplanti::whereIn('durum', ['planlandı', 'devam_ediyor'])
            ->when($bolumId, function($q) use ($bolumId) {
                return $q->whereHas('katilimcilar.user', fn($qu) => $qu->where('bolum_id', $bolumId));
            })
            ->with(['olusturan', 'katilimcilar.user', 'disiplinDosyalari.user'])
            ->orderBy('baslangic_tarihi')
            ->get();

        // Son kararlar
        $sonKararlar = DisciplinaryCase::whereNotNull('karar_tarihi')
            ->whereBetween('karar_tarihi', [$baslangic, $bitis])
            ->when($bolumId, fn($q) => $q->whereHas('user', fn($qu) => $qu->where('bolum_id', $bolumId)))
            ->with(['user', 'behavior'])
            ->orderBy('karar_tarihi', 'desc')
            ->take(8)
            ->get();

        // Toplantı Geçmişi (Tamamlananlar)
        $toplantıGecmisi = DisiplinKuruluToplanti::where('durum', 'tamamlandı')
            ->where('baslangic_tarihi', '<=', $bitis . ' 23:59:59') // Bitiş filtresine kadar olan tamamlanmışlar
            ->when($bolumId, function($q) use ($bolumId) {
                return $q->whereHas('katilimcilar.user', fn($qu) => $qu->where('bolum_id', $bolumId));
            })
            ->with(['olusturan', 'katilimcilar', 'disiplinDosyalari.user'])
            ->orderBy('baslangic_tarihi', 'desc')
            ->get();

        // [YENİ] TÜM GÜNDEM VE TOPLANTILARIN ENTEGRASYONU
        // Hem kuruldaki dosyaları hem de tüm toplantıları (geçmiş/gelecek) birleştiriyoruz
        $gundemItems = collect();

        // 1. Kuruldaki Dosyalar (Toplantı formatına çevirerek ekliyoruz)
        foreach ($kuruldakiDosyalar as $case) {
            $gundemItems->push((object)[
                'id' => 'case_' . $case->id,
                'original_id' => $case->id,
                'type' => 'case',
                'baslik' => $case->user->name . ' - Kurul Gündemi',
                'baslangic_tarihi' => $case->created_at,
                'tur' => 'DİSİPLİN DOSYASI',
                'durum' => 'gündem_bekliyor',
                'yer' => 'Toplantı Planlanmadı',
                'olusturan' => null, // Dosyayı oluşturan yerine sistem veya bölüm lideri gelebilir
                'katilimcilar' => collect(), // Henüz katılımcı yok
                'disiplinDosyalari' => collect([$case]),
                'icerik' => $case->behavior->tanim ?? ''
            ]);
        }

        // 2. Yaklaşan Toplantılar
        foreach ($yaklasanToplantılar as $toplanti) {
            $gundemItems->push((object)[
                'id' => 'meeting_' . $toplanti->id,
                'original_id' => $toplanti->id,
                'type' => 'meeting',
                'baslik' => $toplanti->baslik,
                'baslangic_tarihi' => $toplanti->baslangic_tarihi,
                'tur' => $toplanti->tur,
                'durum' => $toplanti->durum,
                'yer' => $toplanti->yer,
                'olusturan' => $toplanti->olusturan,
                'katilimcilar' => $toplanti->katilimcilar,
                'disiplinDosyalari' => $toplanti->disiplinDosyalari,
                'icerik' => $toplanti->icerik
            ]);
        }

        // 3. Geçmiş Toplantılar
        foreach ($toplantıGecmisi as $toplanti) {
            $gundemItems->push((object)[
                'id' => 'meeting_' . $toplanti->id,
                'original_id' => $toplanti->id,
                'type' => 'meeting',
                'baslik' => $toplanti->baslik,
                'baslangic_tarihi' => $toplanti->baslangic_tarihi,
                'tur' => $toplanti->tur,
                'durum' => $toplanti->durum,
                'yer' => $toplanti->yer,
                'olusturan' => $toplanti->olusturan,
                'katilimcilar' => $toplanti->katilimcilar,
                'disiplinDosyalari' => $toplanti->disiplinDosyalari,
                'icerik' => $toplanti->icerik
            ]);
        }

        // Tarihe göre azalan sırada diz (En yeni en üstte)
        $gundemItems = $gundemItems->sortByDesc('baslangic_tarihi');

        // İSTATİSTİKLER (Kartlar için)
        $planlananToplantiSayisi = $yaklasanToplantılar->count();
        $tamamlananToplantiSayisi = $toplantıGecmisi->count();
        $bekleyenGundemSayisi   = $kuruldakiDosyalar->count();

        // Üyelik geçmişi (tüm zamanlar)
        $uyelikGecmisi = DisiplinKuruluUyelik::with(['user', 'ekleyen', 'cikaran'])
            ->orderBy('katilim_tarihi', 'desc')
            ->get();

        // Bölümler (filtre için)
        $bolumler = \App\Models\Bolum::orderBy('ad')->get();

        // Sistem Personeli (Davet için - Kurul ve Yönetim dışı personel)
        $tumPersonel = User::personel()
            ->whereNotIn('id', $tumUyeler->pluck('id'))
            ->whereDoesntHave('roles', function($q) {
                $q->where('name', 'Yonetim');
            })
            ->with('bolum')
            ->orderBy('name')
            ->get();

        $activeTab = $request->input('activeTab', 'overview');
        $uyeYonetimiYetkisi = $this->uyeYonetimiYetkisi();

        \Log::info('[DİSİPLİN KURULU INDEX]', [
            'user_id' => Auth::id(),
            'activeTab_param' => $request->input('activeTab'),
            'resolved_activeTab' => $activeTab,
            'yetki_durumu' => $uyeYonetimiYetkisi
        ]);

        return view('admin.disiplin.kurul', compact(
            'tumUyeler', 'uyeStats', 'baskanlar', 'uyeler',
            'toplamToplanti', 'toplamDosya', 'kararVerilenDosya',
            'ortalamKatilim', 'uyeYonetimiYetkisi',
            'yaklasanToplantılar', 'sonKararlar', 'toplantıGecmisi',
            'uyelikGecmisi', 'bolumler', 'kuruldakiDosyalar', 'secilebilirDosyalar',
            'baslangic', 'bitis', 'bolumId', 'tumPersonel',
            'planlananToplantiSayisi', 'tamamlananToplantiSayisi', 'bekleyenGundemSayisi',
            'activeTab', 'arsivdekiDosyalar', 'gundemItems'
        ));
    }

    // =========================================================
    // ÜYE EKLEME
    // =========================================================
    public function storeMember(Request $request)
    {
        abort_unless($this->uyeYonetimiYetkisi(), 403);

        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'rol'           => 'required|in:baskan,uye',
            'katilim_tarihi' => 'required|date',
            'notlar'        => 'nullable|string|max:500',
        ]);

        $hedefUser = User::findOrFail($request->user_id);

        // KRİTİK GÜVENLİK: Kendi bölümünde mi?
        $currentUser = Auth::user();
        if ($currentUser->hasRole('Hukuk Yöneticisi') && !$currentUser->hasRole('Superadmin')) {
            // Hukuk Yöneticisi sadece kendi bölümündekileri atayabilir
            if ($hedefUser->bolum_id !== $currentUser->bolum_id) {
                return back()->with('error', 'Hukuk Yöneticisi olarak sadece kendi bölümünüzdeki personeli kurula atayabilirsiniz.');
            }
        }

        $rolAdi    = $request->rol === 'baskan' ? 'Disiplin Kurulu Başkanı' : 'Disiplin Kurulu Üyesi';

        DB::transaction(function () use ($hedefUser, $rolAdi, $request) {
            // Rolü ata (Spatie)
            $hedefUser->assignRole($rolAdi);

            // Üyelik kaydı oluştur
            DisiplinKuruluUyelik::create([
                'user_id'        => $hedefUser->id,
                'rol'            => $request->rol,
                'katilim_tarihi' => $request->katilim_tarihi,
                'ekleyen_user_id' => Auth::id(),
                'notlar'         => $request->notlar,
                'aktif'          => true,
            ]);

            // Bildirim gönder (yeni üyeye)
            $hedefUser->notify(new DisiplinKuruluUyelikNotification(
                'eklendi', $rolAdi, Auth::user()->name, $request->notlar
            ));

            // DİĞER KURUL ÜYELERİNE VE İDARİ PAYDAŞLARA BİLDİRİM GÖNDER
            $paydaslar = $this->getKurulStakeholders($hedefUser->id);

            foreach ($paydaslar as $paydas) {
                try {
                    $paydas->notify(new DisiplinKuruluUyelikNotification(
                        'eklendi_diger',
                        $rolAdi, 
                        Auth::user()->name, 
                        "{$hedefUser->name} kurula yeni üye olarak katıldı."
                    ));
                } catch (\Exception $e) {
                    \Log::error("Bildirim gönderilemedi (Uye Ekleme Diger): " . $e->getMessage());
                }
            }

            // Superadmin log
            Log::channel('single')->info('[DİSİPLİN KURULU ÜYE EKLEME]', [
                'ekleyen'       => Auth::user()->name . ' (ID: ' . Auth::id() . ')',
                'eklenen_kullanici' => $hedefUser->name . ' (ID: ' . $hedefUser->id . ')',
                'rol'           => $rolAdi,
                'katilim_tarihi' => $request->katilim_tarihi,
                'ip'            => request()->ip(),
                'zaman'         => now()->format('Y-m-d H:i:s'),
            ]);
        });

        return redirect()->route('admin.disiplin.kurul.index')
            ->with('success', "{$hedefUser->name} başarıyla {$rolAdi} olarak eklendi ve bildirim gönderildi.");
    }

    // =========================================================
    // ÜYE ÇIKARMA
    // =========================================================
    public function removeMember(Request $request, User $user)
    {
        \Log::info('[DİSİPLİN KURULU ÜYE ÇIKARMA İSTEĞİ]', [
            'istek_yapan' => Auth::user()->name . ' (ID: ' . Auth::id() . ')',
            'cikarilacak_uye' => $user->name . ' (ID: ' . $user->id . ')',
            'referer' => $request->header('referer')
        ]);

        abort_unless($this->uyeYonetimiYetkisi(), 403);

        // KRİTİK GÜVENLİK: Kendi bölümünde mi? (Çıkarma için de geçerli)
        $currentUser = Auth::user();
        if ($currentUser->hasRole('Hukuk Yöneticisi') && !$currentUser->hasRole('Superadmin')) {
            if ($user->bolum_id !== $currentUser->bolum_id) {
                return back()->with('error', 'Hukuk Yöneticisi olarak sadece kendi bölümünüzdeki personeli kuruldan çıkarabilirsiniz.');
            }
        }

        $request->validate([
            'notlar' => 'nullable|string|max:500',
        ]);

        $roller = ['Disiplin Kurulu Başkanı', 'Disiplin Kurulu Üyesi'];

        DB::transaction(function () use ($user, $roller, $request) {
            // İlgili tüm rolleri temizle
            foreach ($roller as $rol) {
                if ($user->hasRole($rol)) {
                    $user->removeRole($rol);
                }
            }

            // Aktif üyelik kaydını kapat
            DisiplinKuruluUyelik::where('user_id', $user->id)
                ->where('aktif', true)
                ->update([
                    'aktif'          => false,
                    'ayrilma_tarihi' => now()->toDateString(),
                    'cikaran_user_id' => Auth::id(),
                    'notlar'         => $request->notlar,
                ]);

            // Kendisine bildirim gönder
            try {
                $user->notify(new DisiplinKuruluUyelikNotification(
                    'cikarildi', 'Kurul Üyeliği', Auth::user()->name, $request->notlar
                ));
            } catch (\Exception $e) {
                \Log::error("Bildirim gönderilemedi (Uye Cikarildi-Kendi): " . $e->getMessage());
            }

            // DİĞER PAYDAŞLARA BİLDİRİM GÖNDER
            $paydaslar = $this->getKurulStakeholders($user->id);
            foreach ($paydaslar as $paydas) {
                try {
                    $paydas->notify(new DisiplinKuruluUyelikNotification(
                        'cikarildi_diger',
                        'Kurul Üyeliği',
                        Auth::user()->name,
                        "{$user->name} kurul üyeliğinden ayrıldı."
                    ));
                } catch (\Exception $e) {
                    \Log::error("Bildirim gönderilemedi (Uye Cikarildi-Diger): " . $e->getMessage());
                }
            }

            // Superadmin log
            Log::channel('single')->info('[DİSİPLİN KURULU ÜYE ÇIKARMA]', [
                'cikaran'       => Auth::user()->name . ' (ID: ' . Auth::id() . ')',
                'cikarilanKullanici' => $user->name . ' (ID: ' . $user->id . ')',
                'ayrilma_tarihi' => now()->toDateString(),
                'ip'            => request()->ip(),
                'zaman'         => now()->format('Y-m-d H:i:s'),
            ]);
        });

        return redirect()->route('admin.disiplin.kurul.index', ['activeTab' => 'members'])
            ->with('success', "{$user->name} Disiplin Kurulundan çıkarıldı ve bildirim gönderildi.");
    }

    // =========================================================
    // TOPLANTI OLUŞTUR
    // =========================================================
    public function storeToplanti(Request $request)
    {
        $user = Auth::user();
        
        // Yetki kontrolü: Toplantı Yönetimi izni olmalı
        $canPlan = $user->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']) || 
                   ($user->hasRole('Hukuk Yöneticisi') && $user->can('disiplin.kurul.toplanti.yonet'));

        if (!$canPlan) {
            abort(403, 'Toplantı planlama yetkiniz bulunmuyor.');
        }

        if ($user->hasRole('Yonetim')) {
            abort(403, 'Yönetim rolü toplantı planlayamaz.');
        }

        $request->validate([
            'baslik'           => 'required|string|max:255',
            'tur'              => 'required|in:olağanüstü,olağan,uye_degisimi,karar_oturumu,diger',
            'baslangic_tarihi' => 'required|date',
            'planlanan_sure_dk'=> 'required|integer|min:1',
            'yer'              => 'nullable|string|max:255',
            'icerik'           => 'nullable|string',
            'katilimcilar'     => 'nullable|array',
            'sistem_katilimcilari' => 'nullable|array',
            'dis_katilimci_adi'   => 'nullable|string|max:255',
            'dis_katilimci_email' => 'nullable|email',
            'disiplin_dosyalari'  => 'nullable|array',
            'disiplin_dosyalari.*'=> 'exists:disciplinary_cases,id',
        ]);

        DB::transaction(function () use ($request) {
            $toplanti = DisiplinKuruluToplanti::create([
                'baslik'           => $request->baslik,
                'icerik'           => $request->icerik,
                'tur'              => $request->tur,
                'baslangic_tarihi' => $request->baslangic_tarihi,
                'yer'              => $request->yer,
                'durum'            => 'planlandı',
                'planlanan_sure_dk' => $request->planlanan_sure_dk ?? 60,
                'olusturan_user_id' => Auth::id(),
            ]);

            if ($request->disiplin_dosyalari) {
                $toplanti->disiplinDosyalari()->attach($request->disiplin_dosyalari);
            }

            $notifiedUserIds = [];

            $katilimciIdleri = array_unique(array_merge(
                $request->katilimcilar ?? [],
                $request->sistem_katilimcilari ?? []
            ));

            foreach ($katilimciIdleri as $userId) {
                DisiplinKuruluToplantiKatilimci::updateOrCreate(
                    [
                        'toplanti_id'    => $toplanti->id,
                        'user_id'        => $userId,
                    ],
                    [
                        'rol'            => 'katilimci',
                        'katilim_durumu' => 'bekleniyor',
                        'davet_gonderildi_at' => now(),
                    ]
                );

                $notifiedUserIds[] = $userId;
                $user = User::find($userId);
                if ($user) {
                    try {
                        $user->notify(new DisiplinToplantiDavetNotification($toplanti, Auth::user()->name));
                    } catch (\Exception $e) {
                        \Log::error("Disiplin Toplantı Daveti Hatası: " . $e->getMessage());
                    }
                }
            }

            // Dış katılımcı
            if ($request->dis_katilimci_adi) {
                DisiplinKuruluToplantiKatilimci::create([
                    'toplanti_id'         => $toplanti->id,
                    'dis_katilimci_adi'   => $request->dis_katilimci_adi,
                    'dis_katilimci_email' => $request->dis_katilimci_email,
                    'rol'                 => 'davetli',
                    'katilim_durumu'      => 'bekleniyor',
                    'davet_gonderildi_at' => now(),
                ]);

                if ($request->dis_katilimci_email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($request->dis_katilimci_email)
                            ->send(new \App\Mail\DisiplinToplantiDavetMail($toplanti));
                    } catch (\Exception $e) {
                        \Log::error("Disiplin Toplantı Mail Hatası (Dış): " . $e->getMessage());
                    }
                }
            }

            // İDARİ PAYDAŞLARA BİLDİRİM GÖNDER (Hukuk Admin vb.)
            $paydaslar = $this->getKurulStakeholders();
            foreach ($paydaslar as $paydas) {
                // Eğer zaten katılımcı olarak bildirim aldıysa tekrar gönderme
                if (in_array($paydas->id, $notifiedUserIds)) {
                    continue;
                }

                try {
                    $paydas->notify(new DisiplinKuruluToplantiNotification('planlandi', $toplanti, Auth::user()->name));
                } catch (\Exception $e) {
                    \Log::error("Bildirim gönderilemedi (Toplanti Planlandi-Paydas): " . $e->getMessage());
                }
            }

            $toplanti->update(['davet_maili_gonderildi' => true]);
        });

        return redirect()->route('admin.disiplin.kurul.index')
            ->with('success', 'Toplantı planlandı ve tüm katılımcılara (kurul, sistem ve dış) bildirim gönderildi.');
    }

    public function destroyToplanti(DisiplinKuruluToplanti $toplanti)
    {
        $user = Auth::user();
        $isAuthorized = ($toplanti->olusturan_user_id == $user->id) || 
                        $user->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']);

        if (!$isAuthorized) {
            return back()->with('error', 'Bu toplantıyı silme yetkiniz bulunmuyor.');
        }

        // İPTAL BİLDİRİMİ GÖNDER (Silinmeden önce yakalıyoruz)
        $paydaslar = $this->getKurulStakeholders();
        foreach ($paydaslar as $paydas) {
            try {
                $paydas->notify(new DisiplinKuruluToplantiNotification('iptal', $toplanti, Auth::user()->name));
            } catch (\Exception $e) {
                \Log::error("Bildirim gönderilemedi (Toplanti Iptal-Paydas): " . $e->getMessage());
            }
        }

        $toplanti->delete();
        return back()->with('success', 'Toplantı başarıyla silindi.');
    }

    // =========================================================
    // TOPLANTI DETAY
    // =========================================================
    public function showToplanti(DisiplinKuruluToplanti $toplanti)
    {
        $this->yetkiKontrol();
        $toplanti->load(['olusturan', 'katilimcilar.user', 'disiplinDosyalari.user', 'disiplinDosyalari.behavior']);
        return view('admin.disiplin.kurul-toplanti', compact('toplanti'));
    }

    public function updateToplanti(Request $request, DisiplinKuruluToplanti $toplanti)
    {
        $user = Auth::user();
        
        // Temel yetki kontrolü
        $isAuthorized = ($toplanti->olusturan_user_id == $user->id) || 
                        $user->hasRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']);

        if (!$isAuthorized) {
            return back()->with('error', 'Bu toplantıyı düzenleme yetkiniz bulunmuyor.');
        }

        // TAMAMLANDI kontrolü: Sadece Superadmin tamamlanmış toplantıyı düzenleyebilir
        if ($toplanti->durum === 'tamamlandı' && !$user->hasRole('Superadmin')) {
            return back()->with('error', 'Tamamlanmış toplantılar sadece Superadmin tarafından düzenlenebilir.');
        }

        $request->validate([
            'baslik'           => 'required|string|max:255',
            'tur'              => 'required|in:olağanüstü,olağan,uye_degisimi,karar_oturumu,diger',
            'baslangic_tarihi' => 'required|date',
            'planlanan_sure_dk'=> 'required|integer|min:1',
            'yer'              => 'nullable|string',
            'disiplin_dosyalari'  => 'nullable|array',
            'disiplin_dosyalari.*'=> 'exists:disciplinary_cases,id',
            'katilimcilar'     => 'nullable|array',
            'sistem_katilimcilari' => 'nullable|array',
            'dis_katilimci_adi'   => 'nullable|string|max:255',
            'dis_katilimci_email' => 'nullable|email',
        ]);

        // Revizyon kontrolü için eski verileri sakla
        $oldData = [
            'baslik' => $toplanti->baslik,
            'baslangic_tarihi' => $toplanti->baslangic_tarihi->toDateTimeString(),
            'yer' => $toplanti->yer,
        ];

        DB::transaction(function () use ($request, $toplanti, $oldData) {
            $toplanti->update([
                'baslik' => $request->baslik,
                'tur' => $request->tur,
                'baslangic_tarihi' => $request->baslangic_tarihi,
                'planlanan_sure_dk' => $request->planlanan_sure_dk,
                'yer' => $request->yer,
                'icerik' => $request->icerik,
            ]);

            // Disiplin dosyalarını senkronize et
            $toplanti->disiplinDosyalari()->sync($request->disiplin_dosyalari ?? []);

            // Katılımcıları güncelle
            $yeniKatilimciIdleri = array_unique(array_merge(
                $request->katilimcilar ?? [],
                $request->sistem_katilimcilari ?? []
            ));

            // Katılımcıları güncelle (Veri kaybını önlemek için: Mevcut olanları koru, çıkarılanları sil, yeni gelenleri ekle)
            $mevcutKatilimcilar = $toplanti->katilimcilar()->whereNotNull('user_id')->get();
            $mevcutUserIds = $mevcutKatilimcilar->pluck('user_id')->toArray();

            // Çıkarılanları sil
            $silinecekUserIds = array_diff($mevcutUserIds, $yeniKatilimciIdleri);
            if (!empty($silinecekUserIds)) {
                $toplanti->katilimcilar()->whereIn('user_id', $silinecekUserIds)->delete();
            }

            // Yeni eklenenleri oluştur
            $eklenecekUserIds = array_diff($yeniKatilimciIdleri, $mevcutUserIds);
            foreach ($eklenecekUserIds as $userId) {
                \App\Models\DisiplinKuruluToplantiKatilimci::create([
                    'toplanti_id'    => $toplanti->id,
                    'user_id'        => $userId,
                    'rol'            => 'katilimci',
                    'katilim_durumu' => 'bekleniyor',
                ]);
            }

            // Dış katılımcı güncelleme (basit yaklaşım: varsa güncelle yoksa oluştur)
            if ($request->dis_katilimci_adi) {
                $toplanti->katilimcilar()->updateOrCreate(
                    ['rol' => 'davetli'],
                    [
                        'dis_katilimci_adi' => $request->dis_katilimci_adi,
                        'dis_katilimci_email' => $request->dis_katilimci_email,
                        'katilim_durumu' => 'bekleniyor'
                    ]
                );
            } else {
                $toplanti->katilimcilar()->where('rol', 'davetli')->delete();
            }

            // REVİZYON BİLDİRİMİ (Sadece Kritik Alanlar Değiştiyse)
            $newData = [
                'baslik' => $toplanti->baslik,
                'baslangic_tarihi' => \Carbon\Carbon::parse($toplanti->baslangic_tarihi)->toDateTimeString(),
                'yer' => $toplanti->yer,
            ];

            $changed = ($oldData['baslik'] !== $newData['baslik']) || 
                       ($oldData['baslangic_tarihi'] !== $newData['baslangic_tarihi']) || 
                       ($oldData['yer'] !== $newData['yer']);
            
            // Tarih karşılaştırması için Carbon kullanmak daha sağlıklı
            $tarihDegisti = !\Carbon\Carbon::parse($oldData['baslangic_tarihi'])->equalTo(\Carbon\Carbon::parse($newData['baslangic_tarihi']));
            
            if ($oldData['baslik'] !== $newData['baslik'] || $tarihDegisti || $oldData['yer'] !== $newData['yer']) {
                $revizyonData = [
                    'baslik' => $newData['baslik'],
                    'baslangic_tarihi' => $newData['baslangic_tarihi'],
                    'yer' => $newData['yer'],
                ];

                // Tüm katılımcılara bildirim gönder
                $tumKatilimcilar = $toplanti->katilimcilar()->get();
                foreach ($tumKatilimcilar as $katilimci) {
                    if ($katilimci->user) {
                        try {
                            $katilimci->user->notify(new DisiplinToplantiRevizyonNotification($toplanti, $oldData, $newData, Auth::user()->name));
                        } catch (\Exception $e) {
                            \Log::error("Revizyon Bildirim Hatası: " . $e->getMessage());
                        }
                    } elseif ($katilimci->dis_katilimci_email) {
                        // Dış katılımcı maili (isteğe bağlı eklenebilir, şu an notify sadece User için)
                    }
                }

                // İdari paydaşlara da genel bildirim (DisiplinKuruluToplantiNotification 'guncellendi' olarak)
                $paydaslar = $this->getKurulStakeholders();
                foreach ($paydaslar as $paydas) {
                    try {
                        $paydas->notify(new DisiplinKuruluToplantiNotification('guncellendi', $toplanti, Auth::user()->name));
                    } catch (\Exception $e) {
                        \Log::error("Bildirim gönderilemedi (Toplanti Revizyon-Paydas): " . $e->getMessage());
                    }
                }
            }
        });

        return back()->with('success', 'Toplantı bilgileri ve katılımcılar başarıyla güncellendi.');
    }

    // =========================================================
    // TOPLANTI DURUMU GÜNCELLE
    // =========================================================
    public function updateToplantiDurum(Request $request, DisiplinKuruluToplanti $toplanti)
    {
        $this->yetkiKontrol();
        
        $user = Auth::user();
        if ($toplanti->durum === 'tamamlandı' && !$user->hasRole('Superadmin')) {
            return back()->with('error', 'Tamamlanmış toplantıların durumu sadece Superadmin tarafından değiştirilebilir.');
        }

        $request->validate(['durum' => 'required|in:planlandı,devam_ediyor,tamamlandı,iptal']);

        $toplanti->update([
            'durum'          => $request->durum,
            'toplanti_notu'  => $request->toplanti_notu,
        ]);

        // --- BİLDİRİM: Toplantı Başlatıldıysa İdari Paydaşlara Haber Ver ---
        if ($request->durum === 'devam_ediyor') {
            try {
                $stakeholders = User::role(['Hukuk Admini', 'Hukuk Yöneticisi'])->get()->filter(function($u) {
                    if ($u->id === Auth::id()) return false;
                    if ($u->hasRole('Hukuk Yöneticisi') && !$u->can('disiplin.kurul.portal.gor')) return false;
                    return true;
                });

                foreach ($stakeholders as $stakeholder) {
                    $stakeholder->notify(new \App\Notifications\DisiplinKuruluToplantiNotification('baslatildi', $toplanti, Auth::user()->name));
                }
            } catch (\Exception $e) {
                \Log::error('Toplantı başlatma bildirimi hatası: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Toplantı durumu güncellendi.');
    }
    public function resendMails(DisiplinKuruluToplanti $toplanti)
    {
        // Yetki Kontrolü: Superadmin, Hukuk Admini, Disiplin Kurulu Başkanı
        if (!Auth::user()->hasAnyRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı'])) {
            return back()->with('error', 'Bu işlemi yapmaya yetkiniz yok.');
        }

        $notifiedUserIds = [];

        // Katılımcıları (Kurul ve Sistem) tekrar bilgilendir
        foreach ($toplanti->katilimcilar as $katilimci) {
            if ($katilimci->user_id) {
                $notifiedUserIds[] = $katilimci->user_id;
                try {
                    if ($katilimci->user) {
                        $katilimci->user->notify(new \App\Notifications\DisiplinToplantiDavetNotification($toplanti, Auth::user()->name));
                    }
                } catch (\Exception $e) {
                    \Log::error("Manuel Mail Hatası (Sistem): " . $e->getMessage());
                }
            } elseif ($katilimci->dis_katilimci_email) {
                // Dış katılımcı maili
                try {
                    \Illuminate\Support\Facades\Mail::to($katilimci->dis_katilimci_email)
                        ->send(new \App\Mail\DisiplinToplantiDavetMail($toplanti));
                } catch (\Exception $e) {
                    \Log::error("Manuel Mail Hatası (Dış): " . $e->getMessage());
                }
            }
        }

        // Paydaşları bilgilendir (Henüz almamış olanlar)
        $paydaslar = $this->getKurulStakeholders();
        foreach ($paydaslar as $paydas) {
            if (in_array($paydas->id, $notifiedUserIds)) continue;

            try {
                $paydas->notify(new \App\Notifications\DisiplinKuruluToplantiNotification('planlandi', $toplanti, Auth::user()->name));
            } catch (\Exception $e) {
                \Log::error("Manuel Mail Hatası (Paydaş): " . $e->getMessage());
            }
        }

        $toplanti->update(['davet_maili_gonderildi' => true]);

        return back()->with('success', 'Davet mailleri tüm katılımcılara tekrar gönderildi.');
    }
}
