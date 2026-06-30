<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\MusteriSikayeti;
use App\Models\IaaZiyaretPlani;
use Carbon\Carbon;

class MusteriSahaTemsilcisiDashboardService
{
    /**
     * Müşteri Saha Temsilcisi İstatistikleri
     */
    public function getStats(User $user, $filters = [])
    {
        // Yetkili olduğu bölümlerin ID'leri
        $bolumIds = $user->getAllowedBolumIds();
        
        // Eğer hiçbir bölüme atanmamışsa boş dön
        if (empty($bolumIds)) {
            return [
                'has_bolum' => false,
                'sorumlu_bolum_isimleri' => [],
            ];
        }

        // Yetkili olduğu bölüm adları (Görsel amaçlı)
        $bolumler = \App\Models\Bolum::whereIn('id', $bolumIds)->pluck('ad')->toArray();

        // 1. ZİYARETLER (En Önemli Odak Noktası)
        $ziyaretlerSorgu = IaaZiyaretPlani::with(['iaa.musteriSikayeti.customer', 'visitor'])
            ->where(function($q) use ($user, $bolumIds) {
                // Ya kendisi ziyaretçi
                $q->where('visitor_id', $user->id)
                  ->orWhereJsonContains('visitors', (string)$user->id)
                  ->orWhereJsonContains('visitors', $user->id)
                  // Ya da sorumlu olduğu bölümlere ait şikayetlerin/projelerin ziyaretleri
                  ->orWhereHas('iaa.musteriSikayeti.sikayetKategori', function($sq) use ($bolumIds) {
                      $sq->whereIn('bolum_id', $bolumIds);
                  });
            });

        // Tüm Ziyaretler (Aktif ve Geçmiş)
        $tumZiyaretler = (clone $ziyaretlerSorgu)->orderBy('visit_date', 'asc')->get();

        // Yaklaşan Ziyaretler (Bugün ve Sonrası)
        $yaklasanZiyaretler = $tumZiyaretler->filter(function($ziyaret) {
            return Carbon::parse($ziyaret->visit_date)->startOfDay()->gte(now()->startOfDay()) && 
                   !in_array($ziyaret->status, ['Tamamlandı', 'İptal Edildi']);
        });

        // Bekleyen/Gecikmiş Ziyaretler (Tarihi geçmiş ama tamamlanmamış)
        $gecikmisZiyaretler = $tumZiyaretler->filter(function($ziyaret) {
            return Carbon::parse($ziyaret->visit_date)->startOfDay()->lt(now()->startOfDay()) && 
                   !in_array($ziyaret->status, ['Tamamlandı', 'İptal Edildi']);
        });
        
        // Takvim Etkinlikleri (FullCalendar Formatı)
        $takvimEtkinlikleri = $tumZiyaretler->map(function($ziyaret) {
            $renk = '#3b82f6'; // Mavi (Planlandı)
            if ($ziyaret->status === 'Tamamlandı') $renk = '#10b981'; // Yeşil
            elseif ($ziyaret->status === 'İptal Edildi') $renk = '#ef4444'; // Kırmızı
            elseif (Carbon::parse($ziyaret->visit_date)->lt(now()) && $ziyaret->status !== 'Tamamlandı') $renk = '#f59e0b'; // Sarı (Gecikmiş)
            
            $title = 'Genel Ziyaret';
            $departmentName = '';
            $complaintSubject = '';
            $customerReps = '';
            $visitorsNames = '';

            if ($ziyaret->iaa && $ziyaret->iaa->musteriSikayeti) {
                $sikayet = $ziyaret->iaa->musteriSikayeti;
                if ($sikayet->customer) {
                    $title = $sikayet->customer->name;
                }
                $complaintSubject = $sikayet->musteri_sikayet_konusu;
                if ($sikayet->sikayetKategori && $sikayet->sikayetKategori->bolum) {
                    $departmentName = $sikayet->sikayetKategori->bolum->name;
                }
            }

            // Müşteri Temsilcileri
            if (!empty($ziyaret->contact_persons) && is_array($ziyaret->contact_persons)) {
                $reps = [];
                foreach ($ziyaret->contact_persons as $cp) {
                    if (isset($cp['name'])) {
                        $reps[] = $cp['name'];
                    }
                }
                $customerReps = implode(', ', $reps);
            }

            // Ziyaret Personelleri
            $zNames = [];
            if ($ziyaret->visitor) {
                $zNames[] = $ziyaret->visitor->name;
            }
            if (is_array($ziyaret->visitors) && count($ziyaret->visitors) > 0) {
                $ekZiyaretciler = \App\Models\User::whereIn('id', $ziyaret->visitors)->pluck('name')->toArray();
                $zNames = array_unique(array_merge($zNames, $ekZiyaretciler));
            }
            if (empty($zNames) && !empty($ziyaret->visitor_name)) {
                $zNames[] = $ziyaret->visitor_name;
            }
            $visitorsNames = implode(', ', $zNames);

            return [
                'id' => $ziyaret->id,
                'title' => $title . ' Ziyareti',
                'start' => Carbon::parse($ziyaret->visit_date)->format('Y-m-d'),
                'color' => $renk,
                'url' => $ziyaret->iaa_id ? route('proje.workspace.show', $ziyaret->iaa_id) . '#ziyaret-bilgileri-alani' : url('/ziyaretler'),
                'extendedProps' => [
                    'status' => $ziyaret->status,
                    'purpose' => $ziyaret->visit_reason,
                    'department' => $departmentName,
                    'complaint' => $complaintSubject,
                    'reps' => $customerReps,
                    'visitors' => $visitorsNames
                ]
            ];
        });

        // 2. ŞİKAYETLER VE İSTATİSTİKLER (Yetkili Bölümlere Göre)
        $sikayetlerSorgu = MusteriSikayeti::whereHas('sikayetKategori', function($q) use ($bolumIds) {
            $q->whereIn('bolum_id', $bolumIds);
        })->with(['customer', 'sikayetKategori.bolum']);

        $tumSikayetler = $sikayetlerSorgu->get();

        $aktifSikayetler = $tumSikayetler->filter(function($sikayet) {
            return !in_array($sikayet->musteri_durum, ['Kapatıldı', 'Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Çözümlendi']);
        });

        // 3. İADELER
        $iadeler = \App\Models\SikayetIadesi::whereHas('musteriSikayeti.sikayetKategori', function($q) use ($bolumIds) {
            $q->whereIn('bolum_id', $bolumIds);
        })->with('musteriSikayeti.customer', 'user')->latest()->take(10)->get();

        // 4. MÜŞTERİ HATIRLATMALARI
        $hatirlatmalar = \App\Models\SikayetHatirlatma::whereHas('musteriSikayeti.sikayetKategori', function($q) use ($bolumIds) {
            $q->whereIn('bolum_id', $bolumIds);
        })->with(['musteriSikayeti.customer', 'gonderen'])->latest()->take(10)->get();

        return [
            'has_bolum' => true,
            'sorumlu_bolum_isimleri' => $bolumler,
            
            // Ziyaretler
            'tum_ziyaretler' => $tumZiyaretler,
            'yaklasan_ziyaretler' => $yaklasanZiyaretler,
            'gecikmis_ziyaretler' => $gecikmisZiyaretler,
            'takvim_etkinlikleri' => $takvimEtkinlikleri,
            'ziyaret_count' => $tumZiyaretler->count(),
            'yaklasan_ziyaret_count' => $yaklasanZiyaretler->count(),
            'gecikmis_ziyaret_count' => $gecikmisZiyaretler->count(),

            // Şikayetler
            'toplam_sikayet_sayisi' => $tumSikayetler->count(),
            'aktif_sikayet_sayisi' => $aktifSikayetler->count(),
            'cozulen_sikayetler_count' => $tumSikayetler->whereIn('musteri_durum', ['Kapatıldı', 'Tamamlandı', 'Çözümlendi'])->count(),
            'son_sikayetler' => $tumSikayetler->sortByDesc('created_at')->take(10)->values(),
            
            // Ekstralar
            'iadeler' => $iadeler,
            'hatirlatmalar' => $hatirlatmalar,
            
            // Aktif Müşteriler (Benzersiz Liste)
            'sorumlu_musteriler' => $tumSikayetler->pluck('customer')->filter()->unique('id')->each(function($customer) {
                $customer->loadMissing('users');
            })->values(),
        ];
    }
}
