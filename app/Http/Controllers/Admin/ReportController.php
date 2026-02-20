<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RaporVeriServisi;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Günlük Müşteri Şikayetleri Raporu Sayfası
     */
    public function dailyComplaintReport(Request $request)
    {
        // Yetki Kontrolü:
        // Superadmin, Yönetim, Kalite Yöneticileri ve Bölüm Liderleri görebilir.
        // Bu roller RaporlariKontrolEt komutunda da kullanılıyor.
        $user = Auth::user();

        if (!$user->hasRole(['Superadmin', 'Yönetim', 'Bölüm Kalite Yöneticisi', 'Bölüm Lideri', 'Müşteri Şikayeti Kurulu'])) {
            abort(403, 'Bu raporu görüntüleme yetkiniz yok.');
        }

        // Rapor Servisini kullanarak verileri çekiyoruz (Email ile aynı veriler)
        $servis = new RaporVeriServisi();

        // E-postadaki gibi tüm verileri istiyoruz
        $icerikAyarlari = [
            'sikayet_ozet' => true,
            'sikayet_detay' => true,
            'iaa_ozet' => true, // İsteğe bağlı, şimdilik sadece şikayet odaklı ama servisten hepsi geliyor
            'disiplin_ozet' => true,
            'arabuluculuk_ozet' => true
        ];

        $raporData = $servis->verileriTopla($icerikAyarlari);

        return view('admin.reports.daily-complaint-report', [
            'raporData' => $raporData,
            'tarih' => now()->translatedFormat('d F Y l')
        ]);
    }
}
