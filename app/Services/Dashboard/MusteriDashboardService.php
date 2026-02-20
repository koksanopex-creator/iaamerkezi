<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\MusteriSikayeti;
use Carbon\Carbon;

class MusteriDashboardService
{
    public function getStats(User $user)
    {
        $stats = [];
        $customerId = $user->customer_id;

        if ($customerId) {
            // Temel Sayılar
            $stats['toplam_sikayet'] = MusteriSikayeti::where('customer_id', $customerId)->count();

            $stats['aktif_sikayet'] = MusteriSikayeti::where('customer_id', $customerId)
                ->whereIn('musteri_durum', ['Yeni', 'İşlemde', 'İnceleniyor'])
                ->count();

            $stats['cozulen_sikayet'] = MusteriSikayeti::where('customer_id', $customerId)
                ->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])
                ->count();

            // ORTALAMA ÇÖZÜM SÜRESİ HESAPLAMA
            $cozulmusKayitlar = MusteriSikayeti::where('customer_id', $customerId)
                ->whereNotNull('musteri_cozum_son_tarihi')
                ->get();

            $toplamGun = 0;
            $adet = $cozulmusKayitlar->count();
            if ($adet > 0) {
                foreach ($cozulmusKayitlar as $kayit) {
                    $cozumTarihi = $kayit->musteri_cozum_son_tarihi;
                    if (!($cozumTarihi instanceof \Carbon\Carbon)) {
                        $cozumTarihi = Carbon::parse($cozumTarihi);
                    }

                    $toplamGun += $kayit->created_at->diffInDays($cozumTarihi);
                }
                $stats['ortalama_sure'] = round($toplamGun / $adet);
            } else {
                $stats['ortalama_sure'] = 0;
            }

            // AKTİF SÜREÇTEKİ DETAYLI ŞİKAYETLER
            $stats['son_sikayetler'] = MusteriSikayeti::where('customer_id', $customerId)
                ->with(['iaaProjesi', 'sikayetKategori', 'cozumTakimi.lider'])
                ->latest()
                ->take(10)
                ->get();
        }

        return $stats;
    }
}
