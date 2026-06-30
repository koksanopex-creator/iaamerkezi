<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MachineLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MachineLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Yetki Kontrolü: Superadmin, Yönetim ve Bölüm Liderleri
        $user = Auth::user();
        if (!$user->hasRole(['Superadmin', 'Yonetim', 'Bölüm Lideri', 'Direktör'])) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        $activeDashboard = session('active_dashboard_' . $user->id);
        $query = MachineLog::with(['user', 'machine', 'bolum']);

        // Yetki Bazlı Filtreleme
        if ($activeDashboard === 'bolum_lideri' && $user->bolum_id) {
            $query->where('bolum_id', $user->bolum_id);
        } elseif ($activeDashboard === 'direktor') {
            $managedBolumIds = $user->yonetilenBolumler()->pluck('id')->toArray();
            $query->whereIn('bolum_id', $managedBolumIds);
        }

        $logs = $query->latest()->paginate(20);

        return view('admin.machine_logs.index', compact('logs'));
    }
}
