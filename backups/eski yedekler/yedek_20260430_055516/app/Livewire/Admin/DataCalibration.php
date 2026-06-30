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
        'departments' => 0
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
        $this->scanResults = ['dates' => 0, 'statuses' => 0, 'departments' => 0];

        // 1. Tarih Tutarsızlıkları
        $projects = Iaa::whereIn('durum', ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi'])->get();
        foreach ($projects as $p) {
            $logDate = IaaLog::where('iaa_id', $p->id)
                ->whereIn('eylem', ['Direktör Onayı Verildi', 'Bölüm Onayı Verildi', 'Proje Onaylandı', 'Hatalı Bildirim Onaylandı', 'Talep Onaylandı'])
                ->latest()
                ->first();

            if ($logDate) {
                $currentDate = $p->tamamlanma_tarihi ?? $p->onaylanma_tarihi;
                $hasDifference = !$currentDate || ($logDate->created_at->format('Y-m-d H:i') != Carbon::parse($currentDate)->format('Y-m-d H:i'));

                if ($hasDifference) {
                    $this->inconsistencies[] = [
                        'type' => 'date',
                        'id' => $p->id,
                        'model_type' => 'Iaa',
                        'name' => $p->musteriSikayeti ? $p->musteriSikayeti->musteri_adi : 'Saf İAA #' . $p->id,
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
            ->whereHas('iaaProjesi', function($q) {
                $q->whereIn('durum', ['Tamamlandı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi']);
            })->get();

        foreach ($mismatchedStatuses as $s) {
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
            ->filter(function($p) {
                return $p->bolum_id != $p->musteriSikayeti->sikayetKategori->bolum_id;
            });

        foreach ($mismatchedDepts as $p) {
            $correctBolum = $p->musteriSikayeti->sikayetKategori->bolum;
            $this->inconsistencies[] = [
                'type' => 'department',
                'id' => $p->id,
                'model_type' => 'Iaa',
                'name' => $p->musteriSikayeti->musteri_adi,
                'old_value' => $p->bolum ? $p->bolum->ad : 'Bilinmiyor',
                'new_value' => $correctBolum ? $correctBolum->ad : 'Bilinmiyor',
                'description' => 'Projenin bağlı olduğu bölüm ile şikayet kategorisinin bölümü farklı.',
                'url' => route('proje.workspace.show', $p->id)
            ];
            $this->scanResults['departments']++;
        }

        $this->isScanning = false;
        
        if ($this->hasScanned && count($this->inconsistencies) === 0) {
            session()->flash('info', 'Tebrikler! Herhangi bir veri uyuşmazlığı bulunamadı.');
        }
    }

    public function calibrate()
    {
        $this->isCalibrating = true;

        DB::transaction(function() {
            foreach ($this->inconsistencies as $item) {
                $logEntry = [
                    'model_type' => $item['model_type'],
                    'model_id' => $item['id'],
                    'type' => $item['type'],
                    'old_value' => $item['old_value'],
                    'new_value' => $item['new_value'],
                    'description' => $item['description'],
                    'causer_id' => Auth::id()
                ];

                if ($item['type'] === 'date') {
                    $p = Iaa::find($item['id']);
                    if ($p) {
                        $logDate = Carbon::createFromFormat('d.m.Y H:i', $item['new_value']);
                        $p->update([
                            'tamamlanma_tarihi' => $logDate,
                            'onaylanma_tarihi' => $logDate
                        ]);
                    }
                } elseif ($item['type'] === 'status') {
                    $s = MusteriSikayeti::find($item['id']);
                    if ($s) {
                        $s->update(['musteri_durum' => 'Kapatıldı']);
                    }
                } elseif ($item['type'] === 'department') {
                    $p = Iaa::find($item['id']);
                    if ($p && $p->musteriSikayeti && $p->musteriSikayeti->sikayetKategori) {
                        $p->update(['bolum_id' => $p->musteriSikayeti->sikayetKategori->bolum_id]);
                    }
                }

                // Log kaydet
                DataCalibrationLog::create($logEntry);
            }
        });

        $this->hasScanned = false;
        $this->inconsistencies = [];
        $this->isCalibrating = false;
        $this->loadHistory();
        
        session()->flash('message', 'Sistem verileri başarıyla kalibre edildi ve loglandı.');
    }

    public function render()
    {
        return view('livewire.admin.data-calibration');
    }
}
