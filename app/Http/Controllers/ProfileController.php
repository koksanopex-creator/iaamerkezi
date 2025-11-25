<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Dosya silmek için gerekli
use Illuminate\View\View;
use Illuminate\Support\Str; // Dosya adı düzenleme için gerekli
use App\Models\User;
use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\ProfileComment;
use Illuminate\Support\Facades\DB;
use App\Notifications\ProfilYorumBildirimi;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $data = $this->getProfileData($user);
        return view('profile.edit', array_merge(['user' => $user], $data));
    }

    public function show(User $user): View
    {
        $currentUser = auth()->user();

        // Güvenlik: Normal kullanıcı Admin profilini göremez
        if ($user->hasRole('Superadmin') && !$currentUser->hasRole('Superadmin')) {
            abort(404);
        }

        $data = $this->getProfileData($user);
        return view('profile.show', array_merge(['user' => $user], $data));
    }

    public function storeComment(Request $request, User $user)
    {
        $request->validate([
            'yorum' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:profile_comments,id'
        ]);

        $comment = ProfileComment::create([
            'user_id' => $user->id,            // Profil Sahibi
            'yazan_user_id' => auth()->id(),   // Yazan (Ben)
            'yorum' => $request->yorum,
            'parent_id' => $request->parent_id
        ]);

        // === BİLDİRİM MANTIĞI (GÜNCELLENDİ) ===
        $benimId = auth()->id();
        $profilSahibiId = $user->id; // Yorumun yapıldığı profilin ID'si

        if ($request->parent_id) {
            // CEVAP İSE:
            $ustYorum = ProfileComment::find($request->parent_id);
            
            // 1. Üst Yorum Sahibine Bildirim (Eğer ben değilsem)
            if ($ustYorum && $ustYorum->yazan_user_id !== $benimId) {
                $target = User::find($ustYorum->yazan_user_id);
                if($target) {
                    // DİKKAT: 3. parametre olarak $profilSahibiId gönderiyoruz
                    $target->notify(new ProfilYorumBildirimi($comment, 'reply', $profilSahibiId));
                }
            }

            // 2. Profil Sahibine Bildirim (Eğer ben değilsem VE Üst yorum sahibi de o değilse)
            if ($profilSahibiId !== $benimId && $ustYorum && $profilSahibiId !== $ustYorum->yazan_user_id) {
                $user->notify(new ProfilYorumBildirimi($comment, 'reply', $profilSahibiId));
            }

        } else {
            // YENİ YORUM İSE:
            if ($profilSahibiId !== $benimId) {
                // DİKKAT: 3. parametre olarak $profilSahibiId gönderiyoruz
                $user->notify(new ProfilYorumBildirimi($comment, 'new_comment', $profilSahibiId));
            }
        }

        // Redirect with 'active_tab' session variable
        return back()->with('success', 'Yorumunuz gönderildi.')->with('active_tab', 'yorumlar');
    }

    /**
     * Yorum Silme İşlemi
     */
    public function destroyComment(ProfileComment $comment)
    {
        $user = auth()->user();
        
        // YETKİ KONTROLÜ:
        // 1. Yorumu yazan kişi silebilir.
        // 2. Profil sahibi silebilir (Ancak Süper Admin'in yorumunu silemez).
        // 3. Süper Admin her şeyi silebilir.

        $isAuthor = $user->id === $comment->yazan_user_id;
        $isProfileOwner = $user->id === $comment->user_id;
        $isSuperAdmin = $user->hasRole('Superadmin');
        $commentAuthorIsAdmin = $comment->yazan->hasRole('Superadmin');

        if (
            $isSuperAdmin || 
            $isAuthor || 
            ($isProfileOwner && !$commentAuthorIsAdmin)
        ) {
            $comment->delete();
            return back()->with('success', 'Yorum silindi.')->with('active_tab', 'yorumlar');
        }

        return back()->with('error', 'Bu yorumu silme yetkiniz yok.')->with('active_tab', 'yorumlar');
    }

    /**
     * GÜNCELLENEN UPDATE METODU (ÖZEL DOSYA ADI İLE)
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // === FOTOĞRAF YÜKLEME İŞLEMİ ===
        if ($request->hasFile('photo')) {
            
            // 1. Varsa eski fotoğrafı sil
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // 2. Yeni Dosya Adını Oluştur
            // Format: ad-soyad_24-11-2025_14-53-01_a1b2.jpg
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            
            $safeName = Str::slug($user->name); // Türkçe karakterleri temizle (Serkan Tölek -> serkan-tolek)
            $timestamp = now()->format('d-m-Y_H-i-s');
            $random = Str::random(4); // Çakışmayı önlemek için kısa rastgele kod
            
            $fileName = "{$safeName}_{$timestamp}_{$random}.{$extension}";

            // 3. Kaydet (storeAs kullanarak isimi biz belirliyoruz)
            $path = $file->storeAs('profile-photos', $fileName, 'public');
            
            $user->profile_photo_path = $path;
        }
        // ===============================

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }

    private function getProfileData($user)
    {
        // 1. Takımlar
        $takimlar = $user->takimlar ?? collect();
        
        // 2. İstatistikler
        $tamamlananProjeSayisi = Iaa::where('durum', 'Tamamlandı')
            ->whereHas('atananTakim.uyeler', fn($q) => $q->where('users.id', $user->id))
            ->count();

        $aktifProjeSayisi = Iaa::whereIn('durum', ['Atandı', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])
            ->whereHas('atananTakim.uyeler', fn($q) => $q->where('users.id', $user->id))
            ->count();

        // 3. Son Aktiviteler (Loglar)
        $sonAktiviteler = IaaLog::where('user_id', $user->id)
            ->with('iaa')
            ->latest()
            ->take(10)
            ->get();

        // 4. Son Proje
        $sonProje = Iaa::whereHas('atananTakim.uyeler', fn($q) => $q->where('users.id', $user->id))
            ->latest('updated_at')
            ->first();

        // 5. Şikayet Bildirimleri
        $girilenSikayetler = Iaa::where('gonderen_user_id', $user->id)
            ->has('musteriSikayeti')
            ->with('musteriSikayeti')
            ->latest()
            ->take(10)
            ->get();
        
        $sikayetPuani = $girilenSikayetler->where('durum', 'Tamamlandı')->sum('puan');

        // 6. Grafik Verisi (Aylık Performans)
        $aylikPerformans = Iaa::where('durum', 'Tamamlandı')
            ->whereHas('atananTakim.uyeler', fn($q) => $q->where('users.id', $user->id))
            ->select(
                DB::raw('count(id) as sayi'),
                DB::raw("DATE_FORMAT(onaylanma_tarihi, '%Y-%m') as ay")
            )
            ->where('onaylanma_tarihi', '>=', now()->subMonths(6))
            ->groupBy('ay')
            ->orderBy('ay')
            ->pluck('sayi', 'ay')
            ->toArray();

        $lastLogin = $user->updated_at;

        // 7. YORUMLAR (GÜNCELLENMİŞ KISIM BURASI)
        // Sadece ana yorumları çekiyoruz, alt yorumları (cevaplar) relation ile yüklüyoruz.
        $yorumlar = ProfileComment::where('user_id', $user->id)
            ->whereNull('parent_id') // Sadece ANA yorumları al (Cevapları değil)
            ->with(['yazan', 'cevaplar.yazan']) // Alt yorumları ve yazarlarını hazır et
            ->latest()
            ->take(20) // SON 20 YORUM LİMİTİ
            ->get();

        // 8. Admin İstatistikleri (Sadece Admin profili için)
        $isAdmin = $user->hasRole('Superadmin');
        $adminStats = [];
        
        if ($isAdmin) {
            $adminStats = [
                'onaylanan_proje' => Iaa::where('onaylayan_user_id', $user->id)->where('durum', 'Tamamlandı')->count(),
                'reddedilen_proje' => Iaa::where('onaylayan_user_id', $user->id)->where('durum', 'Tamamlanması Reddedildi')->count(),
                'havuza_eklenen' => Iaa::where('onaylayan_user_id', $user->id)->where('durum', 'Havuzda')->count(),
                
                // Yönetim Logları
                'son_yonetim_loglari' => IaaLog::where('user_id', $user->id)
                    ->whereIn('eylem', ['Proje Onaylandı', 'Revizyon Talep Edildi', 'Tamamlanmış Projenin Reddi', 'İşlem Geri Alındı'])
                    ->with('iaa')
                    ->latest()
                    ->take(10)
                    ->get(),

                // Login Logları (LoginActivity Modeli Gerekli)
                'login_loglari' => \App\Models\LoginActivity::where('user_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get()
            ];
        }

        // 9. Kullanıcının Dahil Olduğu Projeler (Edit Ekranı İçin)
        $kullaniciProjeleri = Iaa::whereHas('atananTakim.uyeler', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->with('atananTakim')
            ->latest('updated_at')
            ->get();

        return [
            'takimlar' => $takimlar,
            'tamamlananProjeSayisi' => $tamamlananProjeSayisi,
            'aktifProjeSayisi' => $aktifProjeSayisi,
            'sonAktiviteler' => $sonAktiviteler,
            'sonProje' => $sonProje,
            'girilenSikayetler' => $girilenSikayetler,
            'sikayetPuani' => $sikayetPuani,
            'aylikPerformans' => $aylikPerformans,
            'lastLogin' => $lastLogin,
            'yorumlar' => $yorumlar, // <-- ARTIK İÇ İÇE YAPIYI GÖNDERİYORUZ
            'isAdmin' => $isAdmin,
            'adminStats' => $adminStats,
            'kullaniciProjeleri' => $kullaniciProjeleri 
        ];
    }
}