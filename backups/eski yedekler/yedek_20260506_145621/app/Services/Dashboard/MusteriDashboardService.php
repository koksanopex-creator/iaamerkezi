<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\MusteriSikayeti;
use Carbon\Carbon;

class MusteriDashboardService
{
    public function getStats(User $user, $startDate = null, $endDate = null)
    {
        $stats = [];
        $customerId = $user->customer_id;

        if ($customerId) {
            $baseQuery = MusteriSikayeti::where('customer_id', $customerId);
            
            if ($startDate || $endDate) {
                $baseQuery->where(function ($query) use ($startDate, $endDate) {
                    if ($startDate) {
                        $query->where(function ($q) use ($startDate) {
                            $q->whereDate('created_at', '>=', $startDate)
                              ->orWhereDate('updated_at', '>=', $startDate);
                        });
                    }
                    if ($endDate) {
                        $query->where(function ($q) use ($endDate) {
                            $q->whereDate('created_at', '<=', $endDate)
                              ->orWhereDate('updated_at', '<=', $endDate);
                        });
                    }
                });
            }

            // Temel Sayılar
            $stats['toplam_sikayet'] = (clone $baseQuery)->count();

            $stats['aktif_sikayet'] = (clone $baseQuery)
                ->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'İnceleniyor'])
                ->count();

            $stats['cozulen_sikayet'] = (clone $baseQuery)
                ->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                ->count();

            // ORTALAMA ÇÖZÜM SÜRESİ HESAPLAMA
            $cozulmusKayitlar = (clone $baseQuery)
                ->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                ->get();

            $toplamGun = 0;
            $adet = $cozulmusKayitlar->count();
            if ($adet > 0) {
                foreach ($cozulmusKayitlar as $kayit) {
                    $cozumTarihi = $kayit->musteri_cozum_son_tarihi ?? $kayit->updated_at;
                    if (!($cozumTarihi instanceof \Carbon\Carbon)) {
                        $cozumTarihi = Carbon::parse($cozumTarihi);
                    }

                    // En az 1 gün sayılması için min(1, diff) yaklaşımı da kullanılabilir ancak gün farkı olarak alıyoruz
                    $gunFarki = $kayit->created_at->diffInDays($cozumTarihi);
                    $toplamGun += ($gunFarki == 0 ? 1 : $gunFarki); // Aynı gün çözüldüyse "0 gün" değil "1 gün" yazılsın
                }
                $stats['ortalama_sure'] = round($toplamGun / $adet);
            } else {
                $stats['ortalama_sure'] = 0;
            }

            // AKTİF SÜREÇTEKİ DETAYLI ŞİKAYETLER
            $stats['son_sikayetler'] = (clone $baseQuery)
                ->with(['iaaProjesi', 'sikayetKategori', 'cozumTakimi.lider'])
                ->latest()
                ->take(10)
                ->get();
        }

        return $stats;
    }
}
