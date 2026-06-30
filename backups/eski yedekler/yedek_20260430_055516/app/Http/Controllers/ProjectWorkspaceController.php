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
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectExport;

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
        $iaa = Iaa::with([
            'musteriSikayeti.customer.users', 
            'musteriSikayeti.yetkili_user',
            'musteriSikayeti.sikayetKategori.bolum.director',
            'atananTakim.lider', 
            'projeEkibi', 
            'talepEdenTakimlar.lider', 
            'talepEdenTakimlar.uyeler',
            'ziyaretPlani'
        ])->findOrFail($id);

        // 2. Yetki Kontrolü (Servis)
        $isAuthorized = $this->calismaAlaniService->authorizeUser($iaa);

        if (!$isAuthorized) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            if (Auth::user()->hasRole(['Müşteri', 'Müşteri Temsilcisi']) || Auth::user()->customer_id) {
                return redirect()->route('dashboard');
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

    /**
     * İade Notları Müşteri Görünürlüğünü Değiştir
     */
    public function toggleCustomerVisibility($id, Request $request)
    {
        try {
            $iade = \App\Models\SikayetIadesi::findOrFail($id);
            $iaa = Iaa::whereHas('musteriSikayeti', function($q) use ($iade) {
                $q->where('id', $iade->musteri_sikayeti_id);
            })->firstOrFail();

            // Yetki Kontrolü
            $isLeader = ($iaa->atananTakim && auth()->id() == $iaa->atananTakim->lider_user_id) || (auth()->check() && auth()->user()->hasRole('Superadmin'));
            if (!$isLeader) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Bu işlem için yetkiniz bulunmamaktadır.'], 403);
                }
                abort(403, 'Bu işlem için yetkiniz bulunmamaktadır.');
            }

            $iade->update([
                'musteri_gorebilir_mi' => !$iade->musteri_gorebilir_mi
            ]);

            $message = $iade->musteri_gorebilir_mi 
                ? 'İade notları ve belgeleri MÜŞTERİYE AÇILMIŞTIR.'
                : 'İade notları ve belgeleri MÜŞTERİDEN GİZLENMİŞTİR.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_visible' => !!$iade->musteri_gorebilir_mi
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'İşlem başarısız: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'İşlem başarısız: ' . $e->getMessage());
        }
    }

    // --- MÜŞTERİ BİLDİRİM İŞLEMLERİ ---

    public function notifyCustomer(Request $request, $id)
    {
        try {
            $recipients = $request->input('recipients', []);
            $result = $this->musteriBildirimService->notifyCustomer($id, $recipients);

            return back()
                ->with('success', $result['message'])
                ->with('generated_passwords', $result['passwords'] ?? []);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resetCustomerPassword(Request $request, $id)
    {
        try {
            $guestPasswordId = $request->input('guest_password_id');
            $result = $this->musteriBildirimService->resetCustomerPassword($id, $guestPasswordId);
            return back()
                ->with('success', $result['message'])
                ->with('generated_password', $result['password']);
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

    public function recallFaultyByQuality($id)
    {
        $result = $this->talepYonetimService->recallFaultyByQuality($id);
        return back()->with($result['status'], $result['message']);
    }

    public function recallFaultyByDirector($id)
    {
        try {
            $result = $this->talepYonetimService->recallFaultyByDirector($id);
            return back()->with($result['status'], $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function recallFaultyBySuperadmin($id)
    {
        try {
            $result = $this->talepYonetimService->recallFaultyBySuperadmin($id);
            return back()->with($result['status'], $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    // --- EK SÜRE TALEBİ YÖNETİMİ ---

    public function requestExtension(Request $request, $id)
    {
        try {
            $this->talepYonetimService->requestExtension($request, $id);
            return back()->with('success', 'Ek süre talebiniz ilgili yöneticiye başarıyla iletildi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function decideExtensionByDirector(Request $request, $id)
    {
        try {
            $result = $this->talepYonetimService->decideExtensionByDirector($request, $id);
            return back()->with($result['status'], $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function decideExtensionBySuperadmin(Request $request, $id)
    {
        try {
            $result = $this->talepYonetimService->decideExtensionBySuperadmin($request, $id);
            return back()->with($result['status'], $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    /**
     * Projeyi PDF olarak dışa aktarır.
     */
    public function exportPdf($id)
    {
        $iaa = Iaa::findOrFail($id);
        
        // Yetki Kontrolü
        if (!$this->calismaAlaniService->authorizeUser($iaa)) {
            abort(403);
        }

        $data = $this->calismaAlaniService->getProjectData($iaa);
        $data['logo'] = \App\Models\Setting::where('key', 'site_logo')->value('value');

        // İade Bilgileri
        $data['iade'] = null;
        if ($iaa->musteriSikayeti) {
            $data['iade'] = $iaa->musteriSikayeti->iadeler->first();
        }

        // Ziyaret Bilgileri (Takvim API'sinden)
        $data['visitData'] = $this->fetchVisitDataForExport($iaa);

        $pdf = Pdf::loadView('proje-calisma-alani.export.pdf', $data);
        
        $fileName = str_replace(' ', '_', $iaa->baslik) . '_Proje_Raporu.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Projeyi Excel (xlsx) olarak dışa aktarır.
     */
    public function exportExcel($id)
    {
        $iaa = Iaa::findOrFail($id);

        // Yetki Kontrolü
        if (!$this->calismaAlaniService->authorizeUser($iaa)) {
            abort(403);
        }

        $data = $this->calismaAlaniService->getProjectData($iaa);
        $data['logo'] = \App\Models\Setting::where('key', 'site_logo')->value('value');

        // İade Bilgileri
        $data['iade'] = null;
        if ($iaa->musteriSikayeti) {
            $data['iade'] = $iaa->musteriSikayeti->iadeler->first();
        }

        // Ziyaret Bilgileri (Takvim API'sinden)
        $data['visitData'] = $this->fetchVisitDataForExport($iaa);

        $fileName = str_replace(' ', '_', $iaa->baslik) . '_Proje_Raporu.xlsx';
        return Excel::download(new ProjectExport($data), $fileName);
    }

    /**
     * Takvim API'sinden ziyaret bilgilerini çeker (Export için).
     */
    private function fetchVisitDataForExport(Iaa $iaa): ?array
    {
        if (!$iaa->visit_planned || !$iaa->musteriSikayeti || !$iaa->musteriSikayeti->customer) {
            return null;
        }

        try {
            $baseUrl = rtrim(config('services.takvim.url', 'http://localhost:8001'), '/');
            $response = \Illuminate\Support\Facades\Http::get($baseUrl . '/api/customers/visit-data', [
                'customer_name' => $iaa->musteriSikayeti->customer->name,
                'remote_id' => $iaa->id
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['existing_visit'] ?? null;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Export visit data fetch error: " . $e->getMessage());
        }

        return null;
    }
}