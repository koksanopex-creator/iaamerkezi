<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\MusteriSikayeti;
use App\Models\Takim;
use App\Models\Iaa;

class SikayetDashboardService
{
    /**
     * Müşteri Şikayeti Kurulu İstatistikleri
     */
    public function getBoardStats(User $user = null)
    {
        $query = MusteriSikayeti::query();
        
        if ($user) {
            if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                $query->where('konum_tipi', 'Yurt İçi');
            } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                $query->where('konum_tipi', 'Yurt Dışı');
            }
        }
        return [
            'toplam_sikayet' => (clone $query)->count(),
            'yeni_sikayet' => (clone $query)->where('musteri_durum', 'Yeni')->count(),
            'islemde_sikayet' => (clone $query)->where('musteri_durum', 'İşlemde')->count(),
            'tamamlanan_sikayet' => (clone $query)->whereIn('musteri_durum', ['Tamamlandı', 'Kapatıldı', 'Çözümlendi'])->count(),
            'son_sikayetler' => (clone $query)->with([
                'sikayetKategori.bolum', 
                'cozumTakimi.lider', 
                'olusturanKurulUyesi', 
                'customer',
                'iaaProjesi.aktifAdim'
            ])->latest()->take(15)->get(),
            'kurul_uyeleri' => User::role(['Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi', 'Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])
                ->when($user, function($q) use ($user) {
                    if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                        $q->role(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi']);
                    } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                        $q->role(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı']);
                    }
                })
                ->with('bolum')
                ->withCount('girdigiSikayetler')
                ->withSum('girdigiSikayetler', 'kazanilan_puan')
                ->orderByDesc('girdigi_sikayetler_count')
                ->get(),
            'hatirlatmalar' => \App\Models\SikayetHatirlatma::with(['musteriSikayeti.customer', 'gonderen'])
                ->whereHas('musteriSikayeti', function($q) use ($user) {
                    if ($user) {
                        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                            $q->where('konum_tipi', 'Yurt İçi');
                        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                            $q->where('konum_tipi', 'Yurt Dışı');
                        }
                    }
                })
                ->latest()
                ->take(5)
                ->get(),
            'toplam_hatirlatma_sayisi' => \App\Models\SikayetHatirlatma::whereHas('musteriSikayeti', function($q) use ($user) {
                    if ($user) {
                        if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                            $q->where('konum_tipi', 'Yurt İçi');
                        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                            $q->where('konum_tipi', 'Yurt Dışı');
                        }
                    }
                })->count(),
        ];
    }

    /**
     * Çözüm Lideri İstatistikleri
     */
    public function getLeaderStats(User $user, $filters = [])
    {
        // Liderin sorumlu olduğu tüm şikayet takımlarını bul
        $liderinTakimlariIds = Takim::where('lider_user_id', $user->id)
            ->where('tur', 'sikayet')
            ->pluck('id');
            
        $liderinIlkTakimi = Takim::where('lider_user_id', $user->id)->where('tur', 'sikayet')->first();

        $stats = [
            'has_teams' => $liderinTakimlariIds->count() > 0,
            'lider_takim' => $liderinIlkTakimi, // Geriye dönük uyumluluk veya primary takım için
            'sorumlu_oldugu_bolumler' => MusteriSikayeti::whereIn('atanan_cozum_takimi_id', $liderinTakimlariIds)
                ->with('sikayetKategori.bolum')
                ->get()
                ->pluck('sikayetKategori.bolum.ad')
                ->unique()
                ->filter()
                ->values(),
        ];

        if ($stats['has_teams']) {
            // --- TÜM ZAMANLAR VERİLERİ (Filtresiz) ---
            $allTimeQuery = MusteriSikayeti::whereIn('atanan_cozum_takimi_id', $liderinTakimlariIds)
                ->with(['iaaProjesi', 'sikayetKategori.bolum']);
            $allTimeSikayetler = $allTimeQuery->get();

            $stats['toplam_sikayet_sayisi_all_time'] = $allTimeSikayetler->count();
            $stats['cozulen_sikayetler_count_all_time'] = $allTimeSikayetler->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->count();
            
            // "Onay Bekleyenler" (All Time)
            $onayBekleyenlerAllTime = $allTimeSikayetler->filter(function($sikayet) {
                if (in_array($sikayet->musteri_durum, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor'])) {
                    return true;
                }
                if ($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, [
                    'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor',
                    'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin',
                    'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'
                ])) {
                    return true;
                }
                return false;
            });
            $stats['onay_bekleyen_sikayetler_count_all_time'] = $onayBekleyenlerAllTime->count();

            // "Aktif İşlemde Olanlar" (All Time)
            $aktifSikayetlerAllTime = $allTimeSikayetler->filter(function($sikayet) {
                if (in_array($sikayet->musteri_durum, ['Kapatıldı', 'Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Çözümlendi'])) {
                    return false;
                }
                if (in_array($sikayet->musteri_durum, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor'])) {
                    return false;
                }
                if ($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, [
                    'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor',
                    'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin',
                    'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'
                ])) {
                    return false;
                }
                return true;
            });
            $stats['aktif_sikayet_count_all_time'] = $aktifSikayetlerAllTime->count();

            // --- BÖLÜM BAZLI DAĞILIMLAR ---
            $stats['breakdown'] = [
                'toplam' => $allTimeSikayetler->groupBy('sikayetKategori.bolum.ad')->map->count(),
                'aktif' => $aktifSikayetlerAllTime->groupBy('sikayetKategori.bolum.ad')->map->count(),
                'tamamlanan' => $allTimeSikayetler->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->groupBy('sikayetKategori.bolum.ad')->map->count(),
                'onay_bekleyen' => $onayBekleyenlerAllTime->groupBy('sikayetKategori.bolum.ad')->map->count(),
            ];

            // --- FİLTRELİ VERİLER (Mevcut Dashboard Görünümü) ---
            $query = MusteriSikayeti::whereIn('atanan_cozum_takimi_id', $liderinTakimlariIds)
                ->with([
                    'customer',
                    'sikayetKategori.bolum.director', 
                    'sikayetKategori.yoneticiler',
                    'cozumTakimi.uyeler',
                    'cozumTakimi.davetiyeler' => function($q) {
                        $q->where('durum', 'bekliyor')->with('davetEdilen', 'davetEden');
                    },
                    'iaaProjesi.stepAssignments.user',
                    'iaaProjesi.aktifAdim'
                ]);
                
            // Global Date Filters
            if (!empty($filters['start_date'])) {
                $query->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $query->whereDate('created_at', '<=', $filters['end_date']);
            }

            $sikayetler = $query->get();
                
            $stats['toplam_sikayet_sayisi'] = $sikayetler->count();
            $stats['cozulen_sikayetler_count'] = $sikayetler->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])->count();
            $stats['islemde_sikayetler_count'] = $sikayetler->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'Atandı', 'İnceleniyor', 'Devam Ediyor', 'Revize Ediliyor'])->count();
            
            // "Onay Bekleyenler" (Filtreli)
            $onayBekleyenler = $sikayetler->filter(function($sikayet) {
                // Şikayet statüsü onay bekliyorsa
                if (in_array($sikayet->musteri_durum, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor'])) {
                    return true;
                }
                // Veya proje bağlıysa ve onay aşamasındaysa
                if ($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, [
                    'Bölüm Onayı Bekliyor', 
                    'Yönetici Onayı Bekliyor', 
                    'talep_onayi_bekliyor_kalite', 
                    'talep_onayi_bekliyor_direktor',
                    'talep_onayi_bekliyor_superadmin', 
                    'Direktör Onayı Bekliyor',
                    'hatali_bildirim_onayi_bekliyor_kalite',
                    'hatali_bildirim_onayi_bekliyor_direktor',
                    'hatali_bildirim_onayi_bekliyor_superadmin'
                ])) {
                    return true;
                }
                return false;
            })->sortByDesc('updated_at');
            
            // Onaylayacak Kişiyi Ekle (Superadmin sorgusunu döngü dışına alma)
            $defaultSuperAdmin = User::role('Superadmin')->first();
            $onayBekleyenler->each(function($sikayet) use ($defaultSuperAdmin) {
                $durum = $sikayet->iaaProjesi ? $sikayet->iaaProjesi->durum : $sikayet->musteri_durum;
                $approver = null;

                if (str_contains($durum, 'Bölüm Onayı') || str_contains($durum, '_kalite')) {
                    $approver = $sikayet->sikayetKategori->yoneticiler->first();
                } elseif (str_contains($durum, 'Direktör Onayı') || str_contains($durum, '_direktor')) {
                    $approver = $sikayet->sikayetKategori->bolum->director ?? null;
                } elseif (str_contains($durum, 'Yönetici Onayı') || str_contains($durum, '_superadmin')) {
                    $approver = $defaultSuperAdmin;
                }

                $sikayet->onaya_gonderilme_tarihi = $sikayet->updated_at;
                $sikayet->onaylayacak_kisi = $approver ? (object)[
                    'name' => $approver->name,
                    'unvan' => $approver->unvan ?? 
                              $approver->roles->pluck('name')->intersect(['Superadmin', 'Direktör', 'Bölüm Lideri', 'Hukuk Admini', 'Kalite Müdürü'])->first() ?? 
                              $approver->roles->first()?->name ?? 
                              $approver->gorev_tanimi ?? 'Yönetici',
                    'profile_photo_url' => $approver->profile_photo_url
                ] : (object)[
                    'name' => 'Bekleniyor',
                    'unvan' => 'Yetkili Atanmadı',
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=?'
                ];
            });
            
            $stats['onay_bekleyen_sikayetler'] = $onayBekleyenler;
            
            // "Aktif İşlemde Olanlar" (Filtreli)
            $aktifSikayetler = $sikayetler->filter(function($sikayet) use ($filters) {
                if (in_array($sikayet->musteri_durum, ['Kapatıldı', 'Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Çözümlendi'])) {
                    return false;
                }
                if (in_array($sikayet->musteri_durum, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor'])) {
                    return false;
                }
                if ($sikayet->iaaProjesi && in_array($sikayet->iaaProjesi->durum, [
                    'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor',
                    'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin',
                    'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'
                ])) {
                    return false;
                }
                
                if (!empty($filters['sikayet_bolum_id'])) {
                    if (!$sikayet->sikayetKategori || $sikayet->sikayetKategori->bolum_id != $filters['sikayet_bolum_id']) {
                        return false;
                    }
                }
                if (!empty($filters['sikayet_durum'])) {
                    if ($sikayet->musteri_durum != $filters['sikayet_durum']) {
                        return false;
                    }
                }
                if (!empty($filters['sikayet_musteri_id'])) {
                    if ($sikayet->customer_id != $filters['sikayet_musteri_id']) {
                        return false;
                    }
                }
                if (!empty($filters['sikayet_arama'])) {
                    $search = mb_strtolower($filters['sikayet_arama']);
                    $subject = mb_strtolower($sikayet->musteri_sikayet_konusu);
                    if (!str_contains($subject, $search)) {
                        return false;
                    }
                }
                return true;
            })->sortByDesc('updated_at');
            
            $stats['aktif_sikayetler_projeler'] = $aktifSikayetler;
            
            $stats['sorumlu_musteriler'] = $sikayetler->pluck('customer')->filter()->unique('id')->each(function($customer) {
                $customer->loadMissing('users');
            })->values();

            $stats['aktif_sikayet_musterileri'] = $aktifSikayetler->pluck('customer')->filter()->unique('id')->values();

            $sikayetIds = $sikayetler->pluck('id')->filter()->toArray();
            $stats['iadeler'] = \App\Models\SikayetIadesi::with('musteriSikayeti.customer', 'user')
                ->whereIn('musteri_sikayeti_id', $sikayetIds)
                ->orderByDesc('created_at')
                ->get();

            $iaaIds = $sikayetler->pluck('iaa_id')->filter()->toArray();
            $stats['ziyaretler'] = \App\Models\IaaZiyaretPlani::with(['iaa', 'visitor'])
                ->whereIn('iaa_id', $iaaIds)
                ->orderByDesc('created_at')
                ->get();
            $stats['liderin_sikayetleri'] = $sikayetler;
        }

        return $stats;
    }
}
