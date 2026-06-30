<?php

namespace App\Services\ProjectWorkspace;

use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\IaaProgressUpdate;
use App\Models\Setting;
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

        if (!$assignment)
        {
            return null; // Controller'da handle edilecek
        }

        $workflow = \App\Models\IaaWorkflow::with('steps')->find($assignment->iaa_workflow_id);
        $steps = collect();

        if ($assignment && !empty($assignment->workflow_snapshot))
        {
            $snapshotData = is_array($assignment->workflow_snapshot)
                ? $assignment->workflow_snapshot
                : json_decode($assignment->workflow_snapshot, true);

            foreach ($snapshotData as $stepData)
            {
                $step = new \App\Models\IaaWorkflowStep();
                $step->forceFill($stepData);
                $steps->push($step);
            }
        }
        else
        {
            $steps = $workflow ? $workflow->steps : collect();
        }

        $allProgressUpdates = IaaProgressUpdate::with('user')->where('iaa_talep_id', $assignment->id)->get();
        $completedStepIds = $allProgressUpdates->whereNotNull('completed_at')->pluck('iaa_workflow_step_id')->toArray();
        $stepAssignments = DB::table('iaa_step_assignments')->where('iaa_id', $iaa->id)->get()->groupBy('iaa_workflow_step_id');
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

        // Özel log aksiyonları için tarih belirle
        $specialLogActions = [
            'Revize Ediliyor' => 'Revizyon Talep Edildi',
            'Tamamlanması Reddedildi' => 'Tamamlanmış Projenin Reddi'
        ];

        if (isset($specialLogActions[$iaa->durum]))
        {
            $latestLog = IaaLog::where('iaa_id', $iaa->id)
                ->where('eylem', $specialLogActions[$iaa->durum])
                ->latest('created_at')
                ->first();
            if ($latestLog)
            {
                $statusDate = $latestLog->created_at;
            }
        }

        // Eğer hala null ise ve proje tamamlanmamışsa updated_at kullan (bekleme süresi için)
        if (!$statusDate)
        {
            if ($iaa->durum == 'Tamamlandı')
            {
                $statusDate = $iaa->real_completion_date ?? $iaa->updated_at;
            }
            else
            {
                $statusDate = $iaa->updated_at;
            }
        }

        // Loglar (Erişim Kısıtlamalı)
        $tumProjeLoglariQuery = IaaLog::with('user')->where('iaa_id', $iaa->id);

        $userCanViewExtensionLogs = false;
        if (Auth::check())
        {
            $user = Auth::user();
            $userCanViewExtensionLogs = $user->hasRole(['Superadmin', 'Direktör', 'Bölüm Kalite Yöneticisi']) ||
                ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $user->id);
        }

        $tumProjeLoglari = $tumProjeLoglariQuery->latest()->get();

        // Son 5 logu doğrudan al (take 5 zaten bunu yapar ama garantiye alalım)
        $sonOnLoglar = $tumProjeLoglari->take(5);
        // Edit Yetkisi
        $isQualityManagerInterventionPower = Auth::check() ? $this->isQualityManagerWithInterventionPower(Auth::user(), $iaa) : false;
        $canEdit = $this->canEditProject($iaa);

        // Bölüm Özel Verileri (Makine, Hammadde vb.)
        $isComplaintProject = !is_null($iaa->musteriSikayeti);
        $machines = collect();
        $hammaddeler = collect();
        $versiyonlar = collect();

        if ($isComplaintProject && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum)
        {
            $bolum = $iaa->musteriSikayeti->sikayetKategori->bolum;
            $machines = $bolum->machines()->where('status', '!=', 'inactive')->orderBy('name')->get();
            $hammaddeler = $bolum->genelHammaddeler()->where('aktif_mi', true)->orderBy('ad')->get();
            $versiyonlar = $bolum->urunVersiyonlari()->where('aktif_mi', true)->orderBy('ad')->get();
        }

        // Sadece Ek Süre ile ilgili logları ayır
        $ekSureLoglari = $tumProjeLoglari->filter(function ($log)
        {
            return str_contains($log->eylem, 'Ek Süre');
        })->values();

        // 4. Bölüm Liderleri (Bildirim Detayları İçin)
        $deptLeaders = collect();
        if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori)
        {
            $bolumId = $iaa->musteriSikayeti->sikayetKategori->bolum_id;
            $deptLeaders = \App\Models\User::role('Bölüm Lideri')->where('bolum_id', $bolumId)->get();
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
            'ekSureLoglari',
            'stepAssignments',
            'canEdit',
            'isComplaintProject',
            'machines',
            'hammaddeler',
            'versiyonlar',
            'deptLeaders',
            'isQualityManagerInterventionPower'
        );
    }
    /**
     * Kullanıcının projeyi görüntüleme yetkisini kontrol eder.
     */
    public function authorizeUser(Iaa $iaa)
    {
        // 1. GİRİŞ YAPMIŞ KULLANICI KONTROLÜ
        if (Auth::check())
        {
            $isYetkiliKullanici = $this->checkGeneralAuthorization($iaa);

            // Ziyaretçi Kontrolü (Ziyaretçi Atanan Kişi Giriş Yapabilsin)
            $visitorId = $iaa->ziyaretPlani ? $iaa->ziyaretPlani->visitor_id : null;
            if (!$isYetkiliKullanici && $visitorId && $visitorId == Auth::id())
            {
                return true;
            }

            // Kurul Üyesi İzni (Read Only)
            if (!$isYetkiliKullanici && Auth::user()->hasRole('Müşteri Şikayeti Kurulu'))
            {
                return true;
            }

            if ($isYetkiliKullanici)
            {
                return true;
            }
        }

        // 2. MİSAFİR (MÜŞTERİ) KONTROLÜ
        $sikayet = $iaa->musteriSikayeti;
        if ($sikayet)
        {
            $sessionKey = 'sikayet_logged_in_' . $sikayet->takip_token;
            if (Session::has($sessionKey))
            {
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
        if ($user->hasRole(['Superadmin', 'Yonetim']))
        {
            return true;
        }

        // 1.5. Direktör Yetkisi (Maksimum Kapsamlı Erişim)
        if ($user->hasRole('Direktör'))
        {
            $yonetilenBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();

            // 1. Doğrudan proje bölümü üzerinden
            if (in_array($iaa->bolum_id, $yonetilenBolumIds))
            {
                return true;
            }

            // 2. Şikayet kategorisi departmanı üzerinden
            if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->sikayetKategori && in_array($iaa->musteriSikayeti->sikayetKategori->bolum_id, $yonetilenBolumIds))
            {
                return true;
            }

            // 3. Proje gönderen kişi üzerinden
            if ($iaa->gonderen && in_array($iaa->gonderen->bolum_id, $yonetilenBolumIds))
            {
                return true;
            }

            // 4. Proje ekibi (Squad) üzerinden
            $teamMembersDeptIds = $iaa->projeEkibi()->whereIn('iaa_user.durum', ['onaylandi', 'bekliyor'])->pluck('users.bolum_id')->unique()->toArray();
            if (count(array_intersect($teamMembersDeptIds, $yonetilenBolumIds)) > 0)
            {
                return true;
            }

            // 5. Takım lideri üzerinden
            if ($iaa->atananTakim && $iaa->atananTakim->lider && in_array($iaa->atananTakim->lider->bolum_id, $yonetilenBolumIds))
            {
                return true;
            }
        }

        // 2. Proje Sahibi
        if ($iaa->gonderen_user_id == $user->id)
        {
            return true;
        }

        // 3. Müşteri Yetkilisi / Temsilcisi (Dış veya İç Firma Sorumlusu)
        if ($iaa->musteriSikayeti)
        {
            $customer_id = $iaa->musteriSikayeti->customer_id;
            // Kullanıcı bu firmanın temsilcisiyse (is_personnel farketmeksizin) veya firmaya bağlıysa
            if ($user->customer_id == $customer_id || $user->customers()->where('customers.id', $customer_id)->exists())
            {
                return true;
            }
        }

        // 3.5. Şikayete Atanan Müşteri Temsilcisi (İç/Personel)
        if ($iaa->musteriSikayeti && $iaa->musteriSikayeti->yetkili_user_id == $user->id)
        {
            return true;
        }

        // 4. Müşteri Şikayeti Kaynaklı Proje
        if ($iaa->musteriSikayeti)
        {
            // A) Takım Lideri
            if ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $user->id)
            {
                return true;
            }

            // B) Squad Üyesi (Onaylı veya Bekliyor)
            if ($iaa->projeEkibi()->where('user_id', $user->id)->whereIn('iaa_user.durum', ['onaylandi', 'bekliyor'])->exists())
            {
                return true;
            }

            // C) Bölüm Kalite Yöneticisi
            if ($user->hasRole('Bölüm Kalite Yöneticisi'))
            {
                $sikayetKategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
                if ($sikayetKategoriId && $user->yonettigiSikayetKategorileri->contains($sikayetKategoriId))
                {
                    return true;
                }
                if ($user->bolum_id && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum_id == $user->bolum_id)
                {
                    return true;
                }
            }

            // D) Bölüm Lideri
            if ($user->hasRole('Bölüm Lideri') && $user->bolum_id)
            {
                if ($iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum_id == $user->bolum_id)
                {
                    return true;
                }

                $bolumPersonelIdleri = \App\Models\User::where('bolum_id', $user->bolum_id)->pluck('id');
                if ($iaa->projeEkibi()->whereIn('iaa_user.durum', ['onaylandi', 'bekliyor'])->whereIn('users.id', $bolumPersonelIdleri)->exists())
                {
                    return true;
                }
            }

            return false;
        }
        // 5. Standart İAA Projesi
        else
        {
            if ($user->takimlar->contains($iaa->atanan_takim_id))
            {
                return true;
            }

            if ($user->hasRole('Bölüm Lideri') && $user->bolum_id)
            {
                if ($iaa->bolum_id == $user->bolum_id)
                {
                    return true;
                }
                $bolumPersonelIdleri = \App\Models\User::where('bolum_id', $user->bolum_id)->pluck('id');
                if ($iaa->projeEkibi()->whereIn('iaa_user.durum', ['onaylandi', 'bekliyor'])->whereIn('users.id', $bolumPersonelIdleri)->exists())
                {
                    return true;
                }
            }
        }

        return false;
    }

    private function isTeamMember(Iaa $iaa)
    {
        if (Auth::check())
        {
            $user = Auth::user();

            // Superadmin her zaman ekip üyesi yetkisine sahiptir ve heryere müdahale edebilir.
            if ($user->hasRole('Superadmin'))
            {
                return true;
            }

            $squadUyesi = $iaa->projeEkibi()->where('user_id', $user->id)->whereIn('iaa_user.durum', ['onaylandi', 'bekliyor'])->exists();
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

        // DİREKTÖR YETKİSİ: Direktörler sadece onay verir, adımları düzenleyemez. (Kaldırıldı)

        // BÖLÜM KALİTE YÖNETİCİSİ MÜDAHALE YETKİSİ (rules1.md & Talep Üzerine)
        if ($this->isQualityManagerWithInterventionPower($user, $iaa)) {
            return true;
        }

        if ($iaa->atananTakim && $iaa->atananTakim->lider_user_id == $user->id)
            return true;
        if ($iaa->gonderen_user_id == $user->id)
            return true;

        // SQUAD KONTROLÜ (Tüm projeler için: Eğer kullanıcı onaylı bir ekip üyesi ise düzenleyebilir)
        if ($iaa->projeEkibi()->where('user_id', $user->id)->wherePivot('durum', 'onaylandi')->exists())
        {
            return true;
        }

        // SAF İAA MI? (Müşteri şikayetinden dönüşmemiş)
        if (is_null($iaa->musteri_sikayeti_id))
        {
            // Saf İAA'da tüm takım üyeleri yetkilidir (atama bazlı kısıtlama Blade'de yapılacak)
            if ($iaa->atanan_takim_id && $user->takimlar->contains('id', $iaa->atanan_takim_id))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Müşteri Şikayeti Teknik Detaylarını Günceller
     */
    public function updateComplaintDetails(\Illuminate\Http\Request $request, $id)
    {
        $iaa = Iaa::findOrFail($id);

        if (!$this->authorizeUser($iaa))
        {
            abort(403, 'Yetkisiz erişim.');
        }

        // Yetki Kontrolü (Saf İAA Filtresi ile)
        $isLeader = ($iaa->atananTakim && Auth::id() == $iaa->atananTakim->lider_user_id);
        $isSuperAdmin = Auth::user()->hasRole('Superadmin');
        $isSafeIaa = is_null($iaa->musteri_sikayeti_id);
        $isTeamMember = $iaa->atanan_takim_id && Auth::user()->takimlar->contains('id', $iaa->atanan_takim_id);

        $isAuthorized = $isSuperAdmin || $isLeader;

        // Saf İAA ise takım üyesi de güncelleyebilir
        if ($isSafeIaa && $isTeamMember)
        {
            $isAuthorized = true;
        }

        $isDirector = false;
        if (Auth::user()->hasRole('Direktör'))
        {
            $yonetilenBolumIds = Auth::user()->yonetilenBolumler()->pluck('bolumler.id')->toArray();
            if (in_array($iaa->bolum_id, $yonetilenBolumIds))
            {
                $isDirector = true;
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized)
        {
            abort(403, 'Bu bilgileri sadece yetkili ekip üyeleri, direktör veya yönetici güncelleyebilir.');
        }

        if (!$iaa->musteriSikayeti)
        {
            throw new \Exception('Bu projeye bağlı bir müşteri şikayeti bulunamadı.');
        }

        $request->validate([
            'lot_no' => 'required|array',
            'lot_no.*' => 'nullable|string',
            'machine_id' => 'nullable|array',
            'genel_hammadde_id' => 'nullable|array',
            'urun_versiyonu_id' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $iaa)
        {
            $iaa->musteriSikayeti->teknikDetaylar()->delete();

            foreach ($request->lot_no as $key => $lotNo)
            {
                if (empty($lotNo) && empty($request->machine_id[$key]) && empty($request->genel_hammadde_id[$key]))
                {
                    continue;
                }

                $machine = $request->machine_id[$key] ? \App\Models\Machine::withTrashed()->find($request->machine_id[$key]) : null;
                $hammadde = $request->genel_hammadde_id[$key] ? \App\Models\GenelHammadde::withTrashed()->find($request->genel_hammadde_id[$key]) : null;
                $versiyon = $request->urun_versiyonu_id[$key] ? \App\Models\UrunVersiyonu::withTrashed()->find($request->urun_versiyonu_id[$key]) : null;

                $iaa->musteriSikayeti->teknikDetaylar()->create([
                    'lot_no' => $lotNo,
                    'machine_id' => $request->machine_id[$key] ?? null,
                    'machine_name' => $machine ? $machine->name : null,
                    'genel_hammadde_id' => $request->genel_hammadde_id[$key] ?? null,
                    'genel_hammadde_name' => $hammadde ? $hammadde->ad : null,
                    'urun_versiyonu_id' => $request->urun_versiyonu_id[$key] ?? null,
                    'urun_versiyonu_name' => $versiyon ? $versiyon->ad : null,
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

    /**
     * Kullanıcının müdahale yetkisi olan bir Bölüm Kalite Yöneticisi olup olmadığını kontrol eder.
     */
    public function isQualityManagerWithInterventionPower($user, Iaa $iaa)
    {
        if (!$user->hasRole('Bölüm Kalite Yöneticisi')) {
            return false;
        }

        if (!$user->can_intervene_quality) {
            return false;
        }

        // Projenin bir şikayet olması ve kullanıcının o kategoriden sorumlu olması gerekir
        if ($iaa->musteriSikayeti) {
            $kategoriId = $iaa->musteriSikayeti->sikayet_kategorisi_id;
            if ($kategoriId && $user->yonettigiSikayetKategorileri->contains($kategoriId)) {
                return true;
            }
            
            // Eğer kategori bazlı atama yoksa ama bölümü aynıysa (opsiyonel ama güvenli)
            if ($user->bolum_id && $iaa->musteriSikayeti->sikayetKategori && $iaa->musteriSikayeti->sikayetKategori->bolum_id == $user->bolum_id) {
                return true;
            }
        }

        return false;
    }
}
