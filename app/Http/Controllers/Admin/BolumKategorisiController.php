<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BolumKategorisi;
use Illuminate\Http\Request;

class BolumKategorisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoriler = BolumKategorisi::latest()->get();
        return view('admin.bolum_kategorileri.index', compact('kategoriler'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:bolum_kategorileri',
        ]);

        BolumKategorisi::create($validated);

        return redirect()->back()->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BolumKategorisi $bolumKategorisi)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:bolum_kategorileri,ad,' . $bolumKategorisi->id,
        ]);

        $bolumKategorisi->update($validated);

        return redirect()->back()->with('success', 'Kategori başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BolumKategorisi $bolumKategorisi)
    {
        // Eğer bu kategoriye bağlı bölümler varsa silme işlemini engelle
        if ($bolumKategorisi->bolumler()->exists()) {
            return redirect()->back()->with('error', 'Bu kategoriye bağlı bölümler olduğu için silinemez.');
        }

        $bolumKategorisi->delete();

        return redirect()->back()->with('success', 'Kategori başarıyla silindi.');
    }
}