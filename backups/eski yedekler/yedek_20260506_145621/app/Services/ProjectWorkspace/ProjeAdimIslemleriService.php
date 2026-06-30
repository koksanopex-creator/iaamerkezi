<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\IaaProgressUpdate;
use App\Models\IaaWorkflowStep;
use App\Notifications\AdimSorumlusuAtandi;
use App\Notifications\ProjeAdimiGuncellendi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Traits\NotifiesManager;

class ProjeAdimIslemleriService
{
    use NotifiesManager;

    protected $calismaAlaniService;

    public function __construct(ProjeCalismaAlaniService $calismaAlaniService)
    {
        $this->calismaAlaniService = $calismaAlaniService;
    }

    /**
     * Adım Kaydetme ve Tamamlama
     */
    public function completeStep(Request $request, $assignmentId, $stepId)
    {
        Log::info("completeStep Metodu Tetiklendi: AssignmentID: {$assignmentId}, StepID: {$stepId}");

        $validated = $request->validate([
            'content' => 'required|string|min:20',
        ]);

        $assignment = DB::table('iaa_talepleri')->find($assignmentId);
        $step = IaaWorkflowStep::find($stepId);
        $iaa = Iaa::find($assignment->iaa_id);

        if (!$assignment) {
            abort(404, 'Proje ataması bulunamadı.');
        }

        // Yetki Kontrolü (Servis üzerinden)
        if (!$this->calismaAlaniService->authorizeUser($iaa)) {
            abort(403, 'Bu projeye müdahale etme yetkiniz yok.');
        }

        // Sorumluluk Kontrolü
        $this->checkStepResponsibility($iaa, $step);

        // Bildirim Gönderilecek mi kontrolü (Daha önce tamamlanmamışsa gönderilecek)
        $isFirstCompletion = !IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
            ->where('iaa_workflow_step_id', $step->id)
            ->whereNotNull('completed_at')
            ->exists();

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

        // Loglama
        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Proje Adımı Tamamlandı',
            'aciklama' => Auth::user()->name . " adlı kullanıcı, '" . $step->name . "' adımını kaydetti ve tamamladı."
        ]);

        // Tüm Paydaşlara Bildirim (Sadece ilk kez tamamlandığında)
        if ($isFirstCompletion) {
            $this->notifyStakeholdersAboutProgress($iaa, $step, $assignment);
        }

        // Bildirim (Takım üyelerine)
        try {
            $takim = \App\Models\Takim::find($assignment->takim_id);
            if ($takim) {
                $guncelleyenKullanici = Auth::user();
                $bildirimAlacaklar = $takim->users->where('id', '!=', $guncelleyenKullanici->id);

                if ($bildirimAlacaklar->isNotEmpty()) {
                    Notification::send($bildirimAlacaklar, new ProjeAdimiGuncellendi($iaa, $step, $guncelleyenKullanici));
                }
            }
        } catch (\Exception $e) {
            // Bildirim hatası süreci durdurmasın
        }

        return $iaa;
    }

    /**
     * Tüm paydaşlara (Müşteri, Kalite, Bölüm Lideri, Squad) ilerleme bildirimi gönderir
     */
    public function notifyStakeholdersAboutProgress(Iaa $iaa, IaaWorkflowStep $step, $assignment)
    {
        try {
            Log::info("Bildirim Süreci Başladı: Proje #{$iaa->id}, Adım: {$step->name}");
            
            $currentUser = Auth::user();
            $recipients = collect();

            // 1. Müşteri Temsilcileri ve Ek İlgililer
            $sikayet = $iaa->musteriSikayeti;
            if ($sikayet) {
                $customerUsers = \App\Models\User::where('customer_id', $sikayet->customer_id)->get();
                $ekYetkililer = $sikayet->ekYetkililer ?: collect();
                Log::info("Müşteri/Ek İlgili Sayısı: " . ($customerUsers->count() + $ekYetkililer->count()));
                $recipients = $recipients->merge($customerUsers)->merge($ekYetkililer);
            } else {
                Log::warning("Proje #{$iaa->id} için müşteri şikayeti bağlantısı bulunamadı.");
            }

            // 2. Squad Takım Üyeleri
            $squadMembers = $iaa->projeEkibi ?: collect();
            Log::info("Squad Üye Sayısı: " . $squadMembers->count());
            $recipients = $recipients->merge($squadMembers);

            // 3. Bölüm Lideri ve Kalite Yöneticisi (Bölüm bazlı)
            $bolumId = $iaa->bolum_id;
            if (!$bolumId && $iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori) {
                $bolumId = $iaa->musteriSikayeti->sikayetKategori->bolum_id;
            }

            if ($bolumId) {
                // Bölüm Liderleri (Emrah Al vs.)
                $bolumLiderleri = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $bolumId)->get();
                Log::info("Bölüm Lideri Sayısı: " . $bolumLiderleri->count());
                $recipients = $recipients->merge($bolumLiderleri);

                // Kalite Yöneticileri (Serkan Tölek vs.)
                // Mantık: Hem bölüme ait olanlar hem de kategoriye özel yetkisi olanlar
                $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                    ->where(function ($q) use ($bolumId, $sikayet) {
                        $q->where('bolum_id', $bolumId);
                        if ($sikayet && $sikayet->sikayet_kategorisi_id) {
                            $q->orWhereHas('yonettigiSikayetKategorileri', function ($sq) use ($sikayet) {
                                $sq->where('sikayet_kategori_id', $sikayet->sikayet_kategorisi_id);
                            });
                        }
                    })
                    ->get();
                Log::info("Kalite Yöneticisi Sayısı: " . $kaliteYoneticileri->count());
                $recipients = $recipients->merge($kaliteYoneticileri);
            } else {
                Log::warning("Proje #{$iaa->id} için bir bölüm ID'si tespit edilemedi.");
            }

            // 4. Müşteri Şikayeti Kurulu Üyeleri (Global her zaman bilgilendirilirler)
            $kurulUyeleri = \App\Models\User::role('Müşteri Şikayeti Kurulu')->get();
            Log::info("Kurul Üyesi Sayısı: " . $kurulUyeleri->count());
            $recipients = $recipients->merge($kurulUyeleri);

            // İşlemi yapan kullanıcıyı listeden çıkar ve tekilleştir
            $finalRecipients = $recipients->reject(fn($u) => $u->id == $currentUser->id)->unique('id');

            Log::info("Toplam Filtrelenmiş Alıcı Sayısı: " . $finalRecipients->count());

            if ($finalRecipients->isEmpty()) {
                Log::warning("Bildirim gönderilecek alıcı bulunamadı (Kendisi hariç).");
                return;
            }

            // İlerleme bilgisini hesapla
            $snapshot = $assignment->workflow_snapshot;
            $stepList = is_array($snapshot) ? $snapshot : (json_decode($snapshot, true) ?: []);
            $totalCount = count($stepList);
            $completedCount = IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
                ->whereNotNull('completed_at')
                ->count();

            Log::info("Bildirim Gönderiliyor: {$completedCount}/{$totalCount}");

            // Bildirimi gönder
            Notification::send($finalRecipients, new \App\Notifications\ProjeAdimiTamamlandiMusteri($iaa, $step, $completedCount, $totalCount));
            Log::info("Bildirim Gönderimi Tamamlandı.");

        } catch (\Exception $e) {
            Log::error('Paydaş ilerleme bildirimi hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }


    /**
     * Adımı Yeniden Açma
     */
    public function reopenStep(IaaProgressUpdate $progressUpdate)
    {
        $assignment = DB::table('iaa_talepleri')->find($progressUpdate->iaa_talep_id);
        $iaa = Iaa::find($assignment->iaa_id);

        if (!$this->calismaAlaniService->authorizeUser($iaa)) {
            abort(403, 'Bu adımı düzenleme yetkiniz yok.');
        }

        // Durum Kilidi
        $kilitliDurumlar = ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı'];
        if (in_array($iaa->durum, $kilitliDurumlar)) {
            throw new \Exception('Proje onay aşamasında veya tamamlandığı için değişiklik yapılamaz.');
        }

        $progressUpdate->update(['completed_at' => null]);
        DB::table('iaa_talepleri')->where('id', $assignment->id)->update(['status' => 'Devam Ediyor']);

        // Loglama
        $progressUpdate->load('step');
        $stepName = $progressUpdate->step ? $progressUpdate->step->name : 'Bilinmeyen Adım';

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Proje Adımı Yeniden Açıldı',
            'aciklama' => Auth::user()->name . " adlı kullanıcı, '" . $stepName . "' adımını yeniden düzenlemek için açtı."
        ]);

        return $iaa;
    }

    /**
     * Zorla Kapatma (İptal)
     */
    public function cancelReopenStep($id)
    {
        $update = DB::table('iaa_progress_updates')->where('id', $id)->first();
        if (!$update) {
            throw new \Exception('Kayıt bulunamadı.');
        }

        $assignment = DB::table('iaa_talepleri')->where('id', $update->iaa_talep_id)->first();

        DB::table('iaa_progress_updates')
            ->where('id', $id)
            ->update([
                'completed_at' => now(),
                'updated_at' => now()
            ]);

        return $assignment->iaa_id;
    }

    /**
     * Adıma Sorumlu Atama
     */
    public function assignUser(Request $request, $iaaId, $stepId)
    {
        $iaa = Iaa::with('atananTakim', 'projeEkibi')->findOrFail($iaaId);
        $step = IaaWorkflowStep::findOrFail($stepId);
        $liderId = $iaa->atananTakim->lider_user_id;
        $currentUser = Auth::user();

        // Yetki: Sadece Lider veya Admin
        if ($currentUser->id != $liderId && !$currentUser->hasRole('Superadmin')) {
            abort(403, 'Atama yapma yetkiniz yok.');
        }

        $targetUserId = $request->input('user_id');
        $mesaj = "";

        if ($targetUserId) {
            DB::table('iaa_step_assignments')->updateOrInsert(
                ['iaa_id' => $iaa->id, 'iaa_workflow_step_id' => $step->id],
                [
                    'user_id' => $targetUserId,
                    'assigned_by' => $currentUser->id,
                    'updated_at' => now(),
                    // created_at ilk insertte lazım, burada basitçe current time basıyoruz, 
                    // eğer update ise zaten değişmez (MySQL) veya değişir (farketmez).
                    // Strict için insert durumunu kontrol etmek gerekebilir ama şimdilik bu yeterli.
                    'created_at' => now()
                ]
            );

            $sorumlu = \App\Models\User::find($targetUserId);
            $mesaj = "Adım sorumlusu '{$sorumlu->name}' olarak güncellendi.";

            // Bildirimler
            $this->notifyUserAndManager($sorumlu, new AdimSorumlusuAtandi($iaa, $step, $sorumlu, $currentUser));

            $ekip = $iaa->projeEkibi->merge([$iaa->atananTakim->lider]);
            $notifyList = $ekip->filter(fn($u) => $u->id != $currentUser->id);

            if ($notifyList->isNotEmpty()) {
                Notification::send($notifyList, new AdimSorumlusuAtandi($iaa, $step, $sorumlu, $currentUser));
            }

        } else {
            DB::table('iaa_step_assignments')
                ->where('iaa_id', $iaa->id)
                ->where('iaa_workflow_step_id', $step->id)
                ->delete();
            $mesaj = "Adım ataması kaldırıldı, herkes işlem yapabilir.";
        }

        return $mesaj;
    }

    /**
     * Adım Gizliliğini Değiştir (Müşteri İçin)
     */
    public function toggleVisibility($iaaId, $stepId)
    {
        $assignment = DB::table('iaa_talepleri')->where('iaa_id', $iaaId)->first();
        if (!$assignment) {
            throw new \Exception('Proje ataması bulunamadı.');
        }

        // Yetki kontrolü (Basitçe personel mi diye bakıyoruz, detaylısı controller/middleware'de olabilir ama burada da yapmak iyi)
        if (!Auth::check() || !Auth::user()->is_personnel) {
            abort(403, 'Yetkisiz işlem.');
        }

        $update = IaaProgressUpdate::firstOrCreate(
            [
                'iaa_talep_id' => $assignment->id,
                'iaa_workflow_step_id' => $stepId,
            ],
            [
                'user_id' => Auth::id(), // Oluşturan olarak şimdilik işlem yapanı atayalım
                // 'content' => null, // İçerik henüz yoksa null kalabilir
            ]
        );

        $update->is_hidden_from_customer = !$update->is_hidden_from_customer;
        $update->save();

        // Loglama
        $step = IaaWorkflowStep::find($stepId);
        $action = $update->is_hidden_from_customer ? 'Gizlendi' : 'Açık';
        $logMessage = Auth::user()->name . " " . $step->name . " adımını müşteriye " . ($update->is_hidden_from_customer ? "gizlemek" : "göstermek") . " için " . $action . " butonuna bastı.";

        IaaLog::create([
            'iaa_id' => $iaaId,
            'user_id' => Auth::id(),
            'eylem' => 'Adım Görünürlüğü Değiştirildi',
            'aciklama' => $logMessage
        ]);

        return $update->is_hidden_from_customer ? 'Adım müşteriden gizlendi.' : 'Adım müşteriye görünür yapıldı.';
    }

    private function checkStepResponsibility(Iaa $iaa, IaaWorkflowStep $step)
    {
        $assignmentRecord = DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa->id)
            ->where('iaa_workflow_step_id', $step->id)
            ->first();

        if ($assignmentRecord) {
            $liderId = $iaa->atananTakim->lider_user_id ?? 0;
            $userId = Auth::id();

            if ($assignmentRecord->user_id != $userId && $userId != $liderId && !Auth::user()->hasRole('Superadmin')) {
                $sorumluUser = \App\Models\User::find($assignmentRecord->user_id);
                abort(403, "Bu adım '{$sorumluUser->name}' kullanıcısına atanmıştır. Sadece sorumlu kişi veya lider tamamlayabilir.");
            }
        }
    }
}
