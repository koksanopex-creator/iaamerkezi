<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\MusteriSikayeti;
use App\Models\DataCalibrationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DataCalibration extends Component
{
    public $inconsistencies = [];
    public $isScanning = false;
    public $isCalibrating = false;
    public $hasScanned = false;
    public $scanResults = [
        'dates' => 0,
        'statuses' => 0,
        'departments' => 0,
        'points' => 0
    ];
    public $history = [];
    public $lastCalibrationDate = null;

    public function mount()
    {
        $this->loadHistory();
        $this->lastCalibrationDate = DataCalibrationLog::where('type', 'veri')->latest()->value('created_at');
    }

    public function loadHistory()
    {
        $this->history = DataCalibrationLog::with('causer')
            ->latest()
            ->take(20)
            ->get();
    }

    public function scan()
    {
        $this->isScanning = true;
        $this->hasScanned = true;
        $this->inconsistencies = [];
        $this->scanResults = ['dates' => 0, 'statuses' => 0, 'departments' => 0, 'points' => 0];

        $direktorOnayiAktif = \App\Models\Setting::where('key', 'sikayet_direktor_onayi_aktif')->value('value') == '1';

        // 1. Tarih Tutarsızlıkları
        $projects = Iaa::whereIn('durum', ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi'])->get();
        foreach ($projects as $p)
        {
            $logQuery = IaaLog::where('iaa_id', $p->id);
            
            if (!$direktorOnayiAktif) {
                $logDate = (clone $logQuery)->where('eylem', 'Bölüm Onayı Verildi')->latest()->first()
                           ?? (clone $logQuery)->whereIn('eylem', ['Direktör Onayı Verildi', 'Proje Onaylandı', 'Hatalı Bildirim Onaylandı', 'Talep Onaylandı'])->latest()->first();
            } else {
                $logDate = $logQuery->whereIn('eylem', ['Direktör Onayı Verildi', 'Bölüm Onayı Verildi', 'Proje Onaylandı', 'Hatalı Bildirim Onaylandı', 'Talep Onaylandı'])
                    ->latest()
                    ->first();
            }

            if ($logDate)
            {
                $currentDate = $p->tamamlanma_tarihi ?? $p->onaylanma_tarihi;
                $hasDifference = ($logDate->created_at->format('Y-m-d H:i') != Carbon::parse($currentDate)->format('Y-m-d H:i'));

                if ($hasDifference)
                {
                    $this->inconsistencies[] = [
                        'type' => 'date',
                        'id' => $p->id,
                        'model_type' => 'Iaa',
                        'name' => 'Proje: ' . ($p->baslik ?: ($p->musteriSikayeti ? $p->musteriSikayeti->musteri_adi : 'İAA')) . ' (#' . $p->id . ')',
                        'old_value' => $currentDate ? Carbon::parse($currentDate)->format('d.m.Y H:i') : 'Boş',
                        'new_value' => $logDate->created_at->format('d.m.Y H:i'),
                        'description' => 'Log kaydı ile veritabanı tarihi uyuşmuyor.',
                        'url' => route('proje.workspace.show', $p->id)
                    ];
                    $this->scanResults['dates']++;
                }
            }
        }

        // 2. Durum Tutarsızlıkları
        $mismatchedStatuses = MusteriSikayeti::whereNotNull('iaa_id')
            ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
            ->whereHas('iaaProjesi', function ($q)
            {
                $q->whereIn('durum', ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi']);
            })->get();

        foreach ($mismatchedStatuses as $s)
        {
            $this->inconsistencies[] = [
                'type' => 'status',
                'id' => $s->id,
                'model_type' => 'MusteriSikayeti',
                'name' => $s->musteri_adi,
                'old_value' => $s->musteri_durum,
                'new_value' => 'Kapatıldı',
                'description' => 'Proje tamamlanmış ancak şikayet hala açık görünüyor.',
                'url' => route('iaa.sikayetler.show', $s->id)
            ];
            $this->scanResults['statuses']++;
        }

        // 3. Bölüm Tutarsızlıkları
        $mismatchedDepts = Iaa::whereHas('musteriSikayeti.sikayetKategori')
            ->get()
            ->filter(function ($p)
            {
                return $p->bolum_id != $p->musteriSikayeti->sikayetKategori->bolum_id;
            });

        foreach ($mismatchedDepts as $p)
        {
            $correctBolum = $p->musteriSikayeti->sikayetKategori->bolum;
            $this->inconsistencies[] = [
                'type' => 'department',
                'id' => $p->id,
                'model_type' => 'Iaa',
                'name' => 'Proje: ' . ($p->baslik ?: $p->musteriSikayeti->musteri_adi) . ' (#' . $p->id . ')',
                'old_value' => $p->bolum ? $p->bolum->ad : 'Bilinmiyor',
                'new_value' => $correctBolum ? $correctBolum->ad : 'Bilinmiyor',
                'description' => 'Projenin bağlı olduğu bölüm ile şikayet kategorisinin bölümü farklı.',
                'url' => route('proje.workspace.show', $p->id)
            ];
            $this->scanResults['departments']++;
        }

        // 4. Hatalı Puan Tutarsızlıkları (İşten Ayrılanlar - Pivot Kontrolü)
        // Kural: İşten ayrıldıktan sonra tamamlanan projelerden puan alamaz.
        // Önceden aldıkları puanlar (tamamlanma_tarihi <= departureDate) korunmalıdır.
        $pointMismatches = DB::table('iaa_user')
            ->join('users', 'iaa_user.user_id', '=', 'users.id')
            ->join('iaas', 'iaa_user.iaa_id', '=', 'iaas.id')
            ->where('iaa_user.kazanilan_puan', '>', 0)
            ->whereIn('iaas.durum', ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi'])
            ->where(function($q) {
                $q->whereNotNull('users.deleted_at')
                  ->orWhereNotNull('users.termination_date');
            })
            ->select('iaa_user.*', 'users.name as user_name', 'users.deleted_at', 'users.termination_date', 'iaas.baslik as proje_baslik', 'iaas.tamamlanma_tarihi', 'iaas.onaylanma_tarihi')
            ->get();

        foreach ($pointMismatches as $pm)
        {
            // En erken ayrılma tarihini bul
            $termDate = $pm->termination_date ? Carbon::parse($pm->termination_date)->endOfDay() : null;
            $deletedAt = $pm->deleted_at ? Carbon::parse($pm->deleted_at) : null;
            
            if ($termDate && $deletedAt) {
                $departureDate = $termDate->lt($deletedAt) ? $termDate : $deletedAt;
            } else {
                $departureDate = $deletedAt ?? $termDate;
            }
            $compDate = $pm->tamamlanma_tarihi ?? $pm->onaylanma_tarihi ?? $pm->created_at;
            
            if ($departureDate && $compDate && Carbon::parse($compDate)->gt(Carbon::parse($departureDate)->endOfDay())) {
                $this->inconsistencies[] = [
                    'type' => 'point',
                    'id' => $pm->iaa_id . '-' . $pm->user_id,
                    'model_type' => 'IaaUser',
                    'name' => 'Pivot Puan: ' . $pm->user_name . ' - ' . ($pm->proje_baslik ?: 'Proje #' . $pm->iaa_id),
                    'old_value' => $pm->kazanilan_puan . ' Puan',
                    'new_value' => '0 Puan',
                    'description' => 'Personel işten ayrıldıktan (' . Carbon::parse($departureDate)->format('d.m.Y') . ') sonra tamamlanan projeden puan almış.',
                    'url' => route('proje.workspace.show', $pm->iaa_id)
                ];
                $this->scanResults['points']++;
            }
        }

        // 5. Kullanıcı Toplam Puan Önbellek Tutarsızlıkları (Point Cache)
        // Müşteri olanların puanı 0 olmalıdır. Ayrılanlar ise servis tarafından hesaplanacaktır (Section 9'da).
        $userCaches = \App\Models\User::where('toplam_puan', '>', 0)
            ->where('is_personnel', false)
            ->get();
        
        foreach ($userCaches as $uc) {
             $this->inconsistencies[] = [
                'type' => 'point_cache',
                'id' => $uc->id,
                'model_type' => 'User',
                'name' => 'Önbellek: ' . $uc->name . ' (ID: ' . $uc->id . ')',
                'old_value' => $uc->toplam_puan . ' Puan',
                'new_value' => '0 Puan',
                'description' => 'Müşterinin profilinde (cache sütununda) hatalı puan kalmış. Müşteriler puan alamaz.',
                'url' => route('profile.puanlar', $uc->id)
            ];
            $this->scanResults['points']++;
        }

        // 5. Liderlik Puanları (İşten Ayrılanlar - Proje Bazlı)
        // Kural: Lider işten ayrıldıktan sonra tamamlanan projenin liderlik puanı 0 olmalıdır.
        $liderMismatches = Iaa::where('puan', '>', 0)
            ->whereIn('durum', ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi'])
            ->whereHas('tamamlayanLider', function($q) {
                $q->whereNotNull('deleted_at')->orWhereNotNull('termination_date');
            })
            ->with('tamamlayanLider')
            ->get();
        
        foreach ($liderMismatches as $lm) {
            $lider = $lm->tamamlayanLider;
            // En erken ayrılma tarihini bul
            $termDate = $lider->termination_date ? Carbon::parse($lider->termination_date)->endOfDay() : null;
            $deletedAt = $lider->deleted_at ? Carbon::parse($lider->deleted_at) : null;
            
            if ($termDate && $deletedAt) {
                $departureDate = $termDate->lt($deletedAt) ? $termDate : $deletedAt;
            } else {
                $departureDate = $deletedAt ?? $termDate;
            }
            $compDate = $lm->tamamlanma_tarihi ?? $lm->onaylanma_tarihi ?? $lm->created_at;
            
            if ($departureDate && $compDate && Carbon::parse($compDate)->gt(Carbon::parse($departureDate)->endOfDay())) {
                $this->inconsistencies[] = [
                    'type' => 'leader_point',
                    'id' => $lm->id,
                    'model_type' => 'Iaa',
                    'name' => 'Liderlik: ' . $lider->name . ' - ' . ($lm->baslik ?: 'Proje #' . $lm->id),
                    'old_value' => $lm->puan . ' Puan',
                    'new_value' => '0 Puan (Servis Seviyesinde)',
                    'description' => 'Lider işten ayrıldıktan (' . Carbon::parse($departureDate)->format('d.m.Y') . ') sonra tamamlanan projenin puanı 0 olmalıdır.',
                    'url' => route('proje.workspace.show', $lm->id)
                ];
                $this->scanResults['points']++;
            }
        }

        // 8. Şikayet vs Proje Puan Tutarsızlıkları
        // Şikayet kaynaklı projeler (oneri sütunu boş olanlar VEYA dönüşüm bilgisi içerenler) kontrol edilir.
        $complaintPuanMismatches = Iaa::whereHas('musteriSikayeti')
            ->with('musteriSikayeti')
            ->get()
            ->filter(function($p) {
                // Sadece şikayet kaynaklı olanları al (oneri kolonu standart metin desenini içeriyorsa)
                $isComplaintBased = str_contains($p->oneri, 'Müşteri şikayetinden');
                if (!$isComplaintBased) return false;
                
                return (float)$p->puan != (float)$p->musteriSikayeti->musteri_puan;
            });

        foreach ($complaintPuanMismatches as $p) {
            $this->inconsistencies[] = [
                'type' => 'complaint_puan_sync',
                'id' => $p->id,
                'model_type' => 'Iaa',
                'name' => 'Şikayet Puan Senk: ' . ($p->baslik ?: $p->musteriSikayeti->musteri_adi) . ' (#' . $p->id . ')',
                'old_value' => $p->puan . ' Puan (Proje)',
                'new_value' => $p->musteriSikayeti->musteri_puan . ' Puan (Şikayet)',
                'description' => 'Proje puanı ile bağlı olduğu müşteri şikayeti puanı uyuşmuyor. Şikayet puanı esas alınmalıdır.',
                'url' => route('proje.workspace.show', $p->id)
            ];
            $this->scanResults['points']++;
        }

        // 9. Genel Puan Önbellek (toplam_puan) Doğrulaması
        // Tüm aktif personellerin puanlarını KullaniciPuanService ile karşılaştırır.
        $puanService = app(\App\Services\Dashboard\KullaniciPuanService::class);
        $activeUsers = \App\Models\User::where('is_personnel', true)->where('onaylandi_mi', true)->get();
        
        foreach ($activeUsers as $u) {
            $realScore = $puanService->calculateTotalScore($u);
            if ((float)$u->toplam_puan != (float)$realScore) {
                $this->inconsistencies[] = [
                    'type' => 'global_point_cache',
                    'id' => $u->id,
                    'model_type' => 'User',
                    'name' => 'Genel Önbellek: ' . $u->name . ' (ID: ' . $u->id . ')',
                    'old_value' => $u->toplam_puan . ' Puan (DB)',
                    'new_value' => $realScore . ' Puan (Hesaplanan)',
                    'description' => 'Kullanıcının profilindeki toplam puan, gerçek zamanlı hesaplamayla uyuşmuyor. Bu durum "hayalet" puanlardan kaynaklanıyor olabilir.',
                    'url' => route('profile.puanlar', $u->id)
                ];
                $this->scanResults['points']++;
            }
        }

        $this->isScanning = false;

        if ($this->hasScanned && count($this->inconsistencies) === 0)
        {
            session()->flash('info', 'Tebrikler! Herhangi bir veri uyuşmazlığı bulunamadı.');
        }
    }

    public function calibrate()
    {
        $this->isCalibrating = true;

        DB::transaction(function ()
        {
            foreach ($this->inconsistencies as $item)
            {
                $this->applyCalibration($item);
            }
        });

        $this->hasScanned = false;
        $this->inconsistencies = [];
        $this->isCalibrating = false;
        $this->loadHistory();

        session()->flash('message', 'Sistem verileri başarıyla kalibre edildi ve loglandı.');
    }

    public function calibrateSingle($index)
    {
        if (!isset($this->inconsistencies[$index])) return;

        $item = $this->inconsistencies[$index];

        DB::transaction(function () use ($item, $index) {
            $this->applyCalibration($item);
            
            unset($this->inconsistencies[$index]);
            $this->inconsistencies = array_values($this->inconsistencies);
        });

        $this->loadHistory();
        session()->flash('message', 'Kayıt başarıyla kalibre edildi.');
    }

    private function applyCalibration($item)
    {
        $logEntry = [
            'model_type' => $item['model_type'],
            'model_id' => $item['id'],
            'type' => 'veri',
            'old_value' => $item['old_value'],
            'new_value' => $item['new_value'],
            'description' => "({$item['type']}) " . $item['description'],
            'causer_id' => Auth::id() ?? 1 // Fallback to Superadmin if no user is logged in (e.g. Tinker)
        ];

        if ($item['type'] === 'date')
        {
            $p = Iaa::find($item['id']);
            if ($p)
            {
                $logDate = Carbon::createFromFormat('d.m.Y H:i', $item['new_value']);
                $p->update([
                    'tamamlanma_tarihi' => $logDate,
                    'onaylanma_tarihi' => $logDate
                ]);
            }
        }
        elseif ($item['type'] === 'status')
        {
            $s = MusteriSikayeti::find($item['id']);
            if ($s)
            {
                $s->update(['musteri_durum' => 'Kapatıldı']);
            }
        }
        elseif ($item['type'] === 'department')
        {
            $p = Iaa::find($item['id']);
            if ($p && $p->musteriSikayeti && $p->musteriSikayeti->sikayetKategori)
            {
                $p->update(['bolum_id' => $p->musteriSikayeti->sikayetKategori->bolum_id]);
            }
        }
        elseif ($item['type'] === 'point')
        {
            $ids = explode('-', $item['id']);
            if (count($ids) === 2) {
                DB::table('iaa_user')
                    ->where('iaa_id', $ids[0])
                    ->where('user_id', $ids[1])
                    ->update(['kazanilan_puan' => 0]);
            }
        }
        elseif ($item['type'] === 'point_cache')
        {
            $u = \App\Models\User::withTrashed()->find($item['id']);
            if ($u) {
                $u->update(['toplam_puan' => 0]);
            }
        }
        elseif ($item['type'] === 'leader_point')
        {
            $p = Iaa::find($item['id']);
            if ($p) {
                // Lideri null yapmıyoruz çünkü tarihsel kayıt bozulmasın, 
                // ama serviste artık 0 döndüğü için bu sadece bir temizlik logu gibi çalışacak.
            }
        }
        elseif ($item['type'] === 'complaint_puan_sync')
        {
            $p = Iaa::with('musteriSikayeti')->find($item['id']);
            if ($p && $p->musteriSikayeti) {
                $yeniPuan = $p->musteriSikayeti->musteri_puan;
                $p->update(['puan' => $yeniPuan]);
                
                // Eğer proje tamamlanmışsa, ekip üyelerinin puanlarını da güncelle (Pivot tablo)
                if (in_array($p->durum, ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi'])) {
                    DB::table('iaa_user')->where('iaa_id', $p->id)->update(['kazanilan_puan' => $yeniPuan]);
                }
            }
        }
        elseif ($item['type'] === 'global_point_cache' || $item['type'] === 'point_cache')
        {
            $u = \App\Models\User::withTrashed()->find($item['id']);
            if ($u) {
                app(\App\Services\Dashboard\KullaniciPuanService::class)->syncUserCache($u);
            }
        }

        DataCalibrationLog::create($logEntry);
    }

    public function render()
    {
        return view('livewire.admin.data-calibration');
    }
}
