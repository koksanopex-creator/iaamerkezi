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

use App\Services\Dashboard\KullaniciPuanService;

class ProfileController extends Controller
{
    protected $puanService;

    public function __construct(KullaniciPuanService $puanService)
    {
        $this->puanService = $puanService;
    }
    public function edit(Request $request): View
    {
        $user = $request->user();

        // === PUAN SENKRONİZASYONU (PROFİL DÜZENLEME EKRANI) ===
        if ($user->is_personnel) {
            $guncelPuan = $this->puanService->calculateTotalScore($user);
            if ($user->toplam_puan != $guncelPuan) {
                $user->toplam_puan = $guncelPuan;
                $user->save();
            }
        }

        $data = $this->getProfileData($user);
        return view('profile.edit', array_merge(['user' => $user], $data));
    }

    public function show(User $user): View
    {
        $currentUser = auth()->user();

        // === GÜVENLİK DUVARI BAŞLANGICI ===

        // 1. KURAL: Superadmin herkesi görebilir.
        if ($currentUser->hasRole('Superadmin')) {
            // Sorun yok, devam et.
        }
        // 2. KURAL: Kişi KENDİ profilini her zaman görebilir.
        elseif ($currentUser->id == $user->id) {
            // Sorun yok, devam et.
        } else {
            // --- YASAKLI DURUMLAR ---

            // A) Hedef kişi Müşteri ise (is_personnel = false) -> 404
            // (Bu satır önemli, veritabanında is_personnel sütununuzun 0/1 veya true/false olmasına göre değişebilir)
            if ($user->is_personnel == false) {
                abort(404);
            }

            // B) Hedef kişi "Yasaklı Roller"den birine sahipse -> 403
            // Yönetim, Dış Avukat, Finans, Hukuk Yöneticisi...
            // Not: 'Yonetim' rolünü zaten gizlemişsiniz ama diğerlerini de ekleyelim.
            $yasakliRoller = [
                'Superadmin',
                'Yonetim',
                'Dış Avukat',
                'Arabuluculuk Finans',
                'Hukuk Yöneticisi',
                'Hukuk Admini'
            ];

            if ($user->hasRole($yasakliRoller)) {
                abort(403, 'Bu kullanıcının profili gizlidir.');
            }
        }
        // === GÜVENLİK DUVARI BİTİŞİ ===


        // === PUAN SENKRONİZASYONU (GÖRÜNTÜLENEN PROFİL İÇİN) ===
        if ($user->is_personnel) {
            $guncelPuan = $this->puanService->calculateTotalScore($user);
            if ($user->toplam_puan != $guncelPuan) {
                $user->toplam_puan = $guncelPuan;
                $user->save();
            }
        }

        // Mevcut Veri Hazırlama Kodunuz (Aynen Korundu)
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
                if ($target) {
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
        // 1. Takımlar (ŞİKAYET TAKIMLARI HARİÇ)
        $takimlar = $user->takimlar()->where('tur', '!=', 'sikayet')->get();

        // 2. İstatistikler (DÜZELTİLDİ: SQUAD ÜYELİĞİ DAHİL)
        $tamamlananProjeSayisi = Iaa::where('durum', 'Tamamlandı')
            ->where(function ($q) use ($user) {
                $q->whereHas('atananTakim.uyeler', fn($sub) => $sub->where('users.id', $user->id))
                    ->orWhereHas('projeEkibi', fn($sub) => $sub->where('users.id', $user->id));
            })
            ->count();

        $aktifProjeSayisi = Iaa::whereIn('durum', ['Atandı', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])
            ->where(function ($q) use ($user) {
                $q->whereHas('atananTakim.uyeler', fn($sub) => $sub->where('users.id', $user->id))
                    ->orWhereHas('projeEkibi', fn($sub) => $sub->where('users.id', $user->id));
            })
            ->count();

        // 3. Son Aktiviteler (Loglar)
        $sonAktiviteler = IaaLog::where('user_id', $user->id)
            ->with('iaa')
            ->latest()
            ->take(10)
            ->get();

        // 4. Son Proje (KENDİ ÖNERDİKLERİ VE SQUAD DAHİL)
        $sonProje = Iaa::where(function ($query) use ($user) {
            $query->whereHas('atananTakim.uyeler', fn($q) => $q->where('users.id', $user->id))
                ->orWhereHas('projeEkibi', fn($q) => $q->where('users.id', $user->id));
        })
            ->orWhere('gonderen_user_id', $user->id)
            ->latest('updated_at')
            ->first();

        // 5. Şikayet Bildirimleri (DÜZELTİLEN MANTIK)
        // Kullanıcının "Oluşturduğu" (Bildirdiği) şikayetleri alıyoruz.
        // Dashboard mantığıyla birebir aynı olması için User->Iaa ilişkisi yerine MusteriSikayeti tablosuna bakıyoruz.
        $girilenSikayetler = \App\Models\MusteriSikayeti::where('olusturan_kurul_uyesi_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $sikayetPuani = $girilenSikayetler->sum('kazanilan_puan');

        // 6. Grafik Verisi (SAF İAA ÇÖZME PERFORMANSI - ŞİKAYET HARİÇ)
        $aylikPerformans = Iaa::where('durum', 'Tamamlandı')
            ->doesntHave('musteriSikayeti') // <--- SADECE SAF İAA PROJELERİ
            ->where(function ($q) use ($user) {
                $q->whereHas('atananTakim.uyeler', fn($sub) => $sub->where('users.id', $user->id))
                    ->orWhereHas('projeEkibi', fn($sub) => $sub->where('users.id', $user->id));
            })
            ->select(
                DB::raw('count(id) as sayi'),
                DB::raw("DATE_FORMAT(onaylanma_tarihi, '%Y-%m') as ay")
            )
            // TARİH KISITLAMASI KALDIRILDI (Tüm Zamanlar)
            ->groupBy('ay')
            ->orderBy('ay')
            ->pluck('sayi', 'ay')
            ->toArray();

        // 6b. Müşteri Şikayeti Çözme Performansı (Tüm Zamanlar)
        // Kapsam: Doğrudan Çözüm Takımı + Yetkili + Şikayetten Dönüşen Projelerin Ekibi
        $sikayetPerformans = \App\Models\MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
            ->where(function ($q) use ($user) {
                // 1. Çözüm Takımındaysa
                $q->whereHas('cozumTakimi.uyeler', fn($uq) => $uq->where('users.id', $user->id))
                    // 2. Veya Doğrudan Yetkiliyse
                    ->orWhere('yetkili_user_id', $user->id)
                    // 3. Veya Şikayet bir Projeye dönüşmüşse ve kullanıcı o projenin ekibindeyse (Takım veya Squad)
                    ->orWhereHas('iaaProjesi', function ($iq) use ($user) {
                    $iq->whereHas('atananTakim.uyeler', fn($uq) => $uq->where('users.id', $user->id))
                        ->orWhereHas('projeEkibi', fn($uq) => $uq->where('users.id', $user->id));
                });
            })
            ->select(
                DB::raw('count(id) as sayi'),
                DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as ay")
            )
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

        // 9. Kullanıcının Dahil Olduğu Projeler (DÜZELTİLDİ: SQUAD DAHİL)
        $kullaniciProjeleri = Iaa::where(function ($q) use ($user) {
            $q->whereHas('atananTakim.uyeler', function ($sub) use ($user) {
                $sub->where('users.id', $user->id);
            })
                ->orWhereHas('projeEkibi', function ($sub) use ($user) {
                    $sub->where('users.id', $user->id);
                });
        })
            ->orWhere('gonderen_user_id', $user->id) // Kendi önerileri
            ->with('atananTakim')
            ->latest('updated_at')
            ->get();



        // 10. AKTİF GÖREVLER (Kişiye Özel Sekme)
        $viewer = auth()->user();

        // Görünürlük Kontrolü
        $canViewActiveTasks = false;

        if ($viewer->id === $user->id) {
            $canViewActiveTasks = true; // Kendisi
        } elseif ($viewer->hasRole(['Superadmin', 'Yonetim'])) {
            $canViewActiveTasks = true; // Yönetici
        } elseif ($viewer->hasRole('Bölüm Lideri') && $viewer->bolum_id == $user->bolum_id) {
            $canViewActiveTasks = true; // İlgili Bölüm Lideri
        }

        $activeTasks = [];

        if ($canViewActiveTasks) {
            $query = Iaa::with([
                'musteriSikayeti.sikayetKategori',
                'atananTakim',
                'aktifAdim.sorumlular',
                'logs.user'
            ])
                ->has('musteriSikayeti')
                ->where(function ($topQ) use ($user) {

                    // 1. DURUM: BÖLÜM ONAYI BEKLİYOR (Eğer kullanıcı Bölüm Lideriyse)
                    if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
                        $topQ->orWhere(function ($q) use ($user) {
                            $q->where('durum', 'Bölüm Onayı Bekliyor')
                                ->whereHas('musteriSikayeti.sikayetKategori', function ($k) use ($user) {
                                    $k->where('bolum_id', $user->bolum_id);
                                });
                        });
                    }

                    // 2. DURUM: BÖLÜM KALİTE YÖNETİCİSİ İSE
                    if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                        $yonetilenKategoriler = $user->yonettigiSikayetKategorileri->pluck('id');
                        if ($yonetilenKategoriler->isNotEmpty()) {
                            $topQ->orWhere(function ($q) use ($yonetilenKategoriler) {
                                $q->where('durum', 'Bölüm Onayı Bekliyor')
                                    ->whereHas('musteriSikayeti', function ($k) use ($yonetilenKategoriler) {
                                        $k->whereIn('sikayet_kategorisi_id', $yonetilenKategoriler);
                                    });
                            });
                        }
                    }

                    // 3. DURUM: YÖNETİCİ ONAYI BEKLİYOR (Eğer kullanıcı Yönetim ise)
                    if ($user->hasRole(['Superadmin', 'Yonetim'])) {
                        $topQ->orWhere('durum', 'Yönetici Onayı Bekliyor');
                    }

                    // 4. DURUM: KİŞİSEL ADIM ATAMASI
                    $topQ->orWhereHas('stepAssignments', function ($assignQ) use ($user) {
                        $assignQ->where('user_id', $user->id)
                            ->whereNotExists(function ($sub) {
                                $sub->select(DB::raw(1))
                                    ->from('iaa_progress_updates')
                                    ->join('iaa_talepleri', 'iaa_progress_updates.iaa_talep_id', '=', 'iaa_talepleri.id')
                                    ->whereColumn('iaa_talepleri.iaa_id', 'iaa_step_assignments.iaa_id')
                                    ->whereColumn('iaa_progress_updates.iaa_workflow_step_id', 'iaa_step_assignments.iaa_workflow_step_id')
                                    ->whereNotNull('completed_at');
                            });
                    });

                    // 5. DURUM: TAKIM ÜYELİĞİ (Aktif Görevli)
                    $topQ->orWhere(function ($activeQ) use ($user) {
                        $activeQ->whereNotIn('durum', ['Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Talep Olarak Kapatıldı', 'talep_olarak_kapatildi', 'TALEP_OLARAK_KAPATİLDİ', 'TALEP_OLARAK_KAPATILDI'])
                            ->where(function ($teamQ) use ($user) {
                                $teamQ->whereHas('projeEkibi', fn($pe) => $pe->where('users.id', $user->id))
                                    ->orWhereHas('atananTakim', fn($at) => $at->where('lider_user_id', $user->id));
                            });
                    });
                });

            $activeTasks = $query->latest('updated_at')->get();
        }

        return [
            'takimlar' => $takimlar,
            'tamamlananProjeSayisi' => $tamamlananProjeSayisi,
            'aktifProjeSayisi' => $aktifProjeSayisi,
            'sonAktiviteler' => $sonAktiviteler,
            'sonProje' => $sonProje,
            'girilenSikayetler' => $girilenSikayetler,
            'sikayetPuani' => $sikayetPuani,
            'aylikPerformans' => $aylikPerformans,
            'sikayetPerformans' => $sikayetPerformans,
            'lastLogin' => $lastLogin,
            'yorumlar' => $yorumlar,
            'isAdmin' => $isAdmin,
            'adminStats' => $adminStats,
            'kullaniciProjeleri' => $kullaniciProjeleri,
            'canViewComplaintStats' => auth()->id() == $user->id || auth()->user()->hasRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Müşteri Şikayeti Çözüm Lideri', 'Müşteri Şikayeti Kurulu', 'Bölüm Kalite Yöneticisi']),
            'activeTasks' => $activeTasks,
            'canViewActiveTasks' => $canViewActiveTasks
        ];
    }
}