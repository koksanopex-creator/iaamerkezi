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
        // Yetki Kontrolü: Sadece Superadmin ve Yönetim
        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Yonetim')) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        $logs = MachineLog::with(['user', 'machine', 'bolum'])
            ->latest()
            ->paginate(20);

        return view('admin.machine_logs.index', compact('logs'));
    }
}
