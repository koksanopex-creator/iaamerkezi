<?php

namespace App\Http\Controllers;

use App\Models\Iaa;
use App\Models\IaaProgressUpdate;
use App\Services\ProjectWorkspace\ProjeCalismaAlaniService;
use App\Services\ProjectWorkspace\ProjeAdimIslemleriService;
use App\Services\ProjectWorkspace\ProjeTalepYonetimService;
use App\Services\ProjectWorkspace\ProjeTamamlamaService;
use App\Services\ProjectWorkspace\MusteriBildirimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectWorkspaceController extends Controller
{
    private $calismaAlaniService;
    private $stepIslemleriService;
    private $talepYonetimService;
    private $tamamlamaService;
    private $musteriBildirimService;

    public function __construct(
        ProjeCalismaAlaniService $calismaAlaniService,
        ProjeAdimIslemleriService $stepIslemleriService,
        ProjeTalepYonetimService $talepYonetimService,
        ProjeTamamlamaService $tamamlamaService,
        MusteriBildirimService $musteriBildirimService
    ) {
        $this->calismaAlaniService = $calismaAlaniService;
        $this->stepIslemleriService = $stepIslemleriService;
        $this->talepYonetimService = $talepYonetimService;
        $this->tamamlamaService = $tamamlamaService;
        $this->musteriBildirimService = $musteriBildirimService;
    }

    /**
     * Proje çalışma alanını gösterir.
     */
    public function show($id)
    {
        // 1. Proje Varlığı
        $iaa = Iaa::with(['musteriSikayeti', 'atananTakim', 'projeEkibi', 'talepEdenTakimlar.lider', 'talepEdenTakimlar.uyeler'])->findOrFail($id);

        // 2. Yetki Kontrolü (Servis)
        $isAuthorized = $this->calismaAlaniService->authorizeUser($iaa);

        if (!$isAuthorized) {
            if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->takip_token) {
                return redirect()->route('public.sikayet.show', ['token' => $iaa->musteriSikayeti->takip_token])
                    ->with('error', 'Proje detaylarını görmek için lütfen şifrenizle giriş yapın.');
            }
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            abort(403, 'Bu projeye erişim yetkiniz bulunmamaktadır. (Squad veya Takım listesinde değilsiniz.)');
        }

        // 3. Verileri Hazırla
        $data = $this->calismaAlaniService->getProjectData($iaa);

        if (!$data) {
            return redirect()->route('dashboard')->with('error', 'Proje ataması bulunamadı.');
        }

        return view('proje-calisma-alani.show', $data);
    }

    /**
     * Adım Kaydetme İşlemi
     */
    public function storeStep(Request $request, $assignment_id, $step_id)
    {
        $iaa = $this->stepIslemleriService->completeStep($request, $assignment_id, $step_id);

        return redirect()->route('proje.workspace.show', $iaa->id)
            ->with('success', 'Adım başarıyla tamamlandı!');
    }

    /**
     * Adımı Yeniden Açma İşlemi
     */
    public function reopenStep(IaaProgressUpdate $progress_update)
    {
        try {
            $iaa = $this->stepIslemleriService->reopenStep($progress_update);
            return redirect()->route('proje.workspace.show', $iaa->id)
                ->with('success', 'Adım yeniden düzenlemeye açıldı.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Yanlışlıkla açılan adımı tekrar kapatır
     */
    public function cancelReopenStep($id)
    {
        try {
            $iaaId = $this->stepIslemleriService->cancelReopenStep($id);
            return redirect()->route('proje.workspace.show', $iaaId)->with('info', 'İşlem iptal edildi, adım kapatıldı.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Adım Atama
     */
    public function assignUserToStep(Request $request, $iaa_id, $step_id)
    {
        $message = $this->stepIslemleriService->assignUser($request, $iaa_id, $step_id);
        return back()->with('success', $message);
    }

    /**
     * Adım Gizliliğini Değiştir
     */
    public function toggleStepVisibility($iaa_id, $step_id)
    {
        try {
            $message = $this->stepIslemleriService->toggleVisibility($iaa_id, $step_id);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- MÜŞTERİ BİLDİRİM İŞLEMLERİ ---

    public function notifyCustomer(Request $request, $id)
    {
        try {
            $result = $this->musteriBildirimService->notifyCustomer($id);
            if (isset($result['password'])) {
                return back()->with('success', $result['message'])->with('generated_password', $result['password']);
            }
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resetCustomerPassword($id)
    {
        try {
            $result = $this->musteriBildirimService->resetCustomerPassword($id);
            return back()->with('success', $result['message'])->with('generated_password', $result['password']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- TALEP YÖNETİMİ ---

    public function markAsRequest(Request $request, $id)
    {
        $this->talepYonetimService->markAsRequest($request, $id);
        return back()->with('success', 'Talep bildirimi yapıldı, kalite yöneticisine iletildi.');
    }

    public function decideRequestByQuality(Request $request, $id)
    {
        $result = $this->talepYonetimService->decideRequestByQuality($request, $id);
        return back()->with($result['status'], $result['message']);
    }

    public function decideRequestBySuperadmin(Request $request, $id)
    {
        $result = $this->talepYonetimService->decideRequestBySuperadmin($request, $id);

        if (isset($result['redirect'])) {
            return redirect()->route($result['redirect'])->with($result['status'], $result['message']);
        }

        return back()->with($result['status'], $result['message']);
    }

    // --- PROJE TAMAMLAMA ---

    public function completeWithReturn(Request $request, $id)
    {
        $this->tamamlamaService->completeWithReturn($request, $id);
        return redirect()->route('proje.workspace.show', $id)->with('success', 'Proje tamamlandı ve bölüm onayına gönderildi.');
    }

    public function completeWithoutReturn(Request $request, $id)
    {
        $this->tamamlamaService->completeWithoutReturn($request, $id);
        return redirect()->route('proje.workspace.show', $id)->with('success', 'Proje tamamlandı ve bölüm onayına gönderildi.');
    }

    public function recallSubmission(Request $request, $id)
    {
        $this->tamamlamaService->recallSubmission($request, $id);
        return back()->with('success', 'Onay süreci iptal edildi, proje tekrar düzenlemeye açıldı.');
    }

    public function updateComplaintDetails(Request $request, $id)
    {
        try {
            $message = $this->calismaAlaniService->updateComplaintDetails($request, $id);
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    // --- HATALI BİLDİRİM (FAULTY NOTIFICATION) ---

    public function markAsFaulty(Request $request, $id)
    {
        $this->talepYonetimService->markAsFaulty($request, $id);
        return back()->with('success', 'Hatalı bildirim yapıldı, Kalite Yöneticisi onayına sunuldu.');
    }

    public function decideFaultyByQuality(Request $request, $id)
    {
        $result = $this->talepYonetimService->decideFaultyByQuality($request, $id);
        return back()->with($result['status'], $result['message']);
    }

    public function decideFaultyByDirector(Request $request, $id)
    {
        $result = $this->talepYonetimService->decideFaultyByDirector($request, $id);
        return back()->with($result['status'], $result['message']);
    }

    public function decideFaultyBySuperadmin(Request $request, $id)
    {
        $result = $this->talepYonetimService->decideFaultyBySuperadmin($request, $id);
        return back()->with($result['status'], $result['message']);
    }

    public function recallFaulty($id)
    {
        $result = $this->talepYonetimService->recallFaulty($id);
        return back()->with($result['status'], $result['message']);
    }
}