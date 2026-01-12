<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Arabulucu;
use App\Models\ArabulucuLog; // Log Modeli
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArabulucuController extends Controller
{
    /**
     * YARDIMCI FONKSİYON: İşlem Logu Kaydeder
     * Bu fonksiyon, diğer metotlar tarafından çağrılır.
     */
    private function logAction($arabulucuId, $islem, $detay = null)
    {
        ArabulucuLog::create([
            'user_id' => auth()->id(),
            'arabulucu_id' => $arabulucuId,
            'islem_turu' => $islem,
            'detay' => $detay,
            'ip_adres' => request()->ip()
        ]);
    }

    /**
     * Logları Listeleme Sayfası (Sadece Superadmin)
     */
    public function logs()
    {
        if (!auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Bu sayfayı görme yetkiniz yok.');
        }

        $logs = ArabulucuLog::with(['user', 'arabulucu'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);

        return view('admin.arabulucular.logs', compact('logs'));
    }

    /**
     * Arabulucu Listesi
     */
    public function index()
    {
        $currentYear = now()->year;
        
        // İstatistikler için veriler
        $totalCases = \App\Models\ArabuluculukCase::count(); 
        $totalCasesCurrentYear = \App\Models\ArabuluculukCase::whereYear('created_at', $currentYear)->count();

        // Arabulucuları ve istatistiklerini çekiyoruz
        $arabulucular = Arabulucu::withCount([
            'cases as total_cases',
            'cases as closed_cases_count' => function ($query) { 
                $query->where('status', 'kapatildi'); 
            },
            'cases as open_cases_count' => function ($query) { 
                $query->where('status', '!=', 'kapatildi'); 
            },
            'cases as current_year_count' => function ($query) use ($currentYear) {
                $query->whereYear('created_at', $currentYear);
            }
        ])
        ->orderBy('total_cases', 'desc')
        ->paginate(10);
        
        return view('admin.arabulucular.index', compact('arabulucular', 'totalCases', 'totalCasesCurrentYear', 'currentYear'));
    }

    /**
     * Yeni Ekleme Formu
     */
    public function create()
    {
        return view('admin.arabulucular.create');
    }

    /**
     * Yeni Kayıt İşlemi
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sicil_no' => 'required|string|unique:arabulucular,sicil_no',
            'sehir' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefon' => 'nullable|string|max:20',
            'adres' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;

        $arabulucu = Arabulucu::create($validated);

        // LOG KAYDI
        $this->logAction($arabulucu->id, 'OLUŞTURMA', $arabulucu->name . ' sisteme eklendi.');

        return redirect()->route('admin.arabulucular.index')->with('success', 'Arabulucu başarıyla eklendi.');
    }

    /**
     * Detay Sayfası
     */
    public function show(Request $request, $id)
    {
        $arabulucu = Arabulucu::with(['creator', 'cases'])->findOrFail($id);

        // --- SOL TARAF: YILLIK İSTATİSTİK ---
        $baslangicYili = 2024;
        $mevcutYil = now()->year;
        $yillikVeriler = [];

        for ($yil = $mevcutYil; $yil >= $baslangicYili; $yil--) {
            $sistemToplam = \App\Models\ArabuluculukCase::whereYear('created_at', $yil)->count();
            
            // with('calisan') ekledik (Personel ismini görmek için)
            $arabulucuDosyalari = $arabulucu->cases()
                                            ->with('calisan') 
                                            ->whereYear('created_at', $yil)
                                            ->orderBy('created_at', 'desc')
                                            ->get();
            
            $arabulucuSayi = $arabulucuDosyalari->count();
            $oran = $sistemToplam > 0 ? ($arabulucuSayi / $sistemToplam) * 100 : 0;

            $yillikVeriler[] = [
                'yil' => $yil,
                'sistem_toplam' => $sistemToplam,
                'kendi_toplam' => $arabulucuSayi,
                'oran' => $oran,
                'dosyalar' => $arabulucuDosyalari
            ];
        }

        // --- SAĞ TARAF: FİLTRELENEBİLİR LİSTE ---
        $query = $arabulucu->cases()->with('calisan');

        if ($request->filled('durum')) {
            $query->where('status', $request->durum);
        }
        if ($request->filled('baslangic_tarihi')) {
            $query->whereDate('created_at', '>=', $request->baslangic_tarihi);
        }
        if ($request->filled('bitis_tarihi')) {
            $query->whereDate('created_at', '<=', $request->bitis_tarihi);
        }
        if ($request->filled('min_tutar')) {
            $query->where('anlasilan_tutar', '>=', $request->min_tutar);
        }

        // Verileri çekip yıllara göre grupluyoruz
        $casesCollection = $query->orderBy('created_at', 'desc')->get();
        $groupedCases = $casesCollection->groupBy(function($item) {
            return $item->created_at->format('Y');
        });

        return view('admin.arabulucular.show', compact('arabulucu', 'groupedCases', 'yillikVeriler'));
    }

    /**
     * Düzenleme Formu
     */
    public function edit($id)
    {
        $arabulucu = Arabulucu::findOrFail($id);
        return view('admin.arabulucular.edit', compact('arabulucu'));
    }

    /**
     * Güncelleme İşlemi
     */
    public function update(Request $request, $id)
    {
        $arabulucu = Arabulucu::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sicil_no' => 'required|string|unique:arabulucular,sicil_no,' . $arabulucu->id,
            'sehir' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefon' => 'nullable|string|max:20',
            'adres' => 'nullable|string',
        ]);

        $arabulucu->update($validated);

        // LOG KAYDI
        $this->logAction($arabulucu->id, 'DÜZENLEME', 'Bilgiler güncellendi.');

        return redirect()->route('admin.arabulucular.index')->with('success', 'Arabulucu bilgileri güncellendi.');
    }

    /**
     * Silme İşlemi
     */
    public function destroy($id)
    {
        $arabulucu = Arabulucu::findOrFail($id);
        $isim = $arabulucu->name;
        $arabulucu->delete();

        // LOG KAYDI
        $this->logAction($id, 'SİLME', "$isim arşivlendi/silindi.");

        return redirect()->route('admin.arabulucular.index')->with('success', 'Arabulucu silindi.');
    }

    /**
     * Durum Değiştirme (Aktif/Pasif)
     */
    public function toggleStatus($id)
    {
        $arabulucu = Arabulucu::findOrFail($id);
        $arabulucu->is_active = !$arabulucu->is_active;
        $arabulucu->save();

        $durum = $arabulucu->is_active ? 'Aktif' : 'Pasif';
        
        // LOG KAYDI
        $this->logAction($arabulucu->id, 'DURUM DEĞİŞTİRME', "Durum $durum yapıldı.");

        return back()->with('success', "Arabulucu durumu $durum yapıldı.");
    }
}