<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\Iaa;
use App\Models\MusteriSikayeti;
use App\Models\Takim;
use Illuminate\Support\Facades\DB;

class BolumDashboardService
{
    /**
     * Bölüm Kalite Yöneticisi İstatistikleri
     */
    public function getQualityStats(User $user, array $filters = [])
    {
        $sorumluKategoriler = $user->yonettigiSikayetKategorileri;
        $sorumluKategoriIds = $sorumluKategoriler->pluck('id')->toArray();

        $startDate = $filters['areas_start_date'] ?? null;
        $endDate = $filters['areas_end_date'] ?? null;

        $stats = [
            'bolum_onay_sayisi' => Iaa::whereIn('durum', ['Bölüm Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_kalite'])
                ->whereHas('musteriSikayeti', function ($q) use ($sorumluKategoriIds) {
                    $q->whereIn('sikayet_kategorisi_id', $sorumluKategoriIds);
                })->count(),

            'kategori_bazli_stats' => [],

            'onay_bekleyen_liste' => Iaa::whereIn('durum', ['Bölüm Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_kalite'])
                ->whereHas('musteriSikayeti', function ($q) use ($sorumluKategoriIds) {
                    $q->whereIn('sikayet_kategorisi_id', $sorumluKategoriIds);
                })
                ->with(['atananTakim', 'musteriSikayeti'])
                ->latest('updated_at')
                ->take(5)
                ->get(),

            'son_departman_sikayetleri' => MusteriSikayeti::whereIn('sikayet_kategorisi_id', $sorumluKategoriIds)
                ->with('cozumTakimi')
                ->latest()
                ->take(5)
                ->get(),
        ];

        // Durum listeleri (getLeaderStatsByBolum ile uyumlu)
        $statuses = [
            'onay_bekleyen' => ['Havuzda', 'Onay Bekliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'],
            'tamamlanan'    => ['Kapatıldı', 'İptal Edildi'],
            'islemde'       => ['Yeni', 'İşlemde', 'Atandı']
        ];

        foreach ($sorumluKategoriler as $kat) {
            $baseQ = MusteriSikayeti::where('sikayet_kategorisi_id', $kat->id);
            
            if ($startDate) $baseQ->whereDate('created_at', '>=', $startDate);
            if ($endDate) $baseQ->whereDate('created_at', '<=', $endDate);

            $toplam = (clone $baseQ)->count();
            $cozulen = (clone $baseQ)->whereIn('musteri_durum', $statuses['tamamlanan'])->count();
            
            // Onay Bekleyen: Bağlı İAA projesi onay bekleyen statülerinden birindeyse
            $onayBekleyen = (clone $baseQ)->whereHas('iaaProjesi', function($sq) use ($statuses) {
                $sq->whereIn('durum', $statuses['onay_bekleyen']);
            })->count();

            // İşlemde: 'Yeni', 'İşlemde' veya 'Atandı' olanlar ama İAA'sı onay beklemeyenler
            $islemde = (clone $baseQ)->whereIn('musteri_durum', $statuses['islemde'])
                ->where(function($sq1) use ($statuses) {
                    $sq1->whereDoesntHave('iaaProjesi')
                        ->orWhereHas('iaaProjesi', function($sq2) use ($statuses) {
                            $sq2->whereNotIn('durum', $statuses['onay_bekleyen']);
                        });
                })->count();

            $stats['kategori_bazli_stats'][$kat->id] = [
                'toplam' => $toplam,
                'cozulen' => $cozulen,
                'onay_bekleyen' => $onayBekleyen,
                'islemde' => $islemde
            ];
        }

        // Genel istatistikler (KPI kartları için)
        $genelToplamSikayet = 0;
        $genelIslemdeki = 0;
        $genelCozulen = 0;
        foreach ($stats['kategori_bazli_stats'] as $katStat) {
            $genelToplamSikayet += $katStat['toplam'];
            $genelIslemdeki += $katStat['islemde'] + $katStat['onay_bekleyen'];
            $genelCozulen += $katStat['cozulen'];
        }
        $stats['toplam_sikayet'] = $genelToplamSikayet;
        $stats['islemdeki_sikayet'] = $genelIslemdeki;
        $stats['cozulen_sikayet'] = $genelCozulen;

        return $stats;
    }

    /**
     * Bölüm Lideri İstatistikleri
     */
    public function getLeaderStats(User $user, array $filters = [])
    {
        $stats = [];

        // Kendi bölümü yoksa boş dön
        if (!$user->bolum_id)
            return $stats;

        // Bölümdeki TÜM personellerin ID'leri (Lider dahil)
        $personelIds = User::where('bolum_id', $user->bolum_id)->pluck('id');

        // Yetkiler / Sorumluluklar
        $responsibleForComplaints = \App\Models\SikayetKategori::where('bolum_id', $user->bolum_id)->exists();
        $stats['is_responsible_for_sikayet'] = $responsibleForComplaints;

        $canSeeMediation = $user->hasRole(['Arabuluculuk Personel Lideri', 'Arabuluculuk Personel']) || $user->can('arabuluculuk.view_all');
        $stats['has_mediation_access'] = $canSeeMediation;

        // Filtre Helper
        $filterYear = $filters['year'] ?? null;
        $filterMonth = $filters['month'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        $applyDateFilter = function ($query, $column = 'created_at') use ($filterYear, $filterMonth, $startDate, $endDate) {
            if (!empty($filterYear))
                $query->whereYear($column, $filterYear);
            if (!empty($filterMonth))
                $query->whereMonth($column, $filterMonth);
            
            if (!empty($startDate))
                $query->whereDate($column, '>=', $startDate);
            if (!empty($endDate))
                $query->whereDate($column, '<=', $endDate);
        };


        // --- KAPSAYICI SORGU HELPER ---
        $inclusiveQuery = function ($q) use ($user, $personelIds) {
            $q->where('bolum_id', $user->bolum_id)
                ->orWhereHas('projeEkibi', fn($sq) => $sq->whereIn('users.id', $personelIds))
                ->orWhereHas('atananTakim', fn($sq) => $sq->whereIn('lider_user_id', $personelIds));
        };

        // --- 1. PROJELER ---
        $bolumProjeleriQuery = Iaa::whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'])
            ->where(function ($q) use ($inclusiveQuery, $user) {
                $inclusiveQuery($q);
                $q->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($user) {
                    $sq->where('bolum_id', $user->bolum_id);
                });
            });
        $applyDateFilter($bolumProjeleriQuery, 'created_at');

        $stats['bolum_aktif_proje_count'] = $bolumProjeleriQuery->count();
        $stats['bolum_projeleri'] = $bolumProjeleriQuery->with([
            'atananTakim.lider',
            'musteriSikayeti',
            'projeEkibi' => fn($q) => $q->whereIn('users.id', $personelIds)
        ])->get();

        // --- 2. HAVUZDAKİ ÖNERİLER ---
        $havuzQuery = Iaa::sadeceOneriler()
            ->whereIn('gonderen_user_id', $personelIds)
            ->whereIn('durum', ['Yeni', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin']);
        $applyDateFilter($havuzQuery, 'created_at');

        $stats['havuzdaki_oneriler_count'] = $havuzQuery->count();
        $stats['havuzdaki_oneriler'] = $havuzQuery->with('gonderen')->latest()->take(5)->get();

        // --- 3. BÖLÜME BAĞLI ŞİKAYETLER ---
        if ($responsibleForComplaints) {
            $bolumSikayetQuery = MusteriSikayeti::whereHas('sikayetKategori', function ($q) use ($user) {
                $q->where('bolum_id', $user->bolum_id);
            });
            $applyDateFilter($bolumSikayetQuery, 'created_at');

            $stats['bolum_sikayet_count'] = $bolumSikayetQuery->count();
            $stats['bolum_sikayetleri'] = $bolumSikayetQuery->with('iaaProjesi')->latest()->take(5)->get();
        } else {
            $stats['bolum_sikayet_count'] = 0;
            $stats['bolum_sikayetleri'] = collect();
        }

        // --- 4. AKTİF PERSONEL SAYISI (BU AY) ---
        $stats['bu_ay_aktif_personel_count'] = \DB::table('iaa_user')
            ->whereIn('user_id', $personelIds)
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->distinct('user_id')
            ->count();

        $stats['tum_personel_listesi'] = User::whereIn('id', $personelIds)
            ->withCount([
                'gorevliOlduguProjeler' => function ($q) {
                    $q->whereIn('iaas.durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']);
                }
            ])
            ->with('roles')
            ->orderByRaw("id = ? DESC", [$user->id]) // Çağıran lider en üstte
            ->orderBy('name')
            ->get();

        // --- 5. DİSİPLİN OLAYLARI ---
        $disiplinQuery = \App\Models\DisciplinaryCase::whereIn('user_id', $personelIds);
        $applyDateFilter($disiplinQuery, 'created_at');
        $stats['bolum_disiplin_count'] = $disiplinQuery->count();

        $disiplinListQuery = \App\Models\DisciplinaryCase::whereIn('user_id', $personelIds);
        $applyDateFilter($disiplinListQuery, 'karar_tarihi');
        $stats['bolum_disiplin_cezalari'] = $disiplinListQuery->with(['user', 'behavior'])->latest()->take(5)->get();

        // --- 6. BÖLÜM PERSONEL TUTANAKLARI (Liderin tuttukları dahil) ---
        $tuttugumTutanaklarQuery = \App\Models\DisciplinaryCase::whereIn('user_id', $personelIds);
        $applyDateFilter($tuttugumTutanaklarQuery, 'created_at');
        $stats['tuttugum_tutanaklar_count'] = $tuttugumTutanaklarQuery->count();
        $stats['tuttugum_tutanaklar'] = $tuttugumTutanaklarQuery->with(['user', 'behavior'])->latest()->take(5)->get();

        // --- 7. DAĞILIM VE LİSTELER (TABLAR) ---
        $baseQuery = function () use ($user, $personelIds, $inclusiveQuery) {
            return \App\Models\Iaa::where(function ($q) use ($user, $personelIds, $inclusiveQuery) {
                $inclusiveQuery($q);
                $q->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($user) {
                    $sq->where('bolum_id', $user->bolum_id);
                });
            });
        };

        $baseSikayetQuery = function () use ($user, $personelIds) {
            // Şikayetler tablosu üzerinden bölüm ile eşleşenleri çekiyoruz ki Iaa projesi olmayan ("Yeni") şikayetler de gelsin.
            return \App\Models\MusteriSikayeti::whereHas('sikayetKategori', function ($q) use ($user) {
                $q->where('bolum_id', $user->bolum_id);
            });
        };

        $statuses = [
            'tamamlanan'    => ['Tamamlandı', 'Talep Olarak Kapatıldı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi', 'İptal Edildi'],
            'devam_eden'    => ['Yeni', 'Havuzda', 'Atandı', 'Devam Ediyor', 'Revize Ediliyor'],
            'onay_bekleyen' => ['Onay Bekliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin']
        ];

        // Şikayet Durumları
        $sikayetStatuses = [
            'tamamlanan' => ['Kapatıldı', 'İptal Edildi'],
            'devam_eden' => ['Yeni', 'İşlemde', 'Atandı'],
            'onay_bekleyen' => [] // Şikayetlerde direkt yönetici onayı statüsü yoksa boş
        ];

        $filterStatusDate = function ($q, $statusList) use ($filterYear, $filterMonth) {
            if (!empty($filterYear))
                $q->whereYear('updated_at', $filterYear);
            if (!empty($filterMonth))
                $q->whereMonth('updated_at', $filterMonth);
            $q->whereIn('durum', $statusList);
        };

        $filterSikayetStatusDate = function ($q, $statusList) use ($filterYear, $filterMonth) {
            if (!empty($filterYear))
                $q->whereYear('updated_at', $filterYear);
            if (!empty($filterMonth))
                $q->whereMonth('updated_at', $filterMonth);
            $q->whereIn('musteri_durum', $statusList);
        };

        foreach ($statuses as $key => $statusList) {
            $iaaQ = $baseQuery()->tap(fn($q) => $q->sadeceOneriler());
            $filterStatusDate($iaaQ, $statusList);
            $stats['dagilim']['iaa'][$key] = $iaaQ->count();
            $stats['list']['iaa'][$key] = $iaaQ->with(['atananTakim.lider', 'gonderen'])->latest()->take(10)->get();

            // Complaint
            if ($responsibleForComplaints) {
                $compQ = $baseSikayetQuery();
                
                if ($key === 'onay_bekleyen') {
                    // Şikayete bağlı İAA projesi onay bekleyen statülerinden birindeyse 'Onay Bekleyen' say
                    $compQ->whereHas('iaaProjesi', function($sq) use ($statusList) {
                        $sq->whereIn('durum', $statusList);
                    });
                } elseif ($key === 'devam_eden') {
                    // İşlemde olanlar ama İAA'sı onay beklemeyenler
                    $compQ->whereIn('musteri_durum', $sikayetStatuses[$key])
                          ->where(function($sq1) use ($statuses) {
                              $sq1->whereDoesntHave('iaaProjesi')
                                  ->orWhereHas('iaaProjesi', function($sq2) use ($statuses) {
                                      $sq2->whereNotIn('durum', $statuses['onay_bekleyen']);
                                  });
                          });
                } else {
                    $filterSikayetStatusDate($compQ, $sikayetStatuses[$key]);
                }

                $stats['dagilim']['sikayet'][$key] = $compQ->count();
                $stats['list']['sikayet'][$key] = (clone $compQ)->with(['cozumTakimi.lider', 'iaaProjesi'])->latest()->take(10)->get();
            } else {
                $stats['dagilim']['sikayet'][$key] = 0;
                $stats['list']['sikayet'][$key] = collect();
            }
        }

        // --- 8. SON HAREKETLER (GENEL) ---
        $lastMovesIaa = $baseQuery()->tap(fn($q) => $q->sadeceOneriler());
        $applyDateFilter($lastMovesIaa, 'created_at');
        $stats['last_moves_iaa'] = $lastMovesIaa
            ->whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Tamamlandı', 'Talep Olarak Kapatıldı'])
            ->with(['projeEkibi', 'gonderen'])
            ->latest()
            ->take(5)
            ->get();
        $stats['total_iaa_count'] = (clone $baseQuery())->tap(fn($q) => $q->sadeceOneriler())->count();

        if ($responsibleForComplaints) {
            $lastMovesComplaints = $baseSikayetQuery();
            $applyDateFilter($lastMovesComplaints, 'created_at');
            $stats['last_moves_sikayet'] = $lastMovesComplaints
                ->whereIn('musteri_durum', ['Yeni', 'Atandı', 'İşlemde', 'Kapatıldı', 'İptal Edildi'])
                ->with(['cozumTakimi', 'iaaProjesi'])
                ->latest()
                ->take(5)
                ->get();
            $stats['total_sikayet_count'] = (clone $baseSikayetQuery())->count();
        } else {
            $stats['last_moves_sikayet'] = collect();
            $stats['total_sikayet_count'] = 0;
        }

        if ($canSeeMediation) {
            $stats['total_arabuluculuk_count'] = \App\Models\ArabuluculukCase::count();
        }

        return $stats;
    }

    /**
     * Belirli bir bölüm için lider istatistiklerini getirir (Direktör dashboardu için).
     */
    public function getLeaderStatsByBolum(\App\Models\Bolum $bolum, array $filters = [])
    {
        $stats = [];

        // Bölüm personellerinin ID'leri
        $personelIds = User::where('bolum_id', $bolum->id)->pluck('id');

        // Yetkiler / Sorumluluklar
        $responsibleForComplaints = \App\Models\SikayetKategori::where('bolum_id', $bolum->id)->exists();
        $stats['is_responsible_for_sikayet'] = $responsibleForComplaints;

        $applyDateFilter = function ($query, $type = 'sikayet', $column = 'created_at') use ($filters, $bolum) {
            // Eğer aktif bölüm seçilmişse ve bu bölüm değilse filtreleme (Tüm zamanlar göster)
            if (isset($filters['active_bolum']) && $filters['active_bolum'] != $bolum->id) {
                return;
            }

            // Tip bazlı anahtar haritası
            $keyMap = [
                'sikayet' => ['start_date', 'end_date'],
                'return' => ['return_start_date', 'return_end_date'],
                'iaa' => ['iaa_start_date', 'iaa_end_date'],
                'disiplin' => ['disiplin_start_date', 'disiplin_end_date'],
                'gorev' => ['gorev_start_date', 'gorev_end_date'],
            ];

            $keys = $keyMap[$type] ?? ['start_date', 'end_date'];
            
            $startDate = $filters[$keys[0]] ?? null;
            $endDate = $filters[$keys[1]] ?? null;

            if ($startDate) $query->whereDate($column, '>=', $startDate);
            if ($endDate) $query->whereDate($column, '<=', $endDate);
            
            // Legacy year/month desteği (Global filtreler için)
            if (empty($startDate) && empty($endDate)) {
                if (!empty($filters['year'])) $query->whereYear($column, $filters['year']);
                if (!empty($filters['month'])) $query->whereMonth($column, $filters['month']);
            }
        };


        // --- 1. PROJELER ---
        $inclusiveQuery = function ($q) use ($bolum, $personelIds) {
            $q->where('bolum_id', $bolum->id)
                ->orWhereHas('projeEkibi', fn($sq) => $sq->whereIn('users.id', $personelIds))
                ->orWhereHas('atananTakim', fn($sq) => $sq->whereIn('lider_user_id', $personelIds));
        };

        // Sadece Aktif Projeleri Say (Arayüzdeki sayaç için)
        $aktifProjelerQuery = Iaa::whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'])
            ->where(function ($q) use ($inclusiveQuery, $bolum) {
                $inclusiveQuery($q);
                $q->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($bolum) {
                    $sq->where('bolum_id', $bolum->id);
                });
            });
        $applyDateFilter($aktifProjelerQuery, 'iaa', 'created_at');
        $stats['bolum_aktif_proje_count'] = $aktifProjelerQuery->count();

        // Saf İAA Sayısı (Şikayet olmayan + Personel dahil)
        $safIaaCountQuery = Iaa::sadeceOneriler()
            ->where($inclusiveQuery);
        $applyDateFilter($safIaaCountQuery, 'iaa', 'created_at');
        $stats['bolum_saf_iaa_count'] = $safIaaCountQuery->count();

        // Tüm Projeleri Getir (Son Eklenenler Listesi için)
        $bolumProjeleriQuery = Iaa::where(function ($q) use ($inclusiveQuery, $bolum) {
            $inclusiveQuery($q);
            $q->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($bolum) {
                $sq->where('bolum_id', $bolum->id);
            });
        });
        $applyDateFilter($bolumProjeleriQuery, 'iaa', 'created_at');

        $stats['bolum_projeleri'] = $bolumProjeleriQuery->with([
            'atananTakim.lider',
            'musteriSikayeti',
            'projeEkibi'
        ])->latest()->take(10)->get();

        // --- 2. BÖLÜME BAĞLI ŞİKAYETLER ---
        $bolumSikayetQuery = MusteriSikayeti::whereHas('sikayetKategori', function ($q) use ($bolum) {
            $q->where('bolum_id', $bolum->id);
        });
        $applyDateFilter($bolumSikayetQuery, 'sikayet', 'created_at');

        $stats['bolum_sikayet_count'] = $bolumSikayetQuery->count();
        $stats['bolum_sikayetleri'] = (clone $bolumSikayetQuery)->with(['customer', 'iaaProjesi'])->latest()->take(10)->get();

        // --- 3. DİSİPLİN OLAYLARI ---
        $disiplinQuery = \App\Models\DisciplinaryCase::whereIn('user_id', $personelIds);
        $applyDateFilter($disiplinQuery, 'disiplin', 'created_at');
        $stats['bolum_disiplin_count'] = $disiplinQuery->count();
        $stats['bolum_disiplin_olaylari'] = $disiplinQuery->with(['user', 'behavior'])->latest()->get(); // Tümü gelsin dashboardda tablo scroll olabilir

        // --- 4. ONAY BEKLEYEN ZİYARET PLANLARI (YENİ) ---
        $pendingVisitsQuery = \App\Models\IaaZiyaretPlani::where('status', 'Beklemede')
            ->whereHas('iaa', function ($q) use ($bolum, $personelIds, $inclusiveQuery) {
                $q->where(function ($sq) use ($bolum, $personelIds, $inclusiveQuery) {
                    $inclusiveQuery($sq);
                    $sq->orWhereHas('musteriSikayeti.sikayetKategori', function ($skq) use ($bolum) {
                        $skq->where('bolum_id', $bolum->id);
                    });
                });
            });
        
        $stats['pending_visit_count'] = $pendingVisitsQuery->count();
        $stats['pending_visits_list'] = $pendingVisitsQuery->with(['iaa.musteriSikayeti.customer', 'iaa.gonderen'])->latest()->get();

        // --- 5. DAĞILIM VE LİSTELER ---
        $baseQuery = function () use ($bolum, $personelIds, $inclusiveQuery) {
            return \App\Models\Iaa::where(function ($q) use ($bolum, $personelIds, $inclusiveQuery) {
                $inclusiveQuery($q);
                $q->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($bolum) {
                    $sq->where('bolum_id', $bolum->id);
                });
            });
        };

        $baseSikayetQuery = function () use ($bolum) {
            return \App\Models\MusteriSikayeti::whereHas('sikayetKategori', function ($q) use ($bolum) {
                $q->where('bolum_id', $bolum->id);
            });
        };

        $statuses = [
            'tamamlanan'    => ['Tamamlandı', 'Talep Olarak Kapatıldı', 'talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi', 'Reddedildi', 'İptal Edildi'],
            'devam_eden'    => ['Atandı', 'Devam Ediyor', 'Revize Ediliyor'],
            'onay_bekleyen' => ['Havuzda', 'Onay Bekliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin']
        ];

        $sikayetStatuses = [
            'tamamlanan'    => ['Kapatıldı', 'İptal Edildi'],
            'devam_eden'    => ['Yeni', 'İşlemde', 'Atandı'],
            'onay_bekleyen' => ['Onay Bekliyor'] // Müşteri portalı statüsü olarak 'Onay Bekliyor' varsayılabilir veya boş kalabilir ama aşağıda özelleştireceğiz
        ];

        $filterStatusDate = function ($q, $statusList) use ($filters) {
            if (!empty($filters['year']))
                $q->whereYear('updated_at', $filters['year']);
            if (!empty($filters['month']))
                $q->whereMonth('updated_at', $filters['month']);
            $q->whereIn('durum', $statusList);
        };

        $filterSikayetStatusDate = function ($q, $statusList) use ($filters) {
            if (!empty($filters['year']))
                $q->whereYear('updated_at', $filters['year']);
            if (!empty($filters['month']))
                $q->whereMonth('updated_at', $filters['month']);
            $q->whereIn('musteri_durum', $statusList);
        };

        foreach ($statuses as $key => $statusList) {
            $iaaQ = $baseQuery()->tap(fn($q) => $q->sadeceOneriler());
            $filterStatusDate($iaaQ, $statusList);
            $stats['dagilim']['iaa'][$key] = $iaaQ->count();
            $stats['list']['iaa'][$key] = $iaaQ->with(['atananTakim.lider', 'gonderen'])->latest()->take(5)->get();

            if ($responsibleForComplaints) {
                $compQ = $baseSikayetQuery();
                
                if ($key === 'onay_bekleyen') {
                    // Şikayete bağlı İAA projesi onay bekleyen statülerinden birindeyse 'Onay Bekleyen' say
                    $compQ->whereHas('iaaProjesi', function($sq) use ($statusList) {
                        $sq->whereIn('durum', $statusList);
                    });
                } elseif ($key === 'islemde' || $key === 'devam_eden') {
                    // İşlemde olanlar ama İAA'sı onay beklemeyenler
                    $compQ->whereIn('musteri_durum', $sikayetStatuses[$key])
                          ->where(function($sq1) use ($statuses) {
                              $sq1->whereDoesntHave('iaaProjesi')
                                  ->orWhereHas('iaaProjesi', function($sq2) use ($statuses) {
                                      $sq2->whereNotIn('durum', $statuses['onay_bekleyen']);
                                  });
                          });
                } else {
                    $filterSikayetStatusDate($compQ, $sikayetStatuses[$key]);
                }

                $stats['dagilim']['sikayet'][$key] = $compQ->count();
                $stats['list']['sikayet'][$key] = (clone $compQ)->with(['cozumTakimi.lider', 'iaaProjesi'])->latest()->take(5)->get();
            } else {
                $stats['dagilim']['sikayet'][$key] = 0;
                $stats['list']['sikayet'][$key] = collect();
            }
        }

        // --- 5. GECİKENLER (Ek Kategori) ---
        $gecikenBaseQuery = $baseQuery()->whereNotIn('durum', ['Tamamlandı', 'Talep Olarak Kapatıldı', 'talep_olarak_kapatildi', 'Reddedildi', 'İptal Edildi'])
            ->whereHas('talepEdenTakimlar', function ($q) {
                $q->where('iaa_talepleri.due_date', '<', now());
            });
        $stats['dagilim']['iaa']['geciken'] = (clone $gecikenBaseQuery)->sadeceOneriler()->count();
        $stats['list']['iaa']['geciken'] = (clone $gecikenBaseQuery)->sadeceOneriler()->with(['atananTakim.lider', 'gonderen'])->latest()->take(5)->get();

        if ($responsibleForComplaints) {
            $stats['dagilim']['sikayet']['geciken'] = (clone $gecikenBaseQuery)->whereHas('musteriSikayeti')->count();
            $stats['list']['sikayet']['geciken'] = (clone $gecikenBaseQuery)->whereHas('musteriSikayeti')->with(['atananTakim.lider', 'musteriSikayeti'])->latest()->take(5)->get();
        } else {
            $stats['dagilim']['sikayet']['geciken'] = 0;
            $stats['list']['sikayet']['geciken'] = collect();
        }

        // --- 6. SAF İAA PROJELERİ (Tablo İçin) ---
        $iaaTableQuery = $baseQuery()->tap(fn($q) => $q->sadeceOneriler());
        $applyDateFilter($iaaTableQuery, 'iaa', 'created_at');
        $stats['bolum_iaa_projeleri'] = $iaaTableQuery
            ->with(['atananTakim.lider', 'gonderen'])
            ->latest()
            ->take(10)
            ->get();
            
        $lastMovesIaa = $baseQuery()->tap(fn($q) => $q->sadeceOneriler());
        $applyDateFilter($lastMovesIaa, 'iaa', 'created_at');
        $stats['total_iaa_count'] = (clone $lastMovesIaa)->count();

        if ($responsibleForComplaints) {
            $lastMovesComplaints = $baseSikayetQuery();
            $applyDateFilter($lastMovesComplaints, 'sikayet', 'created_at');
            $stats['total_sikayet_count'] = $lastMovesComplaints->count();
        } else {
            $stats['total_sikayet_count'] = 0;
        }

        // --- 5. PERSONEL LİSTESİ ---
        // Bölüm Liderini bul (Rol üzerinden)
        $lider = User::where('bolum_id', $bolum->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Bölüm Lideri');
            })->first();

        $liderId = $lider ? $lider->id : 0;
        $bolum->lider_user_id = $liderId; // Blade tarafında isLider kontrolü için

        // Bölümdeki tüm kullanıcılar (lider en üstte, sonra son online olma zamanı)
        $stats['personel_listesi'] = User::where('bolum_id', $bolum->id)
            ->withCount([
                'gorevliOlduguProjeler' => function ($q) {
                    $q->whereIn('iaas.durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor']);
                }
            ])
            ->with([
                'gorevliOlduguProjeler' => function ($q) {
                    $q->whereIn('iaas.durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor'])
                        ->select('iaas.id', 'iaas.baslik', 'iaas.durum');
                },
                'roles'
            ])
            ->orderByRaw("id = ? DESC", [$bolum->lider_user_id ?? 0]) // Lider her zaman en üstte
            ->orderByDesc('last_seen_at')
            ->get();

        // --- 6. PERSONEL BEKLEYEN GÖREVLERİ (YENİ - V5.30) ---
        // Bu bölümdeki tüm personellerin (Lider dahil) "Şikayet Görevlerim" sayfasındaki gibi bekleyen işlerini getirir.
        $gorevQuery = Iaa::whereHas('musteriSikayeti')
            ->where(function ($topQ) use ($bolum, $personelIds) {
                // A) Bölüm Onayı Bekleyenler (Bölüm Lideri veya Kalite Yöneticisi için)
                $topQ->orWhere(function ($q) use ($bolum) {
                    $q->where('durum', 'Bölüm Onayı Bekliyor')
                        ->whereHas('musteriSikayeti.sikayetKategori', function ($k) use ($bolum) {
                            $k->where('bolum_id', $bolum->id);
                        });
                });

                // B) Personellere atanmış aktif adımlar
                $topQ->orWhereHas('stepAssignments', function ($assignQ) use ($personelIds) {
                    $assignQ->whereIn('user_id', $personelIds)
                        ->whereNotExists(function ($sub) {
                            $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                                ->from('iaa_progress_updates')
                                ->join('iaa_talepleri', 'iaa_progress_updates.iaa_talep_id', '=', 'iaa_talepleri.id')
                                ->whereColumn('iaa_talepleri.iaa_id', 'iaa_step_assignments.iaa_id')
                                ->whereColumn('iaa_progress_updates.iaa_workflow_step_id', 'iaa_step_assignments.iaa_workflow_step_id')
                                ->whereNotNull('completed_at');
                        });
                });

                // C) Personellerin dahil olduğu aktif projeler
                $topQ->orWhere(function ($activeQ) use ($personelIds) {
                    $activeQ->whereNotIn('durum', ['Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Talep Olarak Kapatıldı'])
                        ->where(function ($teamQ) use ($personelIds) {
                            $teamQ->whereHas('projeEkibi', function ($pe) use ($personelIds) {
                                $pe->whereIn('users.id', $personelIds);
                            })
                                ->orWhereHas('atananTakim', function ($at) use ($personelIds) {
                                    $at->whereIn('lider_user_id', $personelIds);
                                });
                        });
                });
            });
        
        $applyDateFilter($gorevQuery, 'gorev', 'updated_at');

        $stats['bolum_personel_gorevleri'] = $gorevQuery
            ->distinct()
            ->with([
                'musteriSikayeti.sikayetKategori',
                'aktifAdim.sorumlular',
                'atananTakim'
            ])
            ->latest('updated_at')
            ->take(15)
            ->get();

        return $stats;
    }


    /**
     * Direktör için tüm bölümlerin agrega verilerini hesaplar (Dökümlü).
     */
    public function getDirectorAggregateStats($bolumIds, array $filters = [])
    {
        $bolumler = \App\Models\Bolum::whereIn('id', $bolumIds)->get();
        $results = [
            'sikayet' => ['total' => 0, 'breakdown' => []],
            'proje' => ['total' => 0, 'breakdown' => []],
            'saf_iaa' => ['total' => 0, 'breakdown' => []],
            'disiplin' => ['total' => 0, 'breakdown' => []],
        ];

        $filterYear = $filters['year'] ?? null;
        $filterMonth = $filters['month'] ?? null;

        $applyDateFilter = function ($query, $column = 'created_at') use ($filterYear, $filterMonth) {
            if (!empty($filterYear))
                $query->whereYear($column, $filterYear);
            if (!empty($filterMonth))
                $query->whereMonth($column, $filterMonth);
        };

        foreach ($bolumler as $bolum) {
            $personelIds = User::where('bolum_id', $bolum->id)->pluck('id');

            // Helper for inclusive logic
            $inclusiveQuery = function ($q) use ($bolum, $personelIds) {
                $q->where('bolum_id', $bolum->id)
                    ->orWhereHas('projeEkibi', fn($sq) => $sq->whereIn('users.id', $personelIds))
                    ->orWhereHas('atananTakim', fn($sq) => $sq->whereIn('lider_user_id', $personelIds));
            };

            // 1. Şikayetler (Bölüme ait)
            $sikayetQ = \App\Models\MusteriSikayeti::whereHas('sikayetKategori', function ($q) use ($bolum) {
                $q->where('bolum_id', $bolum->id);
            });
            $applyDateFilter($sikayetQ, 'created_at');
            $sikayetCount = $sikayetQ->count();
            $results['sikayet']['total'] += $sikayetCount;
            $results['sikayet']['breakdown'][$bolum->ad] = $sikayetCount;

            // 2. Saf İAA (Şikayet olmayan + Personel dahil olan)
            $safIaaQ = Iaa::sadeceOneriler()
                ->where($inclusiveQuery);
            $applyDateFilter($safIaaQ, 'created_at');
            $safIaaCount = $safIaaQ->count();
            $results['saf_iaa']['total'] += $safIaaCount;
            $results['saf_iaa']['breakdown'][$bolum->ad] = $safIaaCount;

            // 3. Toplam Proje (Şikayet + Saf İAA, Aktif Durumdakiler)
            $projeQ = Iaa::whereIn('durum', ['Atandı', 'Devam Ediyor', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin', 'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'])
                ->where(function ($q) use ($inclusiveQuery, $bolum) {
                    $inclusiveQuery($q);
                    $q->orWhereHas('musteriSikayeti.sikayetKategori', function ($sq) use ($bolum) {
                        $sq->where('bolum_id', $bolum->id);
                    });
                });
            $applyDateFilter($projeQ, 'created_at');
            $projeCount = $projeQ->count();
            $results['proje']['total'] += $projeCount;
            $results['proje']['breakdown'][$bolum->ad] = $projeCount;

            // 4. Disiplin
            $disiplinQ = \App\Models\DisciplinaryCase::whereHas('user', function ($q) use ($bolum) {
                $q->where('bolum_id', $bolum->id);
            });
            $applyDateFilter($disiplinQ, 'created_at');
            $disiplinCount = $disiplinQ->count();
            $results['disiplin']['total'] += $disiplinCount;
            $results['disiplin']['breakdown'][$bolum->ad] = $disiplinCount;
        }

        return $results;
    }
}
