<?php
namespace App\Http\Controllers;

use App\Models\Takim;
use App\Models\User;
use App\Models\TakimDavetiyesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\IaaLog;
use App\Traits\NotifiesManager;
use App\Notifications\TakimKatilmaIstegi;
use App\Notifications\TakimaDavetEdildi;  // Üyeye giden
use App\Notifications\TakimDavetiYanitlandi; // Lidere ve Yöneticisine giden

class TakimController extends Controller
{
    use NotifiesManager; // Add this line here
    public function index()
    {
        $user = Auth::user();

        // 1. KULLANICININ ÜYE OLDUĞU TAKIMLAR (Detaylı Veri - İyileştirildi)
        $katildigimTakimlar = $user->takimlar()
            ->with('lider')
            ->withCount(['uyeler', 'atananProjeler']) // Yeni kartlar için proje sayısı lazım
            ->latest('pivot_created_at')
            ->get();

        $katildigimTakimIdleri = $katildigimTakimlar->pluck('id');

        // 2. DAVET VE İSTEKLER (Aynen Korundu)
        $gonderdigimIstekler = TakimDavetiyesi::where('type', 'istek')
            ->where('davet_eden_user_id', $user->id)->where('durum', 'bekliyor')->with('takim.lider')->latest()->get();
        $istekGonderilenTakimIdleri = $gonderdigimIstekler->pluck('takim_id');

        $gelenDavetler = TakimDavetiyesi::where('type', 'davet')
            ->where('davet_edilen_user_id', $user->id)->where('durum', 'bekliyor')->get();

        // 3. DİĞER TAKIMLAR (Filtreli - Aynen Korundu)
        $digerTakimlar = Takim::whereNotIn('id', $katildigimTakimIdleri->merge($istekGonderilenTakimIdleri))
            ->where('tur', '!=', 'sikayet')
            ->with('lider')->withCount('uyeler')->latest()->get();

        // =============================================================
        // === YENİ ÖZELLİKLER İÇİN EKLENEN VERİLER ===
        // =============================================================

        // A) Aktif Projeler (Tarih verisiyle)
        $atanmisProjeler = \App\Models\Iaa::whereIn('atanan_takim_id', $katildigimTakimIdleri)
            ->whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Çalışılıyor'])
            ->with(['atananTakim', 'musteriSikayeti'])
            ->latest('updated_at')
            ->get();

        // B) Onay Bekleyenler (Loglardan tarih çekmek için)
        $onayBekleyenTamamlanmisProjeler = \App\Models\Iaa::whereIn('atanan_takim_id', $katildigimTakimIdleri)
            ->whereIn('durum', ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])
            ->with([
                'atananTakim',
                'musteriSikayeti',
                'logs' => function ($q) {
                    $q->whereIn('eylem', ['Proje Tamamlandı (İadesiz)', 'İade Girildi/Güncellendi', 'Revizyon Talep Edildi'])->latest();
                }
            ])
            ->get();

        // C) Tamamlananlar
        $tamamlananProjeler = \App\Models\Iaa::whereIn('atanan_takim_id', $katildigimTakimIdleri)
            ->where('durum', 'Tamamlandı')
            ->with(['atananTakim', 'musteriSikayeti'])
            ->latest('onaylanma_tarihi')
            ->take(10)
            ->get();

        // D) Kişisel Görevler (Öneri)
        $banaAtananAdimlar = DB::table('iaa_step_assignments')
            ->join('iaas', 'iaa_step_assignments.iaa_id', '=', 'iaas.id')
            ->join('iaa_workflow_steps', 'iaa_step_assignments.iaa_workflow_step_id', '=', 'iaa_workflow_steps.id')
            ->where('iaa_step_assignments.user_id', $user->id)
            ->where('iaas.durum', '!=', 'Tamamlandı')
            ->select('iaas.id as iaa_id', 'iaas.baslik', 'iaa_workflow_steps.name as adim_adi', 'iaa_step_assignments.created_at as atama_tarihi')
            ->get();

        // E) İstatistikler
        $stats = [
            'aktif' => $atanmisProjeler->count(),
            'talep' => DB::table('iaa_talepleri')->whereIn('takim_id', $katildigimTakimIdleri)->where('durum', 'beklemede')->count(),
            'onay_bekleyen_tamamlanmis' => $onayBekleyenTamamlanmisProjeler->count(),
            'tamamlanan' => \App\Models\Iaa::whereIn('atanan_takim_id', $katildigimTakimIdleri)->where('durum', 'Tamamlandı')->count(),
            'toplam_puan' => $user->toplam_puan
        ];

        return view('takimlar.index', compact(
            'katildigimTakimlar',
            'gonderdigimIstekler',
            'digerTakimlar',
            'gelenDavetler',
            'atanmisProjeler',
            'onayBekleyenTamamlanmisProjeler',
            'tamamlananProjeler',
            'stats',
            'banaAtananAdimlar'
        ));
    }

    public function create()
    {
        return view('takimlar.create');
    }

    /**
     * Yeni bir takımı veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:takimlar',
            'amac' => 'nullable|string',
            'vizyon' => 'nullable|string',
            'misyon' => 'nullable|string',
            'kurallar' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $takim = Takim::create([
                'ad' => $validated['ad'],
                'lider_user_id' => Auth::id(),
                'amac' => $validated['amac'],
                'vizyon' => $validated['vizyon'],
                'misyon' => $validated['misyon'],
                'kurallar' => $validated['kurallar'],
            ]);
            $takim->uyeler()->attach(Auth::id(), ['katilma_sekli' => 'Kurucu Lider']);
        });

        return redirect()->route('takimlar.index')->with('success', 'Takım başarıyla oluşturuldu!');
    }



    public function show(Takim $takim)
    {
        $user = Auth::user();

        // YETKİ KONTROLÜ
        $canAccess = false;

        // 1. Üst Roller
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            $canAccess = true;
        }

        // 2. Takım Üyeliği
        if (!$canAccess && $takim->uyeler->contains('id', $user->id)) {
            $canAccess = true;
        }

        // 3. Bölüm Lideri Kontrolü (Personeli bu takımda mı veya davetli mi?)
        if (!$canAccess && $user->hasRole('Bölüm Lideri')) {
            // A) Kendi bölümünden birisi bu takımda üye mi?
            $hasPersonnelInTeam = $takim->uyeler()->where('bolum_id', $user->bolum_id)->exists();

            if ($hasPersonnelInTeam) {
                $canAccess = true;
            } else {
                // B) Kendi bölümünden birisine bu takım için bekleyen davet var mı?
                $hasPersonnelInvited = TakimDavetiyesi::where('takim_id', $takim->id)
                    ->where('durum', 'bekliyor')
                    ->whereHas('davetEdilen', function ($q) use ($user) {
                        $q->where('bolum_id', $user->bolum_id);
                    })->exists();

                if ($hasPersonnelInvited) {
                    $canAccess = true;
                }
            }
        }

        if (!$canAccess) {
            abort(403, 'Bu takımı görüntüleme yetkiniz yok.');
        }

        $takim->load('uyeler.bolum', 'lider');

        // === FİLTRELEME BAŞLANGICI ===

        // 1. Listeden Gizlenecek Roller
        $gizlenecekRoller = [
            'Superadmin',
            'Yonetim',
            'Dış Avukat',
            'Müşteri',
            'Arabuluculuk Finans',
            'Hukuk Yöneticisi',
            'Hukuk Admini',
            'Disiplin Kurulu Başkanı'
        ];

        // 2. Takımdaki mevcut üyelerin ID'lerini al
        $mevcutUyeIds = $takim->uyeler->pluck('id')->toArray();

        // 3. Potansiyel Üyeleri Filtreli Getir
        $potansiyelUyeler = \App\Models\User::where('onaylandi_mi', true)
            ->where('id', '!=', auth()->id()) // Kendini listede görme

            // A) Sadece Personel Olanlar (Müşterileri Gizle)
            ->where('is_personnel', true)

            // B) Zaten takımda olanları gizle
            ->whereNotIn('id', $mevcutUyeIds)

            // C) Yasaklı rollere sahip olanları gizle
            ->whereDoesntHave('roles', function ($q) use ($gizlenecekRoller) {
                $q->whereIn('name', $gizlenecekRoller);
            })

            ->orderBy('name')
            ->get();

        // === FİLTRELEME BİTİŞİ ===

        $gonderilenDavetler = TakimDavetiyesi::where('takim_id', $takim->id)->where('type', 'davet')->where('durum', 'bekliyor')->with('davetEdilen.bolum')->get();
        $gelenIstekler = TakimDavetiyesi::where('takim_id', $takim->id)->where('type', 'istek')->where('durum', 'bekliyor')->with('davetEden.bolum')->get();

        // Bekleyen talepler
        $bekleyenTalepler = DB::table('iaa_talepleri')
            ->join('iaas', 'iaa_talepleri.iaa_id', '=', 'iaas.id')
            ->where('iaa_talepleri.takim_id', $takim->id)
            ->where('iaa_talepleri.durum', 'beklemede')
            ->where('iaas.durum', 'Havuzda')
            ->select('iaas.id as iaa_id', 'iaas.baslik', 'iaas.puan', 'iaa_talepleri.created_at as talep_tarihi')
            ->latest('talep_tarihi')
            ->get();

        // Aktif Projeler
        $devamEdenProjeler = $takim->atananProjeler()
            ->whereIn('durum', ['Atandı', 'Revize Ediliyor'])
            ->with([
                'logs' => function ($query) {
                    $query->where('eylem', 'Revizyon Talep Edildi')
                        ->with('user')
                        ->latest('created_at');
                },
                'musteriSikayeti',
                'aktifAdim' // Bu ilişki view'de kullanılıyor, eklenmesi iyi olur
            ])
            ->get();

        $tamamlananProjeler = $takim->atananProjeler()->where('durum', 'Tamamlandı')->get();
        $onayBekleyenTamamlanmisProjeler = $takim->atananProjeler()->where('durum', 'Yönetici Onayı Bekliyor')->get();

        // İptal Edilen veya Reddedilen Projeler
        $iptalEdilenProjeler = $takim->atananProjeler()
            ->whereIn('durum', ['İptal Edildi', 'Reddedildi'])
            ->get();

        // TAKIM DAVETİ İÇİN KULLANICI LİSTESİ
        $uyelerIds = $takim->uyeler->pluck('id')->toArray();
        $uyelerIds[] = $takim->lider_user_id;

        $bostaPersoneller = \App\Models\User::where('is_personnel', true)
            ->whereNotIn('id', $uyelerIds)
            ->orderBy('name')
            ->get(['id', 'name', 'bolum_id']);

        return view('takimlar.show', compact(
            'takim',
            'devamEdenProjeler',
            'tamamlananProjeler',
            'gonderilenDavetler',
            'gelenIstekler',
            'bekleyenTalepler',
            'onayBekleyenTamamlanmisProjeler',
            'iptalEdilenProjeler',
            'bostaPersoneller'
        ));
    }

    public function davetGonder(Request $request, Takim $takim)
    {
        // 1. Yetki Kontrolü
        if (Auth::id() !== $takim->lider_user_id) {
            return back()->with('error', 'Bu işlemi yapma yetkiniz yok.');
        }

        // 2. Validasyonlar
        $request->validate(['user_id' => 'required|exists:users,id']);

        if ($takim->uyeler()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'Bu kullanıcı zaten takımda mevcut.');
        }

        if (TakimDavetiyesi::where('takim_id', $takim->id)->where('davet_edilen_user_id', $request->user_id)->where('durum', 'bekliyor')->exists()) {
            return back()->with('error', 'Bu kullanıcıya zaten bir davet gönderilmiş.');
        }

        // 3. Davet Kaydını Oluşturma (Değişkene atıyoruz: $davet)
        $davet = TakimDavetiyesi::create([
            'takim_id' => $takim->id,
            'davet_eden_user_id' => Auth::id(),
            'davet_edilen_user_id' => $request->user_id,
            'type' => 'davet',     // Tipi belirttik
            'durum' => 'bekliyor'  // Durumu belirttik
        ]);

        // 4. BİLDİRİM GÖNDERME (YENİ KISIM)
        // Mevcut kodunuzdaki 'notifyUserAndManager' yerine bunu kullanıyoruz.
        // Çünkü bu sınıf (/davetlerim) sayfasına yönlendiriyor.
        $davetEdilenKullanici = User::find($request->user_id);

        if ($davetEdilenKullanici) {
            if ($davetEdilenKullanici) {
                // 1. DAVET EDİLEN PERSONELE BİLDİRİM (Sadece ona gitmeli)
                // "Sinan sizi davet etti"
                $davetEdilenKullanici->notify(new TakimaDavetEdildi($davet));

                // 2. BÖLÜM LİDERİNE BİLDİRİM (Varsa)
                // "Personeliniz Furkan davet edildi"
                $this->notifyDepartmentLeader(
                    $davetEdilenKullanici,
                    new \App\Notifications\PersonelTakimBildirimi($davetEdilenKullanici, $takim, Auth::user(), 'davet')
                );
            }
        }

        return back()->with('success', 'Davet başarıyla gönderildi.');
    }



    /**
     * Takımdan üye çıkarır. (Sadece lider yapabilir)
     */
    public function uyeCikar(Takim $takim, User $user)
    {
        // Yetki Kontrolü: Sadece takım lideri üye çıkarabilir.
        if (Auth::id() !== $takim->lider_user_id) {
            return redirect()->route('takimlar.show', $takim)->with('error', 'Bu işlemi yapma yetkiniz yok.');
        }

        // Liderin kendisini takımdan çıkarmasını engelle
        if ($user->id === $takim->lider_user_id) {
            return redirect()->route('takimlar.show', $takim)->with('error', 'Takım lideri kendisini takımdan çıkaramaz.');
        }

        $takim->uyeler()->detach($user->id);

        // Zil Bildirimi Gönder (Hali Hazırda Personele Gidiyor)
        try {
            $user->notify(new \App\Notifications\TakimdanCikarildi($takim, Auth::user()));

            // Bölüm Liderine Bilgi Ver
            $this->notifyDepartmentLeader(
                $user,
                new \App\Notifications\PersonelTakimBildirimi($user, $takim, Auth::user(), 'cikarildi')
            );
        } catch (\Exception $e) {
            \Log::error('Takımdan çıkarma bildirimi hatası: ' . $e->getMessage());
        }

        return redirect()->route('takimlar.show', $takim)->with('success', 'Üye takımdan çıkarıldı.');
    }

    // 1. SADECE BANA GELEN DAVETLER (Biri beni takıma çağırıyorsa)
    public function davetlerim()
    {
        $davetler = TakimDavetiyesi::where('davet_edilen_user_id', Auth::id())
            ->where('type', 'davet') // <--- EKLEME: Sadece 'davet' olanlar
            ->where('durum', 'bekliyor')
            ->with('takim.lider')
            ->get();

        return view('takimlar.davetlerim', compact('davetler'));
    }

    // 2. TAKIMIMA GELEN KATILMA İSTEKLERİ (Ben Lidersem ve biri girmek istiyorsa)
    public function isteklerim()
    {
        // Ben lidersem, 'davet_edilen_user_id' benim ID'm olur ama type 'istek'tir.
        $istekler = TakimDavetiyesi::where('davet_edilen_user_id', Auth::id())
            ->where('type', 'istek') // <--- KRİTİK NOKTA: Sadece istekler
            ->where('durum', 'bekliyor')
            ->with(['davetEden', 'takim']) // İsteyen kişiyi çekiyoruz
            ->get();

        return view('takimlar.isteklerim', compact('istekler'));
    }

    public function davetiKabulEt(TakimDavetiyesi $davetiye)
    {
        if ($davetiye->davet_edilen_user_id !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($davetiye) {
            $davetiye->update(['durum' => 'kabul edildi']);

            // Kullanıcıyı takıma ekle
            $davetiye->takim->uyeler()->syncWithoutDetaching([
                $davetiye->davet_edilen_user_id => [
                    'katilma_sekli' => 'Davet ile Katıldı'
                ]
            ]);

            // 1. TAKIM SAHİBİNE (LİDERE) BİLDİRİM
            // "Furkan daveti kabul etti" (Sadece Takım Sahibine gitmeli)
            if ($davetiye->takim->lider) {
                $davetiye->takim->lider->notify(new TakimDavetiYanitlandi($davetiye, Auth::user()));
            }

            // 2. BÖLÜM LİDERİNE BİLDİRİM (Personelin Amirine)
            // "Personeliniz Furkan kabul etti"
            $kabulEdenUser = Auth::user();

            $this->notifyDepartmentLeader(
                $kabulEdenUser,
                new \App\Notifications\PersonelTakimBildirimi($kabulEdenUser, $davetiye->takim, $davetiye->davetEden, 'kabul')
            );
        });

        return redirect()->route('takimlar.davetlerim')->with('success', $davetiye->takim->ad . ' takımına katıldınız!');
    }

    public function davetiReddet(TakimDavetiyesi $davetiye)
    {
        if ($davetiye->davet_edilen_user_id !== Auth::id()) {
            abort(403);
        }
        $davetiye->update(['durum' => 'reddedildi']);
        return redirect()->route('takimlar.davetlerim')->with('success', 'Davet reddedildi.');
    }

    public function davetiIptalEt(TakimDavetiyesi $davetiye)
    {
        // 1. Yetki Kontrolü
        if (Auth::id() !== $davetiye->takim->lider_user_id) {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

        // 2. BİLDİRİM TEMİZLİĞİ (YENİ EKLENEN KISIM)
        // Davet edilen kullanıcıyı bul
        $davetEdilen = \App\Models\User::find($davetiye->davet_edilen_user_id);

        // Eğer kullanıcı varsa, ona giden ve bu davet ID'sini taşıyan bildirimi sil
        if ($davetEdilen) {
            $davetEdilen->notifications()
                ->where('data->davet_id', $davetiye->id)
                ->delete();
        }

        // 3. Daveti Sil
        $davetiye->delete();

        return back()->with('success', 'Davet başarıyla iptal edildi.');
    }


    /**
     * Takım bilgilerini düzenleme formunu gösterir.
     */
    public function edit(Takim $takim)
    {
        // Yetki Kontrolü: Sadece takım lideri takımı düzenleyebilir.
        if (Auth::id() !== $takim->lider_user_id) {
            abort(403, 'Bu takımı düzenleme yetkiniz yok.');
        }

        return view('takimlar.edit', compact('takim'));
    }

    /**
     * Takım bilgilerini günceller.
     */
    public function update(Request $request, Takim $takim)
    {
        // Yetki Kontrolü: Sadece takım lideri takımı güncelleyebilir.
        if (Auth::id() !== $takim->lider_user_id) {
            abort(403, 'Bu takımı düzenleme yetkiniz yok.');
        }

        $validated = $request->validate([
            // Takım adının benzersiz (unique) olması kuralını, güncellenen takımın kendisi hariç tutacak şekilde ayarlıyoruz.
            'ad' => 'required|string|max:255|unique:takimlar,ad,' . $takim->id,
            'amac' => 'nullable|string',
            'vizyon' => 'nullable|string',
            'misyon' => 'nullable|string',
            'kurallar' => 'nullable|string',
        ]);

        $takim->update($validated);

        return redirect()->route('takimlar.show', $takim)->with('success', 'Takım bilgileri başarıyla güncellendi!');
    }

    public function katilmaIstegiGonder(Request $request, Takim $takim)
    {
        // 1. Zaten üye mi?
        if ($takim->uyeler->contains(auth()->id()) || $takim->lider_user_id == auth()->id()) {
            return back()->with('error', 'Zaten bu takımın üyesisiniz.');
        }

        // 2. Zaten istek var mı?
        $mevcutIstek = TakimDavetiyesi::where('takim_id', $takim->id)
            ->where('davet_eden_user_id', auth()->id())
            ->where('type', 'istek')
            ->where('durum', 'bekliyor')
            ->first();

        if ($mevcutIstek) {
            return back()->with('warning', 'Zaten bekleyen bir isteğiniz var.');
        }

        // 3. İsteği Oluştur
        $davet = new TakimDavetiyesi();
        $davet->takim_id = $takim->id;
        $davet->davet_eden_user_id = auth()->id(); // İsteyen kişi
        $davet->davet_edilen_user_id = $takim->lider_user_id; // Onaylayacak kişi (Lider)
        $davet->type = 'istek';
        $davet->durum = 'bekliyor';
        $davet->save();

        // 4. BİLDİRİM GÖNDER (Lidere)
        if ($takim->lider) {
            $takim->lider->notify(new TakimKatilmaIstegi($davet));
        }

        return back()->with('success', 'Katılma isteğiniz takım liderine iletildi.');
    }

    public function istegiGeriCek($id)
    {
        $davet = TakimDavetiyesi::findOrFail($id);

        // Güvenlik: Sadece isteği atan kişi silebilir
        if ($davet->davet_eden_user_id !== auth()->id()) {
            abort(403);
        }

        // --- KRİTİK KISIM: LİDERİN BİLDİRİMİNİ DE SİL ---
        // Böylece Sinan'ın (Liderin) zilindeki ve dashboard'undaki uyarı kalkar.
        if ($davet->takim && $davet->takim->lider) {
            $takimLideri = $davet->takim->lider;

            // Liderin bildirimlerinde, 'data' içindeki 'davet_id' bu olanı bul ve sil
            $takimLideri->notifications()
                ->where('data->davet_id', $davet->id)
                ->delete();
        }

        $davet->delete();

        return back()->with('success', 'Katılma isteği geri çekildi ve bildirim iptal edildi.');
    }

    public function istekKabulEt(TakimDavetiyesi $davetiye)
    {
        // Yetki Kontrolü: Sadece takım lideri bu işlemi yapabilir.
        if (Auth::id() !== $davetiye->takim->lider_user_id) {
            abort(403);
        }

        DB::transaction(function () use ($davetiye) {
            $davetiye->update(['durum' => 'kabul edildi']);
            // Üyeyi eklerken katılma şeklini belirtiyoruz
            $davetiye->takim->uyeler()->syncWithoutDetaching([
                $davetiye->davet_eden_user_id => [
                    'katilma_sekli' => 'İstek ile Katıldı'
                ]
            ]);

        });

        return back()->with('success', $davetiye->davetEden->name . ' takıma başarıyla eklendi.');
    }

    /**
     * Takım liderinin bir katılma isteğini reddetmesi.
     */
    public function istegiReddet(TakimDavetiyesi $davetiye)
    {
        // Yetki Kontrolü
        if (Auth::id() !== $davetiye->takim->lider_user_id) {
            abort(403);
        }

        // İsteğin durumunu 'reddedildi' olarak güncelle
        $davetiye->update(['durum' => 'reddedildi']);

        return back()->with('success', 'Katılma isteği reddedildi.');
    }

}