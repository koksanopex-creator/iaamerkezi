<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusteriLog;
use Illuminate\Http\Request;

class MusteriLogController extends Controller
{
    public function index()
    {
        // Sadece yetkililer görebilir
        if (!auth()->user()->hasRole(['Superadmin', 'Super Admin', 'Yonetim'])) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        // DEĞİŞİKLİK YAPILDI:
        // Blade tarafında gruplama yapabilmek için önce customer_id'ye göre sıralıyoruz.
        // Daha sonra her müşterinin kendi loglarını tarihe göre (yeni en üstte) sıralıyoruz.
        $logs = MusteriLog::with(['user', 'customer'])
            ->orderBy('customer_id', 'desc') // Ana Gruplama (Müşterileri bir araya toplar)
            ->orderBy('created_at', 'desc')  // Grup İçi Sıralama (En yeni işlem üstte)
            ->paginate(50);

        return view('admin.musteri_logs.index', compact('logs'));
    }
}