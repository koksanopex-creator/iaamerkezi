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

class TakimController extends Controller
{
    use NotifiesManager; // Add this line here
    public function index()
    {
        $user = Auth::user();
        
        // 1. Kullanıcının üye olduğu takımlar (Mevcut kodunuz korundu)
        $katildigimTakimlar = $user->takimlar()->with('lider')->withCount('uyeler')->latest()->get();
        $katildigimTakimIdleri = $katildigimTakimlar->pluck('id');

        // 2. Kullanıcının gönderdiği ve durumu "bekliyor" olan istekler (Mevcut kodunuz korundu)
        $gonderdigimIstekler = TakimDavetiyesi::where('type', 'istek')
            ->where('davet_eden_user_id', $user->id)
            ->where('durum', 'bekliyor')
            ->with('takim.lider')
            ->latest()->get();
        $istekGonderilenTakimIdleri = $gonderdigimIstekler->pluck('takim_id');

        // ===== DÜZELTME BAŞLANGICI =====
        // Hatanın nedeni olan $gelenDavetler değişkenini burada oluşturuyoruz.
        // Bu, kullanıcıya gelen ve beklemede olan davetlerin tam listesidir.
        $gelenDavetler = TakimDavetiyesi::where('type', 'davet')
                                     ->where('davet_edilen_user_id', $user->id)
                                     ->where('durum', 'bekliyor')
                                     ->get(); // pluck() yerine get() kullanarak tüm veriyi alıyoruz.
        // ===== DÜZELTME SONU =====

        // 3. Diğer tüm takımlar (Mevcut kodunuz korundu)
        $digerTakimlar = Takim::whereNotIn('id', $katildigimTakimIdleri->merge($istekGonderilenTakimIdleri))
                                ->with('lider')->withCount('uyeler')->latest()->get();

        // View'e HATAYI GİDEREN $gelenDavetler değişkenini de gönderiyoruz.
        return view('takimlar.index', compact(
            'katildigimTakimlar', 
            'gonderdigimIstekler', 
            'digerTakimlar', 
            'gelenDavetler' // Önceki kodda olmayan eksik değişkeni ekledik.
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
            'amac' => 'nullable|string','vizyon' => 'nullable|string',
            'misyon' => 'nullable|string','kurallar' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $takim = Takim::create([
                'ad' => $validated['ad'],
                'lider_user_id' => Auth::id(),
                'amac' => $validated['amac'],'vizyon' => $validated['vizyon'],
                'misyon' => $validated['misyon'],'kurallar' => $validated['kurallar'],
            ]);
            $takim->uyeler()->attach(Auth::id(), ['katilma_sekli' => 'Kurucu Lider']);
        });

        return redirect()->route('takimlar.index')->with('success', 'Takım başarıyla oluşturuldu!');
    }



    public function show(Takim $takim)
    {
        if (!Auth::user()->hasRole('Superadmin') && !$takim->uyeler->contains('id', Auth::id())) {
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
        $aktifProjeler = $takim->atananProjeler()
            ->whereIn('durum', ['Atandı', 'Revize Ediliyor'])
            ->with([
                'logs' => function ($query) {
                    $query->where('eylem', 'Revizyon Talep Edildi')
                        ->with('user')
                        ->latest('created_at');
                },
                'musteriSikayeti' 
            ])
            ->get();
        
        $tamamlananProjeler = $takim->atananProjeler()->where('durum', 'Tamamlandı')->get();
        $yoneticiOnayiBekleyenProjeler = $takim->atananProjeler()->where('durum', 'Yönetici Onayı Bekliyor')->get();
        
        return view('takimlar.show', compact(
            'takim', 
            'potansiyelUyeler', 
            'gonderilenDavetler', 
            'gelenIstekler',
            'bekleyenTalepler',
            'aktifProjeler',      
            'tamamlananProjeler',
            'yoneticiOnayiBekleyenProjeler'   
        ));
    }
    
    public function davetGonder(Request $request, Takim $takim)
    {
        if (Auth::id() !== $takim->lider_user_id) {
            return back()->with('error', 'Bu işlemi yapma yetkiniz yok.');
        }
        $request->validate(['user_id' => 'required|exists:users,id']);
        if ($takim->uyeler()->where('user_id', $request->user_id)->exists()) {
             return back()->with('error', 'Bu kullanıcı zaten takımda mevcut.');
        }
        if (TakimDavetiyesi::where('takim_id', $takim->id)->where('davet_edilen_user_id', $request->user_id)->where('durum', 'bekliyor')->exists()) {
            return back()->with('error', 'Bu kullanıcıya zaten bir davet gönderilmiş.');
        }
        
        // Create the invitation record
        TakimDavetiyesi::create([
            'takim_id' => $takim->id,
            'davet_eden_user_id' => Auth::id(),
            'davet_edilen_user_id' => $request->user_id,
        ]);

        // --- NEW NOTIFICATION LOGIC ---
        $user = User::findOrFail($request->user_id);
        // Use the trait method to notify user AND their manager
        $this->notifyUserAndManager($user, new \App\Notifications\TakimDavetiAldin($takim, Auth::user()));
        // ------------------------------

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

        return redirect()->route('takimlar.show', $takim)->with('success', 'Üye takımdan çıkarıldı.');
    }

    // YENİ FONKSİYONLAR
    public function davetlerim()
    {
        $davetler = TakimDavetiyesi::where('davet_edilen_user_id', Auth::id())
                                ->where('durum', 'bekliyor')
                                ->with('takim.lider')
                                ->get();
        return view('takimlar.davetlerim', compact('davetler'));
    }

    public function davetiKabulEt(TakimDavetiyesi $davetiye)
    {
        if ($davetiye->davet_edilen_user_id !== Auth::id()) {
            abort(403);
        }
        DB::transaction(function () use ($davetiye) {
            $davetiye->update(['durum' => 'kabul edildi']);
            // Üyeyi eklerken katılma şeklini belirtiyoruz
            $davetiye->takim->uyeler()->attach($davetiye->davet_edilen_user_id, ['katilma_sekli' => 'Davet ile Katıldı']);

            // === YENİ EKLENEN KISIM: MÜDÜRE BİLDİRİM ===
            // Daveti kabul eden kişi (Şu anki kullanıcı)
            $kabulEdenUser = Auth::user();

            // Trait fonksiyonunu kullanarak hem kişiye hem müdürüne bildir
            // Not: notifyUserAndManager fonksiyonunu TakimController'a dahil ettiğimizden (use NotifiesManager) emin olun.
            $this->notifyUserAndManager(
                $kabulEdenUser, 
                new \App\Notifications\TakimDavetiYanitlandi($davetiye->takim, $kabulEdenUser)
            );
            // ===========================================
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
        // Yetki Kontrolü: Sadece takım lideri kendi takımının davetini iptal edebilir.
        if (Auth::id() !== $davetiye->takim->lider_user_id) {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }

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
        $user = Auth::user();

        // Kullanıcı zaten üyeyse veya istek göndermişse tekrar göndermesini engelle
        if ($takim->uyeler->contains($user)) {
            return back()->with('error', 'Zaten bu takımın üyesisiniz.');
        }
        if (TakimDavetiyesi::where('takim_id', $takim->id)->where('davet_eden_user_id', $user->id)->where('durum', 'bekliyor')->exists()) {
            return back()->with('error', 'Bu takıma zaten bir katılma isteği göndermişsiniz.');
        }

        // Davet/İstek kaydını oluştur
        TakimDavetiyesi::create([
            'takim_id' => $takim->id,
            'davet_eden_user_id' => $user->id, // İsteği yapan
            'davet_edilen_user_id' => $takim->lider_user_id, // İstek lidere gider
            'durum' => 'bekliyor',
            'type' => 'istek',
        ]);

        return back()->with('success', 'Katılma isteğiniz takım liderine başarıyla gönderildi.');
    }

    public function istegiGeriCek(TakimDavetiyesi $davetiye)
    {
        if ($davetiye->davet_eden_user_id !== Auth::id() || $davetiye->type !== 'istek') {
            abort(403, 'Bu işlemi yapma yetkiniz yok.');
        }
        $davetiye->delete();
        return back()->with('success', 'Katılma isteğiniz başarıyla geri çekildi.');
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
            $davetiye->takim->uyeler()->attach($davetiye->davet_eden_user_id, ['katilma_sekli' => 'İstek ile Katıldı']);
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