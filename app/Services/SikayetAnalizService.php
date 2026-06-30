<?php

namespace App\Services;

use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use App\Models\SikayetAltKategori;
use App\Models\Bolum;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SikayetAnalizService
{
    protected $bolumId;
    protected $startDate;
    protected $endDate;
    protected $tarihAlani = 'created_at'; // default: sisteme giriş tarihi
    protected $durum;
    protected $oncelik;
    protected $customerId;
    protected $konumTipi;
    protected $altKategoriId;
    protected $squadUserId;
    protected $takimId;

    /**
     * Yetki matrisinden gelen zorunlu bölüm kısıtlaması.
     * '*' = tüm bölümler, array = belirli bölüm ID'leri, [] = hiçbir bölüm (erişim yok).
     */
    protected $hardBolumFilter = '*';

    public function setFilters(array $filters): self
    {
        $this->bolumId       = array_filter((array) ($filters['bolumId'] ?? []));
        $this->startDate     = $filters['startDate'] ?? null;
        $this->endDate       = $filters['endDate'] ?? null;
        $this->tarihAlani    = $filters['tarihAlani'] ?? 'created_at';
        $this->durum         = array_filter((array) ($filters['durum'] ?? []));
        $this->oncelik       = array_filter((array) ($filters['oncelik'] ?? []));
        $this->customerId    = array_filter((array) ($filters['customerId'] ?? []));
        $this->konumTipi     = array_filter((array) ($filters['konumTipi'] ?? []));
        $this->altKategoriId = array_filter((array) ($filters['altKategoriId'] ?? []));
        $this->squadUserId   = array_filter((array) ($filters['squadUserId'] ?? []));
        $this->takimId       = array_filter((array) ($filters['takimId'] ?? []));

        return $this;
    }

    /**
     * Yetki matrisinden gelen zorunlu bölüm kısıtlamasını ayarlar.
     * Livewire component tarafından çağrılır.
     */
    public function setHardBolumFilter(array|string $allowedBolumIds): self
    {
        $this->hardBolumFilter = $allowedBolumIds;
        return $this;
    }

    /**
     * Temel sorguyu oluşturur — tüm filtreler burada uygulanır.
     */
    private function baseQuery()
    {
        $tarihAlani = $this->getTarihColumn();

        $query = MusteriSikayeti::query();

        // === ZORUNLU YETKİ FİLTRESİ (Yetki Matrisi) ===
        // hardBolumFilter '*' değilse, kullanıcı sadece izin verilen bölümleri görebilir.
        $effectiveBolumIds = null; // null = filtre yok

        if ($this->hardBolumFilter !== '*') {
            $allowedIds = (array) $this->hardBolumFilter;

            if (!empty($this->bolumId)) {
                // Kullanıcı kendi filtresini de seçtiyse, izin verilen bölümlerle kesişim al
                $effectiveBolumIds = array_intersect($allowedIds, $this->bolumId);
                if (empty($effectiveBolumIds)) {
                    // Kullanıcı yetkisi olmayan bir bölüm seçti — boş sonuç döndür
                    $effectiveBolumIds = [0]; // Var olmayan ID ile boş sonuç garantile
                }
            } else {
                // Kullanıcı bölüm filtresi seçmedi — sadece yetki kısıtlamasını uygula
                $effectiveBolumIds = $allowedIds;
            }
        } else {
            // Tam erişim var, sadece kullanıcının kendi filtresini uygula
            if (!empty($this->bolumId)) {
                $effectiveBolumIds = $this->bolumId;
            }
        }

        // Bölüm filtresi uygula (hem yetki hem kullanıcı filtresi birleşik)
        if ($effectiveBolumIds !== null) {
            $ids = $effectiveBolumIds;
            $query->whereHas('sikayetKategori', function ($q) use ($ids) {
                $q->whereIn('bolum_id', $ids);
            });
        }

        // Tarih filtresi
        if ($this->startDate) {
            $query->whereDate("musteri_sikayetleri.{$tarihAlani}", '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate("musteri_sikayetleri.{$tarihAlani}", '<=', $this->endDate);
        }

        // Durum filtresi (Multi-select)
        if (!empty($this->durum)) {
            $query->where(function ($q) {
                foreach ($this->durum as $d) {
                    $q->orWhere(function ($qInner) use ($d) {
                        switch ($d) {
                            case 'yeni':
                                $qInner->whereNull('iaa_id')->where('musteri_durum', 'Yeni')
                                  ->orWhereHas('iaaProjesi', fn($sq) => $sq->where('durum', 'Yeni'));
                                break;
                            case 'islemde':
                                $qInner->where(function($sq1) {
                                    $sq1->whereNull('iaa_id')->whereIn('musteri_durum', ['İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor', 'Havuzda']);
                                })->orWhereHas('iaaProjesi', function($sq2) {
                                    $sq2->whereIn('durum', ['Atandı', 'İşlemde', 'Havuzda']);
                                });
                                break;
                            case 'bolum_onay':
                                $qInner->where(function($sq1) {
                                    $sq1->whereNull('iaa_id')->where('musteri_durum', 'Bölüm Onayı Bekliyor');
                                })->orWhereHas('iaaProjesi', function($sq2) {
                                    $sq2->where('durum', 'Bölüm Onayı Bekliyor');
                                });
                                break;
                            case 'direktor_onay':
                                $qInner->where(function($sq1) {
                                    $sq1->whereNull('iaa_id')->where('musteri_durum', 'Direktör Onayı Bekliyor');
                                })->orWhereHas('iaaProjesi', function($sq2) {
                                    $sq2->where('durum', 'Direktör Onayı Bekliyor');
                                });
                                break;
                            case 'final_onay':
                                $qInner->where(function($sq1) {
                                    $sq1->whereNull('iaa_id')->where('musteri_durum', 'Yönetici Onayı Bekliyor');
                                })->orWhereHas('iaaProjesi', function($sq2) {
                                    $sq2->where('durum', 'Yönetici Onayı Bekliyor');
                                });
                                break;
                            case 'hatali_bildirim':
                                $qInner->whereHas('iaaProjesi', function($sq2) {
                                    $sq2->where('durum', 'like', '%hatali_bildirim%');
                                });
                                break;
                            case 'talep':
                                $qInner->whereHas('iaaProjesi', function($sq2) {
                                    $sq2->where('durum', 'like', '%talep%');
                                });
                                break;
                            case 'kapatildi':
                                $qInner->where(function($sq1) {
                                    $sq1->whereNull('iaa_id')->whereIn('musteri_durum', ['Kapatıldı', 'Çözümlendi']);
                                })->orWhereHas('iaaProjesi', function($sq2) {
                                    $sq2->whereIn('durum', ['Tamamlandı', 'TALEP_OLARAK_KAPATILDI', 'TALEP_OLARAK_KAPATİLDİ', 'hatali_bildirim_olarak_kapatildi', 'Tamamlandi']);
                                });
                                break;
                            default:
                                $qInner->where(function($sq1) use ($d) {
                                    $sq1->whereNull('iaa_id')->where('musteri_durum', $d);
                                })->orWhereHas('iaaProjesi', function($sq2) use ($d) {
                                    $sq2->where('durum', $d);
                                });
                                break;
                        }
                    });
                }
            });
        }

        // Öncelik filtresi
        if (!empty($this->oncelik)) {
            $query->whereIn('musteri_oncelik', $this->oncelik);
        }

        // Müşteri (Firma) filtresi
        if (!empty($this->customerId)) {
            $query->whereIn('customer_id', $this->customerId);
        }

        // Konum tipi filtresi
        if (!empty($this->konumTipi)) {
            $query->whereIn('konum_tipi', $this->konumTipi);
        }

        // Çözüm Takımı / Personel Filtresi (Squad Üyesi)
        if (!empty($this->squadUserId)) {
            $query->whereHas('iaaProjesi.projeEkibi', function($q) {
                $q->whereIn('users.id', $this->squadUserId);
            });
        }

        // Takım Filtresi (Gerçek Takım)
        if (!empty($this->takimId)) {
            $query->whereHas('iaaProjesi', function($q) {
                $q->whereIn('atanan_takim_id', $this->takimId);
            });
        }

        // Alt kategori filtresi
        if (!empty($this->altKategoriId)) {
            $query->whereIn('sikayet_alt_kategori_id', $this->altKategoriId);
        }

        return $query;
    }

    /**
     * Tarih alanı sütun adını döner
     */
    private function getTarihColumn(): string
    {
        return match ($this->tarihAlani) {
            'musteri_sikayet_tarihi'  => 'musteri_sikayet_tarihi',
            'musteri_cozum_son_tarihi' => 'musteri_cozum_son_tarihi',
            default                    => 'created_at',
        };
    }

    /**
     * KPI Kartları
     */
    public function getKpiData(): array
    {
        $base = $this->baseQuery();

        $toplam = (clone $base)->count();
        $acik   = (clone $base)->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'Direktör Onayı Bekliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])->count();
        $cozulen = (clone $base)->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count();
        $gecikmis = (clone $base)
            ->whereNotNull('musteri_cozum_son_tarihi')
            ->where('musteri_cozum_son_tarihi', '<', now())
            ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->count();

        // Ortalama çözüm süresi (gün) - Kapalı şikayetlerde updated_at baz alınır
        $ortCozumSuresi = (clone $base)
            ->where(function($q) {
                $q->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                  ->orWhereHas('iaaProjesi', fn($sq) => $sq->whereIn('durum', ['Tamamlandı', 'TALEP_OLARAK_KAPATILDI', 'TALEP_OLARAK_KAPATİLDİ', 'hatali_bildirim_olarak_kapatildi', 'Tamamlandi']));
            })
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as ort_gun')
            ->value('ort_gun');

        // İade oranı
        $iadeliSayisi = (clone $base)->has('iadeler')->count();
        $iadeOrani = $toplam > 0 ? round(($iadeliSayisi / $toplam) * 100, 1) : 0;

        return [
            'toplam'          => $toplam,
            'acik'            => $acik,
            'cozulen'         => $cozulen,
            'gecikmis'        => $gecikmis,
            'ortCozumSuresi'  => $ortCozumSuresi ? round($ortCozumSuresi, 1) : 0,
            'iadeOrani'       => $iadeOrani,
        ];
    }

    /**
     * 1. Aylık Trend (Gelen vs Çözülen)
     */
    public function getAylikTrend(): array
    {
        $tarihAlani = $this->getTarihColumn();

        $gelen = (clone $this->baseQuery())
            ->select(
                DB::raw("DATE_FORMAT(musteri_sikayetleri.created_at, '%Y-%m') as ay"),
                DB::raw('count(*) as total')
            )
            ->when(!$this->startDate && !$this->endDate, fn($q) => $q->where('musteri_sikayetleri.created_at', '>=', now()->subMonths(12)))
            ->groupBy('ay')
            ->orderBy('ay')
            ->pluck('total', 'ay');

        $cozulen = MusteriSikayeti::whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
            ->when($this->bolumId, fn($q) => $q->whereHas('sikayetKategori', fn($sq) => $sq->where('bolum_id', $this->bolumId)))
            ->when($this->customerId, fn($q) => $q->where('customer_id', $this->customerId))
            ->when($this->konumTipi, fn($q) => $q->where('konum_tipi', $this->konumTipi))
            ->select(
                DB::raw("DATE_FORMAT(musteri_sikayetleri.updated_at, '%Y-%m') as ay"),
                DB::raw('count(*) as total')
            )
            ->when(!$this->startDate && !$this->endDate, fn($q) => $q->where('musteri_sikayetleri.updated_at', '>=', now()->subMonths(12)))
            ->when($this->startDate, fn($q) => $q->whereDate('musteri_sikayetleri.updated_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('musteri_sikayetleri.updated_at', '<=', $this->endDate))
            ->groupBy('ay')
            ->orderBy('ay')
            ->pluck('total', 'ay');

        $allMonths = collect(array_unique(array_merge(
            array_keys($gelen->toArray()),
            array_keys($cozulen->toArray())
        )))->sort()->values();

        return [
            'labels'  => $allMonths->map(fn($ay) => Carbon::parse($ay . '-01')->translatedFormat('M Y'))->toArray(),
            'gelen'   => $allMonths->map(fn($ay) => $gelen[$ay] ?? 0)->toArray(),
            'cozulen' => $allMonths->map(fn($ay) => $cozulen[$ay] ?? 0)->toArray(),
        ];
    }

    /**
     * 2. Durum Dağılımı (Donut)
     */
    public function getDurumDagilimi(): array
    {
        $data = (clone $this->baseQuery())
            ->select('musteri_durum', DB::raw('count(*) as total'))
            ->groupBy('musteri_durum')
            ->pluck('total', 'musteri_durum');

        return [
            'labels' => $data->keys()->toArray(),
            'series' => $data->values()->toArray(),
        ];
    }

    /**
     * 3. Bölüm Bazlı Dağılım (Yatay Bar)
     */
    public function getBolumDagilimi(): array
    {
        $data = (clone $this->baseQuery())
            ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
            ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
            ->select('bolumler.ad as bolum_adi', DB::raw('count(*) as total'))
            ->groupBy('bolumler.ad')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $data->pluck('bolum_adi')->toArray(),
            'series' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * 4. Öncelik Dağılımı (Pie)
     */
    public function getOncelikDagilimi(): array
    {
        $data = (clone $this->baseQuery())
            ->select('musteri_oncelik', DB::raw('count(*) as total'))
            ->whereNotNull('musteri_oncelik')
            ->groupBy('musteri_oncelik')
            ->pluck('total', 'musteri_oncelik');

        return [
            'labels' => $data->keys()->toArray(),
            'series' => $data->values()->toArray(),
        ];
    }

    /**
     * 5. Kategori Bazlı Analiz (Sütun)
     */
    public function getKategoriDagilimi(): array
    {
        $data = (clone $this->baseQuery())
            ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
            ->select('sikayet_kategorileri.ad as kategori_adi', DB::raw('count(*) as total'))
            ->groupBy('sikayet_kategorileri.ad')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $data->pluck('kategori_adi')->toArray(),
            'series' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * 6. Alt Kategori Dağılımı (TreeMap veya Bar)
     */
    public function getAltKategoriDagilimi(): array
    {
        $data = (clone $this->baseQuery())
            ->whereNotNull('sikayet_alt_kategori_id')
            ->join('sikayet_alt_kategorileri', 'musteri_sikayetleri.sikayet_alt_kategori_id', '=', 'sikayet_alt_kategorileri.id')
            ->join('sikayet_kategorileri', 'sikayet_alt_kategorileri.sikayet_kategori_id', '=', 'sikayet_kategorileri.id')
            ->select(
                'sikayet_kategorileri.ad as ana_kategori',
                'sikayet_alt_kategorileri.ad as alt_kategori',
                DB::raw('count(*) as total')
            )
            ->groupBy('sikayet_kategorileri.ad', 'sikayet_alt_kategorileri.ad')
            ->orderByDesc('total')
            ->take(15)
            ->get();

        return [
            'labels' => $data->map(fn($r) => $r->ana_kategori . ' → ' . $r->alt_kategori)->toArray(),
            'series' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * 7. Konum Tipi Dağılımı (Pie)
     */
    public function getKonumTipiDagilimi(): array
    {
        $data = (clone $this->baseQuery())
            ->select('konum_tipi', DB::raw('count(*) as total'))
            ->whereNotNull('konum_tipi')
            ->where('konum_tipi', '!=', '')
            ->groupBy('konum_tipi')
            ->pluck('total', 'konum_tipi');

        return [
            'labels' => $data->keys()->toArray(),
            'series' => $data->values()->toArray(),
        ];
    }

    /**
     * 8. Müşteri Bazlı Top 10 (Yatay Bar)
     */
    public function getMusteriTop10(): array
    {
        $data = (clone $this->baseQuery())
            ->whereNotNull('customer_id')
            ->join('customers', 'musteri_sikayetleri.customer_id', '=', 'customers.id')
            ->select('customers.name as firma', DB::raw('count(*) as total'))
            ->groupBy('customers.name')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        return [
            'labels' => $data->pluck('firma')->toArray(),
            'series' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * 9. Ortalama Çözüm Süresi Trendi (Area)
     */
    public function getCozumSuresiTrend(): array
    {
        $data = (clone $this->baseQuery())
            ->where(function($q) {
                $q->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                  ->orWhereHas('iaaProjesi', fn($sq) => $sq->whereIn('durum', ['Tamamlandı', 'TALEP_OLARAK_KAPATILDI', 'TALEP_OLARAK_KAPATİLDİ', 'hatali_bildirim_olarak_kapatildi', 'Tamamlandi']));
            })
            ->select(
                DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as ay"),
                DB::raw('ROUND(AVG(DATEDIFF(updated_at, created_at)), 1) as ort_gun')
            )
            ->groupBy('ay')
            ->orderBy('ay')
            ->get();

        return [
            'labels' => $data->pluck('ay')->map(fn($ay) => Carbon::parse($ay . '-01')->translatedFormat('M Y'))->toArray(),
            'series' => $data->pluck('ort_gun')->toArray(),
        ];
    }

    /**
     * 10. Çözüm Takımı (Squad) Personel Dağılımı (Bar)
     */
    public function getSquadPersonelDagilimi(): array
    {
        // Şikayetleri ve bunlara bağlı IAA projelerindeki kullanıcıları say
        $data = (clone $this->baseQuery())
            ->whereNotNull('musteri_sikayetleri.iaa_id')
            ->join('iaa_user', 'musteri_sikayetleri.iaa_id', '=', 'iaa_user.iaa_id')
            ->join('users', 'iaa_user.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(DISTINCT musteri_sikayetleri.id) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->take(15)
            ->get();

        return [
            'labels' => $data->pluck('name')->toArray(),
            'series' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * 11. OPEX: Pareto Analizi (Alt Kategorilere Göre %80/%20)
     */
    public function getParetoAnalizi(): array
    {
        $data = (clone $this->baseQuery())
            ->join('sikayet_alt_kategorileri', 'musteri_sikayetleri.sikayet_alt_kategori_id', '=', 'sikayet_alt_kategorileri.id')
            ->join('sikayet_kategorileri', 'sikayet_alt_kategorileri.sikayet_kategori_id', '=', 'sikayet_kategorileri.id')
            ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
            ->select(DB::raw('CONCAT(bolumler.ad, " - ", sikayet_alt_kategorileri.ad) as alt_kategori'), DB::raw('count(*) as total'))
            ->groupBy('sikayet_alt_kategorileri.id', 'sikayet_alt_kategorileri.ad', 'bolumler.ad')
            ->orderByDesc('total')
            ->get();

        $totalComplaints = $data->sum('total');
        $cumulative = 0;
        $labels = [];
        $barSeries = [];
        $lineSeries = [];

        foreach ($data as $item) {
            $labels[] = $item->alt_kategori;
            $barSeries[] = $item->total;
            $cumulative += $item->total;
            $lineSeries[] = $totalComplaints > 0 ? round(($cumulative / $totalComplaints) * 100, 1) : 0;
        }

        return [
            'labels' => $labels,
            'barSeries' => $barSeries, // Hacim (Bar)
            'lineSeries' => $lineSeries, // Kümülatif Yüzde (Line)
        ];
    }

    /**
     * 12. OPEX: Süreç Darboğazı Analizi (Bottleneck)
     * Aktif şikayetlerin hangi statüde ortalama kaç gün beklediği
     */
    public function getDarBogazAnalizi(): array
    {
        $data = (clone $this->baseQuery())
            ->whereNotIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'İptal Edildi', 'Reddedildi'])
            ->select('musteri_durum', DB::raw('ROUND(AVG(DATEDIFF(NOW(), updated_at)), 1) as avg_days_waiting'))
            ->groupBy('musteri_durum')
            ->orderByDesc('avg_days_waiting')
            ->get();

        return [
            'labels' => $data->pluck('musteri_durum')->toArray(),
            'series' => $data->pluck('avg_days_waiting')->toArray(),
        ];
    }

    /**
     * 13. OPEX: Isı Haritası (Bölüm x Kategori)
     */
    public function getBolumKategoriHeatmap(): array
    {
        $data = (clone $this->baseQuery())
            ->join('sikayet_kategorileri', 'musteri_sikayetleri.sikayet_kategorisi_id', '=', 'sikayet_kategorileri.id')
            ->join('bolumler', 'sikayet_kategorileri.bolum_id', '=', 'bolumler.id')
            ->select(
                'bolumler.ad as bolum_adi',
                'sikayet_kategorileri.ad as kategori_adi',
                DB::raw('count(*) as total')
            )
            ->groupBy('bolumler.id', 'bolumler.ad', 'sikayet_kategorileri.id', 'sikayet_kategorileri.ad')
            ->get();

        $bolumler = $data->pluck('bolum_adi')->unique()->values()->toArray();
        $kategoriler = $data->pluck('kategori_adi')->unique()->values()->toArray();

        $series = [];
        foreach ($kategoriler as $kategori) {
            $row = [];
            foreach ($bolumler as $bolum) {
                $match = $data->where('bolum_adi', $bolum)->where('kategori_adi', $kategori)->first();
                $row[] = $match ? $match->total : 0;
            }
            $series[] = [
                'name' => $kategori,
                'data' => $row
            ];
        }

        return [
            'categories' => $bolumler,
            'series' => $series,
        ];
    }

    /**
     * Analiz Sayfası Tablo Verisi (Pagination)
     */
    public function getDetayTablosu(int $perPage = 5)
    {
        return (clone $this->baseQuery())
            ->with(['sikayetKategori.bolum', 'customer', 'olusturanKurulUyesi', 'iadeler', 'iaaProjesi'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Filtre dropdown verileri
     */
    public static function getFilterData(array|string $allowedBolumIds = '*'): array
    {
        $altKategorilerQuery = SikayetAltKategori::with('anaKategori.bolum')->orderBy('ad');
        
        $bolumlerQuery = Bolum::with(['director', 'liderler'])
            ->whereHas('sikayetKategorileri.sikayetler')
            ->orderBy('ad');
            
        $musterilerQuery = Customer::orderBy('name');
        
        $squadUsersQuery = \App\Models\User::with('roles')->orderBy('name');

        $takimlarQuery = \App\Models\Takim::where('tur', 'sikayet')
            ->with('lider')
            ->orderBy('ad');

        if ($allowedBolumIds !== '*') {
            $allowedIds = (array) $allowedBolumIds;
            if (empty($allowedIds)) {
                $allowedIds = [0]; // Hiçbir şeye erişimi yoksa boş dönsün
            }

            $bolumlerQuery->whereIn('id', $allowedIds);

            $altKategorilerQuery->whereHas('anaKategori', function($q) use ($allowedIds) {
                $q->whereIn('bolum_id', $allowedIds);
            });

            // Müşterileri, kullanıcının yetkili olduğu bölümlere ait şikayeti olanlarla sınırla
            $musterilerQuery->whereIn('id', function($q) use ($allowedIds) {
                $q->select('customer_id')
                  ->from('musteri_sikayetleri')
                  ->whereIn('sikayet_kategorisi_id', function($q2) use ($allowedIds) {
                      $q2->select('id')->from('sikayet_kategorileri')->whereIn('bolum_id', $allowedIds);
                  });
            });

            // Squad User'ları (Görevli Personel) sınırla
            $squadUsersQuery->whereIn('id', function($query) use ($allowedIds) {
                $query->select('user_id')
                    ->from('iaa_user')
                    ->whereIn('iaa_id', function($q2) use ($allowedIds) {
                        $q2->select('iaa_id')->from('musteri_sikayetleri')
                           ->whereNotNull('iaa_id')
                           ->whereIn('sikayet_kategorisi_id', function($q3) use ($allowedIds) {
                               $q3->select('id')->from('sikayet_kategorileri')->whereIn('bolum_id', $allowedIds);
                           });
                    });
            });

            // Çözüm Takımlarını sınırla
            $takimlarQuery->whereIn('id', function($q) use ($allowedIds) {
                $q->select('atanan_cozum_takimi_id')
                  ->from('musteri_sikayetleri')
                  ->whereIn('sikayet_kategorisi_id', function($q2) use ($allowedIds) {
                      $q2->select('id')->from('sikayet_kategorileri')->whereIn('bolum_id', $allowedIds);
                  });
            });
        } else {
            // Default tüm squad kullanıcıları (yetki kısıtı yoksa)
            $squadUsersQuery->whereIn('id', function($query) {
                $query->select('user_id')
                    ->from('iaa_user')
                    ->whereIn('iaa_id', function($q2) {
                        $q2->select('iaa_id')->from('musteri_sikayetleri')->whereNotNull('iaa_id');
                    });
            });
        }

        $altKategoriler = $altKategorilerQuery->get()->map(function($ak) {
            $bolumAdi = $ak->anaKategori && $ak->anaKategori->bolum ? $ak->anaKategori->bolum->ad : '';
            return [
                'id' => $ak->id,
                'ad' => $bolumAdi ? $ak->ad . " ({$bolumAdi})" : $ak->ad,
                'sikayet_kategori_id' => $ak->sikayet_kategori_id
            ];
        });

        return [
            'bolumler'       => $bolumlerQuery->get(['id', 'ad', 'logo_yolu', 'director_id']),
            'altKategoriler' => $altKategoriler,
            'musteriler'     => $musterilerQuery->get(['id', 'name']),
            'squadUsers'     => $squadUsersQuery->get(['id', 'name', 'email']),
            'takimlar'       => $takimlarQuery->get(['id', 'ad', 'lider_user_id']),
            'durumlar'       => [
                'yeni'            => 'Yeni',
                'islemde'         => 'İşlemde (Atandı, İnceleniyor vb.)',
                'bolum_onay'      => 'Bölüm Kalite Yöneticisi Onayı Bekliyor',
                'direktor_onay'   => 'Direktör Onayı Bekliyor',
                'final_onay'      => 'Final Onay Bekliyor',
                'hatali_bildirim' => 'Hatalı Bildirim Sürecinde',
                'talep'           => 'Talep Sürecinde',
                'kapatildi'       => 'Kapatıldı / Çözümlendi'
            ],
            'oncelikler'   => ['Acil', 'Yüksek', 'Normal', 'Düşük'],
            'konumTipleri' => ['Yurt İçi', 'Yurt Dışı'],
        ];
    }
}
