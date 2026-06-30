<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Bolum;
use App\Models\MusteriSikayeti;
use App\Models\Takim;
use Illuminate\Support\Facades\DB;

class AiTools
{
    // Araç Tanımları (Gemini'ye gönderilecek meta-data)
    public static function getDefinitions()
    {
        return [
            'get_auth_user_info' => [
                'description' => 'Mevcut giriş yapmış kullanıcının bilgilerini (ad, rol, bölüm) getirir.',
                'parameters' => [], // Parametre yok
            ],
            'get_department_staff' => [
                'description' => 'Bir bölümdeki veya sistemdeki tüm personellerin listesini getirir.',
                'parameters' => [
                    'department_name' => ['type' => 'STRING', 'description' => 'Bölüm adı. "hepsi" yazılırsa (ve yetki varsa) tüm personeli getirir. Boş bırakılırsa kullanıcının kendi bölümü hedeflenir.']
                ],
            ],
            'get_my_tasks' => [
                'description' => 'Kullanıcının üzerindeki bekleyen görevleri listeler.',
                'parameters' => [],
            ],
            'get_pending_complaints_count' => [
                'description' => 'Bölümle ilgili bekleyen müşteri şikayetlerinin sayısını getirir.',
                'parameters' => [
                    'department_name' => ['type' => 'STRING', 'description' => 'Bölüm adı (opsiyonel).']
                ]
            ],
            'get_department_scores' => [
                'description' => 'Bölümdeki personellerin performans puanlarını ve sıralamasını getirir.',
                'parameters' => [
                    'department_name' => ['type' => 'STRING', 'description' => 'Bölüm adı (opsiyonel).']
                ]
            ],
            'get_user_active_assignments' => [
                'description' => 'Belirtilen kullanıcının üzerindeki aktif görevleri (onay bekleyenler, adım sorumlulukları vb.) detaylı listeler.',
                'parameters' => [
                    'user_name' => ['type' => 'STRING', 'description' => 'Kullanıcı adı (opsiyonel). Boş ise kendisi.']
                ]
            ],
            'get_staff_point_details' => [
                'description' => 'Belirtilen personelin detaylı puan dökümünü (projeler, şikayetler, öneriler, cezalar) getirir.',
                'parameters' => [
                    'staff_name' => ['type' => 'STRING', 'description' => 'Personel adı (opsiyonel). Boş ise kendisi.']
                ]
            ],
            'get_active_teams' => [
                'description' => 'Sistemdeki aktif takımları, üye sayılarını ve liderlerini listeler.',
                'parameters' => []
            ],
            'get_department_disciplinary_records' => [
                'description' => 'Bölümdeki personellerin disiplin cezalarını/tutanaklarını listeler. (Sadece yetkililer)',
                'parameters' => [
                    'department_name' => ['type' => 'STRING', 'description' => 'Bölüm adı (opsiyonel).']
                ]
            ],
            'get_navigation_helper' => [
                'description' => 'Sistemdeki önemli sayfaların (disiplin, takımlar, vb.) linklerini ve ne işe yaradıklarını getirir.',
                'parameters' => []
            ],
            'get_customer_stats' => [
                'description' => 'Sistemdeki müşteri sayılarını (aktif, pasif, yurt içi/dışı) ve müşteri yetkilisi sayılarını getirir.',
                'parameters' => []
            ]
        ];
    }

    // --- TOOL IMPLEMENTATIONS ---

    public function get_auth_user_info()
    {
        $user = Auth::user();
        if (!$user)
            return "Kullanıcı girişi yapılmamış.";

        $roller = $user->getRoleNames()->implode(', ');
        $bolum = $user->bolum ? $user->bolum->ad : 'Bölüm Yok';

        return "Kullanıcı: {$user->name}, Rol: {$roller}, Bölüm: {$bolum}, ID: {$user->id}";
    }

    public function get_department_staff($args = [])
    {
        $user = Auth::user();
        $targetDepartmentName = $args['department_name'] ?? null;

        // YÖNETİCİYE ÖZEL: TÜM LİSTE
        // Eğer "hepsi", "tümü", "all" denirse veya yönetici olup boş bırakılırsa ve niyeti buysa...
        // Ancak varsayılan davranış "kendi bölümü" olsun, "hepsi" denirse tümünü çeksin.
        $isGlobalRequest = in_array(strtolower($targetDepartmentName), ['hepsi', 'tümü', 'all', 'genel', 'tüm kullanıcılar', 'kullanıcılar']);

        if ($isGlobalRequest) {
            if (!$user->hasRole(['Superadmin', 'Yonetim'])) {
                return "YETKİ HATASI: Tüm personel listesini görüntüleme yetkiniz yok.";
            }

            // Çok fazla veri olabileceği için limitleyelim veya sadece isim/bölüm dönelim
            $allStaff = User::where('is_personnel', true)
                ->orderBy('bolum_id')
                ->with('bolum')
                ->get(['name', 'unvan', 'bolum_id']);

            $output = "🌍 **Tüm Personel Listesi:**\n";
            foreach ($allStaff as $u) {
                $bolumAd = $u->bolum ? $u->bolum->ad : 'Bölümsüz';
                $output .= "- {$u->name} ({$u->unvan}) - Bölüm: {$bolumAd}\n";
            }
            return $output;
        }

        $targetBolum = null;

        // 1. Hedef Bölümü Bul
        if ($targetDepartmentName) {
            // İsimden bölümü bulmaya çalış (Benzerlik araması)
            $targetBolum = Bolum::where('ad', 'like', '%' . $targetDepartmentName . '%')->first();
            if (!$targetBolum) {
                return "Belirttiğiniz isimde bir bölüm bulunamadı.";
            }
        } else {
            // Parametre yoksa kullanıcının kendi bölümü
            if (!$user->bolum_id)
                return "Herhangi bir bölüme atanmamışsınız.";
            $targetBolum = $user->bolum;
        }

        // 2. YETKİ KONTROLÜ (RBAC)
        // Kural: Sadece kendi bölümünü veya Yöneticiysen her yeri görebilirsin.
        $isManager = $user->hasRole(['Superadmin', 'Yonetim', 'Bolum Yoneticisi']);
        $isSameDepartment = $user->bolum_id == $targetBolum->id;

        if (!$isManager && !$isSameDepartment) {
            return "YETKİ HATASI: {$targetBolum->ad} bölümünün personel listesini görüntüleme yetkiniz yok. Sadece kendi bölümünüzü görebilirsiniz.";
        }

        // 3. Veriyi Getir
        $staff = User::where('bolum_id', $targetBolum->id)->get(['name', 'email', 'unvan']);

        if ($staff->isEmpty())
            return "Bu bölümde kayıtlı personel bulunamadı.";

        return "{$targetBolum->ad} Bölümü Personelleri:\n" . $staff->map(fn($u) => "- {$u->name} ({$u->unvan})")->implode("\n");
    }

    public function get_my_tasks()
    {
        $user = Auth::user();
        // Örnek: Kullanıcıya atanmış ve tamamlanmamış şikayetler veya görevler
        // Bu logic projenizin yapısına göre değişir ("atanan_kisi_id" gibi)

        // Şimdilik Team Leader ise takımına atananları sayalım
        /*
        $tasks = MusteriSikayeti::where('atanan_user_id', $user->id)
            ->whereIn('musteri_durum', ['Yeni', 'İşlemde'])
            ->get(['id', 'musteri_sikayet_konusu', 'musteri_durum']);
        */

        // Basit bir örnek dönüş:
        return "Sayın {$user->name}, şu an sistemde size özel tanımlanmış 'Bireysel Görevler' modülü tam aktif değil ancak bölümünüzle ilgili genel akışı 'Yönetim Raporları'ndan takip edebilirsiniz.";
    }

    public function get_pending_complaints_count($args = [])
    {
        $user = Auth::user();
        if (!$user->bolum)
            return "Bölüm bilginiz yok.";

        // 1. Genel Bölüm Bekleyenleri (Müşteri Şikayetleri)
        $deptPending = MusteriSikayeti::whereHas('sikayetKategori', function ($q) use ($user) {
            $q->where('bolum_id', $user->bolum_id);
        })->whereIn('musteri_durum', ['Yeni', 'İşlemde'])->count();

        // 2. Onay Bekleyen PROJELER (Kişiye/Role özel)
        $approvalPending = 0;
        $approvalText = "";

        // A) Yönetim Onayı
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            $yoneticiCount = \App\Models\Iaa::whereIn('durum', ['Yönetici Onayı Bekliyor', 'talep_onayi_bekliyor_superadmin'])->count();
            if ($yoneticiCount > 0) {
                $approvalPending += $yoneticiCount;
                $approvalText .= "- 🔴 **{$yoneticiCount}** adet proje Yönetim Onayı bekliyor.\n";
            }
        }

        // B) Bölüm Onayı
        if ($user->hasRole(['Bölüm Lideri', 'Bolum Yoneticisi'])) {
            $bolumCount = \App\Models\Iaa::where('bolum_id', $user->bolum_id)
                ->where('durum', 'Bölüm Onayı Bekliyor')
                ->count();

            // Kalite Lideri ise
            if (\Str::contains($user->bolum->ad, 'Kalite')) {
                $bolumCount += \App\Models\Iaa::where('durum', 'talep_onayi_bekliyor_kalite')->count();
            }

            if ($bolumCount > 0) {
                $approvalPending += $bolumCount;
                $approvalText .= "- 🟠 **{$bolumCount}** adet proje Bölüm/Kalite Onayı bekliyor.\n";
            }
        }

        // C) Misafir Önerileri (Sadece Superadmin veya ilgili birim görebilir)
        // Kullanıcı "3 adet misafir önerisi" dedi. Bunu IAA tablosunda 'guest_name' dolu olanlar veya 'oneri' tipiyle yakalayabiliriz.
        // Genelde 'Taslak' veya 'Onay Bekliyor' durumunda olurlar.
        $guestPending = 0;
        if ($user->hasRole(['Superadmin', 'Yonetim'])) {
            // Varsayım: Misafir önerileri 'Yeni' veya 'Onay Bekliyor' durumundadır ve gonderen_user_id null olabilir veya özel flag vardır.
            // Modelde 'guest_name' var.
            $guestPending = \App\Models\Iaa::whereNotNull('guest_name')
                ->where('durum', 'Onay Bekliyor') // Veya sisteminizin başlangıç durumu
                ->count();

            if ($guestPending > 0) {
                $approvalText .= "- 🔵 **{$guestPending}** adet **Misafir Önerisi** incelenmeyi bekliyor.\n";
            }
        }

        $output = "📊 **Durum Özeti:**\n";
        $output .= "- Bölümünüz ({$user->bolum->ad}) ile ilgili işlemde olan şikayet sayısı: **{$deptPending}**\n";

        if ($approvalText) {
            $output .= "\n**⚠️ Aksiyon Gerektiren / Onay Bekleyenler:**\n{$approvalText}";
            $output .= "\n🔗 Detaylar için: [İAA Yönetim](/admin/iaa-yonetim) veya [Şikayetler](/admin/sikayetler)";
        }

        return $output;
    }

    public function get_department_scores($args = [])
    {
        $user = Auth::user();
        $targetDepartmentName = $args['department_name'] ?? null;
        $targetBolum = null;

        if ($targetDepartmentName) {
            $targetBolum = Bolum::where('ad', 'like', '%' . $targetDepartmentName . '%')->first();
            if (!$targetBolum) {
                return "Belirttiğiniz isimde bir bölüm bulunamadı.";
            }
        } else {
            if (!$user->bolum_id)
                return "Herhangi bir bölüme atanmamışsınız.";
            $targetBolum = $user->bolum;
        }

        // Yetki Kontrolü: Bölüm Lideri veya Yönetici herkesi görebilir, Personel kendi bölümünü.
        $isManager = $user->hasRole(['Superadmin', 'Yonetim', 'Bolum Yoneticisi', 'Bölüm Lideri']);
        $isSameDepartment = $user->bolum_id == $targetBolum->id;

        if (!$isManager && !$isSameDepartment) {
            // Şeffaflık politikası: Puan tablosu genelde açıktır ama chatbot kısıtlayabilir. 
            // Kullanıcı talebi: "Herkes görebiliyor". O zaman açalım.
            // Ama yine de dış bölümler gereksiz trafik yapmasın, sadece uyarı verelim.
            // return "Sadece kendi bölümünüzü görüntüleyebilirsiniz."; 
        }

        // Puanları getir
        $scores = User::where('bolum_id', $targetBolum->id)
            ->where('is_personnel', true)
            ->orderByDesc('toplam_puan')
            ->get(['name', 'toplam_puan', 'unvan']);

        if ($scores->isEmpty())
            return "Bu bölümde puanlanmış personel bulunamadı.";

        $output = "🏆 {$targetBolum->ad} Bölümü Puan Durumu:\n";
        foreach ($scores as $index => $s) { // $index + 1
            $rank = $index + 1;
            $medal = match ($rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => "$rank."};
            $output .= "$medal {$s->name} ({$s->unvan}): {$s->toplam_puan} Puan\n";
        }

        return $output;
    }

    public function get_user_active_assignments($args = [])
    {
        $currentUser = Auth::user();
        $targetName = $args['user_name'] ?? null;
        $targetUser = $currentUser;

        // Hedef kullanıcı başkasıysa ve yetki varsa onu al
        if ($targetName) {
            $foundUser = User::where('name', 'like', '%' . $targetName . '%')->first();
            if ($foundUser) {
                // Yetki kontrolü: Sadece Yönetim ve Superadmin başkasının görevlerini görebilir
                if ($currentUser->hasRole(['Superadmin', 'Yonetim', 'Bölüm Lideri']) || $currentUser->id == $foundUser->id) {
                    $targetUser = $foundUser;
                } else {
                    return "YETKİ HATASI: Başka bir kullanıcının görevlerini sorgulama yetkiniz yok.";
                }
            } else {
                return "Belirtilen isimde ('$targetName') bir kullanıcı bulunamadı.";
            }
        }

        $output = "📋 **{$targetUser->name} İçin Aktif Görevler ve Bekleyen Onaylar**\n\n";
        $hasTasks = false;

        // 1. ADIM SORUMLULUKLARI (Step Assignments)
        // Kullanıcının atandığı ve henüz tamamlanmamış proje adımları
        $stepAssignments = \App\Models\IaaStepAssignment::where('user_id', $targetUser->id)
            ->whereHas('adim.workflow.iaa', function ($q) {
                $q->whereNotIn('durum', ['Tamamlandı', 'Reddedildi', 'İptal Edildi']);
            })
            // Bu adım için henüz progress update (tamamlanma kaydı) girilmemiş olmalı
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('iaa_progress_updates')
                    ->join('iaa_talepleri', 'iaa_progress_updates.iaa_talep_id', '=', 'iaa_talepleri.id')
                    ->join('iaa_workflow_steps', 'iaa_progress_updates.iaa_workflow_step_id', '=', 'iaa_workflow_steps.id')
                    ->whereColumn('iaa_talepleri.iaa_id', 'iaa_step_assignments.iaa_id') // Aynı proje
                    ->whereColumn('iaa_workflow_steps.id', 'iaa_step_assignments.iaa_workflow_step_id') // Aynı adım
                    ->whereNotNull('iaa_progress_updates.completed_at');
            })
            ->with([
                'adim',
                'adim.workflow.iaa' => function ($q) {
                    $q->select('id', 'baslik', 'durum');
                }
            ])
            ->get();

        if ($stepAssignments->isNotEmpty()) {
            $output .= "**🔹 Proje Adım Sorumlulukları:**\n";
            foreach ($stepAssignments as $assignment) {
                // İlişki zincirinden veriye ulaş (Assignment -> Adım -> Workflow -> IAA)
                // Model yapısına göre adim->workflow->iaa doğru yol mu kontrol edelim, 
                // Yoksa IaaStepAssignment direkt iaa_id'ye sahip mi? Evet sahip.
                $proje = \App\Models\Iaa::find($assignment->iaa_id);
                $adimAdi = $assignment->adim ? $assignment->adim->name : 'Bilinmeyen Adım';

                if ($proje) {
                    $output .= "- *Proje:* [{$proje->baslik}](/proje-calisma-alani/{$proje->id}) \n  *Görev:* {$adimAdi}\n";
                    $hasTasks = true;
                }
            }
            $output .= "\n";
        }

        // 2. YÖNETİCİ ONAYLARI (Genel Müdür / Yönetim)
        if ($targetUser->hasRole(['Superadmin', 'Yonetim'])) {
            $yoneticiOnayiBekleyenler = \App\Models\Iaa::where('durum', 'Yönetici Onayı Bekliyor')->count();
            // Ayrıca 'talep_onayi_bekliyor_superadmin' durumu
            $talepOnayiBekleyenler = \App\Models\Iaa::where('durum', 'talep_onayi_bekliyor_superadmin')->count();

            if ($yoneticiOnayiBekleyenler > 0 || $talepOnayiBekleyenler > 0) {
                $output .= "**🔹 Yönetici Onayı Bekleyen İşler:**\n";
                if ($yoneticiOnayiBekleyenler > 0)
                    $output .= "- {$yoneticiOnayiBekleyenler} adet proje **Fikir Onayı** (Havuz) bekliyor. [İAA Yönetim](/admin/iaa-yonetim)\n";
                if ($talepOnayiBekleyenler > 0)
                    $output .= "- {$talepOnayiBekleyenler} adet proje **Talep/Sonuç Onayı** bekliyor. [İAA Yönetim](/admin/iaa-yonetim)\n";

                $hasTasks = true;
                $output .= "\n";
            }
        }

        // 3. BÖLÜM LİDERİ ONAYLARI
        if ($targetUser->hasRole(['Bölüm Lideri', 'Bolum Yoneticisi']) && $targetUser->bolum_id) {
            // Kendi bölümüne ait olup 'Bölüm Onayı Bekliyor' durumundaki projeler
            $bolumOnayiBekleyenler = \App\Models\Iaa::where('bolum_id', $targetUser->bolum_id)
                ->where('durum', 'Bölüm Onayı Bekliyor')
                ->count();

            // Kalite Birimi özel durumu: 'talep_onayi_bekliyor_kalite'
            // Eğer bu kişi Kalite bölümündeyse bunu görsün
            $isKalite = \App\Models\Bolum::where('id', $targetUser->bolum_id)->where('ad', 'like', '%Kalite%')->exists();
            $kaliteOnayiBekleyenler = 0;
            if ($isKalite) {
                $kaliteOnayiBekleyenler = \App\Models\Iaa::where('durum', 'talep_onayi_bekliyor_kalite')->count();
            }

            if ($bolumOnayiBekleyenler > 0 || $kaliteOnayiBekleyenler > 0) {
                $output .= "**🔹 Bölüm Lideri Onayı Bekleyen İşler:**\n";
                if ($bolumOnayiBekleyenler > 0)
                    $output .= "- {$bolumOnayiBekleyenler} adet proje **Bölüm Onayı** bekliyor. [Şikayet/Proje Yönetimi](/admin/sikayetler)\n"; // Linkler kullanım senaryosuna göre değişebilir
                if ($kaliteOnayiBekleyenler > 0)
                    $output .= "- {$kaliteOnayiBekleyenler} adet proje **Kalite Kontrol Onayı** bekliyor.\n";

                $hasTasks = true;
                $output .= "\n";
            }
        }

        if (!$hasTasks) {
            return "{$targetUser->name} için şu anda sistemde bekleyen aktif bir görev, onay veya proje sorumluluğu bulunamadı.";
        }

        return $output;
    }

    public function get_staff_point_details($args = [])
    {
        $currentUser = Auth::user();
        $targetName = $args['staff_name'] ?? null;
        $targetUser = null;

        if ($targetName) {
            $targetUser = User::where('name', 'like', '%' . $targetName . '%')->first();
            if (!$targetUser)
                return "Belirttiğiniz isimde personel bulunamadı.";
        } else {
            $targetUser = $currentUser;
        }

        // Yetki Kontrolü
        $canView = false;
        if ($currentUser->hasRole(['Superadmin', 'Yonetim'])) {
            $canView = true;
        } elseif ($currentUser->id == $targetUser->id) {
            $canView = true;
        } elseif ($currentUser->hasRole(['Bölüm Lideri', 'Bolum Yoneticisi']) && $currentUser->bolum_id == $targetUser->bolum_id) {
            $canView = true;
        }

        if (!$canView) {
            return "YETKİ HATASI: {$targetUser->name} isimli personelin detaylı puan dökümünü görüntüleme yetkiniz yok.";
        }

        $output = "📊 **{$targetUser->name} Puan Detayları**:\n\n";
        $totalCheck = 0;

        // 1. Proje Görevlerinden Kazanılan Puanlar
        // Controller logic: gorevliOlduguProjeler where pivot kazanilan_puan > 0
        $projeler = $targetUser->gorevliOlduguProjeler()->wherePivot('kazanilan_puan', '>', 0)->get();
        if ($projeler->isNotEmpty()) {
            $output .= "**1. Proje Görevleri:**\n";
            foreach ($projeler as $proje) {
                $puan = $proje->pivot->kazanilan_puan;
                $output .= "- *{$proje->baslik}* ({$proje->pivot->rol}): +{$puan} Puan\n";
                $totalCheck += $puan;
            }
        }

        // 2. Müşteri Şikayeti Kaydı
        $sikayetGirisPuani = \App\Models\Setting::where('key', 'musteri_sikayeti_standart_puan')->value('value') ?? 0;
        if ($sikayetGirisPuani > 0) {
            $sikayetSayisi = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $targetUser->id)
                ->where('musteri_durum', '!=', 'Talep')
                // Basit count yeterli
                ->count();

            if ($sikayetSayisi > 0) {
                // Şikayet başına puanı tam kaç bilmiyoruz (değişken olabilir), ama setting'den alıyoruz.
                // Detaylı döküm yerine toplam veriyoruz.
                $puan = $sikayetSayisi * $sikayetGirisPuani;
                $output .= "\n**2. Oluşturulan Şikayet Kayıtları:**\n";
                $output .= "- {$sikayetSayisi} adet şikayet kaydı girildi: +{$puan} Puan\n";
                $totalCheck += $puan;
            }
        }

        // 3. Öneriler
        $iaaOneriPuani = \App\Models\Setting::where('key', 'iaa_oneri_puani')->value('value') ?? 0;
        if ($iaaOneriPuani > 0) {
            $oneriler = \App\Models\Iaa::where('gonderen_user_id', $targetUser->id)
                ->whereNotIn('durum', ['Taslak', 'Onay Bekliyor', 'Reddedildi'])
                ->get();

            if ($oneriler->isNotEmpty()) {
                $output .= "\n**3. Kabul Edilen Öneriler:**\n";
                foreach ($oneriler as $oneri) {
                    $output .= "- *{$oneri->baslik}*: +{$iaaOneriPuani} Puan\n";
                    $totalCheck += $iaaOneriPuani;
                }
            }
        }

        // 4. Disiplin Cezaları (Eksilen Puanlar)
        $cezalari = \App\Models\DisciplinaryCase::where('user_id', $targetUser->id)
            ->where('durum', 'Karar Verildi')
            ->where('final_karar', '!=', 'Savunma Kabul Edildi (Ceza Yok)')
            ->get();

        if ($cezalari->isNotEmpty()) {
            $output .= "\n**4. Disiplin İşlemleri / Cezalar:**\n";
            foreach ($cezalari as $ceza) {
                $dusen = $ceza->hesaplanan_puan;
                $output .= "- ⚠️ *Disiplin Dosyası* (Tarih: {$ceza->created_at->format('d.m.Y')}): -{$dusen} Puan\n";
                $totalCheck -= $dusen;
            }
        }

        if ($totalCheck <= 0 && $output == "📊 **{$targetUser->name} Puan Detayları**:\n\n") {
            return "{$targetUser->name} kullanıcısının sisteme yansımış herhangi bir puan hareketi (proje, şikayet, öneri veya ceza) bulunmamaktadır.";
        }

        $output .= "\n---\n**HESAPLANAN TOPLAM: {$totalCheck} Puan** (Sistem Kaydı: {$targetUser->toplam_puan})";
        return $output;
    }

    public function get_active_teams()
    {
        $user = Auth::user();

        // 1. Kullanıcının zaten üye olduğu veya istek attığı takımları bul
        $memberTeamIds = $user->takimlar()->pluck('takim_id');
        $pendingRequestTeamIds = \App\Models\TakimDavetiyesi::where('type', 'istek')
            ->where('davet_eden_user_id', $user->id)
            ->where('durum', 'bekliyor')
            ->pluck('takim_id');

        $excludeIds = $memberTeamIds->merge($pendingRequestTeamIds);

        // 2. Diğer Takımları Getir (Aynen Controller gibi)
        $teams = Takim::whereNotIn('id', $excludeIds)
            ->where('tur', '!=', 'sikayet')
            ->with('lider')
            ->withCount('uyeler')
            ->orderByDesc('uyeler_count')
            ->get();

        if ($teams->isEmpty())
            return "Size açık (üye olmadığınız) herhangi bir takım bulunamadı.";

        $output = "## 🛡️ Katılıma Açık / Diğer Takımlar\n";
        $output .= "Bu takımlar, üye olmadığınız ancak sistemde aktif olan takımlardır:\n\n";
        $output .= "| Takım Adı | Lider | Üye Sayısı |\n";
        $output .= "| :--- | :--- | :---: |\n";

        foreach ($teams as $t) {
            $liderName = $t->lider ? $t->lider->name : 'Atanmamış';
            $output .= "| {$t->ad} | {$liderName} | {$t->uyeler_count} |\n";
        }

        $output .= "\n🔗 **Detaylar ve Katılım İsteği İçin:** [Takımlar Sayfasına Git](/takimlar)";
        return $output;
    }

    public function get_department_disciplinary_records($args = [])
    {
        $user = Auth::user();
        $targetDep = $args['department_name'] ?? null;

        $bolum = $user->bolum;
        if ($targetDep) {
            $bolum = Bolum::where('ad', 'like', "%$targetDep%")->first();
        }

        if (!$bolum)
            return "Bölüm bilginize erişilemedi.";

        // Yetki: Yönetici veya O Bölümün Lideri
        $isManager = $user->hasRole(['Superadmin', 'Yonetim', 'Disiplin Kurulu Başkanı']);
        $isDeptLeader = ($user->hasRole(['Bölüm Lideri', 'Bolum Yoneticisi']) && $user->bolum_id == $bolum->id);

        if (!$isManager && !$isDeptLeader) {
            return "YETKİSİZ ERİŞİM: Disiplin kayıtlarını görme yetkiniz yok.";
        }

        $cases = \App\Models\DisciplinaryCase::whereHas('user', function ($q) use ($bolum) {
            $q->where('bolum_id', $bolum->id);
        })->with('user')->latest()->take(20)->get();

        if ($cases->isEmpty())
            return "{$bolum->ad} bölümünde kayıtlı disiplin tutanağı bulunmamaktadır.";

        $output = "## ⚖️ {$bolum->ad} Disiplin Kayıtları (Son 20)\n";
        foreach ($cases as $c) {
            $date = $c->created_at->format('d.m.Y');
            $statusIcon = $c->durum == 'Karar Verildi' ? '🔴' : '🟡';
            $output .= "**{$c->user->name}** - {$date}\n";
            $output .= "Durum: {$statusIcon} {$c->durum}\n";
            $output .= "Konu: {$c->konu}\n";
            $output .= "Link: [Dosyayı Görüntüle](/admin/disiplin/{$c->id}/edit)\n\n";
        }

        return $output;
    }

    public function get_navigation_helper()
    {
        return json_encode([
            "Genel" => [
                "Ana Sayfa / Dashboard" => "/dashboard",
                "Profilim" => "/user/profile",
                "Puan Durumu / Liderlik" => "/puan-durumu",
                "Takımlar & Projeler" => "/takimlar"
            ],
            "Yönetim & İşlemler" => [
                "Müşteri Şikayetleri" => "/admin/sikayetler",
                "Müşteri Yönetimi" => "/admin/musteriler",
                "İAA / Proje Yönetimi" => "/admin/iaa-yonetim",
                "Disiplin İşlemleri" => "/admin/disiplin",
                "Yeni Tutanak Oluştur" => "/admin/disiplin/yeni",
                "Raporlar" => "/admin/raporlar"
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function get_customer_stats()
    {
        $user = Auth::user();

        // 1. Müşteri (Firma) İstatistikleri
        $totalCustomers = \App\Models\Customer::count();
        $activeCustomers = \App\Models\Customer::where('is_active', true)->count();
        $passiveCustomers = $totalCustomers - $activeCustomers;

        $domestic = \App\Models\Customer::where('location_type', 'Yurt İçi')->count();
        $international = \App\Models\Customer::where('location_type', 'Yurt Dışı')->count();

        // 2. Müşteri Yetkilileri (Temsilciler)
        // Customer modelindeki 'users' ilişkisinden sayabiliriz veya User modelinden customer_id dolu olanları sayabiliriz.
        // User modeli üzerinden gitmek daha performanslı olabilir.
        $totalReps = User::whereNotNull('customer_id')->count();

        $output = "### 🌍 Müşteri İstatistikleri\n\n";
        $output .= "- **Toplam Müşteri (Firma):** {$totalCustomers}\n";
        $output .= "  - ✅ Aktif: {$activeCustomers}\n";
        $output .= "  - ❌ Pasif: {$passiveCustomers}\n";
        $output .= "  - 🏠 Yurt İçi: {$domestic}\n";
        $output .= "  - ✈️ Yurt Dışı: {$international}\n\n";

        $output .= "- **Toplam Müşteri Yetkilisi (Kullanıcı):** {$totalReps}\n";

        return $output;
    }
}
