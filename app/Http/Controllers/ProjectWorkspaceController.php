<?php

namespace App\Http\Controllers;

use App\Models\Iaa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\IaaProgressUpdate;
use App\Models\IaaWorkflowStep;
use App\Models\IaaLog;
use Illuminate\Support\Facades\Notification; // <-- EKLE
use App\Notifications\ProjeAdimiGuncellendi; // <-- EKLE
use Illuminate\Support\Facades\Session; // <-- 1. BU SATIRI EKLEYİN

class ProjectWorkspaceController extends Controller
{
    /**
     * Proje çalışma alanını gösterir.
     * Hem giriş yapmış kullanıcıları hem de şifreyle giriş yapmış misafirleri kontrol eder.
     */
    public function show(Iaa $iaa)
    {
        // === 2. YENİ YETKİ KONTROLÜ (Mevcut 'abort_if' satırınızın yerine geçer) ===
        $isYetkiliKullanici = false;
        $isYetkiliMisafir = false;

        // 1. Giriş yapmış bir "Kullanıcı" mı (Admin, Erhan Cesur vb.)?
        if (Auth::check()) {
            $user = Auth::user();
            $takim = $iaa->atananTakim;
            
            // === GÜNCELLEME: 'Bölüm Kalite Yöneticisi' rolü eklendi ===
            if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi']) || 
                ($takim && $user->takimlar->contains($takim))) 
            {
                // Eğer 'Bölüm Kalite Yöneticisi' ise, projenin kategorisinden sorumlu mu diye de bakmalıyız
                if ($user->hasRole('Bölüm Kalite Yöneticisi') && !$user->hasRole('Superadmin')) {
                     if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayet_kategorisi_id) {
                         if ($user->yonettigiSikayetKategorileri->contains($iaa->musteriSikayeti->sikayet_kategorisi_id)) {
                             $isYetkiliKullanici = true;
                         }
                     }
                } else {
                    // Diğer roller için genel yetki
                    $isYetkiliKullanici = true;
                }
            }
        }

        // 2. Giriş yapmış bir "Misafir" (Müşteri) mi?
        // Projeye bağlı şikayeti ve token'ı kontrol et
        $sikayet = $iaa->musteriSikayeti; // Bu ilişkiyi bir sonraki adımda Iaa modeline ekleyeceğiz
        
        if ($sikayet) {
            // Müşterinin bu şikayet için şifre girip girmediğini (Session) kontrol et
            $sessionKey = 'sikayet_logged_in_' . $sikayet->takip_token;
            if (Session::has($sessionKey)) {
                $isYetkiliMisafir = true; // Evet, şifreyle giriş yapmış
            }
        }

        // 3. GÜVENLİK KONTROLÜ:
        // Eğer giriş yapmış bir kullanıcı DEĞİLSE ve giriş yapmış bir misafir DEĞİLSE
        if (!$isYetkiliKullanici && !$isYetkiliMisafir) {
            
            // Eğer bu proje bir şikayete bağlıysa (ama misafir giriş yapmamışsa)
            // $sikayet->takip_token kontrolü de ekledik ki null hatası almayalım
            if ($sikayet && $sikayet->takip_token) {
                // === HATA DÜZELTMESİ BURADA: Token parametresi array olarak ['token' => ...] gönderilmeli ===
                return redirect()->route('public.sikayet.show', ['token' => $sikayet->takip_token])
                    ->with('error', 'Proje detaylarını görmek için lütfen şifrenizle giriş yapın.');
            }
            
            // Eğer login değilse Login sayfasına yönlendir (Yetki yoksa login olsun)
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            
            // Login olmuş ama yetkisi yoksa (Bölüm yöneticisi olup yetkisi olmayan kategoriye girenler vb.)
            abort(403, 'Bu projeyi görüntüleme yetkiniz yok.');
        }
        // === YENİ YETKİ KONTROLÜ SONU ===


        // --- Yetki kontrolü geçildiyse, mevcut kodunuz buradan devam ediyor ---
        
        $iaa->load([
            'musteriSikayeti.dosyalar', 
            'musteriSikayeti.sikayetKategori'
        ]);
        
        $takim = $iaa->atananTakim;
        
        $assignment = DB::table('iaa_talepleri')
                        ->where('iaa_id', $iaa->id)
                        ->first(); // Güvenlik: Sadece ilk atamayı alıyoruz

        // Eğer atama kaydı (iaa_talepleri) yoksa (örn: silinmiş)
        if (!$assignment) {
            if($sikayet) {
                 return redirect()->route('public.sikayet.show', $sikayet->takip_token)
                    ->with('error', 'Proje ataması bulunamadı veya iptal edildi.');
            }
            return redirect()->route('dashboard')->with('error', 'Proje ataması bulunamadı.');
        }

        $workflow = \App\Models\IaaWorkflow::with('steps')->find($assignment->iaa_workflow_id);
        $steps = $workflow->steps;

        $allProgressUpdates = \App\Models\IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
                                        ->get();

        $completedStepIds = $allProgressUpdates->whereNotNull('completed_at')
                                            ->pluck('iaa_workflow_step_id') 
                                            ->toArray();

        $progressUpdates = $allProgressUpdates->keyBy('iaa_workflow_step_id');
        
        // Misafirler takım üyesi değildir, bu yüzden $isTeamMember = false olmalı
        $isTeamMember = $isYetkiliKullanici ? Auth::user()->takimlar->contains($takim) : false;

        $totalStepsCount = $steps->count();
        $completedStepsCount = count($completedStepIds);
        $progressPercentage = $totalStepsCount > 0 ? ($completedStepsCount / $totalStepsCount) * 100 : 0;

        $statusDate = null;
        $logAction = null;

        switch ($iaa->durum) {
            case 'Revize Ediliyor':
                $logAction = 'Revizyon Talep Edildi';
                break;
            case 'Tamamlanması Reddedildi':
                $logAction = 'Tamamlanmış Projenin Reddi';
                break;
        }

        if ($logAction) {
            $latestLog = IaaLog::where('iaa_id', $iaa->id)
                                ->where('eylem', $logAction)
                                ->latest('created_at')
                                ->first();
            if ($latestLog) {
                $statusDate = $latestLog->created_at;
            }
        } 
        elseif ($iaa->durum === 'Tamamlandı') {
            $statusDate = $iaa->onaylanma_tarihi;
        }

        $tumProjeLoglari = IaaLog::where('iaa_id', $iaa->id)
                            ->whereIn('eylem', [
                                'Proje Adımı Tamamlandı', 
                                'Proje Adımı Yeniden Açıldı',
                                'Revizyon Talep Edildi', 
                                'Proje Onaylandı',
                                'Onay Geri Alındı',
                            ])
                            ->with('user') 
                            ->latest()     
                            ->get();
                            
        $sonOnLoglar = $tumProjeLoglari->take(5);

        return view('proje-calisma-alani.show', compact(
            'iaa', 'takim', 'assignment', 'workflow', 'steps',
            'completedStepIds', 'progressUpdates', 'totalStepsCount', 'completedStepsCount',
            'progressPercentage', 'isTeamMember', 'statusDate', 'tumProjeLoglari', 'sonOnLoglar'
        ));
    }

    public function storeStep(Request $request, $assignment_id, $step_id)
    {
        // 1. Gelen veriyi doğrula
        $validated = $request->validate([
            'content' => 'required|string|min:20',
        ]);

        // 2. İlgili kayıtları bul
        $assignment = DB::table('iaa_talepleri')->find($assignment_id);
        $step = IaaWorkflowStep::find($step_id);

        // ================== DEĞİŞİKLİK BURADA ==================
        // 3. Yetki Kontrolü: Takımı Eloquent Model olarak buluyoruz.
        $takim = \App\Models\Takim::find($assignment->takim_id);
        abort_if(!$takim || !Auth::user()->takimlar->contains($takim), 403, 'Bu projeye kayıt ekleme yetkiniz yok.');
        // ========================================================

        // 4. İlerleme kaydını oluştur veya güncelle
        IaaProgressUpdate::updateOrCreate(
            [
                'iaa_talep_id' => $assignment->id,
                'iaa_workflow_step_id' => $step->id,
            ],
            [
                'user_id' => Auth::id(),
                'content' => $validated['content'],
                'completed_at' => now(),
            ]
        );
        
        // 5. Projenin ana kaydını bul ve geri yönlendir
        $iaa = Iaa::find($assignment->iaa_id);

        // ================== YENİ LOGLAMA KODU ==================
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Proje Adımı Tamamlandı',
            'aciklama' => Auth::user()->name . " adlı kullanıcı, '" . $step->name . "' adımını kaydetti ve tamamladı."
        ]);
        // ================== LOGLAMA SONU ==================

        // === BİLDİRİM KODU BAŞLANGICI ===
        try {
            // Adımı güncelleyen kişi HARİÇ takımdaki diğer üyeleri bul
            $guncelleyenKullanici = Auth::user();
            $bildirimAlacaklar = $takim->users->where('id', '!=', $guncelleyenKullanici->id);

            if ($bildirimAlacaklar->isNotEmpty()) {
                Notification::send($bildirimAlacaklar, new ProjeAdimiGuncellendi($iaa, $step, $guncelleyenKullanici));
            }
        } catch (\Exception $e) {
            Log::error('Proje adımı güncellendi bildirimi gönderilemedi: ' . $e->getMessage());
        }
        // === BİLDİRİM KODU SONU ===

        return redirect()->route('proje.workspace.show', $iaa)
                        ->with('success', '"' . $step->name . '" adımı başarıyla tamamlandı!');
    }

    /**
     * Tamamlanmış bir adımı ve ondan sonraki adımları yeniden düzenlemek için açar.
     */
    public function reopenStep(IaaProgressUpdate $progress_update)
    {
        // 1. Yetki Kontrolü
        $assignment = DB::table('iaa_talepleri')->find($progress_update->iaa_talep_id);
        $takim = \App\Models\Takim::find($assignment->takim_id);
        abort_if(!$takim || !Auth::user()->takimlar->contains($takim), 403, 'Bu adımı düzenleme yetkiniz yok.');

        // Sadece mevcut adımın "tamamlanma" işaretini kaldırarak onu tekrar aktif hale getir.
        $progress_update->update(['completed_at' => null]);

        // Projenin genel durumunu da "Devam Ediyor" olarak güncelle.
        DB::table('iaa_talepleri')->where('id', $assignment->id)->update(['status' => 'Devam Ediyor']);

        // 5. Kullanıcıyı, projenin ana kaydını bularak çalışma alanına geri yönlendir.
        $iaa = Iaa::find($assignment->iaa_id);

        // ================== YENİ LOGLAMA KODU ==================
        // Adımın adını alabilmek için step ilişkisini yüklüyoruz
        $progress_update->load('step'); 
        $stepName = $progress_update->step ? $progress_update->step->name : 'Bilinmeyen Adım';

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Proje Adımı Yeniden Açıldı',
            'aciklama' => Auth::user()->name . " adlı kullanıcı, '" . $stepName . "' adımını yeniden düzenlemek için açtı."
        ]);
        // ================== LOGLAMA SONU ==================

        return redirect()->route('proje.workspace.show', $iaa)
                         ->with('success', 'Adım yeniden düzenlemeye açıldı.');
    }
}
