<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bolum; // <-- BU SATIRI EKLE


class BolumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Veritabanından tüm bölümleri en yeniden eskiye doğru sıralayarak al.
        $bolumler = Bolum::latest()->get();

        // Verileri view'e gönder.
        return view('admin.bolumler.index', compact('bolumler'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Sadece oluşturma formunu gösterir.
        return view('admin.bolumler.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Formdan gelen veriyi doğrula
        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:bolumler',
            'is_active' => 'required|boolean',
        ]);

        // 2. Veriyi veritabanına kaydet
        Bolum::create($validated);

        // 3. Başarılı mesajıyla birlikte listeleme sayfasına geri yönlendir
        return redirect()->route('admin.bolumler.index')->with('success', 'Bölüm başarıyla oluşturuldu!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

   /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bolum $bolum)
    {
        // Düzenlenecek bölümün verileriyle birlikte edit formunu göster.
        return view('admin.bolumler.edit', compact('bolum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bolum $bolum)
    {
        // 1. Veriyi doğrula (unique kuralı güncellenen bölümün kendisini hariç tutmalı)
        $validated = $request->validate([
            'ad' => 'required|string|max:255|unique:bolumler,ad,' . $bolum->id,
            'is_active' => 'required|boolean',
        ]);

        // 2. Bölüm verilerini güncelle
        $bolum->update($validated);

        // 3. Başarı mesajıyla listeleme sayfasına yönlendir
        return redirect()->route('admin.bolumler.index')->with('success', 'Bölüm başarıyla güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bolum $bolum)
    {
        // Bölümü sil (Soft delete sayesinde veritabanından kalıcı olarak silinmez)
        $bolum->delete();

        // Başarı mesajıyla listeleme sayfasına yönlendir
        return redirect()->route('admin.bolumler.index')->with('success', 'Bölüm başarıyla silindi!');
    }
}
