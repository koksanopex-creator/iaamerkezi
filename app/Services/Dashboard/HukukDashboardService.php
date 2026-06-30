<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\DB;
use App\Models\User;

class HukukDashboardService
{
    /**
     * Hukuk Dashboard istatistiklerini ve verilerini döndürür.
     *
     * @param User $user
     * @param array $filters (request altındaki filtreler: hukuk_start_date, hukuk_end_date)
     * @return array
     */
    public function getStats(User $user, array $filters = [])
    {
        // Standart Filtreler
        $startDate = $filters['hukuk_start_date'] ?? null;
        $endDate = $filters['hukuk_end_date'] ?? null;

        // Arabuluculuk Sorgusu (Base)
        $arabuluculukQuery = \App\Models\ArabuluculukCase::query();
        if ($startDate)
            $arabuluculukQuery->whereDate('created_at', '>=', $startDate);
        if ($endDate)
            $arabuluculukQuery->whereDate('created_at', '<=', $endDate);

        // Disiplin Sorgusu (Base)
        $disiplinQuery = \App\Models\DisciplinaryCase::query();
        if ($startDate)
            $disiplinQuery->whereDate('created_at', '>=', $startDate);
        if ($endDate)
            $disiplinQuery->whereDate('created_at', '<=', $endDate);

        // 1. Genel İstatistikler (Filtreli)
        $stats = [
            'toplam_arabuluculuk' => (clone $arabuluculukQuery)->count(),
            'aktif_arabuluculuk' => (clone $arabuluculukQuery)->where('status', '!=', 'kapatildi')->count(),
            'bekleyen_arabuluculuk' => (clone $arabuluculukQuery)->whereIn('status', ['taslak', 'arabulucuda', 'imza_asamasinda'])->count(),

            'toplam_disiplin' => (clone $disiplinQuery)->count(),
            'aktif_disiplin' => (clone $disiplinQuery)->whereNotIn('durum', ['Karar Verildi', 'İptal Edildi', 'Kapandı'])->where('durum', '!=', 'İptal Edildi')->count(),
            'karar_bekleyen_disiplin' => (clone $disiplinQuery)->where(function ($q) {
                $q->where('durum', 'Savunma Alındı')->orWhere('durum', 'Kurul Kararı Bekleniyor');
            })->count(),
        ];

        // 2. Bekleyen Arabuluculuk Görevleri (Son 5 - FİLTRELİ)
        $stats['bekleyen_arabuluculuk_listesi'] = (clone $arabuluculukQuery)
            ->where('status', '!=', 'kapatildi')
            ->where('status', '!=', 'KAPATILDI')
            ->with(['calisan', 'creator'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 3. Disiplin Tutanakları (Son Hareketler - FİLTRELİ)
        $stats['son_disiplin_vakalari'] = (clone $disiplinQuery)
            ->with(['user.bolum', 'behavior'])
            ->latest()
            ->take(5)
            ->get();

        // 4. Disiplin Süreçlerinin Bölüm Dağılımı (FİLTRELİ)
        $stats['disiplin_bolum_dagilimi'] = \App\Models\DisciplinaryCase::join('users', 'disciplinary_cases.user_id', '=', 'users.id')
            ->join('bolumler', 'users.bolum_id', '=', 'bolumler.id')
            ->selectRaw('bolumler.ad as bolum_adi, count(*) as dosya_sayisi')
            ->whereNull('disciplinary_cases.deleted_at');

        // Manuel Tarih Filtresi (Join olduğu için)
        if ($startDate)
            $stats['disiplin_bolum_dagilimi']->whereDate('disciplinary_cases.created_at', '>=', $startDate);
        if ($endDate)
            $stats['disiplin_bolum_dagilimi']->whereDate('disciplinary_cases.created_at', '<=', $endDate);

        $stats['disiplin_bolum_dagilimi'] = $stats['disiplin_bolum_dagilimi']
            ->where('disciplinary_cases.durum', '!=', 'İptal Edildi')
            ->groupBy('bolumler.ad')
            ->orderBy('dosya_sayisi', 'desc')
            ->get();

        return $stats;
    }
}
