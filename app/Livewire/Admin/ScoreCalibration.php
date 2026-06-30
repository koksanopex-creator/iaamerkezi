<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Takim;
use App\Models\DataCalibrationLog;
use App\Services\Dashboard\KullaniciPuanService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ScoreCalibration extends Component
{
    public $inconsistencies = [];
    public $scanning = false;
    public $lastScanResults = null;
    public $recentLogs = [];
    public $lastCalibrationDate = null;

    protected $puanService;

    public function boot(KullaniciPuanService $puanService)
    {
        $this->puanService = $puanService;
    }

    public function mount()
    {
        $this->loadRecentLogs();
        $this->lastCalibrationDate = DataCalibrationLog::where('type', 'puan')->latest()->value('created_at');
    }

    public function loadRecentLogs()
    {
        $this->recentLogs = DataCalibrationLog::where('type', 'puan')
            ->latest()
            ->take(20)
            ->get();
    }

    public function scan()
    {
        $this->scanning = true;
        $this->inconsistencies = [];

        // 1. Kullanıcı Puanlarını Kontrol Et
        $users = User::where('is_personnel', true)->get();
        foreach ($users as $user)
        {
            $data = $this->puanService->getDetailedScoreData($user);
            $calculated = $data['toplam_puan'];

            if ($user->toplam_puan != $calculated)
            {
                $this->inconsistencies[] = [
                    'type' => 'user',
                    'id' => $user->id,
                    'name' => $user->name,
                    'current' => $user->toplam_puan,
                    'calculated' => $calculated,
                    'diff' => $calculated - $user->toplam_puan,
                    'details' => [
                        'projeler' => $data['tum_projeler']->count(),
                        'projeler_puan' => $data['tum_projeler']->sum('puan'),
                        'sikayet_giris' => $data['sikayet_girisleri']->count(),
                        'sikayet_giris_puan' => $data['sikayet_girisleri']->count() * $data['sikayet_giris_puani'],
                        'oneriler' => $data['oneriler']->count(),
                        'oneriler_puan' => $data['oneriler']->count() * $data['oneri_puani'],
                        'cezalar' => $data['cezalar']->count(),
                        'cezalar_puan' => $data['cezalar']->sum('hesaplanan_puan'),
                        'proje_listesi' => $data['tum_projeler']->take(5)->pluck('baslik', 'id')->toArray(),
                        'sikayet_listesi' => $data['sikayet_girisleri']->take(5)->pluck('musteri_adi', 'id')->toArray(),
                        'oneri_listesi' => $data['oneriler']->take(5)->pluck('baslik', 'id')->toArray()
                    ]
                ];
            }
        }

        // 2. Takım Puanlarını Kontrol Et
        $takimlar = Takim::all();
        foreach ($takimlar as $takim)
        {
            $data = $this->puanService->getTeamDetailedScoreData($takim);
            $calculated = $data['hesaplananPuan'] ?? 0;
            if ($takim->toplam_puan != $calculated)
            {
                $this->inconsistencies[] = [
                    'type' => 'team',
                    'id' => $takim->id,
                    'name' => $takim->ad,
                    'current' => $takim->toplam_puan,
                    'calculated' => $calculated,
                    'diff' => $calculated - $takim->toplam_puan,
                    'details' => [
                        'projeler' => $data['tamamlananProjeler']->count(),
                        'projeler_puan' => $data['tamamlananProjeler']->sum('puan'),
                        'sikayetler' => count($data['cozulenSikayetler']),
                        'sikayetler_puan' => collect($data['cozulenSikayetler'])->sum(function($s) {
                            return ($s->iaa_id && $s->iaaProjesi) ? $s->iaaProjesi->puan : $s->kazanilan_puan;
                        }),
                    ]
                ];
            }
        }

        $this->lastScanResults = count($this->inconsistencies);
        $this->scanning = false;
    }

    public function calibrate()
    {
        if (empty($this->inconsistencies))
        {
            return;
        }

        DB::transaction(function ()
        {
            foreach ($this->inconsistencies as $item)
            {
                if ($item['type'] === 'user')
                {
                    $model = User::find($item['id']);
                    if ($model)
                    {
                        $oldValue = $model->toplam_puan;
                        $model->toplam_puan = $item['calculated'];
                        $model->save();

                        $description = "{$item['name']} kullanıcısının puanı kalibre edildi.";
                        $breakdown = [];
                        if ($item['details']['projeler_puan'] > 0)
                            $breakdown[] = "{$item['details']['projeler']} Proje ({$item['details']['projeler_puan']} P)";
                        if ($item['details']['sikayet_giris_puan'] > 0)
                            $breakdown[] = "Şikayet ({$item['details']['sikayet_giris_puan']} P)";
                        if ($item['details']['oneriler_puan'] > 0)
                            $breakdown[] = "{$item['details']['oneriler']} Öneri ({$item['details']['oneriler_puan']} P)";
                        if ($item['details']['cezalar_puan'] > 0)
                            $breakdown[] = "Ceza (-{$item['details']['cezalar_puan']} P)";

                        if (!empty($breakdown))
                        {
                            $description .= " | " . implode(', ', $breakdown);
                        }

                        if (!empty($item['details']['proje_listesi']))
                        {
                            $projeStrings = [];
                            foreach ($item['details']['proje_listesi'] as $id => $name)
                            {
                                $projeStrings[] = "Proje #{$id} (" . \Illuminate\Support\Str::limit($name, 15) . ")";
                            }
                            $description .= " | " . implode(', ', $projeStrings);
                        }

                        if (!empty($item['details']['sikayet_listesi']))
                        {
                            $sikayetStrings = [];
                            foreach ($item['details']['sikayet_listesi'] as $id => $name)
                            {
                                $sikayetStrings[] = "Şikayet #{$id} (" . \Illuminate\Support\Str::limit($name, 15) . ")";
                            }
                            $description .= " | " . implode(', ', $sikayetStrings);
                        }

                        if (!empty($item['details']['oneri_listesi']))
                        {
                            $oneriStrings = [];
                            foreach ($item['details']['oneri_listesi'] as $id => $name)
                            {
                                $oneriStrings[] = "Öneri #{$id} (" . \Illuminate\Support\Str::limit($name, 15) . ")";
                            }
                            $description .= " | " . implode(', ', $oneriStrings);
                        }

                        DataCalibrationLog::create([
                            'causer_id' => Auth::id(),
                            'type' => 'puan',
                            'model_type' => 'User',
                            'model_id' => $item['id'],
                            'field' => 'toplam_puan',
                            'old_value' => (string)$oldValue,
                            'new_value' => (string)$item['calculated'],
                            'description' => $description,
                        ]);
                    }
                }
                else
                {
                    $model = Takim::find($item['id']);
                    if ($model)
                    {
                        $oldValue = $model->toplam_puan;
                        $model->toplam_puan = $item['calculated'];
                        $model->save();

                        $description = "{$item['name']} takımının puanı kalibre edildi.";
                        $breakdown = [];
                        if (isset($item['details']['projeler_puan']) && $item['details']['projeler_puan'] > 0)
                            $breakdown[] = "{$item['details']['projeler']} Proje ({$item['details']['projeler_puan']} P)";
                        
                        if (isset($item['details']['sikayetler_puan']) && $item['details']['sikayetler_puan'] > 0)
                            $breakdown[] = "{$item['details']['sikayetler']} Şikayet ({$item['details']['sikayetler_puan']} P)";

                        if (!empty($breakdown))
                        {
                            $description .= " (Detay: " . implode(', ', $breakdown) . ")";
                        }

                        DataCalibrationLog::create([
                            'causer_id' => Auth::id(),
                            'type' => 'puan',
                            'model_type' => 'Takim',
                            'model_id' => $item['id'],
                            'field' => 'toplam_puan',
                            'old_value' => (string)$oldValue,
                            'new_value' => (string)$item['calculated'],
                            'description' => $description,
                        ]);
                    }
                }
            }
        });

        $this->inconsistencies = [];
        $this->lastScanResults = null;
        $this->loadRecentLogs();

        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Başarılı!',
            'text' => 'Puanlar başarıyla kalibre edildi.',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.score-calibration');
    }
}
