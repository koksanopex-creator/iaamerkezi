<?php

namespace App\Services\Dashboard;

use App\Models\DisciplinaryCase;
use App\Models\Bolum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DisiplinKuruluDashboardService
{
    /**
     * Disiplin Kurulu Başkanı için istatistikleri getirir.
     */
    public function getChairmanStats($user, $filters = [])
    {
        $stats = [];
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        $baseQuery = DisciplinaryCase::query();
        if ($startDate) $baseQuery->whereDate('created_at', '>=', $startDate);
        if ($endDate) $baseQuery->whereDate('created_at', '<=', $endDate);

        // 1. Genel Sayılar
        $stats['toplam_tutanak'] = (clone $baseQuery)->count();
        $stats['toplanti_bekleyen_sayisi'] = (clone $baseQuery)->where('durum', 'Kurulda')->count();
        $stats['onay_bekleyen_sayisi'] = (clone $baseQuery)->where('durum', 'Kurulda')->count();
        $stats['karar_verilen_sayisi'] = (clone $baseQuery)->where('durum', 'Karar Verildi')->count();

        // 2. Bölüm Bazlı Dağılım (Tarih filtreli)
        $stats['bolum_dagilimi'] = Bolum::withCount(['disciplinaryCases' => function($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereDate('disciplinary_cases.created_at', '>=', $startDate);
                if ($endDate) $q->whereDate('disciplinary_cases.created_at', '<=', $endDate);
            }])
            ->orderByDesc('disciplinary_cases_count')
            ->take(5)
            ->get();

        // 3. Kurul Gündemindeki Dosyalar (Aktif/Yaklaşan/Tarih Bekleyen)
        $stats['yaklasan_toplantilar'] = (clone $baseQuery)->with(['user.bolum', 'behavior'])
            ->where('durum', 'Kurulda')
            // Tarihi olanları önce, olmayanı sonra göster veya tarihe göre sırala
            ->orderByRaw('CASE WHEN toplanti_tarihi IS NULL THEN 1 ELSE 0 END, toplanti_tarihi ASC')
            ->take(10)
            ->get();

        // 4. Onay Bekleyen Son Dosyalar
        $stats['onay_bekleyen_dosyalar'] = (clone $baseQuery)->with(['user.bolum', 'behavior'])
            ->where('durum', 'Kurulda')
            ->latest()
            ->take(5)
            ->get();

        // 5. Tamamlanan Son Kararlar (Toplantısı Yapılmışlar)
        $stats['son_kararlar'] = (clone $baseQuery)->with(['user.bolum', 'behavior'])
            ->where('durum', 'Karar Verildi')
            ->latest('updated_at')
            ->take(10)
            ->get();

        // 6. Son Disiplin Hareketleri
        $stats['son_hareketler'] = (clone $baseQuery)->with(['user.bolum', 'behavior'])
            ->latest()
            ->take(10)
            ->get();

        return $stats;
    }

    /**
     * Disiplin Kurulu Üyesi için istatistikleri getirir.
     */
    public function getMemberStats($user, $filters = [])
    {
        $stats = [];
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        $baseQuery = DisciplinaryCase::query();
        if ($startDate) $baseQuery->whereDate('created_at', '>=', $startDate);
        if ($endDate) $baseQuery->whereDate('created_at', '<=', $endDate);

        // 1. Üye İstatistikleri
        $stats['toplam_tutanak'] = (clone $baseQuery)->count();
        $stats['toplanti_bekleyen_sayisi'] = (clone $baseQuery)->where('durum', 'Kurulda')->count();
        $stats['karar_verilen_sayisi'] = (clone $baseQuery)->where('durum', 'Karar Verildi')->count();
        $stats['onay_bekleyen_sayisi'] = (clone $baseQuery)->where('durum', 'Kurulda')->count();

        // 2. Bekleyen Toplantılar Listesi
        $stats['yaklasan_toplantilar'] = (clone $baseQuery)->with(['user.bolum', 'behavior'])
            ->where('durum', 'Kurulda')
            ->orderByRaw('CASE WHEN toplanti_tarihi IS NULL THEN 1 ELSE 0 END, toplanti_tarihi ASC')
            ->take(10)
            ->get();

        // 3. Tamamlanan Kararlar (Toplantısı Yapılmışlar)
        $stats['son_kararlar'] = (clone $baseQuery)->with(['user.bolum', 'behavior'])
            ->where('durum', 'Karar Verildi')
            ->latest('updated_at')
            ->take(10)
            ->get();

        // 4. Bölüm Bazlı Tutanak Sayıları
        $stats['bolum_dagilimi'] = Bolum::withCount(['disciplinaryCases' => function($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereDate('disciplinary_cases.created_at', '>=', $startDate);
                if ($endDate) $q->whereDate('disciplinary_cases.created_at', '<=', $endDate);
            }])
            ->having('disciplinary_cases_count', '>', 0)
            ->orderByDesc('disciplinary_cases_count')
            ->get();

        // 5. Son Hareketler
        $stats['son_hareketler'] = (clone $baseQuery)->with(['user.bolum', 'behavior'])
            ->latest()
            ->take(10)
            ->get();

        return $stats;
    }
}
