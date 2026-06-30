<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SikayetHatirlaticiKurali;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SikayetHatirlaticiController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', SikayetHatirlaticiKurali::class);
        $kurallar = SikayetHatirlaticiKurali::latest()->get();
        return view('admin.sikayet-hatirlatma.hatirlaticilar', compact('kurallar'));
    }

    public function create()
    {
        $this->authorize('create', SikayetHatirlaticiKurali::class);
        $users = User::orderBy('name')->get();
        return view('admin.sikayet-hatirlatma.hatirlaticilar_form', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', SikayetHatirlaticiKurali::class);

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'proje_durumlari' => 'required|array',
            'siklik' => 'required|in:gunluk,haftalik,aylik',
            'saat' => 'required',
            'bildirim_rolleri' => 'nullable|array',
            'ek_kullanici_ids' => 'nullable|array',
            'haftanin_gunleri' => 'nullable|array',
            'mail_konusu' => 'nullable|string|max:255',
            'mail_taslagi' => 'nullable|string',
            'bildirim_metni' => 'nullable|string|max:255',
        ]);

        SikayetHatirlaticiKurali::create([
            'ad' => $validated['ad'],
            'aktif' => $request->has('aktif'),
            'proje_durumlari' => $validated['proje_durumlari'],
            'siklik' => $validated['siklik'],
            'haftanin_gunleri' => $validated['haftanin_gunleri'] ?? null,
            'saat' => $validated['saat'],
            'bildirim_rolleri' => $validated['bildirim_rolleri'] ?? [],
            'sikayeti_girene_bildir' => $request->has('sikayeti_girene_bildir'),
            'musteriye_bildir' => $request->has('musteriye_bildir'),
            'ek_kullanici_ids' => $validated['ek_kullanici_ids'] ?? [],
            'mail_konusu' => $validated['mail_konusu'] ?? null,
            'mail_taslagi' => $validated['mail_taslagi'] ?? null,
            'bildirim_metni' => $validated['bildirim_metni'] ?? null,
        ]);

        return redirect()->route('admin.sikayet-hatirlaticilar.index')->with('success', 'Hatırlatıcı kuralı başarıyla oluşturuldu.');
    }

    public function edit(SikayetHatirlaticiKurali $kural)
    {
        $this->authorize('update', $kural);
        $users = User::orderBy('name')->get();
        return view('admin.sikayet-hatirlatma.hatirlaticilar_form', compact('kural', 'users'));
    }

    public function update(Request $request, SikayetHatirlaticiKurali $kural)
    {
        $this->authorize('update', $kural);

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'proje_durumlari' => 'required|array',
            'siklik' => 'required|in:gunluk,haftalik,aylik',
            'saat' => 'required',
            'bildirim_rolleri' => 'nullable|array',
            'ek_kullanici_ids' => 'nullable|array',
            'haftanin_gunleri' => 'nullable|array',
            'mail_konusu' => 'nullable|string|max:255',
            'mail_taslagi' => 'nullable|string',
            'bildirim_metni' => 'nullable|string|max:255',
        ]);

        $kural->update([
            'ad' => $validated['ad'],
            'aktif' => $request->has('aktif'),
            'proje_durumlari' => $validated['proje_durumlari'],
            'siklik' => $validated['siklik'],
            'haftanin_gunleri' => $validated['haftanin_gunleri'] ?? null,
            'saat' => $validated['saat'],
            'bildirim_rolleri' => $validated['bildirim_rolleri'] ?? [],
            'sikayeti_girene_bildir' => $request->has('sikayeti_girene_bildir'),
            'musteriye_bildir' => $request->has('musteriye_bildir'),
            'ek_kullanici_ids' => $validated['ek_kullanici_ids'] ?? [],
            'mail_konusu' => $validated['mail_konusu'] ?? null,
            'mail_taslagi' => $validated['mail_taslagi'] ?? null,
            'bildirim_metni' => $validated['bildirim_metni'] ?? null,
        ]);

        return redirect()->route('admin.sikayet-hatirlaticilar.index')->with('success', 'Kural başarıyla güncellendi.');
    }

    public function destroy(SikayetHatirlaticiKurali $kural)
    {
        $this->authorize('delete', $kural);
        $kural->delete();
        return back()->with('success', 'Kural silindi.');
    }
}
