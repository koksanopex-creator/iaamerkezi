<?php

namespace App\Livewire\Admin;

use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SikayetRaporSayfasi extends Component
{
    public $perPage = 10;

    // Filtreler
    public $filtreBaslik = '';
    public $filtreBaslangicTarihi = '';
    public $filtreBitisTarihi = '';
    public $filtreDurum = '';
    public $filtreMusteri = '';

    // Grafikler için veriyi saklayacağımız özellik
    public $chartData = [];

    // Component yüklendiğinde çalışır
    public function mount()
    {
        // Gerekirse query string'den de alabiliriz ama şimdilik basit tutalım.
    }

    // Filtreler değiştiğinde sayfalamayı/limiti sıfırla
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filtreBaslik', 'filtreBaslangicTarihi', 'filtreBitisTarihi', 'filtreDurum', 'filtreMusteri'])) {
            $this->perPage = 10;
        }
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    public function filtreleriTemizle()
    {
        $this->reset(['filtreBaslik', 'filtreBaslangicTarihi', 'filtreBitisTarihi', 'filtreDurum', 'filtreMusteri']);
        $this->perPage = 10;
    }

    public function render()
    {
        $user = Auth::user();

        $query = MusteriSikayeti::with([
            'sikayetKategori.bolum',
            'customer',
            'iaaProjesi.ziyaretPlani'
        ]);

        // === 1. YETKİ KONTROLÜ (Standart Sikayet Tablosundaki Gibi) ===
        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
            $query->where('konum_tipi', 'Yurt İçi');
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $query->where('konum_tipi', 'Yurt Dışı');
        } elseif ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Yonetim'])) {
            // Hepsini görebilir
        } elseif ($user->hasRole('Direktör')) {
            $yonettigiBolumIds = $user->getAllowedBolumIds();
            if ($yonettigiBolumIds === '*') {
                // Hepsi
            } elseif (empty($yonettigiBolumIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('sikayetKategori', function ($q) use ($yonettigiBolumIds) {
                    $q->whereIn('bolum_id', $yonettigiBolumIds);
                });
            }
        } elseif ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            $yonettigiKategoriIds = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            if (empty($yonettigiKategoriIds) && $user->bolum_id) {
                $yonettigiKategoriIds = SikayetKategori::where('bolum_id', $user->bolum_id)->pluck('id')->toArray();
            }
            if (empty($yonettigiKategoriIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('sikayet_kategorisi_id', $yonettigiKategoriIds);
            }
        } elseif ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
            $bolumId = $user->bolum_id;
            $personelIds = User::where('bolum_id', $bolumId)->pluck('id');
            $query->where(function ($q) use ($bolumId, $personelIds) {
                $q->whereHas('sikayetKategori', function ($subQ) use ($bolumId) {
                    $subQ->where('bolum_id', $bolumId);
                })->orWhereHas('iaaProjesi', function ($subQ) use ($personelIds) {
                    $subQ->whereHas('projeEkibi', function ($squadQ) use ($personelIds) {
                        $squadQ->whereIn('users.id', $personelIds)->where('iaa_user.durum', 'onaylandi');
                    });
                });
            });
        } elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $lideriOlduguTakimIds = $user->lideriOlduguTakimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($lideriOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $lideriOlduguTakimIds);
            }
        } elseif ($user->hasRole('Müşteri Saha Temsilcisi')) {
            $allowedBolumIds = $user->getAllowedBolumIds();
            if (empty($allowedBolumIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('sikayetKategori', function ($q) use ($allowedBolumIds) {
                    $q->whereIn('bolum_id', $allowedBolumIds);
                });
            }
        } else {
            $uyesiOlduguTakimIds = $user->takimlar()->where('tur', 'sikayet')->pluck('takimlar.id');
            if ($uyesiOlduguTakimIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('atanan_cozum_takimi_id', $uyesiOlduguTakimIds);
            }
        }

        // === 2. FİLTRELER ===
        if ($this->filtreBaslik) {
            $query->where('musteri_sikayet_konusu', 'like', '%' . $this->filtreBaslik . '%');
        }

        if ($this->filtreMusteri) {
            $query->where('musteri_adi', 'like', '%' . $this->filtreMusteri . '%');
        }

        if ($this->filtreBaslangicTarihi) {
            $query->whereDate('created_at', '>=', $this->filtreBaslangicTarihi);
        }

        if ($this->filtreBitisTarihi) {
            $query->whereDate('created_at', '<=', $this->filtreBitisTarihi);
        }

        if ($this->filtreDurum) {
            if ($this->filtreDurum === 'talep_kapali') {
                $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'));
            } elseif ($this->filtreDurum === 'hatali_bildirim') {
                $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'));
            } else {
                $query->where('musteri_durum', $this->filtreDurum);
            }
        }

        // === 3. GRAFİK VERİSİ HESAPLAMALARI ===
        // Grafik verileri için yetki kısıtlamasını "Bölüm" bazlı genişletiyoruz (Takım bağımsız)
        $chartQuery = MusteriSikayeti::with(['sikayetKategori.bolum', 'customer', 'iaaProjesi']);
        
        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
            $chartQuery->where('konum_tipi', 'Yurt İçi');
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $chartQuery->where('konum_tipi', 'Yurt Dışı');
        } elseif ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Yonetim'])) {
            // Hepsi
        } elseif ($user->hasRole('Direktör')) {
            $yonettigiBolumIds = $user->getAllowedBolumIds();
            if ($yonettigiBolumIds !== '*') {
                $chartQuery->whereHas('sikayetKategori', fn($q) => $q->whereIn('bolum_id', (array)$yonettigiBolumIds));
            }
        } elseif ($user->hasRole(['Bölüm Kalite Yöneticisi', 'Bölüm Lideri', 'Müşteri Şikayeti Çözüm Lideri', 'Müşteri Şikayeti Takım Üyesi'])) {
            // Kendi bölümündeki tüm şikayetleri görsün (takıma atanmasa da)
            if ($user->bolum_id) {
                $chartQuery->whereHas('sikayetKategori', fn($q) => $q->where('bolum_id', $user->bolum_id));
            } else {
                // Bölümü yoksa takım bazlı kalsın veya boş dönsün
                $chartQuery = clone $query;
            }
        } elseif ($user->hasRole('Müşteri Saha Temsilcisi')) {
            $allowedBolumIds = $user->getAllowedBolumIds();
            if (empty($allowedBolumIds)) {
                $chartQuery->whereRaw('1 = 0');
            } else {
                $chartQuery->whereHas('sikayetKategori', fn($q) => $q->whereIn('bolum_id', $allowedBolumIds));
            }
        } else {
            $chartQuery = clone $query;
        }

        // Filtreleri grafik sorgusuna da uygula
        if ($this->filtreBaslangicTarihi) $chartQuery->whereDate('created_at', '>=', $this->filtreBaslangicTarihi);
        if ($this->filtreBitisTarihi) $chartQuery->whereDate('created_at', '<=', $this->filtreBitisTarihi);
        if ($this->filtreMusteri) $chartQuery->where('musteri_adi', 'like', '%' . $this->filtreMusteri . '%');
        if ($this->filtreBaslik) $chartQuery->where('musteri_sikayet_konusu', 'like', '%' . $this->filtreBaslik . '%');
        if ($this->filtreDurum) {
            if ($this->filtreDurum === 'talep_kapali') $chartQuery->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'));
            elseif ($this->filtreDurum === 'hatali_bildirim') $chartQuery->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'));
            else $chartQuery->where('musteri_durum', $this->filtreDurum);
        }

        $allComplaints = $chartQuery->get();

        // 3.1. Şikayetlerin durumu (Pie)
        $statusCounts = [
            'Yeni' => 0,
            'İşlemde' => 0,
            'Çözümlendi' => 0,
            'Kapatıldı' => 0,
            'İptal Edildi' => 0,
            'Talep Olarak Kapatıldı' => 0,
            'Hatalı Bildirim' => 0,
            'Diğer' => 0
        ];

        // 3.2. Bölüm ve Müşteri verileri 
        $bolumStats = [];
        $musteriCounts = [];
        $ziyaretliSikayetCount = 0;
        $ziyaretliMusteriCounts = [];

        foreach ($allComplaints as $c) {
            // Durum hesapla
            if ($c->iaaProjesi && $c->iaaProjesi->durum === 'talep_olarak_kapatildi') {
                $statusCounts['Talep Olarak Kapatıldı']++;
            } elseif ($c->iaaProjesi && $c->iaaProjesi->durum === 'hatali_bildirim_olarak_kapatildi') {
                $statusCounts['Hatalı Bildirim']++;
            } else {
                $durum = in_array($c->musteri_durum, ['İnceleniyor', 'Atandı', 'Devam Ediyor']) ? 'İşlemde' : $c->musteri_durum;
                if (isset($statusCounts[$durum])) {
                    $statusCounts[$durum]++;
                } else {
                    $statusCounts['Diğer']++;
                }
            }

            // Bölüm hesapla
            $bolumAd = $c->sikayetKategori?->bolum?->ad ?? 'Bölüm Yok';
            if (!isset($bolumStats[$bolumAd])) {
                $bolumStats[$bolumAd] = ['count' => 0, 'totalDays' => 0, 'closedCount' => 0];
            }
            $bolumStats[$bolumAd]['count']++;

            $isClosed = in_array($c->musteri_durum, ['Çözümlendi', 'Kapatıldı', 'Tamamlandı']) ||
                ($c->iaaProjesi && in_array($c->iaaProjesi->durum, ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']));

            if ($isClosed) {
                $bitisTarihi = $c->iaaProjesi ? $c->iaaProjesi->real_completion_date : $c->updated_at;
                if ($bitisTarihi) {
                    $days = ceil($c->created_at->diffInDays($bitisTarihi)); // Yukarı yuvarlama talebi
                    $days = $days < 1 ? 1 : $days; // En az 1 gün sayalım
                    $bolumStats[$bolumAd]['totalDays'] += $days;
                    $bolumStats[$bolumAd]['closedCount']++;
                }
            }

            // Müşteri hesapla
            $musteriAd = $c->customer?->musteri_adi ?? $c->musteri_adi ?? 'Bilinmeyen';
            if (mb_strlen($musteriAd) > 15) {
                $musteriAd = mb_substr($musteriAd, 0, 15) . '..';
            }
            if (!isset($musteriCounts[$musteriAd])) {
                $musteriCounts[$musteriAd] = 0;
            }
            $musteriCounts[$musteriAd]++;

            // Ziyaret Kontrolü
            if ($c->iaaProjesi && $c->iaaProjesi->ziyaretPlani) {
                $ziyaretliSikayetCount++;
                if (!isset($ziyaretliMusteriCounts[$musteriAd])) {
                    $ziyaretliMusteriCounts[$musteriAd] = 0;
                }
                $ziyaretliMusteriCounts[$musteriAd]++;
            }
        }

        $durumCountsFiltered = array_filter($statusCounts);

        $bolumCozumData = [];
        $bolumCozumLabels = [];
        $genelCozumSuresiToplam = 0;
        $genelCozumAdet = 0;

        foreach ($bolumStats as $ad => $stat) {
            if ($stat['closedCount'] > 0) {
                $bolumCozumLabels[] = $ad;
                $bolumCozumData[] = round($stat['totalDays'] / $stat['closedCount'], 1);

                $genelCozumSuresiToplam += $stat['totalDays'];
                $genelCozumAdet += $stat['closedCount'];
            }
        }

        arsort($musteriCounts);
        $topMusteriler = array_slice($musteriCounts, 0, 10, true);

        arsort($ziyaretliMusteriCounts);
        $topZiyaretliMusteriler = array_slice($ziyaretliMusteriCounts, 0, 10, true);

        // Özet Kart Verileri
        $toplamSikayet = $allComplaints->count();
        $cozulenSikayet = $genelCozumAdet;
        $bekleyenSikayet = $toplamSikayet - $cozulenSikayet;
        $ortalamaCozumHizi = $genelCozumAdet > 0 ? ceil($genelCozumSuresiToplam / $genelCozumAdet) : 0;
        $ziyaretOrani = $toplamSikayet > 0 ? round(($ziyaretliSikayetCount / $toplamSikayet) * 100, 1) : 0;

        // Bilgilendirme Metni
        if ($this->filtreBaslangicTarihi && $this->filtreBitisTarihi) {
            $dateText = \Carbon\Carbon::parse($this->filtreBaslangicTarihi)->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($this->filtreBitisTarihi)->format('d.m.Y') . ' tarih aralığını göstermektedir';
        } elseif ($this->filtreBaslangicTarihi) {
            $dateText = \Carbon\Carbon::parse($this->filtreBaslangicTarihi)->format('d.m.Y') . ' tarihinden sonrasını göstermektedir';
        } elseif ($this->filtreBitisTarihi) {
            $dateText = \Carbon\Carbon::parse($this->filtreBitisTarihi)->format('d.m.Y') . ' tarihinden öncesini göstermektedir';
        } else {
            $dateText = 'Tüm zamanları göstermektedir';
        }

        // Trend Analizi (Gelen ve Çözülen)
        $aylikTrend = (clone $query)
            ->select(\Illuminate\Support\Facades\DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ay"), \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('ay')
            ->orderBy('ay', 'asc')
            ->pluck('total', 'ay');

        $aylikCozulenTrend = (clone $query)
            ->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
            ->select(\Illuminate\Support\Facades\DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as ay"), \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('ay')
            ->orderBy('ay', 'asc')
            ->pluck('total', 'ay');

        $trendLabels = collect(array_unique(array_merge(array_keys($aylikTrend->toArray()), array_keys($aylikCozulenTrend->toArray()))))->sort()->values();
        $combinedTrend = [
            'labels' => $trendLabels->map(fn($ay) => \Carbon\Carbon::parse($ay . '-01')->format('M Y'))->toArray(),
            'datasets' => [
                [
                    'name' => 'Gelen Şikayet',
                    'data' => $trendLabels->map(fn($ay) => $aylikTrend[$ay] ?? 0)->toArray()
                ],
                [
                    'name' => 'Çözülen Şikayet',
                    'data' => $trendLabels->map(fn($ay) => $aylikCozulenTrend[$ay] ?? 0)->toArray()
                ]
            ]
        ];

        $chartData = [
            'durum' => [
                'labels' => array_keys($durumCountsFiltered),
                'data' => array_values($durumCountsFiltered)
            ],
            'bolumSikayet' => [
                'labels' => array_keys($bolumStats),
                'data' => array_column($bolumStats, 'count')
            ],
            'bolumCozum' => [
                'labels' => $bolumCozumLabels,
                'data' => $bolumCozumData
            ],
            'musteri' => [
                'labels' => array_keys($topMusteriler),
                'data' => array_values($topMusteriler)
            ],
            'musteriZiyaret' => [
                'labels' => array_keys($topZiyaretliMusteriler),
                'data' => array_values($topZiyaretliMusteriler)
            ],
            'trend' => $combinedTrend,
            'ozet' => [
                'toplam' => $toplamSikayet,
                'cozulen' => $cozulenSikayet,
                'bekleyen' => $bekleyenSikayet,
                'ortalamaHiz' => $ortalamaCozumHizi,
                'ziyaretliSayi' => $ziyaretliSikayetCount,
                'ziyaretOrani' => $ziyaretOrani,
                'dateText' => $dateText
            ]
        ];

        // Grafikleri güncelleyebilmek için Livewire instance değerine aktarıyoruz.
        $this->chartData = $chartData;
        $this->dispatch('update-charts', $chartData);

        $totalCount = $query->count(); // Use original query structure (without take) for accurate total. Actually $allComplaints->count() is faster.
        $totalCount = $allComplaints->count();

        $sikayetler = $query->latest()->take($this->perPage)->get();

        return view('livewire.admin.sikayet-rapor-sayfasi', [
            'sikayetler' => $sikayetler,
            'totalCount' => $totalCount,
            'chartData' => $chartData
        ]);
    }
}
