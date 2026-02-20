<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\IaaProgressUpdate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProjeCalismaAlaniService
{
    /**
     * Proje detay sayfası için gerekli tüm verileri hazırlar.
     */
    public function getProjectData(Iaa $iaa)
    {
        $takim = $iaa->atananTakim;
        $assignment = DB::table('iaa_talepleri')->where('iaa_id', $iaa->id)->first();

        if (!$assignment) {
            return null; // Controller'da handle edilecek
        }

        $workflow = \App\Models\IaaWorkflow::with('steps')->find($assignment->iaa_workflow_id);
        $steps = $workflow->steps;

        $allProgressUpdates = IaaProgressUpdate::where('iaa_talep_id', $assignment->id)->get();
        $completedStepIds = $allProgressUpdates->whereNotNull('completed_at')->pluck('iaa_workflow_step_id')->toArray();
        $stepAssignments = DB::table('iaa_step_assignments')->where('iaa_id', $iaa->id)->get()->keyBy('iaa_workflow_step_id');
        $progressUpdates = $allProgressUpdates->keyBy('iaa_workflow_step_id');

        // İlerleme Yüzdesi
        $totalStepsCount = $steps->count();
        $completedStepsCount = count($completedStepIds);
        $progressPercentage = $totalStepsCount > 0 ? ($completedStepsCount / $totalStepsCount) * 100 : 0;

        // Takım Üyesi Kontrolü
        $isTeamMember = $this->isTeamMember($iaa);

        // Durum Tarihi ve Log Action
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
            $latestLog = IaaLog::where('iaa_id', $iaa->id)->where('eylem', $logAction)->latest('created_at')->first();
            if ($latestLog) {
                $statusDate = $latestLog->created_at;
            }
        } elseif ($iaa->durum === 'Tamamlandı') {
            $statusDate = $iaa->onaylanma_tarihi;
        }

        // Loglar
        $tumProjeLoglari = IaaLog::with('user')
            ->where('iaa_id', $iaa->id)
            ->latest()
            ->get();

        $sonOnLoglar = $tumProjeLoglari->take(5);

        // Edit Yetkisi
        $canEdit = $this->canEditProject($iaa);

        // Bölüm Özel Verileri (Makine, Hammadde vb.)
        $isComplaintProject = !is_null($iaa->musteriSikayeti);
        $machines = collect();
        $hammaddeler = collect();
        $versiyonlar = collect();

        if ($isComplaintProject && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum) {
            $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum;
            $machines = $bolum->machines()->where('status', '!=', 'inactive')->orderBy('name')->get();
            $hammaddeler = $bolum->genelHammaddeler()->where('aktif_mi', true)->orderBy('ad')->get();
            $versiyonlar = $bolum->urunVersiyonlari()->where('aktif_mi', true)->orderBy('ad')->get();
        }

        return compact(
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
            'tumProjeLoglari',
            'sonOnLoglar',
            'stepAssignments',
            'canEdit',
            'isComplaintProject',
            'machines',
            'hammaddeler',
            'versiyonlar'
        );
    }

    /**
     * Kullanıcının projeyi görüntüleme yetkisini kontrol eder.
     */
    public function authorizeUser(Iaa $iaa)
    {
        // 1. GİRİŞ YAPMIŞ KULLANICI KONTROLÜ
        if (Auth::check()) {
            $isYetkiliKullanici = $this->checkGeneralAuthorization($iaa);

            // Kurul Üyesi İzni (Read Only)
            if (!$isYetkiliKullanici && Auth::user()->hasRole('Müşteri Şikayeti Kurulu')) {
                return true;
            }

            // Bölüm Lideri Görünürlük İzni (Read Only - Bölüm Bağı)
            if (!$isYetkiliKullanici && Auth::user()->bolum_id) {
                $deptUserIds = \App\Models\User::where('bolum_id', Auth::user()->bolum_id)->pluck('id')->toArray();

                $teamMemberInDept = $iaa->projeEkibi()->whereIn('users.id', $deptUserIds)->exists();
                $teamLeaderInDept = $iaa->atananTakim && in_array($iaa->atananTakim->lider_user_id, $deptUserIds);

                if ($teamMemberInDept || $teamLeaderInDept) {
                    return true;
                }
            }

            if ($isYetkiliKullanici) {
                return true;
            }
        }

        // 2. MİSAFİR (MÜŞTERİ) KONTROLÜ
        $sikayet = $iaa->musteriSikayeti;
        if ($sikayet) {
            $sessionKey = 'sikayet_logged_in_' . $sikayet->takip_token;
            if (Session::has($sessionKey)) {
                return true;
            }
        }

        return false;
    }

    /**
     * İç Yetki Kontrolü (Private - Controller'daki checkAuthorization mantığı)
     */
    private function checkGeneralAuthorization(Iaa $iaa)
    {
        $user = Auth::user();

        // 1. Superadmin veya Yönetim
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            return true;
        }

        // 1.5. Direktör Yetkisi (Maksimum Kapsamlı Erişim)
        if ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();

            // 1. Doğrudan proje bölümü üzerinden
            if (in_array($iaa->bolum_id, $yonetilenBolumIds)) {
                return true;
            }

            // 2. Şikayet kategorisi departmanı üzerinden
            if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori && in_array($iaa->musteriSikayeti->sikayetKategori->bolum_id, $yonetilenBolumIds)) {
                return true;
            }

            // 3. Proje gönderen kişi üzerinden
            if ($iaa->gonderen && in_array($iaa->gonderen->bolum_id, $yonetilenBolumIds)) {
                return true;
            }

            // 4. Proje ekibi (Squad) üzerinden
            $teamMembersDeptIds = $iaa->projeEkibi()->pluck('users.bolum_id')->unique()->toArray();
            if (count(array_intersect($teamMembersDeptIds, $yonetilenBolumIds)) > 0) {
                return true;
            }

            // 5. Takım lideri üzerinden
            if ($iaa->atananTakim && $iaa->atananTakim->lider && in_array($iaa->atananTakim->lider->bolum_id, $yonetilenBolumIds)) {
                return true;
            }
        }

        // 2. Proje Sahibi
        if ($iaa->gonderen_user_id == $user->id) {
            return true;
        }

        // 3. Müşteri Yetkilisi
        if ($user->customer_id && $iaa->musteriSikayeti && $iaa->musteriSikayeti->customer_id == $user->customer_id) {
            return true;
        }

        // 4. Müşteri Şikayeti Kaynaklı Proje
        if ($iaa->musteriSikayeti) {
            // A) Takım Lideri
            if ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $user->id) {
                return true;
            }

            // B) Squad Üyesi (Onaylı veya Bekliyor)
            if ($iaa->projeEkibi()->where('user_id', $user->id)->whereIn('iaa_user.durum', ['onaylandi', 'bekliyor'])->exists()) {
                return true;
            }

            // C) Bölüm Kalite Yöneticisi
            if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                $sikayetKategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                if ($sikayetKategoriId && $user->yonettigiSikayetKategorileri->contains($sikayetKategoriId)) {
                    return true;
                }
                if ($user->bolum_id && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum_id == $user->bolum_id) {
                    return true;
                }
            }

            // D) Bölüm Lideri
            if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
                if ($iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum_id == $user->bolum_id) {
                    return true;
                }

                $bolumPersonelIdleri = \App\Models\User::where('bolum_id', $user->bolum_id)->pluck('id');
                if ($iaa->projeEkibi()->whereIn('users.id', $bolumPersonelIdleri)->exists()) {
                    return true;
                }
            }

            return false;
        }
        // 5. Standart İAA Projesi
        else {
            if ($user->takimlar->contains($iaa->atanan_takim_id)) {
                return true;
            }

            if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
                if ($iaa->bolum_id == $user->bolum_id) {
                    return true;
                }
                $bolumPersonelIdleri = \App\Models\User::where('bolum_id', $user->bolum_id)->pluck('id');
                if ($iaa->projeEkibi()->whereIn('users.id', $bolumPersonelIdleri)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isTeamMember(Iaa $iaa)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $squadUyesi = $iaa->projeEkibi->contains($user->id);
            $takimUyesi = $iaa->atanan_takim_id && $user->takimlar->contains('id', $iaa->atanan_takim_id);
            return $squadUyesi || $takimUyesi;
        }
        return false;
    }

    private function canEditProject(Iaa $iaa)
    {
        if (!Auth::check())
            return false;

        $user = Auth::user();
        if ($user->hasRole('Superadmin'))
            return true;

        // DİREKTÖR YETKİSİ
        if ($user->hasRole('Direktör')) {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            if (in_array($iaa->bolum_id, $yonetilenBolumIds)) {
                return true;
            }
        }

        if ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $user->id)
            return true;
        if ($iaa->gonderen_user_id == $user->id)
            return true;
        if ($iaa->projeEkibi()->where('user_id', $user->id)->wherePivot('durum', 'onaylandi')->exists())
            return true;

        return false;
    }

    /**
     * Müşteri Şikayeti Teknik Detaylarını Günceller
     */
    public function updateComplaintDetails(\Illuminate\Http\Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if (!$this->authorizeUser($iaa)) {
            abort(403, 'Yetkisiz erişim.');
        }

        // Sadece Lider veya Yönetici veya Direktör
        $isLeader = ($iaa->atananTakim && Auth::id() == $iaa->atananTakim->lider_user_id);
        $isDirector = false;
        if (Auth::user()->hasRole('Direktör')) {
            $yonetilenBolumIds = Auth::user()->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            if (in_array($iaa->bolum_id, $yonetilenBolumIds)) {
                $isDirector = true;
            }
        }

        if (!$isLeader && !$isDirector && !Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Bu bilgileri sadece takım lideri, direktör veya yönetici güncelleyebilir.');
        }

        if (!$iaa->musteriSikayeti) {
            throw new \Exception('Bu projeye bağlı bir müşteri şikayeti bulunamadı.');
        }

        $request->validate([
            'lot_no' => 'required|array',
            'lot_no.*' => 'nullable|string',
            'machine_id' => 'nullable|array',
            'genel_hammadde_id' => 'nullable|array',
            'urun_versiyonu_id' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $iaa) {
            $iaa->musteriSikayeti->teknikDetaylar()->delete();

            foreach ($request->lot_no as $key => $lotNo) {
                if (empty($lotNo) && empty($request->machine_id[$key]) && empty($request->genel_hammadde_id[$key])) {
                    continue;
                }

                $iaa->musteriSikayeti->teknikDetaylar()->create([
                    'lot_no' => $lotNo,
                    'machine_id' => $request->machine_id[$key] ?? null,
                    'genel_hammadde_id' => $request->genel_hammadde_id[$key] ?? null,
                    'urun_versiyonu_id' => $request->urun_versiyonu_id[$key] ?? null,
                ]);
            }

            IaaLog::create([
                'iaa_id' => $iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Şikayet Detayları Güncellendi',
                'aciklama' => Auth::user()->name . " tarafından üretim detayları (Lot, Makine vb.) güncellendi."
            ]);
        });

        return "Şikayet detayları başarıyla güncellendi.";
    }
}
