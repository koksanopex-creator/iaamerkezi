<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Services\SikayetAnalizService;
use App\Models\Bolum;
use App\Models\SikayetAltKategori;

class MusteriSikayetKarsilastirma extends Component
{
    // Ortak Filtreler
    #[Url(except: [])]
    public array $bolumId = [];
    #[Url(except: '')]
    public $startDate = '';
    #[Url(except: '')]
    public $endDate = '';
    #[Url(except: 'created_at')]
    public $tarihAlani = 'created_at';
    #[Url(except: [])]
    public array $durum = [];
    #[Url(except: [])]
    public array $oncelik = [];
    #[Url(except: [])]
    public array $customerId = [];
    #[Url(except: [])]
    public array $konumTipi = [];
    #[Url(except: [])]
    public array $altKategoriId = [];
    #[Url(except: [])]
    public array $squadUserId = [];
    #[Url(except: [])]
    public array $takimId = [];
    #[Url(except: false)]
    public bool $kiyaslaGenelOrtalama = false;

    // Kıyaslama Kriteri (Örn: bolum, durum, oncelik, squad)
    #[Url(except: 'bolum')]
    public $kiyaslamaKriteri = 'bolum'; 

    public function mount()
    {
        $authorization = \App\Models\ReportRoleAuthorization::getAuthorizationForUser(auth()->user(), 'karsilastirma_raporu');
        if (!$authorization) {
            abort(403, 'Bu karşılaştırma raporunu görüntüleme yetkiniz bulunmamaktadır.');
        }
    }


    public function updated($propertyName)
    {
        // Filtrelerden biri değiştiğinde yeniden render tetiklenir
    }

    public function clearFilters()
    {
        $this->bolumId = [];
        $this->startDate = '';
        $this->endDate = '';
        $this->tarihAlani = 'created_at';
        $this->durum = [];
        $this->oncelik = [];
        $this->customerId = [];
        $this->konumTipi = [];
        $this->altKategoriId = [];
        $this->squadUserId = [];
        $this->takimId = [];
        $this->kiyaslaGenelOrtalama = false;
        $this->kiyaslamaKriteri = 'bolum';
    }

    /**
     * Kıyaslanacak öğeleri ve onlara ait verileri (KPI, Trend) döner.
     */
    public function getComparisonData()
    {
        $filterData = SikayetAnalizService::getFilterData();
        $comparisonItems = [];
        $results = [];

        // Hangi boyutta kıyaslama yapacağımızı bul
        if ($this->kiyaslamaKriteri === 'bolum') {
            $items = !empty($this->bolumId) 
                ? $filterData['bolumler']->whereIn('id', $this->bolumId) 
                : $filterData['bolumler'];
            
            foreach ($items as $item) {
                $comparisonItems[] = [
                    'key' => $item->id,
                    'label' => $item->ad,
                    'filter_override' => ['bolumId' => [$item->id]],
                    'details' => [
                        'logo' => $item->logo_yolu ? asset('storage/' . $item->logo_yolu) : null,
                        'director' => $item->director ? $item->director->name : '-',
                        'liderler' => $item->liderler ? $item->liderler->pluck('name')->join(', ') : '-',
                        'subtitle' => 'Bölüm Karşılaştırması'
                    ]
                ];
            }
        } elseif ($this->kiyaslamaKriteri === 'durum') {
            $durumlar = $filterData['durumlar'];
            $selectedKeys = !empty($this->durum) ? $this->durum : array_keys($durumlar);
            
            foreach ($selectedKeys as $key) {
                $comparisonItems[] = [
                    'key' => $key,
                    'label' => $durumlar[$key] ?? $key,
                    'filter_override' => ['durum' => [$key]]
                ];
            }
        } elseif ($this->kiyaslamaKriteri === 'oncelik') {
            $oncelikler = $filterData['oncelikler'];
            $selectedKeys = !empty($this->oncelik) ? $this->oncelik : $oncelikler;

            foreach ($selectedKeys as $val) {
                $comparisonItems[] = [
                    'key' => $val,
                    'label' => $val,
                    'filter_override' => ['oncelik' => [$val]]
                ];
            }
        } elseif ($this->kiyaslamaKriteri === 'alt_kategori') {
            $items = !empty($this->altKategoriId) 
                ? $filterData['altKategoriler']->whereIn('id', $this->altKategoriId) 
                : $filterData['altKategoriler'];
            
            foreach ($items as $item) {
                $comparisonItems[] = [
                    'key' => $item['id'],
                    'label' => $item['ad'],
                    'filter_override' => ['altKategoriId' => [$item['id']]]
                ];
            }
        } elseif ($this->kiyaslamaKriteri === 'squad') {
            $items = !empty($this->squadUserId) 
                ? $filterData['squadUsers']->whereIn('id', $this->squadUserId) 
                : $filterData['squadUsers'];
            
            foreach ($items as $item) {
                $comparisonItems[] = [
                    'key' => $item->id,
                    'label' => $item->name,
                    'filter_override' => ['squadUserId' => [$item->id]],
                    'details' => [
                        'logo' => null, // squad için avatar kullanılabilir
                        'email' => $item->email,
                        'roles' => $item->roles ? $item->roles->pluck('name')->join(', ') : 'Personel',
                        'subtitle' => 'Görevli Personel'
                    ]
                ];
            }
        } elseif ($this->kiyaslamaKriteri === 'takim') {
            $items = !empty($this->takimId) 
                ? $filterData['takimlar']->whereIn('id', $this->takimId) 
                : $filterData['takimlar'];
            
            foreach ($items as $item) {
                $comparisonItems[] = [
                    'key' => $item->id,
                    'label' => $item->ad,
                    'filter_override' => ['takimId' => [$item->id]],
                    'details' => [
                        'logo' => null,
                        'director' => '-',
                        'liderler' => $item->lider ? $item->lider->name : '-',
                        'subtitle' => 'Çözüm Takımı Karşılaştırması'
                    ]
                ];
            }
        }

        // Genel Ortalama eklensin mi?
        if ($this->kiyaslaGenelOrtalama) {
            $comparisonItems[] = [
                'key' => 'genel_ortalama',
                'label' => 'Sistem Genel Ortalaması',
                'filter_override' => [], // Hiçbir kısıtlama yok (veya baseFilters ile sınırlı)
                'details' => [
                    'logo' => null,
                    'subtitle' => 'Tüm Kayıtların Ortalaması'
                ]
            ];
        }

        // Ana filtreleri al
        $baseFilters = [
            'bolumId' => $this->bolumId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'tarihAlani' => $this->tarihAlani,
            'durum' => $this->durum,
            'oncelik' => $this->oncelik,
            'customerId' => $this->customerId,
            'konumTipi' => $this->konumTipi,
            'altKategoriId' => $this->altKategoriId,
            'squadUserId' => $this->squadUserId,
            'takimId' => $this->takimId,
        ];

        $service = new SikayetAnalizService();
        $allowedIds = \App\Models\ReportRoleAuthorization::getAllowedBolumIdsForUser(auth()->user(), 'karsilastirma_raporu');
        $service->setHardBolumFilter($allowedIds);

        // Her bir item için analiz verisi oluştur
        foreach ($comparisonItems as $item) {
            // override filter
            $currentFilters = array_merge($baseFilters, $item['filter_override']);
            $service->setFilters($currentFilters);

            $results[] = [
                'label' => $item['label'],
                'kpi' => $service->getKpiData(),
                'trend' => $service->getCozumSuresiTrend(),
            ];
        }

        return $results;
    }

    public function getActiveFilterInfo()
    {
        $kriterIsimleri = [
            'bolum' => 'Bölümler',
            'durum' => 'Durumlar',
            'oncelik' => 'Öncelikler',
            'alt_kategori' => 'Alt Kategoriler',
            'takim' => 'Çözüm Takımları',
            'squad' => 'Görevli Personeller (Squad)'
        ];
        $kriter = $kriterIsimleri[$this->kiyaslamaKriteri] ?? $this->kiyaslamaKriteri;
        $info = "<div class='text-[13px] mb-2 font-medium'>Şu anda şikayet verilerini <strong class='text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded'>{$kriter}</strong> bazında kıyaslıyorsunuz.</div>";

        $aktifFiltreler = [];
        if (!empty($this->bolumId)) $aktifFiltreler[] = "<span class='inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded-md text-xs font-bold border border-yellow-200'>" . count($this->bolumId) . " Bölüm</span>";
        if (!empty($this->durum)) $aktifFiltreler[] = "<span class='inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-xs font-bold border border-blue-200'>" . count($this->durum) . " Durum</span>";
        if (!empty($this->oncelik)) $aktifFiltreler[] = "<span class='inline-block bg-pink-100 text-pink-800 px-2 py-1 rounded-md text-xs font-bold border border-pink-200'>" . count($this->oncelik) . " Öncelik</span>";
        if (!empty($this->altKategoriId)) $aktifFiltreler[] = "<span class='inline-block bg-emerald-100 text-emerald-800 px-2 py-1 rounded-md text-xs font-bold border border-emerald-200'>" . count($this->altKategoriId) . " Alt Kategori</span>";
        if (!empty($this->squadUserId)) $aktifFiltreler[] = "<span class='inline-block bg-purple-100 text-purple-800 px-2 py-1 rounded-md text-xs font-bold border border-purple-200'>" . count($this->squadUserId) . " Personel</span>";
        if (!empty($this->takimId)) $aktifFiltreler[] = "<span class='inline-block bg-cyan-100 text-cyan-800 px-2 py-1 rounded-md text-xs font-bold border border-cyan-200'>" . count($this->takimId) . " Çözüm Takımı</span>";
        if ($this->startDate || $this->endDate) $aktifFiltreler[] = "<span class='inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded-md text-xs font-bold border border-gray-200'>Tarih: " . ($this->startDate ?: 'Başı') . " - " . ($this->endDate ?: 'Sonu') . "</span>";

        if (count($aktifFiltreler) > 0) {
            $info .= "<div class='flex items-center gap-2 flex-wrap mt-2'><span class='text-[11px] font-black text-gray-500 uppercase tracking-wider'>Aktif Daraltıcı Filtreler:</span> " . implode(' ', $aktifFiltreler) . "</div>";
        } else {
            $info .= "<div class='text-xs text-gray-500 mt-1 font-medium'>Sistemdeki tüm veriler üzerinden analiz yapılmaktadır (Filtre uygulanmadı).</div>";
        }

        return $info;
    }

    public function getAlgorithmicSummary($comparisonData)
    {
        if (count($comparisonData) < 2) {
            return "Karşılaştırma yapabilmek için (grafiklerin çizilebilmesi için) en az 2 veri grubu olmalıdır.";
        }

        $toplamSikayetler = [];
        $cozumHizlari = [];
        $iadeOranlari = [];

        foreach ($comparisonData as $data) {
            $label = $data['label'];
            $kpi = $data['kpi'];
            if ($kpi['toplam'] > 0) {
                $toplamSikayetler[$label] = $kpi['toplam'];
                if ($kpi['ortCozumSuresi'] > 0) {
                    $cozumHizlari[$label] = $kpi['ortCozumSuresi'];
                }
                $iadeOranlari[$label] = $kpi['iadeOrani'] ?? 0;
            }
        }

        if (empty($toplamSikayetler)) {
            return "Seçili kriterlere ait yeterli veri bulunamadı.";
        }

        arsort($toplamSikayetler);
        asort($cozumHizlari); // En düşük gün en hızlıdır
        arsort($iadeOranlari);

        $enCokSikayetAlan = array_key_first($toplamSikayetler);
        $enHizliCozan = !empty($cozumHizlari) ? array_key_first($cozumHizlari) : null;
        $enCokIadeAlan = array_key_first($iadeOranlari);

        $ozetler = [];
        $ozetler[] = "📌 <strong>$enCokSikayetAlan</strong>, " . $toplamSikayetler[$enCokSikayetAlan] . " şikayet ile en yüksek hacme sahiptir.";
        
        if ($enHizliCozan) {
            $ozetler[] = "⚡ <strong>$enHizliCozan</strong>, ortalama " . number_format($cozumHizlari[$enHizliCozan], 1) . " gün ile sorunları en hızlı çözen gruptur.";
        }

        if ($enCokIadeAlan && $iadeOranlari[$enCokIadeAlan] > 0) {
            $ozetler[] = "⚠️ <strong>$enCokIadeAlan</strong>, %" . number_format($iadeOranlari[$enCokIadeAlan], 1) . " ile en yüksek iade oranına sahiptir.";
        }

        return implode(' &nbsp; | &nbsp; ', $ozetler);
    }

    public function render()
    {
        $allowedBolumIds = \App\Models\ReportRoleAuthorization::getAllowedBolumIdsForUser(auth()->user(), 'karsilastirma_raporu');
        $filterData = SikayetAnalizService::getFilterData($allowedBolumIds);
        
        // Çok fazla veri olmasını önlemek adına, seçili kıyaslama öğesi 10'dan fazlaysa uyarı verebiliriz ama şimdilik limit koymayalım.
        $comparisonData = $this->getComparisonData();
        $activeFilterInfo = $this->getActiveFilterInfo();
        $algorithmicSummary = $this->getAlgorithmicSummary($comparisonData);

        $this->dispatch('karsilastirma-updated', data: $comparisonData);

        return view('livewire.admin.musteri-sikayet-karsilastirma', [
            'filterData' => $filterData,
            'comparisonData' => $comparisonData,
            'activeFilterInfo' => $activeFilterInfo,
            'algorithmicSummary' => $algorithmicSummary,
        ])->layout('layouts.app');
    }
}
