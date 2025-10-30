<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SikayetKategori;
use App\Models\Takim; // Takımları çekmek için ekliyoruz
use Illuminate\Http\Request;

class SikayetKategoriController extends Controller
{
    public function index()
    {
        $kategoriler = SikayetKategori::with('varsayilanTakim')->latest()->get();
        return view('admin.sikayet_kategorileri.index', compact('kategoriler'));
    }

    public function create()
    {
        // Sadece şikayet takımlarını al
        $takimlar = Takim::where('tur', 'sikayet')->get();
        return view('admin.sikayet_kategorileri.create', compact('takimlar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ad' => 'required|string|max:255|unique:sikayet_kategorileri,ad',
            'varsayilan_takim_id' => 'nullable|exists:takimlar,id',
        ]);

        SikayetKategori::create($request->all());

        return redirect()->route('admin.sikayet-kategorileri.index')->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    public function destroy(SikayetKategori $sikayetKategori)
    {
        $sikayetKategori->delete();
        return redirect()->route('admin.sikayet-kategorileri.index')->with('success', 'Kategori başarıyla silindi.');
    }

    public function edit(SikayetKategori $sikayetKategori)
    {
        $takimlar = Takim::where('tur', 'sikayet')->get(); // Sadece çözüm takımlarını al
        return view('admin.sikayet_kategorileri.edit', compact('sikayetKategori', 'takimlar'));
    }

    public function update(Request $request, SikayetKategori $sikayetKategori)
    {
        $request->validate([
            // Kategori adını güncellerken, KENDİSİ HARİÇ bu ismin unique olmasını kontrol et
            'ad' => 'required|string|max:255|unique:sikayet_kategorileri,ad,' . $sikayetKategori->id,
            'varsayilan_takim_id' => 'nullable|exists:takimlar,id',
        ]);

        $sikayetKategori->update($request->all());

        return redirect()->route('admin.sikayet-kategorileri.index')->with('success', 'Kategori başarıyla güncellendi.');
    }
}