<?php

namespace App\Http\Controllers;

use App\Models\Iaa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\IaaProgressUpdate;
use App\Models\IaaWorkflowStep;
use App\Models\IaaLog;

class ProjectWorkspaceController extends Controller
{
    public function show(Iaa $iaa)
    {
        // === GÜNCELLEME: Şikayet detaylarını da yükle ===
        $iaa->load([
            'musteriSikayeti.dosyalar', 
            'musteriSikayeti.sikayetKategori'
        ]);
        // === GÜNCELLEME SONU ===
        $takim = $iaa->atananTakim;
        // === YETKİ GÜNCELLEMESİ: Müşteri Şikayeti Kurulu eklendi ===
        abort_if(
            !Auth::user()->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu']) && // Admin VEYA Kurul değilse VE
            (!$takim || !Auth::user()->takimlar->contains($takim)), // Takım üyesi değilse
            403, 
            'Bu projeyi görüntüleme yetkiniz yok.'
        );
        // === YETKİ GÜNCELLEMESİ SONU ===

        $assignment = DB::table('iaa_talepleri')
                        ->where('iaa_id', $iaa->id)
                        ->where('takim_id', $takim->id)
                        ->first();
        abort_if(!$assignment, 404, 'Proje atama kaydı bulunamadı.');

        $workflow = \App\Models\IaaWorkflow::with('steps')->find($assignment->iaa_workflow_id);
        $steps = $workflow->steps;

        // ================== DEĞİŞİKLİK BURADA BAŞLIYOR ==================
        
        // 1. Progress güncellemelerini normal bir koleksiyon olarak al
        $allProgressUpdates = \App\Models\IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
                                                           ->get();

        // 2. Tamamlanmış adımların ID'lerini bu normal koleksiyondan al
        //    (whereNotNull filtresi + pluck)
        $completedStepIds = $allProgressUpdates->whereNotNull('completed_at')
                                               ->pluck('iaa_workflow_step_id') 
                                               ->toArray();

        // 3. View'de kullanmak için anahtarlı (keyed) versiyonunu oluştur
        //    (Tüm güncellemeleri kullanarak)
        $progressUpdates = $allProgressUpdates->keyBy('iaa_workflow_step_id');
        
        // ================== DEĞİŞİKLİK SONU ==================
        
        $isTeamMember = Auth::user()->takimlar->contains($takim);

        $totalStepsCount = $steps->count();
        $completedStepsCount = count($completedStepIds);
        $progressPercentage = $totalStepsCount > 0 ? ($completedStepsCount / $totalStepsCount) * 100 : 0;

        // ======================== YENİ EKLENEN TARİH SORGUSU BAŞLANGICI ========================

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
            // "Tamamlandı" durumunun tarihi, kendi özel sütununda tutulduğu için oradan alıyoruz.
            $statusDate = $iaa->onaylanma_tarihi;
        }

        // ================== LOG SORGUSU GÜNCELLEMESİ ==================
        // TÜM logları çek (Modal için)
        $tumProjeLoglari = IaaLog::where('iaa_id', $iaa->id)
                            ->whereIn('eylem', [
                                'Proje Adımı Tamamlandı', 
                                'Proje Adımı Yeniden Açıldı',
                                'Revizyon Talep Edildi', 
                                'Proje Onaylandı',
                                'Onay Geri Alındı',
                                // Belki eklemek istersin:
                                // 'Tamamlanmış Projenin Reddi', 
                            ])
                            ->with('user') 
                            ->latest()     
                            ->get();
                            
        // Sadece son 10 logu al (İlk tablo için)
        $sonOnLoglar = $tumProjeLoglari->take(5);
        // ================== GÜNCELLEME SONU ==================

        // dd($projeLoglari); // Debug satırını kaldırdığından emin ol!

        return view('proje-calisma-alani.show', compact(
            'iaa',
            'takim',
            'assignment',
            'workflow',
            'steps',
            'completedStepIds',
            'progressUpdates',
            'totalStepsCount',
            'completedStepsCount',
            'progressPercentage',
            'isTeamMember',
            'statusDate',
            'tumProjeLoglari', // <-- TÜM logları gönder (Modal için)
            'sonOnLoglar'      // <-- Son 10 logu gönder (Tablo için)
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
