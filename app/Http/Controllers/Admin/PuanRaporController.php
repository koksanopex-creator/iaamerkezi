<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bolum;
use App\Models\User;
use App\Services\PuanAnalizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PuanRaporController extends Controller
{
    protected $analizService;

    public function __construct(PuanAnalizService $analizService)
    {
        $this->analizService = $analizService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $allowedBolumIds = $user->getAllowedBolumIds();

        // Bölüm Filtresi
        $selectedBolumId = $request->input('bolum_id');
        
        // Yetki Kontrolü
        $activeBolumIds = $allowedBolumIds;
        if ($selectedBolumId && $allowedBolumIds !== '*') {
            if (in_array($selectedBolumId, (array)$allowedBolumIds)) {
                $activeBolumIds = [$selectedBolumId];
            } else {
                // Yetkisiz erişim denemesi
                $activeBolumIds = $allowedBolumIds;
                $selectedBolumId = null;
            }
        } elseif ($selectedBolumId) {
            $activeBolumIds = [$selectedBolumId];
        }

        // Tarih Filtreleri
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Verileri Topla
        $kpiStats = $this->analizService->getKpiStats($activeBolumIds, $startDate, $endDate);
        $trendStats = $this->analizService->getTrendStats($activeBolumIds, $startDate, $endDate);
        $categoryStats = $this->analizService->getCategoryStats($activeBolumIds, $startDate, $endDate);
        $teamStats = $this->analizService->getTeamStats($activeBolumIds, $startDate, $endDate);
        $departmentStats = $this->analizService->getDepartmentStats($activeBolumIds, $startDate, $endDate);
        $topPerformersArr = $this->analizService->getTopPerformers($activeBolumIds, 50, $startDate, $endDate);

        // Bölüm Listesi (Filtre için)
        $bolumlerQuery = Bolum::query();
        if ($allowedBolumIds !== '*') {
            $bolumlerQuery->whereIn('id', (array)$allowedBolumIds);
        }
        $bolumler = $bolumlerQuery->orderBy('ad')->get();

        return view('admin.raporlar.puan-performans', compact(
            'kpiStats',
            'trendStats',
            'categoryStats',
            'teamStats',
            'departmentStats',
            'topPerformersArr',
            'bolumler',
            'selectedBolumId',
            'startDate',
            'endDate'
        ));
    }
}
