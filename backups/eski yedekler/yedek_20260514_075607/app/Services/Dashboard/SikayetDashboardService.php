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
    public function getBoardStats()
    {
        return [
            'toplam_sikayet' => MusteriSikayeti::count(),
            'yeni_sikayet' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
            'islemde_sikayet' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
            'tamamlanan_sikayet' => MusteriSikayeti::whereIn('musteri_durum', ['Tamamlandı', 'Kapatıldı', 'Çözümlendi'])->count(),
            'son_sikayetler' => MusteriSikayeti::with([
                'sikayetKategori.bolum', 
                'cozumTakimi.lider', 
                'olusturanKurulUyesi', 
                'customer',
                'iaaProjesi.aktifAdim'
            ])->latest()->take(15)->get(),
            'kurul_uyeleri' => User::role('Müşteri Şikayeti Kurulu')
                ->with('bolum')
                ->withCount('girdigiSikayetler')
                ->withSum('girdigiSikayetler', 'kazanilan_puan')
                ->orderByDesc('girdigi_sikayetler_count')
                ->get(),
            'hatirlatmalar' => \App\Models\SikayetHatirlatma::with(['musteriSikayeti.customer', 'gonderen'])
                ->latest()
                ->take(5)
                ->get(),
            'toplam_hatirlatma_sayisi' => \App\Models\SikayetHatirlatma::count(),
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
        ];

        if ($stats['has_teams']) {
            // Liderin takımlarına atanmış olan tüm şikayetleri çek (squad bazlı olarak)
            $query = MusteriSikayeti::whereIn('atanan_cozum_takimi_id', $liderinTakimlariIds)
                ->with([
                    'customer',
                    'sikayetKategori.bolum', // Eager load department info
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
            
            // "Onay Bekleyenler"
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
            
            
            $stats['onay_bekleyen_sikayetler'] = $onayBekleyenler;
            
            // "Aktif İşlemde Olanlar" (Liderin takımındaki güncel squad görevleri)
            $aktifSikayetler = $sikayetler->filter(function($sikayet) use ($filters) {
                // Kapatılmış veya iptal edilmişleri çıkar
                if (in_array($sikayet->musteri_durum, ['Kapatıldı', 'Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Çözümlendi'])) {
                    return false;
                }
                // Onayda bekleyenleri de listeden çıkar (farklı listede var)
                if (in_array($sikayet->musteri_durum, ['Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Direktör Onayı Bekliyor'])) {
                    return false;
                }
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
                    return false;
                }
                
                // Aktif Şikayetler Özel Filtreleri
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
            
            // Sorumlu olunan müşteri firmalarının listesi
            $stats['sorumlu_musteriler'] = $sikayetler->pluck('customer')->filter()->unique('id')->each(function($customer) {
                // yetkilileri yükle
                $customer->loadMissing('users');
            })->values();

            // Aktif şikayeti olan müşterilerin listesi (Filtreleme için)
            $stats['aktif_sikayet_musterileri'] = $aktifSikayetler->pluck('customer')->filter()->unique('id')->values();

            // Müşteri şikayeti çözüm liderinin sorumlu olduğu şikayetlere ait iadeler
            $sikayetIds = $sikayetler->pluck('id')->filter()->toArray();
            $stats['iadeler'] = \App\Models\SikayetIadesi::with('musteriSikayeti.customer', 'user')
                ->whereIn('musteri_sikayeti_id', $sikayetIds)
                ->orderByDesc('created_at')
                ->get();

            // Sorumlu olunan şikayetlerin bağlı olduğu projelere (iaa_id) ait ziyaretler
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
