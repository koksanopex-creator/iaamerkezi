<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusteriSikayetiYoneticiRaporKurali;
use Illuminate\Http\Request;

class MusteriSikayetiYoneticiRaporController extends Controller
{
    public function index()
    {
        // Only Superadmin or specific roles should access this settings page
        if (!auth()->user()->hasRole(['Superadmin'])) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        $kurallar = MusteriSikayetiYoneticiRaporKurali::latest()->get();
        return view('admin.sikayet-yonetici-rapor.index', compact('kurallar'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole(['Superadmin'])) {
            abort(403, 'Yetkiniz yok.');
        }
        return view('admin.sikayet-yonetici-rapor.form');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['Superadmin'])) {
            abort(403, 'Yetkiniz yok.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'siklik' => 'required|in:gunluk,haftalik,aylik',
            'saat' => 'required',
            'haftanin_gunleri' => 'nullable|array',
            'mail_konusu' => 'nullable|string|max:255',
            'mail_taslagi' => 'nullable|string',
            'bildirim_metni' => 'nullable|string|max:255',
        ]);

        MusteriSikayetiYoneticiRaporKurali::create([
            'ad' => $validated['ad'],
            'aktif' => $request->has('aktif'),
            'siklik' => $validated['siklik'],
            'haftanin_gunleri' => $validated['haftanin_gunleri'] ?? null,
            'saat' => $validated['saat'],
            'mail_aktif_et' => $request->has('mail_aktif_et'),
            'zili_aktif_et' => $request->has('zili_aktif_et'),
            'mail_konusu' => $validated['mail_konusu'] ?? null,
            'mail_taslagi' => $validated['mail_taslagi'] ?? null,
            'bildirim_metni' => $validated['bildirim_metni'] ?? null,
        ]);

        return redirect()->route('admin.sikayet-yonetici-rapor.index')->with('success', 'Rapor kuralı başarıyla oluşturuldu.');
    }

    public function edit(MusteriSikayetiYoneticiRaporKurali $kural)
    {
        if (!auth()->user()->hasRole(['Superadmin'])) {
            abort(403, 'Yetkiniz yok.');
        }
        return view('admin.sikayet-yonetici-rapor.form', compact('kural'));
    }

    public function update(Request $request, MusteriSikayetiYoneticiRaporKurali $kural)
    {
        if (!auth()->user()->hasRole(['Superadmin'])) {
            abort(403, 'Yetkiniz yok.');
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'siklik' => 'required|in:gunluk,haftalik,aylik',
            'saat' => 'required',
            'haftanin_gunleri' => 'nullable|array',
            'mail_konusu' => 'nullable|string|max:255',
            'mail_taslagi' => 'nullable|string',
            'bildirim_metni' => 'nullable|string|max:255',
        ]);

        $kural->update([
            'ad' => $validated['ad'],
            'aktif' => $request->has('aktif'),
            'siklik' => $validated['siklik'],
            'haftanin_gunleri' => $validated['haftanin_gunleri'] ?? null,
            'saat' => $validated['saat'],
            'mail_aktif_et' => $request->has('mail_aktif_et'),
            'zili_aktif_et' => $request->has('zili_aktif_et'),
            'mail_konusu' => $validated['mail_konusu'] ?? null,
            'mail_taslagi' => $validated['mail_taslagi'] ?? null,
            'bildirim_metni' => $validated['bildirim_metni'] ?? null,
        ]);

        return redirect()->route('admin.sikayet-yonetici-rapor.index')->with('success', 'Kural başarıyla güncellendi.');
    }

    public function destroy(MusteriSikayetiYoneticiRaporKurali $kural)
    {
        if (!auth()->user()->hasRole(['Superadmin'])) {
            abort(403, 'Yetkiniz yok.');
        }
        $kural->delete();
        return back()->with('success', 'Kural silindi.');
    }
}
