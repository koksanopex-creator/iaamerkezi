<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArabuluculukAnlasmaMaddesi;
use App\Models\ArabuluculukSettingLog;
use Illuminate\Support\Facades\Auth;

class ArabuluculukTanimController extends Controller
{
    /**
     * Anlaşma Maddeleri Listesi ve Yönetim Ekranı
     */
    public function anlasmaMaddeleri()
    {
        // 1. Yetki Kontrolü (Hukuk Admini, Yöneticisi veya Superadmin)
        if (!Auth::user()->can('arabuluculuk.settings_view') && !Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Bu alana erişim yetkiniz yok.');
        }

        // 2. Verileri Çek
        $maddeler = ArabuluculukAnlasmaMaddesi::orderBy('created_at', 'desc')->get();
        
        // 3. Logları Çek (Sadece Superadmin görsün)
        $logs = [];
        if (Auth::user()->hasRole('Superadmin')) {
            $logs = ArabuluculukSettingLog::with('user')->latest()->limit(30)->get();
        }

        return view('admin.arabuluculuk.tanimlar.anlasma_maddeleri', compact('maddeler', 'logs'));
    }

    // Yeni Madde Ekleme
    public function storeMadde(Request $request)
    {
        if (!Auth::user()->can('arabuluculuk.settings_create') && !Auth::user()->hasRole('Superadmin')) {
            abort(403);
        }

        $request->validate([
            'icerik' => 'required|string|min:5',
            'hukuki_dayanak' => 'nullable|string|max:255' // Yeni Validasyon
        ]);

        ArabuluculukAnlasmaMaddesi::create([
            'icerik' => $request->icerik,
            'hukuki_dayanak' => $request->hukuki_dayanak, // Kayıt
            'is_active' => true
        ]);

        $this->logAction('EKLEME', '"' . \Str::limit($request->icerik, 30) . '..." maddesi eklendi.');

        return back()->with('success', 'Madde eklendi.');
    }

    // --- YENİ EKLENEN UPDATE METODU ---
    public function updateMadde(Request $request, $id)
    {
        if (!Auth::user()->can('arabuluculuk.settings_edit') && !Auth::user()->hasRole('Superadmin')) {
            abort(403);
        }

        $madde = ArabuluculukAnlasmaMaddesi::findOrFail($id);
        
        $madde->update([
            'icerik' => $request->icerik,
            'hukuki_dayanak' => $request->hukuki_dayanak
        ]);

        $this->logAction('DÜZENLEME', 'Madde #' . $id . ' güncellendi.');

        return back()->with('success', 'Madde güncellendi.');
    }

    /**
     * Madde Silme
     */
    public function destroyMadde($id)
    {
        if (!Auth::user()->can('arabuluculuk.settings_delete') && !Auth::user()->hasRole('Superadmin')) {
            abort(403, 'Madde silme yetkiniz yok.');
        }

        $madde = ArabuluculukAnlasmaMaddesi::findOrFail($id);
        $eskiIcerik = $madde->icerik;
        
        $madde->delete();

        // LOG TUTMA
        $this->logAction('SİLME', '"' . \Str::limit($eskiIcerik, 30) . '..." maddesi silindi.');

        return back()->with('success', 'Madde silindi.');
    }

    // Yardımcı Log Fonksiyonu
    private function logAction($tur, $detay) {
        ArabuluculukSettingLog::create([
            'user_id' => Auth::id(),
            'islem_turu' => $tur,
            'detay' => $detay,
            'ip_adresi' => request()->ip()
        ]);
    }

    /**
     * Tüm Logları Listeleme Sayfası
     */
    
    public function showAllLogs(Request $request)
    {
        // Yetki Kontrolü
        if (!Auth::user()->hasRole('Superadmin')) {
            abort(403);
        }

        // Sorguyu Başlat
        $query = ArabuluculukSettingLog::with('user');

        // 1. Kullanıcı Filtresi
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 2. İşlem Türü Filtresi
        if ($request->filled('islem_turu')) {
            $query->where('islem_turu', $request->islem_turu);
        }

        // 3. İçerik Arama (Maddeye göre)
        if ($request->filled('search')) {
            $query->where('detay', 'like', '%' . $request->search . '%');
        }

        // 4. Tarih Aralığı
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        // Listeleme (Pagination)
        // withQueryString() sayesinde sayfa değişince filtreler kaybolmaz.
        $logs = $query->latest()->paginate(20)->withQueryString();

        // 1. Log tablosunda işlem yapmış olan benzersiz (unique) user_id'leri bul
        $activeUserIds = ArabuluculukSettingLog::distinct()->pluck('user_id');

        // 2. Sadece bu ID'lere sahip kullanıcıları isme göre sıralı getir
        $users = \App\Models\User::whereIn('id', $activeUserIds)
                    ->orderBy('name')
                    ->get();

        return view('admin.arabuluculuk.tanimlar.log_history', compact('logs', 'users'));
    }
}