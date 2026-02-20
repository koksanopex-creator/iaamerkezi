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
        $validated = $request->validate([
            'content' => 'required|string|min:20',
        ]);

        $assignment = DB::table('iaa_talepleri')->find($assignmentId);
        $step = IaaWorkflowStep::find($stepId);
        $iaa = Iaa::find($assignment->iaa_id);

        // Yetki Kontrolü (Servis üzerinden)
        if (!$this->calismaAlaniService->authorizeUser($iaa)) {
            abort(403, 'Bu projeye müdahale etme yetkiniz yok.');
        }

        // Sorumluluk Kontrolü
        $this->checkStepResponsibility($iaa, $step);

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
