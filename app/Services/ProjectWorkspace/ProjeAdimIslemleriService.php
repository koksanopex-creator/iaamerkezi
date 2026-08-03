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

        if (!$assignment)
        {
            abort(404, 'Proje ataması bulunamadı.');
        }

        // Yetki Kontrolü (Servis üzerinden)
        if (!$this->calismaAlaniService->authorizeUser($iaa))
        {
            abort(403, 'Bu projeye müdahale etme yetkiniz yok.');
        }

        // Sorumluluk Kontrolü
        $this->checkStepResponsibility($iaa, $step);

        // Ziyaret Kontrolü: Bu adıma ait onay bekleyen veya tamamlanmamış bir ziyaret varsa adım kapatılamaz.
        $pendingVisit = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaa->id)
            ->where('iaa_workflow_step_id', $step->id)
            ->whereNotIn('status', ['Tamamlandı', 'İptal Edildi'])
            ->exists();

        if ($pendingVisit) {
            abort(403, 'Bu adıma ait onay bekleyen veya henüz tamamlanmamış bir müşteri ziyareti planı bulunmaktadır. Adımı kapatabilmek için ziyaretin tamamlanmasını beklemelisiniz.');
        }

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
        if ($isFirstCompletion)
        {
            defer(function () use ($iaa, $step, $assignment) {
                $this->notifyStakeholdersAboutProgress($iaa, $step, $assignment);
            });
        }

        // Bildirim (Takım üyelerine)
        $guncelleyenKullanici = Auth::user();
        defer(function () use ($iaa, $step, $assignment, $guncelleyenKullanici) {
            try
            {
                $takim = \App\Models\Takim::find($assignment->takim_id);
                if ($takim)
                {
                    $bildirimAlacaklar = $takim->users->where('id', '!=', $guncelleyenKullanici->id);

                    if ($bildirimAlacaklar->isNotEmpty())
                    {
                        Notification::send($bildirimAlacaklar, new ProjeAdimiGuncellendi($iaa, $step, $guncelleyenKullanici));
                    }
                }
            }
            catch (\Exception $e)
            {
                // Bildirim hatası süreci durdurmasın, loglansın ve tekrar denenebilsin
                \App\Helpers\MailLogHelper::logFailure(
                    $iaa,
                    '"' . $iaa->baslik . '" projesinde ekip bilgilendirmesi gönderilemedi',
                    $bildirimAlacaklar ?? collect(),
                    $e->getMessage(),
                    \App\Notifications\ProjeAdimiGuncellendi::class,
                    [
                        'recipient_ids' => ($bildirimAlacaklar ?? collect())->pluck('id')->toArray(),
                        'params' => ['iaa' => $iaa, 'step' => $step, 'user' => $guncelleyenKullanici]
                    ],
                    $iaa->bolum_id
                );
            }
        });

        return $iaa;
    }

    /**
     * Tüm paydaşlara (Müşteri, Kalite, Bölüm Lideri, Squad) ilerleme bildirimi gönderir
     */
    public function notifyStakeholdersAboutProgress(Iaa $iaa, IaaWorkflowStep $step, $assignment)
    {
        try
        {
            Log::info("Bildirim Süreci Başladı: Proje #{$iaa->id}, Adım: {$step->name}");

            $currentUser = Auth::user();
            $recipients = collect();

            // 1. Müşteri Temsilcileri ve Ek İlgililer
            $sikayet = $iaa->musteriSikayeti;
            if ($sikayet)
            {
                $customerUsers = \App\Models\User::where('customer_id', $sikayet->customer_id)->get();
                $ekYetkililer = $sikayet->ekYetkililer ?: collect();
                Log::info("Müşteri/Ek İlgili Sayısı: " . ($customerUsers->count() + $ekYetkililer->count()));
                $recipients = $recipients->merge($customerUsers)->merge($ekYetkililer);
            }
            else
            {
                Log::warning("Proje #{$iaa->id} için müşteri şikayeti bağlantısı bulunamadı.");
            }

            // 2. Squad Takım Üyeleri
            $squadMembers = $iaa->projeEkibi ?: collect();
            Log::info("Squad Üye Sayısı: " . $squadMembers->count());
            $recipients = $recipients->merge($squadMembers);

            // 3. Bölüm Lideri ve Kalite Yöneticisi (Bölüm bazlı)
            $bolumId = $iaa->bolum_id;
            if (!$bolumId && $iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori)
            {
                $bolumId = $iaa->musteriSikayeti->sikayetKategori->bolum_id;
            }

            if ($bolumId)
            {
                // Bölüm Liderleri (Emrah Al vs.)
                $bolumLiderleri = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $bolumId)->get();
                Log::info("Bölüm Lideri Sayısı: " . $bolumLiderleri->count());
                $recipients = $recipients->merge($bolumLiderleri);

                // Kalite Yöneticileri (Serkan Tölek vs.)
                // Mantık: Hem bölüme ait olanlar hem de kategoriye özel yetkisi olanlar
                $kaliteYoneticileri = \App\Models\User::role('Bölüm Kalite Yöneticisi')
                    ->where(function ($q) use ($bolumId, $sikayet)
                    {
                        $q->where('bolum_id', $bolumId);
                        if ($sikayet && $sikayet->sikayet_kategorisi_id)
                        {
                            $q->orWhereHas('yonettigiSikayetKategorileri', function ($sq) use ($sikayet)
                            {
                                $sq->where('sikayet_kategori_id', $sikayet->sikayet_kategorisi_id);
                            });
                        }
                    })
                    ->get();
                Log::info("Kalite Yöneticisi Sayısı: " . $kaliteYoneticileri->count());
                $recipients = $recipients->merge($kaliteYoneticileri);
            }
            else
            {
                Log::warning("Proje #{$iaa->id} için bir bölüm ID'si tespit edilemedi.");
            }

            // 4. Müşteri Şikayeti Kurulu Üyeleri (Global her zaman bilgilendirilirler)
            $kurulUyeleri = \App\Models\User::role('Müşteri Şikayeti Kurulu')->get();
            Log::info("Kurul Üyesi Sayısı: " . $kurulUyeleri->count());
            $recipients = $recipients->merge($kurulUyeleri);

            // İşlemi yapan kullanıcıyı listeden çıkar ve tekilleştir
            $finalRecipients = $recipients->reject(fn($u) => $u->id == $currentUser->id)->unique('id');

            Log::info("Toplam Filtrelenmiş Alıcı Sayısı: " . $finalRecipients->count());

            if ($finalRecipients->isEmpty())
            {
                Log::warning("Bildirim gönderilecek alıcı bulunamadı (Kendisi hariç).");
                return;
            }

            // İlerleme bilgisini hesapla
            $snapshot = $assignment->workflow_snapshot;
            $stepList = is_array($snapshot) ? $snapshot : (json_decode($snapshot, true) ?: []);
            $totalCount = count($stepList);
            $completedCount = IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
                ->whereNotNull('completed_at')
                ->pluck('iaa_workflow_step_id')
                ->unique()
                ->count();

            Log::info("Bildirim Gönderiliyor: {$completedCount}/{$totalCount}");

            // Bildirimi gönder
            Notification::send($finalRecipients, new \App\Notifications\ProjeAdimiTamamlandiMusteri($iaa, $step, $completedCount, $totalCount));
            Log::info("Bildirim Gönderimi Tamamlandı.");

        }
        catch (\Exception $e)
        {
            Log::error('Paydaş ilerleme bildirimi hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            \App\Helpers\MailLogHelper::logFailure(
                $iaa,
                '"' . $iaa->baslik . '" projesinde "' . $step->name . '" adımı paydaş bildirimi gönderilemedi',
                $finalRecipients ?? collect(),
                $e->getMessage(),
                null,
                null,
                $iaa->bolum_id
            );
        }
    }


    /**
     * Adımı Yeniden Açma
     */
    public function reopenStep(IaaProgressUpdate $progressUpdate)
    {
        $assignment = DB::table('iaa_talepleri')->find($progressUpdate->iaa_talep_id);
        $iaa = Iaa::find($assignment->iaa_id);

        // Yetki: Sadece Lider, Admin, Müdahale Yetkili Kalite Yöneticisi VEYA Adım Sorumlusu
        $isQualityManager = $this->calismaAlaniService->isQualityManagerWithInterventionPower(Auth::user(), $iaa);
        $isLeader = $iaa->atananTakim && $iaa->atananTakim->lider_user_id == Auth::id();
        $isSuperAdmin = Auth::user()->hasRole('Superadmin');
        
        $userId = Auth::id();
        $isAssigned = DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa->id)
            ->where('iaa_workflow_step_id', $progressUpdate->iaa_workflow_step_id)
            ->where('user_id', $userId)
            ->exists();
            
        if (!$isAssigned && $progressUpdate->content) {
            $contentDecoded = json_decode($progressUpdate->content, true);
            $formData = $contentDecoded['form_data'] ?? [];
            foreach ($formData as $data) {
                if (isset($data['user_ids']) && is_array($data['user_ids']) && in_array($userId, $data['user_ids'])) {
                    $isAssigned = true;
                    break;
                }
            }
        }

        if (!$isLeader && !$isSuperAdmin && !$isQualityManager && !$isAssigned)
        {
            abort(403, 'Bu adımı geri alma yetkiniz yok. Sadece ekip lideri, ilgili adım sorumlusu veya müdahale yetkili kalite yöneticisi bu işlemi yapabilir.');
        }

        // --- SONRADAN DAHİL OLAN SORUMLU İÇİN KİLİT ---
        $isOrdinaryAssignee = !$isLeader && !$isSuperAdmin && !$isQualityManager && $isAssigned;
        if ($isOrdinaryAssignee) {
            $step = IaaWorkflowStep::find($progressUpdate->iaa_workflow_step_id);
            if ($step && isset($step->order)) {
                $subsequentCompleted = \App\Models\IaaProgressUpdate::where('iaa_talep_id', $progressUpdate->iaa_talep_id)
                    ->whereNotNull('completed_at')
                    ->whereHas('step', function($q) use ($step) {
                        $q->where('order', '>', $step->order);
                    })
                    ->exists();

                if ($subsequentCompleted) {
                    abort(403, 'Bir sonraki adım tamamlandığı için bu adımı düzenleme yetkiniz kapanmıştır.');
                }
            }
        }
        // ----------------------------------------------

        // Durum Kilidi
        $kilitliDurumlar = ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı'];
        if (in_array($iaa->durum, $kilitliDurumlar))
        {
            throw new \Exception('Proje onay aşamasında veya tamamlandığı için değişiklik yapılamaz. Müdahale etmek için önce onayı geri çekmeniz gerekir.');
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
     * Adımı Geri Al (Tamamen Sil/Sıfırla)
     */
    public function undoStep(IaaProgressUpdate $progressUpdate)
    {
        $assignment = DB::table('iaa_talepleri')->find($progressUpdate->iaa_talep_id);
        $iaa = Iaa::find($assignment->iaa_id);

        // Yetki Kontrolü: Lider, Admin, Müdahale Yetkili Kalite Yöneticisi VEYA Adım Sorumlusu
        $isQualityManager = $this->calismaAlaniService->isQualityManagerWithInterventionPower(Auth::user(), $iaa);
        $isLeader = $iaa->atananTakim && $iaa->atananTakim->lider_user_id == Auth::id();
        $isSuperAdmin = Auth::user()->hasRole('Superadmin');
        
        $userId = Auth::id();
        $isAssigned = DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa->id)
            ->where('iaa_workflow_step_id', $progressUpdate->iaa_workflow_step_id)
            ->where('user_id', $userId)
            ->exists();
            
        if (!$isAssigned && $progressUpdate->content) {
            $contentDecoded = json_decode($progressUpdate->content, true);
            $formData = $contentDecoded['form_data'] ?? [];
            foreach ($formData as $data) {
                if (isset($data['user_ids']) && is_array($data['user_ids']) && in_array($userId, $data['user_ids'])) {
                    $isAssigned = true;
                    break;
                }
            }
        }

        if (!$isLeader && !$isSuperAdmin && !$isQualityManager && !$isAssigned)
        {
            abort(403, 'Bu adımı geri alma (silme) yetkiniz yok. Sadece ekip lideri, ilgili adım sorumlusu veya müdahale yetkili kalite yöneticisi bu işlemi yapabilir.');
        }

        // Kronolojik Kilit Kontrolü: Kendisinden SONRA tamamlanmış adım var mı?
        $step = IaaWorkflowStep::find($progressUpdate->iaa_workflow_step_id);
        if ($step && isset($step->order)) {
            $subsequentCompleted = \App\Models\IaaProgressUpdate::where('iaa_talep_id', $progressUpdate->iaa_talep_id)
                ->whereNotNull('completed_at')
                ->whereHas('step', function($q) use ($step) {
                    $q->where('order', '>', $step->order);
                })
                ->exists();

            if ($subsequentCompleted) {
                throw new \Exception('Sadece en son tamamlanmış adımı geri alabilirsiniz. Bir sonraki adım tamamlandığı için bu adımı geri alamazsınız.');
            }
        }

        // Durum Kilidi: Proje onay aşamasında veya tamamlanmışsa engelle
        $kilitliDurumlar = ['Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlandı'];
        if (in_array($iaa->durum, $kilitliDurumlar))
        {
            throw new \Exception('Proje onay aşamasında veya tamamlandığı için işlem yapılamaz. Geri alma işlemi için önce onayı geri çekmeniz gerekir.');
        }

        // Dosyaları Fiziksel Olarak Silme
        if ($progressUpdate->content) {
            $contentDecoded = json_decode($progressUpdate->content, true);
            $formData = $contentDecoded['form_data'] ?? [];
            foreach ($formData as $data) {
                // file_upload veya image_upload widget'larından gelen "files" array'i
                if (isset($data['files']) && is_array($data['files'])) {
                    foreach ($data['files'] as $filePath) {
                        if (\Illuminate\Support\Facades\Storage::exists($filePath)) {
                            \Illuminate\Support\Facades\Storage::delete($filePath);
                        }
                    }
                }
                // before_after widget'ından gelen resimler
                if (isset($data['before_image_path']) && \Illuminate\Support\Facades\Storage::exists($data['before_image_path'])) {
                    \Illuminate\Support\Facades\Storage::delete($data['before_image_path']);
                }
                if (isset($data['after_image_path']) && \Illuminate\Support\Facades\Storage::exists($data['after_image_path'])) {
                    \Illuminate\Support\Facades\Storage::delete($data['after_image_path']);
                }
            }
        }

        // Adıma ve sonraki adımlara bağlı Ziyaret Planlarını sil (Hiyerarşik)
        $visits = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaa->id)
            ->where('iaa_workflow_step_id', '>=', $progressUpdate->iaa_workflow_step_id)
            ->get();

        foreach ($visits as $visit) {
            // Takvim'e silme isteği at
            try {
                $takvimUrl = rtrim(config('services.takvim.url'), '/');
                \Illuminate\Support\Facades\Http::timeout(5)->post($takvimUrl . '/api/visits/delete', [
                    'remote_id' => $iaa->id
                ]);
            } catch (\Exception $e) {
                Log::error('Takvim visit delete error on undo step: ' . $e->getMessage());
            }

            $visit->delete();
        }

        // Adımı Sil (IaaProgressUpdate veritabanından kaldır)
        $progressUpdate->delete();

        // Eğer projedeki tüm ilerlemeler silindiyse, iaa_talepleri status'ünü kontrol et
        $anyCompleted = \App\Models\IaaProgressUpdate::where('iaa_talep_id', $assignment->id)
                ->whereNotNull('completed_at')
                ->exists();
        if (!$anyCompleted) {
            DB::table('iaa_talepleri')->where('id', $assignment->id)->update(['status' => 'Devam Ediyor']);
        }

        // Loglama (Superadmin/Yönetim/Kalite Yöneticisi için)
        $stepName = $step ? $step->name : 'Bilinmeyen Adım';

        IaaLog::create([
            'iaa_id' => $iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Proje Adımı Tamamen Geri Alındı (Silindi)',
            'aciklama' => Auth::user()->name . " adlı kullanıcı, '" . $stepName . "' adımını tamamen geri aldı (sıfırladı)."
        ]);

        return $iaa;
    }

    /**
     * Zorla Kapatma (İptal)
     */
    public function cancelReopenStep($id)
    {
        $update = DB::table('iaa_progress_updates')->where('id', $id)->first();
        if (!$update)
        {
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

        // Yetki: Sadece Lider, Admin veya Müdahale Yetkili Kalite Yöneticisi
        $isQualityManager = $this->calismaAlaniService->isQualityManagerWithInterventionPower($currentUser, $iaa);
        if ($currentUser->id != $liderId && !$currentUser->hasRole('Superadmin') && !$isQualityManager)
        {
            abort(403, 'Atama yapma yetkiniz yok.');
        }

        $targetUserIds = $request->input('user_ids', []); // Dizi olarak bekliyoruz
        if (!is_array($targetUserIds) && $request->has('user_id')) {
            $targetUserIds = [$request->input('user_id')]; // Geriye dönük uyumluluk
        }

        $mesaj = "";

        // Önce mevcut atamaları temizle
        DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa->id)
            ->where('iaa_workflow_step_id', $step->id)
            ->delete();

        if (!empty($targetUserIds))
        {
            $assignments = [];
            $sorumluUsers = \App\Models\User::whereIn('id', $targetUserIds)->get();

            foreach ($targetUserIds as $id) {
                if (!$id) continue;
                $assignments[] = [
                    'iaa_id' => $iaa->id,
                    'iaa_workflow_step_id' => $step->id,
                    'user_id' => $id,
                    'assigned_by' => $currentUser->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            if (!empty($assignments)) {
                DB::table('iaa_step_assignments')->insert($assignments);
                
                $sorumluNames = $sorumluUsers->pluck('name')->implode(', ');
                $mesaj = "Adım sorumluları '{$sorumluNames}' olarak güncellendi.";

                // BİLDİRİMLER:
                defer(function () use ($sorumluUsers, $iaa, $step, $currentUser, $targetUserIds) {
                    // 1. Atanan Her Bir Sorumluya ve Onların Bölüm Liderlerine Gönder
                    foreach ($sorumluUsers as $sorumlu) {
                        $this->notifyUserAndManager($sorumlu, new AdimSorumlusuAtandi($iaa, $step, $sorumlu, $currentUser));
                    }

                    // 2. Proje Ekibinin Geri Kalanına (Observer olarak) Bilgi Ver
                    $ekip = $iaa->projeEkibi->merge([$iaa->atananTakim->lider]);
                    $notifyList = $ekip->filter(function($u) use ($currentUser, $targetUserIds) {
                        return $u->id != $currentUser->id && !in_array($u->id, $targetUserIds);
                    })->unique('id');

                    if ($notifyList->isNotEmpty()) {
                        foreach ($sorumluUsers as $sorumlu) {
                            try {
                                // Sorumlu olmayan ekip üyelerine sadece veritabanı (Zil) bildirimi gitsin
                                Notification::send($notifyList, new AdimSorumlusuAtandi($iaa, $step, $sorumlu, $currentUser));
                            } catch (\Exception $e) {
                                \App\Helpers\MailLogHelper::logFailure(
                                    $iaa,
                                    'Adım Ataması Ekip Bilgilendirmesi',
                                    $notifyList,
                                    $e->getMessage(),
                                    \App\Notifications\AdimSorumlusuAtandi::class,
                                    [
                                        'recipient_ids' => $notifyList->pluck('id')->toArray(),
                                        'params' => [
                                            'iaa' => $iaa,
                                            'step' => $step,
                                            'sorumlu' => $sorumlu,
                                            'lider' => $currentUser
                                        ]
                                    ],
                                    $iaa->bolum_id
                                );
                            }
                        }
                    }
                });
            } else {
                $mesaj = "Adım ataması kaldırıldı, herkes işlem yapabilir.";
            }
        }
        else
        {
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
        if (!$assignment)
        {
            throw new \Exception('Proje ataması bulunamadı.');
        }

        // Yetki kontrolü (Basitçe personel mi diye bakıyoruz, detaylısı controller/middleware'de olabilir ama burada da yapmak iyi)
        if (!Auth::check() || !Auth::user()->is_personnel)
        {
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
        $assignments = DB::table('iaa_step_assignments')
            ->where('iaa_id', $iaa->id)
            ->where('iaa_workflow_step_id', $step->id)
            ->get();

        if ($assignments->isNotEmpty())
        {
            $liderId = $iaa->atananTakim->lider_user_id ?? 0;
            $userId = Auth::id();
            
            $assignedUserIds = $assignments->pluck('user_id')->toArray();

            $isQualityManager = $this->calismaAlaniService->isQualityManagerWithInterventionPower(Auth::user(), $iaa);
            if (!in_array($userId, $assignedUserIds) && $userId != $liderId && !Auth::user()->hasRole('Superadmin') && !$isQualityManager)
            {
                $sorumluNames = \App\Models\User::whereIn('id', $assignedUserIds)->pluck('name')->implode(', ');
                abort(403, "Bu adım '{$sorumluNames}' kullanıcısına/kullanıcılarına atanmıştır. Sadece sorumlu kişiler veya lider müdahale edebilir.");
            }
        }
    }
}
